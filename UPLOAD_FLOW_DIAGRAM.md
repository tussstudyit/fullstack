# 🚀 Hướng Dẫn Upload Nhiều Ảnh - Thứ Tự Chi Tiết

## ⚡ Quy Trình 5 Bước

### 🖼️ BỨC 1: Chọn Ảnh (Frontend)
- Bạn chọn 3 ảnh: `ảnh1.jpg`, `ảnh2.jpg`, `ảnh3.jpg`
- File `create.php` gọi `handleImageSelect()`
- Các ảnh được thêm vào array `uploadedImages[]`:
  ```javascript
  uploadedImages = [
    { name: 'ảnh1.jpg', size: 500KB },  // [0] ← ẢNH BÌA
    { name: 'ảnh2.jpg', size: 480KB },  // [1]
    { name: 'ảnh3.jpg', size: 520KB }   // [2]
  ]
  ```

### 📝 BƯỚC 2: Gửi Form (Frontend)
- Console log: `uploadedImages count: 3`
- File form gửi POST tới `PostController.php`
- PostController tạo bài đăng mới
- **Response**: `{ success: true, post_id: 4 }`
- Console log: `Post created with ID: 4`

### 📤 BƯỚC 3: Upload Ảnh (Frontend)
- Hàm `uploadPostImages(4)` được gọi
- **Vòng lặp từng ảnh**:
  ```
  [0] photo1.jpg → FormData.append('images', photo1)
  [1] photo2.jpg → FormData.append('images', photo2)
  [2] photo3.jpg → FormData.append('images', photo3)
  ```
- POST tới: `api/upload-image.php?action=upload-multiple`

### ⚙️ BƯỚC 4: Xử Lý Server (ImageController.php)
- Hàm `uploadMultipleImages(postId=4, $_FILES['images'])`
- **Chuyển đổi $_FILES**:
  ```php
  $_FILES['images'] = [
    'name' => [0 => 'photo1.jpg', 1 => 'photo2.jpg', 2 => 'photo3.jpg'],
    'tmp_name' => [0 => '/tmp/xxx', 1 => '/tmp/yyy', 2 => '/tmp/zzz'],
    'type' => [0 => 'image/jpeg', 1 => 'image/jpeg', 2 => 'image/jpeg'],
    'size' => [0 => 500000, 1 => 480000, 2 => 520000]
  ]
  ```
  ⬇️ Chuyển thành array:
  ```php
  $fileArray = [
    ['name' => 'photo1.jpg', 'tmp_name' => '/tmp/xxx', 'order' => 0],
    ['name' => 'photo2.jpg', 'tmp_name' => '/tmp/yyy', 'order' => 1],
    ['name' => 'photo3.jpg', 'tmp_name' => '/tmp/zzz', 'order' => 2]
  ]
  ```

- **Xử lý từng file**:
  ```
  i=0: photo1.jpg
    ✓ isPrimary = true (imageIndex === 0)
    ✓ Tạo tên file: post_4_1764613181_abc123.jpg
    ✓ Move file từ /tmp/xxx → uploads/post_4_...jpg
    ✓ INSERT vào DB: is_primary = 1 ✓
  
  i=1: photo2.jpg
    ✓ isPrimary = false (imageIndex !== 0)
    ✓ Tạo tên file: post_4_1764613182_def456.jpg
    ✓ Move file
    ✓ INSERT vào DB: is_primary = 0
  
  i=2: photo3.jpg
    ✓ isPrimary = false
    ✓ Tạo tên file: post_4_1764613183_ghi789.jpg
    ✓ Move file
    ✓ INSERT vào DB: is_primary = 0
  ```

- **Response**:
  ```json
  {
    "success": true,
    "message": "Tất cả 3 ảnh upload thành công (ảnh đầu tiên là ảnh bìa)",
    "uploaded": [
      { "filename": "post_4_1764613181_abc123.jpg", "isPrimary": true },
      { "filename": "post_4_1764613182_def456.jpg", "isPrimary": false },
      { "filename": "post_4_1764613183_ghi789.jpg", "isPrimary": false }
    ]
  }
  ```

### 🎬 BƯỚC 5: Hiển Thị Ảnh (my-posts.php)
- Truy vấn DB:
  ```sql
  SELECT image_url FROM post_images
  WHERE post_id = 4 AND is_primary = TRUE
  LIMIT 1
  ```
- **Result**: `post_4_1764613181_abc123.jpg`
- URL ảnh bìa:
  ```
  http://localhost:3000/fullstack/uploads/post_4_1764613181_abc123.jpg
  ```

## 🔄 Công Thức Chính

| Bước | Xử Lý | Kết Quả |
|------|-------|--------|
| 1 | Chọn 3 ảnh | uploadedImages[0,1,2] ✓ |
| 2 | POST form | Post ID = 4 ✓ |
| 3 | FormData append | 3 files trong FormData ✓ |
| 4 | uploadMultipleImages | [0]=primary, [1],[2]=not primary ✓ |
| 5 | getPrimaryImage | Trả về ảnh [0] ✓ |

## 💡 Chi Tiết Quan Trọng

### Ảnh Bìa (Primary Image)
- **Định nghĩa**: Ảnh đầu tiên được chọn (index 0)
- **Đánh dấu**: `is_primary = 1` trong DB
- **Hiển thị**: Trên my-posts.php, detail.php, list.php

### Tên File Quy Tắc
```
post_{postId}_{timestamp}_{uniqueId}.{ext}
Ví dụ: post_4_1764613181_abc123.jpg
       ├─ post_4: ID của bài đăng
       ├─ 1764613181: Timestamp
       ├─ abc123: Unique ID
       └─ jpg: File type
```

### Đường Dẫn Ảnh
```
Database: post_4_1764613181_abc123.jpg (chỉ filename)
URL: getBasePath() . '/uploads/' . $filename
    = /fullstack/uploads/post_4_1764613181_abc123.jpg
Full: http://localhost:3000/fullstack/uploads/post_4_1764613181_abc123.jpg
```

## 🧪 Test Nhanh

```bash
# 1. Mở create.php
http://localhost:3000/fullstack/Views/posts/create.php

# 2. Điền form → Bước 4 chọn 2 ảnh
# 3. Mở Console (F12)
# 4. Submit form
# 5. Xem console log

Kỳ vọng:
✓ uploadedImages count: 2
✓ Appending image 0: file1.jpg (ảnh bìa)
✓ Appending image 1: file2.jpg
✓ Upload response: success true
✓ Tất cả 2 ảnh upload thành công
```

## 🐛 Troubleshoot

| Vấn Đề | Nguyên Nhân | Fix |
|--------|-----------|-----|
| uploadedImages = 0 | Không chọn ảnh | Chọn ảnh ở Step 4 |
| uploadedImages = 1 | Array chỉ có 1 item | Kiểm tra handleImageSelect() |
| Ảnh bìa sai | isPrimary logic sai | Kiểm tra imageIndex === 0 |
| 404 Not Found | URL sai | Kiểm tra getBasePath() + /uploads/ |
| Chỉ 1 ảnh upload | FormData.append() sai | Kiểm tra vòng lặp append |

## 📋 Checklist Upload Thành Công

- [ ] Chọn 2+ ảnh
- [ ] Console hiển thị đúng count
- [ ] Không có lỗi JSON parse
- [ ] API response: success = true
- [ ] Cả 2 ảnh xuất hiện trong DB
- [ ] Ảnh đầu tiên có is_primary = 1
- [ ] my-posts.php hiển thị đúng ảnh bìa
- [ ] Không có lỗi 404
