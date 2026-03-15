# 📚 PHÂN CÔNG THUYẾT TRÌNH - DỰ ÁN GÓC SÁCH

> **Đề tài**: Website Review Sách Cộng Đồng  
> **Công nghệ**: Laravel, MySQL, Blade, JavaScript, CSS  
> **Ngày**: 04/01/2026

---

## 📋 BẢNG PHÂN CÔNG TỔNG QUAN

| STT | Thành viên | Vai trò | Nhiệm vụ chính |
|-----|------------|---------|----------------|
| 1 | **Hoài** (Nhóm trưởng) | PM | Quản lý dự án, Routing, Review logic, Deploy, Git merge |
| 2 | **Thông** | Database / Admin | Thiết kế CSDL, Dữ liệu mẫu, Trang Admin, Thống kê |
| 3 | **Đạo** | Backend | Xác thực (Auth), Tìm kiếm, Upload file, Xử lý dữ liệu |
| 4 | **Tú** | Frontend | Layout chính, Trang chủ, Danh sách, Chi tiết sách, Tương thích |
| 5 | **Kha** | Frontend / Tester | Profile, Form Review, Like/Comment, Kiểm thử (Testing) |

---

## 1️⃣ HOÀI (PM - Nhóm trưởng)

**⏱️ Thời lượng đề xuất**: 5-7 phút (Mở đầu) + 3-5 phút (Kết thúc)

---

### 📌 PHẦN MỞ ĐẦU (5-7 phút)

#### 1. Giới thiệu đề tài (1 phút)

**Kịch bản nói:**
> "Xin chào thầy/cô và các bạn. Hôm nay nhóm chúng em sẽ trình bày đề tài **'Góc Sách - Website Review Sách Cộng Đồng'**.
> 
> Đây là một nền tảng web cho phép người dùng tìm kiếm sách, đọc và viết review, đánh giá sách, cũng như tương tác với cộng đồng những người yêu sách."

**Điểm cần show trên slide:**
- Logo "Góc Sách"
- Tagline: "Nơi kết nối những tâm hồn yêu sách"
- Screenshot trang chủ

---

#### 2. Mục tiêu dự án (1 phút)

| Mục tiêu | Mô tả |
|----------|-------|
| **Mục tiêu 1** | Xây dựng nền tảng chia sẻ review sách dành cho cộng đồng Việt Nam |
| **Mục tiêu 2** | Giúp người đọc dễ dàng tìm kiếm và lựa chọn sách phù hợp |
| **Mục tiêu 3** | Tạo không gian tương tác: like, comment, follow giữa các thành viên |
| **Mục tiêu 4** | Gamification: huy hiệu, thử thách để khuyến khích đóng góp |

**Kịch bản nói:**
> "Mục tiêu chính của dự án là tạo ra một cộng đồng sôi động, nơi mọi người có thể chia sẻ cảm nhận về sách, giúp nhau tìm được những cuốn sách hay, và cùng nhau lan tỏa văn hóa đọc."

---

#### 3. Đối tượng người dùng (30 giây)

| Đối tượng | Nhu cầu |
|-----------|---------|
| **Người yêu sách** | Tìm sách hay, đọc review trước khi mua |
| **Độc giả muốn chia sẻ** | Viết review, đánh giá sách đã đọc |
| **Cộng đồng đọc sách** | Kết nối, follow, tương tác với người cùng sở thích |
| **Admin/Quản trị** | Quản lý nội dung, duyệt sách, xử lý báo cáo |

---

#### 4. Tổng quan kiến trúc hệ thống (1-2 phút)

**Kiến trúc MVC của Laravel:**

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT                               │
│              (Browser - Desktop/Mobile)                     │
└─────────────────────────┬───────────────────────────────────┘
                          │ HTTP Request
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                     ROUTES (web.php)                        │
│    Định tuyến request đến Controller phù hợp                │
│    • /              → HomeController@index                  │
│    • /chi-tiet/{slug} → BookController@show                 │
│    • /admin/*       → Admin Controllers                     │
└─────────────────────────┬───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLERS                              │
│    Xử lý logic nghiệp vụ, gọi Model, trả về View            │
│    • HomeController     • BookController                    │
│    • AuthController     • ProfileController                 │
│    • AdminController    • PostController                    │
└─────────────────────────┬───────────────────────────────────┘
                          │
          ┌───────────────┴───────────────┐
          ▼                               ▼
┌──────────────────────┐       ┌──────────────────────┐
│       MODELS         │       │       VIEWS          │
│   (Eloquent ORM)     │       │   (Blade Templates)  │
│                      │       │                      │
│ • User    • Book     │       │ • home.blade.php     │
│ • Post    • Comment  │       │ • book-detail.blade  │
│ • Category • Author  │       │ • profile.blade.php  │
└──────────┬───────────┘       └──────────────────────┘
           │
           ▼
┌──────────────────────┐
│       DATABASE       │
│       (MySQL)        │
│   • 20+ Tables       │
│   • 41 Migrations    │
└──────────────────────┘
```

**Kịch bản nói:**
> "Hệ thống được xây dựng theo kiến trúc MVC của Laravel. Khi user truy cập, request sẽ đi qua Route để định tuyến đến Controller phù hợp. Controller xử lý logic, lấy dữ liệu từ Model (database), sau đó render View trả về cho người dùng."

---

#### 5. Cấu trúc Routing - 4 nhóm chính (1-2 phút)

**Giải thích chi tiết về phân quyền:**

| Nhóm | Middleware | Mô tả | Ví dụ route |
|------|------------|-------|-------------|
| **PUBLIC** | (không có) | Ai cũng xem được | `/`, `/chi-tiet/{slug}`, `/tac-gia` |
| **GUEST** | `guest` | Chỉ user chưa đăng nhập | `/login`, `/register` |
| **AUTH** | `auth`, `email.verified` | User đã đăng nhập + đã xác thực email | `/profile`, `/reviews/viet-bai` |
| **ADMIN** | `auth`, `admin` | Chỉ admin | `/admin/dashboard`, `/admin/books` |

**Demo trực tiếp (nếu có thể):**
1. Mở file `routes/web.php`
2. Chỉ ra 4 nhóm route được comment rõ ràng
3. Giải thích ngắn gọn về middleware

**Kịch bản nói:**
> "Chúng em chia routes thành 4 nhóm rõ ràng:
> - PUBLIC: ai cũng xem được như trang chủ, chi tiết sách
> - GUEST: dành cho người chưa đăng nhập như form login, register
> - AUTH: dành cho thành viên đã xác thực như viết review, profile
> - ADMIN: chỉ admin mới vào được như dashboard, quản lý sách"

---

#### 6. Công nghệ sử dụng (30 giây)

| Công nghệ | Version | Vai trò |
|-----------|---------|---------|
| **Laravel** | 11.x | PHP Framework chính, cung cấp MVC, ORM, Auth |
| **MySQL** | 8.x | Cơ sở dữ liệu quan hệ |
| **Blade** | (built-in) | Template Engine với layout inheritance |
| **Tailwind CSS** | 3.x | Utility-first CSS framework |
| **JavaScript** | ES6+ | AJAX, DOM manipulation, interactivity |
| **Font Awesome** | 6.x | Icon library |
| **Gemini AI** | API | Chatbot trợ lý thông minh |

---

#### 7. Giới thiệu các thành viên (30 giây)

> "Và bây giờ, em xin giới thiệu các thành viên trong nhóm sẽ demo từng phần:
> - **Thông**: Sẽ trình bày về Database và trang Admin
> - **Tú**: Sẽ demo giao diện Frontend
> - **Đạo**: Sẽ demo phần Backend như Auth, Search, Upload
> - **Kha**: Sẽ demo Profile, Review và kết quả Testing
> 
> Xin mời Thông bắt đầu phần tiếp theo."

---

### 📌 PHẦN KẾT THÚC (3-5 phút)

#### 1. Tổng kết các chức năng (1-2 phút)

**Checklist chức năng đã hoàn thành:**

| Module | Chức năng | Trạng thái |
|--------|-----------|------------|
| **Xác thực** | Đăng ký, Đăng nhập, OTP, Quên mật khẩu | ✅ Hoàn thành |
| **Sách** | Danh sách, Chi tiết, Tìm kiếm, Lọc | ✅ Hoàn thành |
| **Review** | Viết, Sửa, Xóa, Like, Comment | ✅ Hoàn thành |
| **Profile** | Xem, Sửa, Avatar, Follow | ✅ Hoàn thành |
| **Admin** | Dashboard, CRUD, Báo cáo, Logs | ✅ Hoàn thành |
| **Gamification** | Badges, Challenges, Frames | ✅ Hoàn thành |
| **Extras** | Chatbot AI, Themes, Newsletter | ✅ Hoàn thành |

**Kịch bản nói:**
> "Như vậy, nhóm chúng em đã hoàn thành đầy đủ các chức năng chính của một website review sách cộng đồng, bao gồm hệ thống xác thực với OTP, quản lý sách và review, hệ thống tương tác xã hội, trang admin quản trị, và cả các tính năng gamification như huy hiệu, thử thách."

---

#### 2. Thống kê dự án (30 giây)

| Metric | Số lượng |
|--------|----------|
| Số lượng Routes | 80+ |
| Số lượng Controllers | 25+ |
| Số lượng Models | 20+ |
| Số lượng Migrations | 41 |
| Số lượng Views (Blade) | 60+ |
| Lines of Code (ước tính) | 15,000+ |

---

#### 3. Hướng phát triển tương lai (1 phút)

| Giai đoạn | Tính năng dự kiến |
|-----------|-------------------|
| **Ngắn hạn** | App mobile (React Native), Push notification |
| **Trung hạn** | AI gợi ý sách cá nhân hóa, Ebook reader tích hợp |
| **Dài hạn** | Marketplace sách cũ, Tích hợp với nhà sách |

**Kịch bản nói:**
> "Trong tương lai, chúng em dự định phát triển thêm ứng dụng mobile, tích hợp AI để gợi ý sách cá nhân hóa, và có thể mở rộng thành một marketplace để trao đổi sách giữa các thành viên."

---

#### 4. Khó khăn và bài học (30 giây)

| Khó khăn | Giải pháp |
|----------|-----------|
| Deploy lên hosting chia sẻ | Học cách config .htaccess, symlinks |
| Responsive trên nhiều thiết bị | Sử dụng Tailwind CSS breakpoints |
| Xử lý AJAX và realtime | Polling + session management |
| Quản lý code nhiều người | Git workflow, PR review |

---

#### 5. Demo website trực tiếp (nếu còn thời gian)

**URL demo:** `https://tronghoai.id.vn`

---

#### 6. Phần hỏi đáp Q&A (1-2 phút)

**Kịch bản nói:**
> "Đó là toàn bộ bài thuyết trình của nhóm chúng em về dự án Góc Sách. Xin cảm ơn thầy/cô và các bạn đã lắng nghe.
> 
> Nếu có câu hỏi nào, xin mời thầy/cô và các bạn đặt câu hỏi. Chúng em sẵn sàng giải đáp."

**Câu hỏi có thể được hỏi:**
1. *"Tại sao chọn Laravel thay vì Node.js?"* → Cộng đồng lớn, dễ học, hosting PHP phổ biến
2. *"Bảo mật được xử lý như thế nào?"* → CSRF token, password hash, middleware
3. *"Có scale được không?"* → Có thể dùng queue, cache, CDN

---

### 📌 CHECKLIST CẦN CHUẨN BỊ

- [ ] Slide PowerPoint/Canva cho phần mở đầu
- [ ] Screenshot trang chủ, kiến trúc MVC
- [ ] File `routes/web.php` mở sẵn để demo
- [ ] Slide tổng kết và hướng phát triển
- [ ] Website chạy sẵn trên localhost hoặc hosting
- [ ] Chuẩn bị câu trả lời cho các câu hỏi thường gặp

---

## 2️⃣ THÔNG (Database / Admin)

**⏱️ Thời lượng đề xuất**: 7-10 phút

### Thiết kế Cơ sở dữ liệu (ERD)

#### Các bảng chính

| Bảng | Mô tả | Quan hệ chính |
|------|-------|---------------|
| `users` | Thông tin người dùng | 1-n với posts, comments, likes |
| `books` | Thông tin sách | n-1 với categories, 1-n với posts |
| `categories` | Danh mục sách | 1-n với books |
| `authors` | Thông tin tác giả | n-n với books |
| `posts` | Bài review | n-1 với users, books |
| `comments` | Bình luận | n-1 với posts, users |
| `likes` | Lượt thích | n-1 với posts, users |
| `follows` | Theo dõi | n-n giữa users |
| `notifications` | Thông báo | n-1 với users |
| `badges` | Huy hiệu | n-n với users |
| `challenges` | Thử thách | n-n với users |
| `banners` | Banner quảng cáo | - |
| `articles` | Bài viết tạp chí | - |
| `quotes` | Trích dẫn hay | - |

#### Tổng số migrations: 41 files

### Demo Trang Admin

#### Dashboard (`/admin/dashboard`)
- Tổng quan số liệu: Users, Posts, Books, Views
- Biểu đồ thống kê theo thời gian
- Bài viết mới nhất
- Users mới đăng ký
- Export báo cáo Excel

#### Quản lý Sách (`/admin/books`)
| Chức năng | Route | Mô tả |
|-----------|-------|-------|
| Danh sách | `admin.books.index` | Xem tất cả sách |
| Thêm mới | `admin.books.create` | Form thêm sách |
| Chỉnh sửa | `admin.books.edit` | Form sửa sách |
| Xóa | `admin.books.destroy` | Xóa sách |
| Duyệt | `admin.books.approve` | Duyệt sách đề xuất |

#### Quản lý Danh mục (`/admin/categories`)
- CRUD Categories (Thêm, Sửa, Xóa danh mục sách)

#### Quản lý Tác giả (`/admin/authors`)
- CRUD Authors với proxy image từ URL

#### Quản lý Bài viết (`/admin/posts`)
- Xem danh sách reviews
- Duyệt/Từ chối yêu cầu xóa bài
- Chỉnh sửa nội dung bài viết

#### Quản lý Banner (`/admin/banners`)
- CRUD Banner slider trang chủ

#### Quản lý Báo cáo (`/admin/comment-reports`, `/admin/post-reports`)
- Xem danh sách báo cáo vi phạm
- Duyệt/Từ chối báo cáo
- Xóa nội dung vi phạm

#### Activity Logs (`/admin/activity-logs`)
- Lịch sử hoạt động của admin
- Khôi phục dữ liệu đã xóa

#### Quản lý Gamification
| Module | Đường dẫn |
|--------|-----------|
| Badges (Huy hiệu) | `/admin/badges` |
| Challenges (Thử thách) | `/admin/challenges` |
| Avatar Frames | `/admin/avatar-frames` |
| Activity Titles | `/admin/activity-titles` |

#### Theme Management (`/admin/theme`)
- Quản lý giao diện theo mùa: Christmas, Tết, Valentine, Halloween
- Cài đặt hiệu ứng falling icons

---

## 3️⃣ ĐẠO (Backend)

**⏱️ Thời lượng đề xuất**: 7-10 phút

### Hệ thống Xác thực (Authentication)

#### Đăng ký tài khoản
| Route | Controller | Mô tả |
|-------|------------|-------|
| `GET /register` | `AuthController@showRegisterForm` | Hiển thị form đăng ký |
| `POST /register` | `AuthController@register` | Xử lý đăng ký |

**Flow đăng ký:**
```
1. Nhập thông tin (tên, email, mật khẩu)
2. Validate dữ liệu
3. Tạo user với status chưa verify
4. Gửi mã OTP qua email
5. Redirect đến trang xác thực
```

#### Xác thực Email (OTP)
| Route | Mô tả |
|-------|-------|
| `GET /email/verify` | Hiển thị form nhập mã OTP |
| `POST /email/verify` | Xác thực mã OTP |
| `POST /email/resend-code` | Gửi lại mã OTP |

#### Đăng nhập / Đăng xuất
| Route | Controller | Mô tả |
|-------|------------|-------|
| `GET /login` | `AuthController@showLoginForm` | Form đăng nhập |
| `POST /login` | `AuthController@login` | Xử lý đăng nhập |
| `POST /logout` | `AuthController@logout` | Đăng xuất |

#### Quên mật khẩu
**Flow quên mật khẩu:**
```
1. GET /forgot-password      → Nhập email
2. POST /forgot-password     → Gửi mã xác thực
3. GET /verify-code          → Nhập mã xác thực
4. POST /verify-code         → Kiểm tra mã
5. GET /reset-password       → Nhập mật khẩu mới
6. POST /reset-password      → Cập nhật mật khẩu
```

#### Đổi mật khẩu (khi đã đăng nhập)
| Route | Mô tả |
|-------|-------|
| `GET /change-password` | Form đổi mật khẩu |
| `POST /change-password` | Xử lý đổi mật khẩu |

---

### Hệ thống Tìm kiếm

#### AJAX Live Search (Header)
```javascript
Route: GET /ajax-search?keyword=...

Response:
[
  {
    "id": 1,
    "title": "Đắc Nhân Tâm",
    "slug": "dac-nhan-tam",
    "author_name": "Dale Carnegie",
    "cover_image": "...",
    "avg_rating": 4.5
  }
]
```

#### Tìm kiếm nâng cao
| Route | Controller | Mô tả |
|-------|------------|-------|
| `GET /review-search` | `BookController@search` | Tìm kiếm sách + filter |

**Các filter hỗ trợ:**
- Từ khóa (keyword)
- Danh mục (category)
- Sắp xếp (sort): mới nhất, rating cao nhất, views nhiều nhất

---

### Upload File

#### Upload Avatar
| Route | Controller | Mô tả |
|-------|------------|-------|
| `POST /profile/update` | `ProfileController@update` | Upload avatar người dùng |

**Định dạng hỗ trợ:** `.jpg`, `.png`, `.webp`, `.gif`, `.svg`

**Phương thức upload:**
- Upload từ máy tính
- Upload từ URL

#### Upload ảnh Sách (Admin)
| Route | Controller |
|-------|------------|
| `POST /admin/books` | `AdminBookController@store` |
| `PUT /admin/books/{id}` | `AdminBookController@update` |

#### Upload Thumbnail Review
| Route | Controller |
|-------|------------|
| `POST /posts/store` | `PostController@store` |
| `PUT /reviews/{id}/update` | `PostController@update` |

---

### API Backend

#### API Sách
| Endpoint | Mô tả |
|----------|-------|
| `GET /api/books/search?q=...` | Tìm sách (cho form viết review) |
| `GET /api/books/popular` | Lấy sách phổ biến (random 6 từ top 20) |

#### API Chatbot
| Endpoint | Mô tả |
|----------|-------|
| `POST /api/chatbot` | Gửi tin nhắn cho AI |
| `GET /api/chatbot/history` | Lấy lịch sử chat |
| `DELETE /api/chatbot/history` | Xóa lịch sử |

#### API Notifications
| Endpoint | Mô tả |
|----------|-------|
| `GET /api/notifications` | Lấy thông báo realtime |

#### API Follow
| Endpoint | Mô tả |
|----------|-------|
| `GET /api/user/{id}/followers` | Danh sách người theo dõi |
| `GET /api/user/{id}/following` | Danh sách đang theo dõi |

---

## 4️⃣ TÚ (Frontend)

**⏱️ Thời lượng đề xuất**: 7-10 phút

### Layout chính (`layouts/app.blade.php`)

#### Header
- Logo + Tên website
- Thanh tìm kiếm (AJAX live search)
- Menu điều hướng: Danh sách, Tác giả, Thử thách
- Dropdown thể loại sách
- Nút Đăng nhập/Đăng ký hoặc User menu
- Icon thông báo (với badge số lượng)

#### Footer
- Thông tin về website
- Links: Về chúng tôi, Điều khoản, Chính sách, Liên hệ
- Social media links

---

### Trang chủ (`home.blade.php`)

#### Các section chính

| Section | Mô tả |
|---------|-------|
| **Hero Banner/Slider** | Slideshow quảng cáo sách nổi bật, events |
| **"Hôm nay đọc gì?"** | Gợi ý sách random với rating, card hover effect |
| **"Cộng Đồng Review"** | Tab: Mới nhất / Nổi bật - Hiển thị reviews |
| **"Tạp Chí Đọc"** | Bài viết/Articles từ admin |
| **Sidebar Widgets** | Thống kê, Quote, Top thành viên, Back to top |

#### Các hiệu ứng đặc biệt
- Seasonal themes (Giáng sinh, Tết, Valentine...)
- Falling icons effect
- Smooth scroll
- Card hover animations

---

### Danh sách sách (`list.blade.php`)

**Route**: `GET /danh-sach`

| Chức năng | Mô tả |
|-----------|-------|
| Filter theo danh mục | Dropdown chọn thể loại |
| Sắp xếp | Mới nhất, Rating cao, Views nhiều |
| Phân trang | Custom pagination (10 items/page) |
| Card sách | Cover, tên, tác giả, rating stars |

---

### Chi tiết sách (`book-detail.blade.php`)

**Route**: `GET /chi-tiet/{slug}`

#### Layout chi tiết sách

```
┌─────────────────────────────────────────┐
│  ┌───────┐  Tên sách                    │
│  │ Cover │  Tác giả                     │
│  │ Image │  ⭐⭐⭐⭐⭐ (4.5/5)            │
│  │       │  📖 123 views | 45 reviews   │
│  └───────┘  [Viết Review] [Đề xuất sửa] │
├─────────────────────────────────────────┤
│  📝 Mô tả sách                          │
│  Lorem ipsum dolor sit amet...          │
├─────────────────────────────────────────┤
│  📊 Phân tích đánh giá                  │
│  ⭐⭐⭐⭐⭐ ████████░░ 80%               │
│  ⭐⭐⭐⭐   ██░░░░░░░░ 15%               │
│  ...                                    │
├─────────────────────────────────────────┤
│  💬 Các bài review                      │
│  ┌─────────────────────────────────┐    │
│  │ User1 - ⭐⭐⭐⭐⭐ - 2 ngày trước│    │
│  │ Nội dung review...              │    │
│  └─────────────────────────────────┘    │
└─────────────────────────────────────────┘
```

---

### Trang tác giả

| Trang | Route | Mô tả |
|-------|-------|-------|
| Danh sách tác giả | `GET /tac-gia` | Grid/List tác giả |
| Chi tiết tác giả | `GET /tac-gia/{slug}` | Bio, ảnh, danh sách sách |

---

### Responsive Design

| Breakpoint | Thiết bị | Điều chỉnh |
|------------|----------|------------|
| < 576px | Mobile | Menu hamburger, 1 column |
| 576-768px | Tablet Portrait | 2 columns |
| 768-992px | Tablet Landscape | 3 columns |
| > 992px | Desktop | Full layout, sidebar |

---

## 5️⃣ KHA (Frontend / Tester)

**⏱️ Thời lượng đề xuất**: 7-10 phút

### Trang Profile (`profile.blade.php`)

**Route**: `GET /profile/{id}` hoặc `GET /thanh-vien/{id}` (public)

#### Layout Profile

```
┌─────────────────────────────────────────────┐
│  ┌─────────┐  Tên người dùng                │
│  │ Avatar  │  @username                     │
│  │ + Frame │  📝 Bio/Giới thiệu             │
│  └─────────┘  🏅 Badges hiển thị            │
│                                             │
│  📊 12 Reviews | 45 Followers | 23 Following│
│  [Theo dõi] [Nhắn tin]                      │
├─────────────────────────────────────────────┤
│  [Reviews] [Sách đề xuất] [Đã lưu]          │
├─────────────────────────────────────────────┤
│  Danh sách bài viết của user                │
└─────────────────────────────────────────────┘
```

#### Chức năng Profile
| Chức năng | Route | Mô tả |
|-----------|-------|-------|
| Xem profile | `profile` | Hiển thị thông tin |
| Cập nhật profile | `profile.update` | Đổi avatar, bio, thông tin |
| Trang bị Avatar Frame | `profile.avatar-frame.equip` | Chọn khung avatar |
| Sắp xếp Badges | `profile.badges.order` | Thay đổi thứ tự huy hiệu |
| Xem tất cả reviews | `profile.reviews` | Danh sách đầy đủ |
| Follow/Unfollow | `follow.toggle` | Theo dõi người dùng |

---

### Form Review (`create-review.blade.php`)

**Route**: `GET /reviews/viet-bai`

#### Các thành phần form

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| Chọn sách | Autocomplete | Tìm và chọn sách từ CSDL |
| Tiêu đề | Text | Tiêu đề bài review |
| Nội dung | Textarea/Rich Editor | Nội dung chi tiết |
| Đánh giá | Star Rating | 1-5 sao |
| Thumbnail | File/URL | Ảnh đại diện bài viết |

**Flow viết review:**
```
1. Chọn sách (autocomplete search)
2. Nhập tiêu đề hấp dẫn
3. Viết nội dung review
4. Chọn số sao đánh giá
5. Upload ảnh thumbnail (optional)
6. Submit → Pending/Published
```

#### Chỉnh sửa Review (`edit-review.blade.php`)
| Route | Mô tả |
|-------|-------|
| `GET /reviews/{id}/chinh-sua` | Form chỉnh sửa |
| `PUT /reviews/{id}/update` | Lưu thay đổi |
| `POST /reviews/{id}/request-delete` | Yêu cầu xóa |

---

### Like / Comment System

#### Like bài viết
```javascript
Route: POST /like
Payload: { type: 'post', id: 123 }
Response: { success: true, likes_count: 45, liked: true }
```

#### Like comment
```javascript
Route: POST /like
Payload: { type: 'comment', id: 456 }
Response: { success: true, likes_count: 12, liked: true }
```

#### Comment bài viết
| Route | Mô tả |
|-------|-------|
| `POST /post/{post_id}/comment` | Thêm comment mới |
| `POST /comment/{id}/reply` | Reply comment |

#### Save/Unsave bài viết
```javascript
Route: POST /post/save
Payload: { post_id: 123 }
Response: { success: true, saved: true }
```

---

### Báo cáo vi phạm (Report)

| Route | Mô tả |
|-------|-------|
| `POST /report/post/{id}` | Báo cáo bài viết |
| `POST /report/comment/{id}` | Báo cáo bình luận |

---

### Phần Kiểm thử (Testing)

#### Test Cases chính

| Module | Test Case | Kết quả |
|--------|-----------|---------|
| **Auth** | Đăng ký tài khoản mới | ✅ Pass |
| **Auth** | Đăng ký email đã tồn tại | ✅ Pass |
| **Auth** | Xác thực OTP đúng/sai | ✅ Pass |
| **Auth** | Đăng nhập đúng/sai | ✅ Pass |
| **Auth** | Quên mật khẩu flow | ✅ Pass |
| **Search** | Tìm kiếm có kết quả | ✅ Pass |
| **Search** | Tìm kiếm không có kết quả | ✅ Pass |
| **Review** | Viết review thành công | ✅ Pass |
| **Review** | Viết review thiếu trường | ✅ Pass |
| **Like** | Like/Unlike post | ✅ Pass |
| **Comment** | Thêm comment | ✅ Pass |
| **Comment** | Reply comment | ✅ Pass |
| **Profile** | Cập nhật thông tin | ✅ Pass |
| **Profile** | Upload avatar | ✅ Pass |
| **Follow** | Follow/Unfollow user | ✅ Pass |
| **Admin** | CRUD sách | ✅ Pass |
| **Admin** | Duyệt báo cáo | ✅ Pass |

#### Các lỗi đã phát hiện và sửa

| # | Mô tả lỗi | Nguyên nhân | Cách sửa |
|---|-----------|-------------|----------|
| 1 | Theme không hiển thị cho guest | Middleware check admin | Sửa logic hiển thị |
| 2 | Hardcoded URL localhost | Development config | Dùng `config('app.url')` |
| 3 | Header search responsive | CSS breakpoint | Thêm media query |
| 4 | Pagination style | Default Laravel | Custom pagination view |

#### Cross-browser Testing

| Browser | Version | Kết quả |
|---------|---------|---------|
| Chrome | 120+ | ✅ Pass |
| Firefox | 115+ | ✅ Pass |
| Safari | 17+ | ✅ Pass |
| Edge | 120+ | ✅ Pass |

#### Responsive Testing

| Device | Resolution | Kết quả |
|--------|------------|---------|
| iPhone 14 | 390x844 | ✅ Pass |
| iPad | 768x1024 | ✅ Pass |
| Desktop | 1920x1080 | ✅ Pass |

---

## 🎬 FLOW THUYẾT TRÌNH

```
┌─────────────────────────────────────────────────────────────────┐
│  1. HOÀI (5-7 phút)                                              │
│     → Giới thiệu đề tài                                          │
│     → Mục tiêu dự án                                             │
│     → Tổng quan hệ thống & Routing                               │
├─────────────────────────────────────────────────────────────────┤
│  2. THÔNG (7-10 phút)                                            │
│     → Trình bày ERD / Cấu trúc CSDL                              │
│     → Demo trang Admin Dashboard                                 │
│     → Demo quản lý Sách, Danh mục, Tác giả                       │
│     → Demo Thống kê, Activity Logs                               │
├─────────────────────────────────────────────────────────────────┤
│  3. TÚ (7-10 phút)                                               │
│     → Demo Layout chính (Header, Footer)                         │
│     → Demo Trang chủ (các sections)                              │
│     → Demo Danh sách sách (filter, phân trang)                   │
│     → Demo Chi tiết sách                                         │
│     → Demo Responsive trên mobile                                │
├─────────────────────────────────────────────────────────────────┤
│  4. ĐẠO (7-10 phút)                                              │
│     → Demo Đăng ký / Xác thực OTP                                │
│     → Demo Đăng nhập / Quên mật khẩu                            │
│     → Demo Tìm kiếm (live search + nâng cao)                     │
│     → Demo Upload file (avatar, ảnh sách)                        │
│     → Giải thích API backend                                     │
├─────────────────────────────────────────────────────────────────┤
│  5. KHA (7-10 phút)                                              │
│     → Demo trang Profile                                         │
│     → Demo Form viết Review                                      │
│     → Demo Like, Comment, Reply                                  │
│     → Demo Follow/Unfollow                                       │
│     → Trình bày kết quả Testing                                  │
├─────────────────────────────────────────────────────────────────┤
│  6. HOÀI (3-5 phút)                                              │
│     → Tổng kết các chức năng                                     │
│     → Hướng phát triển tương lai                                 │
│     → Q&A                                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📝 GHI CHÚ

- Mỗi người nên chuẩn bị slides riêng cho phần của mình
- Demo trực tiếp trên localhost hoặc hosting
- Chuẩn bị sẵn dữ liệu mẫu để demo
- Ghi lại video demo phòng trường hợp lỗi kỹ thuật

---

*Tài liệu được tạo tự động dựa trên phân tích codebase dự án Góc Sách Laravel*
