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
require_once __DIR__ . '/../Models/PostImage.php';

class PostController {
    private $postModel;
    private $userModel;
    private $postImageModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->userModel = new User();
        $this->postImageModel = new PostImage();
    }

    /**
     * Tạo bài đăng mới
     */
    public function create() { // Tạo bài đăng: kiểm tra quyền + validate + lưu
        if (!isLoggedIn()) { // Kiểm tra đăng nhập
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if (!isLandlord() && !isAdmin()) { // Chỉ chủ trọ + admin được đăng
            return ['success' => false, 'message' => 'Chỉ chủ trọ mới có thể đăng tin'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ POST request
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        // Validate input
        $title = sanitize($_POST['title'] ?? ''); // Tiêu đề (sanitize XSS)
        $description = sanitize($_POST['description'] ?? ''); // Mô tả
        $address = sanitize($_POST['address'] ?? ''); // Địa chỉ
        $district = sanitize($_POST['district'] ?? ''); // Quận/huyện
        $city = sanitize($_POST['city'] ?? ''); // Thành phố
        $price = $_POST['price'] ?? ''; // Giá tiền
        $category_id = $_POST['category_id'] ?? '1'; // Danh mục

        if (empty($title) || empty($description) || empty($address) || empty($price)) { // Kiểm tra bắt buộc
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
        }

        if (!is_numeric($price) || $price <= 0) { // Giá phải > 0
            return ['success' => false, 'message' => 'Giá không hợp lệ'];
        }

        $amenities = $_POST['amenities'] ?? []; // Tiện ích (tivi, wifi, ...)
        $utilities = $_POST['utilities'] ?? []; // Dịch vụ (nước, điện, ...)
        $rules = $_POST['rules'] ?? []; // Quy định (không khách, ...)

        $data = [
            'user_id' => $_SESSION['user_id'], // ID chủ trọ
            'category_id' => $category_id, // Danh mục
            'title' => $title,
            'description' => $description,
            'address' => $address,
            'district' => $district,
            'city' => $city,
            'price' => $price, // Giá phòng/tháng
            'area' => $_POST['area'] ?? null, // Diện tích m2
            'room_type' => $_POST['room_type'] ?? 'single', // Loại: single/shared/apartment/house
            'room_status' => $_POST['room_status'] ?? 'available', // Trạng thái: available/occupied/renting
            'max_people' => $_POST['max_people'] ?? 1, // Số người tối đa
            'gender' => $_POST['gender'] ?? 'any', // Yêu cầu giới tính: male/female/any
            'amenities' => !empty($amenities) ? json_encode($amenities) : null, // JSON array
            'utilities' => !empty($utilities) ? json_encode($utilities) : null, // JSON array
            'rules' => !empty($rules) ? json_encode($rules) : null, // JSON array
            'available_from' => $_POST['available_from'] ?? date('Y-m-d'), // Ngày sẵn sàng
            'deposit_amount' => $_POST['deposit_amount'] ?? null, // Tiền cọc
            'electric_price' => $_POST['electric_price'] ?? null, // Giá điện/kWh
            'water_price' => $_POST['water_price'] ?? null, // Giá nước/m3
            'status' => 'approved' // Trạng thái: approved/pending
        ];

        $result = $this->postModel->create($data); // Lưu vào DB
        return $result;
    }

    /**
     * Cập nhật bài đăng
     */
    public function update() { // Cập nhật bài viết: kiểm tra quyền + validate + cập nhật
        if (!isLoggedIn()) { // Kiểm tra đăng nhập
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ POST
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null; // Lấy post_id

        if (!$post_id) { // Kiểm tra post_id tồn tại
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $post = $this->postModel->findById($post_id); // Tìm bài viết

        if (!$post) { // Kiểm tra bài viết tồn tại
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        // Kiểm tra quyền: chỉ chủ bài hoặc admin được sửa
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['success' => false, 'message' => 'Bạn không có quyền cập nhật bài đăng này'];
        }

        $data = [];
        $allowedFields = ['title', 'description', 'address', 'district', 'city', 'price', 'area', 'room_type', 'room_status', 'max_people', 'gender', 'deposit_amount', 'electric_price', 'water_price'];

        foreach ($allowedFields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = sanitize($_POST[$field]);
            }
        }

        // Handle JSON arrays
        $amenities = $_POST['amenities'] ?? [];
        $utilities = $_POST['utilities'] ?? [];
        $rules = $_POST['rules'] ?? [];

        if (!empty($amenities)) {
            $data['amenities'] = json_encode($amenities);
        }
        if (!empty($utilities)) {
            $data['utilities'] = json_encode($utilities);
        }
        if (!empty($rules)) {
            $data['rules'] = json_encode($rules);
        }

        $result = $this->postModel->update($post_id, $data);
        
        // Handle deleted images
        $deletedIds = $_POST['deleted_image_ids'] ?? '';
        if (!empty($deletedIds)) {
            $ids = explode(',', $deletedIds);
            $uploadDir = __DIR__ . '/../uploads/';
            $primaryImageDeleted = false;
            
            foreach ($ids as $imageId) {
                $imageId = (int)trim($imageId);
                if ($imageId > 0) {
                    // Get image details
                    $image = $this->postImageModel->getImageById($imageId);
                    if ($image) {
                        // Check if this is primary image
                        if ($image['is_primary']) {
                            $primaryImageDeleted = true;
                            error_log("Primary image deleted: $imageId");
                        }
                        
                        // Delete file
                        $filePath = $uploadDir . $image['image_url'];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                            error_log("Deleted image file: $filePath");
                        }
                        // Delete from database
                        $this->postImageModel->deleteImage($imageId);
                        error_log("Deleted image record: $imageId");
                    }
                }
            }
            
            // If primary image was deleted, promote first remaining image to primary
            if ($primaryImageDeleted) {
                $remainingImages = $this->postImageModel->getImages($post_id);
                if (!empty($remainingImages)) {
                    // Set first remaining image as primary
                    $firstImageId = $remainingImages[0]['id'];
                    $this->postImageModel->setPrimaryImage($post_id, $firstImageId);
                    error_log("Promoted image $firstImageId to primary");
                }
            }
        }
        
        // Return post_id for image upload
        if ($result['success']) {
            $result['post_id'] = $post_id;
        }
        
        return $result;
    }

    /**
     * Xóa bài đăng
     */
    public function delete() { // Xóa bài viết: kiểm tra quyền + xóa file + DB
        if (!isLoggedIn()) { // Kiểm tra đăng nhập
            return ['success' => false, 'message' => 'Vui lòng đăng nhập'];
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ chấp nhận POST request
            return ['success' => false, 'message' => 'Invalid request method'];
        }

        $post_id = $_POST['post_id'] ?? null; // Lấy post_id từ request

        if (!$post_id) { // Kiểm tra post_id tồn tại
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        $post = $this->postModel->findById($post_id); // Tìm bài viết trong DB

        if (!$post) { // Kiểm tra bài viết có tồn tại
            return ['success' => false, 'message' => 'Bài đăng không tồn tại'];
        }

        // Kiểm tra quyền: chỉ chủ bài hoặc admin mới được xóa
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            return ['success' => false, 'message' => 'Bạn không có quyền xóa bài đăng này'];
        }

        // Delete images from filesystem first
        $images = $this->postImageModel->getImages($post_id); // Lấy danh sách ảnh
        $uploadDir = __DIR__ . '/../uploads/'; // Thư mục uploads
        
        foreach ($images as $image) { // Xóa từng file ảnh
            $filePath = $uploadDir . $image['image_url']; // Đường dẫn file
            if (file_exists($filePath)) { // Nếu file tồn tại
                @unlink($filePath); // Xóa file
                error_log("Deleted image file: " . $filePath);
            }
        }

        // Delete from database
        $result = $this->postModel->delete($post_id); // Xóa khỏi DB
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
