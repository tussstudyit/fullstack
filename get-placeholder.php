<?php
/**
 * Generate placeholder image dynamically
 */

// Get parameters
$width = isset($_GET['w']) ? (int)$_GET['w'] : 300;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 200;
$bg = isset($_GET['bg']) ? preg_replace('/[^0-9a-fA-F]/', '', $_GET['bg']) : '667eea';
$text = isset($_GET['text']) ? substr(urldecode($_GET['text']), 0, 50) : 'Image';
$textColor = isset($_GET['text_color']) ? preg_replace('/[^0-9a-fA-F]/', '', $_GET['text_color']) : 'ffffff';

// Create image
$image = imagecreatetruecolor($width, $height);

// Parse hex colors
$bgRGB = hex2rgb($bg);
$textRGB = hex2rgb($textColor);

// Create colors
$bgColor = imagecolorallocate($image, $bgRGB[0], $bgRGB[1], $bgRGB[2]);
$textColorObj = imagecolorallocate($image, $textRGB[0], $textRGB[1], $textRGB[2]);

// Fill background
imagefill($image, 0, 0, $bgColor);

// Add text in center
$fontSize = 5;
$fontWidth = imagefontwidth($fontSize);
$fontHeight = imagefontheight($fontSize);

$textWidth = $fontWidth * strlen($text);
$textX = ($width - $textWidth) / 2;
$textY = ($height - $fontHeight) / 2;

imagestring($image, $fontSize, $textX, $textY, $text, $textColorObj);

// Output image
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
imagepng($image);
imagedestroy($image);

/**
 * Convert hex color to RGB array
 */
function hex2rgb($hex) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
    ];
}
?>
