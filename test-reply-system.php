<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Comment.php';

echo "<h2>Testing Reply System</h2>\n";

// Simulate logged in user
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'landlord';

$commentModel = new Comment();

// Get comments with replies
$comments = $commentModel->getByPost(1, 10, 0);

echo "<h3>Comments with Replies:</h3>";
echo "<pre>";
foreach ($comments as $comment) {
    echo "=== Comment ID: {$comment['id']}\n";
    echo "    User: {$comment['username']} (Role: {$comment['role']})\n";
    echo "    Content: " . substr($comment['content'], 0, 50) . "...\n";
    echo "    Replies: " . count($comment['replies']) . "\n";
    
    if (!empty($comment['replies'])) {
        foreach ($comment['replies'] as $reply) {
            echo "    └─ Reply ID: {$reply['id']}\n";
            echo "       User: {$reply['username']} (Role: {$reply['role']})\n";
            echo "       Parent ID: {$reply['parent_id']}\n";
            echo "       Content: " . substr($reply['content'], 0, 50) . "...\n";
        }
    }
    echo "\n";
}
echo "</pre>";

// Show SQL queries for debugging
echo "<h3>Testing getReplies for specific comment:</h3>";
echo "<pre>";
$post = $commentModel->getByPost(1, 1, 0);
if (!empty($post[0])) {
    $replies = $commentModel->getReplies($post[0]['id']);
    echo "Parent Comment ID: {$post[0]['id']}\n";
    echo "Replies found: " . count($replies) . "\n\n";
    foreach ($replies as $reply) {
        echo "Reply ID: {$reply['id']}\n";
        echo "Parent ID: " . ($reply['parent_id'] ?? 'NULL') . "\n";
        echo "User: {$reply['username']} (Role: {$reply['role']})\n";
        echo "User Vote: " . ($reply['user_vote'] ?? 'NULL') . "\n";
        echo "---\n";
    }
}
echo "</pre>";
?>
