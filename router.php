<?php
/**
 * Router for PHP built-in server
 * Enables pretty URLs like /phong-tro-gan-fpt instead of /Views/posts/detail.php?slug=phong-tro-gan-fpt
 */

$requested_file = $_SERVER["PHP_SELF"];
$requested_uri = $_SERVER["REQUEST_URI"];

// Remove query string
$path = parse_url($requested_uri, PHP_URL_PATH);

// List of directories/files that should be served directly
$directories = ['/Views/', '/api/', '/assets/', '/uploads/', '/vendor/', '/Controllers/', '/Models/'];
$extensions = ['.php', '.html', '.css', '.js', '.jpg', '.jpeg', '.png', '.gif', '.ico', '.svg', '.woff', '.woff2', '.ttf', '.eot'];

// Check if it's a real file or directory
if (file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
    return false;
}

// Check if it's a real directory
if (file_exists(__DIR__ . $path) && is_dir(__DIR__ . $path)) {
    return false;
}

// Check if path contains one of the listed directories
foreach ($directories as $dir) {
    if (strpos($path, $dir) === 0) {
        return false;
    }
}

// Check if path has one of the listed extensions
foreach ($extensions as $ext) {
    if (substr($path, -strlen($ext)) === $ext) {
        return false;
    }
}

// Match pretty URL pattern: /slug or /category/slug
// Pattern: /lowercase-letters-numbers-hyphens
if (preg_match('/^\/([a-z0-9\-]+)$/', $path, $matches)) {
    $slug = $matches[1];
    
    // Rewrite to detail.php?slug=...
    $_GET['slug'] = $slug;
    $_SERVER['QUERY_STRING'] = 'slug=' . $slug;
    
    // Include the detail page
    include __DIR__ . '/Views/posts/detail.php';
    exit;
}

// For all other requests, serve the requested file
// If file doesn't exist, return 404
http_response_code(404);
echo "404 - File not found: " . htmlspecialchars($path);
exit;
?>
