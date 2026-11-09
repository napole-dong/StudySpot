# StudySpot – Website Review Quán Nước & Cà Phê Học Bài Dành Cho Sinh Viên

> **Nền tảng tra cứu, đánh giá quán cà phê và không gian học bài quanh Trường Đại học An Giang (AGU)**  
> *Đồ án môn học: Công nghệ Web – PHP (Mã học phần: SEE518)*

---

## 📌 Thông Tin Đồ Án & Sinh Viên Thực Hiện

* **Cơ sở đào tạo:** Trường Đại học An Giang (AGU) – Đại học Quốc gia TP. Hồ Chí Minh
* **Khoa:** Khoa Công nghệ Thông tin
* **Học phần:** Công nghệ Web – PHP (Mã học phần: **SEE518**)
* **Đề tài:** **StudySpot – Nền tảng Review Quán Nước & Cà Phê Học Bài Dành Cho Sinh Viên**
* **Sinh viên thực hiện:** Huỳnh Nguyễn Phương Đông
* **Mã số sinh viên (MSSV):** DTH235628
* **Lớp:** DH23TH (Ngành Tin học / Công nghệ Thông tin)
* **Email liên hệ:** phuongdong1741@gmail.com
* **Địa bàn khảo sát thực tế:** Thành phố Long Xuyên, Tỉnh An Giang (Đặc biệt tập trung khu vực quanh cơ sở chính & cơ sở mới Trường Đại học An Giang)
* **Thời gian hoàn thiện:** Tháng 11/2026

---

## 📖 1. Giới thiệu Dự án

### 1.1. Bối cảnh & Tính cấp thiết
Đối với sinh viên Đại học An Giang (AGU), nhu cầu tìm kiếm không gian yên tĩnh để tự học, học nhóm, làm bài tập lớn và chạy deadline là rất lớn. Tuy nhiên, các nền tảng đánh giá địa điểm phổ biến hiện nay thường tập trung vào đồ uống hoặc chụp ảnh "check-in", thiếu các tiêu chí học tập thiết yếu như:
- **Tốc độ và độ ổn định của mạng Wifi** để làm việc trực tuyến.
- **Mật độ ổ cắm điện** tại từng vị trí bàn để cắm sạc laptop.
- **Mức độ tiếng ồn** và không gian bàn ghế phù hợp để ngồi lâu.
- **Thời gian hoạt động** (đặc biệt là các quán mở khuya hoặc 24/24 trong mùa thi cử).
- **Mức giá sinh viên** hợp túi tiền.

**StudySpot** ra đời nhằm giải quyết chính xác bài toán trên: Cung cấp một chuyên trang đánh giá trực quan, khách quan, được thiết kế riêng cho sinh viên Long Xuyên với bộ tiêu chí đánh giá chuyên biệt cho việc học tập.

### 1.2. Mục tiêu đề tài
- Ứng dụng mô hình kiến trúc **MVC (Model - View - Controller)** thuần bằng ngôn ngữ **PHP 8 OOP**.
- Thiết kế hệ thống cơ sở dữ liệu quan hệ chuẩn hóa trên **MySQL/MariaDB**, đảm bảo toàn vẹn dữ liệu bằng ràng buộc khóa ngoại (Foreign Keys).
- Đảm bảo an toàn thông tin theo các nguyên tắc bảo mật web cốt lõi (chống SQL Injection, XSS, CSRF, Session Fixation, bảo mật file upload).
- Xây dựng giao diện responsive thân thiện, tương thích tối ưu trên máy tính bảng, điện thoại di động và máy tính bàn.

---

## 🌟 2. Các Phân Hệ & Chức Năng Chính

Hệ thống được phân quyền chặt chẽ thành 3 nhóm người dùng: **Khách vãng lai (Guest)**, **Thành viên sinh viên (User)** và **Quản trị viên (Admin)**.

### 2.1. Phân hệ Khách vãng lai (Guest)
* **Trang chủ trực quan:**
  * Khối **Hero Search** tìm kiếm nhanh theo tên quán, địa chỉ hoặc khu vực.
  * Tự động chọn ảnh bìa hero ngẫu nhiên từ nhóm quán có điểm đánh giá cao và nhiều lượt lưu.
  * Danh sách **Quán học bài nổi bật** kèm phân trang động.
* **Bộ lọc khám phá đa tiêu chí (`/place`):**
  * Tìm kiếm theo từ khóa (tên quán, địa chỉ).
  * Lọc theo danh mục quán (Cà phê học bài, Cà phê mở khuya, Không gian yên tĩnh, Trà sữa học nhóm,...).
  * Lọc theo khu vực/phường tại Long Xuyên: Đông Xuyên (Khu vực AGU), Mỹ Xuyên, Mỹ Long, Mỹ Quý (KDC Tây Sông Hậu),...
  * Lọc theo khoảng giá ngân sách sinh viên (`price_min` - `price_max`).
  * Lọc theo tiện ích chọn lọc: *Wifi mạnh, Nhiều ổ cắm điện, Máy lạnh mát mẻ, Chỗ đậu xe thuận tiện, Cho phép thú cưng*.
  * Lọc theo mức độ ồn: *Rất yên tĩnh, Yên tĩnh, Trung bình, Ồn ào*.
  * Sắp xếp: *Điểm đánh giá cao nhất, Mới nhất, Đánh giá nhiều nhất*.
  * Giữ trạng thái bộ lọc trên URL phân trang (Pagination query persistence).
* **Trang chi tiết quán học bài (`/place/detail/{slug}`):**
  * Thư viện hình ảnh quán với ảnh đại diện nổi bật.
  * **Huy hiệu trạng thái thời gian thực:** Tự động so sánh giờ hệ thống với khung giờ `open_time` – `close_time` để gắn nhãn `Đang mở cửa` (xanh) hoặc `Đã đóng cửa` (xám).
  * **Huy hiệu thông minh:**
    * `Phù hợp học khuya`: Dành cho quán mở sau 22h30 hoặc 24/24, có sẵn Wifi mạnh và ổ cắm điện.
    * `Giá sinh viên`: Quán có mức giá trung bình từ 15.000đ – 35.000đ.
  * Điểm trung bình tổng hợp (`avg_rating`) kết hợp 3 thanh tiến trình điểm thành phần chi tiết:
    * 📶 *Tốc độ Wifi* (Thang điểm 1 - 5)
    * 🪑 *Không gian & Bàn ghế* (Thang điểm 1 - 5)
    * 💰 *Mức giá hợp lý* (Thang điểm 1 - 5)
  * Khối điểm Google Maps đối chiếu thực tế (kèm link chuyển hướng xem vị trí).
  * Nhúng bản đồ vị trí trực quan bằng Google Maps iframe.
  * Danh sách đánh giá công khai kèm hình ảnh minh chứng của sinh viên (có phân trang).

### 2.2. Phân hệ Thành viên Sinh viên (User)
* **Xác thực tài khoản:** Đăng ký thành viên, Đăng nhập an toàn, Đăng xuất phiên làm việc.
* **Đánh giá & Review quán:**
  * Mỗi sinh viên được gửi 1 đánh giá cho mỗi quán để đảm bảo tính khách quan (chống spam điểm).
  * Chấm điểm tổng quan và 3 điểm tiêu chí học tập (Wifi, Không gian, Giá cả).
  * Đính kèm hình ảnh chụp thực tế tại quán (giới hạn dung lượng và kiểm tra định dạng an toàn).
  * Thành viên có quyền xem lại, chỉnh sửa nội dung hoặc xóa đánh giá của chính mình.
  * Cơ chế kiểm duyệt: Đánh giá của thành viên sẽ ở trạng thái `pending` và chỉ hiển thị công khai sau khi được Admin duyệt.
* **Quản lý danh sách Yêu thích (Favorites):**
  * Lưu/bỏ lưu quán vào danh sách cá nhân bằng công nghệ **AJAX (`fetch` API)** mà không cần tải lại trang.
  * Trang quản lý danh sách quán đã lưu (`/favorite`) giúp sinh viên dễ dàng chọn lại góc học quen thuộc.
* **Trang cá nhân (Profile):**
  * Cập nhật thông tin cá nhân và tải lên ảnh đại diện (Avatar).
  * Đổi mật khẩu tài khoản (yêu cầu xác thực mật khẩu hiện tại).
  * Xem nhanh danh sách lịch sử các bài đánh giá của bản thân và thống kê số quán đã lưu.

### 2.3. Phân hệ Quản trị viên (Admin)
* **Bảng điều khiển tổng quan (Dashboard):**
  * 4 thẻ chỉ số thống kê (KPI cards): Tổng số quán, Tổng số thành viên, Tổng số đánh giá, Số đánh giá đang chờ duyệt.
  * Bảng xếp hạng Top 5 quán có điểm đánh giá học tập cao nhất tại Long Xuyên.
* **Quản lý Quán học bài (Places):**
  * Xem danh sách, tìm kiếm, lọc theo danh mục/trạng thái, phân trang.
  * Thêm mới / Chỉnh sửa thông tin quán: địa chỉ, giờ mở/đóng, khoảng giá VNĐ, tọa độ/link Google Maps, mức độ ồn, tiện ích đính kèm.
  * Tải lên cùng lúc nhiều hình ảnh cho quán, chọn ảnh làm ảnh bìa chính (`is_primary`), xóa ảnh phụ.
  * Thao tác bật/tắt nhanh trạng thái hiển thị quán (`Ẩn / Hiện`).
* **Quản lý Danh mục (Categories):**
  * Thêm, sửa, xóa danh mục quán.
  * Cơ chế toàn vẹn: Chặn xóa danh mục nếu đang có quán trực thuộc.
* **Quản lý Tiện ích học tập (Amenities):**
  * Quản lý các tiện ích (Wifi, Ổ cắm, Máy lạnh, Chỗ đỗ xe, Thú cưng,...).
  * Dữ liệu tiện ích được chuẩn hóa bảng riêng (`amenities` & `place_amenities`), tự động đồng bộ trên toàn bộ bộ lọc và form thêm quán.
  * Chặn xóa tiện ích nếu đang có quán sử dụng.
* **Kiểm duyệt Đánh giá (Reviews Moderation):**
  * Lọc đánh giá theo trạng thái: `Chờ duyệt` (pending), `Đã duyệt` (approved), `Đã ẩn` (hidden).
  * Thao tác Duyệt, Ẩn hoặc Xóa đánh giá vi phạm tiêu chuẩn cộng đồng.
  * **Cơ chế tính điểm tự động:** Mỗi khi một đánh giá được duyệt, chỉnh sửa hoặc xóa, Model `Place` sẽ tự động tính toán lại điểm trung bình (`avg_rating`), các điểm thành phần và tổng số lượt đánh giá (`total_reviews`) của quán.
* **Quản lý Người dùng (Users):**
  * Danh sách thành viên, lọc theo vai trò và trạng thái.
  * Khóa / Mở khóa tài khoản người dùng vi phạm.
  * Quy tắc bảo vệ: Tuyệt đối ngăn chặn hành động tự khóa tài khoản của chính mình hoặc khóa tài khoản Admin khác.

---

## 🛠️ 3. Công Nghệ & Kiến Trúc Kỹ Thuật

```text
+--------------------------------------------------------------------------+
|                               Client Browser                             |
|         (Bootstrap 5.3 + Custom CSS + Vanilla JS AJAX Fetch API)         |
+--------------------------------------------------------------------------+
                                     │  HTTP Requests (.htaccess Rewrite)
                                     ▼
+--------------------------------------------------------------------------+
|                          Front Controller (index.php)                    |
|             (spl_autoload_register + Config + Session Handling)          |
+--------------------------------------------------------------------------+
                                     │  Dispatching URL
                                     ▼
+--------------------------------------------------------------------------+
|                           Router (core/Router.php)                       |
|         Phân tích URI -> Điều phối Controller & Action tương ứng          |
+--------------------------------------------------------------------------+
                                     │
         ┌───────────────────────────┴───────────────────────────┐
         ▼                                                       ▼
+--------------------+                                  +------------------+
|    Controllers     | ── Nhận request & kiểm tra quyền ── |   Session / CSRF |
| (Place, Auth, ...) |                                  +------------------+
+--------------------+
   │               ▲
   │ Gọi xử lý     │ Trả dữ liệu
   ▼               │
+--------------------+                                  +------------------+
|       Models       | ──── Truy vấn PDO Prepared Stmts ──► | MySQL / MariaDB  |
|  (Place, User, ...) |                                  |  (8 Tables UTF8) |
+--------------------+                                  +------------------+
   │
   │ Render dữ liệu
   ▼
+--------------------------------------------------------------------------+
|                         Views & Layouts (PHP + HTML5)                    |
|              (layouts/main.php, layouts/admin.php, XSS escaped)          |
+--------------------------------------------------------------------------+
```

### 3.1. Ngôn ngữ & Nền tảng
- **Backend:** PHP 8.2+ hướng đối tượng (OOP thuần, Class & Method, Strict Types).
- **Kiến trúc:** Mô hình MVC (Model - View - Controller) tự xây dựng gọn nhẹ, không phụ thuộc framework bên thứ ba, dễ bảo trì và mở rộng.
- **Routing:** Front Controller kết hợp Apache `.htaccess` Rewrite Rule để tạo đường dẫn URL thân thiện, chuẩn SEO (`/place/detail/ten-quan`).
- **Database:** MySQL 8 / MariaDB, giao tiếp thông qua **PDO (PHP Data Objects)**.
- **Frontend:**
  - HTML5 chuẩn ngữ nghĩa + CSS3 tùy biến (bảng màu ấm cúng, chuẩn UX).
  - **Bootstrap 5.3.3** & **Bootstrap Icons 1.11.3**.
  - Font chữ: *Be Vietnam Pro* và *Plus Jakarta Sans* tối ưu hiển thị tiếng Việt.
  - JavaScript thuần (**ES6 Fetch API**) xử lý tương tác bất đồng bộ không cần jQuery.

### 3.2. Cấu trúc Cơ sở dữ liệu (8 Bảng)

CSDL được thiết kế chuẩn hóa bậc 3 (3NF) với bảng mã `utf8mb4_unicode_ci` hỗ trợ đầy đủ tiếng Việt có dấu và emoji:

| Tên bảng | Chức năng chính | Các trường quan trọng |
| :--- | :--- | :--- |
| `users` | Lưu trữ tài khoản người dùng & quản trị | `id`, `full_name`, `email`, `password`, `avatar`, `role`, `status` |
| `categories` | Danh mục phân loại quán | `id`, `name`, `slug`, `description`, `icon` |
| `amenities` | Danh mục tiện ích học tập | `id`, `name`, `icon`, `description` |
| `places` | Thông tin chi tiết các quán học bài | `id`, `category_id`, `name`, `slug`, `address`, `ward`, `price_min`, `price_max`, `open_time`, `close_time`, `avg_rating`, `total_reviews` |
| `place_amenities`| Bảng trung gian liên kết Quán & Tiện ích (N-N) | `place_id`, `amenity_id` |
| `place_images` | Lưu trữ các ảnh chụp của quán | `id`, `place_id`, `image_url`, `is_primary`, `caption` |
| `reviews` | Đánh giá & nhận xét của sinh viên | `id`, `place_id`, `user_id`, `rating`, `rating_wifi`, `rating_space`, `rating_price`, `comment`, `image_url`, `status` |
| `favorites` | Danh sách quán đã lưu của thành viên | `id`, `user_id`, `place_id`, `created_at` |

---

## 🔒 4. Điểm Nhấn Kỹ Thuật & Giải Pháp An Ninh (Security)

Hệ thống được lập trình tuân thủ nghiêm ngặt các nguyên tắc bảo mật ứng dụng web theo chuẩn OWASP:

1. **Chống SQL Injection (100% PDO Prepared Statements):**
   - Tuyệt đối không nối chuỗi biến trực tiếp vào câu lệnh SQL.
   - Toàn bộ các câu truy vấn qua tầng Model đều truyền tham số qua Prepared Statements với cơ chế `PDO::ATTR_EMULATE_PREPARES => false`.
2. **Chống tấn công XSS (Cross-Site Scripting):**
   - Mọi dữ liệu do người dùng nhập vào khi in ra màn hình HTML đều được lọc qua hàm helper `e()`:
     ```php
     function e(?string $value): string {
         return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
     }
     ```
3. **Chống tấn công CSRF (Cross-Site Request Forgery):**
   - Khởi tạo `csrf_token` ngẫu nhiên lưu trong Session bằng `bin2hex(random_bytes(32))`.
   - Tất cả các form submit `POST` và request AJAX `fetch` đều được kiểm tra token qua hàm `hash_equals()`.
4. **Bảo mật Mật khẩu & Phiên làm việc (Session Security):**
   - Băm mật khẩu bằng thuật toán mạnh `password_hash($password, PASSWORD_BCRYPT)`.
   - Gọi `session_regenerate_id(true)` ngay khi đăng nhập và đổi mật khẩu thành công nhằm triệt tiêu nguy cơ Session Fixation / Hijacking.
   - Kiểm tra trạng thái hoạt động của tài khoản ở mọi request yêu cầu quyền hạn (chặn ngay tài khoản vừa bị khóa).
5. **Bảo mật Tải lên tập tin (File Upload Hardening):**
   - Không tin tưởng phần mở rộng của client gửi lên; sử dụng thư viện `finfo` (File Information) để đọc kiểu MIME nhị phân thực tế của file.
   - Giới hạn dung lượng tối đa 2MB (`MAX_FILE_SIZE = 2 * 1024 * 1024`).
   - Tự động sinh tên file ngẫu nhiên bằng `uniqid()` kết hợp mã băm để chống tấn công ghi đè file hoặc chèn mã thực thi shell (`.php`).
6. **Cơ chế Hoạt động Bền bỉ (Offline Fallback & Error Resilience):**
   - Hàm `get_image_url($path, $type)` tự động kiểm tra sự tồn tại vật lý của file trên ổ đĩa. Trường hợp mất ảnh hoặc chưa có ảnh, hệ thống tự fallback về ảnh vector SVG cục bộ (`assets/img/placeholder.svg`), hoàn toàn không phụ thuộc CDN ngoài và không làm vỡ layout giao diện.

---

## 📁 5. Cấu Trúc Thư Mục Dự Án

```text
StudySpot/
├── assets/                          # Tài nguyên tĩnh phía Client
│   ├── css/
│   │   └── style.css                # CSS tùy biến toàn bộ giao diện, bảng màu ấm cúng
│   ├── js/
│   │   └── main.js                  # JavaScript xử lý AJAX favorite, modal ảnh, xác nhận xóa
│   ├── img/
│   │   ├── placeholder.svg          # Ảnh fallback quán khi không có ảnh thực tế
│   │   ├── avatar-default.svg       # Avatar mặc định cho người dùng mới
│   │   └── places/                  # Ảnh minh họa cho panel đăng ký / đăng nhập
│   └── uploads/                     # Thư mục lưu trữ file người dùng tải lên
│       ├── avatars/                 # Ảnh đại diện cá nhân
│       ├── places/                  # Hình ảnh chụp các quán học bài
│       └── reviews/                 # Ảnh đính kèm theo đánh giá của sinh viên
├── config/                          # Cấu hình hệ thống
│   ├── config.php                   # Định nghĩa BASE_URL, giới hạn upload, múi giờ Việt Nam
│   └── database.php                 # Thông số kết nối CSDL PDO MySQL
├── controllers/                     # Tầng điều khiển (Controllers)
│   ├── AuthController.php           # Xử lý đăng ký, đăng nhập, đăng xuất
│   ├── FavoriteController.php       # Xử lý lưu/bỏ lưu quán yêu thích (AJAX)
│   ├── HomeController.php           # Xử lý hiển thị trang chủ và quán nổi bật
│   ├── PlaceController.php          # Xử lý khám phá, tìm kiếm, lọc và chi tiết quán
│   ├── ProfileController.php        # Quản lý hồ sơ, đổi mật khẩu, đổi avatar
│   ├── ReviewController.php         # Tạo, sửa, xóa bài đánh giá
│   └── Admin/                       # Phân hệ Quản trị viên
│       ├── AdminAmenityController.php   # Quản lý tiện ích
│       ├── AdminCategoryController.php  # Quản lý danh mục
│       ├── AdminDashboardController.php # Thống kê dashboard KPI
│       ├── AdminPlaceController.php     # CRUD quán học bài và quản lý ảnh
│       ├── AdminReviewController.php    # Kiểm duyệt đánh giá
│       └── AdminUserController.php      # Quản lý và khóa/mở tài khoản
├── core/                            # Thành phần hạt nhân của hệ thống (Mini-Framework)
│   ├── Controller.php               # Controller cha: render view, redirect, json response, auth check
│   ├── Model.php                    # Model cha: kết nối PDO Singleton, thực thi câu lệnh SQL
│   ├── Router.php                   # Điều phối URL đẹp, phân tích controller/action/params
│   └── Session.php                  # Quản lý session, flash messages, phân quyền và CSRF token
├── database/                        # Cơ sở dữ liệu
│   ├── studyspot.sql                # File dump CSDL đầy đủ bảng + 33 quán mẫu thật tại Long Xuyên
│   ├── migrate_20261103_*.sql       # Các script cập nhật cấu trúc bảng
│   └── seed_users.php               # Script hỗ trợ khởi tạo lại mật khẩu các tài khoản mẫu
├── helpers/                         # Các hàm tiện ích dùng chung
│   └── functions.php                # Hàm chống XSS e(), upload_image(), base_url(), get_image_url()...
├── models/                          # Tầng mô hình dữ liệu (Models)
│   ├── Amenity.php                  # Thao tác bảng amenities
│   ├── Category.php                 # Thao tác bảng categories
│   ├── Favorite.php                 # Thao tác bảng favorites
│   ├── Place.php                    # Nghiệp vụ quán học bài, bộ lọc và tính điểm trung bình
│   ├── PlaceImage.php               # Quản lý ảnh của quán
│   ├── Review.php                   # Xử lý đánh giá, duyệt review
│   └── User.php                     # Xác thực, thông tin và trạng thái người dùng
├── views/                           # Tầng giao diện người dùng (Views)
│   ├── admin/                       # Giao diện các trang quản trị
│   ├── auth/                        # Giao diện Đăng nhập / Đăng ký
│   ├── errors/                      # Giao diện trang thông báo lỗi (404 Not Found)
│   ├── favorites/                   # Danh sách quán đã lưu
│   ├── home/                        # Trang chủ
│   ├── layouts/                     # Layout khung chung (header, footer, main, admin)
│   ├── places/                      # Danh sách khám phá quán và trang chi tiết quán
│   ├── profile/                     # Trang thông tin cá nhân và chỉnh sửa hồ sơ
│   └── reviews/                     # Component form viết đánh giá và danh sách đánh giá
├── .htaccess                        # Cấu hình Apache mod_rewrite tạo URL đẹp
├── index.php                        # Điểm đón request duy nhất (Front Controller)
└── README.md                        # Tài liệu hướng dẫn đồ án
```

---

## 🚀 6. Hướng Dẫn Cài Đặt & Triển Khai Trên XAMPP

### 6.1. Yêu cầu môi trường
* **Hệ điều hành:** Windows 10/11, macOS, hoặc Linux (Ubuntu, Debian, Fedora,...).
* **Môi trường Web Server:** XAMPP, WampServer hoặc LAMP Stack.
* **PHP:** Phiên bản **8.1** trở lên (Khuyến nghị **PHP 8.2** trở lên).
  * Yêu cầu các extension PHP: `pdo_mysql`, `fileinfo`, `mbstring`, `session`.
* **Cơ sở dữ liệu:** MySQL 5.7+ hoặc MariaDB 10.4+.
* **Apache:** Bật sẵn module `mod_rewrite` để hỗ trợ URL thân thiện.

---

### 6.2. Các bước cài đặt chi tiết

#### Bước 1: Sao chép mã nguồn vào thư mục Web
- Đưa toàn bộ thư mục dự án `StudySpot` vào thư mục gốc của web server:
  - **Trên Windows (XAMPP):** `C:\xampp\htdocs\studyspot\`
  - **Trên Linux (XAMPP / LAMP):** `/opt/lampp/htdocs/studyspot/` hoặc `/var/www/html/studyspot/`
  - **Trên macOS (XAMPP):** `/Applications/XAMPP/htdocs/studyspot/`

#### Bước 2: Khởi động Dịch vụ
- Mở **XAMPP Control Panel**, nhấn **Start** cho cả hai dịch vụ:
  - **Apache** (Cổng mặc định `80`, `443`)
  - **MySQL** (Cổng mặc định `3306`)

#### Bước 3: Import Cơ sở dữ liệu
1. Truy cập giao diện quản lý cơ sở dữ liệu phpMyAdmin: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
2. Bấm vào thẻ **Import** (Nhập) trên thanh điều hướng phía trên.
3. Nhấn **Choose File** (Chọn tệp) và trỏ tới file:
   ```text
   studyspot/database/studyspot.sql
   ```
4. Cuộn xuống cuối trang và nhấn nút **Import** (Thực hiện).
5. Hệ thống sẽ tự động khởi tạo cơ sở dữ liệu `studyspot` với đầy đủ 8 bảng quan hệ và nạp sẵn **33 quán học bài có thực tại Long Xuyên**, 4 danh mục, 5 tiện ích và 21 bài đánh giá mẫu.

#### Bước 4: Kiểm tra cấu hình kết nối (Nếu cần)
Mặc định hệ thống đã được cấu hình tương thích 100% với XAMPP chuẩn. Nếu máy của bạn sử dụng tài khoản MySQL khác, hãy mở file [config/database.php](file:///home/dyspo/DTH235628/web/StudySpot/config/database.php) để hiệu chỉnh:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studyspot');
define('DB_USER', 'root');
define('DB_PASS', '');       // Nhập mật khẩu MySQL nếu có
define('DB_CHARSET', 'utf8mb4');
```

Nếu bạn đặt tên thư mục khác hoặc đổi cổng Apache (ví dụ `localhost:8080`), hãy điều chỉnh hằng số `BASE_URL` trong [config/config.php](file:///home/dyspo/DTH235628/web/StudySpot/config/config.php):
```php
define('BASE_URL', 'http://localhost/studyspot');
```

#### Bước 5: Truy cập Website & Kiểm thử
- **Trang chủ Người dùng:** [http://localhost/studyspot/](http://localhost/studyspot/)
- **Trang Đăng nhập:** [http://localhost/studyspot/auth/login](http://localhost/studyspot/auth/login)
- **Trang Quản trị (Admin):** [http://localhost/studyspot/admin/dashboard](http://localhost/studyspot/admin/dashboard)

#### Bước 6: Cấu hình đăng nhập Google (@agu.edu.vn) — tùy chọn
Hệ thống hỗ trợ đăng nhập bằng tài khoản Google của Trường Đại học An Giang. Chỉ email `@agu.edu.vn` đã xác minh mới đăng nhập được; tài khoản mới được tạo tự động với vai trò thành viên.
1. Vào [Google Cloud Console](https://console.cloud.google.com/), tạo (hoặc chọn) một project.
2. **APIs & Services → OAuth consent screen:** chọn loại **External**, điền tên ứng dụng (StudySpot) và email hỗ trợ. Khi ứng dụng còn ở chế độ **Testing**, chỉ tài khoản được thêm vào mục **Test users** mới đăng nhập được — nhớ thêm email `@agu.edu.vn` của bạn vào đó.
3. **APIs & Services → Credentials → Create Credentials → OAuth client ID**, loại **Web application**, thêm **Authorized redirect URI** đúng y hệt:
   ```text
   http://localhost/studyspot/auth/google-callback
   ```
4. Sao chép **Client ID** và **Client Secret** vào `config/config.php`:
   ```php
   define('GOOGLE_CLIENT_ID', '...');      // Client ID vừa tạo
   define('GOOGLE_CLIENT_SECRET', '...');  // Client Secret vừa tạo
   ```
   **Lưu ý:** không đẩy 2 giá trị thật lên GitHub công khai.
5. Mở trang đăng nhập, bấm **Đăng nhập với Google** và chọn tài khoản `@agu.edu.vn`.
   - Luồng chạy: `/auth/google` chuyển tới Google (giới hạn tài khoản `@agu.edu.vn`) → Google trả về `/auth/google-callback` kèm mã `code` → server đổi `code` lấy access token → gọi `userinfo` lấy email, tên → kiểm tra tên miền `@agu.edu.vn` và `verified_email` → tìm tài khoản theo email, chưa có thì tạo mới → đăng nhập như bình thường (đổi Session ID, lưu session, chuyển theo role).
   - Không cần cài thêm thư viện: luồng OAuth 2.0 được viết tay bằng PHP thuần (`file_get_contents` + stream context, kiểm tra `state` chống CSRF).

---

## 👥 7. Danh Sách Tài Khoản Thử Nghiệm (Test Accounts)

Cơ sở dữ liệu mẫu đã được nạp sẵn 6 tài khoản người dùng tương ứng với các kịch bản kiểm thử khác nhau. Tất cả tài khoản đều sử dụng chung mật khẩu gốc: **`123456`**.

| STT | Họ và tên | Email đăng nhập | Mật khẩu | Vai trò | Trạng thái | Mục đích kiểm thử tính năng |
| :-: | :--- | :--- | :---: | :---: | :---: | :--- |
| **1** | **Quản Trị Viên StudySpot AGU** | `admin@studyspot.vn` | `123456` | `admin` | Hoạt động | Toàn quyền quản trị hệ thống, duyệt review, thêm/sửa/xóa quán |
| **2** | **Nguyễn Minh Khôi (DH23TH)** | `khoi.nguyen@student.edu.vn` | `123456` | `user` | Hoạt động | Tài khoản sinh viên tiêu biểu (đã có sẵn review và quán yêu thích) |
| **3** | **Trần Thảo Vy (DH23QT)** | `vy.tran@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên thực hiện viết review mới và lưu quán |
| **4** | **Lê Hoàng Nam (DH22PM)** | `nam.le@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên thực hiện đổi thông tin cá nhân và avatar |
| **5** | **Phạm Ngọc Ánh (DH24AV)** | `anh.pham@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên kiểm thử tính năng đổi mật khẩu |
| **6** | **Võ Quốc Bảo (DH23KT)** | `bao.vo@student.edu.vn` | `123456` | `user` | **Đang khóa** | **Kiểm thử tính năng bảo mật: chặn đăng nhập tài khoản bị khóa** |

---

## 🎯 8. Đánh Giá Đề Tài & Hướng Phát Triển

### 8.1. Kết quả đạt được
- Hoàn thành đầy đủ 100% các yêu cầu của học phần **Công nghệ Web – PHP (SEE518)**.
- Xây dựng thành công mô hình MVC thuần chuẩn mực, tách biệt hoàn toàn giữa dữ liệu (Model), logic điều khiển (Controller) và giao diện hiển thị (View).
- Giao diện thân thiện, hiện đại, tối ưu trải nghiệm cho sinh viên trên cả máy tính và điện thoại thông minh.
- Dữ liệu khảo sát thực tế phong phú với **33 quán cà phê học bài thật tại Long Xuyên**, cung cấp giá trị sử dụng thực tiễn cho sinh viên Đại học An Giang.

### 8.2. Hướng phát triển trong tương lai
- **Google Maps Places API:** Tự động đồng bộ giờ mở cửa, số điện thoại và cập nhật đánh giá mới nhất từ Google Maps theo thời gian thực.
- **Tính năng Đặt chỗ / Giữ bàn học nhóm:** Hỗ trợ sinh viên liên hệ hoặc đặt bàn trước vào các mùa thi cao điểm.
- **Gợi ý thông minh (AI Recommendation):** Phân tích sở thích của người dùng để gợi ý quán cà phê phù hợp nhất dựa trên vị trí hiện tại và ngân sách.

---

*Đồ án môn học Công nghệ Web – PHP • Khoa Công nghệ Thông tin • Trường Đại học An Giang (AGU)*  
**© 2026 StudySpot Team. All rights reserved.**
