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
    max_people INT DEFAULT 1,
    gender ENUM('male', 'female', 'any') DEFAULT 'any',
    amenities TEXT,
    utilities TEXT,
    rules TEXT,
    available_from DATE,
    status ENUM('pending', 'approved', 'rejected', 'rented') DEFAULT 'pending',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_status (status),
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
    type ENUM('message', 'review', 'post_approved', 'post_rejected', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    link VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
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
INSERT INTO posts (user_id, category_id, title, description, address, district, city, price, area, room_type, max_people, gender, amenities, utilities, status) VALUES
(2, 1, 'Phòng trọ gần ĐH Bách Khoa', 'Phòng trọ sạch sẽ, thoáng mát, an ninh tốt', '123 Nguyễn Chí Thanh', 'Quận Hải Châu', 'Đà Nẵng', 2500000, 20, 'single', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh', 'Điện, nước, internet', 'approved'),
(2, 2, 'Căn hộ mini cao cấp Quận 1', 'Căn hộ đầy đủ nội thất, view đẹp', '456 Tran Phu', 'Quận Thanh Khê', 'Đà Nẵng', 8000000, 35, 'apartment', 2, 'any', 'WiFi, Điều hòa, Tủ lạnh, Máy giặt, Bếp', 'Điện, nước, internet, dọn dẹp', 'approved'),
(3, 1, 'Phòng trọ sinh viên giá rẻ', 'Phòng mới xây, gần chợ, trường học', '789 Lê Văn Việt', 'Quận Cẩm Lệ', 'Đà Nẵng', 1800000, 18, 'single', 1, 'female', 'WiFi, Điều hòa', 'Điện, nước', 'approved');

-- Insert sample images
INSERT INTO post_images (post_id, image_url, is_primary, display_order) VALUES
(1, 'room1-1.jpg', TRUE, 1),
(1, 'room1-2.jpg', FALSE, 2),
(2, 'room2-1.jpg', TRUE, 1),
(3, 'room3-1.jpg', TRUE, 1);

-- =============================================
-- END OF DATABASE SCHEMA
-- =============================================
