<?php
class TransportController {

    private DeliveryModel $delivery;
    private OrderModel    $order;

    public function __construct() {
        Auth::requireRole(['transport']);
        $this->delivery = new DeliveryModel();
        $this->order    = new OrderModel();
    }

    /* ── Dashboard ──────────────────────────────────────────────── */
    public function dashboard(): void {
        $transporterId = Session::userId();
        $active  = $this->delivery->getByTransporter($transporterId, 'in_transit');
        $pending = $this->delivery->getByTransporter($transporterId, 'pending');
        $done    = $this->delivery->getByTransporter($transporterId, 'delivered');
        $performance = $this->delivery->getTransportPerformance($transporterId);

        $stats = [
            'active'    => count($active),
            'pending'   => count($pending),
            'completed' => count($done),
            'earnings'  => count($done) * 50, // ₵50 fixed rate placeholder
        ];
        $pageTitle = 'Transport Dashboard';
        include BASE_PATH . '/app/views/transport/dashboard.php';
    }

    /* ── Available Jobs ─────────────────────────────────────────── */
    public function jobs(): void {
        $unassigned = $this->delivery->getUnassigned();
        $pageTitle  = 'Available Jobs';
        include BASE_PATH . '/app/views/transport/jobs.php';
    }

    /* ── Accept Job ─────────────────────────────────────────────── */
    public function doAcceptJob(): void {
        Auth::requireRole(['transport']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/transport/jobs');
        }

        $deliveryId    = (int)($_POST['delivery_id'] ?? 0);
        $transporterId = Session::userId();

        $delivery = $this->delivery->getById($deliveryId);
        if (!$delivery || $delivery['transport_id']) {
            Session::setFlash('error', 'Job no longer available.');
            Auth::redirect('/transport/jobs');
        }

        $this->delivery->assignTransporter($deliveryId, $transporterId);
        Session::setFlash('success', 'Job accepted! You can now track this delivery.');
        Auth::redirect('/transport/dashboard');
    }

    /* ── Delivery Timeline ──────────────────────────────────────── */
    public function deliveryTimeline(?string $id = null): void {
        $deliveryId = $id ?? ($_GET['id'] ?? null);
        if (!$deliveryId) Auth::redirect('/transport/dashboard');

        $transporterId = Session::userId();
        $delivery = $this->delivery->getById((int)$deliveryId);

        if (!$delivery || (int)$delivery['transport_id'] !== $transporterId) {
            Session::setFlash('error', 'Delivery not found.');
            Auth::redirect('/transport/dashboard');
        }

        $pageTitle = 'Delivery #' . str_pad($deliveryId, 4, '0', STR_PAD_LEFT);
        include BASE_PATH . '/app/views/transport/delivery_timeline.php';
    }

    /* ── Update Delivery Status ─────────────────────────────────── */
    public function doUpdateDeliveryStatus(): void {
        Auth::requireRole(['transport']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/transport/dashboard');
        }

        $deliveryId    = (int)($_POST['delivery_id'] ?? 0);
        $transporterId = Session::userId();
        $newStatus     = sanitize($_POST['status'] ?? '');
        $note          = sanitize($_POST['note'] ?? '');

        $delivery = $this->delivery->getById($deliveryId);
        if ($delivery && (int)$delivery['transport_id'] === $transporterId) {
            $this->delivery->updateStatus($deliveryId, $newStatus, $note);
            // Update linked order status
            if ($delivery['order_id'] && $newStatus === 'delivered') {
                $this->order->updateStatus($delivery['order_id'], 'delivered');
            }
            Session::setFlash('success', 'Delivery status updated.');
        }
        Auth::redirect('/transport/delivery?id=' . $deliveryId);
    }
}
