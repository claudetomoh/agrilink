<?php
/**
 * AgriLink – Front Controller / Router
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/config/config.php';
require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/core/Session.php';
require_once BASE_PATH . '/app/core/Auth.php';
require_once BASE_PATH . '/app/core/Helpers.php';
require_once BASE_PATH . '/app/core/Mailer.php';

// Auto-load models
foreach (glob(BASE_PATH . '/app/models/*.php') as $model) {
    require_once $model;
}
// Auto-load controllers
foreach (glob(BASE_PATH . '/app/controllers/*.php') as $ctrl) {
    require_once $ctrl;
}

Session::start();

// ── Parse URL ──────────────────────────────────────────────────────────────
$url = trim($_GET['url'] ?? 'home', '/');
$segments = explode('/', $url);

$segment0 = strtolower($segments[0] ?? 'home');
$segment1 = strtolower($segments[1] ?? 'index');
$segment2 = $segments[2] ?? null;

// ── Route Table ────────────────────────────────────────────────────────────
$routes = [
    ''        => ['HomeController',      'index'],
    'home'    => ['HomeController',      'index'],

    // Auth
    'login'             => ['AuthController',    'showLogin'],
    'register'          => ['AuthController',    'showRegister'],
    'forgot-password'   => ['AuthController',    'showForgotPassword'],
    'reset-password'    => ['AuthController',    'showResetPassword'],
    'logout'            => ['AuthController',    'doLogout'],
    'auth/onboarding'   => ['AuthController',    'showOnboarding'],

    // Farmer
    'farmer'              => ['FarmerController', 'dashboard'],
    'farmer/dashboard'    => ['FarmerController', 'dashboard'],
    'farmer/listings'     => ['FarmerController', 'listings'],
    'farmer/listings/add' => ['FarmerController', 'addListing'],
    'farmer/listings/edit'=> ['FarmerController', 'editListing'],
    'farmer/orders'       => ['FarmerController', 'orders'],
    'farmer/profile'      => ['FarmerController', 'profile'],

    // Buyer
    'buyer'                     => ['BuyerController', 'marketplace'],
    'buyer/marketplace'         => ['BuyerController', 'marketplace'],
    'buyer/product'             => ['BuyerController', 'productDetail'],
    'buyer/orders'              => ['BuyerController', 'orders'],
    'buyer/matching'            => ['BuyerController', 'matching'],
    'buyer/review'              => ['BuyerController', 'showReview'],

    // Notifications
    'notifications'             => ['NotificationController', 'index'],

    // Transport
    'transport'                  => ['TransportController', 'dashboard'],
    'transport/dashboard'        => ['TransportController', 'dashboard'],
    'transport/jobs'             => ['TransportController', 'jobs'],
    'transport/delivery'         => ['TransportController', 'deliveryTimeline'],

    // Admin
    'admin'             => ['AdminController', 'dashboard'],
    'admin/dashboard'   => ['AdminController', 'dashboard'],
    'admin/users'       => ['AdminController', 'users'],
    'admin/orders'      => ['AdminController', 'orders'],
    'admin/deliveries'  => ['AdminController', 'deliveries'],
    'admin/analytics'   => ['AdminController', 'analytics'],

    // Analytics (standalone)
    'analytics'         => ['AnalyticsController', 'dashboard'],
    'analytics/market'  => ['AnalyticsController', 'market'],
];

// Build segment path for lookup
$path = implode('/', array_filter([$segment0, ($segment0 !== $segment1 ? $segment1 : '')]));
$path = trim($path, '/');

// Try full path first, then segment0 only
$key = null;
if (isset($routes[$url])) {
    $key = $url;
} elseif (isset($routes[$path])) {
    $key = $path;
} elseif (isset($routes[$segment0])) {
    $key = $segment0;
}

// ── Dispatch ───────────────────────────────────────────────────────────────
if ($key !== null) {
    [$controllerClass, $method] = $routes[$key];

    if (!class_exists($controllerClass)) {
        http_response_code(500);
        die('Controller not found: ' . htmlspecialchars($controllerClass));
    }

    $controller = new $controllerClass();

    // POST override: for login/register dispatch to doLogin / doRegister
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'login')    { $controller->doLogin();   exit; }
        if ($action === 'register') { $controller->doRegister(); exit; }
        if ($action === 'forgot_password') { $controller->doForgotPassword(); exit; }
        if ($action === 'reset_password') { $controller->doResetPassword(); exit; }
        if ($action === 'onboarding') { $controller->doOnboarding(); exit; }
        if ($action === 'add_listing')    { $controller->doAddListing();    exit; }
        if ($action === 'edit_listing')   { $controller->doEditListing();   exit; }
        if ($action === 'delete_listing') { $controller->doDeleteListing(); exit; }
        if ($action === 'place_order')    { $controller->doPlaceOrder();    exit; }
        if ($action === 'update_order_status') { $controller->doUpdateOrderStatus(); exit; }
        if ($action === 'update_delivery_status') { $controller->doUpdateDeliveryStatus(); exit; }
        if ($action === 'accept_job')    { $controller->doAcceptJob();    exit; }
        if ($action === 'place_bid')     { $controller->doPlaceBid();     exit; }
        if ($action === 'update_profile')        { $controller->doUpdateProfile();       exit; }
        if ($action === 'submit_review')           { $controller->doSubmitReview();        exit; }
        if ($action === 'verify_user')             { $controller->doVerifyUser();          exit; }
        if ($action === 'toggle_active')           { $controller->doToggleActive();        exit; }
        if ($action === 'mark_all_read')           { $controller->doMarkAllRead();         exit; }
    }

    $controller->$method($segment2);
} else {
    http_response_code(404);
    $pageTitle = '404 – Page Not Found';
    include BASE_PATH . '/app/views/errors/404.php';
}
