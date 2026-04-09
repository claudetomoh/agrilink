<?php
/**
 * AgriLink – Notification Controller
 * Handles the full notification page and AJAX mark-read endpoint.
 */
class NotificationController {

    private NotificationModel $notif;

    public function __construct() {
        Auth::requireRole(['farmer', 'buyer', 'transport', 'admin']);
        $this->notif = new NotificationModel();
    }

    /** GET /notifications — list all, then mark all as read */
    public function index(): void {
        $userId        = Session::userId();
        $notifications = $this->notif->getForUser($userId, 50);
        // Mark all read after fetching so the "unread" styling shows on this page
        $this->notif->markAllRead($userId);
        $pageTitle = 'Notifications';
        include BASE_PATH . '/app/views/notifications/index.php';
    }

    /** POST action=mark_all_read — AJAX/redirect endpoint */
    public function doMarkAllRead(): void {
        Auth::requireRole(['farmer', 'buyer', 'transport', 'admin']);
        $this->notif->markAllRead(Session::userId());

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }
        Auth::redirect('/notifications');
    }
}
