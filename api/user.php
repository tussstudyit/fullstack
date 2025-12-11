<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    $conn = getDB();

    switch ($action) {
        case 'getUserInfo':
            getUserInfo($conn);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Get user information by user_id
 */
function getUserInfo($conn) {
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }

    $user_id = (int)$_GET['user_id'];

    $stmt = $conn->prepare("
        SELECT 
            id,
            username,
            email,
            phone,
            full_name,
            avatar,
            role,
            created_at
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        return;
    }

    // Don't expose sensitive data
    unset($user['password']);

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
}
?>
