<?php
require_once __DIR__ . '/config.php';

// Simple test to verify image upload
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check $_FILES
    $response = [
        'files_received' => isset($_FILES),
        'files_keys' => array_keys($_FILES ?? []),
        'images_data' => [],
        'post_id' => $_POST['post_id'] ?? 'NOT SET',
        'logged_in' => isLoggedIn(),
        'user_role' => $_SESSION['role'] ?? 'NOT SET'
    ];
    
    if (isset($_FILES['images'])) {
        $response['images_count'] = count($_FILES['images']['name']);
        foreach ($_FILES['images']['name'] as $key => $name) {
            $response['images_data'][] = [
                'name' => $name,
                'size' => $_FILES['images']['size'][$key],
                'type' => $_FILES['images']['type'][$key],
                'error' => $_FILES['images']['error'][$key],
                'tmp_name' => $_FILES['images']['tmp_name'][$key]
            ];
        }
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    echo json_encode(['message' => 'Please POST']);
}
?>
