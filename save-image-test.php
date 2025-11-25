<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controllers/ImageController.php';

header('Content-Type: application/json');

// Kiểm tra xem có file được upload không
if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    echo json_encode([
        'success' => false,
        'message' => 'Không có file nào được upload',
        'received_files' => $_FILES
    ]);
    exit;
}

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn phải đăng nhập trước'
    ]);
    exit;
}

$postId = $_POST['post_id'] ?? 1;

try {
    $imageController = new ImageController();
    
    // Upload ảnh
    $result = $imageController->uploadMultipleImages($postId, $_FILES['images']);
    
    // Kiểm tra folder uploads
    $uploadDir = $imageController->getUploadDir();
    $files = scandir($uploadDir);
    
    echo json_encode([
        'success' => $result['success'],
        'message' => $result['message'],
        'uploaded' => $result['uploaded'],
        'upload_dir' => $uploadDir,
        'files_in_dir' => $files,
        'files_count' => count($files) - 2 // trừ . và ..
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' + $e->getMessage()
    ]);
}
?>
