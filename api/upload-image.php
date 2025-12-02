<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controllers/ImageController.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in and is landlord
if (!isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

error_log("=== upload-image.php START ===");
error_log("Action: " . ($_GET['action'] ?? 'none'));
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . json_encode($_POST));
error_log("FILES keys: " . implode(', ', array_keys($_FILES)));

if (isset($_FILES['images'])) {
    error_log("_FILES['images'] structure:");
    error_log("  - tmp_name is array: " . (is_array($_FILES['images']['tmp_name']) ? 'YES' : 'NO'));
    error_log("  - tmp_name count: " . (is_array($_FILES['images']['tmp_name']) ? count($_FILES['images']['tmp_name']) : 'N/A'));
    error_log("  - Full _FILES['images']: " . json_encode($_FILES['images']));
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
        error_log("ERROR: _FILES['images'] NOT SET!");
        echo json_encode(['success' => false, 'message' => 'Không có file được chọn']);
        exit;
    }

    error_log("Calling uploadMultipleImages with postId=$postId");
    $result = $imageController->uploadMultipleImages($postId, $_FILES['images']);
    error_log("uploadMultipleImages result: " . json_encode($result));
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

error_log("=== upload-image.php END - Invalid action ===");
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>

