# Overview

Đây là dự án **Web Tìm Trọ Cho Sinh Viên** - một nền tảng kết nối người cho thuê và người thuê phòng trọ dành riêng cho sinh viên. Hệ thống hỗ trợ 3 vai trò chính: Admin (quản lý và phê duyệt), Người cho thuê (đăng tin), và Người thuê (tìm kiếm và liên hệ).

Dự án hiện tại đã hoàn thiện **100% giao diện frontend** (HTML/CSS/JS) và **database schema SQL**. Người dùng cần tự tích hợp PHP vào backend (Models và Controllers) để có chức năng đầy đủ.

# Recent Changes

**Ngày 10/11/2024**: Hoàn thiện toàn bộ giao diện và cấu trúc dự án
- ✅ Tạo cấu trúc thư mục MVC đầy đủ
- ✅ Xây dựng database schema với 9 bảng chính
- ✅ Hoàn thiện 15+ giao diện HTML/CSS/JS
- ✅ Tích hợp JavaScript utilities và responsive design
- ✅ Cấu hình Python development server
- ✅ Tạo documentation đầy đủ trong README.md

# User Preferences

- Ngôn ngữ giao tiếp: Tiếng Việt
- Phong cách thiết kế: Hiện đại, đơn giản, thân thiện với sinh viên
- Màu sắc chủ đạo: Gradient xanh tím (#667eea đến #764ba2)

# System Architecture

## Frontend Architecture

**Technology Stack**:
- HTML5 với semantic markup
- CSS3 với CSS Variables cho theming
- Vanilla JavaScript (ES6+)
- Font Awesome 6.4.0 cho icons
- Responsive design cho mobile/tablet/desktop

**Design Pattern**: 
- Multi-page application với file-based routing
- Component-based visual structure
- Consistent color scheme và spacing system
- Mobile-first responsive approach

**Layout System**:
- CSS Grid và Flexbox
- Breakpoints: 1024px (tablet), 768px (mobile), 640px (small mobile)
- Container max-width: 1400px

**Key Pages**:
1. **Authentication** (`Views/auth/`)
   - `login.html` - Form đăng nhập với remember me
   - `register.html` - Form đăng ký với role selection (Landlord/Tenant)

2. **Home** (`Views/home/`)
   - `index.html` - Trang chủ với hero section, search box, categories, featured posts

3. **Posts** (`Views/posts/`)
   - `list.html` - Danh sách bài đăng với filters sidebar và pagination
   - `detail.html` - Chi tiết bài đăng với image gallery, reviews, rating system
   - `create.html` - Form đăng tin với multi-step wizard và image preview

4. **User** (`Views/user/`)
   - `my-posts.html` - Quản lý bài đăng cá nhân
   - `favorites.html` - Danh sách phòng trọ yêu thích

5. **Chat** (`Views/chat/`)
   - `index.html` - Giao diện chat realtime với conversations sidebar

6. **Admin** (`Views/admin/`)
   - `dashboard.html` - Admin dashboard với statistics và approval queue

## Backend Architecture (Ready for Integration)

**Framework**: PHP với MVC pattern
- `Models/` - Chứa các file Model (trống, chờ tích hợp)
- `Controllers/` - Chứa các file Controller (trống, chờ tích hợp)

**Database Layer**: 
- MySQL database với UTF-8 encoding
- File `database.sql` chứa schema đầy đủ
- File `config.php` cung cấp database connection helper

**Authentication System** (chưa tích hợp):
- Role-based access control (Admin, Landlord, Tenant)
- Session-based authentication
- Password hashing với bcrypt

## Database Schema

**Core Tables**:
1. `users` - Thông tin người dùng với roles
2. `posts` - Bài đăng phòng trọ
3. `categories` - Danh mục loại phòng
4. `post_images` - Hình ảnh của bài đăng
5. `favorites` - Danh sách yêu thích
6. `reviews` - Đánh giá phòng trọ
7. `conversations` - Cuộc hội thoại chat
8. `messages` - Tin nhắn chi tiết
9. `notifications` - Hệ thống thông báo

**Sample Data**:
- 1 admin account
- 2 landlord accounts
- 2 tenant accounts
- 3 sample posts với categories

## File Upload System (Frontend Only)

**Image Preview Functionality**:
- JavaScript preview trước khi upload
- Support multiple images (max 10)
- Remove preview button
- Client-side validation

**Upload Directory Structure**:
```
uploads/
├── posts/      # Hình ảnh bài đăng
└── users/      # Avatar người dùng
```

## Assets Structure

**CSS** (`assets/css/style.css`):
- Global styles với CSS variables
- Responsive utilities
- Component styles (buttons, forms, cards, badges)
- Layout helpers (grid, flexbox)

**JavaScript** (`assets/js/main.js`):
- Image preview functionality
- Favorite toggle
- Rating system
- Form validation
- Notification system
- Search & filter utilities

# External Dependencies

## Frontend Libraries
- **Font Awesome 6.4.0**: Icon library (CDN)

## Development Server
- **Python 3 HTTP Server**: Development web server trên port 5000
- File `server.py` với cache-busting headers
- Root redirect tới `/Views/home/index.html`

## Planned Integrations (Chưa thực hiện)

**Backend**:
- PHP 7.4+ cho server-side logic
- MySQL/MariaDB cho database
- PDO cho database abstraction

**Chat Realtime**:
- WebSocket hoặc Socket.io
- PHP Ratchet library (đề xuất)

**File Upload**:
- PHP file handling với validation
- Image compression và optimization
- CDN integration cho production

# Configuration Files

**config.php**:
- Database connection settings
- Site configuration
- Upload settings và validation rules
- Helper functions (authentication, sanitization)

**server.py**:
- Python development server
- Port 5000 binding
- Cache-control headers
- Root path routing

**index.php**:
- Redirect tới Views/home/index.html
- Entry point cho production (khi tích hợp PHP)

# Development Workflow

## Current Setup (Static Frontend)
1. Server chạy trên port 5000
2. Truy cập: http://0.0.0.0:5000/
3. Tất cả pages hoạt động như static HTML
4. JavaScript chỉ handle client-side interactions

## Next Steps for PHP Integration

### 1. Setup Controllers
Tạo các controller files:
- `AuthController.php` - Login, Register, Logout
- `PostController.php` - CRUD operations cho posts
- `UserController.php` - User profile management
- `AdminController.php` - Admin functions
- `ChatController.php` - Chat messaging
- `FavoriteController.php` - Favorite operations

### 2. Setup Models
Tạo các model files:
- `UserModel.php` - User database operations
- `PostModel.php` - Post database operations
- `MessageModel.php` - Chat message operations
- `ReviewModel.php` - Review operations

### 3. Convert Views
- Rename .html files to .php
- Add PHP includes for header/footer
- Replace static data với dynamic PHP/MySQL queries
- Implement session management
- Add CSRF protection

### 4. Implement Features
- Upload handling với validation
- Email notifications
- Chat realtime với WebSocket
- Payment integration (nếu cần)

# Security Considerations (Chưa implement)

**Cần implement khi tích hợp PHP**:
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- CSRF tokens
- Password hashing (bcrypt)
- File upload validation
- Session security
- Input sanitization

# Performance Optimization (Suggestions)

**Frontend**:
- Image lazy loading
- CSS/JS minification cho production
- CDN cho static assets
- Browser caching

**Backend** (khi tích hợp):
- Database indexing (đã có trong schema)
- Query optimization
- Redis caching cho sessions
- Image optimization và compression

# Deployment Guide

## Development (Current)
```bash
python3 server.py
# Access: http://localhost:5000
```

## Production (Sau khi tích hợp PHP)
1. Upload files lên hosting
2. Import database.sql
3. Configure config.php với production credentials
4. Set proper file permissions (755 for directories, 644 for files)
5. Configure Apache/Nginx
6. Enable HTTPS với SSL certificate
7. Setup cron jobs cho maintenance tasks

---

**Status**: ✅ Frontend hoàn thiện, chờ tích hợp PHP backend
**Last Updated**: 10/11/2024
