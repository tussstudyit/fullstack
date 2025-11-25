<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controllers/ImageController.php';

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start debug info
$debugInfo = [];

// Check if user is logged in and is landlord
if (!isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    $debugInfo['auth_check'] = 'Failed - User not logged in or not landlord';
    $debugInfo['is_logged_in'] = isLoggedIn();
    $debugInfo['user_role'] = $_SESSION['role'] ?? 'NOT SET';
    
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'debug' => $debugInfo]);
    exit;
}

$action = $_GET['action'] ?? '';
$imageController = new ImageController();

// Check upload permissions
$permissionCheck = $imageController->checkUploadPermissions();
$debugInfo['upload_permissions'] = $permissionCheck;

if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID không hợp lệ', 'debug' => $debugInfo]);
        exit;
    }

    if (!isset($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'Không có file được chọn', 'debug' => $debugInfo]);
        exit;
    }

    $isPrimary = isset($_POST['is_primary']) && $_POST['is_primary'] === 'true';
    $result = $imageController->uploadImage($postId, $_FILES['image'], $isPrimary);
    
    echo json_encode(array_merge($result, ['debug' => $debugInfo]));
    exit;
}

if ($action === 'upload-multiple' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $debugInfo['action'] = 'upload-multiple';
    $debugInfo['post_data'] = $_POST;
    $debugInfo['files_keys'] = array_keys($_FILES ?? []);
    
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $debugInfo['post_id'] = $postId;
    
    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID không hợp lệ', 'debug' => $debugInfo]);
        exit;
    }

    if (!isset($_FILES['images'])) {
        echo json_encode(['success' => false, 'message' => 'Không có file được chọn', 'debug' => $debugInfo]);
        exit;
    }

    $debugInfo['files_images'] = $_FILES['images'];
    
    $result = $imageController->uploadMultipleImages($postId, $_FILES['images']);
    echo json_encode(array_merge($result, ['debug' => $debugInfo]));
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action', 'debug' => $debugInfo]);
?>

