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

/**
 * Generate URL-friendly slug from Vietnamese text
 * @param string $text Text to convert
 * @return string URL-friendly slug
 */
function generateSlug($text) {
    // Convert Vietnamese characters to ASCII
    $vietnamese = [
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ',
        'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
        'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
        'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
        'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
        'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
        'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
        'Đ'
    ];
    
    $ascii = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd',
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd'
    ];
    
    $text = str_replace($vietnamese, $ascii, $text);
    
    // Convert to lowercase
    $text = strtolower($text);
    
    // Replace non-alphanumeric characters with hyphens
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Remove leading/trailing hyphens
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Generate unique slug for a post
 * @param string $title Post title
 * @param int $postId Post ID (for updates)
 * @return string Unique slug
 */
function getUniqueSlug($title, $postId = null) {
    $conn = getDB();
    $baseSlug = generateSlug($title);
    $slug = $baseSlug;
    $counter = 1;
    
    while (true) {
        $query = "SELECT id FROM posts WHERE slug = ?";
        $params = [$slug];
        
        if ($postId !== null) {
            $query .= " AND id != ?";
            $params[] = $postId;
        }
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        if ($stmt->rowCount() === 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}
?>
