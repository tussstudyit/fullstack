<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Notification.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * Lấy danh sách thông báo
     */
    public function getNotifications() {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $user_id = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $notifications = $this->notificationModel->getNotifications($user_id, $limit, $offset);
        $unread_count = $this->notificationModel->getUnreadCount($user_id);

        return [
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ];
    }

    /**
     * Lấy số thông báo chưa đọc (dùng cho badge trên navbar)
     */
    public function getUnreadCount() {
        if (!isLoggedIn()) {
            return ['success' => false, 'count' => 0];
        }

        $user_id = $_SESSION['user_id'];
        $count = $this->notificationModel->getUnreadCount($user_id);

        return ['success' => true, 'count' => $count];
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $notification_id = sanitize($_POST['notification_id'] ?? '');

        if (empty($notification_id)) {
            return ['success' => false, 'message' => 'Notification ID required'];
        }

        $result = $this->notificationModel->markAsRead($notification_id);

        if ($result) {
            return ['success' => true, 'message' => 'Marked as read'];
        } else {
            return ['success' => false, 'message' => 'Failed to mark as read'];
        }
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $user_id = $_SESSION['user_id'];
        $result = $this->notificationModel->markAllAsRead($user_id);

        if ($result) {
            return ['success' => true, 'message' => 'All marked as read'];
        } else {
            return ['success' => false, 'message' => 'Failed to mark all as read'];
        }
    }

    /**
     * Xóa thông báo
     */
    public function deleteNotification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $notification_id = sanitize($_POST['notification_id'] ?? '');

        if (empty($notification_id)) {
            return ['success' => false, 'message' => 'Notification ID required'];
        }

        $result = $this->notificationModel->delete($notification_id);

        if ($result) {
            return ['success' => true, 'message' => 'Notification deleted'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete notification'];
        }
    }

    /**
     * Xử lý hành động
     */
    public function handleAction($action) {
        switch ($action) {
            case 'getNotifications':
                return $this->getNotifications();
            case 'getUnreadCount':
                return $this->getUnreadCount();
            case 'markAsRead':
                return $this->markAsRead();
            case 'markAllAsRead':
                return $this->markAllAsRead();
            case 'deleteNotification':
                return $this->deleteNotification();
            default:
                return ['success' => false, 'message' => 'Action not found'];
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = sanitize($_POST['action'] ?? $_GET['action'] ?? '');
    $controller = new NotificationController();
    $result = $controller->handleAction($action);
    
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
