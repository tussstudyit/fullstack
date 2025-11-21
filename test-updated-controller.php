<?php
// Test the updated toggleFavorite action
session_start();

// Simulate a logged-in tenant
$_SESSION['user_id'] = 4;
$_SESSION['username'] = 'tenant1';
$_SESSION['role'] = 'tenant';

// Test both actions
echo "<h2>Testing Updated FavoriteController</h2>\n";

// Test 1: Add action
echo "<h3>Test 1: Testing 'add' action</h3>";
$_POST['action'] = 'add';
$_POST['post_id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'POST';

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Favorite.php';
require_once __DIR__ . '/Controllers/FavoriteController.php';

// The controller code should execute above
?>
