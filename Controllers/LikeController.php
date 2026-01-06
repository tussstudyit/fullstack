<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Post.php';

class LikeController {
    private $db;
    private $postModel;

    public function __construct() {
        $this->db = getDB();
        $this->postModel = new Post();
    }

    /**
     * Toggle like on a post
     * POST /Controllers/LikeController.php?action=toggleLike
     */
    public function toggleLike() { // Like/Unlike: toggle like + notify + cập nhật count
        // Check if user is logged in
        if (!isLoggedIn()) { // Chỉ user đăng nhập mới like
            return $this->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0; // Lấy post_id
        $user_id = $_SESSION['user_id']; // User hiện tại

        if (!$post_id) { // Kiểm tra post_id tồn tại
            return $this->json(['success' => false, 'message' => 'Post ID is required']);
        }

        // Verify post exists
        $post = $this->postModel->findById($post_id); // Kiểm tra bài viết tồn tại
        if (!$post) {
            return $this->json(['success' => false, 'message' => 'Post not found']);
        }

        try {
            // Check if user already liked this post
            $stmt = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?"); // Kiểm tra đã like chưa
            $stmt->execute([$post_id, $user_id]);
            $existing_like = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_like) { // Nếu đã like: unlike (xóa)
                // Unlike
                $stmt = $this->db->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
                $stmt->execute([$post_id, $user_id]);
                $action = 'unliked';
            } else { // Nếu chưa like: like (insert)
                // Like
                $stmt = $this->db->prepare("INSERT INTO post_likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$post_id, $user_id]);
                $action = 'liked';
                
                // Send notification to post author
                require_once __DIR__ . '/../Models/Notification.php';
                $notificationModel = new Notification();
                $notificationModel->notifyLike($post_id, $user_id, $_SESSION['username'], $post['title']); // Thông báo cho chủ bài
            }

            // Get updated likes count
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?"); // Đếm lại like
            $stmt->execute([$post_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $likes_count = $result['count'] ?? 0; // Số like mới

            return $this->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $likes_count,
                'message' => $action === 'liked' ? 'Post liked successfully' : 'Like removed successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error toggling like: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Lấy só like của bài viết
     */
    public function getLikesCount() {
        $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

        if (!$post_id) {
            return $this->json(['success' => false, 'message' => 'Post ID is required']);
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?");
            $stmt->execute([$post_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $likes_count = $result['count'] ?? 0;

            return $this->json([
                'success' => true,
                'likes_count' => $likes_count
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Kiểm tra người dùng đã like bài viết chưa
     */
    public function checkLike() {
        if (!isLoggedIn()) {
            return $this->json(['success' => false, 'liked' => false]);
        }

        $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
        $user_id = $_SESSION['user_id'];

        if (!$post_id) {
            return $this->json(['success' => false, 'message' => 'Post ID is required']);
        }

        try {
            $stmt = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$post_id, $user_id]);
            $liked = $stmt->rowCount() > 0;

            return $this->json([
                'success' => true,
                'liked' => $liked
            ]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Handle request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = sanitize($_POST['action'] ?? $_GET['action'] ?? 'toggleLike');
    $controller = new LikeController();
    
    switch ($action) {
        case 'toggleLike':
            $controller->toggleLike();
            break;
        case 'getLikesCount':
            $controller->getLikesCount();
            break;
        case 'checkLike':
            $controller->checkLike();
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
