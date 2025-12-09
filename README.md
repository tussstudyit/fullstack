# WEB TÌM TRỌ CHO SINH VIÊN

Hệ thống tìm kiếm và quản lý phòng trọ dành cho sinh viên với 3 vai trò: Admin, Người cho thuê, Người thuê.

## 🔧 Công nghệ sử dụng

### Backend
- **PHP 7.4+** - Server-side programming language
- **MySQL 5.7+** - Relational database management system
- **PDO (PHP Data Objects)** - Database abstraction layer
- **Composer** - Dependency manager for PHP
- **Ratchet** - WebSocket library for realtime chat
- **bcrypt** - Password hashing algorithm

### Frontend
- **HTML5** - Semantic markup language
- **CSS3** - Styling with modern features (Grid, Flexbox, Variables)
- **JavaScript (Vanilla ES6+)** - Client-side scripting
- **Font Awesome 6.4.0** - Icon library
- **Google Fonts** - Web typography (Dancing Script)

### Server & Infrastructure
- **Apache 2.4** - Web server with mod_rewrite
- **.htaccess** - URL rewriting and routing rules
- **WebSocket Server** - Ratchet on port 8080 for chat
- **Session Management** - PHP native sessions

### Database
- **MySQL 5.7+** / **MariaDB 10.2+**
- **PDO Prepared Statements** - SQL injection prevention
- **InnoDB Engine** - Transaction support
- **Foreign Keys** - Referential integrity
- **Indexes** - Query optimization

### Development Tools
- **Git** - Version control system
- **GitHub** - Code hosting and collaboration
- **VS Code** - Code editor (recommended)
- **phpMyAdmin** - Database administration
- **Composer** - Package management
- **XAMPP/WAMP** - Local development environment

### Architecture & Patterns
- **MVC (Model-View-Controller)** - Application architecture
  - **Models** (`Models/`) - Data layer & business logic
  - **Views** (`Views/`) - Presentation layer (PHP templates)
  - **Controllers** (`Controllers/`) - Request handling
- **RESTful API** - API endpoints design (`api/`)
- **Repository Pattern** - Database query abstraction
- **Session-based Authentication** - User authentication
- **RBAC** - Role-based access control

### Security
- **Password Hashing** - bcrypt with cost factor 10
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - htmlspecialchars() sanitization
- **CSRF Protection** - Session token validation
- **File Upload Validation** - Type, size, and extension checks
- **Input Sanitization** - Filter and validate user inputs

### Libraries & Dependencies (Composer)
```json
{
  "cboden/ratchet": "^0.4",           // WebSocket server
  "guzzlehttp/psr7": "^2.0",          // HTTP message interfaces
  "symfony/http-foundation": "^6.0",  // HTTP abstraction
  "symfony/routing": "^6.0"           // URL routing
}
```

### Browser Compatibility
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 📋 Tính năng

### ✅ Chức năng đã hoàn thành

#### 🔐 Xác thực & Phân quyền
- ✓ Đăng ký tài khoản (Người cho thuê, Người thuê, Admin)
- ✓ Đăng nhập với session management
- ✓ Phân quyền 3 vai trò: Admin, Landlord (Chủ trọ), Tenant (Người thuê)
- ✓ Auto-login sau khi đăng ký thành công
- ✓ Đăng xuất an toàn
- ✓ Quản lý avatar người dùng

#### 📝 Quản lý bài đăng (Posts)
- ✓ Tạo bài đăng cho thuê với mô tả chi tiết
- ✓ Upload nhiều hình ảnh cho một bài đăng (async upload)
- ✓ Cập nhật (edit) bài đăng đã tạo
- ✓ Xóa bài đăng (chỉ chủ sở hữu)
- ✓ Xem danh sách bài đăng của cá nhân (My Posts)
- ✓ Xem chi tiết bài đăng với full thông tin
- ✓ Like/Unlike bài đăng (post_likes)
- ✓ Thống kê lượt xem (views counter)

#### 🖼️ Xử lý ảnh
- ✓ Upload ảnh đơn lẫn multiple (drag-drop, click to upload)
- ✓ Preview ảnh trước khi upload
- ✓ Xóa ảnh khỏi bài đăng
- ✓ Lưu trữ tệp ảnh trong thư mục uploads
- ✓ Hiển thị ảnh theo thứ tự trong chi tiết bài đăng
- ✓ Upload và quản lý avatar người dùng
- ✓ Hiển thị avatar trong comments và navigation

#### ⭐ Bình luận & Phản hồi (Comments)
- ✓ Bình luận trên bài đăng
- ✓ Hệ thống phản hồi lồng nhau (nested replies) - không giới hạn độ sâu
- ✓ Bất kỳ người dùng đã đăng nhập đều có thể phản hồi bình luận khác
- ✓ Hiển thị thông tin tác giả bình luận (avatar, tên, vai trò)
- ✓ Xóa bình luận (chỉ tác giả)
- ✓ Xem thời gian bình luận
- ✓ Responsive nested UI với indentation rõ ràng
- ✓ Đánh giá sao (rating) cho bài đăng

#### 👍 Bình chọn bình luận (Comment Voting)
- ✓ Upvote/Downvote bình luận
- ✓ Theo dõi lịch sử bình chọn của từng người dùng
- ✓ Hiển thị tổng số upvote/downvote
- ✓ Highlight trạng thái bình chọn hiện tại của user
- ✓ Lưu trữ bình chọn trong database (comment_votes)

#### ❤️ Danh sách yêu thích (Favorites)
- ✓ Thêm/xóa bài đăng vào yêu thích
- ✓ Xem danh sách tất cả bài yêu thích
- ✓ Hiển thị icon/status yêu thích trên listing posts
- ✓ Đồng bộ trạng thái yêu thích trên tất cả trang (index, list, detail)
- ✓ Icon heart màu đỏ với animation

#### 👤 Quản lý hồ sơ (Profile)
- ✓ Xem thông tin cá nhân
- ✓ Cập nhật thông tin hồ sơ (tên, email, số điện thoại, địa chỉ, mô tả)
- ✓ Quản lý chi tiết tài khoản
- ✓ Upload và thay đổi avatar
- ✓ User dropdown menu với avatar

#### 🔔 Thông báo (Notifications)
- ✓ Hệ thống thông báo cho người dùng
- ✓ Xem danh sách thông báo
- ✓ Đánh dấu thông báo là đã đọc
- ✓ Notification bell với badge số lượng chưa đọc
- ✓ Dropdown thông báo realtime
- ✓ Các loại thông báo: comment, reply, rating, post_like, post_approved, post_rejected, message, system
- ✓ Icon phân biệt: thumbs-up cho like, heart cho favorite

#### 📊 Dashboard Admin
- ✓ Xem tổng số bài đăng, người dùng
- ✓ Quản lý danh sách bài đăng (view, delete spam posts)
- ✓ Quản lý danh sách người dùng
- ✓ Xem báo cáo/reports từ người dùng
- ✓ Thống kê tổng quan hệ thống

#### 🎨 Giao diện & UX
- ✓ Carousel hero section với gradient overlay cho text
- ✓ Responsive design cho mobile/tablet/desktop
- ✓ Color scheme nhất quán (red cho favorite, blue cho like, green cho contact)
- ✓ Navigation menu với avatar dropdown
- ✓ Notification dropdown với realtime updates
- ✓ Smooth animations và transitions

#### 📌 Tìm kiếm & Lọc
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

## 🏗️ Cấu trúc dự án

**Root Path:** `d:\baitapcuoiky\fullstack`

```
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
│   ├── 🐘 LikeController.php
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
│   ├── 🐘 chat.php
│   ├── 🐘 clear-login-errors.php
│   ├── 🐘 comments.php
│   └── 🐘 upload-image.php
├── 📁 assets
│   ├── 📁 css
│   │   └── 🎨 style.css
│   └── 📁 js
│       ├── 📄 main.js
│       └── 📄 notifications.js
├── 📁 attached_assets
│   └── 🐘 database_1762788624742.php
├── 📁 img
├── 📁 uploads
│   ├── 📁 avatars
│   │   ├── 🖼️ avatar_10_1765186834.png
│   │   ├── 🖼️ avatar_6_1764856466.png
│   │   └── 🖼️ avatar_7_1764857621.png
│   └── ⚙️ .gitignore
├── 📁 vendor
│   ├── 📁 cboden
│   │   └── 📁 ratchet
│   │       ├── 📁 .github
│   │       │   └── 📁 workflows
│   │       │       └── ⚙️ ci.yml
│   │       ├── 📁 src
│   │       │   └── 📁 Ratchet
│   │       │       ├── 📁 Http
│   │       │       │   ├── 🐘 CloseResponseTrait.php
│   │       │       │   ├── 🐘 HttpRequestParser.php
│   │       │       │   ├── 🐘 HttpServer.php
│   │       │       │   ├── 🐘 HttpServerInterface.php
│   │       │       │   ├── 🐘 NoOpHttpServerController.php
│   │       │       │   ├── 🐘 OriginCheck.php
│   │       │       │   └── 🐘 Router.php
│   │       │       ├── 📁 Server
│   │       │       │   ├── 🐘 EchoServer.php
│   │       │       │   ├── 🐘 FlashPolicy.php
│   │       │       │   ├── 🐘 IoConnection.php
│   │       │       │   ├── 🐘 IoServer.php
│   │       │       │   └── 🐘 IpBlackList.php
│   │       │       ├── 📁 Session
│   │       │       │   ├── 📁 Serialize
│   │       │       │   │   ├── 🐘 HandlerInterface.php
│   │       │       │   │   ├── 🐘 PhpBinaryHandler.php
│   │       │       │   │   └── 🐘 PhpHandler.php
│   │       │       │   ├── 📁 Storage
│   │       │       │   │   ├── 📁 Proxy
│   │       │       │   │   │   └── 🐘 VirtualProxy.php
│   │       │       │   │   └── 🐘 VirtualSessionStorage.php
│   │       │       │   └── 🐘 SessionProvider.php
│   │       │       ├── 📁 Wamp
│   │       │       │   ├── 🐘 Exception.php
│   │       │       │   ├── 🐘 JsonException.php
│   │       │       │   ├── 🐘 ServerProtocol.php
│   │       │       │   ├── 🐘 Topic.php
│   │       │       │   ├── 🐘 TopicManager.php
│   │       │       │   ├── 🐘 WampConnection.php
│   │       │       │   ├── 🐘 WampServer.php
│   │       │       │   └── 🐘 WampServerInterface.php
│   │       │       ├── 📁 WebSocket
│   │       │       │   ├── 🐘 ConnContext.php
│   │       │       │   ├── 🐘 MessageCallableInterface.php
│   │       │       │   ├── 🐘 MessageComponentInterface.php
│   │       │       │   ├── 🐘 WsConnection.php
│   │       │       │   ├── 🐘 WsServer.php
│   │       │       │   └── 🐘 WsServerInterface.php
│   │       │       ├── 🐘 AbstractConnectionDecorator.php
│   │       │       ├── 🐘 App.php
│   │       │       ├── 🐘 ComponentInterface.php
│   │       │       ├── 🐘 ConnectionInterface.php
│   │       │       ├── 🐘 MessageComponentInterface.php
│   │       │       └── 🐘 MessageInterface.php
│   │       ├── 📁 tests
│   │       │   ├── 📁 autobahn
│   │       │   │   ├── ⚙️ fuzzingclient-all.json
│   │       │   │   ├── ⚙️ fuzzingclient-profile.json
│   │       │   │   └── ⚙️ fuzzingclient-quick.json
│   │       │   ├── 📁 helpers
│   │       │   │   └── 📁 Ratchet
│   │       │   │       ├── 📁 Mock
│   │       │   │       │   ├── 🐘 Component.php
│   │       │   │       │   ├── 🐘 Connection.php
│   │       │   │       │   ├── 🐘 ConnectionDecorator.php
│   │       │   │       │   └── 🐘 WampComponent.php
│   │       │   │       ├── 📁 Wamp
│   │       │   │       │   └── 📁 Stub
│   │       │   │       │       └── 🐘 WsWampServerInterface.php
│   │       │   │       ├── 📁 WebSocket
│   │       │   │       │   └── 📁 Stub
│   │       │   │       │       └── 🐘 WsMessageComponentInterface.php
│   │       │   │       ├── 🐘 AbstractMessageComponentTestCase.php
│   │       │   │       └── 🐘 NullComponent.php
│   │       │   ├── 📁 unit
│   │       │   │   ├── 📁 Http
│   │       │   │   │   ├── 🐘 HttpRequestParserTest.php
│   │       │   │   │   ├── 🐘 HttpServerTest.php
│   │       │   │   │   ├── 🐘 OriginCheckTest.php
│   │       │   │   │   └── 🐘 RouterTest.php
│   │       │   │   ├── 📁 Server
│   │       │   │   │   ├── 🐘 EchoServerTest.php
│   │       │   │   │   ├── 🐘 FlashPolicyComponentTest.php
│   │       │   │   │   ├── 🐘 IoConnectionTest.php
│   │       │   │   │   ├── 🐘 IoServerTest.php
│   │       │   │   │   └── 🐘 IpBlackListComponentTest.php
│   │       │   │   ├── 📁 Session
│   │       │   │   │   ├── 📁 Serialize
│   │       │   │   │   │   └── 🐘 PhpHandlerTest.php
│   │       │   │   │   ├── 📁 Storage
│   │       │   │   │   │   └── 🐘 VirtualSessionStoragePDOTest.php
│   │       │   │   │   └── 🐘 SessionComponentTest.php
│   │       │   │   ├── 📁 Wamp
│   │       │   │   │   ├── 🐘 ServerProtocolTest.php
│   │       │   │   │   ├── 🐘 TopicManagerTest.php
│   │       │   │   │   ├── 🐘 TopicTest.php
│   │       │   │   │   ├── 🐘 WampConnectionTest.php
│   │       │   │   │   └── 🐘 WampServerTest.php
│   │       │   │   └── 🐘 AbstractConnectionDecoratorTest.php
│   │       │   └── 🐘 bootstrap.php
│   │       ├── ⚙️ .gitignore
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 📄 LICENSE
│   │       ├── 📄 Makefile
│   │       ├── 📝 README.md
│   │       ├── 📝 SECURITY.md
│   │       ├── ⚙️ composer.json
│   │       └── 📄 phpunit.xml.dist
│   ├── 📁 composer
│   │   ├── 🐘 ClassLoader.php
│   │   ├── 🐘 InstalledVersions.php
│   │   ├── 📄 LICENSE
│   │   ├── 🐘 autoload_classmap.php
│   │   ├── 🐘 autoload_files.php
│   │   ├── 🐘 autoload_namespaces.php
│   │   ├── 🐘 autoload_psr4.php
│   │   ├── 🐘 autoload_real.php
│   │   ├── 🐘 autoload_static.php
│   │   ├── ⚙️ installed.json
│   │   ├── 🐘 installed.php
│   │   └── 🐘 platform_check.php
│   ├── 📁 evenement
│   │   └── 📁 evenement
│   │       ├── 📁 .github
│   │       │   └── 📁 workflows
│   │       │       └── ⚙️ ci.yaml
│   │       ├── 📁 doc
│   │       │   ├── 📝 00-intro.md
│   │       │   ├── 📝 01-api.md
│   │       │   └── 📝 02-plugin-system.md
│   │       ├── 📁 examples
│   │       │   ├── 🐘 benchmark-emit-no-arguments.php
│   │       │   ├── 🐘 benchmark-emit-once.php
│   │       │   ├── 🐘 benchmark-emit-one-argument.php
│   │       │   ├── 🐘 benchmark-emit.php
│   │       │   └── 🐘 benchmark-remove-listener-once.php
│   │       ├── 📁 src
│   │       │   ├── 🐘 EventEmitter.php
│   │       │   ├── 🐘 EventEmitterInterface.php
│   │       │   └── 🐘 EventEmitterTrait.php
│   │       ├── 📁 tests
│   │       │   ├── 🐘 EventEmitterTest.php
│   │       │   ├── 🐘 Listener.php
│   │       │   └── 🐘 functions.php
│   │       ├── ⚙️ .gitattributes
│   │       ├── ⚙️ .gitignore
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       ├── ⚙️ composer.json
│   │       └── 📄 phpunit.xml.dist
│   ├── 📁 guzzlehttp
│   │   └── 📁 psr7
│   │       ├── 📁 .github
│   │       │   ├── 📁 workflows
│   │       │   │   ├── ⚙️ checks.yml
│   │       │   │   ├── ⚙️ ci.yml
│   │       │   │   ├── ⚙️ integration.yml
│   │       │   │   └── ⚙️ static.yml
│   │       │   ├── ⚙️ .editorconfig
│   │       │   ├── ⚙️ FUNDING.yml
│   │       │   └── ⚙️ stale.yml
│   │       ├── 📁 src
│   │       │   ├── 📁 Exception
│   │       │   │   └── 🐘 MalformedUriException.php
│   │       │   ├── 🐘 AppendStream.php
│   │       │   ├── 🐘 BufferStream.php
│   │       │   ├── 🐘 CachingStream.php
│   │       │   ├── 🐘 DroppingStream.php
│   │       │   ├── 🐘 FnStream.php
│   │       │   ├── 🐘 Header.php
│   │       │   ├── 🐘 HttpFactory.php
│   │       │   ├── 🐘 InflateStream.php
│   │       │   ├── 🐘 LazyOpenStream.php
│   │       │   ├── 🐘 LimitStream.php
│   │       │   ├── 🐘 Message.php
│   │       │   ├── 🐘 MessageTrait.php
│   │       │   ├── 🐘 MimeType.php
│   │       │   ├── 🐘 MultipartStream.php
│   │       │   ├── 🐘 NoSeekStream.php
│   │       │   ├── 🐘 PumpStream.php
│   │       │   ├── 🐘 Query.php
│   │       │   ├── 🐘 Request.php
│   │       │   ├── 🐘 Response.php
│   │       │   ├── 🐘 Rfc7230.php
│   │       │   ├── 🐘 ServerRequest.php
│   │       │   ├── 🐘 Stream.php
│   │       │   ├── 🐘 StreamDecoratorTrait.php
│   │       │   ├── 🐘 StreamWrapper.php
│   │       │   ├── 🐘 UploadedFile.php
│   │       │   ├── 🐘 Uri.php
│   │       │   ├── 🐘 UriComparator.php
│   │       │   ├── 🐘 UriNormalizer.php
│   │       │   ├── 🐘 UriResolver.php
│   │       │   └── 🐘 Utils.php
│   │       ├── 📁 tests
│   │       │   ├── 📁 Integration
│   │       │   │   ├── 🐘 ServerRequestFromGlobalsTest.php
│   │       │   │   └── 🐘 server.php
│   │       │   ├── 🐘 AppendStreamTest.php
│   │       │   ├── 🐘 BufferStreamTest.php
│   │       │   ├── 🐘 CachingStreamTest.php
│   │       │   ├── 🐘 DroppingStreamTest.php
│   │       │   ├── 🐘 FnStreamTest.php
│   │       │   ├── 🐘 HasToString.php
│   │       │   ├── 🐘 HeaderTest.php
│   │       │   ├── 🐘 InflateStreamTest.php
│   │       │   ├── 🐘 LazyOpenStreamTest.php
│   │       │   ├── 🐘 LimitStreamTest.php
│   │       │   ├── 🐘 MessageTest.php
│   │       │   ├── 🐘 MimeTypeTest.php
│   │       │   ├── 🐘 MultipartStreamTest.php
│   │       │   ├── 🐘 NoSeekStreamTest.php
│   │       │   ├── 🐘 PumpStreamTest.php
│   │       │   ├── 🐘 QueryTest.php
│   │       │   ├── 🐘 ReadSeekOnlyStream.php
│   │       │   ├── 🐘 RequestTest.php
│   │       │   ├── 🐘 ResponseTest.php
│   │       │   ├── 🐘 ServerRequestTest.php
│   │       │   ├── 🐘 StreamDecoratorTraitTest.php
│   │       │   ├── 🐘 StreamTest.php
│   │       │   ├── 🐘 StreamWrapperTest.php
│   │       │   ├── 🐘 UploadedFileTest.php
│   │       │   ├── 🐘 UriComparatorTest.php
│   │       │   ├── 🐘 UriNormalizerTest.php
│   │       │   ├── 🐘 UriResolverTest.php
│   │       │   ├── 🐘 UriTest.php
│   │       │   └── 🐘 UtilsTest.php
│   │       ├── 📁 vendor-bin
│   │       │   ├── 📁 php-cs-fixer
│   │       │   │   └── ⚙️ composer.json
│   │       │   ├── 📁 phpstan
│   │       │   │   └── ⚙️ composer.json
│   │       │   └── 📁 psalm
│   │       │       └── ⚙️ composer.json
│   │       ├── ⚙️ .editorconfig
│   │       ├── ⚙️ .gitattributes
│   │       ├── ⚙️ .gitignore
│   │       ├── 🐘 .php-cs-fixer.dist.php
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 📄 LICENSE
│   │       ├── 📄 Makefile
│   │       ├── 📝 README.md
│   │       ├── ⚙️ composer.json
│   │       ├── 📄 phpstan-baseline.neon
│   │       ├── 📄 phpstan.neon.dist
│   │       ├── 📄 phpunit.xml.dist
│   │       ├── ⚙️ psalm-baseline.xml
│   │       └── ⚙️ psalm.xml
│   ├── 📁 psr
│   │   ├── 📁 http-factory
│   │   │   ├── 📁 src
│   │   │   │   ├── 🐘 RequestFactoryInterface.php
│   │   │   │   ├── 🐘 ResponseFactoryInterface.php
│   │   │   │   ├── 🐘 ServerRequestFactoryInterface.php
│   │   │   │   ├── 🐘 StreamFactoryInterface.php
│   │   │   │   ├── 🐘 UploadedFileFactoryInterface.php
│   │   │   │   └── 🐘 UriFactoryInterface.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── ⚙️ .pullapprove.yml
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   └── ⚙️ composer.json
│   │   └── 📁 http-message
│   │       ├── 📁 docs
│   │       │   ├── 📝 PSR7-Interfaces.md
│   │       │   └── 📝 PSR7-Usage.md
│   │       ├── 📁 src
│   │       │   ├── 🐘 MessageInterface.php
│   │       │   ├── 🐘 RequestInterface.php
│   │       │   ├── 🐘 ResponseInterface.php
│   │       │   ├── 🐘 ServerRequestInterface.php
│   │       │   ├── 🐘 StreamInterface.php
│   │       │   ├── 🐘 UploadedFileInterface.php
│   │       │   └── 🐘 UriInterface.php
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       └── ⚙️ composer.json
│   ├── 📁 ralouphie
│   │   └── 📁 getallheaders
│   │       ├── 📁 src
│   │       │   └── 🐘 getallheaders.php
│   │       ├── 📁 tests
│   │       │   └── 🐘 GetAllHeadersTest.php
│   │       ├── ⚙️ .gitattributes
│   │       ├── ⚙️ .gitignore
│   │       ├── ⚙️ .travis.yml
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       ├── ⚙️ composer.json
│   │       └── ⚙️ phpunit.xml
│   ├── 📁 ratchet
│   │   └── 📁 rfc6455
│   │       ├── 📁 .github
│   │       │   └── 📁 workflows
│   │       │       └── ⚙️ ci.yml
│   │       ├── 📁 src
│   │       │   ├── 📁 Handshake
│   │       │   │   ├── 🐘 ClientNegotiator.php
│   │       │   │   ├── 🐘 InvalidPermessageDeflateOptionsException.php
│   │       │   │   ├── 🐘 NegotiatorInterface.php
│   │       │   │   ├── 🐘 PermessageDeflateOptions.php
│   │       │   │   ├── 🐘 RequestVerifier.php
│   │       │   │   ├── 🐘 ResponseVerifier.php
│   │       │   │   └── 🐘 ServerNegotiator.php
│   │       │   └── 📁 Messaging
│   │       │       ├── 🐘 CloseFrameChecker.php
│   │       │       ├── 🐘 DataInterface.php
│   │       │       ├── 🐘 Frame.php
│   │       │       ├── 🐘 FrameInterface.php
│   │       │       ├── 🐘 Message.php
│   │       │       ├── 🐘 MessageBuffer.php
│   │       │       └── 🐘 MessageInterface.php
│   │       ├── 📁 tests
│   │       │   ├── 📁 ab
│   │       │   │   ├── 🐘 clientRunner.php
│   │       │   │   ├── 📄 docker_bootstrap.sh
│   │       │   │   ├── ⚙️ fuzzingclient.json
│   │       │   │   ├── ⚙️ fuzzingclient_skip_deflate.json
│   │       │   │   ├── ⚙️ fuzzingserver.json
│   │       │   │   ├── ⚙️ fuzzingserver_skip_deflate.json
│   │       │   │   ├── 📄 run_ab_tests.sh
│   │       │   │   └── 🐘 startServer.php
│   │       │   ├── 📁 unit
│   │       │   │   ├── 📁 Handshake
│   │       │   │   │   ├── 🐘 PermessageDeflateOptionsTest.php
│   │       │   │   │   ├── 🐘 RequestVerifierTest.php
│   │       │   │   │   ├── 🐘 ResponseVerifierTest.php
│   │       │   │   │   └── 🐘 ServerNegotiatorTest.php
│   │       │   │   └── 📁 Messaging
│   │       │   │       ├── 🐘 FrameTest.php
│   │       │   │       ├── 🐘 MessageBufferTest.php
│   │       │   │       └── 🐘 MessageTest.php
│   │       │   ├── 🐘 AbResultsTest.php
│   │       │   └── 🐘 bootstrap.php
│   │       ├── ⚙️ .gitignore
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       ├── ⚙️ composer.json
│   │       └── 📄 phpunit.xml.dist
│   ├── 📁 react
│   │   ├── 📁 dns
│   │   │   ├── 📁 .github
│   │   │   │   └── 📁 workflows
│   │   │   │       └── ⚙️ ci.yml
│   │   │   ├── 📁 examples
│   │   │   │   ├── 🐘 01-one.php
│   │   │   │   ├── 🐘 02-concurrent.php
│   │   │   │   ├── 🐘 03-cached.php
│   │   │   │   ├── 🐘 11-all-ips.php
│   │   │   │   ├── 🐘 12-all-types.php
│   │   │   │   ├── 🐘 13-reverse-dns.php
│   │   │   │   ├── 🐘 91-query-a-and-aaaa.php
│   │   │   │   └── 🐘 92-query-any.php
│   │   │   ├── 📁 src
│   │   │   │   ├── 📁 Config
│   │   │   │   │   ├── 🐘 Config.php
│   │   │   │   │   └── 🐘 HostsFile.php
│   │   │   │   ├── 📁 Model
│   │   │   │   │   ├── 🐘 Message.php
│   │   │   │   │   └── 🐘 Record.php
│   │   │   │   ├── 📁 Protocol
│   │   │   │   │   ├── 🐘 BinaryDumper.php
│   │   │   │   │   └── 🐘 Parser.php
│   │   │   │   ├── 📁 Query
│   │   │   │   │   ├── 🐘 CachingExecutor.php
│   │   │   │   │   ├── 🐘 CancellationException.php
│   │   │   │   │   ├── 🐘 CoopExecutor.php
│   │   │   │   │   ├── 🐘 ExecutorInterface.php
│   │   │   │   │   ├── 🐘 FallbackExecutor.php
│   │   │   │   │   ├── 🐘 HostsFileExecutor.php
│   │   │   │   │   ├── 🐘 Query.php
│   │   │   │   │   ├── 🐘 RetryExecutor.php
│   │   │   │   │   ├── 🐘 SelectiveTransportExecutor.php
│   │   │   │   │   ├── 🐘 TcpTransportExecutor.php
│   │   │   │   │   ├── 🐘 TimeoutException.php
│   │   │   │   │   ├── 🐘 TimeoutExecutor.php
│   │   │   │   │   └── 🐘 UdpTransportExecutor.php
│   │   │   │   ├── 📁 Resolver
│   │   │   │   │   ├── 🐘 Factory.php
│   │   │   │   │   ├── 🐘 Resolver.php
│   │   │   │   │   └── 🐘 ResolverInterface.php
│   │   │   │   ├── 🐘 BadServerException.php
│   │   │   │   └── 🐘 RecordNotFoundException.php
│   │   │   ├── 📁 tests
│   │   │   │   ├── 📁 Config
│   │   │   │   │   ├── 🐘 ConfigTest.php
│   │   │   │   │   └── 🐘 HostsFileTest.php
│   │   │   │   ├── 📁 Fixtures
│   │   │   │   │   └── 📁 etc
│   │   │   │   │       └── ⚙️ resolv.conf
│   │   │   │   ├── 📁 Model
│   │   │   │   │   └── 🐘 MessageTest.php
│   │   │   │   ├── 📁 Protocol
│   │   │   │   │   ├── 🐘 BinaryDumperTest.php
│   │   │   │   │   └── 🐘 ParserTest.php
│   │   │   │   ├── 📁 Query
│   │   │   │   │   ├── 🐘 CachingExecutorTest.php
│   │   │   │   │   ├── 🐘 CoopExecutorTest.php
│   │   │   │   │   ├── 🐘 FallbackExecutorTest.php
│   │   │   │   │   ├── 🐘 HostsFileExecutorTest.php
│   │   │   │   │   ├── 🐘 QueryTest.php
│   │   │   │   │   ├── 🐘 RetryExecutorTest.php
│   │   │   │   │   ├── 🐘 SelectiveTransportExecutorTest.php
│   │   │   │   │   ├── 🐘 TcpTransportExecutorTest.php
│   │   │   │   │   ├── 🐘 TimeoutExecutorTest.php
│   │   │   │   │   └── 🐘 UdpTransportExecutorTest.php
│   │   │   │   ├── 📁 Resolver
│   │   │   │   │   ├── 🐘 FactoryTest.php
│   │   │   │   │   ├── 🐘 ResolveAliasesTest.php
│   │   │   │   │   └── 🐘 ResolverTest.php
│   │   │   │   ├── 🐘 FunctionalResolverTest.php
│   │   │   │   └── 🐘 TestCase.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   ├── ⚙️ composer.json
│   │   │   ├── 📄 phpunit.xml.dist
│   │   │   └── 📄 phpunit.xml.legacy
│   │   ├── 📁 event-loop
│   │   │   ├── 📁 .github
│   │   │   │   └── 📁 workflows
│   │   │   │       └── ⚙️ ci.yml
│   │   │   ├── 📁 examples
│   │   │   │   ├── 🐘 01-timers.php
│   │   │   │   ├── 🐘 02-periodic.php
│   │   │   │   ├── 🐘 03-ticks.php
│   │   │   │   ├── 🐘 04-signals.php
│   │   │   │   ├── 🐘 11-consume-stdin.php
│   │   │   │   ├── 🐘 12-generate-yes.php
│   │   │   │   ├── 🐘 13-http-client-blocking.php
│   │   │   │   ├── 🐘 14-http-client-async.php
│   │   │   │   ├── 🐘 21-http-server.php
│   │   │   │   ├── 🐘 91-benchmark-ticks.php
│   │   │   │   ├── 🐘 92-benchmark-timers.php
│   │   │   │   ├── 🐘 93-benchmark-ticks-delay.php
│   │   │   │   ├── 🐘 94-benchmark-timers-delay.php
│   │   │   │   └── 🐘 95-benchmark-memory.php
│   │   │   ├── 📁 src
│   │   │   │   ├── 📁 Tick
│   │   │   │   │   └── 🐘 FutureTickQueue.php
│   │   │   │   ├── 📁 Timer
│   │   │   │   │   ├── 🐘 Timer.php
│   │   │   │   │   └── 🐘 Timers.php
│   │   │   │   ├── 🐘 ExtEvLoop.php
│   │   │   │   ├── 🐘 ExtEventLoop.php
│   │   │   │   ├── 🐘 ExtLibevLoop.php
│   │   │   │   ├── 🐘 ExtLibeventLoop.php
│   │   │   │   ├── 🐘 ExtUvLoop.php
│   │   │   │   ├── 🐘 Factory.php
│   │   │   │   ├── 🐘 Loop.php
│   │   │   │   ├── 🐘 LoopInterface.php
│   │   │   │   ├── 🐘 SignalsHandler.php
│   │   │   │   ├── 🐘 StreamSelectLoop.php
│   │   │   │   └── 🐘 TimerInterface.php
│   │   │   ├── 📁 tests
│   │   │   │   ├── 📁 Timer
│   │   │   │   │   ├── 🐘 AbstractTimerTest.php
│   │   │   │   │   ├── 🐘 ExtEvTimerTest.php
│   │   │   │   │   ├── 🐘 ExtEventTimerTest.php
│   │   │   │   │   ├── 🐘 ExtLibevTimerTest.php
│   │   │   │   │   ├── 🐘 ExtLibeventTimerTest.php
│   │   │   │   │   ├── 🐘 ExtUvTimerTest.php
│   │   │   │   │   ├── 🐘 StreamSelectTimerTest.php
│   │   │   │   │   └── 🐘 TimersTest.php
│   │   │   │   ├── 🐘 AbstractLoopTest.php
│   │   │   │   ├── 🐘 BinTest.php
│   │   │   │   ├── 🐘 ExtEvLoopTest.php
│   │   │   │   ├── 🐘 ExtEventLoopTest.php
│   │   │   │   ├── 🐘 ExtLibevLoopTest.php
│   │   │   │   ├── 🐘 ExtLibeventLoopTest.php
│   │   │   │   ├── 🐘 ExtUvLoopTest.php
│   │   │   │   ├── 🐘 LoopTest.php
│   │   │   │   ├── 🐘 SignalsHandlerTest.php
│   │   │   │   ├── 🐘 StreamSelectLoopTest.php
│   │   │   │   └── 🐘 TestCase.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   ├── ⚙️ composer.json
│   │   │   ├── 📄 phpunit.xml.dist
│   │   │   └── 📄 phpunit.xml.legacy
│   │   ├── 📁 promise
│   │   │   ├── 📁 .github
│   │   │   │   └── 📁 workflows
│   │   │   │       └── ⚙️ ci.yml
│   │   │   ├── 📁 src
│   │   │   │   ├── 📁 Exception
│   │   │   │   │   ├── 🐘 CompositeException.php
│   │   │   │   │   └── 🐘 LengthException.php
│   │   │   │   ├── 📁 Internal
│   │   │   │   │   ├── 🐘 CancellationQueue.php
│   │   │   │   │   ├── 🐘 FulfilledPromise.php
│   │   │   │   │   └── 🐘 RejectedPromise.php
│   │   │   │   ├── 🐘 Deferred.php
│   │   │   │   ├── 🐘 Promise.php
│   │   │   │   ├── 🐘 PromiseInterface.php
│   │   │   │   ├── 🐘 functions.php
│   │   │   │   └── 🐘 functions_include.php
│   │   │   ├── 📁 tests
│   │   │   │   ├── 📁 Internal
│   │   │   │   │   ├── 🐘 CancellationQueueTest.php
│   │   │   │   │   ├── 🐘 FulfilledPromiseTest.php
│   │   │   │   │   └── 🐘 RejectedPromiseTest.php
│   │   │   │   ├── 📁 PromiseAdapter
│   │   │   │   │   ├── 🐘 CallbackPromiseAdapter.php
│   │   │   │   │   └── 🐘 PromiseAdapterInterface.php
│   │   │   │   ├── 📁 PromiseTest
│   │   │   │   │   ├── 🐘 CancelTestTrait.php
│   │   │   │   │   ├── 🐘 FullTestTrait.php
│   │   │   │   │   ├── 🐘 PromiseFulfilledTestTrait.php
│   │   │   │   │   ├── 🐘 PromisePendingTestTrait.php
│   │   │   │   │   ├── 🐘 PromiseRejectedTestTrait.php
│   │   │   │   │   ├── 🐘 PromiseSettledTestTrait.php
│   │   │   │   │   ├── 🐘 RejectTestTrait.php
│   │   │   │   │   └── 🐘 ResolveTestTrait.php
│   │   │   │   ├── 📁 fixtures
│   │   │   │   │   ├── 🐘 CallbackWithDNFTypehintClass.php
│   │   │   │   │   ├── 🐘 CallbackWithIntersectionTypehintClass.php
│   │   │   │   │   ├── 🐘 CallbackWithTypehintClass.php
│   │   │   │   │   ├── 🐘 CallbackWithUnionTypehintClass.php
│   │   │   │   │   ├── 🐘 CallbackWithoutTypehintClass.php
│   │   │   │   │   ├── 🐘 CountableException.php
│   │   │   │   │   ├── 🐘 IterableException.php
│   │   │   │   │   ├── 🐘 SimpleFulfilledTestThenable.php
│   │   │   │   │   ├── 🐘 SimpleTestCancellable.php
│   │   │   │   │   └── 🐘 SimpleTestCancellableThenable.php
│   │   │   │   ├── 📁 types
│   │   │   │   │   ├── 🐘 all.php
│   │   │   │   │   ├── 🐘 any.php
│   │   │   │   │   ├── 🐘 deferred.php
│   │   │   │   │   ├── 🐘 promise.php
│   │   │   │   │   ├── 🐘 race.php
│   │   │   │   │   ├── 🐘 reject.php
│   │   │   │   │   └── 🐘 resolve.php
│   │   │   │   ├── 🐘 DeferredTest.php
│   │   │   │   ├── 📄 DeferredTestCancelNoopThenRejectShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 DeferredTestCancelThatRejectsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 DeferredTestRejectShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 DeferredTestRejectThenCancelShouldNotReportUnhandled.phpt
│   │   │   │   ├── 🐘 Fiber.php
│   │   │   │   ├── 🐘 FunctionAllTest.php
│   │   │   │   ├── 📄 FunctionAllTestRejectedShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionAllTestRejectedThenMatchingThatReturnsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 🐘 FunctionAnyTest.php
│   │   │   │   ├── 📄 FunctionAnyTestRejectedShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionAnyTestRejectedThenMatchingThatReturnsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 🐘 FunctionCheckTypehintTest.php
│   │   │   │   ├── 🐘 FunctionRaceTest.php
│   │   │   │   ├── 📄 FunctionRaceTestRejectedShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRaceTestRejectedThenMatchingThatReturnsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 🐘 FunctionRejectTest.php
│   │   │   │   ├── 📄 FunctionRejectTestCancelShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestCatchMatchingShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestCatchMismatchShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestFinallyThatReturnsShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestFinallyThatThrowsNewExceptionShouldReportUnhandledForNewExceptionOnly.phpt
│   │   │   │   ├── 📄 FunctionRejectTestShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestShouldReportUnhandledWithPreviousExceptions.phpt
│   │   │   │   ├── 📄 FunctionRejectTestThenMatchingThatReturnsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionRejectTestThenMatchingThatThrowsNewExceptionShouldReportUnhandledRejectionForNewExceptionOnly.phpt
│   │   │   │   ├── 📄 FunctionRejectTestThenMismatchThrowsTypeErrorAndShouldReportUnhandledForTypeErrorOnlyOnPhp7.phpt
│   │   │   │   ├── 📄 FunctionRejectTestThenMismatchThrowsTypeErrorAndShouldReportUnhandledForTypeErrorOnlyOnPhp8.phpt
│   │   │   │   ├── 🐘 FunctionResolveTest.php
│   │   │   │   ├── 📄 FunctionResolveTestThenShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerShouldBeInvokedForUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerShouldInvokeLastHandlerForUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatHasUnhandledShouldReportUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatThrowsShouldTerminateProgramForUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatThrowsShouldTerminateProgramForUnhandledWithPreviousExceptions.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatTriggersDefaultHandlerShouldTerminateProgramForUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatTriggersErrorHandlerThatThrowsShouldTerminateProgramForUnhandled.phpt
│   │   │   │   ├── 📄 FunctionSetRejectionHandlerThatUsesNestedSetRejectionHandlerShouldInvokeInnerHandlerForUnhandled.phpt
│   │   │   │   ├── 🐘 PHP8.php
│   │   │   │   ├── 🐘 PromiseTest.php
│   │   │   │   ├── 📄 PromiseTestCancelThatRejectsAfterwardsShouldNotReportUnhandled.phpt
│   │   │   │   ├── 📄 PromiseTestCancelThatRejectsShouldNotReportUnhandled.phpt
│   │   │   │   └── 🐘 TestCase.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   ├── ⚙️ composer.json
│   │   │   ├── 📄 phpstan.neon.dist
│   │   │   ├── 📄 phpunit.xml.dist
│   │   │   └── 📄 phpunit.xml.legacy
│   │   ├── 📁 socket
│   │   │   ├── 📁 .github
│   │   │   │   └── 📁 workflows
│   │   │   │       └── ⚙️ ci.yml
│   │   │   ├── 📁 examples
│   │   │   │   ├── 🐘 01-echo-server.php
│   │   │   │   ├── 🐘 02-chat-server.php
│   │   │   │   ├── 🐘 03-http-server.php
│   │   │   │   ├── 🐘 11-http-client.php
│   │   │   │   ├── 🐘 12-https-client.php
│   │   │   │   ├── 🐘 21-netcat-client.php
│   │   │   │   ├── 🐘 22-http-client.php
│   │   │   │   ├── 🐘 91-benchmark-server.php
│   │   │   │   ├── 🐘 99-generate-self-signed.php
│   │   │   │   ├── 📄 localhost.pem
│   │   │   │   └── 📄 localhost_swordfish.pem
│   │   │   ├── 📁 src
│   │   │   │   ├── 🐘 Connection.php
│   │   │   │   ├── 🐘 ConnectionInterface.php
│   │   │   │   ├── 🐘 Connector.php
│   │   │   │   ├── 🐘 ConnectorInterface.php
│   │   │   │   ├── 🐘 DnsConnector.php
│   │   │   │   ├── 🐘 FdServer.php
│   │   │   │   ├── 🐘 FixedUriConnector.php
│   │   │   │   ├── 🐘 HappyEyeBallsConnectionBuilder.php
│   │   │   │   ├── 🐘 HappyEyeBallsConnector.php
│   │   │   │   ├── 🐘 LimitingServer.php
│   │   │   │   ├── 🐘 SecureConnector.php
│   │   │   │   ├── 🐘 SecureServer.php
│   │   │   │   ├── 🐘 Server.php
│   │   │   │   ├── 🐘 ServerInterface.php
│   │   │   │   ├── 🐘 SocketServer.php
│   │   │   │   ├── 🐘 StreamEncryption.php
│   │   │   │   ├── 🐘 TcpConnector.php
│   │   │   │   ├── 🐘 TcpServer.php
│   │   │   │   ├── 🐘 TimeoutConnector.php
│   │   │   │   ├── 🐘 UnixConnector.php
│   │   │   │   └── 🐘 UnixServer.php
│   │   │   ├── 📁 tests
│   │   │   │   ├── 📁 Stub
│   │   │   │   │   ├── 🐘 CallableStub.php
│   │   │   │   │   ├── 🐘 ConnectionStub.php
│   │   │   │   │   └── 🐘 ServerStub.php
│   │   │   │   ├── 🐘 ConnectionTest.php
│   │   │   │   ├── 🐘 ConnectorTest.php
│   │   │   │   ├── 🐘 DnsConnectorTest.php
│   │   │   │   ├── 🐘 FdServerTest.php
│   │   │   │   ├── 🐘 FixedUriConnectorTest.php
│   │   │   │   ├── 🐘 FunctionalConnectorTest.php
│   │   │   │   ├── 🐘 FunctionalSecureServerTest.php
│   │   │   │   ├── 🐘 FunctionalTcpServerTest.php
│   │   │   │   ├── 🐘 HappyEyeBallsConnectionBuilderTest.php
│   │   │   │   ├── 🐘 HappyEyeBallsConnectorTest.php
│   │   │   │   ├── 🐘 IntegrationTest.php
│   │   │   │   ├── 🐘 LimitingServerTest.php
│   │   │   │   ├── 🐘 SecureConnectorTest.php
│   │   │   │   ├── 🐘 SecureIntegrationTest.php
│   │   │   │   ├── 🐘 SecureServerTest.php
│   │   │   │   ├── 🐘 ServerTest.php
│   │   │   │   ├── 🐘 SocketServerTest.php
│   │   │   │   ├── 🐘 TcpConnectorTest.php
│   │   │   │   ├── 🐘 TcpServerTest.php
│   │   │   │   ├── 🐘 TestCase.php
│   │   │   │   ├── 🐘 TimeoutConnectorTest.php
│   │   │   │   ├── 🐘 TimerSpeedUpEventLoop.php
│   │   │   │   ├── 🐘 UnixConnectorTest.php
│   │   │   │   └── 🐘 UnixServerTest.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   ├── ⚙️ composer.json
│   │   │   ├── 📄 phpunit.xml.dist
│   │   │   └── 📄 phpunit.xml.legacy
│   │   └── 📁 stream
│   │       ├── 📁 .github
│   │       │   └── 📁 workflows
│   │       │       └── ⚙️ ci.yml
│   │       ├── 📁 examples
│   │       │   ├── 🐘 01-http.php
│   │       │   ├── 🐘 02-https.php
│   │       │   ├── 🐘 11-cat.php
│   │       │   └── 🐘 91-benchmark-throughput.php
│   │       ├── 📁 src
│   │       │   ├── 🐘 CompositeStream.php
│   │       │   ├── 🐘 DuplexResourceStream.php
│   │       │   ├── 🐘 DuplexStreamInterface.php
│   │       │   ├── 🐘 ReadableResourceStream.php
│   │       │   ├── 🐘 ReadableStreamInterface.php
│   │       │   ├── 🐘 ThroughStream.php
│   │       │   ├── 🐘 Util.php
│   │       │   ├── 🐘 WritableResourceStream.php
│   │       │   └── 🐘 WritableStreamInterface.php
│   │       ├── 📁 tests
│   │       │   ├── 📁 Stub
│   │       │   │   └── 🐘 ReadableStreamStub.php
│   │       │   ├── 🐘 CompositeStreamTest.php
│   │       │   ├── 🐘 DuplexResourceStreamIntegrationTest.php
│   │       │   ├── 🐘 DuplexResourceStreamTest.php
│   │       │   ├── 🐘 EnforceBlockingWrapper.php
│   │       │   ├── 🐘 FunctionalInternetTest.php
│   │       │   ├── 🐘 ReadableResourceStreamTest.php
│   │       │   ├── 🐘 TestCase.php
│   │       │   ├── 🐘 ThroughStreamTest.php
│   │       │   ├── 🐘 UtilTest.php
│   │       │   └── 🐘 WritableResourceStreamTest.php
│   │       ├── ⚙️ .gitattributes
│   │       ├── ⚙️ .gitignore
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       ├── ⚙️ composer.json
│   │       ├── 📄 phpunit.xml.dist
│   │       └── 📄 phpunit.xml.legacy
│   ├── 📁 symfony
│   │   ├── 📁 deprecation-contracts
│   │   │   ├── 📁 .github
│   │   │   │   ├── 📁 workflows
│   │   │   │   │   └── ⚙️ close-pull-request.yml
│   │   │   │   └── 📝 PULL_REQUEST_TEMPLATE.md
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 📝 README.md
│   │   │   ├── ⚙️ composer.json
│   │   │   └── 🐘 function.php
│   │   ├── 📁 http-foundation
│   │   │   ├── 📁 .github
│   │   │   │   ├── 📁 workflows
│   │   │   │   │   └── ⚙️ close-pull-request.yml
│   │   │   │   └── 📝 PULL_REQUEST_TEMPLATE.md
│   │   │   ├── 📁 Exception
│   │   │   │   ├── 🐘 BadRequestException.php
│   │   │   │   ├── 🐘 ConflictingHeadersException.php
│   │   │   │   ├── 🐘 JsonException.php
│   │   │   │   ├── 🐘 RequestExceptionInterface.php
│   │   │   │   ├── 🐘 SessionNotFoundException.php
│   │   │   │   ├── 🐘 SuspiciousOperationException.php
│   │   │   │   └── 🐘 UnexpectedValueException.php
│   │   │   ├── 📁 File
│   │   │   │   ├── 📁 Exception
│   │   │   │   │   ├── 🐘 AccessDeniedException.php
│   │   │   │   │   ├── 🐘 CannotWriteFileException.php
│   │   │   │   │   ├── 🐘 ExtensionFileException.php
│   │   │   │   │   ├── 🐘 FileException.php
│   │   │   │   │   ├── 🐘 FileNotFoundException.php
│   │   │   │   │   ├── 🐘 FormSizeFileException.php
│   │   │   │   │   ├── 🐘 IniSizeFileException.php
│   │   │   │   │   ├── 🐘 NoFileException.php
│   │   │   │   │   ├── 🐘 NoTmpDirFileException.php
│   │   │   │   │   ├── 🐘 PartialFileException.php
│   │   │   │   │   ├── 🐘 UnexpectedTypeException.php
│   │   │   │   │   └── 🐘 UploadException.php
│   │   │   │   ├── 🐘 File.php
│   │   │   │   ├── 🐘 Stream.php
│   │   │   │   └── 🐘 UploadedFile.php
│   │   │   ├── 📁 RateLimiter
│   │   │   │   ├── 🐘 AbstractRequestRateLimiter.php
│   │   │   │   ├── 🐘 PeekableRequestRateLimiterInterface.php
│   │   │   │   └── 🐘 RequestRateLimiterInterface.php
│   │   │   ├── 📁 RequestMatcher
│   │   │   │   ├── 🐘 AttributesRequestMatcher.php
│   │   │   │   ├── 🐘 ExpressionRequestMatcher.php
│   │   │   │   ├── 🐘 HostRequestMatcher.php
│   │   │   │   ├── 🐘 IpsRequestMatcher.php
│   │   │   │   ├── 🐘 IsJsonRequestMatcher.php
│   │   │   │   ├── 🐘 MethodRequestMatcher.php
│   │   │   │   ├── 🐘 PathRequestMatcher.php
│   │   │   │   ├── 🐘 PortRequestMatcher.php
│   │   │   │   └── 🐘 SchemeRequestMatcher.php
│   │   │   ├── 📁 Session
│   │   │   │   ├── 📁 Attribute
│   │   │   │   │   ├── 🐘 AttributeBag.php
│   │   │   │   │   └── 🐘 AttributeBagInterface.php
│   │   │   │   ├── 📁 Flash
│   │   │   │   │   ├── 🐘 AutoExpireFlashBag.php
│   │   │   │   │   ├── 🐘 FlashBag.php
│   │   │   │   │   └── 🐘 FlashBagInterface.php
│   │   │   │   ├── 📁 Storage
│   │   │   │   │   ├── 📁 Handler
│   │   │   │   │   │   ├── 🐘 AbstractSessionHandler.php
│   │   │   │   │   │   ├── 🐘 IdentityMarshaller.php
│   │   │   │   │   │   ├── 🐘 MarshallingSessionHandler.php
│   │   │   │   │   │   ├── 🐘 MemcachedSessionHandler.php
│   │   │   │   │   │   ├── 🐘 MigratingSessionHandler.php
│   │   │   │   │   │   ├── 🐘 MongoDbSessionHandler.php
│   │   │   │   │   │   ├── 🐘 NativeFileSessionHandler.php
│   │   │   │   │   │   ├── 🐘 NullSessionHandler.php
│   │   │   │   │   │   ├── 🐘 PdoSessionHandler.php
│   │   │   │   │   │   ├── 🐘 RedisSessionHandler.php
│   │   │   │   │   │   ├── 🐘 SessionHandlerFactory.php
│   │   │   │   │   │   └── 🐘 StrictSessionHandler.php
│   │   │   │   │   ├── 📁 Proxy
│   │   │   │   │   │   ├── 🐘 AbstractProxy.php
│   │   │   │   │   │   └── 🐘 SessionHandlerProxy.php
│   │   │   │   │   ├── 🐘 MetadataBag.php
│   │   │   │   │   ├── 🐘 MockArraySessionStorage.php
│   │   │   │   │   ├── 🐘 MockFileSessionStorage.php
│   │   │   │   │   ├── 🐘 MockFileSessionStorageFactory.php
│   │   │   │   │   ├── 🐘 NativeSessionStorage.php
│   │   │   │   │   ├── 🐘 NativeSessionStorageFactory.php
│   │   │   │   │   ├── 🐘 PhpBridgeSessionStorage.php
│   │   │   │   │   ├── 🐘 PhpBridgeSessionStorageFactory.php
│   │   │   │   │   ├── 🐘 SessionStorageFactoryInterface.php
│   │   │   │   │   └── 🐘 SessionStorageInterface.php
│   │   │   │   ├── 🐘 FlashBagAwareSessionInterface.php
│   │   │   │   ├── 🐘 Session.php
│   │   │   │   ├── 🐘 SessionBagInterface.php
│   │   │   │   ├── 🐘 SessionBagProxy.php
│   │   │   │   ├── 🐘 SessionFactory.php
│   │   │   │   ├── 🐘 SessionFactoryInterface.php
│   │   │   │   ├── 🐘 SessionInterface.php
│   │   │   │   └── 🐘 SessionUtils.php
│   │   │   ├── 📁 Test
│   │   │   │   └── 📁 Constraint
│   │   │   │       ├── 🐘 RequestAttributeValueSame.php
│   │   │   │       ├── 🐘 ResponseCookieValueSame.php
│   │   │   │       ├── 🐘 ResponseFormatSame.php
│   │   │   │       ├── 🐘 ResponseHasCookie.php
│   │   │   │       ├── 🐘 ResponseHasHeader.php
│   │   │   │       ├── 🐘 ResponseHeaderLocationSame.php
│   │   │   │       ├── 🐘 ResponseHeaderSame.php
│   │   │   │       ├── 🐘 ResponseIsRedirected.php
│   │   │   │       ├── 🐘 ResponseIsSuccessful.php
│   │   │   │       ├── 🐘 ResponseIsUnprocessable.php
│   │   │   │       └── 🐘 ResponseStatusCodeSame.php
│   │   │   ├── 📁 Tests
│   │   │   │   ├── 📁 File
│   │   │   │   │   ├── 📁 Fixtures
│   │   │   │   │   │   ├── 📁 directory
│   │   │   │   │   │   │   └── ⚙️ .empty
│   │   │   │   │   │   ├── 📄 -test
│   │   │   │   │   │   ├── ⚙️ .unknownextension
│   │   │   │   │   │   ├── 📄 case-sensitive-mime-type.xlsm
│   │   │   │   │   │   ├── 📄 other-file.example
│   │   │   │   │   │   ├── 📄 test
│   │   │   │   │   │   └── 🖼️ test.gif
│   │   │   │   │   ├── 🐘 FakeFile.php
│   │   │   │   │   ├── 🐘 FileTest.php
│   │   │   │   │   └── 🐘 UploadedFileTest.php
│   │   │   │   ├── 📁 Fixtures
│   │   │   │   │   ├── 📁 response-functional
│   │   │   │   │   │   ├── 📄 common.inc
│   │   │   │   │   │   ├── 📄 cookie_raw_urlencode.expected
│   │   │   │   │   │   ├── 🐘 cookie_raw_urlencode.php
│   │   │   │   │   │   ├── 📄 cookie_samesite_lax.expected
│   │   │   │   │   │   ├── 🐘 cookie_samesite_lax.php
│   │   │   │   │   │   ├── 📄 cookie_samesite_strict.expected
│   │   │   │   │   │   ├── 🐘 cookie_samesite_strict.php
│   │   │   │   │   │   ├── 📄 cookie_urlencode.expected
│   │   │   │   │   │   ├── 🐘 cookie_urlencode.php
│   │   │   │   │   │   ├── 📄 deleted_cookie.expected
│   │   │   │   │   │   ├── 🐘 deleted_cookie.php
│   │   │   │   │   │   ├── 🐘 early_hints.php
│   │   │   │   │   │   ├── 📄 invalid_cookie_name.expected
│   │   │   │   │   │   └── 🐘 invalid_cookie_name.php
│   │   │   │   │   ├── 📁 xml
│   │   │   │   │   │   └── ⚙️ http-status-codes.xml
│   │   │   │   │   └── 🐘 FooEnum.php
│   │   │   │   ├── 📁 RateLimiter
│   │   │   │   │   ├── 🐘 AbstractRequestRateLimiterTest.php
│   │   │   │   │   └── 🐘 MockAbstractRequestRateLimiter.php
│   │   │   │   ├── 📁 RequestMatcher
│   │   │   │   │   ├── 🐘 AttributesRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 ExpressionRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 HostRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 IpsRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 IsJsonRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 MethodRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 PathRequestMatcherTest.php
│   │   │   │   │   ├── 🐘 PortRequestMatcherTest.php
│   │   │   │   │   └── 🐘 SchemeRequestMatcherTest.php
│   │   │   │   ├── 📁 Session
│   │   │   │   │   ├── 📁 Attribute
│   │   │   │   │   │   └── 🐘 AttributeBagTest.php
│   │   │   │   │   ├── 📁 Flash
│   │   │   │   │   │   ├── 🐘 AutoExpireFlashBagTest.php
│   │   │   │   │   │   └── 🐘 FlashBagTest.php
│   │   │   │   │   ├── 📁 Storage
│   │   │   │   │   │   ├── 📁 Handler
│   │   │   │   │   │   │   ├── 📁 Fixtures
│   │   │   │   │   │   │   │   ├── 📄 common.inc
│   │   │   │   │   │   │   │   ├── 📄 empty_destroys.expected
│   │   │   │   │   │   │   │   ├── 🐘 empty_destroys.php
│   │   │   │   │   │   │   │   ├── 📄 invalid_regenerate.expected
│   │   │   │   │   │   │   │   ├── 🐘 invalid_regenerate.php
│   │   │   │   │   │   │   │   ├── 📄 read_only.expected
│   │   │   │   │   │   │   │   ├── 🐘 read_only.php
│   │   │   │   │   │   │   │   ├── 📄 regenerate.expected
│   │   │   │   │   │   │   │   ├── 🐘 regenerate.php
│   │   │   │   │   │   │   │   ├── 📄 storage.expected
│   │   │   │   │   │   │   │   ├── 🐘 storage.php
│   │   │   │   │   │   │   │   ├── 📄 with_cookie.expected
│   │   │   │   │   │   │   │   ├── 🐘 with_cookie.php
│   │   │   │   │   │   │   │   ├── 📄 with_cookie_and_session.expected
│   │   │   │   │   │   │   │   ├── 🐘 with_cookie_and_session.php
│   │   │   │   │   │   │   │   ├── 📄 with_samesite.expected
│   │   │   │   │   │   │   │   ├── 🐘 with_samesite.php
│   │   │   │   │   │   │   │   ├── 📄 with_samesite_and_migration.expected
│   │   │   │   │   │   │   │   └── 🐘 with_samesite_and_migration.php
│   │   │   │   │   │   │   ├── 📁 stubs
│   │   │   │   │   │   │   │   └── 🐘 mongodb.php
│   │   │   │   │   │   │   ├── 🐘 AbstractRedisSessionHandlerTestCase.php
│   │   │   │   │   │   │   ├── 🐘 AbstractSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 IdentityMarshallerTest.php
│   │   │   │   │   │   │   ├── 🐘 MarshallingSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 MemcachedSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 MigratingSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 MongoDbSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 NativeFileSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 NullSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 PdoSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 PredisClusterSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 PredisSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 RedisArraySessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 RedisClusterSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 RedisSessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 RelaySessionHandlerTest.php
│   │   │   │   │   │   │   ├── 🐘 SessionHandlerFactoryTest.php
│   │   │   │   │   │   │   └── 🐘 StrictSessionHandlerTest.php
│   │   │   │   │   │   ├── 📁 Proxy
│   │   │   │   │   │   │   ├── 🐘 AbstractProxyTest.php
│   │   │   │   │   │   │   └── 🐘 SessionHandlerProxyTest.php
│   │   │   │   │   │   ├── 🐘 MetadataBagTest.php
│   │   │   │   │   │   ├── 🐘 MockArraySessionStorageTest.php
│   │   │   │   │   │   ├── 🐘 MockFileSessionStorageTest.php
│   │   │   │   │   │   ├── 🐘 NativeSessionStorageTest.php
│   │   │   │   │   │   └── 🐘 PhpBridgeSessionStorageTest.php
│   │   │   │   │   └── 🐘 SessionTest.php
│   │   │   │   ├── 📁 Test
│   │   │   │   │   └── 📁 Constraint
│   │   │   │   │       ├── 🐘 RequestAttributeValueSameTest.php
│   │   │   │   │       ├── 🐘 ResponseCookieValueSameTest.php
│   │   │   │   │       ├── 🐘 ResponseFormatSameTest.php
│   │   │   │   │       ├── 🐘 ResponseHasCookieTest.php
│   │   │   │   │       ├── 🐘 ResponseHasHeaderTest.php
│   │   │   │   │       ├── 🐘 ResponseHeaderLocationSameTest.php
│   │   │   │   │       ├── 🐘 ResponseHeaderSameTest.php
│   │   │   │   │       ├── 🐘 ResponseIsRedirectedTest.php
│   │   │   │   │       ├── 🐘 ResponseIsSuccessfulTest.php
│   │   │   │   │       ├── 🐘 ResponseIsUnprocessableTest.php
│   │   │   │   │       └── 🐘 ResponseStatusCodeSameTest.php
│   │   │   │   ├── 📁 schema
│   │   │   │   │   ├── 📄 http-status-codes.rng
│   │   │   │   │   └── 📄 iana-registry.rng
│   │   │   │   ├── 🐘 AcceptHeaderItemTest.php
│   │   │   │   ├── 🐘 AcceptHeaderTest.php
│   │   │   │   ├── 🐘 BinaryFileResponseTest.php
│   │   │   │   ├── 🐘 CookieTest.php
│   │   │   │   ├── 🐘 ExpressionRequestMatcherTest.php
│   │   │   │   ├── 🐘 FileBagTest.php
│   │   │   │   ├── 🐘 HeaderBagTest.php
│   │   │   │   ├── 🐘 HeaderUtilsTest.php
│   │   │   │   ├── 🐘 InputBagTest.php
│   │   │   │   ├── 🐘 IpUtilsTest.php
│   │   │   │   ├── 🐘 JsonResponseTest.php
│   │   │   │   ├── 🐘 ParameterBagTest.php
│   │   │   │   ├── 🐘 RedirectResponseTest.php
│   │   │   │   ├── 🐘 RequestMatcherTest.php
│   │   │   │   ├── 🐘 RequestStackTest.php
│   │   │   │   ├── 🐘 RequestTest.php
│   │   │   │   ├── 🐘 ResponseFunctionalTest.php
│   │   │   │   ├── 🐘 ResponseHeaderBagTest.php
│   │   │   │   ├── 🐘 ResponseTest.php
│   │   │   │   ├── 🐘 ResponseTestCase.php
│   │   │   │   ├── 🐘 ServerBagTest.php
│   │   │   │   ├── 🐘 StreamedJsonResponseTest.php
│   │   │   │   ├── 🐘 StreamedResponseTest.php
│   │   │   │   ├── 🐘 UriSignerTest.php
│   │   │   │   └── 🐘 UrlHelperTest.php
│   │   │   ├── ⚙️ .gitattributes
│   │   │   ├── ⚙️ .gitignore
│   │   │   ├── 🐘 AcceptHeader.php
│   │   │   ├── 🐘 AcceptHeaderItem.php
│   │   │   ├── 🐘 BinaryFileResponse.php
│   │   │   ├── 📝 CHANGELOG.md
│   │   │   ├── 🐘 ChainRequestMatcher.php
│   │   │   ├── 🐘 Cookie.php
│   │   │   ├── 🐘 ExpressionRequestMatcher.php
│   │   │   ├── 🐘 FileBag.php
│   │   │   ├── 🐘 HeaderBag.php
│   │   │   ├── 🐘 HeaderUtils.php
│   │   │   ├── 🐘 InputBag.php
│   │   │   ├── 🐘 IpUtils.php
│   │   │   ├── 🐘 JsonResponse.php
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 🐘 ParameterBag.php
│   │   │   ├── 📝 README.md
│   │   │   ├── 🐘 RedirectResponse.php
│   │   │   ├── 🐘 Request.php
│   │   │   ├── 🐘 RequestMatcher.php
│   │   │   ├── 🐘 RequestMatcherInterface.php
│   │   │   ├── 🐘 RequestStack.php
│   │   │   ├── 🐘 Response.php
│   │   │   ├── 🐘 ResponseHeaderBag.php
│   │   │   ├── 🐘 ServerBag.php
│   │   │   ├── 🐘 StreamedJsonResponse.php
│   │   │   ├── 🐘 StreamedResponse.php
│   │   │   ├── 🐘 UriSigner.php
│   │   │   ├── 🐘 UrlHelper.php
│   │   │   ├── ⚙️ composer.json
│   │   │   └── 📄 phpunit.xml.dist
│   │   ├── 📁 polyfill-mbstring
│   │   │   ├── 📁 Resources
│   │   │   │   └── 📁 unidata
│   │   │   │       ├── 🐘 caseFolding.php
│   │   │   │       ├── 🐘 lowerCase.php
│   │   │   │       ├── 🐘 titleCaseRegexp.php
│   │   │   │       └── 🐘 upperCase.php
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 🐘 Mbstring.php
│   │   │   ├── 📝 README.md
│   │   │   ├── 🐘 bootstrap.php
│   │   │   ├── 🐘 bootstrap80.php
│   │   │   └── ⚙️ composer.json
│   │   ├── 📁 polyfill-php83
│   │   │   ├── 📁 Resources
│   │   │   │   └── 📁 stubs
│   │   │   │       ├── 🐘 DateError.php
│   │   │   │       ├── 🐘 DateException.php
│   │   │   │       ├── 🐘 DateInvalidOperationException.php
│   │   │   │       ├── 🐘 DateInvalidTimeZoneException.php
│   │   │   │       ├── 🐘 DateMalformedIntervalStringException.php
│   │   │   │       ├── 🐘 DateMalformedPeriodStringException.php
│   │   │   │       ├── 🐘 DateMalformedStringException.php
│   │   │   │       ├── 🐘 DateObjectError.php
│   │   │   │       ├── 🐘 DateRangeError.php
│   │   │   │       ├── 🐘 Override.php
│   │   │   │       └── 🐘 SQLite3Exception.php
│   │   │   ├── 📄 LICENSE
│   │   │   ├── 🐘 Php83.php
│   │   │   ├── 📝 README.md
│   │   │   ├── 🐘 bootstrap.php
│   │   │   ├── 🐘 bootstrap81.php
│   │   │   └── ⚙️ composer.json
│   │   └── 📁 routing
│   │       ├── 📁 .github
│   │       │   ├── 📁 workflows
│   │       │   │   └── ⚙️ close-pull-request.yml
│   │       │   └── 📝 PULL_REQUEST_TEMPLATE.md
│   │       ├── 📁 Annotation
│   │       │   └── 🐘 Route.php
│   │       ├── 📁 Attribute
│   │       │   └── 🐘 Route.php
│   │       ├── 📁 DependencyInjection
│   │       │   ├── 🐘 AddExpressionLanguageProvidersPass.php
│   │       │   └── 🐘 RoutingResolverPass.php
│   │       ├── 📁 Exception
│   │       │   ├── 🐘 ExceptionInterface.php
│   │       │   ├── 🐘 InvalidArgumentException.php
│   │       │   ├── 🐘 InvalidParameterException.php
│   │       │   ├── 🐘 MethodNotAllowedException.php
│   │       │   ├── 🐘 MissingMandatoryParametersException.php
│   │       │   ├── 🐘 NoConfigurationException.php
│   │       │   ├── 🐘 ResourceNotFoundException.php
│   │       │   ├── 🐘 RouteCircularReferenceException.php
│   │       │   ├── 🐘 RouteNotFoundException.php
│   │       │   └── 🐘 RuntimeException.php
│   │       ├── 📁 Generator
│   │       │   ├── 📁 Dumper
│   │       │   │   ├── 🐘 CompiledUrlGeneratorDumper.php
│   │       │   │   ├── 🐘 GeneratorDumper.php
│   │       │   │   └── 🐘 GeneratorDumperInterface.php
│   │       │   ├── 🐘 CompiledUrlGenerator.php
│   │       │   ├── 🐘 ConfigurableRequirementsInterface.php
│   │       │   ├── 🐘 UrlGenerator.php
│   │       │   └── 🐘 UrlGeneratorInterface.php
│   │       ├── 📁 Loader
│   │       │   ├── 📁 Configurator
│   │       │   │   ├── 📁 Traits
│   │       │   │   │   ├── 🐘 AddTrait.php
│   │       │   │   │   ├── 🐘 HostTrait.php
│   │       │   │   │   ├── 🐘 LocalizedRouteTrait.php
│   │       │   │   │   ├── 🐘 PrefixTrait.php
│   │       │   │   │   └── 🐘 RouteTrait.php
│   │       │   │   ├── 🐘 AliasConfigurator.php
│   │       │   │   ├── 🐘 CollectionConfigurator.php
│   │       │   │   ├── 🐘 ImportConfigurator.php
│   │       │   │   ├── 🐘 RouteConfigurator.php
│   │       │   │   └── 🐘 RoutingConfigurator.php
│   │       │   ├── 📁 schema
│   │       │   │   └── 📁 routing
│   │       │   │       └── 📄 routing-1.0.xsd
│   │       │   ├── 🐘 AnnotationClassLoader.php
│   │       │   ├── 🐘 AnnotationDirectoryLoader.php
│   │       │   ├── 🐘 AnnotationFileLoader.php
│   │       │   ├── 🐘 AttributeClassLoader.php
│   │       │   ├── 🐘 AttributeDirectoryLoader.php
│   │       │   ├── 🐘 AttributeFileLoader.php
│   │       │   ├── 🐘 ClosureLoader.php
│   │       │   ├── 🐘 ContainerLoader.php
│   │       │   ├── 🐘 DirectoryLoader.php
│   │       │   ├── 🐘 GlobFileLoader.php
│   │       │   ├── 🐘 ObjectLoader.php
│   │       │   ├── 🐘 PhpFileLoader.php
│   │       │   ├── 🐘 Psr4DirectoryLoader.php
│   │       │   ├── 🐘 XmlFileLoader.php
│   │       │   └── 🐘 YamlFileLoader.php
│   │       ├── 📁 Matcher
│   │       │   ├── 📁 Dumper
│   │       │   │   ├── 🐘 CompiledUrlMatcherDumper.php
│   │       │   │   ├── 🐘 CompiledUrlMatcherTrait.php
│   │       │   │   ├── 🐘 MatcherDumper.php
│   │       │   │   ├── 🐘 MatcherDumperInterface.php
│   │       │   │   └── 🐘 StaticPrefixCollection.php
│   │       │   ├── 🐘 CompiledUrlMatcher.php
│   │       │   ├── 🐘 ExpressionLanguageProvider.php
│   │       │   ├── 🐘 RedirectableUrlMatcher.php
│   │       │   ├── 🐘 RedirectableUrlMatcherInterface.php
│   │       │   ├── 🐘 RequestMatcherInterface.php
│   │       │   ├── 🐘 TraceableUrlMatcher.php
│   │       │   ├── 🐘 UrlMatcher.php
│   │       │   └── 🐘 UrlMatcherInterface.php
│   │       ├── 📁 Requirement
│   │       │   ├── 🐘 EnumRequirement.php
│   │       │   └── 🐘 Requirement.php
│   │       ├── 📁 Tests
│   │       │   ├── 📁 Attribute
│   │       │   │   └── 🐘 RouteTest.php
│   │       │   ├── 📁 DependencyInjection
│   │       │   │   ├── 🐘 AddExpressionLanguageProvidersPassTest.php
│   │       │   │   └── 🐘 RoutingResolverPassTest.php
│   │       │   ├── 📁 Fixtures
│   │       │   │   ├── 📁 AnnotationFixtures
│   │       │   │   │   ├── 🐘 AbstractClassController.php
│   │       │   │   │   ├── 🐘 ActionPathController.php
│   │       │   │   │   ├── 🐘 BazClass.php
│   │       │   │   │   ├── 🐘 DefaultValueController.php
│   │       │   │   │   ├── 🐘 EncodingClass.php
│   │       │   │   │   ├── 🐘 ExplicitLocalizedActionPathController.php
│   │       │   │   │   ├── 🐘 FooController.php
│   │       │   │   │   ├── 🐘 GlobalDefaultsClass.php
│   │       │   │   │   ├── 🐘 InvokableController.php
│   │       │   │   │   ├── 🐘 InvokableFQCNAliasConflictController.php
│   │       │   │   │   ├── 🐘 InvokableLocalizedController.php
│   │       │   │   │   ├── 🐘 InvokableMethodController.php
│   │       │   │   │   ├── 🐘 LocalizedActionPathController.php
│   │       │   │   │   ├── 🐘 LocalizedMethodActionControllers.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixLocalizedActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixMissingLocaleActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixMissingRouteLocaleActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixWithRouteWithoutLocale.php
│   │       │   │   │   ├── 🐘 MethodActionControllers.php
│   │       │   │   │   ├── 🐘 MethodsAndSchemes.php
│   │       │   │   │   ├── 🐘 MissingRouteNameController.php
│   │       │   │   │   ├── 🐘 NothingButNameController.php
│   │       │   │   │   ├── 🐘 PrefixedActionLocalizedRouteController.php
│   │       │   │   │   ├── 🐘 PrefixedActionPathController.php
│   │       │   │   │   ├── 🐘 RequirementsWithoutPlaceholderNameController.php
│   │       │   │   │   ├── 🐘 RouteWithEnv.php
│   │       │   │   │   ├── 🐘 RouteWithPrefixController.php
│   │       │   │   │   └── 🐘 Utf8ActionControllers.php
│   │       │   │   ├── 📁 AttributeFixtures
│   │       │   │   │   ├── 🐘 ActionPathController.php
│   │       │   │   │   ├── 🐘 BazClass.php
│   │       │   │   │   ├── 🐘 DefaultValueController.php
│   │       │   │   │   ├── 🐘 EncodingClass.php
│   │       │   │   │   ├── 🐘 ExplicitLocalizedActionPathController.php
│   │       │   │   │   ├── 🐘 ExtendedRoute.php
│   │       │   │   │   ├── 🐘 ExtendedRouteOnClassController.php
│   │       │   │   │   ├── 🐘 ExtendedRouteOnMethodController.php
│   │       │   │   │   ├── 🐘 FooController.php
│   │       │   │   │   ├── 🐘 GlobalDefaultsClass.php
│   │       │   │   │   ├── 🐘 InvokableController.php
│   │       │   │   │   ├── 🐘 InvokableFQCNAliasConflictController.php
│   │       │   │   │   ├── 🐘 InvokableLocalizedController.php
│   │       │   │   │   ├── 🐘 InvokableMethodController.php
│   │       │   │   │   ├── 🐘 LocalizedActionPathController.php
│   │       │   │   │   ├── 🐘 LocalizedMethodActionControllers.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixLocalizedActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixMissingLocaleActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixMissingRouteLocaleActionController.php
│   │       │   │   │   ├── 🐘 LocalizedPrefixWithRouteWithoutLocale.php
│   │       │   │   │   ├── 🐘 MethodActionControllers.php
│   │       │   │   │   ├── 🐘 MethodsAndSchemes.php
│   │       │   │   │   ├── 🐘 MissingRouteNameController.php
│   │       │   │   │   ├── 🐘 NothingButNameController.php
│   │       │   │   │   ├── 🐘 PrefixedActionLocalizedRouteController.php
│   │       │   │   │   ├── 🐘 PrefixedActionPathController.php
│   │       │   │   │   ├── 🐘 RequirementsWithoutPlaceholderNameController.php
│   │       │   │   │   ├── 🐘 RouteWithEnv.php
│   │       │   │   │   ├── 🐘 RouteWithPrefixController.php
│   │       │   │   │   ├── 🐘 RouteWithPriorityController.php
│   │       │   │   │   └── 🐘 Utf8ActionControllers.php
│   │       │   │   ├── 📁 AttributedClasses
│   │       │   │   │   ├── 🐘 AbstractClass.php
│   │       │   │   │   ├── 🐘 BarClass.php
│   │       │   │   │   ├── 🐘 BazClass.php
│   │       │   │   │   ├── 🐘 EncodingClass.php
│   │       │   │   │   ├── 🐘 FooClass.php
│   │       │   │   │   └── 🐘 FooTrait.php
│   │       │   │   ├── 📁 Attributes
│   │       │   │   │   └── 🐘 FooAttributes.php
│   │       │   │   ├── 📁 AttributesFixtures
│   │       │   │   │   ├── 🐘 AttributesClassParamAfterCommaController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamAfterParenthesisController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamInlineAfterCommaController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamInlineAfterParenthesisController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamInlineQuotedAfterCommaController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamInlineQuotedAfterParenthesisController.php
│   │       │   │   │   ├── 🐘 AttributesClassParamQuotedAfterCommaController.php
│   │       │   │   │   └── 🐘 AttributesClassParamQuotedAfterParenthesisController.php
│   │       │   │   ├── 📁 Enum
│   │       │   │   │   ├── 🐘 TestIntBackedEnum.php
│   │       │   │   │   ├── 🐘 TestStringBackedEnum.php
│   │       │   │   │   ├── 🐘 TestStringBackedEnum2.php
│   │       │   │   │   └── 🐘 TestUnitEnum.php
│   │       │   │   ├── 📁 OtherAnnotatedClasses
│   │       │   │   │   ├── 🐘 AnonymousClassInTrait.php
│   │       │   │   │   ├── 🐘 NoStartTagClass.php
│   │       │   │   │   └── 🐘 VariadicClass.php
│   │       │   │   ├── 📁 Psr4Controllers
│   │       │   │   │   ├── 📁 SubNamespace
│   │       │   │   │   │   ├── 📁 EvenDeeperNamespace
│   │       │   │   │   │   │   └── 🐘 MyOtherController.php
│   │       │   │   │   │   ├── 🐘 IrrelevantClass.php
│   │       │   │   │   │   ├── 🐘 IrrelevantEnum.php
│   │       │   │   │   │   ├── 🐘 IrrelevantInterface.php
│   │       │   │   │   │   ├── 🐘 MyAbstractController.php
│   │       │   │   │   │   ├── 🐘 MyChildController.php
│   │       │   │   │   │   ├── 🐘 MyControllerWithATrait.php
│   │       │   │   │   │   └── 🐘 SomeSharedImplementation.php
│   │       │   │   │   ├── 🐘 MyController.php
│   │       │   │   │   └── 🐘 MyUnannotatedController.php
│   │       │   │   ├── 📁 alias
│   │       │   │   │   ├── 🐘 alias.php
│   │       │   │   │   ├── ⚙️ alias.xml
│   │       │   │   │   ├── ⚙️ alias.yaml
│   │       │   │   │   ├── 🐘 expected.php
│   │       │   │   │   ├── ⚙️ invalid-alias.yaml
│   │       │   │   │   ├── ⚙️ invalid-deprecated-no-package.xml
│   │       │   │   │   ├── ⚙️ invalid-deprecated-no-package.yaml
│   │       │   │   │   ├── ⚙️ invalid-deprecated-no-version.xml
│   │       │   │   │   ├── ⚙️ invalid-deprecated-no-version.yaml
│   │       │   │   │   └── ⚙️ override.yaml
│   │       │   │   ├── 📁 controller
│   │       │   │   │   ├── 📁 empty_wildcard
│   │       │   │   │   │   └── ⚙️ .gitignore
│   │       │   │   │   ├── ⚙️ import__controller.xml
│   │       │   │   │   ├── ⚙️ import__controller.yml
│   │       │   │   │   ├── ⚙️ import_controller.xml
│   │       │   │   │   ├── ⚙️ import_controller.yml
│   │       │   │   │   ├── ⚙️ import_override_defaults.xml
│   │       │   │   │   ├── ⚙️ import_override_defaults.yml
│   │       │   │   │   ├── ⚙️ override_defaults.xml
│   │       │   │   │   ├── ⚙️ override_defaults.yml
│   │       │   │   │   ├── ⚙️ routing.xml
│   │       │   │   │   └── ⚙️ routing.yml
│   │       │   │   ├── 📁 directory
│   │       │   │   │   ├── 📁 recurse
│   │       │   │   │   │   ├── ⚙️ routes1.yml
│   │       │   │   │   │   └── ⚙️ routes2.yml
│   │       │   │   │   └── ⚙️ routes3.yml
│   │       │   │   ├── 📁 directory_import
│   │       │   │   │   └── ⚙️ import.yml
│   │       │   │   ├── 📁 dumper
│   │       │   │   │   ├── 🐘 compiled_url_matcher0.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher1.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher10.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher11.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher12.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher13.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher14.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher2.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher3.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher4.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher5.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher6.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher7.php
│   │       │   │   │   ├── 🐘 compiled_url_matcher8.php
│   │       │   │   │   └── 🐘 compiled_url_matcher9.php
│   │       │   │   ├── 📁 glob
│   │       │   │   │   ├── ⚙️ bar.xml
│   │       │   │   │   ├── ⚙️ bar.yml
│   │       │   │   │   ├── ⚙️ baz.xml
│   │       │   │   │   ├── ⚙️ baz.yml
│   │       │   │   │   ├── ⚙️ import_multiple.xml
│   │       │   │   │   ├── ⚙️ import_multiple.yml
│   │       │   │   │   ├── ⚙️ import_single.xml
│   │       │   │   │   ├── ⚙️ import_single.yml
│   │       │   │   │   ├── 🐘 php_dsl.php
│   │       │   │   │   ├── 🐘 php_dsl_bar.php
│   │       │   │   │   └── 🐘 php_dsl_baz.php
│   │       │   │   ├── 📁 import_with_name_prefix
│   │       │   │   │   ├── ⚙️ routing.xml
│   │       │   │   │   └── ⚙️ routing.yml
│   │       │   │   ├── 📁 import_with_no_trailing_slash
│   │       │   │   │   ├── ⚙️ routing.xml
│   │       │   │   │   └── ⚙️ routing.yml
│   │       │   │   ├── 📁 locale_and_host
│   │       │   │   │   ├── 🐘 import-with-host-expected-collection.php
│   │       │   │   │   ├── 🐘 import-with-locale-and-host-expected-collection.php
│   │       │   │   │   ├── 🐘 import-with-single-host-expected-collection.php
│   │       │   │   │   ├── 🐘 import-without-host-expected-collection.php
│   │       │   │   │   ├── 🐘 imported.php
│   │       │   │   │   ├── ⚙️ imported.xml
│   │       │   │   │   ├── ⚙️ imported.yml
│   │       │   │   │   ├── 🐘 importer-with-host.php
│   │       │   │   │   ├── ⚙️ importer-with-host.xml
│   │       │   │   │   ├── ⚙️ importer-with-host.yml
│   │       │   │   │   ├── 🐘 importer-with-locale-and-host.php
│   │       │   │   │   ├── ⚙️ importer-with-locale-and-host.xml
│   │       │   │   │   ├── ⚙️ importer-with-locale-and-host.yml
│   │       │   │   │   ├── 🐘 importer-with-single-host.php
│   │       │   │   │   ├── ⚙️ importer-with-single-host.xml
│   │       │   │   │   ├── ⚙️ importer-with-single-host.yml
│   │       │   │   │   ├── 🐘 importer-without-host.php
│   │       │   │   │   ├── ⚙️ importer-without-host.xml
│   │       │   │   │   ├── ⚙️ importer-without-host.yml
│   │       │   │   │   ├── ⚙️ priorized-host.yml
│   │       │   │   │   ├── 🐘 route-with-hosts-expected-collection.php
│   │       │   │   │   ├── 🐘 route-with-hosts.php
│   │       │   │   │   ├── ⚙️ route-with-hosts.xml
│   │       │   │   │   └── ⚙️ route-with-hosts.yml
│   │       │   │   ├── 📁 localized
│   │       │   │   │   ├── ⚙️ imported-with-locale-but-not-localized.xml
│   │       │   │   │   ├── ⚙️ imported-with-locale-but-not-localized.yml
│   │       │   │   │   ├── ⚙️ imported-with-locale.xml
│   │       │   │   │   ├── ⚙️ imported-with-locale.yml
│   │       │   │   │   ├── 🐘 imported-with-utf8.php
│   │       │   │   │   ├── ⚙️ imported-with-utf8.xml
│   │       │   │   │   ├── ⚙️ imported-with-utf8.yml
│   │       │   │   │   ├── ⚙️ importer-with-controller-default.yml
│   │       │   │   │   ├── ⚙️ importer-with-locale-imports-non-localized-route.xml
│   │       │   │   │   ├── ⚙️ importer-with-locale-imports-non-localized-route.yml
│   │       │   │   │   ├── ⚙️ importer-with-locale.xml
│   │       │   │   │   ├── ⚙️ importer-with-locale.yml
│   │       │   │   │   ├── 🐘 importer-with-utf8.php
│   │       │   │   │   ├── ⚙️ importer-with-utf8.xml
│   │       │   │   │   ├── ⚙️ importer-with-utf8.yml
│   │       │   │   │   ├── ⚙️ importing-localized-route.yml
│   │       │   │   │   ├── ⚙️ localized-prefix.yml
│   │       │   │   │   ├── ⚙️ localized-route.yml
│   │       │   │   │   ├── ⚙️ missing-locale-in-importer.yml
│   │       │   │   │   ├── ⚙️ not-localized.yml
│   │       │   │   │   ├── ⚙️ officially_formatted_locales.yml
│   │       │   │   │   ├── ⚙️ route-without-path-or-locales.yml
│   │       │   │   │   ├── 🐘 utf8.php
│   │       │   │   │   ├── ⚙️ utf8.xml
│   │       │   │   │   └── ⚙️ utf8.yml
│   │       │   │   ├── 📁 psr4-controllers-redirection
│   │       │   │   │   ├── 🐘 psr4-attributes.php
│   │       │   │   │   ├── ⚙️ psr4-attributes.xml
│   │       │   │   │   └── ⚙️ psr4-attributes.yaml
│   │       │   │   ├── 🐘 CustomCompiledRoute.php
│   │       │   │   ├── 🐘 CustomRouteCompiler.php
│   │       │   │   ├── 🐘 CustomXmlFileLoader.php
│   │       │   │   ├── 🐘 RedirectableUrlMatcher.php
│   │       │   │   ├── 🐘 TraceableAttributeClassLoader.php
│   │       │   │   ├── 🐘 annotated.php
│   │       │   │   ├── ⚙️ bad_format.yml
│   │       │   │   ├── ⚙️ bar.xml
│   │       │   │   ├── 🐘 class-attributes.php
│   │       │   │   ├── ⚙️ class-attributes.xml
│   │       │   │   ├── ⚙️ class-attributes.yaml
│   │       │   │   ├── 🐘 collection-defaults.php
│   │       │   │   ├── 🐘 defaults.php
│   │       │   │   ├── ⚙️ defaults.xml
│   │       │   │   ├── ⚙️ defaults.yml
│   │       │   │   ├── ⚙️ empty.yml
│   │       │   │   ├── ⚙️ file_resource.yml
│   │       │   │   ├── ⚙️ foo.xml
│   │       │   │   ├── ⚙️ foo1.xml
│   │       │   │   ├── 🐘 imported-with-defaults.php
│   │       │   │   ├── ⚙️ imported-with-defaults.xml
│   │       │   │   ├── ⚙️ imported-with-defaults.yml
│   │       │   │   ├── 🐘 importer-with-defaults.php
│   │       │   │   ├── ⚙️ importer-with-defaults.xml
│   │       │   │   ├── ⚙️ importer-with-defaults.yml
│   │       │   │   ├── ⚙️ incomplete.yml
│   │       │   │   ├── ⚙️ list_defaults.xml
│   │       │   │   ├── ⚙️ list_in_list_defaults.xml
│   │       │   │   ├── ⚙️ list_in_map_defaults.xml
│   │       │   │   ├── ⚙️ list_null_values.xml
│   │       │   │   ├── ⚙️ localized.xml
│   │       │   │   ├── ⚙️ map_defaults.xml
│   │       │   │   ├── ⚙️ map_in_list_defaults.xml
│   │       │   │   ├── ⚙️ map_in_map_defaults.xml
│   │       │   │   ├── ⚙️ map_null_values.xml
│   │       │   │   ├── ⚙️ missing_id.xml
│   │       │   │   ├── ⚙️ missing_path.xml
│   │       │   │   ├── ⚙️ namespaceprefix.xml
│   │       │   │   ├── ⚙️ nonesense_resource_plus_path.yml
│   │       │   │   ├── ⚙️ nonesense_type_without_resource.yml
│   │       │   │   ├── ⚙️ nonvalid-deprecated-route.xml
│   │       │   │   ├── ⚙️ nonvalid.xml
│   │       │   │   ├── ⚙️ nonvalid.yml
│   │       │   │   ├── ⚙️ nonvalid2.yml
│   │       │   │   ├── ⚙️ nonvalidkeys.yml
│   │       │   │   ├── ⚙️ nonvalidnode.xml
│   │       │   │   ├── ⚙️ nonvalidroute.xml
│   │       │   │   ├── ⚙️ null_values.xml
│   │       │   │   ├── 🐘 php_dsl.php
│   │       │   │   ├── 🐘 php_dsl_i18n.php
│   │       │   │   ├── 🐘 php_dsl_sub.php
│   │       │   │   ├── 🐘 php_dsl_sub_i18n.php
│   │       │   │   ├── 🐘 php_dsl_sub_root.php
│   │       │   │   ├── 🐘 php_object_dsl.php
│   │       │   │   ├── 🐘 psr4-attributes.php
│   │       │   │   ├── ⚙️ psr4-attributes.xml
│   │       │   │   ├── ⚙️ psr4-attributes.yaml
│   │       │   │   ├── 🐘 psr4-controllers-redirection.php
│   │       │   │   ├── ⚙️ psr4-controllers-redirection.xml
│   │       │   │   ├── ⚙️ psr4-controllers-redirection.yaml
│   │       │   │   ├── ⚙️ requirements_without_placeholder_name.yml
│   │       │   │   ├── ⚙️ scalar_defaults.xml
│   │       │   │   ├── ⚙️ special_route_name.yml
│   │       │   │   ├── 🐘 validpattern.php
│   │       │   │   ├── ⚙️ validpattern.xml
│   │       │   │   ├── ⚙️ validpattern.yml
│   │       │   │   ├── 🐘 validresource.php
│   │       │   │   ├── ⚙️ validresource.xml
│   │       │   │   ├── ⚙️ validresource.yml
│   │       │   │   ├── ⚙️ when-env.xml
│   │       │   │   ├── ⚙️ when-env.yml
│   │       │   │   ├── 🐘 with_define_path_variable.php
│   │       │   │   └── ⚙️ withdoctype.xml
│   │       │   ├── 📁 Generator
│   │       │   │   ├── 📁 Dumper
│   │       │   │   │   └── 🐘 CompiledUrlGeneratorDumperTest.php
│   │       │   │   └── 🐘 UrlGeneratorTest.php
│   │       │   ├── 📁 Loader
│   │       │   │   ├── 🐘 AttributeClassLoaderTestCase.php
│   │       │   │   ├── 🐘 AttributeClassLoaderWithAnnotationsTest.php
│   │       │   │   ├── 🐘 AttributeClassLoaderWithAttributesTest.php
│   │       │   │   ├── 🐘 AttributeDirectoryLoaderTest.php
│   │       │   │   ├── 🐘 AttributeFileLoaderTest.php
│   │       │   │   ├── 🐘 ClosureLoaderTest.php
│   │       │   │   ├── 🐘 ContainerLoaderTest.php
│   │       │   │   ├── 🐘 DirectoryLoaderTest.php
│   │       │   │   ├── 🐘 FileLocatorStub.php
│   │       │   │   ├── 🐘 GlobFileLoaderTest.php
│   │       │   │   ├── 🐘 ObjectLoaderTest.php
│   │       │   │   ├── 🐘 PhpFileLoaderTest.php
│   │       │   │   ├── 🐘 Psr4DirectoryLoaderTest.php
│   │       │   │   ├── 🐘 XmlFileLoaderTest.php
│   │       │   │   └── 🐘 YamlFileLoaderTest.php
│   │       │   ├── 📁 Matcher
│   │       │   │   ├── 📁 Dumper
│   │       │   │   │   ├── 🐘 CompiledUrlMatcherDumperTest.php
│   │       │   │   │   └── 🐘 StaticPrefixCollectionTest.php
│   │       │   │   ├── 🐘 CompiledRedirectableUrlMatcherTest.php
│   │       │   │   ├── 🐘 CompiledUrlMatcherTest.php
│   │       │   │   ├── 🐘 ExpressionLanguageProviderTest.php
│   │       │   │   ├── 🐘 RedirectableUrlMatcherTest.php
│   │       │   │   ├── 🐘 TraceableUrlMatcherTest.php
│   │       │   │   └── 🐘 UrlMatcherTest.php
│   │       │   ├── 📁 Requirement
│   │       │   │   ├── 🐘 EnumRequirementTest.php
│   │       │   │   └── 🐘 RequirementTest.php
│   │       │   ├── 🐘 CompiledRouteTest.php
│   │       │   ├── 🐘 RequestContextTest.php
│   │       │   ├── 🐘 RouteCollectionTest.php
│   │       │   ├── 🐘 RouteCompilerTest.php
│   │       │   ├── 🐘 RouteTest.php
│   │       │   └── 🐘 RouterTest.php
│   │       ├── ⚙️ .gitattributes
│   │       ├── ⚙️ .gitignore
│   │       ├── 🐘 Alias.php
│   │       ├── 📝 CHANGELOG.md
│   │       ├── 🐘 CompiledRoute.php
│   │       ├── 📄 LICENSE
│   │       ├── 📝 README.md
│   │       ├── 🐘 RequestContext.php
│   │       ├── 🐘 RequestContextAwareInterface.php
│   │       ├── 🐘 Route.php
│   │       ├── 🐘 RouteCollection.php
│   │       ├── 🐘 RouteCompiler.php
│   │       ├── 🐘 RouteCompilerInterface.php
│   │       ├── 🐘 Router.php
│   │       ├── 🐘 RouterInterface.php
│   │       ├── ⚙️ composer.json
│   │       └── 📄 phpunit.xml.dist
│   └── 🐘 autoload.php
├── 📁 websocket
│   └── 🐘 server.php
├── ⚙️ .htaccess
├── 📝 README.md
├── ⚙️ composer.json
├── 🐘 config.php
├── 📄 database.sql
├── 🐘 get-placeholder.php
├── 🐘 helpers.php
├── 🐘 index.php
├── 📄 migration_add_post_like.sql
├── 📄 migration_avatars.sql
└── 📄 start-chat.bat

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
  - `getPlaceholderImage()` - Generate placeholder

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
- [x] Posts Management (CRUD)
- [x] Comments & Nested Replies
- [x] Favorites & Likes
- [x] Notifications System
- [x] Search & Filters
- [x] Admin Dashboard
- [x] User Profiles with Avatar
- [x] Responsive Design

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

---

## 🔄 Recent Updates

### December 2025
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
