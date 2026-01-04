# 🧪 HƯỚNG DẪN TEST NOTIFICATION CHAT (ĐÃ SỬA - V2)

## ✅ CÁC LỖI ĐÃ SỬA (CẬP NHẬT)

### **Lỗi 1: WebSocket không cập nhật navbar badge** ✅
- **File:** `Views/chat/chat.php`
- **Sửa:** Thêm hàm `updateNavbarChatBadge()` để cập nhật badge realtime
- **Dòng:** 1063-1098

### **Lỗi 2: Polling API dùng hardcoded path** ✅
- **File:** `assets/js/main.js`
- **Sửa:** Thêm hàm `getApiPath()` để tính path động
- **Dòng:** 177-193

### **Lỗi 3: Không đồng bộ giữa WebSocket và Polling** ✅
- **Sửa:** Gọi `updateNavbarChatBadge()` trong `updateTotalUnreadInSidebar()`
- **Dòng:** 1059

### **🆕 Lỗi 4: Badge biến mất khi chuyển trang** ✅ FIXED!
- **Nguyên nhân:** 
  - PHP render badge ban đầu
  - JavaScript không force refresh ngay khi load
  - Badge PHP và badge JS conflict với nhau
- **Sửa:**
  - Force polling chạy ngay sau 100ms khi load trang
  - Xóa TẤT CẢ badges cũ trước khi tạo mới
  - Thêm ID unique cho badge (`chat-navbar-badge`)
  - Gộp tất cả DOMContentLoaded listeners thành 1

---

## 🎯 FLOW MỚI (ĐÃ FIX V2)

```
Tin nhắn mới gửi đến
    ↓
WebSocket nhận (ws.onmessage)
    ↓
✅ Cập nhật: Sidebar conversation badge
✅ Cập nhật: Sidebar header totalUnreadBadge
✅ GỌI: updateNavbarChatBadge() ← MỚI!
    ↓
✅ Navbar badge cập nhật NGAY LẬP TỨC (realtime)
    ↓
Polling vẫn chạy (2 giây) để backup
    ↓
✅ Polling fetch với DYNAMIC PATH ← MỚI!
    ↓
✅ Badge được đồng bộ từ cả 2 nguồn
```

---

## 📋 CÁC BƯỚC TEST

### **Bước 1: Chuẩn bị**
```bash
# 1. Khởi động WebSocket Server
cd d:\baitapcuoiky\fullstack
php websocket/server.php

# 2. Khởi động XAMPP (Apache + MySQL)
# 3. Mở trình duyệt: http://localhost:3000
```

### **Bước 2: Test Realtime Badge (WebSocket)**

**Test Case 1: Nhận tin nhắn mới khi ĐANG Ở TRANG CHAT**

1. Đăng nhập vào 2 tài khoản khác nhau (2 trình duyệt/2 tab incognito)
2. User A mở trang chat: `http://localhost:3000/Views/chat/chat.php`
3. User B mở trang khác (VD: trang chủ): `http://localhost:3000/`
4. User A gửi tin nhắn cho User B
5. **✅ KIỂM TRA:**
   - Badge "Tin nhắn" trên navbar của User B xuất hiện NGAY LẬP TỨC
   - Badge hiển thị số 1 (hoặc tăng lên nếu đã có tin nhắn chưa đọc)
   - Console log: `✅ Navbar badge updated: 1`

**Test Case 2: Nhận tin nhắn mới khi KHÔNG Ở TRANG CHAT**

1. User B đang ở trang `Views/posts/list.php`
2. User A gửi tin nhắn cho User B
3. **✅ KIỂM TRA:**
   - Badge "Tin nhắn" trên navbar của User B xuất hiện sau tối đa 2 giây
   - Console log: `✅ Polling updated navbar badge: 1`

**Test Case 3: Đọc tin nhắn thì badge biến mất**

1. User B có badge "Tin nhắn" (số 1)
2. User B click vào "Tin nhắn" và mở conversation
3. **✅ KIỂM TRA:**
   - Badge biến mất NGAY KHI mở conversation
   - Console log: `🗑️ Navbar badge removed (no unread messages)`

**🆕 Test Case 4: Badge persist khi chuyển trang**

1. User A gửi tin nhắn cho User B
2. User B thấy badge "Tin nhắn" xuất hiện (số 1)
3. User B **CHUYỂN TRANG** (VD: từ trang chủ → trang danh sách trọ)
4. **✅ KIỂM TRA:**
   - Badge vẫn hiển thị SAU KHI chuyển trang
   - Console log: `🚀 Main.js initializing...`
   - Console log: `🔔 Initializing chat badge polling...`
   - Console log: `✅ Polling updated navbar badge: 1` (sau 100ms)
   - Badge KHÔNG biến mất
5. User B chuyển sang trang khác (VD: trang yêu thích)
6. **✅ KIỂM TRA:**
   - Badge vẫn hiển thị
   - Polling tiếp tục chạy mỗi 3 giây

**🆕 Test Case 5: Badge update chính xác khi chuyển trang**

1. User B có 2 conversations chưa đọc → badge hiển thị `2`
2. User B chuyển sang trang khác
3. **✅ KIỂM TRA:**
   - Badge vẫn hiển thị `2`
   - Console log: `✅ Polling updated navbar badge: 2`
4. Trong khi đó, User B mở trang chat ở tab khác và đọc 1 conversation
5. Quay lại tab đầu tiên, đợi 3 giây
6. **✅ KIỂM TRA:**
   - Badge tự động cập nhật từ `2` → `1`
   - Console log: `✅ Polling updated navbar badge: 1`

---

### **Bước 3: Test Dynamic Path (Polling API)**

**Test Case 4: Polling hoạt động ở mọi trang**

Mở console (F12) và kiểm tra các trang sau:

1. **Trang chủ** (`/index.php`):
   ```
   API path: api/get-unread-conversations.php
   Status: ✅ 200 OK
   ```

2. **Trang danh sách** (`/Views/posts/list.php`):
   ```
   API path: ../../api/get-unread-conversations.php
   Status: ✅ 200 OK
   ```

3. **Trang chat** (`/Views/chat/chat.php`):
   ```
   API path: ../../api/get-unread-conversations.php
   Status: ✅ 200 OK
   ```

4. **Trang favorites** (`/Views/user/favorites.php`):
   ```
   API path: ../../api/get-unread-conversations.php
   Status: ✅ 200 OK
   ```

**❌ KHÔNG ĐƯỢC THẤY:** 
- `⚠️ API response not OK: 404`
- `❌ Error fetching unread count`

---

### **Bước 4: Test Đồng Bộ WebSocket + Polling**

**Test Case 5: WebSocket disconnect → Polling backup**

1. User B đang ở trang chat
2. Tắt WebSocket server (`Ctrl+C`)
3. User A gửi tin nhắn (tin nhắn lưu vào database)
4. **✅ KIỂM TRA:**
   - Badge của User B vẫn cập nhật sau 2 giây (nhờ polling)
   - Console log: `✅ Polling updated navbar badge: 1`

**Test Case 6: Nhiều tin nhắn từ nhiều người**

1. User B nhận tin nhắn từ User A (conversation 1)
2. User B nhận tin nhắn từ User C (conversation 2)
3. **✅ KIỂM TRA:**
   - Badge hiển thị: `2` (tổng số conversations chưa đọc)
   - Khi đọc conversation 1 → Badge giảm xuống `1`
   - Khi đọc conversation 2 → Badge biến mất

---

## 🐛 DEBUG

### **Kiểm tra Console Logs**

**Khi nhận tin nhắn mới (WebSocket):**
```javascript
💬 Received message: {...}
✅ Message added to UI
✅ Navbar badge updated: 1
```

**Khi polling chạy:**
```javascript
✅ Polling updated navbar badge: 2
```

**Khi đọc tin nhắn:**
```javascript
🗑️ Navbar badge removed (no unread messages)
```

### **Kiểm tra Network Tab (F12)**

**Polling Request:**
```
GET /api/get-unread-conversations.php
Status: 200 OK
Response: {"count": 1}
```

**WebSocket Connection:**
```
WS ws://localhost:8080
Status: 101 Switching Protocols (Connected)
```

---

## 🔧 NẾU VẪN CÓ LỖI

### **Lỗi: Badge không cập nhật realtime**

1. Kiểm tra WebSocket đang chạy:
   ```bash
   php websocket/server.php
   ```

2. Kiểm tra console có thấy:
   ```
   ✅ WebSocket connected
   ✅ Authenticated as user X
   ```

3. Kiểm tra hàm `updateNavbarChatBadge()` có tồn tại trong [chat.php](Views/chat/chat.php#L1063)

### **Lỗi: Polling API 404**

1. Kiểm tra file tồn tại: `api/get-unread-conversations.php`
2. Kiểm tra console log:
   ```
   ⚠️ API response not OK: 404 /path/to/api
   ```
3. Kiểm tra hàm `getApiPath()` trong [main.js](assets/js/main.js#L163)

### **Lỗi: Badge không đồng bộ**

1. Clear cache trình duyệt (Ctrl+Shift+Delete)
2. Hard reload (Ctrl+F5)
3. Kiểm tra cả 2 nguồn:
   - WebSocket: `updateNavbarChatBadge()`
   - Polling: `updateNavbarBadgePolling()`

---

## 📊 KẾT QUẢ MONG ĐỢI

| Tình huống | Navbar Badge | Thời gian | Nguồn |
|------------|-------------|-----------|-------|
| Nhận tin nhắn mới (đang ở chat) | Xuất hiện ngay | <100ms | WebSocket |
| Nhận tin nhắn mới (không ở chat) | Xuất hiện | <2s | Polling |
| Đọc tin nhắn | Biến mất ngay | <100ms | WebSocket |
| WebSocket disconnect | Vẫn cập nhật | <2s | Polling |
| Nhiều conversations chưa đọc | Hiển thị tổng | Realtime | Cả 2 |

---

## ✅ CHECKLIST

- [ ] WebSocket server đang chạy
- [ ] Badge hiển thị realtime khi nhận tin nhắn mới
- [ ] Badge biến mất khi đọc tin nhắn
- [ ] Polling hoạt động ở mọi trang (không 404)
- [ ] Console không có error
- [ ] Badge đồng bộ giữa WebSocket và Polling
- [ ] Badge hiển thị đúng số lượng conversations chưa đọc

---

## 📝 GHI CHÚ

**3 thay đổi chính:**

1. **Thêm `updateNavbarChatBadge()`** trong chat.php
   - Cập nhật navbar badge realtime
   - Tự động xóa badge khi không còn tin nhắn chưa đọc

2. **Thêm `getApiPath()`** trong main.js
   - Tính toán path động dựa vào vị trí trang
   - Tránh 404 error trên các trang khác nhau

3. **Đồng bộ 2 hệ thống**
   - WebSocket cập nhật ngay lập tức
   - Polling backup mỗi 2 giây
   - Cả 2 đều gọi cùng 1 logic cập nhật badge
