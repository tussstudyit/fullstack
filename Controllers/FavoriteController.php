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
    public function add() {
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

        $result = $this->favoriteModel->add($_SESSION['user_id'], $post_id);
        return $result;
    }

    /**
     * Xóa khỏi yêu thích
     */
    public function remove() {
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

        $result = $this->favoriteModel->remove($_SESSION['user_id'], $post_id);
        return $result;
    }

    /**
     * Xử lý hành động
     */
    public function handleAction($action) {
        switch ($action) {
            case 'add':
                return $this->add();
            case 'remove':
                return $this->remove();
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
