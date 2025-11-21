<?php
// Set output buffering before any output
if (!ob_get_level()) {
    ob_start();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
ini_set('display_errors', '0');
error_reporting(0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Post.php';
require_once __DIR__ . '/../Models/User.php';

class PostController {
    private $postModel;
    private $userModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->userModel = new User();
    }

    /**
     * Tạo bài đăng mới
     */
    public function create() {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if (!isLandlord() && !isAdmin()) {
            return ['success' => false, 'message' => 'Chỉ chủ trọ mới có thể đăng tin'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        // Validate input
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $district = sanitize($_POST['district'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $price = $_POST['price'] ?? '';
        $category_id = $_POST['category_id'] ?? '1';

        if (empty($title) || empty($description) || empty($address) || empty($price)) {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
        }

        if (!is_numeric($price) || $price <= 0) {
            return ['success' => false, 'message' => 'Giá không hợp lệ'];
        }

        $amenities = $_POST['amenities'] ?? [];
        $utilities = $_POST['utilities'] ?? [];
        $rules = $_POST['rules'] ?? [];

        $data = [
            'user_id' => $_SESSION['user_id'],
            'category_id' => $category_id,
            'title' => $title,
            'description' => $description,
            'address' => $address,
            'district' => $district,
            'city' => $city,
            'price' => $price,
            'area' => $_POST['area'] ?? null,
            'room_type' => $_POST['room_type'] ?? 'single',
            'max_people' => $_POST['max_people'] ?? 1,
            'gender' => $_POST['gender'] ?? 'any',
            'amenities' => !empty($amenities) ? json_encode($amenities) : null,
            'utilities' => !empty($utilities) ? json_encode($utilities) : null,
            'rules' => !empty($rules) ? json_encode($rules) : null,
            'available_from' => $_POST['available_from'] ?? date('Y-m-d'),
            'status' => 'approved'
        ];

        $result = $this->postModel->create($data);
        return $result;
    }

    /**
     * Cập nhật bài đăng
     */
    public function update() {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null;

        if (!$post_id) {
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $post = $this->postModel->findById($post_id);

        if (!$post) {
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        // Kiểm tra quyền
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['success' => false, 'message' => 'Bạn không có quyền cập nhật bài đăng này'];
        }

        $data = [];
        $allowedFields = ['title', 'description', 'address', 'district', 'city', 'price', 'area', 'room_type', 'max_people', 'gender', 'amenities', 'utilities', 'rules'];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = sanitize($_POST[$field]);
            }
        }

        $result = $this->postModel->update($post_id, $data);
        return $result;
    }

    /**
     * Xóa bài đăng
     */
    public function delete() {
        if (!isLoggedIn()) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null;

        if (!$post_id) {
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $post = $this->postModel->findById($post_id);

        if (!$post) {
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        // Kiểm tra quyền
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['success' => false, 'message' => 'Bạn không có quyền xóa bài đăng này'];
        }

        $result = $this->postModel->delete($post_id);
        return $result;
    }

    /**
     * Xử lý hành động
     */
    public function handleAction($action) {
        switch ($action) {
            case 'create':
                return $this->create();
            case 'update':
                return $this->update();
            case 'delete':
                return $this->delete();
            default:
                return ['success' => false, 'message' => 'Action not found'];
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = sanitize($_POST['action'] ?? '');
        $controller = new PostController();
        $result = $controller->handleAction($action);
        
        if (is_array($result)) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid response']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Flush output buffer
    if (ob_get_level()) {
        ob_end_flush();
    }
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
