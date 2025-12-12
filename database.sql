-- =============================================
-- DATABASE SCHEMA FOR WEB TÌM TRỌ CHO SINH VIÊN
-- =============================================

DROP DATABASE IF EXISTS fullstack;
CREATE DATABASE fullstack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fullstack;

-- Bảng users: Quản lý người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    bio TEXT,
    role ENUM('admin', 'landlord', 'tenant') NOT NULL DEFAULT 'tenant',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng categories: Danh mục loại trọ
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng posts: Bài đăng cho thuê trọ
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    address VARCHAR(255) NOT NULL,
    district VARCHAR(100),
    city VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    area DECIMAL(10, 2),
    room_type ENUM('single', 'shared', 'apartment', 'house') DEFAULT 'single',
    room_status ENUM('available', 'unavailable') DEFAULT 'available',
    max_people INT DEFAULT 1,
    gender ENUM('male', 'female', 'any') DEFAULT 'any',
    amenities TEXT,
    utilities TEXT,
    rules TEXT,
    available_from DATE,
    deposit_amount DECIMAL(10, 2),
    electric_price DECIMAL(10, 2),
    water_price DECIMAL(10, 2),
    status ENUM('pending', 'approved', 'rejected', 'rented') DEFAULT 'pending',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_room_status (room_status),
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_city_district (city, district),
    INDEX idx_price (price),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng post_images: Hình ảnh của bài đăng
CREATE TABLE post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng favorites: Danh sách yêu thích
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, post_id),
    INDEX idx_user_id (user_id),
    INDEX idx_post_id (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng reviews: Đánh giá phòng trọ
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (post_id, user_id),
    INDEX idx_post_id (post_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng post_likes: Lượt thích bài viết
CREATE TABLE post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_user (post_id, user_id),
    INDEX idx_post_id (post_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng conversations: Cuộc hội thoại chat
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    landlord_id INT NOT NULL,
    tenant_id INT NOT NULL,
    last_message TEXT,
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_conversation (post_id, landlord_id, tenant_id),
    INDEX idx_landlord_id (landlord_id),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_last_message_at (last_message_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng messages: Tin nhắn chat
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    image VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_sender_id (sender_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng notifications: Thông báo
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('message', 'review', 'post_approved', 'post_rejected', 'system', 'comment', 'reply', 'rating', 'post_like') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    link VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Bảng comments: Bình luận/nhận xét bài đăng
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT,
    content TEXT NOT NULL,
    rating INT DEFAULT 0 CHECK (rating BETWEEN 0 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_user_id (user_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng comment_votes: Upvote/Downvote bình luận
CREATE TABLE comment_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    vote INT NOT NULL CHECK (vote IN (1, -1)),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (comment_id, user_id),
    INDEX idx_comment_id (comment_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- INSERT SAMPLE DATA
-- =============================================

-- Insert categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Phòng trọ sinh viên', 'phong-tro-sinh-vien', 'Phòng trọ giá rẻ dành cho sinh viên', 'fa-home'),
('Căn hộ mini', 'can-ho-mini', 'Căn hộ mini đầy đủ tiện nghi', 'fa-building'),
('Nhà nguyên căn', 'nha-nguyen-can', 'Nhà nguyên căn cho thuê', 'fa-house'),
('Ở ghép', 'o-ghep', 'Phòng ở ghép tiết kiệm', 'fa-users');

-- Insert admin user (password: 123456)
INSERT INTO users (username, email, password, full_name, phone, role) VALUES
('admin', 'admin@timtro.com', '$2y$12$CF5hr5WW2u1zroqvMKIgieujT0ExvKVg1o1jYVuD6vQ1glFwmo6/y', 'Quản trị viên', '0901234567', 'admin');

-- Insert sample landlords (password: 123456)
INSERT INTO users (username, email, password, full_name, phone, role) VALUES
('landlord1', 'landlord1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0912345678', 'landlord'),
('landlord2', 'landlord2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0923456789', 'landlord');

-- Insert sample tenants (password: 123456)
INSERT INTO users (username, email, password, full_name, phone, role) VALUES
('tenant1', 'tenant1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0934567890', 'tenant'),
('tenant2', 'tenant2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', '0945678901', 'tenant');

-- Insert sample posts
INSERT INTO posts (user_id, category_id, title, description, address, district, city, price, area, room_type, room_status, max_people, gender, amenities, utilities, deposit_amount, electric_price, water_price, status) VALUES
(2, 1, 'Phòng trọ gần ĐH Bách Khoa', 'Phòng trọ sạch sẽ, thoáng mát, an ninh tốt', '123 Nguyễn Chí Thanh', 'Quận Hải Châu', 'TP. Đà Nẵng', 2500000, 20, 'single', 'available', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh', 'Điện, nước, internet', 2500000, 3500, 20000, 'approved'),
(2, 2, 'Căn hộ mini cao cấp Quận Thanh Khê', 'Căn hộ đầy đủ nội thất, view đẹp', '456 Trần Phú', 'Quận Thanh Khê', 'TP. Đà Nẵng', 8000000, 35, 'apartment', 'available', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh, Máy giặt, Bếp', 'Điện, nước, internet, dọn dẹp', 8000000, 3500, 25000, 'approved'),
(3, 1, 'Phòng trọ sinh viên giá rẻ', 'Phòng mới xây, gần chợ, trường học', '789 Lê Văn Việt', 'Quận Sơn Trà', 'TP. Đà Nẵng', 1800000, 18, 'single', 'available', 1, 'female', 'WiFi, Điều hòa', 'Điện, nước', 1800000, 3500, 15000, 'approved'),
(2, 1, 'Phòng trọ tại Huyện Hòa Vang', 'Phòng trọ yên tĩnh, gần công viên', '321 Nguyễn Văn Linh', 'Huyện Hòa Vang', 'TP. Đà Nẵng', 1500000, 15, 'single', 'available', 1, 'male', 'WiFi', 'Điện, nước', 1500000, 3500, 15000, 'approved'),
(3, 2, 'Căn hộ mini tại Quận Ngũ Hành Sơn', 'Căn hộ gần biển, tầm nhìn đẹp', '654 Hùng Vương', 'Quận Ngũ Hành Sơn', 'TP. Đà Nẵng', 6500000, 30, 'apartment', 'available', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh, Máy giặt', 'Điện, nước, internet', 6500000, 3500, 25000, 'approved'),
(2, 1, 'Phòng ghép với người bạn', 'Phòng ghép, giá rẻ, thoáng mát', '147 Thái Phiên', 'Quận Liên Chiểu', 'TP. Đà Nẵng', 1200000, 12, 'shared', 'available', 2, 'any', 'WiFi, Quạt', 'Điện, nước', 1200000, 3500, 15000, 'approved'),
(3, 3, 'Nhà nguyên căn tại Hải Châu', 'Nhà 2 tầng, có sân, gần trường', '369 Lý Thái Tổ', 'Quận Hải Châu', 'TP. Đà Nẵng', 12000000, 80, 'house', 'available', 6, 'any', 'WiFi, Điều hòa, Bếp, TV', 'Điện, nước, gas, internet', 12000000, 3500, 25000, 'approved'),
(2, 2, 'Studio cao cấp tại Thanh Khê', 'Studio 1 phòng ngủ + 1 phòng khách', '258 Ngô Quyền', 'Quận Thanh Khê', 'TP. Đà Nẵng', 5000000, 25, 'apartment', 'available', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh, Bếp', 'Điện, nước, internet', 5000000, 3500, 20000, 'approved'),
(3, 1, 'Phòng trọ gần chợ Hàn', 'Phòng trọ an toàn, có khóa riêng', '111 Nguyễn Hữu Thọ', 'Quận Sơn Trà', 'TP. Đà Nẵng', 2000000, 16, 'single', 'available', 1, 'female', 'WiFi, Quạt, Tủ lạnh', 'Điện, nước', 2000000, 3500, 15000, 'approved'),
(2, 2, 'Căn hộ tại Ngũ Hành Sơn', 'Căn hộ view biển, gần chợ', '777 Bạch Đằng', 'Quận Ngũ Hành Sơn', 'TP. Đà Nẵng', 7000000, 32, 'apartment', 'available', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh, Máy giặt', 'Điện, nước, internet', 7000000, 3500, 25000, 'approved');


-- Insert sample images
INSERT INTO post_images (post_id, image_url, is_primary, display_order) VALUES
(1, 'room1-1.jpg', TRUE, 1),
(1, 'room1-2.jpg', FALSE, 2),
(2, 'room2-1.jpg', TRUE, 1),
(3, 'room3-1.jpg', TRUE, 1),
(4, 'room4-1.jpg', TRUE, 1),
(5, 'room5-1.jpg', TRUE, 1),
(6, 'room6-1.jpg', TRUE, 1),
(7, 'room7-1.jpg', TRUE, 1),
(8, 'room8-1.jpg', TRUE, 1),
(9, 'room9-1.jpg', TRUE, 1),
(10, 'room10-1.jpg', TRUE, 1);

-- =============================================
-- END OF DATABASE SCHEMA
-- =============================================
