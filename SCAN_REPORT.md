# 📋 BÁNG CÁO QUÉT DỰ ÁN PHP - LINK/PATH SAI & FILE THIẾU

**Ngày quét:** 21-11-2025  
**Dự án:** Tìm Trọ Sinh Viên (fullstack)  
**Đường dẫn:** d:\baitapcuoiky\fullstack

---

## 1️⃣ DANH SÁCH FILE THIẾU - CẤP ĐỘ CAO

### A. Views bị thiếu (ưu tiên cao):
| File | Đường dẫn | Lỗi | Dòng |
|------|-----------|-----|------|
| **profile.php** | `Views/user/profile.php` | Link được gọi nhưng file không tồn tại | `index.php:426`, `Views/posts/list.php:359`, `Views/posts/detail.php:358` |
| **index.php** | `Views/chat/index.php` | Link được gọi nhưng file không tồn tại | `Views/posts/detail.php:511` |
| **Folder home** | `Views/home/` | Folder tồn tại nhưng trống, không có index.php | - |

### B. Controllers cần kiểm tra:
Tất cả Controllers hiện tại có đầy đủ (AuthController, FavoriteController, NotificationController, PostController)

### C. Models cần kiểm tra:
Tất cả Models hiện tại có đầy đủ (User, Post, Favorite, Notification, Category)

---

## 2️⃣ DANH SÁCH LINK/PATH SAI - CHI TIẾT

### 🔴 LỖI REDIRECT PATH SAI

**File:** `Views/posts/create.php`  
**Dòng:** 5  
**Sai:** `redirect('/fullstack/Views/home/index.php');`  
**Vấn đề:** Path tuyệt đối sai, Views/home/index.php không tồn tại  
**Nên sửa thành:** `redirect('../../index.php');` hoặc `redirect('../../Views/home/index.php');` (sau khi tạo file)

**File:** `Views/admin/dashboard.php`  
**Dòng:** 340  
**Sai:** `href="<?php echo '/fullstack/Controllers/AuthController.php?action=logout'; ?>"`  
**Vấn đề:** Path tuyệt đối `/fullstack/` không có ý nghĩa trong URL (nên là relative)  
**Nên sửa thành:** `href="../../Controllers/AuthController.php?action=logout"`

---

### 🟡 LỖI LINK CHAT SAI (Views/chat/index.php không tồn tại)

**File:** `Views/posts/detail.php`  
**Dòng:** 511  
```php
<a href="../chat/index.php" class="btn btn-outline">
```
**Vấn đề:** File `Views/chat/index.php` không tồn tại  
**Hiện tại:** Chỉ có `Views/chat/chat.php`  
**Nên sửa thành:** `href="../chat/chat.php"`

---

### 🟡 LỖI PROFILE PAGE SAI (Views/user/profile.php không tồn tại)

| File | Dòng | Link hiện tại | Vấn đề |
|------|------|--------------|--------|
| `index.php` | 426 | `href="Views/user/profile.php"` | File không tồn tại |
| `Views/posts/list.php` | 359 | `href="../user/profile.php"` | File không tồn tại |
| `Views/posts/detail.php` | 358 | `href="../user/profile.php"` | File không tồn tại |
| `Views/user/notifications.php` | 205 | `href="profile.php"` | File không tồn tại |

**Nên sửa thành:** `href="my-posts.php"` (thay thế tạm) hoặc tạo `profile.php` mới

---

## 3️⃣ DANH SÁCH PLACEHOLDER IMAGE (via.placeholder.com)

### Tất cả placeholder images cần thay thế:

| File | Dòng | Placeholder | Số lần |
|------|------|-------------|--------|
| `index.php` | 531 | `https://via.placeholder.com/400x250` | 1 |
| `Views/chat/chat.php` | 352, 364, 375, 390, 408, 418, 428, 438, 482 | Multiple variants | 10 |
| `Views/user/my-posts.php` | 164, 205, 242 | `https://via.placeholder.com/200x150/...` | 3 |
| `Views/posts/list.php` | 474, 509, 544 | `https://via.placeholder.com/400x250/...` | 3 |
| `Views/posts/detail.php` | 377, 379-382, 501, 560 | `https://via.placeholder.com/...` | 9 |

**Tổng cộng:** 26 placeholder images cần xử lý

---

## 4️⃣ KIỂM TRA ĐỊA CHỈ (Hồ Chí Minh vs Đà Nẵng)

✅ **TỐTỆ:** Địa chỉ Đã Nẵng là CHÍNH XÁC trong form  
**File:** `Views/posts/create.php` (Dòng 383-402)  
**Quận được hỗ trợ:**
- Quận Hải Châu ✓
- Quận Thanh Khê ✓
- Quận Cẩm Lệ ✓
- Quận Ngũ Hành Sơn ✓
- Quận Sơn Trà ✓
- Quận Liên Chiểu ✓
- Huyện Hòa Vang ✓

**Kết luận:** Không tìm thấy TPHCM/Hồ Chí Minh nào trong project (Đã Nẵng là chính xác)

---

## 5️⃣ KIỂM TRA FUNCTION JavaScript

### toggleFavorite() function:
✅ **Tồn tại:** `assets/js/main.js` (Dòng 60)  
**Được gọi từ:**
- `Views/posts/detail.php` (dòng 514): `onclick="toggleFavorite(1, this)"`

---

## 6️⃣ KIỂM TRA ACTION CONTROLLER

### AuthController logout action:
✅ **Tồn tại:** `Controllers/AuthController.php` (Dòng 116, 131-132)  
**Được gọi từ:**
- `index.php` (dòng 427)
- `Views/auth/login.php` (không có)
- `Views/auth/register.php` (không có)
- `Views/chat/chat.php` (dòng 325)
- `Views/user/my-posts.php` (dòng 136)
- `Views/user/favorites.php` (dòng 204)
- `Views/user/notifications.php` (dòng 206)
- `Views/posts/create.php` (dòng 258)
- `Views/posts/list.php` (dòng 360)
- `Views/posts/detail.php` (dòng 359)
- `Views/admin/dashboard.php` (dòng 340)

---

## 7️⃣ TÓNG HỢP VẤN ĐỀ CẤP ĐỘ

### 🔴 CRITICAL (Sai sẽ lỗi ngay):
1. `Views/user/profile.php` - Không tồn tại (5 link đến nó)
2. `Views/chat/index.php` - Không tồn tại (1 link đến nó)
3. Redirect path sai trong `Views/posts/create.php` dòng 5

### 🟡 HIGH (Nên sửa):
1. 26 placeholder images cần thay thế
2. Path absolute sai trong `Views/admin/dashboard.php` dòng 340
3. `Views/home/` folder trống, chưa có index.php

### 🟢 LOW (Lưu ý):
1. Các CSS/JS path bình thường (dùng relative `../../`)
2. Các link form action có đúng path

---

## 📊ĐỀ XUẤT HÀNH ĐỘNG

### Ưu tiên 1: Tạo file thiếu
```
1. Views/user/profile.php (tạo mới)
2. Views/home/index.php (tạo mới hoặc xóa redirect)
3. Views/chat/index.php (tạo hoặc sửa link thành chat.php)
```

### Ưu tiên 2: Sửa link sai
```
1. Sửa profile.php link → my-posts.php (tạm thời)
2. Sửa chat/index.php → chat.php
3. Sửa redirect path trong create.php
4. Sửa admin logout path
```

### Ưu tiên 3: Thay placeholder images
```
- 26 images via.placeholder.com cần thay thế bằng ảnh thực tế
- Hoặc dùng image default local
```

---

**Báo cáo được tạo tự động bởi quét dự án**
