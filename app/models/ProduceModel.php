<?php
/**
 * AgriLink – Produce Model
 */

require_once __DIR__ . '/../config/database.php';

class ProduceModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $where  = ['p.status != "archived"'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[] = 'p.category = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['region'])) {
            $where[] = 'p.region = ?';
            $params[] = $filters['region'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['farmer_id'])) {
            $where[] = 'p.farmer_id = ?';
            $params[] = $filters['farmer_id'];
        }
        if (isset($filters['min_price'])) {
            $where[] = 'p.price_per_unit >= ?';
            $params[] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $where[] = 'p.price_per_unit <= ?';
            $params[] = $filters['max_price'];
        }
        if (!empty($filters['verified_only'])) {
            $where[] = 'u.is_verified = 1';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $order    = $filters['order'] ?? 'p.created_at DESC';

        $sql = "SELECT p.*,
                       u.name AS farmer_name, u.region AS farmer_region,
                       u.is_verified AS farmer_verified,
                       COALESCE(rv.avg_rating, 0) AS farmer_avg_rating,
                       COALESCE(rv.review_count, 0) AS farmer_review_count
                FROM produce p
                JOIN users u ON u.id = p.farmer_id
                LEFT JOIN (
                    SELECT reviewee_id,
                           ROUND(AVG(rating),1) AS avg_rating,
                           COUNT(*) AS review_count
                    FROM reviews GROUP BY reviewee_id
                ) rv ON rv.reviewee_id = p.farmer_id
                $whereSql
                ORDER BY $order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false {
        $sql = 'SELECT p.*, u.name AS farmer_name, u.phone AS farmer_phone, u.region AS farmer_region, u.town AS farmer_town
                FROM produce p JOIN users u ON u.id = p.farmer_id
                WHERE p.id = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $sql = 'INSERT INTO produce (farmer_id,name,category,description,quantity,unit,price_per_unit,region,town,grade,harvest_date,image,status)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['farmer_id'],
            $data['name'],
            $data['category'],
            $data['description']    ?? null,
            $data['quantity'],
            $data['unit']           ?? 'bags',
            $data['price_per_unit'],
            $data['region'],
            $data['town']           ?? null,
            $data['grade']          ?? 'A',
            $data['harvest_date']   ?? null,
            $data['image']          ?? null,
            $data['status']         ?? 'available',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $allowed = ['name','category','description','quantity','unit','price_per_unit','region','town','grade','harvest_date','image','status'];
        $fields  = [];
        $params  = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $this->db->prepare('UPDATE produce SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($params);
    }

    public function archive(int $id, int $farmerId): bool {
        $stmt = $this->db->prepare('UPDATE produce SET status = "archived" WHERE id = ? AND farmer_id = ?');
        return $stmt->execute([$id, $farmerId]);
    }

    public function delete(int $id, int $farmerId): bool {
        $stmt = $this->db->prepare('DELETE FROM produce WHERE id = ? AND farmer_id = ?');
        return $stmt->execute([$id, $farmerId]);
    }

    public function countByStatus(int $farmerId): array {
        $stmt = $this->db->prepare('SELECT status, COUNT(*) as n FROM produce WHERE farmer_id = ? GROUP BY status');
        $stmt->execute([$farmerId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getTopProduce(int $limit = 5): array {
        $sql = 'SELECT p.name, SUM(o.quantity) as total_qty, SUM(o.total_price) as revenue
                FROM orders o JOIN produce p ON p.id = o.produce_id
                WHERE o.status != "cancelled"
                GROUP BY p.id ORDER BY revenue DESC LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /** Smart matching: score produce for a buyer. */
    public function getMatches(string $buyerRegion, string $category, int $limit = 6): array {
        $all = $this->getAll(['status' => 'available', 'category' => $category]);
        usort($all, function($a, $b) use ($buyerRegion, $category) {
            $sa = 0; $sb = 0;
            if (strcasecmp($a['region'], $buyerRegion) === 0) $sa += 40;
            if (strcasecmp($b['region'], $buyerRegion) === 0) $sb += 40;
            if ($a['quantity'] >= 10) $sa += 20;
            if ($b['quantity'] >= 10) $sb += 20;
            if ($a['price_per_unit'] <= 200) $sa += 10;
            if ($b['price_per_unit'] <= 200) $sb += 10;
            return $sb - $sa;
        });
        return array_slice($all, 0, $limit);
    }

    /**
     * Return produce listings where quantity <= low_stock_threshold.
     * Used to power the farmer dashboard low-stock alerts.
     */
    public function getLowStock(int $farmerId): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM produce
             WHERE farmer_id = ? AND status = "available"
             AND quantity <= low_stock_threshold
             ORDER BY quantity ASC'
        );
        $stmt->execute([$farmerId]);
        return $stmt->fetchAll();
    }

    /**
     * Regional demand: for a given farmer, find the top-selling
     * produce categories based on recent orders (last 90 days).
     */
    public function getRegionalDemand(string $region, int $limit = 5): array {
        $stmt = $this->db->prepare(
            'SELECT p.category,
                    COUNT(o.id) AS order_count,
                    SUM(o.total_price) AS revenue
             FROM orders o
             JOIN produce p ON p.id = o.produce_id
             WHERE p.region = ? AND o.status != "cancelled"
               AND o.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
             GROUP BY p.category
             ORDER BY order_count DESC
             LIMIT ?'
        );
        $stmt->execute([$region, $limit]);
        return $stmt->fetchAll();
    }

    /* ── Aliases ─────────────────────────────────────────────────── */
    public function getById(int $id): array|false { return $this->findById($id); }
    public function search(array $filters = []): array { return $this->getAll($filters); }
    public function getByFarmer(int $farmerId): array { return $this->getAll(['farmer_id' => $farmerId]); }
}
