<?php
// Debug test to check what's being received
session_start();
$_SESSION['user_id'] = 4;
$_SESSION['username'] = 'tenant1';
$_SESSION['role'] = 'tenant';

// Simulate AJAX request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['action'] = 'add';
$_POST['post_id'] = 1;

// Log what we're receiving
$action = trim($_POST['action'] ?? '');
echo "Action received: '" . $action . "'\n";
echo "Action length: " . strlen($action) . "\n";
echo "Action type: " . gettype($action) . "\n";
echo "Action empty?: " . (empty($action) ? 'YES' : 'NO') . "\n";
echo "Post ID: " . ($_POST['post_id'] ?? 'NOT SET') . "\n";
echo "Full POST: ";
var_dump($_POST);
?>
