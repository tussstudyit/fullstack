<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controllers/ImageController.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in and is landlord
if (!isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$imageController = new ImageController();

if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID không hợp lệ']);
        exit;
    }

    if (!isset($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'Không có file được chọn']);
        exit;
    }

    $isPrimary = isset($_POST['is_primary']) && $_POST['is_primary'] === 'true';
    $result = $imageController->uploadImage($postId, $_FILES['image'], $isPrimary);
    
    echo json_encode($result);
    exit;
}

if ($action === 'upload-multiple' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID không hợp lệ']);
        exit;
    }

    if (!isset($_FILES['images'])) {
        echo json_encode(['success' => false, 'message' => 'Không có file được chọn']);
        exit;
    }

    $result = $imageController->uploadMultipleImages($postId, $_FILES['images']);
    echo json_encode($result);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = isset($_POST['image_id']) ? (int)$_POST['image_id'] : 0;
    
    if (!$imageId) {
        echo json_encode(['success' => false, 'message' => 'Image ID không hợp lệ']);
        exit;
    }

    $result = $imageController->deleteImage($imageId);
    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>

