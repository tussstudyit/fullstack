# Models và Controllers - Hướng dẫn sử dụng

## Cấu trúc thư mục

```
fullstack/
├── Models/
│   ├── User.php          # Model quản lý users
│   ├── Post.php          # Model quản lý bài đăng
│   ├── Favorite.php      # Model quản lý yêu thích
│   └── Category.php      # Model quản lý danh mục
├── Controllers/
│   ├── AuthController.php     # Xử lý đăng nhập, đăng ký, đăng xuất
│   ├── PostController.php     # Xử lý tạo, cập nhật, xóa bài đăng
│   └── FavoriteController.php # Xử lý thêm/xóa yêu thích
└── config.php            # Cấu hình database
```

## Models

### User Model
**Quản lý tất cả thao tác liên quan đến người dùng**

```php
require_once __DIR__ . '/../Models/User.php';

$userModel = new User();

// Tìm user theo ID
$user = $userModel->findById(1);

// Tìm user theo email
$user = $userModel->findByEmail('user@example.com');

// Tìm user theo username
$user = $userModel->findByUsername('username');

// Đăng ký user mới
$result = $userModel->register([
    'username' => 'newuser',
    'email' => 'newuser@example.com',
    'password' => 'password123',
    'full_name' => 'Nguyễn Văn A',
    'phone' => '0901234567',
    'role' => 'tenant'
]);

// Đăng nhập
$result = $userModel->login('user@example.com', 'password123');

// Cập nhật profile
$result = $userModel->updateProfile(1, [
    'full_name' => 'Nguyễn Văn B',
    'phone' => '0987654321'
]);

// Đổi mật khẩu
$result = $userModel->changePassword(1, 'oldPassword', 'newPassword');

// Lấy danh sách users
$users = $userModel->getAllUsers(limit: 10, offset: 0);

// Đếm tổng users
$total = $userModel->countAll();
```

### Post Model
**Quản lý bài đăng cho thuê**

```php
require_once __DIR__ . '/../Models/Post.php';

$postModel = new Post();

// Tìm bài đăng theo ID
$post = $postModel->findById(1);

// Tạo bài đăng mới
$result = $postModel->create([
    'user_id' => $_SESSION['user_id'],
    'category_id' => 1,
    'title' => 'Phòng trọ gần trường',
    'description' => 'Phòng sạch, thoáng mát...',
    'address' => '123 Đường ABC',
    'district' => 'Quận 1',
    'city' => 'TP. Hồ Chí Minh',
    'price' => 2500000,
    'area' => 20,
    'room_type' => 'single',
    'max_people' => 1,
    'gender' => 'any',
    'amenities' => 'WiFi, Điều hòa, Tủ lạnh',
    'utilities' => 'Điện, nước, internet'
]);

// Cập nhật bài đăng
$result = $postModel->update(1, [
    'title' => 'Tiêu đề mới',
    'price' => 3000000
]);

// Xóa bài đăng
$result = $postModel->delete(1);

// Lấy danh sách bài đăng với filter
$posts = $postModel->getFiltered([
    'search' => 'phòng trọ',
    'category_id' => 1,
    'district' => 'Quận 1',
    'city' => 'TP. Hồ Chí Minh',
    'price_min' => 1000000,
    'price_max' => 5000000
], limit: 10, offset: 0);

// Lấy bài đăng nổi bật
$featured = $postModel->getFeatured(limit: 3);

// Lấy bài đăng của user
$userPosts = $postModel->getByUserId($_SESSION['user_id']);

// Đếm bài đăng của user
$total = $postModel->countByUserId($_SESSION['user_id']);

// Tăng lượt xem
$postModel->incrementView(1);
```

### Favorite Model
**Quản lý danh sách yêu thích**

```php
require_once __DIR__ . '/../Models/Favorite.php';

$favoriteModel = new Favorite();

// Thêm vào yêu thích
$result = $favoriteModel->add($_SESSION['user_id'], $post_id);

// Xóa khỏi yêu thích
$result = $favoriteModel->remove($_SESSION['user_id'], $post_id);

// Lấy danh sách yêu thích của user
$favorites = $favoriteModel->getByUserId($_SESSION['user_id']);

// Kiểm tra bài đăng có yêu thích không
$isFavorited = $favoriteModel->isFavorited($_SESSION['user_id'], $post_id);

// Đếm yêu thích của user
$total = $favoriteModel->countByUserId($_SESSION['user_id']);
```

### Category Model
**Quản lý danh mục bài đăng**

```php
require_once __DIR__ . '/../Models/Category.php';

$categoryModel = new Category();

// Lấy tất cả danh mục
$categories = $categoryModel->getAll();

// Tìm danh mục theo ID
$category = $categoryModel->findById(1);

// Lấy danh mục với số lượng bài đăng
$categoriesWithCount = $categoryModel->getAllWithCount();
```

## Controllers

### AuthController
**Xử lý đăng nhập, đăng ký, đăng xuất**

**Đăng ký:**
```html
<form action="../../Controllers/AuthController.php" method="POST">
    <input type="hidden" name="action" value="register">
    <input type="text" name="username" required>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <input type="password" name="confirm_password" required>
    <input type="text" name="fullname" required>
    <input type="tel" name="phone">
    <select name="role" required>
        <option value="tenant">Người thuê</option>
        <option value="landlord">Chủ trọ</option>
    </select>
    <button type="submit">Đăng ký</button>
</form>
```

**Đăng nhập:**
```html
<form action="../../Controllers/AuthController.php" method="POST">
    <input type="hidden" name="action" value="login">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Đăng nhập</button>
</form>
```

**Đăng xuất:**
```html
<a href="../../Controllers/AuthController.php?action=logout">Đăng xuất</a>
```

### PostController
**Xử lý tạo, cập nhật, xóa bài đăng**

**Tạo bài đăng:**
```html
<form action="../../Controllers/PostController.php" method="POST">
    <input type="hidden" name="action" value="create">
    <input type="text" name="title" required>
    <textarea name="description" required></textarea>
    <input type="text" name="address" required>
    <input type="text" name="district">
    <input type="text" name="city">
    <input type="number" name="price" required>
    <input type="number" name="area">
    <input type="number" name="category_id" value="1">
    <button type="submit">Đăng tin</button>
</form>
```

**Cập nhật bài đăng:**
```html
<form action="../../Controllers/PostController.php" method="POST">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <input type="text" name="title">
    <input type="number" name="price">
    <!-- Các field khác -->
    <button type="submit">Cập nhật</button>
</form>
```

**Xóa bài đăng:**
```html
<form action="../../Controllers/PostController.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <button type="submit">Xóa</button>
</form>
```

### FavoriteController
**Xử lý thêm/xóa yêu thích**

**Thêm yêu thích:**
```html
<form action="../../Controllers/FavoriteController.php" method="POST">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <button type="submit">Thêm vào yêu thích</button>
</form>
```

**Xóa yêu thích:**
```html
<form action="../../Controllers/FavoriteController.php" method="POST">
    <input type="hidden" name="action" value="remove">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <button type="submit">Xóa khỏi yêu thích</button>
</form>
```

## Quy ước response

Tất cả Models và Controllers trả về response theo định dạng:

```php
[
    'success' => true/false,
    'message' => 'Thông báo',
    'data' => [...] // Tuỳ chọn
]
```

## Xử lý lỗi

Tất cả lỗi được ghi vào `error_log()`:

```php
// Development
error_log("Error message");

// Xem log tại:
// Windows: C:\xampp\apache\logs\error.log
// Linux: /var/log/apache2/error.log
```

## Session Variables

Sau khi đăng nhập, các session variables sẽ được set:

```php
$_SESSION['user_id']    // ID của user
$_SESSION['username']   // Username
$_SESSION['email']      // Email
$_SESSION['user_name']  // Tên đầy đủ
$_SESSION['role']       // Role (admin, landlord, tenant)
```

## Helper Functions

Các helper function trong `config.php`:

```php
isLoggedIn()      // Kiểm tra đã đăng nhập
isAdmin()         // Kiểm tra là admin
isLandlord()      // Kiểm tra là chủ trọ
redirect($url)    // Redirect đến URL
sanitize($data)   // Làm sạch input
getDB()           // Lấy kết nối database
```

## Ví dụ hoàn chỉnh

### Tạo bài đăng
```php
<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Post.php';

if (!isLoggedIn()) {
    redirect('../../Views/auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postModel = new Post();
    
    $result = $postModel->create([
        'user_id' => $_SESSION['user_id'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'address' => $_POST['address'],
        'price' => $_POST['price']
    ]);
    
    if ($result['success']) {
        // Redirect tới trang danh sách bài đăng
        redirect('../../Views/posts/list.php?success=1');
    } else {
        $error = $result['message'];
    }
}
?>
```

### Hiển thị bài đăng nổi bật
```php
<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Post.php';

$postModel = new Post();
$featured_posts = $postModel->getFeatured(limit: 3);

foreach ($featured_posts as $post) {
    echo '<div class="post-card">';
    echo '<h3>' . $post['title'] . '</h3>';
    echo '<p>' . $post['address'] . '</p>';
    echo '<span class="price">' . number_format($post['price'], 0, ',', '.') . '₫</span>';
    echo '</div>';
}
?>
```
