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
        // Validate file exists
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Không có file được chọn'];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
        }

        // Get file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return ['success' => false, 'message' => 'Định dạng file không được phép. Chỉ chấp nhận: ' . implode(', ', $this->allowedExtensions)];
        }

        // Validate MIME type from $_FILES['type']
        $mimeType = $file['type'] ?? '';
        if (!in_array($mimeType, $this->allowedMimes) && !empty($mimeType)) {
            // If MIME type is provided, validate it; if empty, we only rely on extension check
            return ['success' => false, 'message' => 'File không phải là ảnh hợp lệ'];
        }

        // Generate unique filename
        $filename = 'post_' . $postId . '_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = $this->uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'message' => 'Lỗi khi upload file'];
        }

        // Save to database
        try {
            // If this is primary, unset other primary images first
            if ($isPrimary) {
                $this->postImageModel->setPrimaryImage($postId, null);
            }

            // Add image to database
            $imageId = $this->postImageModel->addImage($postId, $filename, $isPrimary);

            return [
                'success' => true,
                'message' => 'Upload thành công',
                'imageUrl' => 'uploads/' . $filename,
                'imageId' => $imageId,
                'filename' => $filename
            ];
        } catch (Exception $e) {
            // Delete the uploaded file if database insert fails
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
                        'error' => $files['error'][$i]
                    ];
                }
            } else {
                // Single file
                $fileArray[] = $files;
            }
        }

        $primarySet = false;
        foreach ($fileArray as $file) {
            // Set first image as primary
            $isPrimary = !$primarySet;
            $result = $this->uploadImage($postId, $file, $isPrimary);

            if ($result['success']) {
                $uploaded[] = $result;
                if ($isPrimary) {
                    $primarySet = true;
                }
            } else {
                $errors[] = $result['message'];
            }
        }

        $hasErrors = !empty($errors);
        $hasSuccess = !empty($uploaded);

        if ($hasSuccess && !$hasErrors) {
            return ['success' => true, 'message' => 'Tất cả ảnh upload thành công', 'uploaded' => $uploaded];
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
