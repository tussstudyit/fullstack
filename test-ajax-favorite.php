<?php
// Simulate a logged-in session and POST request
session_start();

// Set up the session as if user is logged in
$_SESSION['user_id'] = 4;
$_SESSION['username'] = 'tenant1';
$_SESSION['role'] = 'tenant';

// Set up POST request
$_POST['action'] = 'add';
$_POST['post_id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the controller
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Models/Favorite.php';
require_once __DIR__ . '/Controllers/FavoriteController.php';
?>
