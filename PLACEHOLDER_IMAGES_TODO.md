# 📸 DANH SÁCH PLACEHOLDER IMAGES CẦN THAY THẾ

**Tổng số:** 26 placeholder images sử dụng `via.placeholder.com`

---

## 📋 Chi tiết từng file

### 1. `index.php` (1 image)
```
Dòng 531:
<img src="<?php echo htmlspecialchars($post['image'] ?? 'https://via.placeholder.com/400x250'); ?>"
```
**Thay thế:** Dùng URL ảnh thực tế hoặc `/assets/images/default-post.jpg`

---

### 2. `Views/chat/chat.php` (10 images)

**Conversation avatars (3 images):**
```
Dòng 352: <img src="https://via.placeholder.com/50/667eea/ffffff?text=A"
Dòng 364: <img src="https://via.placeholder.com/50/764ba2/ffffff?text=B"
Dòng 375: <img src="https://via.placeholder.com/50/3b82f6/ffffff?text=C"
```

**User avatar (1 image):**
```
Dòng 390: <img src="https://via.placeholder.com/45/667eea/ffffff?text=A"
```

**Message avatars (5 images):**
```
Dòng 408: <img src="https://via.placeholder.com/40/667eea/ffffff?text=A"
Dòng 418: <img src="https://via.placeholder.com/40/3b82f6/ffffff?text=Me"
Dòng 428: <img src="https://via.placeholder.com/40/667eea/ffffff?text=A"
Dòng 438: <img src="https://via.placeholder.com/40/3b82f6/ffffff?text=Me"
Dòng 482: <img src="https://via.placeholder.com/40/3b82f6/ffffff?text=Me"
```

**Đề xuất:** Thay bằng `/assets/images/user-avatar.jpg` hoặc `default-user-{id}.jpg`

---

### 3. `Views/user/my-posts.php` (3 images)

```
Dòng 164: <img src="https://via.placeholder.com/200x150/667eea/ffffff?text=Post+1"
Dòng 205: <img src="https://via.placeholder.com/200x150/764ba2/ffffff?text=Post+2"
Dòng 242: <img src="https://via.placeholder.com/200x150/3b82f6/ffffff?text=Post+3"
```

**Đề xuất:** Thay bằng `/assets/images/post-default.jpg`

---

### 4. `Views/posts/list.php` (3 images)

```
Dòng 474: <img src="https://via.placeholder.com/400x250/667eea/ffffff?text=Phong+Tro+1"
Dòng 509: <img src="https://via.placeholder.com/400x250/764ba2/ffffff?text=Can+Ho+Mini"
Dòng 544: <img src="https://via.placeholder.com/400x250/3b82f6/ffffff?text=Phong+SV"
```

**Đề xuất:** Thay bằng `/assets/images/room-default.jpg`

---

### 5. `Views/posts/detail.php` (9 images)

**Main image (1 image):**
```
Dòng 377: <img src="https://via.placeholder.com/1200x600/667eea/ffffff?text=Main+Image"
```

**Thumbnails (4 images):**
```
Dòng 379: <img src="https://via.placeholder.com/300x200/667eea/ffffff?text=1"
Dòng 380: <img src="https://via.placeholder.com/300x200/764ba2/ffffff?text=2"
Dòng 381: <img src="https://via.placeholder.com/300x200/3b82f6/ffffff?text=3"
Dòng 382: <img src="https://via.placeholder.com/300x200/8b5cf6/ffffff?text=4"
```

**Landlord avatar (1 image):**
```
Dòng 501: <img src="https://via.placeholder.com/60/667eea/ffffff?text=A"
```

**Reviewer avatars (2 images):**
```
Dòng 560: <img src="https://via.placeholder.com/48/3b82f6/ffffff?text=B"
```

---

## 🎯 Cách thay thế nhanh

### Option 1: Dùng ảnh default local
Tạo các file trong `/assets/images/`:
- `default-post.jpg` (400x250)
- `default-room.jpg` (1200x600)
- `default-room-thumb.jpg` (300x200)
- `default-user-avatar.jpg` (50x50)
- `default-landlord-avatar.jpg` (60x60)

Sau đó replace:
```bash
# Tìm và thay thế tất cả placeholder URLs
find . -name "*.php" -type f -exec sed -i 's|https://via\.placeholder\.com/400x250|/fullstack/assets/images/default-room.jpg|g' {} \;
find . -name "*.php" -type f -exec sed -i 's|https://via\.placeholder\.com/[0-9x].*|/fullstack/assets/images/default-post.jpg|g' {} \;
```

### Option 2: Dùng URL ảnh online khác
- Pixabay: https://pixabay.com/api/
- Unsplash: https://api.unsplash.com/
- Pexels: https://www.pexels.com/api/

### Option 3: Dùng gravatar cho user avatars
```php
// Thay cho https://via.placeholder.com/50/667eea/ffffff?text=A
<img src="https://www.gravatar.com/avatar/<?php echo md5($user['email']); ?>?s=50&d=identicon">
```

---

## ✅ Checklist thay thế

- [ ] Tạo thư mục `/assets/images/`
- [ ] Tạo/upload 5 ảnh default
- [ ] Chọn phương pháp thay thế
- [ ] Cập nhật tất cả 26 placeholder URLs
- [ ] Test toàn bộ site

---

**Ghi chú:** Các image placeholder không ảnh hưởng đến chức năng website, chỉ là UI/UX. Ưu tiên sửa link/path sai trước, placeholder images sau.
