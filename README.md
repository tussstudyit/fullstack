# WEB TÌM TRỌ CHO SINH VIÊN

Hệ thống tìm kiếm và quản lý phòng trọ dành cho sinh viên với 3 vai trò: Admin, Người cho thuê, Người thuê.

## 📋 Tính năng

### ✅ Chức năng đã hoàn thành

#### 🔐 Xác thực & Phân quyền
- ✓ Đăng ký tài khoản (Người cho thuê, Người thuê, Admin)
- ✓ Đăng nhập với session management
- ✓ Phân quyền 3 vai trò: Admin, Landlord (Chủ trọ), Tenant (Người thuê)
- ✓ Auto-login sau khi đăng ký thành công
- ✓ Đăng xuất an toàn

#### 📝 Quản lý bài đăng (Posts)
- ✓ Tạo bài đăng cho thuê với mô tả chi tiết
- ✓ Upload nhiều hình ảnh cho một bài đăng (async upload)
- ✓ Cập nhật (edit) bài đăng đã tạo
- ✓ Xóa bài đăng (chỉ chủ sở hữu)
- ✓ Xem danh sách bài đăng của cá nhân (My Posts)
- ✓ Xem chi tiết bài đăng với full thông tin

#### 🖼️ Xử lý ảnh
- ✓ Upload ảnh đơn lẫn multiple (drag-drop, click to upload)
- ✓ Preview ảnh trước khi upload
- ✓ Xóa ảnh khỏi bài đăng
- ✓ Lưu trữ tệp ảnh trong thư mục uploads
- ✓ Hiển thị ảnh theo thứ tự trong chi tiết bài đăng

#### ⭐ Bình luận & Phản hồi (Comments)
- ✓ Bình luận trên bài đăng
- ✓ Hệ thống phản hồi lồng nhau (nested replies) - không giới hạn độ sâu
- ✓ Bất kỳ người dùng đã đăng nhập đều có thể phản hồi bình luận khác
- ✓ Hiển thị thông tin tác giả bình luận (avatar, tên, vai trò)
- ✓ Xóa bình luận (chỉ tác giả)
- ✓ Xem thời gian bình luận
- ✓ Responsive nested UI với indentation rõ ràng

#### 👍 Bình chọn bình luận (Comment Voting)
- ✓ Upvote/Downvote bình luận
- ✓ Theo dõi lịch sử bình chọn của từng người dùng
- ✓ Hiển thị tổng số upvote/downvote
- ✓ Highlight trạng thái bình chọn hiện tại của user
- ✓ Lưu trữ bình chọn trong database

#### ❤️ Danh sách yêu thích (Favorites)
- ✓ Thêm/xóa bài đăng vào yêu thích
- ✓ Xem danh sách tất cả bài yêu thích
- ✓ Hiển thị icon/status yêu thích trên listing posts

#### 👤 Quản lý hồ sơ (Profile)
- ✓ Xem thông tin cá nhân
- ✓ Cập nhật thông tin hồ sơ (tên, email, số điện thoại, địa chỉ, mô tả)
- ✓ Quản lý chi tiết tài khoản

#### 🔔 Thông báo (Notifications)
- ✓ Hệ thống thông báo cho người dùng
- ✓ Xem danh sách thông báo
- ✓ Đánh dấu thông báo là đã đọc

#### 📊 Dashboard Admin
- ✓ Xem tổng số bài đăng, người dùng
- ✓ Quản lý danh sách bài đăng (view, delete spam posts)
- ✓ Quản lý danh sách người dùng
- ✓ Xem báo cáo/reports từ người dùng

### 🚀 Chức năng đang phát triển

#### 💬 Chat/Messaging
- ⏳ Hệ thống nhắn tin giữa người cho thuê và người thuê
- ⏳ Danh sách hội thoại
- ⏳ Chat realtime

#### 📌 Tìm kiếm & Lọc
- ⏳ Tìm kiếm bài đăng theo từ khóa
- ⏳ Lọc theo địa điểm, giá, loại phòng
- ⏳ Sắp xếp kết quả

#### 📝 Đánh giá (Reviews)
- ⏳ Người thuê đánh giá phòng trọ
- ⏳ Hiển thị rating tổng hợp
- ⏳ Lịch sử đánh giá

### 🗑️ Chức năng đã xóa (Not Implemented)
- ❌ Tích hợp thanh toán online
- ❌ Xác thực email
- ❌ Tìm kiếm advanced
- ❌ QR code cho bài đăng

## 🏗️ Cấu trúc dự án

**Root Path:** `d:\baitapcuoiky`

```
└── 📁 fullstack
    ├── 📁 .local
    │   └── 📁 state
    │       └── 📁 replit
    │           └── 📁 agent
    │               ├── ⚙️ .agent_state_0d679634a6b1adac0df9b45b6d3b2c9dbed86e2f.bin
    │               ├── ⚙️ .agent_state_352005164862c10675240e8e12ea6abef594e543.bin
    │               ├── ⚙️ .agent_state_5824295faf1d89009bdedc6e6b2a7c444c5b930a.bin
    │               ├── ⚙️ .agent_state_bd0421fbf8e37c41afcff2d25b48299623fc0a3e.bin
    │               ├── ⚙️ .agent_state_main.bin
    │               ├── ⚙️ .latest.json
    │               └── ⚙️ repl_state.bin
    ├── 📁 Controllers
    │   ├── 🐘 AuthController.php
    │   ├── 🐘 CommentController.php
    │   ├── 🐘 FavoriteController.php
    │   ├── 🐘 ImageController.php
    │   ├── 🐘 NotificationController.php
    │   └── 🐘 PostController.php
    ├── 📁 Models
    │   ├── 🐘 Category.php
    │   ├── 🐘 Comment.php
    │   ├── 🐘 Favorite.php
    │   ├── 🐘 Notification.php
    │   ├── 🐘 Post.php
    │   ├── 🐘 PostImage.php
    │   └── 🐘 User.php
    ├── 📁 Views
    │   ├── 📁 admin
    │   │   ├── 🐘 dashboard.php
    │   │   ├── 🐘 posts.php
    │   │   ├── 🐘 reports.php
    │   │   ├── 🐘 settings.php
    │   │   └── 🐘 users.php
    │   ├── 📁 auth
    │   │   ├── 🐘 login.php
    │   │   └── 🐘 register.php
    │   ├── 📁 chat
    │   │   ├── 🐘 chat-list.php
    │   │   └── 🐘 chat.php
    │   ├── 📁 home
    │   │   └── 🐘 home.php
    │   ├── 📁 posts
    │   │   ├── 🐘 create.php
    │   │   ├── 🐘 detail.php
    │   │   └── 🐘 list.php
    │   └── 📁 user
    │       ├── 🐘 favorites.php
    │       ├── 🐘 my-posts.php
    │       ├── 🐘 notifications.php
    │       └── 🐘 profile.php
    ├── 📁 api
    │   ├── 🐘 comments.php
    │   └── 🐘 upload-image.php
    ├── 📁 assets
    │   ├── 📁 css
    │   │   └── 🎨 style.css
    │   └── 📁 js
    │       └── 📄 main.js
    ├── 📁 attached_assets
    │   └── 🐘 database_1762788624742.php
    ├── 📁 uploads
    │   ├── ⚙️ .gitignore
    │   ├── 🖼️ post_11_1764662359_692e9c5706be2.png
    │   ├── 🖼️ post_11_1764662551_692e9d17f2dee.png
    │   ├── 🖼️ post_11_1764662552_692e9d1812735.png
    │   ├── 🖼️ post_12_1764662852_692e9e446c73e.png
    │   ├── 🖼️ post_12_1764662852_692e9e4471968.png
    │   ├── 🖼️ post_13_1764690362_692f09baafee4.png
    │   └── 🖼️ post_13_1764690362_692f09bab9697.png
    ├── ⚙️ .htaccess
    ├── 📝 README.md
    ├── 🐘 config.php
    ├── 📄 database.sql
    ├── 🐘 helpers.php
    └── 🐘 index.php
```

## 🚀 Hướng dẫn cài đặt

### Yêu cầu
- PHP 7.4 trở lên (khuyến nghị PHP 8.0+)
- MySQL 5.7 trở lên
- Apache/Nginx web server
- Composer (tùy chọn)

### Các bước cài đặt

1. **Clone repository**
   ```bash
   git clone https://github.com/tussstudyit/fullstack.git
   cd fullstack
   ```

2. **Import database**
   ```bash
   mysql -u root -p < database.sql
   # Hoặc import thông qua phpMyAdmin
   ```

3. **Cấu hình kết nối database**
   - Mở file `config.php`
   - Cập nhật thông tin kết nối:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'baitaplon');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Cấp quyền thư mục uploads**
   ```bash
   # Linux/Mac
   chmod 755 uploads/
   chmod 755 uploads/*
   
   # Windows (thường tự động)
   ```

5. **Chạy ứng dụng**
   - Khởi động web server: `php -S localhost:8000` hoặc Apache
   - Truy cập: `http://localhost:8000` (hoặc domain được cấu hình)
   - Login bằng tài khoản mặc định hoặc đăng ký tài khoản mới

### Troubleshooting

**Lỗi 404 khi truy cập:**
- Kiểm tra file `.htaccess` có được kích hoạt
- Đảm bảo mod_rewrite được enable trên Apache

**Lỗi kết nối database:**
- Kiểm tra MySQL service đang chạy
- Xác nhận credentials trong `config.php`
- Kiểm tra database `baitaplon` đã được tạo

**Lỗi upload ảnh:**
- Kiểm tra thư mục `uploads/` tồn tại
- Xác nhận quyền ghi file (777 cho dev)

## 📊 Cơ sở dữ liệu

### Các bảng chính:
- **users**: Thông tin người dùng
- **posts**: Bài đăng phòng trọ
- **categories**: Danh mục loại trọ
- **post_images**: Hình ảnh bài đăng
- **favorites**: Danh sách yêu thích
- **reviews**: Đánh giá phòng trọ
- **conversations**: Cuộc hội thoại chat
- **messages**: Tin nhắn
- **notifications**: Thông báo

## 🎨 Giao diện

### Trang người dùng
- `Views/auth/login.html` - Đăng nhập
- `Views/auth/register.html` - Đăng ký (có phân quyền)
- `Views/home/index.html` - Trang chủ
- `Views/posts/list.html` - Danh sách bài đăng
- `Views/posts/detail.html` - Chi tiết bài đăng
- `Views/posts/create.html` - Đăng tin (có upload và preview hình ảnh)
- `Views/user/my-posts.html` - Quản lý tin đăng
- `Views/user/favorites.html` - Danh sách yêu thích
- `Views/chat/index.html` - Giao diện chat realtime

### Trang Admin
- `Views/admin/dashboard.html` - Dashboard tổng quan
- `Views/admin/posts.html` - Quản lý bài đăng
- `Views/admin/users.html` - Quản lý người dùng

## 🔐 Tài khoản mặc định

Sau khi import database, có thể sử dụng các tài khoản mặc định:

**Admin:**
- Username: `admin`
- Password: `admin123`
- Vai trò: Quản lý toàn bộ hệ thống

**Người cho thuê (Landlord):**
- Username: `landlord1`
- Password: `123456`
- Vai trò: Đăng bài, quản lý phòng trọ

**Người thuê (Tenant):**
- Username: `tenant1`
- Password: `123456`
- Vai trò: Tìm kiếm, bình luận, yêu thích

> Có thể tạo tài khoản mới thông qua trang đăng ký

## 📝 Tích hợp PHP

✅ **Dự án hiện đã được tích hợp PHP hoàn toàn**

### Cấu trúc MVC
- **Models**: Xử lý dữ liệu từ database
  - User.php - Quản lý người dùng
  - Post.php - Quản lý bài đăng
  - PostImage.php - Quản lý ảnh bài đăng
  - Comment.php - Quản lý bình luận & phản hồi lồng nhau
  - Favorite.php - Quản lý danh sách yêu thích
  - Notification.php - Quản lý thông báo
  - Category.php - Quản lý danh mục

- **Controllers**: Xử lý logic nghiệp vụ
  - AuthController.php - Đăng nhập, đăng ký
  - PostController.php - CRUD bài đăng
  - CommentController.php - Bình luận, phản hồi, voting
  - FavoriteController.php - Quản lý yêu thích
  - ImageController.php - Xử lý upload ảnh
  - NotificationController.php - Xử lý thông báo

- **Views**: Giao diện người dùng
  - auth/ - Đăng nhập, đăng ký
  - home/ - Trang chủ
  - posts/ - Danh sách, chi tiết, tạo bài đăng
  - user/ - Hồ sơ, bài đăng của tôi, yêu thích, thông báo
  - admin/ - Dashboard, quản lý bài đăng, người dùng
  - chat/ - Giao diện chat (đang phát triển)

### API Endpoints
- **POST** `/api/comments.php` - Thêm bình luận/phản hồi
  - `action=add_comment` - Bình luận trên bài đăng
  - `action=add_reply` - Phản hồi bình luận (với parent_id)
  - `action=vote` - Bình chọn bình luận
  - `action=delete` - Xóa bình luận

- **POST** `/api/upload-image.php` - Upload ảnh bài đăng

### Tính năng Backend
- PDO Database Connection (config.php)
- Session-based Authentication
- Role-based Access Control (RBAC)
- File Upload with Validation
- Error Handling & Logging
- Data Sanitization & Validation

## 🔧 Công nghệ sử dụng

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP (cần tích hợp)
- **Database**: MySQL
- **Icons**: Font Awesome 6.4
- **Styling**: Custom CSS với CSS Variables

## 📱 Responsive Design

Giao diện được thiết kế responsive, tương thích với:
- Desktop (1200px+)
- Tablet (768px - 1024px)
- Mobile (< 768px)

## 🤝 Hỗ trợ

Nếu gặp vấn đề hoặc cần tính năng mới:
1. Kiểm tra phần Troubleshooting ở trên
2. Xem console.log trong trình duyệt (F12)
3. Kiểm tra error logs của PHP/MySQL
4. Liên hệ qua GitHub Issues

## 📧 Liên hệ & Đóng góp

- **Repository**: https://github.com/tussstudyit/fullstack
- **Author**: tussstudyit
- **License**: MIT (cho mục đích học tập)

---

**Lưu ý**: Đây là dự án được phát triển bằng PHP thuần (không dùng framework). Mã nguồn sạch, dễ hiểu và phù hợp cho học tập.
