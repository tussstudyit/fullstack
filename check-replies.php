<?php
require_once __DIR__ . '/config.php';

$conn = getDB();
$stmt = $conn->query("SELECT id, post_id, user_id, parent_id, content, created_at FROM comments ORDER BY id DESC LIMIT 20");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
echo "=== CHECKING COMMENTS IN DATABASE ===\n";
echo "Total comments: " . count($comments) . "\n\n";

foreach ($comments as $comment) {
    echo "ID: {$comment['id']} | Post: {$comment['post_id']} | User: {$comment['user_id']} | Parent: " . ($comment['parent_id'] ?? 'NULL') . " | Content: " . substr($comment['content'], 0, 40) . "...\n";
}

echo "\n=== CHECKING PARENT/CHILD RELATIONSHIP ===\n";
$stmt = $conn->query("SELECT c1.id, c1.content as parent_content, c2.id as reply_id, c2.content as reply_content FROM comments c1 LEFT JOIN comments c2 ON c2.parent_id = c1.id WHERE c1.parent_id IS NULL");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
    if ($result['reply_id']) {
        echo "Parent Comment ID {$result['id']}: " . substr($result['parent_content'], 0, 30) . "...\n";
        echo "  └─ Reply ID {$result['reply_id']}: " . substr($result['reply_content'], 0, 30) . "...\n";
    } else {
        echo "Comment ID {$result['id']}: " . substr($result['parent_content'], 0, 30) . "... (No replies)\n";
    }
}

echo "</pre>";
?>
