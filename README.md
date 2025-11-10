# WEB TÌM TRỌ CHO SINH VIÊN

Hệ thống tìm kiếm và quản lý phòng trọ dành cho sinh viên với 3 vai trò: Admin, Người cho thuê, Người thuê.

## 📋 Tính năng

### Admin
- Quản lý và phê duyệt các bài đăng cho thuê
- Xóa các bài đăng sai phạm hoặc không hợp lệ
- Quản lý người dùng trong hệ thống
- Theo dõi thống kê, báo cáo tổng quan

### Người cho thuê
- Đăng bài giới thiệu trọ hoặc căn hộ
- Cập nhật mô tả, hình ảnh, giá và trạng thái
- Nhận và phản hồi tin nhắn từ người thuê qua hệ thống chat
- Quản lý danh sách bài đăng cá nhân

### Người thuê
- Truy cập trang chủ, tìm kiếm và lọc bài đăng
- Xem chi tiết bài đăng, đánh giá phòng trọ
- Thêm bài đăng vào mục yêu thích
- Liên hệ với người cho thuê qua hệ thống chat

## 🏗️ Cấu trúc dự án

```
project/
├── Models/                 # Các file Model (PHP) - chưa tích hợp
├── Controllers/            # Các file Controller (PHP) - chưa tích hợp
├── Views/                  # Giao diện HTML
│   ├── auth/              # Đăng nhập, đăng ký
│   ├── home/              # Trang chủ
│   ├── posts/             # Danh sách, chi tiết, đăng bài
│   ├── chat/              # Giao diện chat
│   ├── user/              # Quản lý tin, yêu thích
│   └── admin/             # Dashboard admin
├── assets/
│   ├── css/               # File CSS
│   ├── js/                # File JavaScript
│   └── images/            # Hình ảnh
├── uploads/               # Thư mục upload files
├── database.sql           # File SQL tạo database
├── config.php             # Cấu hình kết nối database
└── index.php              # File index chính

```

## 🚀 Hướng dẫn cài đặt

### Yêu cầu
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Apache/Nginx web server

### Các bước cài đặt

1. **Import database**
   ```bash
   # Tạo database và import file SQL
   mysql -u root -p < database.sql
   ```

2. **Cấu hình kết nối database**
   - Mở file `config.php`
   - Cập nhật thông tin kết nối database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'baitaplon');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Cấp quyền thư mục uploads**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/posts/
   chmod 755 uploads/users/
   ```

4. **Chạy ứng dụng**
   - Khởi động web server (Apache/Nginx)
   - Truy cập: `http://localhost/`

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

**Admin:**
- Username: `admin`
- Password: `admin123`

**Người cho thuê:**
- Username: `landlord1`
- Password: `123456`

**Người thuê:**
- Username: `tenant1`
- Password: `123456`

## 📝 Tích hợp PHP

Dự án hiện tại chỉ có giao diện HTML/CSS/JS. Để tích hợp PHP:

### 1. Tạo Models
Tạo các file trong thư mục `Models/`:
- `UserModel.php` - Xử lý dữ liệu người dùng
- `PostModel.php` - Xử lý dữ liệu bài đăng
- `MessageModel.php` - Xử lý tin nhắn
- v.v...

### 2. Tạo Controllers
Tạo các file trong thư mục `Controllers/`:
- `AuthController.php` - Xử lý đăng nhập, đăng ký
- `PostController.php` - Xử lý CRUD bài đăng
- `ChatController.php` - Xử lý chat
- `AdminController.php` - Xử lý các chức năng admin
- v.v...

### 3. Chuyển đổi Views
- Đổi đuôi file từ `.html` sang `.php`
- Thay thế dữ liệu tĩnh bằng PHP dynamic data
- Thêm session handling và authentication

### 4. Tích hợp chat realtime
Sử dụng WebSocket hoặc Socket.io cho chat realtime:
```php
// Có thể dùng Ratchet cho WebSocket với PHP
composer require cboden/ratchet
```

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

Nếu cần hỗ trợ, vui lòng:
1. Kiểm tra file README này
2. Xem file database.sql để hiểu cấu trúc database
3. Kiểm tra console log trong trình duyệt

## 📄 License

Dự án này được tạo ra cho mục đích học tập.

---

**Lưu ý**: Đây là phiên bản giao diện HTML/CSS/JS. Bạn cần tích hợp PHP vào các Controllers và Models để có chức năng đầy đủ.
