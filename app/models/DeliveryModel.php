<?php
/**
 * AgriLink – Delivery Model
 */

require_once __DIR__ . '/../config/database.php';

class DeliveryModel {
    private PDO $db;
    private string $userTable;

    public function __construct() {
        $this->db = Database::connect();
        $this->userTable = USER_TABLE;
    }

    public function createForOrder(int $orderId, string $origin, string $destination): int {
        $sql = 'INSERT INTO deliveries (order_id, origin, destination, status) VALUES (?,?,?,"pending")';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId, $origin, $destination]);
        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): array|false {
        $sql = "SELECT d.*, o.order_ref, o.total_price,
                       b.name AS buyer_name, b.phone AS buyer_phone,
                       f.name AS farmer_name, f.phone AS farmer_phone,
                       p.name AS produce_name, o.quantity, o.unit,
                       t.name AS transport_name, t.phone AS transport_phone
                FROM deliveries d
                JOIN orders  o ON o.id = d.order_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                JOIN produce p ON p.id = o.produce_id
                LEFT JOIN {$this->userTable} t ON t.id = d.transport_id
                WHERE d.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByOrder(int $orderId): array|false {
        $stmt = $this->db->prepare('SELECT * FROM deliveries WHERE order_id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }

    public function getForTransport(int $transportId, ?string $status = null): array {
        $where  = ['d.transport_id = ?'];
        $params = [$transportId];
        if ($status) { $where[] = 'd.status = ?'; $params[] = $status; }
        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT d.*, o.order_ref, p.name AS produce_name, o.quantity, o.unit,
                       b.name AS buyer_name, f.name AS farmer_name
                FROM deliveries d
                JOIN orders  o ON o.id = d.order_id
                JOIN produce p ON p.id = o.produce_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                $whereSql ORDER BY d.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAvailableJobs(): array {
        $sql = "SELECT d.*, o.order_ref, p.name AS produce_name, o.quantity, o.unit,
                       b.name AS buyer_name, f.name AS farmer_name, f.region AS origin_region
                FROM deliveries d
                JOIN orders  o ON o.id = d.order_id
                JOIN produce p ON p.id = o.produce_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                WHERE d.status = 'pending' AND d.transport_id IS NULL
                ORDER BY d.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getAll(?string $status = null): array {
        $where  = [];
        $params = [];
        if ($status) { $where[] = 'd.status = ?'; $params[] = $status; }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT d.*, o.order_ref, p.name AS produce_name, o.quantity, o.unit,
                       b.name AS buyer_name, f.name AS farmer_name,
                       t.name AS transport_name
                FROM deliveries d
                JOIN orders  o ON o.id = d.order_id
                JOIN produce p ON p.id = o.produce_id
                JOIN {$this->userTable} b ON b.id = o.buyer_id
                JOIN {$this->userTable} f ON f.id = o.farmer_id
                LEFT JOIN {$this->userTable} t ON t.id = d.transport_id
                $whereSql ORDER BY d.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function acceptJob(int $deliveryId, int $transportId): bool {
        $stmt = $this->db->prepare(
            'UPDATE deliveries SET transport_id=?, status="assigned", assigned_at=NOW() WHERE id=? AND status="pending"'
        );
        return $stmt->execute([$transportId, $deliveryId]);
    }

    public function updateStatus(int $id, string $status, string $note = ''): bool {
        $extra = match($status) {
            'in_transit' => ', picked_up_at = NOW()',
            'delivered'  => ', delivered_at = NOW()',
            default      => '',
        };
        $noteClause = $note !== '' ? ', notes = ?' : '';
        $sql  = "UPDATE deliveries SET status = ? $extra $noteClause WHERE id = ?";
        $args = [$status];
        if ($note !== '') $args[] = $note;
        $args[] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($args);
    }

    public function getActiveCount(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM deliveries WHERE status IN ("assigned","in_transit")');
        return (int) $stmt->fetchColumn();
    }

    public function getStats(): array {
        $stmt = $this->db->query(
            'SELECT
               COUNT(*) as total,
               SUM(CASE WHEN status="pending"    THEN 1 ELSE 0 END) as pending,
               SUM(CASE WHEN status="assigned"   THEN 1 ELSE 0 END) as assigned,
               SUM(CASE WHEN status="in_transit" THEN 1 ELSE 0 END) as in_transit,
               SUM(CASE WHEN status="delivered"  THEN 1 ELSE 0 END) as delivered
             FROM deliveries'
        );
        return $stmt->fetch();
    }

    /**
     * Performance statistics for a transport provider.
     * Returns: total_jobs, completed, avg_hours per delivery, on_time_rate %, performance_score.
     */
    public function getTransportPerformance(int $transportId): array {
        try {
            $stmt = $this->db->prepare(
                'SELECT
                   COUNT(*) AS total_jobs,
                   SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) AS completed,
                   ROUND(AVG(
                     CASE WHEN delivered_at IS NOT NULL AND picked_up_at IS NOT NULL
                          THEN TIMESTAMPDIFF(HOUR, picked_up_at, delivered_at)
                          ELSE NULL END
                   ), 1) AS avg_hours,
                   SUM(CASE
                     WHEN status = "delivered"
                          AND estimated_arrival IS NOT NULL
                          AND delivered_at <= estimated_arrival
                     THEN 1 ELSE 0 END) AS on_time_count
                 FROM deliveries WHERE transport_id = ?'
            );
            $stmt->execute([$transportId]);
            $row = $stmt->fetch();
        } catch (\Exception $e) {
            return ['total_jobs'=>0,'completed'=>0,'avg_hours'=>0,'on_time_rate'=>0,'performance_score'=>0];
        }

        $completed    = (int)($row['completed'] ?? 0);
        $total        = (int)($row['total_jobs'] ?? 0);
        $onTimeCount  = (int)($row['on_time_count'] ?? 0);
        $onTimeRate   = $completed > 0 ? round(($onTimeCount / $completed) * 100) : 0;
        // Score: 60% weight on completion ratio, 40% on on-time rate
        $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;
        $performanceScore = (int)round(($completionRate * 0.6) + ($onTimeRate * 0.4));

        return [
            'total_jobs'        => $total,
            'completed'         => $completed,
            'avg_hours'         => $row['avg_hours'] ?? 0,
            'on_time_rate'      => $onTimeRate,
            'performance_score' => min(100, $performanceScore),
        ];
    }

    /* ── Aliases ─────────────────────────────────────────────────── */
    public function getById(int $id): array|false { return $this->findById($id); }
    public function getByTransporter(int $transporterId, ?string $status = null): array { return $this->getForTransport($transporterId, $status); }
    public function getUnassigned(): array { return $this->getAvailableJobs(); }
    public function assignTransporter(int $deliveryId, int $transporterId): bool { return $this->acceptJob($deliveryId, $transporterId); }
}
