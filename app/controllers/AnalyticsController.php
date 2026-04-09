<?php
class AnalyticsController {

    private OrderModel    $order;
    private ProduceModel  $produce;
    private UserModel     $user;
    private DeliveryModel $delivery;

    public function __construct() {
        Auth::requireRole(['farmer', 'buyer', 'admin']);
        $this->order    = new OrderModel();
        $this->produce  = new ProduceModel();
        $this->user     = new UserModel();
        $this->delivery = new DeliveryModel();
    }

    public function dashboard(): void {
        $systemStats   = $this->order->getSystemStats();
        $delivStats    = $this->delivery->getStats();
        $topProduce    = $this->produce->getTopProduce(5);
        $roleCounts    = $this->user->countByRole();

        // Regional order data — count orders per produce region
        $regionalData  = $this->getRegionalOrderData();

        $stats = [
            'total_orders'      => $systemStats['total'] ?? 0,
            'gross_revenue'     => $systemStats['gross_revenue'] ?? 0,
            'delivered_orders'  => $systemStats['delivered'] ?? 0,
            'pending_orders'    => $systemStats['pending'] ?? 0,
            'in_transit'        => $systemStats['in_transit'] ?? 0,
            'total_farmers'     => $roleCounts['farmer'] ?? 0,
            'total_buyers'      => $roleCounts['buyer'] ?? 0,
            'total_transporters'=> $roleCounts['transport'] ?? 0,
            'delivery_rate'     => ($systemStats['total'] ?? 0) > 0
                ? round(($systemStats['delivered'] / $systemStats['total']) * 100, 1)
                : 0,
            'logistics_efficiency' => ($delivStats['total'] ?? 0) > 0
                ? round((($delivStats['delivered'] ?? 0) / $delivStats['total']) * 100, 1)
                : 0,
        ];

        $pageTitle = 'Analytics & Reporting';
        include BASE_PATH . '/app/views/analytics/dashboard.php';
    }

    private function getRegionalOrderData(): array {
        try {
            $db   = Database::connect();
            $stmt = $db->query(
                'SELECT p.region, COUNT(o.id) as order_count, SUM(o.total_price) as revenue
                 FROM orders o JOIN produce p ON p.id = o.produce_id
                 WHERE o.status != "cancelled"
                 GROUP BY p.region ORDER BY revenue DESC LIMIT 10'
            );
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function market(): void {
        $db = Database::connect();

        // Top produce by order value
        $topProduce = $this->produce->getTopProduce(6);

        // Regional demand: orders per produce region
        $regionalData = $this->getRegionalOrderData();

        // Category demand: orders per category
        try {
            $catStmt = $db->query(
                'SELECT p.category, COUNT(o.id) as order_count, SUM(o.total_price) as revenue
                 FROM orders o JOIN produce p ON p.id = o.produce_id
                 WHERE o.status != "cancelled"
                 GROUP BY p.category ORDER BY order_count DESC'
            );
            $categoryDemand = $catStmt->fetchAll();
        } catch (\Exception $e) {
            $categoryDemand = [];
        }

        // Price trend proxy: avg price_per_unit per category
        try {
            $priceStmt = $db->query(
                'SELECT category, ROUND(AVG(price_per_unit),2) as avg_price, 
                        COUNT(*) as listings, SUM(quantity) as total_qty
                 FROM produce WHERE status = "available"
                 GROUP BY category ORDER BY avg_price DESC'
            );
            $priceTrends = $priceStmt->fetchAll();
        } catch (\Exception $e) {
            $priceTrends = [];
        }

        // Market summary totals
        try {
            $summaryStmt = $db->query(
                'SELECT COUNT(DISTINCT o.buyer_id) as active_buyers,
                        COUNT(DISTINCT p.farmer_id) as active_farmers,
                        ROUND(AVG(o.total_price),2) as avg_order_value,
                        SUM(o.total_price) as total_traded
                 FROM orders o JOIN produce p ON p.id = o.produce_id
                 WHERE o.status IN ("confirmed","in_transit","delivered")'
            );
            $marketSummary = $summaryStmt->fetch() ?: [];
        } catch (\Exception $e) {
            $marketSummary = [];
        }

        $pageTitle = 'Marketplace Analytics';
        include BASE_PATH . '/app/views/analytics/market.php';
    }
}
