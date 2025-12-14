<?php
require_once __DIR__ . '/../config.php';

class Post {
    private $db;
    private $table = 'posts';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Tìm post theo ID
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.full_name, u.phone, u.email, c.name as category_name
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in Post::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm post theo slug
     */
    public function findBySlug($slug) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.full_name, u.phone, u.email, c.name as category_name
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ?
            ");
            $stmt->execute([$slug]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in Post::findBySlug: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo bài đăng mới
     */
    public function create($data) {
        try {
            // Generate slug from title
            require_once __DIR__ . '/../helpers.php';
            $slug = getUniqueSlug($data['title']);
            
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (
                    user_id, category_id, title, slug, description, address, district, city, 
                    price, area, room_type, max_people, gender, amenities, utilities, 
                    rules, available_from, deposit_amount, electric_price, water_price, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['user_id'],
                $data['category_id'] ?? 1,
                $data['title'],
                $slug,
                $data['description'],
                $data['address'],
                $data['district'] ?? null,
                $data['city'] ?? null,
                $data['price'],
                $data['area'] ?? null,
                $data['room_type'] ?? 'single',
                $data['max_people'] ?? 1,
                $data['gender'] ?? 'any',
                $data['amenities'] ?? null,
                $data['utilities'] ?? null,
                $data['rules'] ?? null,
                $data['available_from'] ?? date('Y-m-d'),
                $data['deposit_amount'] ?? null,
                $data['electric_price'] ?? null,
                $data['water_price'] ?? null,
                $data['status'] ?? 'approved'
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Tạo bài đăng thành công',
                    'post_id' => $this->db->lastInsertId(),
                    'slug' => $slug
                ];
            }

            return ['success' => false, 'message' => 'Lỗi khi tạo bài đăng'];
        } catch (PDOException $e) {
            error_log("Error in Post::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật bài đăng
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $values = [];

            $allowedFields = ['title', 'description', 'address', 'district', 'city', 'price', 'area', 'room_type', 'max_people', 'gender', 'amenities', 'utilities', 'rules', 'available_from', 'deposit_amount', 'electric_price', 'water_price', 'status'];

            // If title is being updated, regenerate slug
            if (isset($data['title'])) {
                require_once __DIR__ . '/../helpers.php';
                $data['slug'] = getUniqueSlug($data['title'], $id);
                $allowedFields[] = 'slug';
            }

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }

            if (empty($fields)) {
                return ['success' => false, 'message' => 'Không có dữ liệu cập nhật'];
            }

            $values[] = $id;
            $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($values);

            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];
        } catch (PDOException $e) {
            error_log("Error in Post::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa bài đăng
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                return ['success' => true, 'message' => 'Xóa bài đăng thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi xóa bài đăng'];
        } catch (PDOException $e) {
            error_log("Error in Post::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách bài đăng với filter
     */
    public function getFiltered($filters = [], $limit = 10, $offset = 0) {
        try {
            $query = "
                SELECT p.*, u.full_name, u.phone, c.name as category_name
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'approved'
            ";
            $params = [];

            if (!empty($filters['search'])) {
                $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
                $params[] = "%{$filters['search']}%";
                $params[] = "%{$filters['search']}%";
            }

            if (!empty($filters['category_id'])) {
                $query .= " AND p.category_id = ?";
                $params[] = $filters['category_id'];
            }

            if (!empty($filters['district'])) {
                $query .= " AND p.district = ?";
                $params[] = $filters['district'];
            }

            if (!empty($filters['city'])) {
                $query .= " AND p.city = ?";
                $params[] = $filters['city'];
            }

            if (!empty($filters['price_min'])) {
                $query .= " AND p.price >= ?";
                $params[] = $filters['price_min'];
            }

            if (!empty($filters['price_max'])) {
                $query .= " AND p.price <= ?";
                $params[] = $filters['price_max'];
            }

            $query .= " ORDER BY p.is_featured DESC, p.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Post::getFiltered: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách bài đăng nổi bật
     */
    public function getFeatured($limit = 3) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.full_name, c.name as category_name
                FROM {$this->table} p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'approved' AND p.is_featured = TRUE
                ORDER BY p.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Post::getFeatured: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách bài đăng của user
     */
    public function getByUserId($user_id, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name
                FROM {$this->table} p
                JOIN categories c ON p.category_id = c.id
                WHERE p.user_id = ? AND p.status = 'approved'
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$user_id, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Post::getByUserId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm bài đăng của user
     */
    public function countByUserId($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ? AND status = 'approved'");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error in Post::countByUserId: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đếm tổng bài đăng
     */
    public function countAll() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'approved'");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error in Post::countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tăng lượt xem
     */
    public function incrementView($id) {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET views = views + 1 WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in Post::incrementView: " . $e->getMessage());
            return false;
        }
    }
}
?>
