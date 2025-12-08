<?php
/**
 * Generate local placeholder image URL
 * @param int $width Image width
 * @param int $height Image height
 * @param string $bg Background color (hex without #)
 * @param string $text Text to display
 * @param string $textColor Text color (hex without #)
 * @return string Image URL
 */
function getPlaceholderImage($width = 300, $height = 200, $bg = '667eea', $text = 'Image', $textColor = 'ffffff') {
    // Get base path dynamically
    $basePath = getBasePath();
    if (!$basePath) {
        $basePath = ''; // Fallback - project is at root
    }
    
    $params = http_build_query([
        'w' => $width,
        'h' => $height,
        'bg' => $bg,
        'text' => $text,
        'text_color' => $textColor
    ]);
    return $basePath . '/get-placeholder.php?' . $params;
}
?>
