<?php
/**
 * AgriLink – Notification Model
 */

require_once __DIR__ . '/../config/database.php';

class NotificationModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(int $userId, string $type, string $title, string $message, string $link = null): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (user_id, type, title, message, link) VALUES (?,?,?,?,?)'
        );
        return $stmt->execute([$userId, $type, $title, $message, $link]);
    }

    public function getForUser(int $userId, int $limit = 20): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function markAllRead(int $userId): bool {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        return $stmt->execute([$userId]);
    }

    public function markRead(int $id, int $userId): bool {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $userId]);
    }
}
