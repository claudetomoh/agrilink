<?php
/**
 * AgriLink – Session helper
 * Handles secure session start, CSRF tokens, flash messages.
 */

require_once __DIR__ . '/../config/config.php';

class Session {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
            session_set_cookie_params([
                'lifetime' => SESSION_TIMEOUT,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
        // Regenerate session ID periodically to prevent fixation
        if (empty($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        session_unset();
        session_destroy();
    }

    /* ------ AUTH helpers ------ */

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email']= $user['email'];
        $_SESSION['user_region']= $user['region'] ?? '';
        $_SESSION['_created']  = time();
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function userRole(): ?string {
        return $_SESSION['user_role'] ?? null;
    }

    public static function userName(): string {
        return $_SESSION['user_name'] ?? 'Guest';
    }

    /* ------ Flash messages ------ */

    public static function flash(string $key, string $message): void {
        $_SESSION['_flash'][$key] = $message;
    }

    /** Alias for flash() — consistent with controller conventions. */
    public static function setFlash(string $key, string $message): void {
        self::flash($key, $message);
    }

    public static function getFlash(string $key): ?string {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }

    /* ------ CSRF ------ */

    public static function csrfToken(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function verifyCsrf(string $token): bool {
        $valid = isset($_SESSION['_csrf_token'])
            && hash_equals($_SESSION['_csrf_token'], $token);
        if ($valid) {
            // Rotate token after successful use
            unset($_SESSION['_csrf_token']);
        }
        return $valid;
    }
}
