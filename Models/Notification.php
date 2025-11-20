<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private $db;
    private $table = 'notifications';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả thông báo của user
     */
    public function getNotifications($user_id, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE recipient_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$user_id, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Notification::getNotifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy số thông báo chưa đọc
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE recipient_id = ? AND is_read = 0
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['count'] : 0;
        } catch (PDOException $e) {
            error_log("Error in Notification::getUnreadCount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo thông báo mới
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (recipient_id, sender_id, type, title, message, related_post_id, is_read, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
            ");
            
            return $stmt->execute([
                $data['recipient_id'],
                $data['sender_id'] ?? null,
                $data['type'], // 'post_comment', 'post_like', 'message', etc.
                $data['title'],
                $data['message'],
                $data['related_post_id'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead($notification_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1 
                WHERE id = ?
            ");
            return $stmt->execute([$notification_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::markAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1 
                WHERE recipient_id = ? AND is_read = 0
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::markAllAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa thông báo
     */
    public function delete($notification_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE id = ?
            ");
            return $stmt->execute([$notification_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa tất cả thông báo của user
     */
    public function deleteAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE recipient_id = ?
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::deleteAll: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thông báo theo ID
     */
    public function getById($notification_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE id = ?
            ");
            $stmt->execute([$notification_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Notification::getById: " . $e->getMessage());
            return false;
        }
    }
}
?>
