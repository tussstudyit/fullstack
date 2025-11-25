<?php
require_once __DIR__ . '/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

$response = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'files_received' => !empty($_FILES),
    'files_count' => count($_FILES),
    'files_keys' => array_keys($_FILES ?? []),
    'post_id' => $_POST['post_id'] ?? 'NOT SET',
    'images_array' => [],
    'logged_in' => isLoggedIn(),
    'user_role' => $_SESSION['role'] ?? 'NOT SET',
    'php_errors' => []
];

if (!empty($_FILES)) {
    foreach ($_FILES as $key => $files) {
        $response['images_array'][] = [
            'key' => $key,
            'count' => is_array($files['name']) ? count($files['name']) : 1,
            'details' => is_array($files['name']) ? $files : [$files]
        ];
    }
}

// Capture PHP errors if any
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    global $response;
    $response['php_errors'][] = "$errstr in $errfile:$errline";
});

echo json_encode($response, JSON_PRETTY_PRINT);
?>

