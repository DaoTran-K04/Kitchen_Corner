# 🚀 HƯỚNG DẪN TRIỂN KHAI LÊN INFINITYFREE (MIỄN PHÍ 24/7)

Chào bạn! Để website **Kitchen Corner** chạy ổn định 24/24 với tên miền **gocbep.great-site.net**, hãy làm theo các bước sau:

## Bước 1: Đăng ký tài khoản và Tên miền
1. Truy cập [InfinityFree.com](https://www.infinityfree.com/) và nhấn **Sign Up**.
2. Sau khi xác thực email, nhấn **Create Account**.
3. Tại mục chọn tên miền:
   - Chọn **Subdomain**.
   - Ô bên trái nhập: `gocbep`
   - Ô bên phải chọn: `great-site.net`
4. Nếu tên này đã có người lấy, hãy thử `gocbep-dht` hoặc tên khác tương tự.
5. Nhấn **Create Account** để hoàn tất.

## Bước 2: Tạo Cơ sở dữ liệu (Database)
1. Trong dashboard của InfinityFree, tìm mục **MySQL Databases**.
2. Tạo một database mới (ví dụ: `if0_xxxxxx_gocbep`).
3. Ghi lại các thông tin: **MySQL Host**, **Username**, **Password**.
4. Nhấn nút **Admin** (hoặc mở phpMyAdmin) -> Chọn **Import** -> Tải file `database_backup.sql` từ máy bạn lên.

## Bước 3: Upload Code
1. Nén toàn bộ thư mục `Kitchen_Laravel` thành file `.zip` (Nhớ bao gồm cả thư mục `vendor`).
2. Vào mục **Online File Manager** trên host.
3. Mở thư mục **`htdocs`**. Xóa sạch các file có sẵn trong đó.
4. **Upload** file `.zip` của bạn lên đây và chọn **Extract** (Giải nén).
5. **Cấu hình file .env:**
   - Tìm file `.env.hosting.example` tôi đã tạo.
   - Đổi tên nó thành **`.env`**.
   - Điền thông tin Database bạn vừa lấy ở Bước 2 vào các dòng `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST`.

## Bước 4: Kiểm tra thành quả
Truy cập: [http://gocbep.great-site.net](http://gocbep.great-site.net)

---
### 💡 Lưu ý quan trọng:
- Website của bạn sẽ chạy được ngay nhờ file `.htaccess` điều hướng tôi đã thiết lập sẵn ở thư mục gốc.
- Nếu gặp lỗi 500, hãy kiểm tra lại thông tin Database trong file `.env`.
- Tài khoản Admin để bạn demo: `newtester@gmail.com` / `password123`.

Chúc bạn bảo vệ đồ án thành công và có một bản demo thật ấn tượng với các công ty!
