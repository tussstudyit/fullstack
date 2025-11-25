<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controllers/ImageController.php';

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    die('Lỗi: Bạn phải đăng nhập trước');
}

// Kiểm tra có file không
if (empty($_FILES['images']['name'][0])) {
    die('Lỗi: Không có file nào được upload');
}

$postId = $_POST['post_id'] ?? 1;

try {
    $imageController = new ImageController();
    
    // Test 1: Kiểm tra quyền ghi folder
    $permCheck = $imageController->checkUploadPermissions();
    echo "=== KIỂM TRA QUYỀN FOLDER ===\n";
    echo json_encode($permCheck, JSON_PRETTY_PRINT) . "\n\n";
    
    if (!$permCheck['writable']) {
        die('LỖI: Folder uploads không có quyền ghi!');
    }
    
    // Test 2: Upload ảnh
    echo "=== ĐANG UPLOAD ===\n";
    $result = $imageController->uploadMultipleImages($postId, $_FILES['images']);
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 3: Kiểm tra file trong folder
    echo "=== KIỂM TRA FILE TRONG FOLDER ===\n";
    $uploadDir = $imageController->getUploadDir();
    
    if (is_dir($uploadDir)) {
        $files = scandir($uploadDir);
        $imageFiles = array_filter($files, function($f) {
            return $f !== '.' && $f !== '..' && $f !== '.gitignore';
        });
        
        echo "Đường dẫn folder: $uploadDir\n";
        echo "Số file ảnh: " . count($imageFiles) . "\n";
        echo "Danh sách file:\n";
        foreach ($imageFiles as $file) {
            $path = $uploadDir . $file;
            $size = filesize($path);
            echo "  - $file (Size: " . round($size/1024, 2) . " KB)\n";
        }
    } else {
        echo "LỖI: Folder không tồn tại!\n";
    }
    
} catch (Exception $e) {
    echo 'LỖI: ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
