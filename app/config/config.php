<?php
/**
 * AgriLink – Database Configuration
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'mobileapps_2026B_tomoh_ikfingeh');
define('DB_USER', 'tomoh.ikfingeh');
define('DB_PASS', 'SqlUssd@2026');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'AgriLink');
define('APP_URL',  'http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public');
define('APP_ROOT', dirname(__DIR__, 2));  // points to agrilink/

define('SESSION_NAME',    'agrilink_session');
define('SESSION_TIMEOUT', 3600);  // 1 hour

define('CURRENCY_SYMBOL', '₵');
define('DEFAULT_REGION',  'Greater Accra');

// Ghana Regions
define('GH_REGIONS', [
    'Greater Accra',
    'Ashanti',
    'Brong-Ahafo',
    'Northern',
    'Upper East',
    'Upper West',
    'Volta',
    'Eastern',
    'Central',
    'Western',
    'Ahafo',
    'Bono East',
    'North East',
    'Oti',
    'Savannah',
    'Western North',
]);

// Error reporting — never expose errors to end users in production
$isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
        || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
