<?php
/**
 * AgriLink – User Model (Repository pattern).
 *
 * Encapsulates all database access for the agrilink_users table.
 * Controllers depend on this class exclusively for user persistence,
 * keeping SQL isolated from business logic (Single Responsibility Principle).
 *
 * @package AgriLink\Models
 * @author  AgriLink Development Team
 * @version 1.0.0
 */

require_once __DIR__ . '/../config/database.php';

class UserModel {
    /** @var PDO Active database connection from the Singleton pool. */
    private PDO $db;

    /** @var string Fully-qualified table name (with prefix). */
    private string $table;

    /**
     * Initialise the model with the shared PDO Singleton connection.
     */
    public function __construct() {
        $this->db = Database::connect();
        $this->table = USER_TABLE;
    }

    /**
     * Find an active user by email address.
     *
     * @param  string       $email  User's email address.
     * @return array|false          User row on success, false if not found.
     */
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by their primary key.
     *
     * @param  int          $id  User's primary key.
     * @return array|false       User row on success, false if not found.
     */
    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create a new user with a bcrypt-hashed password.
     *
     * @param  array  $data  Associative array with keys: name, email, password,
     *                       and optionally phone, role, region, town.
     * @return int           The new user's primary key.
     */
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (name, email, phone, password, role, region, town) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone']   ?? null,
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['role']    ?? 'buyer',
            $data['region']  ?? null,
            $data['town']    ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update profile fields for a user.
     *
     * Only columns present in the $data array are updated; unset columns are
     * left unchanged (partial update).
     *
     * @param  int    $id    User's primary key.
     * @param  array  $data  Key-value pairs of columns to update.
     * @return bool          True on success, false if $data is empty.
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach (['name','email','phone','region','town','profile_photo'] as $col) {
            if (isset($data[$col])) {
                $fields[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';
        return $this->db->prepare($sql)->execute($params);
    }

    /**
     * Check whether an email address is already registered to a different user.
     *
     * Used during profile updates to prevent email collisions.
     *
     * @param  string  $email      Email address to check.
     * @param  int     $excludeId  User ID to exclude from the check (the current user).
     * @return bool                True if the email exists for another user.
     */
    public function emailExistsForOther(string $email, int $excludeId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Update a user's password with a fresh bcrypt hash.
     *
     * @param  int     $id           User's primary key.
     * @param  string  $newPassword  Plain-text new password.
     * @return bool                  True on success.
     */
    public function changePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    /**
     * Store a hashed password reset token and expiry timestamp.
     *
     * @param  int     $id          User's primary key.
     * @param  string  $tokenHash   Bcrypt hash of the raw reset token.
     * @param  string  $expiresAt   Expiry datetime (MySQL DATETIME format).
     * @return bool                 True on success.
     */
    public function storePasswordResetToken(int $id, string $tokenHash, string $expiresAt): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password_reset_token = ?, password_reset_expires_at = ?, password_reset_requested_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$tokenHash, $expiresAt, $id]);
    }

    /**
     * Find a user whose stored reset token matches $token and has not expired.
     *
     * Iterates over all non-expired token rows and calls password_verify()
     * to avoid timing attacks via direct string comparison.
     *
     * @param  string       $token  Plain-text reset token from the URL.
     * @return array|false          User row on success, false if invalid/expired.
     */
    public function findByValidPasswordResetToken(string $token): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE password_reset_token IS NOT NULL AND password_reset_expires_at IS NOT NULL AND password_reset_expires_at >= NOW()"
        );
        $stmt->execute();
        foreach ($stmt->fetchAll() as $user) {
            if (password_verify($token, $user['password_reset_token'])) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Clear the password reset token columns after a successful reset.
     *
     * @param  int   $id  User's primary key.
     * @return bool       True on success.
     */
    public function clearPasswordResetToken(int $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password_reset_token = NULL, password_reset_expires_at = NULL, password_reset_requested_at = NULL WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Update password and atomically clear reset token in a single query.
     *
     * Preferred over calling changePassword() + clearPasswordResetToken()
     * separately to avoid a race-condition window.
     *
     * @param  int     $id           User's primary key.
     * @param  string  $newPassword  Plain-text new password.
     * @return bool                  True on success.
     */
    public function changePasswordAndClearReset(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password = ?, password_reset_token = NULL, password_reset_expires_at = NULL, password_reset_requested_at = NULL WHERE id = ?"
        );
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    /**
     * Retrieve all users, optionally filtered by role.
     *
     * @param  string|null  $role  Role to filter by ('farmer', 'buyer', etc.),
     *                             or null for all roles.
     * @return array               List of user rows ordered by created_at DESC.
     */
    public function getAll(?string $role = null): array {
        if ($role) {
            $stmt = $this->db->prepare("SELECT id,name,email,phone,role,region,town,is_active,created_at FROM {$this->table} WHERE role = ? ORDER BY created_at DESC");
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query("SELECT id,name,email,phone,role,region,town,is_active,created_at FROM {$this->table} ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    }

    /**
     * Toggle the is_active flag for a user (admin action).
     *
     * @param  int   $id  User's primary key.
     * @return bool       True on success.
     */
    public function toggleActive(int $id): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count users grouped by role.
     *
     * @return array  Array of rows with keys 'role' and 'total'.
     */
    public function countByRole(): array {
        $stmt = $this->db->query("SELECT role, COUNT(*) as total FROM {$this->table} GROUP BY role");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Mark a user as verified (admin action). */
    public function setVerified(int $id, bool $verified): bool {
        $sql = $verified
            ? "UPDATE {$this->table} SET is_verified = 1, verified_at = NOW() WHERE id = ?"
            : "UPDATE {$this->table} SET is_verified = 0, verified_at = NULL WHERE id = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }

    /** Enable or disable a user account (admin action). */
    public function setActive(int $id, bool $active): bool {
        return $this->db->prepare("UPDATE {$this->table} SET is_active = ? WHERE id = ?")
                        ->execute([(int)$active, $id]);
    }

    /** Search + filter users for admin panel. */
    public function getAllUsers(string $search = '', string $role = ''): array {
        $where  = [];
        $params = [];
        if ($search) {
            $where[] = '(name LIKE ? OR email LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($role) {
            $where[] = 'role = ?';
            $params[] = $role;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare(
            "SELECT id,name,email,phone,role,region,town,is_active,is_verified,created_at FROM {$this->table} $whereSql ORDER BY created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
