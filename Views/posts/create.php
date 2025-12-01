<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Post.php';
require_once __DIR__ . '/../../Models/PostImage.php';

// Redirect if not logged in or not landlord
if (!isLoggedIn() || $_SESSION['role'] !== 'landlord') {
    redirect('../../index.php');
}

$postModel = new Post();
$postImageModel = new PostImage();
$postId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$editingPost = null;
$existingImages = [];
$amenitiesArray = [];
$utilitiesArray = [];
$rulesArray = [];

// Load existing post data for editing
if ($postId) {
    $editingPost = $postModel->findById($postId);
    // Verify ownership
    if (!$editingPost || $editingPost['user_id'] != $_SESSION['user_id']) {
        redirect('../../index.php');
    }
    $existingImages = $postImageModel->getImages($postId);
    
    // Parse JSON arrays
    if ($editingPost['amenities']) {
        $amenitiesArray = json_decode($editingPost['amenities'], true) ?: [];
    }
    if ($editingPost['utilities']) {
        $utilitiesArray = json_decode($editingPost['utilities'], true) ?: [];
    }
    if ($editingPost['rules']) {
        $rulesArray = json_decode($editingPost['rules'], true) ?: [];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editingPost ? 'Chỉnh sửa tin' : 'Đăng tin'; ?> - Tìm Trọ Sinh Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .content-wrapper {
            padding: 3rem 0;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border-color);
            z-index: -1;
        }

        .step:last-child::after {
            display: none;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--border-color);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .step.active .step-number {
            background: var(--primary-color);
        }

        .step.completed .step-number {
            background: var(--success-color);
        }

        .step-title {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .step.active .step-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .image-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-color);
        }

        .image-upload-area:hover {
            border-color: var(--primary-color);
            background: white;
        }

        .image-upload-area i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .preview-image-wrapper {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            aspect-ratio: 1;
        }

        .preview-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-preview-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .remove-preview-btn:hover {
            background: #dc2626;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .amenity-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .amenity-checkbox:hover {
            border-color: var(--primary-color);
            background: var(--light-color);
        }

        .amenity-checkbox input:checked + label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .form-row,
            .amenities-grid {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../../index.php" class="logo">
                <i class="fas fa-home"></i>
                <span>Tìm Trọ SV</span>
            </a>

            <ul class="nav-menu">
                <li><a href="../../index.php" class="nav-link">Trang chủ</a></li>
                <li><a href="list.php" class="nav-link">Danh sách trọ</a></li>
                <li><a href="create.php" class="nav-link active">Đăng tin</a></li>
                <li><a href="../chat/chat.php" class="nav-link">Tin nhắn</a></li>
            </ul>

            <div class="nav-actions">
                <div style="position: relative; display: inline-block;">
                    <a href="../user/notifications.php" class="btn btn-outline btn-sm" title="Thông báo">
                        <i class="fas fa-bell"></i> Thông báo
                    </a>
                    <?php 
                    require_once '../../Models/Notification.php';
                    $notifModel = new Notification();
                    $unread = $notifModel->getUnreadCount($_SESSION['user_id']);
                    if ($unread > 0): 
                    ?>
                    <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                        <?php echo $unread > 99 ? '99+' : $unread; ?>
                    </span>
                    <?php endif; ?>
                </div>
                <a href="../user/profile.php" class="btn btn-outline btn-sm"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="../../Controllers/AuthController.php?action=logout" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="page-header">
        <h1><?php echo $editingPost ? 'Chỉnh Sửa Tin Đăng' : 'Đăng Tin Cho Thuê Phòng Trọ'; ?></h1>
        <p><?php echo $editingPost ? 'Cập nhật thông tin phòng trọ của bạn' : 'Chia sẻ thông tin phòng trọ của bạn'; ?></p>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="form-container">
                <div class="step-indicator">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-title">Thông tin cơ bản</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-title">Chi tiết phòng</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-title">Chi phí tiện ích</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-title">Hình ảnh</div>
                    </div>
                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-title">Xác nhận</div>
                    </div>
                </div>

                <form id="createPostForm" action="../../Controllers/PostController.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $editingPost ? 'update' : 'create'; ?>">
                    <?php if ($editingPost): ?>
                    <input type="hidden" name="post_id" value="<?php echo $editingPost['id']; ?>">
                    <?php endif; ?>

                    <div class="form-step active" data-step="1">
                        <h2 style="margin-bottom: 2rem;">Thông tin cơ bản</h2>

                        <div class="form-group">
                            <label class="form-label" for="title">Tiêu đề *</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="title" 
                                name="title" 
                                placeholder="VD: Phòng trọ gần trường ĐH Bách Khoa"
                                value="<?php echo $editingPost ? htmlspecialchars($editingPost['title']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="category">Loại phòng *</label>
                            <select class="form-control" id="category" name="category_id" required>
                                <option value="">Chọn loại phòng</option>
                                <option value="1" <?php echo $editingPost && $editingPost['category_id'] == 1 ? 'selected' : ''; ?>>Phòng trọ sinh viên</option>
                                <option value="2" <?php echo $editingPost && $editingPost['category_id'] == 2 ? 'selected' : ''; ?>>Căn hộ mini</option>
                                <option value="3" <?php echo $editingPost && $editingPost['category_id'] == 3 ? 'selected' : ''; ?>>Nhà nguyên căn</option>
                                <option value="4" <?php echo $editingPost && $editingPost['category_id'] == 4 ? 'selected' : ''; ?>>Ở ghép</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Mô tả *</label>
                            <textarea 
                                class="form-control" 
                                id="description" 
                                name="description" 
                                rows="6"
                                placeholder="Mô tả chi tiết về phòng trọ: vị trí, tiện ích, nội thất..."
                                required
                            ><?php echo $editingPost ? htmlspecialchars($editingPost['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="price">Giá thuê (VNĐ/tháng) *</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="price" 
                                    name="price" 
                                    placeholder="2500000"
                                    min="0"
                                    value="<?php echo $editingPost ? $editingPost['price'] : ''; ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="area">Diện tích (m²) *</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="area" 
                                    name="area" 
                                    placeholder="20"
                                    min="0"
                                    step="0.1"
                                    value="<?php echo $editingPost ? $editingPost['area'] : ''; ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-step" data-step="2">
                        <h2 style="margin-bottom: 2rem;">Chi tiết phòng</h2>

                        <div class="form-group">
                            <label class="form-label" for="address">Địa chỉ chi tiết *</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="address" 
                                name="address" 
                                placeholder="Số nhà, tên đường"
                                value="<?php echo $editingPost ? htmlspecialchars($editingPost['address']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="district">Quận/Huyện *</label>
                                <select class="form-control" id="district" name="district" required>
                                    <option value="">Chọn quận/huyện</option>
                                    <option value="Quận Hải Châu" <?php echo $editingPost && $editingPost['district'] == 'Quận Hải Châu' ? 'selected' : ''; ?>>Quận Hải Châu</option>
                                    <option value="Quận Thanh Khê" <?php echo $editingPost && $editingPost['district'] == 'Quận Thanh Khê' ? 'selected' : ''; ?>>Quận Thanh Khê</option>
                                    <option value="Quận Cẩm Lệ" <?php echo $editingPost && $editingPost['district'] == 'Quận Cẩm Lệ' ? 'selected' : ''; ?>>Quận Cẩm Lệ</option>
                                    <option value="Quận Ngũ Hành Sơn" <?php echo $editingPost && $editingPost['district'] == 'Quận Ngũ Hành Sơn' ? 'selected' : ''; ?>>Quận Ngũ Hành Sơn</option>
                                    <option value="Quận Sơn Trà" <?php echo $editingPost && $editingPost['district'] == 'Quận Sơn Trà' ? 'selected' : ''; ?>>Quận Sơn Trà</option>
                                    <option value="Quận Liên Chiểu" <?php echo $editingPost && $editingPost['district'] == 'Quận Liên Chiểu' ? 'selected' : ''; ?>>Quận Liên Chiểu</option>
                                    <option value="Huyện Hòa Vang" <?php echo $editingPost && $editingPost['district'] == 'Huyện Hòa Vang' ? 'selected' : ''; ?>>Huyện Hòa Vang</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="city">Thành phố *</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="city" 
                                    name="city" 
                                    value="<?php echo $editingPost ? htmlspecialchars($editingPost['city']) : 'Đà Nẵng'; ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="room_type">Loại hình *</label>
                                <select class="form-control" id="room_type" name="room_type" required>
                                    <option value="single" <?php echo $editingPost && $editingPost['room_type'] == 'single' ? 'selected' : ''; ?>>Phòng đơn</option>
                                    <option value="shared" <?php echo $editingPost && $editingPost['room_type'] == 'shared' ? 'selected' : ''; ?>>Phòng ghép</option>
                                    <option value="apartment" <?php echo $editingPost && $editingPost['room_type'] == 'apartment' ? 'selected' : ''; ?>>Căn hộ</option>
                                    <option value="house" <?php echo $editingPost && $editingPost['room_type'] == 'house' ? 'selected' : ''; ?>>Nhà nguyên căn</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="max_people">Số người tối đa *</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="max_people" 
                                    name="max_people" 
                                    value="<?php echo $editingPost ? $editingPost['max_people'] : '1'; ?>"
                                    min="1"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Giới tính *</label>
                            <select class="form-control" name="gender" required>
                                <option value="any" <?php echo $editingPost && $editingPost['gender'] == 'any' ? 'selected' : ''; ?>>Nam/Nữ</option>
                                <option value="male" <?php echo $editingPost && $editingPost['gender'] == 'male' ? 'selected' : ''; ?>>Nam</option>
                                <option value="female" <?php echo $editingPost && $editingPost['gender'] == 'female' ? 'selected' : ''; ?>>Nữ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tiện ích</label>
                            <div class="amenities-grid">
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="wifi" <?php echo in_array('wifi', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-wifi"></i> WiFi</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="ac" <?php echo in_array('ac', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-snowflake"></i> Điều hòa</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="fridge" <?php echo in_array('fridge', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-lightbulb"></i> Tủ lạnh</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="washing" <?php echo in_array('washing', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-wind"></i> Máy giặt</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="parking" <?php echo in_array('parking', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-parking"></i> Chỗ để xe</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="security" <?php echo in_array('security', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-shield-alt"></i> An ninh 24/7</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="water_heater" <?php echo in_array('water_heater', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-tint"></i> Máy nóng lạnh</span>
                                </label>
                                <label class="amenity-checkbox">
                                    <input type="checkbox" name="amenities[]" value="flexible_hours" <?php echo in_array('flexible_hours', $amenitiesArray) ? 'checked' : ''; ?>>
                                    <span><i class="fas fa-clock"></i> Giờ giấc tự do</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-step" data-step="3">
                        <h2 style="margin-bottom: 2rem;">Chi phí tiện ích</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="deposit_amount">Tiền cọc (VNĐ)</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="deposit_amount" 
                                    name="deposit_amount" 
                                    placeholder="2500000"
                                    min="0"
                                    step="1000"
                                    value="<?php echo $editingPost && $editingPost['deposit_amount'] ? $editingPost['deposit_amount'] : ''; ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="electric_price">Giá điện (đ/kWh)</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="electric_price" 
                                    name="electric_price" 
                                    placeholder="3500"
                                    min="0"
                                    step="100"
                                    value="<?php echo $editingPost && $editingPost['electric_price'] ? $editingPost['electric_price'] : ''; ?>"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="water_price">Giá nước (đ/người/tháng)</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="water_price" 
                                name="water_price" 
                                placeholder="20000"
                                min="0"
                                step="1000"
                                value="<?php echo $editingPost && $editingPost['water_price'] ? $editingPost['water_price'] : ''; ?>"
                            >
                        </div>

                        <div style="background: var(--light-color); padding: 1.5rem; border-radius: var(--radius-md); margin-top: 2rem;">
                            <p style="margin: 0; color: var(--text-secondary);"><strong>Ghi chú:</strong> Nhập giá tiền cọc, điện, nước để hiển thị trên trang chi tiết. Để trống nếu không áp dụng.</p>
                        </div>
                    </div>

                    <div class="form-step" data-step="4">
                        <h2 style="margin-bottom: 2rem;">Hình ảnh phòng trọ</h2>

                        <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Tải lên hình ảnh</h3>
                            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Kéo thả hoặc click để chọn ảnh (Tối đa 10 ảnh)</p>
                            <input 
                                type="file" 
                                id="imageInput" 
                                multiple 
                                accept="image/*" 
                                style="display: none;"
                                onchange="handleImageSelect(this)"
                            >
                        </div>

                        <div id="imagePreview" class="image-preview-container"></div>
                        <input type="hidden" id="imageCount" value="0">
                    </div>

                    <div class="form-step" data-step="5">
                        <h2 style="margin-bottom: 2rem;">Xác nhận thông tin</h2>

                        <div style="background: var(--light-color); padding: 2rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                            <p style="margin-bottom: 1rem;"><strong>Vui lòng kiểm tra lại thông tin trước khi đăng:</strong></p>
                            <ul style="padding-left: 1.5rem; color: var(--text-secondary);">
                                <li>Thông tin chính xác và đầy đủ</li>
                                <li>Hình ảnh rõ ràng, chân thực</li>
                                <li>Giá cả hợp lý</li>
                                <li>Liên hệ chính xác</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label style="display: flex; align-items: start; gap: 0.75rem;">
                                <input type="checkbox" name="agree_terms" required style="margin-top: 0.25rem;">
                                <span>Tôi cam kết thông tin đăng tải là chính xác và tuân thủ <a href="#" class="text-primary">Quy định đăng tin</a> của website</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                            <i class="fas fa-chevron-left"></i> Quay lại
                        </button>
                        <div style="flex: 1;"></div>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Tiếp theo <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-check"></i> Đăng tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 5;

        function changeStep(direction) {
            const newStep = currentStep + direction;
            
            if (newStep < 1 || newStep > totalSteps) return;

            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelectorAll('.step')[currentStep - 1].classList.remove('active');
            if (direction > 0) {
                document.querySelectorAll('.step')[currentStep - 1].classList.add('completed');
            }

            currentStep = newStep;

            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
            document.querySelectorAll('.step')[currentStep - 1].classList.add('active');

            document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Handle image selection and preview
        let uploadedImages = [];

        function handleImageSelect(input) {
            const files = input.files;
            const preview = document.getElementById('imagePreview');
            
            if (files.length === 0) return;
            
            // Add new files to uploadedImages (don't clear old ones - allow multiple selections)
            for (let file of files) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('File ' + file.name + ' quá lớn (tối đa 5MB)', 'error');
                    continue;
                }
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showNotification('File ' + file.name + ' không phải là ảnh', 'error');
                    continue;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileIndex = uploadedImages.length; // Index của file này
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.id = 'preview-' + fileIndex;
                    div.innerHTML = `
                        <div style="position: relative; overflow: hidden; border-radius: 8px;">
                            <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 150px; object-fit: cover;">
                            <button type="button" class="remove-preview-btn" onclick="removeImagePreview(${fileIndex})" style="position: absolute; top: 0.5rem; right: 0.5rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <p style="margin-top: 0.5rem; font-size: 0.875rem; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</p>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
                
                // Add to uploadedImages array
                uploadedImages.push(file);
            }
            
            console.log('Total images selected:', uploadedImages.length);
            
            // Reset input so same file can be selected again
            input.value = '';
        }
        
        // Remove image from preview and uploadedImages
        function removeImagePreview(index) {
            const element = document.getElementById('preview-' + index);
            if (element) {
                element.remove();
            }
            
            // Mark as null instead of splicing to keep indices correct
            uploadedImages[index] = null;
            
            // Clean up null values
            uploadedImages = uploadedImages.filter(f => f !== null);
            
            console.log('Images after removal:', uploadedImages.length);
        }

        // Drag and drop support
        const uploadArea = document.getElementById('imageUploadArea');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.backgroundColor = '';
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.backgroundColor = '';
                const input = document.getElementById('imageInput');
                input.files = e.dataTransfer.files;
                handleImageSelect(input);
            });
        }

        // Upload images after post creation
        function uploadPostImages(postId) {
            if (uploadedImages.length === 0) {
                return Promise.resolve({ success: true, message: 'Không có ảnh để upload' });
            }

            const formData = new FormData();
            formData.append('post_id', postId);
            
            for (let i = 0; i < uploadedImages.length; i++) {
                formData.append('images', uploadedImages[i]);
            }

            return fetch('../../api/upload-image.php?action=upload-multiple', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON: ' + text.substring(0, 200));
                    });
                }
            })
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    showNotification(data.message || 'Upload thành công', 'success');
                } else {
                    showNotification('Lỗi upload ảnh: ' + (data.message || 'Unknown error'), 'error');
                }
                return data;
            })
            .catch(error => {
                console.error('Error uploading images:', error);
                showNotification('Lỗi khi upload ảnh: ' + error.message, 'error');
            });
        }

        // Update form submission to handle image upload
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm('createPostForm')) {
                showNotification('Đang xử lý...', 'info');
                
                const formData = new FormData(this);
                
                fetch('../../Controllers/PostController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    return response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Response text:', text);
                            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Bài đăng được tạo, đang upload ảnh...', 'info');
                        console.log('Post created with ID:', data.post_id);
                        console.log('Uploaded images count:', uploadedImages.length);
                        // Upload images if there are any
                        if (uploadedImages.length > 0) {
                            console.log('Uploading images...');
                            return uploadPostImages(data.post_id).then(() => data);
                        }
                        return data;
                    } else {
                        showNotification(data.message || 'Có lỗi xảy ra', 'error');
                        throw new Error(data.message);
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        showNotification('Đăng tin thành công!', 'success');
                        setTimeout(() => {
                            window.location.href = '../user/my-posts.php';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.message !== 'Có lỗi xảy ra') {
                        showNotification('Lỗi khi gửi dữ liệu: ' + error.message, 'error');
                    }
                });
            }
        });
    </script>
</body>
</html>
