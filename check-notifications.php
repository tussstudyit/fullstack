<?php
require 'config.php';
$db = getDB();
echo "=== NOTIFICATIONS CHECK ===\n";
$stmt = $db->query('SELECT id, user_id, type, title, content FROM notifications ORDER BY created_at DESC LIMIT 15');
$notifications = $stmt->fetchAll();
if(empty($notifications)) {
    echo "No notifications found\n";
} else {
    foreach($notifications as $row) {
        echo "ID: " . $row['id'] . " | User: " . $row['user_id'] . " | Type: " . $row['type'] . " | Title: " . $row['title'] . " | Content: " . $row['content'] . "\n";
    }
}
?>
