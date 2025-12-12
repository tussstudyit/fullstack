<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Notification.php';

header('Content-Type: application/json');

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Parse JSON body for POST requests
$json_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    if (!empty($raw_input)) {
        $json_data = json_decode($raw_input, true) ?? [];
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

try {
    $conn = getDB();

    switch ($action) {
        case 'getConversations':
            getConversations($conn, $user_id);
            break;
        case 'getMessages':
            getMessages($conn);
            break;
        case 'searchMessages':
            searchMessages($conn);
            break;
        case 'getMediaGallery':
            getMediaGallery($conn);
            break;
        case 'saveMessage':
            saveMessage($conn, $user_id);
            break;
        case 'markAsRead':
            markAsRead($conn, $user_id);
            break;
        case 'createOrGetConversation':
            createOrGetConversation($conn, $user_id);
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
 * Get all conversations for current user
 */
function getConversations($conn, $user_id) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                c.id,
                c.post_id,
                c.landlord_id,
                c.tenant_id,
                c.last_message,
                c.last_message_at,
                p.title as post_title,
                p.price,
                CASE 
                    WHEN c.landlord_id = ? THEN tenant.username
                    ELSE landlord.username
                END as other_user_name,
                CASE 
                    WHEN c.landlord_id = ? THEN tenant.id
                    ELSE landlord.id
                END as other_user_id,
                CASE 
                    WHEN c.landlord_id = ? THEN tenant.avatar
                    ELSE landlord.avatar
                END as other_user_avatar,
                (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND is_read = 0 AND sender_id != ?) as unread_count
            FROM conversations c
            JOIN posts p ON c.post_id = p.id
            JOIN users landlord ON c.landlord_id = landlord.id
            JOIN users tenant ON c.tenant_id = tenant.id
            WHERE c.landlord_id = ? OR c.tenant_id = ?
            ORDER BY c.last_message_at DESC
        ");
        
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $conversations
        ]);
    } catch (PDOException $e) {
        error_log("getConversations error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get messages for a conversation
 */
function getMessages($conn) {
    if (!isset($_GET['conversation_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
        return;
    }

    $conversation_id = (int)$_GET['conversation_id'];
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $messages = [];
    
    // Try to select with image column first
    try {
        $stmt = $conn->prepare("
            SELECT 
                m.id,
                m.conversation_id,
                m.sender_id,
                m.message as text,
                m.image as image_filename,
                m.is_read,
                m.created_at,
                u.username,
                u.avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = ?
            ORDER BY m.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$conversation_id, $limit, $offset]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback query without image column (column doesn't exist)
        try {
            $stmt = $conn->prepare("
                SELECT 
                    m.id,
                    m.conversation_id,
                    m.sender_id,
                    m.message as text,
                    NULL as image_filename,
                    m.is_read,
                    m.created_at,
                    u.username,
                    u.avatar
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.conversation_id = ?
                ORDER BY m.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute([$conversation_id, $limit, $offset]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            return;
        }
    }

    // Reverse to show oldest first
    $messages = array_reverse($messages);

    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);
}

/**
 * Search messages in a conversation
 */
function searchMessages($conn) {
    if (!isset($_GET['conversation_id']) || !isset($_GET['query'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Conversation ID and query required']);
        return;
    }

    $conversation_id = (int)$_GET['conversation_id'];
    $query = '%' . $_GET['query'] . '%';

    try {
        $stmt = $conn->prepare("
            SELECT 
                m.id,
                m.conversation_id,
                m.sender_id,
                m.message as text,
                m.image as image_filename,
                m.is_read,
                m.created_at,
                u.username,
                u.avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = ? AND m.message LIKE ?
            ORDER BY m.created_at DESC
        ");
        
        $stmt->execute([$conversation_id, $query]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $messages
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Get media gallery (images) from a conversation
 */
function getMediaGallery($conn) {
    if (!isset($_GET['conversation_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
        return;
    }

    $conversation_id = (int)$_GET['conversation_id'];

    try {
        $stmt = $conn->prepare("
            SELECT 
                m.id,
                m.conversation_id,
                m.sender_id,
                m.image as image_filename,
                m.created_at,
                u.username,
                u.avatar
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = ? AND m.image IS NOT NULL AND m.image != ''
            ORDER BY m.created_at DESC
        ");
        
        $stmt->execute([$conversation_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add full path to images
        $images = array_map(function($img) {
            if ($img['image_filename']) {
                $img['image_path'] = 'uploads/messages/' . $img['image_filename'];
            }
            return $img;
        }, $images);

        echo json_encode([
            'success' => true,
            'data' => $images
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Save a new message
 */
function saveMessage($conn, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['conversation_id']) || (!isset($data['message']) && !isset($data['image']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $conversation_id = intval($data['conversation_id']);
    $message = isset($data['message']) ? trim($data['message']) : '';
    $image = isset($data['image']) ? $data['image'] : null;

    if (empty($message) && empty($image)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Message and image cannot both be empty']);
        return;
    }

    // Verify user is part of this conversation
    $stmt = $conn->prepare("
        SELECT landlord_id, tenant_id FROM conversations WHERE id = ?
    ");
    $stmt->execute([$conversation_id]);
    $conv = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conv || ($conv['landlord_id'] != $user_id && $conv['tenant_id'] != $user_id)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    // Save message - try with image column first, fallback if it doesn't exist
    try {
        $stmt = $conn->prepare("
            INSERT INTO messages (conversation_id, sender_id, message, image, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$conversation_id, $user_id, $message, $image]);
    } catch (Exception $e) {
        // Fallback: insert without image column (for backward compatibility)
        $stmt = $conn->prepare("
            INSERT INTO messages (conversation_id, sender_id, message, is_read, created_at)
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$conversation_id, $user_id, $message]);
    }
    
    $message_id = $conn->lastInsertId();

    // Update conversation last_message
    $lastMessageText = !empty($message) ? $message : '[Ảnh]';
    $stmt = $conn->prepare("
        UPDATE conversations 
        SET last_message = ?, last_message_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$lastMessageText, $conversation_id]);

    // Get recipient user_id
    $recipient_id = ($conv['landlord_id'] == $user_id) ? $conv['tenant_id'] : $conv['landlord_id'];

    // Get user info for notification
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create notification for recipient
    $notif = new Notification();
    $notif->notifyMessage($recipient_id, $sender['username'], $message, $conversation_id);

    echo json_encode([
        'success' => true,
        'message_id' => $message_id,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Mark messages as read
 */
function markAsRead($conn, $user_id) {
    global $json_data;
    
    $conversation_id = $json_data['conversation_id'] ?? $_POST['conversation_id'] ?? null;
    
    if (!$conversation_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Conversation ID required']);
        return;
    }

    $conversation_id = (int)$conversation_id;

    $stmt = $conn->prepare("
        UPDATE messages 
        SET is_read = 1
        WHERE conversation_id = ? AND sender_id != ? AND is_read = 0
    ");
    $stmt->execute([$conversation_id, $user_id]);

    echo json_encode(['success' => true, 'rows_updated' => $stmt->rowCount()]);
}

/**
 * Create or get existing conversation
 */
function createOrGetConversation($conn, $user_id) {
    global $json_data;
    
    try {
        $post_id = $json_data['post_id'] ?? null;
        $other_user_id = $json_data['other_user_id'] ?? null;
        
        error_log("createOrGetConversation called with post_id=$post_id, other_user_id=$other_user_id, user_id=$user_id");
        
        if (!$post_id || !$other_user_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $post_id = (int)$post_id;
        $other_user_id = (int)$other_user_id;

        // Get post to determine landlord/tenant
        $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            return;
        }

        $landlord_id = $post['user_id'];
        $tenant_id = ($user_id == $landlord_id) ? $other_user_id : $user_id;

        // Check if conversation exists
        $stmt = $conn->prepare("
            SELECT id FROM conversations 
            WHERE post_id = ? AND landlord_id = ? AND tenant_id = ?
        ");
        $stmt->execute([$post_id, $landlord_id, $tenant_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            echo json_encode([
                'success' => true,
                'conversation_id' => $existing['id'],
                'created' => false
            ]);
            return;
        }

        // Create new conversation
        $stmt = $conn->prepare("
            INSERT INTO conversations (post_id, landlord_id, tenant_id, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$post_id, $landlord_id, $tenant_id]);
        $conversation_id = $conn->lastInsertId();

        echo json_encode([
            'success' => true,
            'conversation_id' => $conversation_id,
            'created' => true
        ]);
    } catch (Exception $e) {
        error_log("createOrGetConversation error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
}
?>
