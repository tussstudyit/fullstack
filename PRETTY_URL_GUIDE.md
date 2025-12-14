# Hướng dẫn Pretty URL (Clean URL)

## Tổng quan
Hệ thống đã được nâng cấp để sử dụng Pretty URL cho các bài viết. Thay vì URL dạng:
```
http://localhost:3000/Views/posts/detail.php?id=16
```

Bây giờ sẽ sử dụng URL dạng:
```
http://localhost:3000/phong-tro-gan-fpt
```

## Các thay đổi đã thực hiện

### 1. Database
- ✅ Thêm cột `slug` vào bảng `posts`
- ✅ Generate slug tự động cho tất cả bài viết hiện có
- ✅ Slug được tạo từ tiêu đề bài viết (hỗ trợ tiếng Việt)

### 2. Backend PHP

#### helpers.php
- **generateSlug($text)**: Chuyển đổi tiêu đề tiếng Việt thành slug
  - Ví dụ: "Phòng trọ gần FPT" → "phong-tro-gan-fpt"
- **getUniqueSlug($title, $postId)**: Đảm bảo slug là duy nhất trong database

#### Models/Post.php
- **findBySlug($slug)**: Tìm bài viết theo slug
- **create()**: Tự động generate slug khi tạo bài viết mới
- **update()**: Tự động cập nhật slug khi thay đổi tiêu đề

#### Views/posts/detail.php
- Hỗ trợ cả 2 loại URL:
  - Slug: `/phong-tro-gan-fpt`
  - ID (backward compatible): `detail.php?id=16`
- Tự động redirect từ URL cũ sang URL mới

### 3. URL Rewriting (.htaccess)
```apache
RewriteEngine On
RewriteBase /

# Redirect old URLs to new slug format
RewriteCond %{QUERY_STRING} ^id=([0-9]+)$
RewriteRule ^Views/posts/detail\.php$ /Views/posts/%1? [R=301,L]

# Handle pretty URLs for post details
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-z0-9\-]+)$ /Views/posts/detail.php?slug=$1 [L,QSA]
```

### 4. Frontend Updates
Đã cập nhật tất cả các link trong:
- ✅ index.php (trang chủ)
- ✅ Views/posts/list.php
- ✅ Views/user/my-posts.php
- ✅ Views/user/favorites.php
- ✅ Views/admin/posts.php
- ✅ Views/chat/chat.php
- ✅ Models/Notification.php
- ✅ api/chat.php

## Cách sử dụng

### Tạo bài viết mới
```php
$postModel = new Post();
$result = $postModel->create([
    'title' => 'Phòng trọ đẹp gần trường',
    'description' => '...',
    // ... các field khác
]);

// $result['slug'] sẽ chứa slug: "phong-tro-dep-gan-truong"
```

### Tạo link đến bài viết
```php
// Cách 1: Sử dụng slug (recommended)
<a href="/<?php echo $post['slug']; ?>">Xem chi tiết</a>

// Cách 2: Sử dụng ID (vẫn hoạt động, sẽ redirect sang slug)
<a href="detail.php?id=<?php echo $post['id']; ?>">Xem chi tiết</a>
```

### Lấy bài viết theo slug
```php
$postModel = new Post();
$post = $postModel->findBySlug('phong-tro-gan-fpt');
```

## Lưu ý quan trọng

### 1. SEO-Friendly
- URL ngắn gọn, dễ nhớ
- Chứa từ khóa liên quan đến nội dung
- Thân thiện với search engines

### 2. Unique Slugs
- Hệ thống tự động đảm bảo slug là duy nhất
- Nếu slug đã tồn tại, sẽ thêm số vào cuối
  - Ví dụ: `phong-tro-gan-fpt`, `phong-tro-gan-fpt-1`, `phong-tro-gan-fpt-2`

### 3. Backward Compatibility
- URL cũ (detail.php?id=X) vẫn hoạt động
- Tự động redirect 301 sang URL mới với slug
- Không làm hỏng các link cũ trong hệ thống

### 4. Character Support
- Hỗ trợ chuyển đổi ký tự tiếng Việt có dấu
- Chỉ giữ lại: a-z, 0-9, và dấu gạch ngang (-)
- Tự động chuyển về chữ thường

## Testing

### Test các URL sau:
1. ✅ http://localhost:3000/phong-tro-gan-fpt (pretty URL)
2. ✅ http://localhost:3000/phong-tro-gan-viet-han (pretty URL)
3. ✅ http://localhost:3000/Views/posts/detail.php?id=16 (old URL - should redirect)
4. ✅ http://localhost:3000/Views/posts/detail.php?slug=phong-tro-gan-fpt (direct slug)

## Troubleshooting

### Lỗi: 404 Not Found
- Kiểm tra file `.htaccess` có tồn tại
- Đảm bảo mod_rewrite được bật trên Apache
- Kiểm tra `AllowOverride All` trong cấu hình Apache

### Lỗi: Slug không unique
- Function `getUniqueSlug()` sẽ tự động thêm số vào cuối
- Không cần xử lý thêm

### Lỗi: Không tìm thấy bài viết
- Kiểm tra slug trong database
- Chạy lại migration nếu cần: `php add_slug_column.php`

## Migration Script
Nếu cần chạy lại migration:
```bash
php add_slug_column.php
```

Script này sẽ:
1. Thêm cột `slug` vào bảng `posts`
2. Generate slug cho tất cả bài viết hiện có
3. Hiển thị kết quả migration

---
**Ngày cập nhật**: 15/12/2025
**Phiên bản**: 1.0
