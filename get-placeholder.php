<?php
// Generate dynamic placeholder images locally
header('Content-Type: image/png');

$width = $_GET['w'] ?? 300;
$height = $_GET['h'] ?? 200;
$bg_color = $_GET['bg'] ?? '667eea';
$text_color = $_GET['text_color'] ?? 'ffffff';
$text = $_GET['text'] ?? 'Image';

// Remove # if present
$bg_color = str_replace('#', '', $bg_color);
$text_color = str_replace('#', '', $text_color);

// Create image
$image = imagecreatetruecolor($width, $height);

// Convert hex to RGB
$bg_rgb = [
    hexdec(substr($bg_color, 0, 2)),
    hexdec(substr($bg_color, 2, 2)),
    hexdec(substr($bg_color, 4, 2))
];

$text_rgb = [
    hexdec(substr($text_color, 0, 2)),
    hexdec(substr($text_color, 2, 2)),
    hexdec(substr($text_color, 4, 2))
];

$bg = imagecolorallocate($image, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
$text_col = imagecolorallocate($image, $text_rgb[0], $text_rgb[1], $text_rgb[2]);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Add text
$font_size = min($width, $height) / 8;
$text_bbox = imagettfbbox($font_size, 0, __DIR__ . '/arial.ttf', $text);
$text_width = $text_bbox[2] - $text_bbox[0];
$text_height = $text_bbox[1] - $text_bbox[7];
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;

// Fallback to built-in font if TTF not available
if (!file_exists(__DIR__ . '/arial.ttf')) {
    $text_width = strlen($text) * 8;
    $x = ($width - $text_width) / 2;
    $y = ($height - 8) / 2;
    imagestring($image, 5, $x, $y, substr($text, 0, 20), $text_col);
} else {
    imagettftext($image, $font_size, 0, $x, $y, $text_col, __DIR__ . '/arial.ttf', $text);
}

imagepng($image);
imagedestroy($image);
?>
