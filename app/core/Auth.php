<?php
/**
 * AgriLink – Auth middleware helpers
 */

require_once __DIR__ . '/Session.php';

class Auth {

    /** Require user to be logged in. Redirect to login if not. */
    public static function require(): void {
        Session::start();
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to access that page.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /** Require a specific role. */
    public static function requireRole(string|array $roles): void {
        self::require();
        $roles = (array) $roles;
        if (!in_array(Session::userRole(), $roles, true)) {
            http_response_code(403);
            include APP_ROOT . '/app/views/partials/403.php';
            exit;
        }
    }

    /** Redirect if already logged in (for login/register pages). */
    public static function redirectIfLoggedIn(): void {
        Session::start();
        if (Session::isLoggedIn()) {
            self::redirectToDashboard();
        }
    }

    /** Redirect user to their role dashboard. */
    public static function redirectToDashboard(): void {
        $role = Session::userRole();
        $url = match($role) {
            'farmer'    => APP_URL . '/farmer/dashboard',
            'buyer'     => APP_URL . '/buyer/marketplace',
            'transport' => APP_URL . '/transport/dashboard',
            'admin'     => APP_URL . '/admin/dashboard',
            default     => APP_URL . '/login',
        };
        header('Location: ' . $url);
        exit;
    }

    /** Redirect to a path. Prepends APP_URL for relative paths. */
    public static function redirect(string $path): never {
        $url = str_starts_with($path, 'http') ? $path : APP_URL . $path;
        header('Location: ' . $url);
        exit;
    }
}
