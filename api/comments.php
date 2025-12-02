<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controllers/CommentController.php';

header('Content-Type: application/json');

try {
    $controller = new CommentController(getDB());
    $action = $_GET['action'] ?? 'getComments';

    switch ($action) {
        case 'getComments':
            echo $controller->getComments();
            break;
        case 'addComment':
            echo $controller->addComment();
            break;
        case 'addReply':
            echo $controller->addReply();
            break;
        case 'deleteComment':
            echo $controller->deleteComment();
            break;
        case 'voteComment':
            echo $controller->voteComment();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
