<?php
require_once __DIR__ . '/../Models/PostImage.php';

class ImageController {
    private $postImageModel;
    private $uploadDir = __DIR__ . '/../uploads/';
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct() {
        $this->postImageModel = new PostImage();
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload image for a post
     * @param int $postId Post ID
     * @param array $file $_FILES array
     * @param bool $isPrimary Whether this is the primary image
     * @return array ['success' => bool, 'message' => string, 'imageUrl' => string]
     */
    public function uploadImage($postId, $file, $isPrimary = false) {
        error_log("uploadImage: postId=$postId, isPrimary=" . ($isPrimary ? 'true' : 'false'));
        
        // Validate file exists
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            error_log("uploadImage: No tmp_name found");
            return ['success' => false, 'message' => 'Không có file được chọn'];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            error_log("uploadImage: File too large");
            return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
        }

        // Get file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            error_log("uploadImage: Invalid extension: " . $ext);
            return ['success' => false, 'message' => 'Định dạng file không được phép. Chỉ chấp nhận: ' . implode(', ', $this->allowedExtensions)];
        }

        // Validate MIME type from $_FILES['type']
        $mimeType = $file['type'] ?? '';
        if (!in_array($mimeType, $this->allowedMimes) && !empty($mimeType)) {
            // If MIME type is provided, validate it; if empty, we only rely on extension check
            error_log("uploadImage: Invalid MIME type: " . $mimeType);
            return ['success' => false, 'message' => 'File không phải là ảnh hợp lệ'];
        }

        // Generate unique filename
        $filename = 'post_' . $postId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = $this->uploadDir . $filename;
        
        error_log("uploadImage: Generated filename: " . $filename);

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            error_log("uploadImage: Failed to move file");
            return ['success' => false, 'message' => 'Lỗi khi upload file'];
        }
        
        error_log("uploadImage: File moved successfully");

        // Save to database
        try {
            // If this is primary, unset other primary images first
            if ($isPrimary) {
                error_log("uploadImage: Setting primary - clearing existing primary images");
                $this->postImageModel->setPrimaryImage($postId, null);
            }

            // Add image to database
            $imageId = $this->postImageModel->addImage($postId, $filename, $isPrimary);
            
            error_log("uploadImage: Image saved to DB with ID: " . $imageId);

            return [
                'success' => true,
                'message' => 'Upload thành công',
                'imageUrl' => 'uploads/' . $filename,
                'imageId' => $imageId,
                'filename' => $filename,
                'isPrimary' => $isPrimary
            ];
        } catch (Exception $e) {
            // Delete the uploaded file if database insert fails
            error_log("uploadImage: Database error: " . $e->getMessage());
            @unlink($uploadPath);
            return ['success' => false, 'message' => 'Lỗi khi lưu vào database: ' . $e->getMessage()];
        }
    }

    /**
     * Upload multiple images for a post
     * @param int $postId Post ID
     * @param array $files $_FILES array
     * @return array ['success' => bool, 'message' => string, 'uploaded' => array]
     */
    public function uploadMultipleImages($postId, $files) {
        $uploaded = [];
        $errors = [];

        // Handle both single and multiple file uploads
        $fileArray = [];
        if (isset($files['tmp_name'])) {
            if (is_array($files['tmp_name'])) {
                // Multiple files
                for ($i = 0; $i < count($files['tmp_name']); $i++) {
                    $fileArray[] = [
                        'name' => $files['name'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'size' => $files['size'][$i],
                        'type' => $files['type'][$i],
                        'error' => $files['error'][$i],
                        'order' => $i  // Track upload order
                    ];
                }
            } else {
                // Single file
                $fileArray[] = array_merge($files, ['order' => 0]);
            }
        }

        error_log("=== uploadMultipleImages START ===");
        error_log("postId: " . $postId);
        error_log("Total files to upload: " . count($fileArray));

        // Check if post already has images
        $existingImages = $this->postImageModel->getImages($postId);
        $shouldSetPrimary = empty($existingImages);
        
        error_log("Post already has " . count($existingImages) . " images");
        error_log("Will set primary on first image: " . ($shouldSetPrimary ? 'YES' : 'NO'));

        $imageIndex = 0;
        foreach ($fileArray as $file) {
            error_log("Processing image " . ($imageIndex + 1) . " of " . count($fileArray));
            error_log("  - Filename: " . $file['name']);
            error_log("  - Order: " . $file['order']);
            
            // Set ONLY first uploaded image as primary
            $isPrimary = ($imageIndex === 0) && $shouldSetPrimary;
            
            error_log("  - Setting as primary: " . ($isPrimary ? 'YES' : 'NO'));
            
            $result = $this->uploadImage($postId, $file, $isPrimary);

            if ($result['success']) {
                error_log("  - Upload SUCCESS, filename: " . $result['filename']);
                $result['order'] = $imageIndex;  // Add order to response
                $uploaded[] = $result;
            } else {
                error_log("  - Upload FAILED: " . $result['message']);
                $errors[] = $result['message'];
            }
            
            $imageIndex++;
        }

        error_log("Total uploaded: " . count($uploaded) . ", Total errors: " . count($errors));
        error_log("=== uploadMultipleImages END ===");

        $hasErrors = !empty($errors);
        $hasSuccess = !empty($uploaded);

        if ($hasSuccess && !$hasErrors) {
            return ['success' => true, 'message' => 'Tất cả ' . count($uploaded) . ' ảnh upload thành công (ảnh đầu tiên là ảnh bìa)', 'uploaded' => $uploaded];
        } elseif ($hasSuccess && $hasErrors) {
            return ['success' => true, 'message' => 'Upload thành công ' . count($uploaded) . ' ảnh, lỗi: ' . implode('; ', $errors), 'uploaded' => $uploaded];
        } else {
            return ['success' => false, 'message' => implode('; ', $errors), 'uploaded' => []];
        }
    }

    /**
     * Delete image
     * @param int $imageId Image ID
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteImage($imageId) {
        try {
            // Get image data
            $image = $this->postImageModel->getImageById($imageId);
            if (!$image) {
                return ['success' => false, 'message' => 'Ảnh không tồn tại'];
            }

            // Delete file
            $filepath = $this->uploadDir . $image['image_url'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            // Delete from database
            $this->postImageModel->deleteImage($imageId);

            return ['success' => true, 'message' => 'Xóa ảnh thành công'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    /**
     * Get uploads directory
     */
    public function getUploadDir() {
        return $this->uploadDir;
    }

    /**
     * Validate upload directory permissions
     */
    public function checkUploadPermissions() {
        if (!is_dir($this->uploadDir)) {
            return ['writable' => false, 'message' => 'Thư mục uploads không tồn tại'];
        }

        if (!is_writable($this->uploadDir)) {
            return ['writable' => false, 'message' => 'Không có quyền ghi vào thư mục uploads'];
        }

        return ['writable' => true, 'message' => 'OK'];
    }
}
?>
