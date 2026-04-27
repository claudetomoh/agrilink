<?php
/**
 * AgriLink – Database Configuration Template
 *
 * SETUP INSTRUCTIONS:
 * 1. Copy this file:   cp app/config/config.example.php app/config/config.php
 * 2. Fill in your credentials below.
 * 3. NEVER commit config.php — it is listed in .gitignore.
 */

// ── Database ──────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'your_database_name');
define('DB_USER',    'your_database_user');
define('DB_PASS',    'your_database_password');   // ← replace with a strong password
define('DB_CHARSET', 'utf8mb4');

// ── Application ──────────────────────────────────────────────────────────
define('APP_NAME', 'AgriLink');
define('APP_URL',  'http://localhost/agrilink/public');  // no trailing slash
define('APP_ROOT', dirname(__DIR__, 2));

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
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
        || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
