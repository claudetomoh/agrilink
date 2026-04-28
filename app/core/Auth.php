<?php
/**
 * AgriLink – Authentication & Role-Based Access Control middleware (Strategy pattern).
 *
 * Implements the Chain of Responsibility pattern for route protection:
 * every protected controller calls Auth::requireRole() as the first
 * statement, ensuring authentication and authorisation are enforced
 * uniformly before any business logic executes.
 *
 * @package AgriLink\Core
 * @author  AgriLink Development Team
 * @version 1.0.0
 */

require_once __DIR__ . '/Session.php';

class Auth {

    /**
     * Require an authenticated session.
     *
     * If no valid session exists, sets a flash error message and redirects
     * the browser to the login page. Never returns if unauthenticated.
     *
     * @return void
     */
    public static function require(): void {
        Session::start();
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to access that page.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /**
     * Require the authenticated user to hold one of the given roles (Strategy pattern).
     *
     * Calls Auth::require() first to ensure authentication, then checks the
     * session role against the $roles list. Responds with HTTP 403 and renders
     * the 403 partial if the role is not permitted.
     *
     * @param  string|string[]  $roles  One role string or an array of permitted roles.
     * @return void
     */
    public static function requireRole(string|array $roles): void {
        self::require();
        $roles = (array) $roles;
        if (!in_array(Session::userRole(), $roles, true)) {
            http_response_code(403);
            include APP_ROOT . '/app/views/partials/403.php';
            exit;
        }
    }

    /**
     * Redirect to the user's dashboard if already authenticated.
     *
     * Used on login and register pages to prevent authenticated users from
     * accessing those forms unnecessarily.
     *
     * @return void
     */
    public static function redirectIfLoggedIn(): void {
        Session::start();
        if (Session::isLoggedIn()) {
            self::redirectToDashboard();
        }
    }

    /**
     * Redirect to the role-appropriate dashboard and terminate.
     *
     * Role-to-URL mapping is centralised here so changing a dashboard route
     * requires editing only this method.
     *
     * @return never
     */
    public static function redirectToDashboard(): never {
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

    /**
     * Issue an HTTP redirect and terminate.
     *
     * Relative paths (not starting with "http") are prefixed with APP_URL.
     *
     * @param  string  $path  Absolute URL or app-relative path (e.g. '/login').
     * @return never
     */
    public static function redirect(string $path): never {
        $url = str_starts_with($path, 'http') ? $path : APP_URL . $path;
        header('Location: ' . $url);
        exit;
    }
}
