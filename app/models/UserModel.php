<?php
/**
 * AgriLink – User Model
 */

require_once __DIR__ . '/../config/database.php';

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $sql = 'INSERT INTO users (name, email, phone, password, role, region, town) VALUES (?,?,?,?,?,?,?)';
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
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        return $this->db->prepare($sql)->execute($params);
    }

    public function emailExistsForOther(string $email, int $excludeId): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetchColumn();
    }

    public function changePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function getAll(string $role = null): array {
        if ($role) {
            $stmt = $this->db->prepare('SELECT id,name,email,phone,role,region,town,is_active,created_at FROM users WHERE role = ? ORDER BY created_at DESC');
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query('SELECT id,name,email,phone,role,region,town,is_active,created_at FROM users ORDER BY created_at DESC');
        }
        return $stmt->fetchAll();
    }

    public function toggleActive(int $id): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = NOT is_active WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countByRole(): array {
        $stmt = $this->db->query('SELECT role, COUNT(*) as total FROM users GROUP BY role');
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Mark a user as verified (admin action). */
    public function setVerified(int $id, bool $verified): bool {
        $sql = $verified
            ? 'UPDATE users SET is_verified = 1, verified_at = NOW() WHERE id = ?'
            : 'UPDATE users SET is_verified = 0, verified_at = NULL WHERE id = ?';
        return $this->db->prepare($sql)->execute([$id]);
    }

    /** Enable or disable a user account (admin action). */
    public function setActive(int $id, bool $active): bool {
        return $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?')
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
            "SELECT id,name,email,phone,role,region,town,is_active,is_verified,created_at FROM users $whereSql ORDER BY created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
