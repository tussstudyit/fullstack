<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Notification.php';

echo "=== TEST NOTIFICATION SYSTEM ===\n\n";

try {
    // Test 1: Get database connection
    $conn = getDB();
    echo "✓ Database connection successful\n\n";

    // Test 2: Check if notifications table exists
    $result = $conn->query("SHOW TABLES LIKE 'notifications'")->fetch();
    if ($result) {
        echo "✓ Notifications table exists\n";
        
        // Check table structure
        $columns = $conn->query("DESCRIBE notifications")->fetchAll(PDO::FETCH_ASSOC);
        echo "\nTable columns:\n";
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
    } else {
        echo "✗ Notifications table NOT found\n";
    }
    echo "\n";

    // Test 3: Check if post_likes table exists
    $result = $conn->query("SHOW TABLES LIKE 'post_likes'")->fetch();
    if ($result) {
        echo "✓ Post_likes table exists\n";
    } else {
        echo "✗ Post_likes table NOT found\n";
    }
    echo "\n";

    // Test 4: Create sample notification
    $notificationModel = new Notification();
    $testData = [
        'user_id' => 1,
        'type' => 'comment',
        'title' => 'Test Notification',
        'content' => 'This is a test notification',
        'link' => '/test'
    ];
    
    $created = $notificationModel->create($testData);
    if ($created) {
        echo "✓ Successfully created test notification\n";
        
        // Verify it was inserted
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([1]);
        $notification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($notification) {
            echo "\nNotification details:\n";
            echo "  - ID: {$notification['id']}\n";
            echo "  - User ID: {$notification['user_id']}\n";
            echo "  - Type: {$notification['type']}\n";
            echo "  - Title: {$notification['title']}\n";
            echo "  - Is Read: {$notification['is_read']}\n";
            echo "  - Created At: {$notification['created_at']}\n";
        }
    } else {
        echo "✗ Failed to create notification\n";
    }
    echo "\n";

    // Test 5: Get notifications for user
    $userNotifications = $notificationModel->getNotifications(1, 10, 0);
    echo "✓ Retrieved notifications for user 1: " . count($userNotifications) . " notifications\n";
    
    if (!empty($userNotifications)) {
        echo "\nUser 1's notifications:\n";
        foreach ($userNotifications as $notif) {
            echo "  - [{$notif['type']}] {$notif['title']} (Read: {$notif['is_read']})\n";
        }
    }
    echo "\n";

    // Test 6: Get unread count
    $unreadCount = $notificationModel->getUnreadCount(1);
    echo "✓ Unread notifications for user 1: {$unreadCount}\n\n";

    // Test 7: Check comments table
    $result = $conn->query("SHOW TABLES LIKE 'comments'")->fetch();
    if ($result) {
        echo "✓ Comments table exists\n";
        $comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch(PDO::FETCH_ASSOC);
        echo "  Total comments: {$comments['count']}\n";
    } else {
        echo "✗ Comments table NOT found\n";
    }
    echo "\n";

    echo "=== TEST COMPLETE ===\n";

} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString();
}
?>
