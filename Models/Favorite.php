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
    public function add($user_id, $post_id) { // Thêm yêu thích: insert (user, post) + xử lý duplicate
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (user_id, post_id)
                VALUES (?, ?) -- Insert user_id + post_id
            ");

            $result = $stmt->execute([$user_id, $post_id]); // Thực thi insert

            if ($result) { // Nếu thành công
                return ['success' => true, 'message' => 'Đã thêm vào yêu thích'];
            }

            return ['success' => false, 'message' => 'Lỗi khi thêm vào yêu thích'];
        } catch (PDOException $e) {
            // Kiểm tra nếu là lỗi duplicate
            if (strpos($e->getMessage(), 'Duplicate') !== false) { // Đã thêm trước đó
                return ['success' => false, 'message' => 'Bài đăng đã được thêm vào yêu thích'];
            }
            error_log("Error in Favorite::add: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa khỏi yêu thích
     */
    public function remove($user_id, $post_id) { // Xóa yêu thích: DELETE where user + post
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE user_id = ? AND post_id = ? -- Xóa bản ghi của user này
            ");

            $result = $stmt->execute([$user_id, $post_id]); // Thực thi DELETE

            if ($result) { // Nếu xóa thành công
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
    public function getByUserId($user_id, $limit = 10, $offset = 0) { // Lấy yêu thích: join posts + users + categories
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.full_name, c.name as category_name -- Chọn posts + user + category
                FROM {$this->table} f
                JOIN posts p ON f.post_id = p.id -- Join posts
                JOIN users u ON p.user_id = u.id -- Join users (chủ bài)
                JOIN categories c ON p.category_id = c.id -- Join categories
                WHERE f.user_id = ? AND p.status = 'approved' -- Chỉ bài đã duyệt
                ORDER BY f.created_at DESC -- Mới nhất trước
                LIMIT ? OFFSET ? -- Phân trang
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
