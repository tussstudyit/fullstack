<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../config.php';

try {
    $db = getDB();
    $unread_stmt = $db->prepare("
        SELECT COUNT(DISTINCT c.id) as unread_conversations
        FROM conversations c
        INNER JOIN messages m ON m.conversation_id = c.id
        WHERE m.is_read = 0 
        AND m.sender_id != ?
        AND (c.landlord_id = ? OR c.tenant_id = ?)
    ");
    
    $unread_stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $unread_result = $unread_stmt->fetch(PDO::FETCH_ASSOC);
    $unread_count = $unread_result['unread_conversations'] ?? 0;
    
    echo json_encode(['count' => $unread_count]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
