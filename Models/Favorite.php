<?php
require_once __DIR__ . '/../config.php';

class Favorite {
    private $db;
    private $table = 'favorites';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Thêm vào yêu thích
     */
    public function add($user_id, $post_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (user_id, post_id)
                VALUES (?, ?)
            ");

            $result = $stmt->execute([$user_id, $post_id]);

            if ($result) {
                return ['success' => true, 'message' => 'Đã thêm vào yêu thích'];
            }

            return ['success' => false, 'message' => 'Lỗi khi thêm vào yêu thích'];
        } catch (PDOException $e) {
            // Kiểm tra nếu là lỗi duplicate
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Bài đăng đã được thêm vào yêu thích'];
            }
            error_log("Error in Favorite::add: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa khỏi yêu thích
     */
    public function remove($user_id, $post_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE user_id = ? AND post_id = ?
            ");

            $result = $stmt->execute([$user_id, $post_id]);

            if ($result) {
                return ['success' => true, 'message' => 'Đã xóa khỏi yêu thích'];
            }

            return ['success' => false, 'message' => 'Lỗi khi xóa'];
        } catch (PDOException $e) {
            error_log("Error in Favorite::remove: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách bài đăng yêu thích của user
     */
    public function getByUserId($user_id, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.full_name, c.name as category_name
                FROM {$this->table} f
                JOIN posts p ON f.post_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE f.user_id = ? AND p.status = 'approved'
                ORDER BY f.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$user_id, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Favorite::getByUserId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra bài đăng có trong yêu thích không
     */
    public function isFavorited($user_id, $post_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table}
                WHERE user_id = ? AND post_id = ?
            ");
            $stmt->execute([$user_id, $post_id]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error in Favorite::isFavorited: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm yêu thích của user
     */
    public function countByUserId($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error in Favorite::countByUserId: " . $e->getMessage());
            return 0;
        }
    }
}
?>
