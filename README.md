# 🏠 WEB TÌM TRỌ CHO SINH VIÊN

Hệ thống tìm kiếm và quản lý phòng trọ online dành cho sinh viên với các tính năng:
- 🔐 Xác thực & Phân quyền (Admin, Chủ trọ, Người thuê)
- 📝 Quản lý bài đăng cho thuê với hình ảnh đa phương tiện
- ⭐ Bình luận lồng nhau & Đánh giá sao
- 💬 Chat realtime với WebSocket
- ❤️ Danh sách yêu thích
- 🔔 Hệ thống thông báo

---

## � Yêu cầu hệ thống

| Thành phần | Phiên bản |
|-----------|---------|
| **PHP** | 7.4+ (khuyến nghị 8.0+) |
| **MySQL** | 5.7+ hoặc MariaDB 10.2+ |
| **Composer** | 2.0+ |
| **Node.js** (tuỳ chọn) | 14+ |
| **XAMPP/WAMP/MAMP** | Bất kỳ phiên bản gần đây |

---

## 🚀 Hướng dẫn cài đặt

### **Bước 1: Clone hoặc tải project**

```bash
# Hoặc tải file ZIP, sau đó extract vào thư mục
cd d:\baitapcuoiky\fullstack
```

### **Bước 2: Cài đặt Dependencies (Composer)**

```bash
cd d:\baitapcuoiky\fullstack
composer install
```

**Nếu chưa cài Composer:**
- Tải từ: https://getcomposer.org/download/

### **Bước 3: Tạo Database**

**Cách A: Sử dụng MySQL Command Line**

```bash
# Mở Terminal/CMD
mysql -u root -p
mysql> source database.sql

# Hoặc (nếu MySQL password rỗng)
mysql -u root fullstack < database.sql
```

**Cách B: Sử dụng phpMyAdmin (Giao diện)**
1. Mở http://localhost/phpmyadmin
2. Tạo database `fullstack`
3. Import file `database.sql` vào database

### **Bước 4: Kiểm tra Config Database**

Mở file [config.php](config.php) và xác nhận:

```php
define('DB_HOST', 'localhost');   // ✓ MySQL host
define('DB_NAME', 'fullstack');   // ✓ Database name
define('DB_USER', 'root');        // Sửa nếu username khác
define('DB_PASS', '');            // Nhập password nếu có
```

---

## ▶️ Cách chạy project

### **Cách 1: Chạy file start-chat.bat (⭐ Dễ nhất)**

**Windows:**
```bash
D:\baitapcuoiky\fullstack\start-chat.bat
```

File này sẽ tự động:
- ✅ Khởi động PHP Server trên `http://localhost:3000`
- ✅ Khởi động WebSocket Server trên `ws://localhost:8080`

### **Cách 2: Chạy thủ công (2 Terminal)**

**Terminal 1 - Web Server:**
```bash
cd d:\baitapcuoiky\fullstack
php -S localhost:3000
```

**Terminal 2 - WebSocket Server:**
```bash
cd d:\baitapcuoiky\fullstack
php websocket/server.php
```

### **Cách 3: Sử dụng XAMPP Apache** (Tuỳ chọn)

1. Copy toàn bộ folder `fullstack` vào `C:\xampp\htdocs\`
2. Mở XAMPP Control Panel
3. Click `Start` cho **Apache** và **MySQL**
4. Truy cập: http://localhost/fullstack/

---

## 📍 Truy cập ứng dụng

Sau khi chạy, mở trình duyệt:

```
URL: http://localhost:3000
```

### 🔓 Tài khoản TEST

| Vai trò | Username | Password | Chức năng |
|--------|----------|----------|----------|
| Người Thuê | `tenant1` | `123456` | Tìm kiếm, yêu thích, bình luận, chat |
| Chủ Trọ | `landlord1` | `123456` | Đăng bài, quản lý bài đăng, chat |
| Admin | `admin` | `123456` | Quản lý hệ thống, dashboard |

---

## ⚙️ Ports sử dụng

| Port | Dịch vụ |
|------|--------|
| **3000** | Web Server (PHP) |
| **3306** | MySQL Database |
| **8080** | WebSocket Server (Chat Realtime) |
| **80** | Apache (nếu dùng XAMPP) |

---

## 🔧 Xử lý sự cố

### ❌ Lỗi: `'php' is not recognized`

**Nguyên nhân:** PHP chưa được thêm vào PATH

**Cách fix:**
```bash
# Thêm PHP vào PATH hệ thống
setx PATH "%PATH%;C:\xampp\php"

# Sau đó đóng Terminal cũ, mở Terminal mới
php --version
```

### ❌ Lỗi: `Database connection failed`

**Kiểm tra:**
1. MySQL server đang chạy?
   ```bash
   mysql -u root -p
   ```
2. Database `fullstack` tồn tại?
   ```bash
   mysql> SHOW DATABASES;
   ```
3. Config.php có đúng không?
   ```bash
   # Chạy script kiểm tra
   php check-db-sync.php
   ```

### ❌ Lỗi: `WebSocket connection failed`

1. Kiểm tra port 8080 không bị chiếm:
   ```bash
   netstat -ano | findstr :8080
   ```
2. WebSocket server có đang chạy không?
   - Terminal WebSocket có chạy `php websocket/server.php`?

### ❌ Lỗi: Composer install thất bại

```bash
# Xóa composer.lock và vendor folder, cài lại
rm -r vendor
rm composer.lock
composer install
```

---

## 📁 Cấu trúc dự án

```
fullstack/
├── index.php                    # Entry point chính
├── config.php                   # ⚙️ Cấu hình database & site
├── router.php                   # 🔀 Định tuyến URL
├── helpers.php                  # 🛠️ Hàm tiện ích
│
├── api/                         # 📡 REST API endpoints
│   ├── chat.php
│   ├── comments.php
│   ├── user.php
│   └── ...
│
├── Controllers/                 # 🎮 Logic xử lý
│   ├── AuthController.php
│   ├── PostController.php
│   └── ...
│
├── Models/                      # 💾 Data layer
│   ├── User.php
│   ├── Post.php
│   └── ...
│
├── Views/                       # 🎨 Templates (HTML)
│   ├── home/
│   ├── posts/
│   ├── auth/
│   └── ...
│
├── websocket/
│   └── server.php              # 💬 WebSocket server
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── main.js
│       └── notifications.js
│
├── uploads/                     # 📸 Avatar & images
│   ├── avatars/
│   └── messages/
│
├── vendor/                      # 📦 Composer packages
│
├── database.sql                 # 📊 SQL schema
├── composer.json                # 📋 Dependencies
└── README.md                    # 📖 File này
```

---

## 🔧 Công nghệ sử dụng

### **Backend**
- **PHP 7.4+** - Server-side language
- **MySQL 5.7+ / MariaDB** - Database
- **PDO** - Database abstraction layer
- **Composer** - Dependency manager
- **Ratchet WebSocket** - Real-time chat
- **bcrypt** - Password hashing

### **Frontend**
- **HTML5** - Markup
- **CSS3** - Styling (Grid, Flexbox, Variables)
- **JavaScript ES6+** - Vanilla JS (no framework)
- **Fetch API** - AJAX requests
- **WebSocket API** - Real-time communication
- **Font Awesome 6.4.0** - Icons

### **Architecture**
- **MVC Pattern** - Models, Views, Controllers
- **RESTful API** - Clean endpoints
- **Session-based Auth** - User authentication
- **RBAC** - Role-based access control

---

## ✨ Tính năng chính

- ✅ **Xác thực & Phân quyền** - 3 vai trò (Admin, Chủ trọ, Người thuê)
- ✅ **Quản lý bài đăng** - Tạo, sửa, xóa bài cho thuê
- ✅ **Upload ảnh đa phương tiện** - Drag & drop, preview
- ✅ **Bình luận lồng nhau** - Nested comments không giới hạn
- ✅ **Đánh giá sao** - Rating 1-5 sao
- ✅ **Like/Favorite** - Yêu thích bài đăng
- ✅ **Chat realtime** - WebSocket connection
- ✅ **Thông báo** - Notification system
- ✅ **Dashboard Admin** - Quản lý hệ thống
- ✅ **Responsive Design** - Mobile-friendly

---

## 🔐 Security

- 🔒 Password hashing với bcrypt
- 🔒 PDO prepared statements (SQL injection prevention)
- 🔒 Session-based authentication
- 🔒 Input sanitization & validation
- 🔒 File upload validation

---

## 📞 Liên hệ & Support

- **Tác giả:** tussstudyit
- **Email:** nguyendt.24it@vku.udn.vn

---

## 📄 License

Dự án này được tạo cho mục đích học tập.

**Last Updated:** 2026-07-03
- ✓ Tìm kiếm bài đăng theo từ khóa
- ✓ Lọc theo địa điểm (quận/huyện)
- ✓ Lọc theo khoảng giá
- ✓ Lọc theo category
- ✓ Search box trên trang chủ

### 🚀 Chức năng đang phát triển

#### 💬 Chat/Messaging
- ✓ Cấu trúc database (conversations, messages)
- ⏳ WebSocket server (Ratchet) đã setup
- ⏳ Giao diện chat realtime
- ⏳ Danh sách hội thoại
- ⏳ Chat giữa landlord và tenant

#### 📝 Đánh giá (Reviews)
- ✓ Cấu trúc database (reviews table)
- ⏳ Form đánh giá phòng trọ
- ⏳ Hiển thị rating tổng hợp
- ⏳ Lịch sử đánh giá

### 🗑️ Chức năng chưa triển khai
- ❌ Tích hợp thanh toán online
- ❌ Xác thực email
- ❌ QR code cho bài đăng
- ❌ Export/Import dữ liệu


## 🚀 Hướng dẫn cài đặt

### Yêu cầu hệ thống
- **PHP 7.4+** (khuyến nghị PHP 8.0+)
- **MySQL 5.7+** hoặc **MariaDB 10.2+**
- **Apache** hoặc **Nginx** web server
- **Composer** (để cài đặt dependencies)
- **Git** (để clone repository)
- **mod_rewrite** enabled (cho Apache)

### Các bước cài đặt

#### 1. Clone repository
```bash
git clone https://github.com/tussstudyit/fullstack.git
cd fullstack
```

#### 2. Cài đặt dependencies
```bash
composer install
```
Nếu chưa có Composer, tải tại: https://getcomposer.org/

#### 3. Tạo database
```bash
# Đăng nhập MySQL
mysql -u root -p

# Tạo database
CREATE DATABASE fullstack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 4. Import database schema
```bash
mysql -u root -p fullstack < database.sql
```
Hoặc import qua phpMyAdmin:
- Truy cập http://localhost/phpmyadmin
- Chọn database `fullstack`
- Tab "Import" → Chọn file `database.sql`

#### 5. Cấu hình database
Mở file `config.php` và cập nhật thông tin:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fullstack');
define('DB_USER', 'root');
define('DB_PASS', '');  // Mật khẩu MySQL của bạn
define('BASE_PATH', '/fullstack/');  // Đường dẫn web root
```

#### 6. Cấp quyền thư mục uploads
```bash
# Linux/Mac
chmod 755 uploads/
chmod 755 uploads/avatars/

# Windows
# Chuột phải → Properties → Security → Edit
# Cho phép Full Control cho user hiện tại
```

#### 7. Cấu hình Apache (nếu dùng)
Đảm bảo `mod_rewrite` được bật:
```apache
# Kiểm tra
apache2 -M | grep rewrite

# Enable nếu chưa có
sudo a2enmod rewrite
sudo service apache2 restart
```

#### 8. Chạy ứng dụng

**Option 1: Sử dụng PHP Built-in Server**
```bash
php -S localhost:3000
```
Truy cập: http://localhost:3000

**Option 2: Sử dụng XAMPP/WAMP**
- Copy project vào `htdocs/` hoặc `www/`
- Truy cập: http://localhost/fullstack

**Option 3: Sử dụng Apache VirtualHost**
```apache
<VirtualHost *:80>
    ServerName fullstack.local
    DocumentRoot "D:/baitapcuoiky/fullstack"
    <Directory "D:/baitapcuoiky/fullstack">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Thêm vào `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 fullstack.local
```

#### 9. Chạy WebSocket Server (Tùy chọn - Cho chat)
```bash
# Windows
start-chat.bat

# Linux/Mac
php websocket/server.php
```

#### 10. Đăng nhập
Sử dụng tài khoản mặc định:
- **Admin:** admin / 123456
- **Landlord:** landlord1 / 123456
- **Tenant:** tenant1 / 123456

### Troubleshooting

#### ❌ Lỗi 404 Not Found
**Nguyên nhân:** mod_rewrite chưa được kích hoạt hoặc .htaccess không hoạt động

**Giải pháp:**
```bash
# Apache
sudo a2enmod rewrite
sudo service apache2 restart

# Kiểm tra AllowOverride
# Trong httpd.conf hoặc apache2.conf
<Directory "/path/to/fullstack">
    AllowOverride All
</Directory>
```

#### ❌ Lỗi kết nối database
**Nguyên nhân:** Credentials sai hoặc MySQL chưa chạy

**Giải pháp:**
```bash
# Kiểm tra MySQL
mysql -u root -p

# Xem lại config.php
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

#### ❌ Lỗi upload ảnh
**Nguyên nhân:** Thư mục uploads không có quyền ghi

**Giải pháp:**
```bash
# Linux/Mac
chmod -R 755 uploads/

# Windows: Properties → Security → Full Control
```

#### ❌ Session không hoạt động
**Nguyên nhân:** PHP session chưa được cấu hình

**Giải pháp:**
```php
// Kiểm tra php.ini
session.save_path = "/tmp"

// Hoặc tạo thư mục sessions
mkdir sessions
chmod 755 sessions
```

#### ❌ Composer dependencies lỗi
**Giải pháp:**
```bash
composer update
composer dump-autoload
```

#### ❌ WebSocket không kết nối
**Kiểm tra:**
```bash
# Port 8080 có đang được dùng?
netstat -an | findstr :8080

# Chạy lại server
php websocket/server.php
```

## 📊 Cơ sở dữ liệu

### Các bảng chính:

#### 1. **users** - Quản lý người dùng
- `id`, `username`, `email`, `password`
- `full_name`, `phone`, `avatar`, `bio`
- `role` (admin, landlord, tenant)
- `status` (active, inactive, banned)
- `created_at`, `updated_at`

#### 2. **posts** - Bài đăng phòng trọ
- `id`, `user_id`, `category_id`
- `title`, `description`, `address`, `district`, `city`
- `slug` (VARCHAR 255 UNIQUE) - URL-friendly slug từ title
- `price`, `area`, `room_type`, `room_status`
- `max_people`, `gender`, `amenities`, `utilities`, `rules`
- `available_from`, `deposit_amount`
- `electric_price`, `water_price`
- `status` (pending, approved, rejected, rented)
- `is_featured`, `views`
- `created_at`, `updated_at`

#### 3. **categories** - Danh mục loại trọ
- `id`, `name`, `slug`, `description`, `icon`
- `created_at`

#### 4. **post_images** - Hình ảnh bài đăng
- `id`, `post_id`, `image_url`
- `is_primary`, `display_order`
- `created_at`

#### 5. **favorites** - Danh sách yêu thích
- `id`, `user_id`, `post_id`
- `created_at`
- **Unique constraint:** (user_id, post_id)

#### 6. **comments** - Bình luận & phản hồi lồng nhau
- `id`, `post_id`, `user_id`, `parent_id`
- `content`, `rating` (0-5)
- `created_at`, `updated_at`
- **Hỗ trợ:** Nested replies không giới hạn độ sâu

#### 7. **comment_votes** - Upvote/Downvote bình luận
- `id`, `comment_id`, `user_id`
- `vote` (1 = upvote, -1 = downvote)
- `created_at`
- **Unique constraint:** (comment_id, user_id)

#### 8. **post_likes** - Lượt thích bài đăng
- `id`, `post_id`, `user_id`
- `created_at`
- **Unique constraint:** (post_id, user_id)

#### 9. **reviews** - Đánh giá phòng trọ
- `id`, `post_id`, `user_id`
- `rating` (1-5), `comment`
- `created_at`, `updated_at`
- **Unique constraint:** (post_id, user_id)

#### 10. **conversations** - Cuộc hội thoại chat
- `id`, `post_id`, `landlord_id`, `tenant_id`
- `last_message`, `last_message_at`
- `created_at`
- **Unique constraint:** (post_id, landlord_id, tenant_id)

#### 11. **messages** - Tin nhắn
- `id`, `conversation_id`, `sender_id`
- `message`, `is_read`
- `created_at`

#### 12. **notifications** - Thông báo
- `id`, `user_id`
- `type` (message, review, post_approved, post_rejected, system, comment, reply, rating, post_like)
- `title`, `message`, `link`
- `is_read`, `created_at`

### Database Indexes
- **Performance indexes:** user_id, post_id, category_id, created_at
- **Search indexes:** email, username, slug, status
- **Composite indexes:** (city, district), (post_id, user_id)

### Relationships
```
users (1) ──> (N) posts
users (1) ──> (N) comments
users (1) ──> (N) favorites
posts (1) ──> (N) post_images
posts (1) ──> (N) comments
posts (1) ──> (N) favorites
posts (1) ──> (N) post_likes
comments (1) ──> (N) comment_votes
comments (1) ──> (N) comments (nested replies)
categories (1) ──> (N) posts
```

## 🎨 Giao diện

### Trang chính
- **index.php** - Trang chủ
  - Hero carousel với 5 slides
  - Search box với filter (địa điểm, giá)
  - Categories grid (4 loại phòng)
  - Featured posts (3 bài nổi bật)
  - Amenities showcase (8 tiện ích)
  - Statistics & CTA section
  - Footer với links

### Trang người dùng (Views/)

#### Authentication (/auth/)
- **login.php** - Đăng nhập
  - Username/Password form
  - Remember me option
  - Error handling
- **register.php** - Đăng ký
  - Role selection (Landlord/Tenant)
  - Full form validation
  - Auto-login after registration

#### Posts (/posts/)
- **list.php** - Danh sách bài đăng
  - Search & filter bar
  - Grid layout (3 columns)
  - Pagination
  - Favorite heart icon (red)
  - Sort options
- **detail.php** - Chi tiết bài đăng
  - Image gallery carousel
  - Full post information
  - Contact landlord (call/message - green buttons)
  - Like button (blue, thumbs-up)
  - Favorite button (red, heart)
  - Comments section với nested replies
  - Rating stars
  - Landlord info with avatar
- **create.php** - Tạo/Sửa bài đăng
  - Multi-step form
  - Image upload (drag-drop)
  - Preview functionality
  - Rich text description
  - Category selection

#### User Pages (/user/)
- **profile.php** - Hồ sơ cá nhân
  - Avatar upload
  - Personal information
  - Edit profile form
  - Account settings
- **my-posts.php** - Quản lý tin đăng
  - List user's posts
  - Edit/Delete actions
  - View statistics (views, created date)
  - Quick actions
- **favorites.php** - Danh sách yêu thích
  - Grid layout
  - Quick remove option
  - Post preview
- **notifications.php** - Thông báo
  - Full notification list
  - Mark as read/unread
  - Filter by type
  - Icon-based categorization

#### Chat (/chat/)
- **chat-list.php** - Danh sách cuộc trò chuyện
  - Conversation previews
  - Unread count badges
  - Last message timestamp
- **chat.php** - Cửa sổ chat
  - Realtime messaging (WebSocket)
  - Message history
  - Typing indicators
  - Read receipts

### Trang Admin (Views/admin/)
- **dashboard.php** - Tổng quan hệ thống
  - Statistics cards
  - Recent activities
  - Charts & graphs
- **posts.php** - Quản lý bài đăng
  - Approve/Reject posts
  - View all posts
  - Delete spam
  - Status filters
- **users.php** - Quản lý người dùng
  - User list with roles
  - Ban/Activate users
  - User statistics
- **reports.php** - Báo cáo & thống kê
  - System reports
  - User feedback
  - Violations
- **settings.php** - Cài đặt hệ thống
  - General settings
  - Email configuration
  - System preferences

### UI Components

#### Navigation Header
- Logo với slogan "Nơi bạn thuộc về"
- Menu links (dynamic based on role)
- Notification bell với badge
- User avatar dropdown
- Mobile hamburger menu

#### Notification Dropdown
- Realtime updates
- Icon-based notification types:
  - 👍 Thumbs-up for likes
  - ❤️ Heart for favorites
  - 💬 Comment bubble for comments
  - ⭐ Star for ratings
- Mark all as read button
- View all link

#### Favorite System
- Heart icon (outline/filled)
- Red color (#ef4444)
- Smooth animation
- Synchronized across all pages

#### Color Scheme
- **Primary Blue:** #3b82f6
- **Danger Red:** #ef4444 (favorites, delete)
- **Info Cyan:** #0ea5e9 (likes)
- **Success Green:** #10b981 (contact actions)
- **Warning Yellow:** #f59e0b
- **Gray Scale:** #6b7280, #9ca3af, #e5e7eb

## 🔑 Tài khoản mặc định

Sau khi import database, có thể sử dụng các tài khoản mặc định (tất cả password: **123456**):

### 👨‍💼 Admin
```
Username: admin
Password: 123456
Email: admin@timtro.com
```
**Quyền hạn:**
- Truy cập admin dashboard
- Quản lý tất cả bài đăng
- Quản lý người dùng
- Approve/Reject posts
- Xem reports & statistics
- Xóa bài đăng spam
- Ban/Unban users

### 🏠 Người cho thuê (Landlord)
```
Username: landlord1
Password: 123456
Email: landlord1@gmail.com
```
**Quyền hạn:**
- Đăng bài cho thuê phòng trọ
- Quản lý bài đăng của mình
- Upload/Delete hình ảnh
- Xem bình luận trên bài đăng
- Chat với tenant
- Bình luận trên bài đăng khác
- Like/Favorite bài đăng

```
Username: landlord2
Password: 123456
Email: landlord2@gmail.com
```

### 👨‍🎓 Người thuê (Tenant)
```
Username: tenant1
Password: 123456
Email: tenant1@gmail.com
```
**Quyền hạn:**
- Tìm kiếm phòng trọ
- Xem chi tiết bài đăng
- Bình luận & đánh giá
- Phản hồi bình luận (nested replies)
- Vote bình luận (upvote/downvote)
- Thêm/Xóa yêu thích
- Like bài đăng
- Chat với landlord
- Quản lý profile

```
Username: tenant2
Password: 123456
Email: tenant2@gmail.com
```

### 🔐 Tạo tài khoản mới
Người dùng có thể tự đăng ký tài khoản mới tại:
```
http://localhost:3000/Views/auth/register.php
```
Chọn vai trò: Landlord (Chủ trọ) hoặc Tenant (Người thuê)

### 🔑 Đổi mật khẩu
Để đổi password trong database:
```sql
-- Password mới sẽ là: newpassword123
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
```

### 📧 Forgot Password
⏳ Tính năng đang phát triển (cần email configuration)

## 🔑 Hệ thống Phân quyền (RBAC)

Hệ thống hỗ trợ 3 vai trò với quyền hạn khác nhau:

### 1. **Admin** 👨‍💼
- **Quyền**:
  - Xem tất cả bài đăng
  - Xóa bài đăng sai phạm
  - Quản lý người dùng (view, delete)
  - Xem báo cáo thống kê
  - Truy cập dashboard admin
  - Xóa bình luận spam
- **Hạn chế**:
  - Không thể tạo bài đăng cho thuê
  - Không thể edit bài đăng của người khác

### 2. **Landlord** (Chủ trọ) 🏠
- **Quyền**:
  - Tạo bài đăng cho thuê
  - Quản lý bài đăng của mình (view, edit, delete)
  - Upload ảnh cho bài đăng
  - Xem bình luận trên bài đăng của mình
  - Nhập vai trò Admin khi được gán
  - Bình luận và phản hồi trên bài đăng khác
  - Bình chọn (vote) bình luận
  - Thêm bài đăng vào yêu thích
- **Hạn chế**:
  - Chỉ có thể edit/delete bài đăng của chính mình
  - Không thể xóa bài đăng của người khác
  - Không thể truy cập dashboard admin (trừ khi là Admin)

### 3. **Tenant** (Người thuê) 👨‍🎓
- **Quyền**:
  - Xem tất cả bài đăng
  - Bình luận trên bài đăng
  - Phản hồi bình luận (nested replies)
  - Bình chọn (vote) bình luận
  - Thêm bài đăng vào yêu thích
  - Xem danh sách yêu thích
  - Xem hồ sơ cá nhân
  - Cập nhật thông tin hồ sơ
- **Hạn chế**:
  - Không thể tạo bài đăng
  - Không thể xóa bài đăng khác
  - Không thể truy cập dashboard admin
  - Chỉ có thể edit/delete bình luận của chính mình

### Cách kiểm tra vai trò

Vai trò của người dùng được lưu trong session:
```php
// Kiểm tra vai trò người dùng
if ($_SESSION['role'] === 'admin') {
    // Chỉ admin mới có thể...
}

if ($_SESSION['role'] === 'landlord') {
    // Chỉ chủ trọ mới có thể...
}

if ($_SESSION['role'] === 'tenant') {
    // Chỉ người thuê mới có thể...
}
```

### Middleware kiểm tra quyền

Các view và controller đều có kiểm tra quyền:
- `Views/admin/` - Chỉ admin có thể truy cập
- `Controllers/PostController.php` - Kiểm tra owner khi edit/delete
- `Controllers/CommentController.php` - Kiểm tra quyền bình luận & phản hồi
- `api/comments.php` - Kiểm tra quyền voting & xóa

---

## 📝 Tích hợp PHP

✅ **Dự án đã được tích hợp PHP hoàn toàn**

### Cấu trúc MVC

#### Models (Data Layer)
- **User.php** - Quản lý người dùng
  - `findByUsername()`, `findByEmail()`, `create()`, `update()`
  - `checkLogin()`, `getUserById()`, `getAllUsers()`
  
- **Post.php** - Quản lý bài đăng
  - `create()`, `update()`, `delete()`, `getById()`
  - `getAll()`, `getByUserId()`, `getByCategory()`
  - `search()`, `incrementViews()`, `updateStatus()`
  - `findBySlug()` - Lấy post theo slug
  - Tự động generate unique slug khi tạo/update
  
- **PostImage.php** - Quản lý ảnh bài đăng
  - `add()`, `delete()`, `getByPostId()`
  - `getPrimaryImage()`, `setPrimary()`
  
- **Comment.php** - Quản lý bình luận & phản hồi lồng nhau
  - `create()`, `delete()`, `getByPost()`
  - `getReplies()`, `getVoteCount()`, `getUserVote()`
  - Hỗ trợ nested replies không giới hạn độ sâu
  
- **Favorite.php** - Quản lý yêu thích
  - `add()`, `remove()`, `check()`, `getByUserId()`
  
- **Notification.php** - Quản lý thông báo
  - `create()`, `getByUserId()`, `markAsRead()`
  - `markAllAsRead()`, `getUnreadCount()`, `delete()`
  
- **Category.php** - Quản lý danh mục
  - `getAll()`, `getById()`, `getBySlug()`

#### Controllers (Business Logic)
- **AuthController.php** - Xác thực & phân quyền
  - `register()` - Đăng ký (auto-login)
  - `login()` - Đăng nhập với session
  - `logout()` - Đăng xuất an toàn
  
- **PostController.php** - CRUD bài đăng
  - `create()` - Tạo bài đăng mới
  - `update()` - Cập nhật bài đăng
  - `delete()` - Xóa bài đăng (owner only)
  - `approve()`, `reject()` - Admin actions
  
- **CommentController.php** - Bình luận & voting
  - `addComment()` - Thêm bình luận
  - `addReply()` - Phản hồi với parent_id
  - `vote()` - Upvote/Downvote
  - `delete()` - Xóa (author only)
  
- **FavoriteController.php** - Quản lý yêu thích
  - `add()` - Thêm vào favorites
  - `remove()` - Xóa khỏi favorites
  - `check()` - Kiểm tra trạng thái
  
- **ImageController.php** - Xử lý upload ảnh
  - `upload()` - Upload với validation
  - `delete()` - Xóa ảnh
  - Validation: file type, size, format
  
- **LikeController.php** - Post likes
  - `toggle()` - Toggle like/unlike
  - `getCount()` - Đếm số lượng like
  
- **NotificationController.php** - Hệ thống thông báo
  - `create()` - Tạo notification
  - `markRead()` - Đánh dấu đã đọc
  - `getRecent()` - Lấy thông báo mới nhất

#### Views (Presentation Layer)
- **PHP Templates** - Dynamic rendering với PHP
- **Embedded PHP** - <?php ?> blocks
- **Data Binding** - Variables from controllers
- **Conditional Rendering** - if/else for user roles
- **Loops** - foreach for data lists
- **Includes** - Reusable components

### Security Features
- ✓ **Password Hashing:** bcrypt với cost 10
- ✓ **SQL Injection Prevention:** PDO Prepared Statements
- ✓ **XSS Protection:** htmlspecialchars() cho output
- ✓ **CSRF Protection:** Session validation
- ✓ **File Upload Security:** Type & size validation
- ✓ **Role-based Access:** Middleware checks
- ✓ **Session Security:** Secure session configuration
- ✓ **Slug Generation:** Vietnamese character conversion with unique validation

### Database Connection
```php
// config.php
function getDB() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        die("Database connection error");
    }
}
```

### API Endpoints

#### Comments API (`/api/comments.php`)
- **POST** `action=add_comment` - Thêm bình luận mới
  - Parameters: `post_id`, `content`, `rating` (optional)
  - Returns: Comment object with user info
  
- **POST** `action=add_reply` - Phản hồi bình luận
  - Parameters: `post_id`, `parent_id`, `content`
  - Returns: Reply object with nested structure
  
- **POST** `action=vote` - Upvote/Downvote bình luận
  - Parameters: `comment_id`, `vote` (1 or -1)
  - Returns: Updated vote counts
  
- **POST** `action=delete` - Xóa bình luận
  - Parameters: `comment_id`
  - Authorization: Only comment author
  - Returns: Success/Error status

#### Image Upload API (`/api/upload-image.php`)
- **POST** Upload image file
  - Parameters: `image` (file), `post_id` (optional)
  - Validation: File type, size
  - Returns: Image URL and metadata

#### Chat API (`/api/chat.php`)
- **WebSocket** Realtime messaging
  - Connect via ws://localhost:8080
  - Events: message, typing, read
  - Ratchet server implementation

#### Authentication Flow
- **AuthController.php**
  - `action=register` - User registration
  - `action=login` - User authentication
  - `action=logout` - Session cleanup

#### Posts Management
- **PostController.php**
  - `action=create` - Create new post
  - `action=update` - Update existing post
  - `action=delete` - Delete post
  - `action=approve` - Admin approval (admin only)
  - `action=reject` - Admin rejection (admin only)

#### Favorites Management
- **FavoriteController.php**
  - `action=add` - Add to favorites
  - `action=remove` - Remove from favorites
  - `action=check` - Check if favorited

#### Likes Management
- **LikeController.php**
  - `action=toggle` - Toggle post like
  - Returns: Updated like count

#### Notifications
- **NotificationController.php**
  - `action=get` - Fetch user notifications
  - `action=mark_read` - Mark as read
  - `action=mark_all_read` - Mark all as read
  - `action=delete` - Delete notification

### Tính năng Backend
- ✓ PDO Database Connection (config.php)
- ✓ Session-based Authentication
- ✓ Role-based Access Control (RBAC)
- ✓ File Upload with Validation
- ✓ Error Handling & Logging
- ✓ Data Sanitization & Validation
- ✓ Password Hashing (bcrypt)
- ✓ SQL Injection Prevention (Prepared Statements)
- ✓ XSS Protection (htmlspecialchars)
- ✓ CSRF Protection (Session validation)
- ✓ Image Upload Security (file type, size validation)
- ✓ WebSocket Server Setup (Ratchet)
- ✓ Composer Dependency Management
- ✓ Database Migrations
- ✓ Helper Functions (helpers.php)
  - `isLoggedIn()` - Check authentication
  - `hasRole($role)` - Check user role
  - `redirect($url)` - URL redirection
  - `timeAgo($timestamp)` - Format time
  - `getBasePath()` - Get base URL
  - `getPlaceholderImage()` - Tạo placeholder image động
  - `generateSlug($text)` - Convert tiếng Việt thành slug
  - `getUniqueSlug($title, $postId)` - Tạo slug unique

### Composer Dependencies
```json
{
  "require": {
    "cboden/ratchet": "^0.4",
    "guzzlehttp/psr7": "^2.0",
    "symfony/http-foundation": "^6.0",
    "symfony/routing": "^6.0"
  }
}
```

### WebSocket Server
- **Location:** `websocket/server.php`
- **Port:** 8080
- **Technology:** Ratchet (PHP WebSocket library)
- **Usage:** Realtime chat messaging
- **Start Command:** `php websocket/server.php` or `start-chat.bat`
- **Status:** Configured, ready for chat implementation

- **Repository Pattern** for database queries

## 📋 Tính năng

Giao diện được thiết kế responsive với breakpoints:

### Desktop (1200px+)
- Full navigation menu
- 3-column grid layout
- Sidebar filters
- Large image galleries
- Desktop notifications dropdown

### Tablet (768px - 1024px)
- 2-column grid layout
- Collapsible sidebar
- Touch-friendly buttons
- Optimized image sizes

### Mobile (< 768px)
- Single column layout
- Hamburger menu
- Bottom navigation bar
- Swipe gestures
- Mobile-optimized forms
- Stacked elements

### CSS Breakpoints
```css
/* Mobile First Approach */
@media (max-width: 768px) {
    .grid { grid-template-columns: 1fr; }
    .nav-menu { display: none; }
    .mobile-menu-toggle { display: block; }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1025px) {
    .grid { grid-template-columns: repeat(3, 1fr); }
}
```

### Touch-Friendly Elements
- Minimum 44x44px touch targets
- Swipe-enabled carousels
- Pull-to-refresh (planned)
- Touch-optimized dropdowns

## 🤝 Hỗ trợ & Đóng góp

### Gặp vấn đề?

#### 1. Kiểm tra Troubleshooting
- Xem lại phần [Troubleshooting](#troubleshooting) ở trên
- Kiểm tra logs: `error_log`, console.log (F12)
- Xem PHP errors trong Apache/Nginx logs

#### 2. Tìm trong Issues
- Tìm kiếm trong [GitHub Issues](https://github.com/tussstudyit/fullstack/issues)
- Có thể vấn đề đã được giải quyết

#### 3. Tạo Issue mới
Nếu không tìm thấy giải pháp, tạo issue mới với:
- Mô tả chi tiết vấn đề
- Steps to reproduce
- Screenshot/Error logs
- Môi trường (PHP version, OS, browser)

### Đóng góp code

#### Fork & Pull Request
```bash
# 1. Fork repository
# 2. Clone fork của bạn
git clone https://github.com/YOUR_USERNAME/fullstack.git

# 3. Tạo branch mới
git checkout -b feature/your-feature-name

# 4. Commit changes
git add .
git commit -m "Add: Your feature description"

# 5. Push to fork
git push origin feature/your-feature-name

# 6. Tạo Pull Request trên GitHub
```

#### Coding Standards
- **PHP:** PSR-12 coding style
- **JavaScript:** ES6+ syntax
- **CSS:** BEM naming convention
- **Comments:** Tiếng Việt cho business logic, English cho technical
- **Commits:** Conventional Commits format

#### Before Pull Request
- [ ] Test thoroughly
- [ ] No console.log in production
- [ ] Update README if needed
- [ ] Check for SQL injection vulnerabilities
- [ ] Validate all user inputs

### Báo cáo Security Issues
⚠️ **QUAN TRỌNG:** Không tạo public issue cho security vulnerabilities

Email trực tiếp: security@tussstudyit.com (hoặc private message)

### Liên hệ

- 📧 **Email:** tussstudyit@gmail.com
- 🐙 **GitHub:** [@tussstudyit](https://github.com/tussstudyit)
- 🌐 **Website:** https://tussstudyit.github.io/fullstack
- 💬 **Discussions:** [GitHub Discussions](https://github.com/tussstudyit/fullstack/discussions)

### Contributors
Cảm ơn những người đóng góp cho dự án! 🙏

<!-- Sẽ được cập nhật tự động -->
![Contributors](https://contrib.rocks/image?repo=tussstudyit/fullstack)

### License
📄 **MIT License** - Dự án mã nguồn mở cho mục đích học tập

```
Copyright (c) 2025 tussstudyit

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```

### Acknowledgments
- Font Awesome - Icon library
- Ratchet - WebSocket library
- Google Fonts - Typography
- Unsplash - Sample images
- PHP community - Documentation & support

---

## 🎯 Roadmap

### Version 1.0 (Current) ✅
- [x] Authentication & Authorization
- [x] Posts Management (CRUD) with Slug-based URLs
- [x] Comments & Nested Replies
- [x] Favorites & Likes
- [x] Notifications System
- [x] Search & Filters
- [x] Admin Dashboard
- [x] User Profiles with Avatar
- [x] Responsive Design
- [x] Custom 404 Error Page
- [x] URL Rewriting with .htaccess

### Version 1.1 (In Progress) 🚧
- [ ] Realtime Chat (WebSocket)
- [ ] Advanced Search with Elasticsearch
- [ ] Email Notifications
- [ ] Reviews & Rating System
- [ ] Landlord Verification
- [ ] Map Integration (Google Maps)

### Version 2.0 (Planned) 📅
- [ ] Mobile App (React Native)
- [ ] Payment Integration (VNPay, Momo)
- [ ] AI-powered Recommendations
- [ ] Virtual Tour (360° images)
- [ ] Contract Management
- [ ] Multi-language Support (EN/VI)

### Version 3.0 (Future) 🔮
- [ ] Blockchain for rent contracts
- [ ] IoT Smart Room Integration
- [ ] AR/VR Room Preview
- [ ] Tenant Background Check API
- [ ] Automated Rent Payment

---

## 📊 Project Statistics

![GitHub stars](https://img.shields.io/github/stars/tussstudyit/fullstack?style=social)
![GitHub forks](https://img.shields.io/github/forks/tussstudyit/fullstack?style=social)
![GitHub issues](https://img.shields.io/github/issues/tussstudyit/fullstack)
![GitHub pull requests](https://img.shields.io/github/issues-pr/tussstudyit/fullstack)
![GitHub last commit](https://img.shields.io/github/last-commit/tussstudyit/fullstack)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![License](https://img.shields.io/badge/License-MIT-green)

### Code Statistics
- **Total Files:** 50+ PHP files
- **Lines of Code:** ~15,000+ lines
- **Database Tables:** 12 tables
- **API Endpoints:** 20+ endpoints
- **Features:** 50+ features
- **Test Coverage:** 0% (planning to add)

### Modified Files (Slug Implementation)
- ✅ **Database:**
  - `database.sql` - Added slug column to posts table
  
- ✅ **Configuration & Utilities:**
  - `.htaccess` - URL rewriting + 404 handling
  - `helpers.php` - Added generateSlug() & getUniqueSlug()
  - `config.php` - Database configuration
  
- ✅ **Models:**
  - `Models/Post.php` - Added findBySlug(), slug auto-generation
  
- ✅ **Views:**
  - `Views/posts/detail.php` - Accept slug parameter, backward compatible with ID
  - `Views/posts/list.php` - Updated links to use slug
  - `Views/home/home.php` - Updated links to use slug
  - `Views/user/my-posts.php` - Updated links to use slug
  - `Views/user/favorites.php` - Added slug to query, updated links
  - `Views/admin/posts.php` - Updated links to use slug
  - `Views/chat/chat.php` - Updated post links to use slug
  
- ✅ **Error Handling:**
  - `Views/404/404.html` - Custom 404 page with inline CSS & JS
  - `Views/404/404.css` - (Merged into 404.html)
  - `Views/404/404.js` - (Merged into 404.html)
  
- ✅ **API:**
  - `api/chat.php` - Include slug in conversation response
  
- ✅ **Cleanup:**
  - Removed `test-pretty-urls.html` (test file)
  - Removed `test-rewrite.php` (test file)
  - Removed `add_slug_column.php` (migration script)
  - Removed `check-session.php` (debug file)
  - Removed `router.php` (PHP router - not needed with .htaccess)

---

## 🔄 Recent Updates

### December 2025
- ✅ Implemented slug-based URLs for posts
  - Auto-generate slug từ tiếng Việt title
  - Ensure unique slugs (append số nếu trùng)
  - Backward compatible với ID cũ (auto redirect)
  - Updated all links across the application
  - Added `slug` column to posts table
- ✅ Created custom 404 Error Page
  - Animated house icon with bounce & parallax effects
  - Floating emoji houses with float animation
  - Ripple effect on home button
  - Inline CSS & JS (independent file)
  - Responsive design for mobile
- ✅ Configured .htaccess for URL rewriting
  - ErrorDocument 404 directive
  - mod_rewrite rules wrapped in IfModule
  - Catch-all 404 handling for invalid URLs
- ✅ Fixed slug data retrieval in Views/user/favorites.php
- ✅ Cleaned up temporary test files
- ✅ Updated config files with slug support
- ✅ Synchronized UI/UX across all pages
- ✅ Added carousel hero section with gradient overlay
- ✅ Implemented notification bell with dropdown
- ✅ Updated like icon from heart to thumbs-up
- ✅ Fixed avatar display in comments and navigation
- ✅ Standardized color scheme (red/blue/green)
- ✅ Added user dropdown menu
- ✅ Improved mobile responsiveness

### November 2025
- ✅ Implemented nested comments system
- ✅ Added comment voting (upvote/downvote)
- ✅ Created favorites functionality
- ✅ Built notification system
- ✅ Added post likes feature
- ✅ Developed admin dashboard

### October 2025
- ✅ Initial project setup
- ✅ Database schema design
- ✅ MVC architecture implementation
- ✅ Authentication system
- ✅ Posts CRUD operations

---

**⭐ Star this repo nếu project hữu ích!**

**🔖 Watch để nhận updates**

**🍴 Fork để tùy chỉnh theo ý bạn**

---

<div align="center">
  <strong>Made with ❤️ by tussstudyit</strong>
  <br>
  <em>Dự án học tập - Full Stack Web Development</em>
  <br><br>
  <a href="https://github.com/tussstudyit/fullstack">View on GitHub</a>
  ·
  <a href="https://github.com/tussstudyit/fullstack/issues">Report Bug</a>
  ·
  <a href="https://github.com/tussstudyit/fullstack/issues">Request Feature</a>
</div>
