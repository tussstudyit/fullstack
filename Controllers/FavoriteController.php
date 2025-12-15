<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Favorite.php';

class FavoriteController {
    private $favoriteModel;

    public function __construct() {
        $this->favoriteModel = new Favorite();
    }

    /**
     * Thêm vào yêu thích
     */
    public function add() { // Thêm yêu thích: kiểm tra đăng nhập + gọi model add
        if (!isLoggedIn()) { // Chỉ user đăng nhập mới yêu thích
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ POST
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null; // Lấy post_id

        if (!$post_id) { // Kiểm tra post_id tồn tại
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $result = $this->favoriteModel->add($_SESSION['user_id'], $post_id); // Thêm vào DB
        return $result;
    }

    /**
     * Xóa khỏi yêu thích
     */
    public function remove() { // Xóa yêu thích: kiểm tra đăng nhập + gọi model remove
        if (!isLoggedIn()) { // Chỉ user đăng nhập
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ POST
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null; // Lấy post_id

        if (!$post_id) { // Kiểm tra post_id
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $result = $this->favoriteModel->remove($_SESSION['user_id'], $post_id); // Xóa khỏi DB
        return $result;
    }

    /**
     * Xử lý hành động
     */
    public function handleAction($action) { // Route: add/remove
        switch ($action) {
            case 'add':
                return $this->add(); // Thêm yêu thích
            case 'remove':
                return $this->remove(); // Xóa yêu thích
            default:
                return ['success' => false, 'message' => 'Action not found'];
        }
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');
    $controller = new FavoriteController();
    $result = $controller->handleAction($action);
    
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
