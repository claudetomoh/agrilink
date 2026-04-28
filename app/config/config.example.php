<?php
/**
 * AgriLink – Database Configuration Template
 *
 * SETUP INSTRUCTIONS:
 * 1. Copy this file:   cp app/config/config.example.php app/config/config.php
 * 2. Fill in your credentials below.
 * 3. NEVER commit config.php — it is listed in .gitignore.
 */

// ── Environment detection ────────────────────────────────────────────────
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost';

// ── Database ──────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    $isLocal ? 3307 : 3306);
define('DB_NAME',    $isLocal ? 'your_local_database_name' : 'your_production_database_name');
define('DB_USER',    $isLocal ? 'root' : 'your_database_user');
define('DB_PASS',    $isLocal ? '' : 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// ── Application ──────────────────────────────────────────────────────────
define('APP_NAME', 'AgriLink');
define('APP_URL',  $isLocal ? 'http://localhost/agrilink/public' : 'https://your-production-url/agrilink/public');
define('APP_ROOT', dirname(__DIR__, 2));
define('SUPPORT_EMAIL', 'support@example.com');
define('MAIL_FROM_EMAIL', $isLocal ? 'no-reply@localhost' : 'support@example.com');
define('MAIL_FROM_NAME', 'AgriLink');
define('ENABLE_EMAIL_DELIVERY', false);  // enable only after confirming the host can send mail
define('USER_TABLE', 'agrilink_users');
define('NOTIFICATION_TABLE', 'agrilink_notifications');

// ── Session ───────────────────────────────────────────────────────────────
define('SESSION_NAME',    'agrilink_session');
define('SESSION_TIMEOUT', 3600);  // seconds; 1 hour

// ── Currency / Locale ─────────────────────────────────────────────────────
define('CURRENCY_SYMBOL', '₵');
define('DEFAULT_REGION',  'Greater Accra');

// ── Ghana Regions ─────────────────────────────────────────────────────────
define('GH_REGIONS', [
    'Greater Accra', 'Ashanti', 'Brong-Ahafo', 'Northern',
    'Upper East', 'Upper West', 'Volta', 'Eastern', 'Central', 'Western',
    'Ahafo', 'Bono East', 'North East', 'Oti', 'Savannah', 'Western North',
]);

// ── Error reporting ───────────────────────────────────────────────────────
// Auto-detects local vs production. Override if needed:
// define('APP_ENV', 'production');  // or 'local'
define('APP_ENV', $isLocal ? 'local' : 'production');
define('SHOW_PASSWORD_RESET_LINK', APP_ENV !== 'production');
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
