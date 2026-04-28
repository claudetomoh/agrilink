<?php
/**
 * AgriLink – Order Model
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Helpers.php';

class OrderModel {
    private PDO $db;
    private string $userTable;

    public function __construct() {
        $this->db = Database::connect();
        $this->userTable = USER_TABLE;
    }

    public function create(array $data): int {
        // Ensure unique ref
        do {
            $ref = Helpers::generateOrderRef();
        } while ($this->refExists($ref));

        $sql = 'INSERT INTO orders (order_ref,buyer_id,farmer_id,produce_id,quantity,unit,unit_price,total_price,status,delivery_address,notes)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $ref,
            $data['buyer_id'],
            $data['farmer_id'],
            $data['produce_id'],
            $data['quantity'],
            $data['unit'],
            $data['unit_price'],
            $data['total_price'],
            'pending',
            $data['delivery_address'] ?? null,
            $data['notes']            ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    private function refExists(string $ref): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE order_ref = ?');
        $stmt->execute([$ref]);
        return (bool) $stmt->fetchColumn();
    }

    public function findById(int $id): array|false {
        $sql = 'SELECT o.*,
                       b.name AS buyer_name, b.phone AS buyer_phone, b.region AS buyer_region,
                       f.name AS farmer_name, f.phone AS farmer_phone,
                       p.name AS produce_name, p.unit AS produce_unit, p.image AS produce_image,
                       d.id AS delivery_id, d.status AS delivery_status, d.vehicle_code,
                       d.origin, d.destination, d.estimated_arrival, d.picked_up_at, d.delivered_at
                FROM orders o
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                JOIN produce p ON p.id = o.produce_id
                LEFT JOIN deliveries d ON d.order_id = o.id
                WHERE o.id = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUser(int $userId, string $role, array $filters = []): array {
        $col    = $role === 'buyer' ? 'o.buyer_id' : 'o.farmer_id';
        $where  = ["$col = ?"];
        $params = [$userId];

        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(o.order_ref LIKE ? OR p.name LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT o.*, p.name AS produce_name, p.image AS produce_image,
                       b.name AS buyer_name, f.name AS farmer_name,
                       d.status AS delivery_status
                FROM orders o
                JOIN produce p ON p.id = o.produce_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                LEFT JOIN deliveries d ON d.order_id = o.id
                $whereSql
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAll(array $filters = []): array {
        $where  = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(o.order_ref LIKE ? OR b.name LIKE ? OR p.name LIKE ?)';
            $params[] = $s = '%' . $filters['search'] . '%';
            $params[] = $s;
            $params[] = $s;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT o.*, p.name AS produce_name, b.name AS buyer_name, f.name AS farmer_name,
                       d.status AS delivery_status
                FROM orders o
                JOIN produce p ON p.id = o.produce_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                LEFT JOIN deliveries d ON d.order_id = o.id
                $whereSql
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function getByBuyer(int $buyerId, array $filters = []): array {
        return $this->getByUser($buyerId, 'buyer', $filters);
    }

    public function getBySeller(int $farmerId, array $filters = []): array {
        return $this->getByUser($farmerId, 'farmer', $filters);
    }

    public function getSalesStats(int $farmerId): array {
        $stmt = $this->db->prepare(
            'SELECT
               COUNT(*) as total_orders,
               SUM(CASE WHEN status="delivered" THEN total_price ELSE 0 END) as total_revenue,
               SUM(CASE WHEN status NOT IN ("cancelled","delivered") THEN 1 ELSE 0 END) as active_orders,
               SUM(CASE WHEN status="pending" THEN 1 ELSE 0 END) as pending_orders
             FROM orders WHERE farmer_id = ?'
        );
        $stmt->execute([$farmerId]);
        return $stmt->fetch();
    }

    public function getMonthlyRevenue(int $farmerId, int $months = 6): array {
        $stmt = $this->db->prepare(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as revenue
             FROM orders WHERE farmer_id = ? AND status = "delivered"
             AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
             GROUP BY month ORDER BY month'
        );
        $stmt->execute([$farmerId, $months]);
        return $stmt->fetchAll();
    }

    public function getSystemStats(): array {
        $stmt = $this->db->query(
            'SELECT
               COUNT(*) as total,
               SUM(CASE WHEN status="delivered"  THEN 1 ELSE 0 END) as delivered,
               SUM(CASE WHEN status="in_transit" THEN 1 ELSE 0 END) as in_transit,
               SUM(CASE WHEN status="pending"    THEN 1 ELSE 0 END) as pending,
               SUM(total_price) as gross_revenue
             FROM orders WHERE status != "cancelled"'
        );
        return $stmt->fetch();
    }
}
