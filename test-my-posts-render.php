<?php
session_start();
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'landlord1';
$_SESSION['role'] = 'landlord';

ob_start();
require_once 'Views/user/my-posts.php';
$html = ob_get_clean();

// Extract just the post items
preg_match_all('/<div class="post-item".*?<\/div>\s*<\/div>\s*<\/div>/s', $html, $matches);

echo "=== MY-POSTS PAGE TEST ===\n\n";
echo "Number of posts found: " . count($matches[0]) . "\n\n";

if(count($matches[0]) > 0) {
    echo "First 500 chars of first post:\n";
    echo substr($matches[0][0], 0, 500) . "\n...\n\n";
} else {
    echo "No posts found in HTML!\n";
    echo "\nSearching for 'empty-state' div:\n";
    if(strpos($html, 'empty-state') !== false) {
        echo "Found 'Bạn chưa có tin đăng nào' message\n";
    }
    echo "\nSearching for 'foreach' in HTML:\n";
    if(strpos($html, 'foreach') !== false) {
        echo "Found foreach keyword (shouldn't be here)\n";
    }
}
?>
