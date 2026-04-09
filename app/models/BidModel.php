<?php
/**
 * AgriLink – Bid Model
 */

require_once __DIR__ . '/../config/database.php';

class BidModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO bids (produce_id,buyer_id,quantity,bid_price,message) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([
            $data['produce_id'],
            $data['buyer_id'],
            $data['quantity'],
            $data['bid_price'],
            $data['message'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getByProduce(int $produceId): array {
        $sql = 'SELECT b.*, u.name AS buyer_name, u.region AS buyer_region, u.phone AS buyer_phone
                FROM bids b JOIN users u ON u.id = b.buyer_id
                WHERE b.produce_id = ? ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$produceId]);
        return $stmt->fetchAll();
    }

    public function getNewBidsForFarmer(int $farmerId): array {
        $sql = 'SELECT b.*, p.name AS produce_name, u.name AS buyer_name
                FROM bids b
                JOIN produce p ON p.id = b.produce_id
                JOIN users   u ON u.id = b.buyer_id
                WHERE p.farmer_id = ? AND b.status = "pending"
                ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$farmerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE bids SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }
}
