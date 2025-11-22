<?php
class PostImage {
    private $conn;
    private $table = 'post_images';

    public function __construct() {
        $this->conn = getDB();
    }

    // Lấy ảnh chính của bài đăng
    public function getPrimaryImage($post_id) {
        $stmt = $this->conn->prepare("SELECT image_url FROM {$this->table} WHERE post_id = ? AND is_primary = TRUE LIMIT 1");
        $stmt->execute([$post_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['image_url'] : null;
    }

    // Lấy tất cả ảnh của bài đăng
    public function getImages($post_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE post_id = ? ORDER BY display_order ASC");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm ảnh
    public function addImage($post_id, $image_url, $is_primary = false) {
        // Nếu là ảnh chính, bỏ chọn các ảnh chính trước đó
        if ($is_primary) {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET is_primary = FALSE WHERE post_id = ?");
            $stmt->execute([$post_id]);
        }

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (post_id, image_url, is_primary) VALUES (?, ?, ?)");
        return $stmt->execute([$post_id, $image_url, $is_primary ? 1 : 0]);
    }

    // Xóa ảnh
    public function deleteImage($image_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$image_id]);
    }

    // Cập nhật ảnh chính
    public function setPrimaryImage($post_id, $image_id) {
        // Bỏ chọn tất cả ảnh chính
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET is_primary = FALSE WHERE post_id = ?");
        $stmt->execute([$post_id]);

        // Đặt ảnh chính mới
        if ($image_id !== null) {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET is_primary = TRUE WHERE id = ?");
            return $stmt->execute([$image_id]);
        }
        return true;
    }

    // Lấy ảnh theo ID
    public function getImageById($image_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
