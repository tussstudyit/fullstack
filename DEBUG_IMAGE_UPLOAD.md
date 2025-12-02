# 🔍 Hướng Dẫn Debug Upload Nhiều Ảnh

## 📋 Tóm Tắt Vấn Đề
- **Lỗi**: Khi upload nhiều ảnh, chỉ 1 ảnh được lưu hoặc ảnh bìa không phải ảnh đầu tiên
- **Ảnh bìa**: Phải là ảnh thứ 1 được chọn, không phải ảnh thứ 2 hay thứ 3

## 🔧 Quy Trình Upload Ảnh (Thứ Tự Chi Tiết)

### 1️⃣ Chọn Ảnh trên Frontend (create.php)
```
Người dùng chọn 3 ảnh: photo1.jpg, photo2.jpg, photo3.jpg
        ↓
handleImageSelect() được gọi
        ↓
Vòng lặp qua từng file:
  [0] photo1.jpg → uploadedImages[0] (ảnh bìa)
  [1] photo2.jpg → uploadedImages[1]
  [2] photo3.jpg → uploadedImages[2]
        ↓
console.log: 'Total images selected: 3'
```

### 2️⃣ Submit Form (create.php)
```
Nhấn Gửi
        ↓
validateForm() kiểm tra dữ liệu
        ↓
POST tới PostController.php
        ↓
PostController.create() tạo bài đăng
        ↓
Response: { success: true, post_id: 4 }
        ↓
console.log: 'Post created with ID: 4'
```

### 3️⃣ Upload Ảnh (uploadPostImages)
```
uploadPostImages(post_id=4)
        ↓
For loop i=0 to 2:
  [0] Appending image 0: photo1.jpg (ảnh bìa)
  [1] Appending image 1: photo2.jpg
  [2] Appending image 2: photo3.jpg
        ↓
FormData gồm:
  - post_id: 4
  - images: photo1.jpg (ảnh bìa)
  - images: photo2.jpg
  - images: photo3.jpg
        ↓
POST tới api/upload-image.php?action=upload-multiple
```

### 4️⃣ Server Processing (ImageController.php)
```
uploadMultipleImages(post_id=4, files=[...])
        ↓
Loop qua từng file:
  [0] photo1.jpg:
      - isPrimary = true ✓ (ảnh bìa)
      - uploadImage() → file moved
      - addImage() → INSERT into DB with is_primary=1
      ↓
  [1] photo2.jpg:
      - isPrimary = false
      - uploadImage() → file moved
      - addImage() → INSERT into DB with is_primary=0
      ↓
  [2] photo3.jpg:
      - isPrimary = false
      - uploadImage() → file moved
      - addImage() → INSERT into DB with is_primary=0
        ↓
Response: { success: true, uploaded: [3 items] }
```

### 5️⃣ Hiển Thị ảnh (my-posts.php)
```
Query: SELECT image_url FROM post_images 
       WHERE post_id=4 AND is_primary=TRUE
        ↓
Result: photo1.jpg (ảnh bìa)
        ↓
Hiển thị: uploads/photo1.jpg
```

## 🧪 Cách Test

### A. Test 1: Upload 2 ảnh
1. Vào http://localhost:3000/fullstack/Views/posts/create.php
2. Điền đầy đủ form (Step 1-4)
3. **Step 4**: Chọn 2 ảnh: `pic1.jpg` và `pic2.jpg`
4. Xem console (F12):
   ```
   ✓ Total images selected: 2
   ✓ Appending image 0: pic1.jpg
   ✓ Appending image 1: pic2.jpg
   ```
5. Submit form
6. Xem console kết quả upload:
   ```
   ✓ Tất cả 2 ảnh upload thành công (ảnh đầu tiên là ảnh bìa)
   ```
7. Vào my-posts.php → Xem bài đăng
8. Ảnh bìa phải là `pic1.jpg` ✓

### B. Test 2: Upload 3 ảnh + Kiểm tra DB
1. Upload 3 ảnh: `a.jpg`, `b.jpg`, `c.jpg`
2. Mở browser DevTools → Console (F12)
3. Tìm dòng: `✓ Tất cả 3 ảnh upload thành công`
4. Kiểm tra URL của ảnh bìa trên my-posts.php
5. Phải là `a.jpg` (ảnh đầu tiên)

### C. Test 3: Kiểm tra Database
```php
// Mở check-images.php
http://localhost:3000/fullstack/check-images.php

Kết quả mong đợi:
Post ID 4:
  [0] post_4_1764613181_xxx.jpg → is_primary = 1 ✓
  [1] post_4_1764613182_yyy.jpg → is_primary = 0 ✓
  [2] post_4_1764613183_zzz.jpg → is_primary = 0 ✓
```

## 📊 Console Output Chi Tiết

Khi submit form, bạn sẽ thấy:

```
=== FORM SUBMIT START ===
Thời gian submit: 2/12/2025, 10:30:45 AM
uploadedImages count: 2
Các ảnh đã chọn:
  1. photo1.jpg (512.5KB)
  2. photo2.jpg (480.3KB)
Chế độ: ĐĂNG TIN MỚI

PostController response:
Success: true
Message: Bài đăng được tạo thành công
Post ID: 4

Bắt đầu upload ảnh...

=== uploadPostImages START ===
postId: 4
uploadedImages count: 2
uploadedImages array: [File, File]

Appending images to FormData...
  [0] Ảnh 1/2:
      - Tên: photo1.jpg
      - Kích thước: 512.50 MB
      - Loại: image/jpeg
      - Sẽ là ảnh bìa: CÓ (ảnh đầu tiên)
  [1] Ảnh 2/2:
      - Tên: photo2.jpg
      - Kích thước: 480.30 MB
      - Loại: image/jpeg
      - Sẽ là ảnh bìa: KHÔNG

Sending to API: ../../api/upload-image.php?action=upload-multiple

API response status: 200

Upload response received:
Success: true
Message: Tất cả 2 ảnh upload thành công (ảnh đầu tiên là ảnh bìa)
Uploaded images count: 2

✓ Upload thành công!

✓ Quá trình hoàn tất thành công!

=== FORM SUBMIT END ===
```

## 🔍 Nếu Có Lỗi

### Lỗi 1: "uploadedImages count: 0"
**Nguyên nhân**: Bạn quên chọn ảnh ở Step 4
**Fix**: Chọn ảnh trước khi submit form

### Lỗi 2: "uploadedImages count: 1" (nhưng chọn 2 ảnh)
**Nguyên nhân**: Ảnh thứ 2 không được thêm vào array
**Fix**: Kiểm tra handleImageSelect() logic

### Lỗi 3: Ảnh bìa không phải ảnh đầu tiên
**Nguyên nhân**: Logic isPrimary sai trong uploadMultipleImages()
**Fix**: Kiểm tra `isPrimary = (imageIndex === 0)`

### Lỗi 4: 404 Not Found khi load ảnh
**Nguyên nhân**: Đường dẫn URL sai
**Check**:
```
Ảnh lưu tại: uploads/post_4_1764613181_xxx.jpg
URL phải là: http://localhost:3000/fullstack/uploads/post_4_1764613181_xxx.jpg
```

## 🚀 Cách Xem Error Log Server

Mở terminal và chạy:
```powershell
# Xem PHP error log
Get-Content "php_error.log" -Tail 50

# Hoặc kiểm tra file logs
ls d:\baitapcuoiky\fullstack\uploads\
```

## 📝 Các File Liên Quan

| File | Công Năng |
|------|-----------|
| `Views/posts/create.php` | Frontend form, handleImageSelect(), uploadPostImages() |
| `Controllers/ImageController.php` | Server: uploadMultipleImages(), uploadImage() |
| `Controllers/PostController.php` | Tạo bài đăng POST handler |
| `Models/PostImage.php` | Database: addImage(), getPrimaryImage() |
| `api/upload-image.php` | API endpoint cho upload |
| `Views/user/my-posts.php` | Hiển thị ảnh bìa từ getPrimaryImage() |

## ✅ Checklist Fix

- [x] ImageController.uploadMultipleImages() - Thêm logging chi tiết
- [x] ImageController.uploadImage() - Thêm logging chi tiết
- [x] create.php uploadPostImages() - Format console.log đẹp hơn
- [x] create.php form submit - Log từng bước chi tiết
- [x] Đảm bảo ảnh đầu tiên (imageIndex === 0) được set isPrimary=true
- [x] Xóa code trùng lặp ở cuối create.php

## 🎯 Kết Quả Kỳ Vọng

Sau khi fix, khi upload 2 ảnh:
1. Console log hiển thị đầy đủ thông tin
2. Cả 2 ảnh được upload lên server
3. Ảnh đầu tiên được đánh dấu `is_primary = 1`
4. Trên my-posts.php hiển thị đúng ảnh bìa
5. Không có lỗi 404 khi load ảnh
