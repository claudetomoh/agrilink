<?php
class AdminController {

    private UserModel     $user;
    private OrderModel    $order;
    private DeliveryModel $delivery;
    private ProduceModel  $produce;
    private NotificationModel $notif;

    public function __construct() {
        Auth::requireRole(['admin']);
        $this->user     = new UserModel();
        $this->order    = new OrderModel();
        $this->delivery = new DeliveryModel();
        $this->produce  = new ProduceModel();
        $this->notif    = new NotificationModel();
    }

    /* ── Dashboard ──────────────────────────────────────────────── */
    public function dashboard(): void {
        $allUsers    = $this->user->getAllUsers();
        $allOrders   = $this->order->getAll();
        $allDeliveries = $this->delivery->getAll();
        $allProduces   = $this->produce->getAll();

        $stats = [
            'total_users'      => count($allUsers),
            'farmers'          => count(array_filter($allUsers, fn($u) => $u['role'] === 'farmer')),
            'buyers'           => count(array_filter($allUsers, fn($u) => $u['role'] === 'buyer')),
            'transporters'     => count(array_filter($allUsers, fn($u) => $u['role'] === 'transport')),
            'verified_users'   => count(array_filter($allUsers, fn($u) => !empty($u['is_verified']))),
            'total_listings'   => count(array_filter($allProduces, fn($p) => $p['status'] === 'available')),
            'total_orders'     => count($allOrders),
            'pending_orders'   => count(array_filter($allOrders, fn($o) => $o['status'] === 'pending')),
            'active_deliveries'=> count(array_filter($allDeliveries, fn($d) => $d['status'] === 'in_transit')),
            'total_revenue'    => array_sum(array_column(
                array_filter($allOrders, fn($o) => in_array($o['status'],['delivered','completed'])),
                'total_price'
            )),
            'delivery_rate'    => count($allOrders) > 0
                ? round((count(array_filter($allOrders, fn($o) => $o['status'] === 'delivered')) / count($allOrders)) * 100, 1)
                : 0,
        ];
        $recentOrders = array_slice($allOrders, 0, 8);
        $recentUsers  = array_slice($allUsers,  0, 5);
        $topProduce   = $this->produce->getTopProduce(5);
        $pageTitle = 'Admin Dashboard';
        include BASE_PATH . '/app/views/admin/dashboard.php';
    }

    /* ── Users Management ────────────────────────────────────────── */
    public function users(): void {
        $search = sanitize($_GET['q'] ?? '');
        $role   = sanitize($_GET['role'] ?? '');
        $users  = $this->user->getAllUsers($search, $role);
        $pageTitle = 'Manage Users';
        include BASE_PATH . '/app/views/admin/users.php';
    }

    /* ── Orders Management ───────────────────────────────────────── */
    public function orders(): void {
        $orders    = $this->order->getAll();
        $pageTitle = 'All Orders';
        include BASE_PATH . '/app/views/admin/orders.php';
    }

    public function doUpdateOrderStatus(): void {
        Auth::requireRole(['admin']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/admin/orders');
        }
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status  = sanitize($_POST['status'] ?? '');
        if ($orderId && $status) $this->order->updateStatus($orderId, $status);
        Session::setFlash('success', 'Order status updated.');
        Auth::redirect('/admin/orders');
    }

    /* ── Deliveries Management ───────────────────────────────────── */
    public function deliveries(): void {
        $deliveries = $this->delivery->getAll();
        $pageTitle  = 'All Deliveries';
        include BASE_PATH . '/app/views/admin/deliveries.php';
    }

    /* ── Analytics ───────────────────────────────────────────────── */
    public function analytics(): void {
        Auth::redirect('/analytics');
    }

    /* ── Verify User ─────────────────────────────────────────────── */
    public function doVerifyUser(): void {
        Auth::requireRole(['admin']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/admin/users');
        }
        $userId   = (int)($_POST['user_id'] ?? 0);
        $verified = (bool)(int)($_POST['verified'] ?? 0);
        $this->user->setVerified($userId, $verified);

        // Notify the user about their verification status
        $msg = $verified
            ? 'Congratulations! Your account has been verified by AgriLink. A verified badge is now displayed on your profile.'
            : 'Your account verification has been revoked. Contact support for details.';
        $title = $verified ? 'Account Verified ✓' : 'Verification Revoked';
        $this->notif->create($userId, 'account_verified', $title, $msg);

        Session::setFlash('success', $verified ? 'User has been verified.' : 'Verification revoked.');
        Auth::redirect('/admin/users');
    }

    /* ── Toggle Account Active ───────────────────────────────────── */
    public function doToggleActive(): void {
        Auth::requireRole(['admin']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/admin/users');
        }
        $userId = (int)($_POST['user_id'] ?? 0);
        $this->user->toggleActive($userId);
        Session::setFlash('success', 'User account status updated.');
        Auth::redirect('/admin/users');
    }
}
