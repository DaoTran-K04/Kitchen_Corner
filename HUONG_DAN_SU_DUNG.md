# 🍳 HƯỚNG DẪN SỬ DỤNG WEBSITE GÓC BẾP (KITCHEN CORNER)

---

## MỤC LỤC

### 👤 PHẦN NGƯỜI DÙNG (USER)
1. [Trang Chủ](#-1-trang-chủ)
2. [Đăng Ký & Đăng Nhập](#-2-đăng-ký--đăng-nhập)
3. [Xem & Tìm Kiếm Công Thức (Smart Search)](#-3-xem--tìm-kiếm-công-thức)
4. [Đăng Tải Công Thức & Phân Tích Dinh Dưỡng](#-4-đăng-tải-công-thức)
5. [Tương Tác & Bình Luận](#-5-tương-tác)
6. [Trang Cá Nhân (Profile)](#-6-trang-cá-nhân)
7. [Theo Dõi Đầu Bếp (Follow)](#-7-theo-dõi-đầu-bếp)
8. [Khám Phá Tác Giả](#-8-khám-phá-tác-giả)
9. [Thông Báo](#-9-thông-báo)
10. [Tạp Chí Ẩm Thực](#-10-tạp-chí-ẩm-thực)
11. [Hệ Thống Gamification](#-11-hệ-thống-danh-hiệu-gamification)

### 🔧 PHẦN QUẢN TRỊ (ADMIN)
12. [Dashboard Admin](#-12-dashboard-admin)
13. [Quản Lý Công Thức](#-13-quản-lý-công-thức)
14. [Quản Lý Người Dùng](#-14-quản-lý-người-dùng)
15. [Quản Lý Chủ Đề Ẩm Thực](#-15-quản-lý-chủ-đề)
16. [Quản Lý Tác Giả / Bài Viết](#-16-quản-lý-tác-giả--bài-viết)
17. [Quản Lý Banner & Quotes](#-17-quản-lý-banner--quotes)
18. [Xử Lý Báo Cáo Vi Phạm](#-18-xử-lý-báo-cáo)
19. [Nhật Ký Hoạt Động (Logs)](#-19-nhật-ký-hoạt-động)

---

# 👤 PHẦN NGƯỜI DÙNG

---

## 🏠 1. TRANG CHỦ

Khi truy cập website, bạn sẽ thấy:
- **Banner slider** - Các sự kiện, món ngon tiêu biểu, banner quảng bá.
- **Hôm Nay Nấu Gì?** - Hệ thống gợi ý ngẫu nhiên một món ăn ngon mỗi ngày.
- **Món Ngon Xu Hướng** - Các công thức đang hot, được tương tác và có lượt xem cao nhất.
- **Tạp Chí Ẩm Thực** - Các bài viết chuyên sâu về văn hóa ẩm thực.
- **Công Thức Mới Cập Nhật** - Danh sách công thức vừa được các đầu bếp đăng tải.
- **Gợi ý Chủ Đề** - Lọc món ăn nhanh theo các chủ đề: Món chay, Thịt gia cầm, Hải sản...
- **Thống Kê Cộng Đồng** - Số lượng công thức, số thành viên, và lượt truy cập.
- **Châm Ngôn Hôm Nay** - Quote truyền cảm hứng nấu nướng ngẫu nhiên.

---

## 🔐 2. ĐĂNG KÝ & ĐĂNG NHẬP

### Đăng ký tài khoản mới:
1. Nhấn nút **"Đăng Ký"** trên góc phải header.
2. Điền thông tin: Họ tên, Email, Mật khẩu.
3. Nhấn **"Tham Gia Ngay"**.
4. (Nếu áp dụng) Kiểm tra email để xác thực tài khoản.

### Đăng nhập:
1. Nhấn nút **"Đăng Nhập"**.
2. Nhập Email và Mật khẩu.
3. Nhấn **"Đăng Nhập"**. Bạn cũng có thể tích "Ghi nhớ xác thực" để không cần đăng nhập lại vào lần sau.

### Tùy chỉnh tài khoản:
- Bạn có thể vào **Thiết lập tài khoản** để Đổi mật khẩu, tải lên Avatar ảnh đại diện, điền thêm các thông tin tiểu sử (Bio), quốc gia, năm sinh để cộng đồng biết thêm về bạn.

---

## 🔍 3. XEM & TÌM KIẾM CÔNG THỨC

### Tìm kiếm Live Search thông thường:
1. Sử dụng **thanh tìm kiếm** (Icon Kính lúp) ở header.
2. Từ khóa sẽ trả về các món ăn có chứa tên trên nhan đề ngay lập tức.

### Tìm Kiếm Thông Minh (Tủ Lạnh Web - Jaccard Search):
*Đây là tính năng độc quyền của hệ thống:*
1. Vào mục **"Tủ Lạnh Web"** trên thanh Menu.
2. Điền các nguyên liệu bạn đang sẵn có trong tủ lạnh (ngăn cách bằng dấu phẩy, ví dụ: *thịt lợn, cà chua, hành lá*).
3. Hệ thống dùng Thuật toán **Jaccard Similarity** AI để tính toán độ tương đồng và đề xuất cho bạn các món ăn có thể nấu với độ chính xác cao nhất (Hiển thị tỷ lệ khớp %).

### Xem chi tiết món ăn:
1. Click vào hình ảnh món ăn.
2. Tại đây có toàn bộ thông tin: **Khẩu phần, Thời gian nấu, Độ khó, Nguyên liệu (định lượng cụ thể), và Các Bước Nấu.**
3. Xem bảng **Thành Phần Dinh Dưỡng** (Calo, Protein, Carb, Fat) của món ăn.

---

## 🥘 4. ĐĂNG TẢI CÔNG THỨC (NỔI BẬT)

1. Đăng nhập tài khoản, nhấn nút **"Đăng Công Thức"** (hoặc biểu tượng viết bài +) trên cấu hình.
2. Điền tiêu đề món ăn, thời gian thực hiện, khẩu phần, mức độ khó.
3. **Thêm Nguyên Liệu:** Bạn gõ chính xác số lượng và tên (VD: *200g Thịt bò*).
4. **Viết Các Bước Làm:** Mô tả cách chế biến món ăn từng bước.
5. Úp ảnh Cover món ăn.
6. Khi lưu nháp hoặc Đăng bài, **Hệ thống Nutrition Calculator** sẽ tự động bắt từ khóa định lượng của nguyên liệu, khớp với USDA Database để tự động xuất ra Bảng Dinh Dưỡng cực kỳ chính xác cho món của bạn.

> 💡 Các công thức mới đăng sẽ ở trạng thái chờ duyệt, Admin sẽ kích hoạt cho bạn.

---

## 💬 5. TƯƠNG TÁC

### 🤍 Lưu & Thích Công Thức:
- Nhấn nút **Tim (Heart)** trên công thức để đếm lượt thích.
- Nhấn **Lưu (Bookmark)** để thêm vào danh sách Yêu thích của cá nhân bạn (Xem lại trong tab "Công thức đã lưu" ở Profile).

### 💬 Bình luận (Review):
1. Cuộn tới phần Comment bên dưới món ăn.
2. Bình luận những thắc mắc hoặc lời khen gửi tới Tác giả.
3. Người dùng khác có cơ chế **Thích (Like) bình luận** của bạn.

---

## 👤 6. TRANG CÁ NHÂN (PROFILE)

### Quản lý Hồ sơ:
1. Click vào avatar góc trên phải $\rightarrow$ **Trang cá nhân**.
2. Bạn sẽ thấy giao diện Profile đẹp mắt, cho biết bạn đang ở hạng nào, điểm tích lũy bao nhiêu.
3. Có 3 tab lớn: 
   - **Bài đã đăng**: Danh sách các món ăn bạn là tác giả.
   - **Bài đã lưu**: Bộ sưu tập món ngon học lỏm.
   - **Bình luận**: Lịch sử giao tiếp cộng đồng.

---

## 👥 7. THEO DÕI ĐẦU BẾP (FOLLOW)

- Bạn yêu thích phong cách nấu của một người? Nhấp vào avatar của họ để vào Trang của họ.
- Bấm nút **"Theo dõi" (Follow)**.
- Khi người đó đăng bài mới, bảng tin của bạn sẽ ưu tiên hiển thị. Thông số *Followers/Following* sẽ cấu thành độ uy tín của tài khoản.

---

## 🧑‍🍳 8. KHÁM PHÁ TÁC GIẢ

- Nhấn vào **"Tác Giả"** trên Menu chính.
- Hệ thống sẽ liệt kê các "Đầu bếp xuất sắc" dựa trên thành tích chia sẻ công thức.
- Bạn có thể **Sắp xếp** hiển thị Tên A-Z hoặc Top nhiều công thức nhất.

---

## 🔔 9. THÔNG BÁO

- Hệ thống Notify (Chuông) góc phải trên cùng sẽ sáng khi có biến động.
- Bạn nhận thông báo nếu: Có người thích công thức của bạn, bình luận vào món của bạn, follow bạn, hoặc admin vừa duyệt/không duyệt bài viết của bạn.

---

## 📰 10. TẠP CHÍ ẨM THỰC

- Hệ thống có Blog riêng chia sẻ kinh nghiệm chọn nguyên liệu, mẹo vặt nhà bếp. Nhấp vào các bài viết Tạp Chí từ trang chủ để mở rộng kiến thức.

---

## 🏆 11. HỆ THỐNG DANH HIỆU (GAMIFICATION)

- Mỗi tương tác của bạn (Đăng bài = +20đ, Đăng bình luận = +2đ, Nhận Like = +1đ) đều sinh ra Point (Kinh nghiệm).
- Kinh nghiệm giúp bạn lên hạng. Khi đạt mốc chỉ định, bạn mở khóa **Icon Danh Hiệu (Badges)** (Thực Thần, Vua Đầu Bếp).
- Mở khóa **Khung Avatar** (Frames).
- Bạn vào Profile $\rightarrow$ Click vào Avatar $\rightarrow$ Chọn Khung và Trang bị để khoe với cộng đồng.

---
---

# 🔧 PHẦN QUẢN TRỊ (ADMIN)

> ⚠️ *Khu vực này yêu cầu tài khoản Role: Admin. Truy cập bằng `/admin/dashboard`*

## 📊 12. DASHBOARD ADMIN
Giao diện siêu trực quan:
- **Biểu đồ Cột/Line** thống kê Lượng người dùng mới, và Công thức đăng tải tuần qua/tháng qua.
- **Top Metrics**: Số người online, Tổng số Recipe, Số vi phạm.

## 🥘 13. QUẢN LÝ CÔNG THỨC (RECIPES)
- Theo dõi toàn bộ món ăn được đăng tải.
- Xem các món ở trạng thái **"Chờ Duyệt"** (Pending). Bấm "Xem trước", sau đó chọn "Duyệt" (Published) hoặc "Từ chối khéo" (Rejected) kèm lý do.
- Có khả năng Ẩn, Chỉnh sửa ép buộc hoặc Xóa bình luận tiêu cực trực tiếp trong màn hình chi tiết món.

## 👥 14. QUẢN LÝ NGƯỜI DÙNG
- Tìm kiếm thành viên chuyên sâu theo Tên/Email.
- Can thiệp thay đổi Quyền (User $\leftrightarrow$ Admin).
- Chấm dứt hoạt động (Bank tài khoản) tự động thông qua Soft Delete.

## 📖 15. QUẢN LÝ CHỦ ĐỀ
- Khởi tạo, Sửa, Xóa các Danh mục ẩm thực (Categories) để làm bộ lọc Menu cho User. (Ví dụ: Món ăn dặm, Món chay, Thức uống).

## 📰 16. QUẢN LÝ TẠP CHÍ
- Nơi viết các bài Blog cho nền tảng sử dụng CKEditor cực mạnh. Đăng tải và set cờ "Is Featured" (Đáng chú ý) để lên trang chủ banner.

## 🖼️ 17. QUẢN LÝ BANNER & QUOTES
- **Banner**: Thêm/Sửa hình ảnh Banner slider to trên Homepage, điều phối order.
- **Quotes**: Cập nhật câu nói hay (Châm ngôn nhà bếp), hệ thống tự động bốc ngẫu nhiên lên Sidebar trang chủ mỗi ngày.

## 🚨 18. XỬ LÝ BÁO CÁO (REPORTS)
- User có quyền Báo cáo Nội dung (Bài viết mạo danh, ảnh không hợp lệ) hoặc Báo cáo Comment (Toxic, chửi tục).
- Admin sử dụng mục Báo Cáo để ra lệnh **Xóa Vĩnh Viễn** thành phần bị cảnh báo hoặc Hủy đơn báo cáo sai.

## 📋 19. NHẬT KÝ HOẠT ĐỘNG (SYSTEM LOGS)
- Kiểm toán (Audit) tự động mọi nút nhấp của **Tất cả các Admin**. 
- Lưu trữ IP, Ngày giờ, Hành vi (Sửa User A, Cập nhật Món ăn B).
- Chức năng này phục vụ cho bảo mật hệ thống nghiêm ngặt.

---
*Chúc bạn có trải nghiệm tuyệt vời cùng Kitchen Corner* 🚀
