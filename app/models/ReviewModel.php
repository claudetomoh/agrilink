<?php
/**
 * AgriLink – Review Model
 */

require_once __DIR__ . '/../config/database.php';

class ReviewModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Create or update a review (upsert via ON DUPLICATE KEY UPDATE,
     * relies on unique key on order_id + reviewer_id).
     */
    public function create(array $data): void {
        $sql = 'INSERT INTO reviews (order_id, reviewer_id, reviewee_id, produce_id, rating, comment)
                VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)';
        $this->db->prepare($sql)->execute([
            $data['order_id'],
            $data['reviewer_id'],
            $data['reviewee_id'],
            $data['produce_id'] ?? null,
            max(1, min(5, (int)($data['rating'] ?? 3))),
            $data['comment'] ?? null,
        ]);
    }

    /** All reviews received by a user (as reviewee). */
    public function getForUser(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.name AS reviewer_name, u.role AS reviewer_role
             FROM reviews r
             JOIN users u ON u.id = r.reviewer_id
             WHERE r.reviewee_id = ?
             ORDER BY r.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Average rating + count for a user. */
    public function getAvgRating(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total
             FROM reviews WHERE reviewee_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: ['avg_rating' => 0, 'total' => 0];
    }

    /** Check whether a reviewer already reviewed a specific order. */
    public function hasReviewed(int $orderId, int $reviewerId): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM reviews WHERE order_id = ? AND reviewer_id = ?'
        );
        $stmt->execute([$orderId, $reviewerId]);
        return (bool) $stmt->fetchColumn();
    }

    /** All reviews attached to a specific order. */
    public function getByOrder(int $orderId): array {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.name AS reviewer_name, u.role AS reviewer_role
             FROM reviews r
             JOIN users u ON u.id = r.reviewer_id
             WHERE r.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /** Top-N highest-rated farmers for social proof widget. */
    public function getTopRatedFarmers(int $limit = 5): array {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.region, u.is_verified,
                    ROUND(AVG(r.rating), 1) AS avg_rating, COUNT(r.id) AS review_count
             FROM reviews r
             JOIN users u ON u.id = r.reviewee_id
             WHERE u.role = "farmer"
             GROUP BY u.id
             HAVING review_count > 0
             ORDER BY avg_rating DESC, review_count DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
