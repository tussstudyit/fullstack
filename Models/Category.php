<?php
require_once __DIR__ . '/../config.php';

class Category {
    private $db;
    private $table = 'categories';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Category::getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm danh mục theo ID
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in Category::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách danh mục cùng số lượng bài đăng
     */
    public function getAllWithCount() {
        try {
            $stmt = $this->db->query("
                SELECT c.*, COUNT(p.id) as post_count
                FROM {$this->table} c
                LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'approved'
                GROUP BY c.id
                ORDER BY c.name ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Category::getAllWithCount: " . $e->getMessage());
            return [];
        }
    }
}
?>
