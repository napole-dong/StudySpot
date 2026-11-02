# StudySpot – Website Review Quán Ăn & Cà Phê Học Bài Dành Cho Sinh Viên

**Đồ án Học phần:** Công nghệ Web – PHP (Mã học phần: **SEE518**)  
**Kiến trúc:** Mini-MVC OOP thuần (PHP 8.x) + PDO MySQL + Bootstrap 5.3 + Chart.js  
**Môi trường chạy:** XAMPP (Apache + MySQL/MariaDB + PHP 8.x)

---

## 1. Giới thiệu Dự án

**StudySpot** là nền tảng cộng đồng giúp sinh viên tìm kiếm, tra cứu và đánh giá các quán cà phê học bài, thư viện mở, co-working space và quán mở xuyên đêm 24/7 quanh các cụm trường đại học.

### Các tính năng nổi bật:
- **Khách (Chưa đăng nhập):**
  - Trang chủ với Banner tìm kiếm nhanh, 4 Tag gợi ý nhanh (*Wifi mạnh, Nhiều ổ cắm, Yên tĩnh, Dưới 50k*), Danh mục không gian, Quán nổi bật điểm cao và Quán mới.
  - Khám phá & Lọc đa tiêu chí: Tìm theo tên/địa chỉ, lọc theo loại hình, quận/khu vực, mức giá, độ ồn, Wifi, ổ cắm, máy lạnh; sắp xếp theo điểm cao nhất/mới nhất/giá; phân trang giữ trạng thái lọc.
  - Trang chi tiết quán: Slider nhiều ảnh (Bootstrap Carousel), tự động so sánh giờ hiện tại để hiển thị badge **"Đang mở cửa"** hoặc **"Đã đóng cửa"**, bản đồ Google Maps nhúng `iframe`, điểm trung bình tổng + 3 thanh điểm thành phần (*Wifi, Không gian, Giá cả*), danh sách review đã duyệt có phân trang.
  - Nhãn tự động thông minh: **"Phù hợp chạy deadline"** (mở sau 22:00 + có Wifi + có Ổ cắm) và **"Giá sinh viên"** (`price_range <= 2`).
- **Thành viên (Đã đăng nhập):**
  - Đăng ký, Đăng nhập, Đăng xuất an toàn (`password_hash` / `password_verify`, `session_regenerate_id`, chặn tài khoản bị khóa).
  - Viết đánh giá (Review) kèm ảnh chụp thực tế: mỗi thành viên đánh giá 1 lần/quán, tự sửa/xóa đánh giá của chính mình. Review mới ở trạng thái `pending` chờ Admin duyệt.
  - Lưu / Bỏ lưu quán Yêu thích bằng **AJAX (`fetch` API)** không tải lại trang.
  - Trang cá nhân: Cập nhật thông tin, đổi Avatar, đổi mật khẩu, xem lịch sử đánh giá và danh sách quán yêu thích.
- **Quản trị viên (Admin):**
  - **Dashboard:** 4 thẻ thống kê KPI tổng quan, bảng Top 5 quán điểm cao nhất và biểu đồ cột **Chart.js** thống kê số lượng review theo tháng.
  - **Quản lý Quán học bài:** Tìm kiếm, lọc, phân trang, thêm/sửa/xóa, ẩn/hiện nhanh, upload nhiều ảnh cùng lúc, chọn ảnh bìa chính.
  - **Quản lý Danh mục:** Thêm, sửa, xóa (chặn xóa danh mục đang có quán trực thuộc).
  - **Kiểm duyệt Review:** Lọc theo trạng thái (`pending`/`approved`/`hidden`), Duyệt / Ẩn / Xóa kèm cơ chế **tự động tính lại điểm trung bình (`avg_rating`) và tổng số đánh giá (`total_reviews`)** của quán ngay trong tầng Model.
  - **Quản lý Người dùng:** Tìm kiếm, lọc, Khóa / Mở khóa tài khoản (chặn tự khóa chính mình hoặc khóa Admin khác).

---

## 2. Cấu trúc Thư mục Dự án (`studyspot/`)

```text
studyspot/
├── config/
│   ├── config.php           # Định nghĩa duy nhất hằng số BASE_URL, cấu hình upload, múi giờ Asia/Ho_Chi_Minh
│   └── database.php         # Cấu hình kết nối PDO MySQL (studyspot, root, utf8mb4)
├── core/
│   ├── Router.php           # Điều phối URL đẹp (.htaccess) và query string dự phòng (?c=...&a=...)
│   ├── Controller.php       # Lớp Controller cơ sở: render(), redirect(), json(), requireLogin(), requireAdmin()
│   ├── Model.php            # Lớp Model cơ sở: kết nối PDO Singleton & Prepared Statements
│   └── Session.php          # Quản lý Session, Flash Message, phân quyền & CSRF Token
├── models/
│   ├── User.php
│   ├── Category.php
│   ├── Place.php
│   ├── PlaceImage.php
│   ├── Review.php
│   └── Favorite.php
├── controllers/
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── PlaceController.php
│   ├── ReviewController.php
│   ├── FavoriteController.php
│   ├── ProfileController.php
│   └── Admin/
│       ├── AdminDashboardController.php
│       ├── AdminPlaceController.php
│       ├── AdminCategoryController.php
│       ├── AdminReviewController.php
│       └── AdminUserController.php
├── views/
│   ├── layouts/ (main.php, admin.php, header.php, footer.php)
│   ├── home/index.php
│   ├── auth/ (login.php, register.php)
│   ├── places/ (index.php, show.php)
│   ├── reviews/ (_form.php, _list.php)
│   ├── favorites/index.php
│   ├── profile/ (index.php, edit.php)
│   ├── errors/404.php
│   └── admin/ (dashboard.php, places/, categories/, reviews/, users/)
├── helpers/
│   └── functions.php        # Hàm e() chống XSS, upload_image() với finfo, get_image_url() fallback SVG...
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   ├── img/ (placeholder.svg, avatar-default.svg) # Ảnh SVG cục bộ hoạt động 100% offline
│   └── uploads/ (places/, avatars/, reviews/)
├── database/
│   ├── studyspot.sql        # Cấu trúc 6 bảng + khóa ngoại + dữ liệu mẫu thực tế
│   └── seed_users.php       # Script chạy 1 lần để đặt mật khẩu hash thật ('123456') cho tài khoản mẫu
├── .htaccess
├── index.php                # Front Controller duy nhất + spl_autoload_register()
└── README.md
```

---

## 3. Hướng dẫn Cài đặt & Chạy Đồ án trên XAMPP

### Bước 1: Đặt mã nguồn vào thư mục `htdocs`
- Giải nén hoặc đặt thư mục dự án vào đúng đường dẫn:
  - Trên Windows: `C:\xampp\htdocs\studyspot\`
  - Trên Linux: `/opt/lampp/htdocs/studyspot/`
- Mở **XAMPP Control Panel**, khởi động **Apache** và **MySQL**.

### Bước 2: Import Cơ sở dữ liệu qua phpMyAdmin
1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin/`
2. Bấm vào thẻ **Import** (Nhập) ở thanh menu phía trên.
3. Bấm **Choose File** (Chọn tệp) và chọn file `database/studyspot.sql` trong thư mục dự án.
4. Kéo xuống dưới và bấm nút **Import** (Thực hiện). Hệ thống sẽ tự động tạo CSDL `studyspot` (`utf8mb4`) cùng đầy đủ 6 bảng và dữ liệu mẫu.

### Bước 3: Chạy `seed_users.php` để khởi tạo mật khẩu thật
1. Sau khi import SQL xong, mở trình duyệt truy cập đường dẫn:
   ```
   http://localhost/studyspot/database/seed_users.php
   ```
2. Script này sẽ dùng hàm `password_hash('123456', PASSWORD_DEFAULT)` để cập nhật mật khẩu mã hóa Bcrypt thật cho toàn bộ 6 tài khoản mẫu và in bảng xác nhận ra màn hình.
3. *(Khuyến nghị bảo mật)*: Sau khi chạy xong 1 lần, bạn có thể xóa hoặc đổi tên file `database/seed_users.php`.

### Bước 4: Truy cập Website
- **Trang chủ Người dùng:** `http://localhost/studyspot/`
- **Trang Đăng nhập:** `http://localhost/studyspot/auth/login`
- **Trang Quản trị (Admin):** `http://localhost/studyspot/admin/dashboard`

*(Lưu ý: Nếu cần đổi cổng Apache khác 80 hoặc đổi tên thư mục, bạn chỉ cần sửa duy nhất hằng số `BASE_URL` trong file `config/config.php`).*

---

## 4. Danh sách Tài khoản Mẫu (Mật khẩu gốc: `123456`)

Tất cả tài khoản mẫu dưới đây đều có chung mật khẩu gốc là **`123456`** (sau khi chạy `seed_users.php`):

| STT | Họ và tên | Email đăng nhập | Mật khẩu | Vai trò | Trạng thái | Ghi chú kiểm thử |
| :-: | :--- | :--- | :---: | :---: | :---: | :--- |
| 1 | **Quản Trị Viên StudySpot** | `admin@studyspot.vn` | `123456` | `admin` | Hoạt động | Toàn quyền truy cập `/admin/dashboard` |
| 2 | Nguyễn Minh Khôi | `khoi.nguyen@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên (có sẵn review & quán yêu thích) |
| 3 | Trần Thảo Vy | `vy.tran@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 4 | Lê Hoàng Nam | `nam.le@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 5 | Phạm Ngọc Ánh | `anh.pham@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 6 | Võ Quốc Bảo | `bao.vo@student.edu.vn` | `123456` | `user` | **Đang khóa** | Dùng để kiểm thử tính năng chặn đăng nhập |

---

## 5. Điểm nhấn Bảo mật & Chất lượng Kỹ thuật

1. **Chống SQL Injection:** 100% truy vấn CSDL đi qua PDO Prepared Statements (`PDO::ATTR_EMULATE_PREPARES => false`).
2. **Chống XSS (Cross-Site Scripting):** Mọi dữ liệu xuất ra HTML đều được lọc qua hàm helper `e()` (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`).
3. **Chống CSRF (Cross-Site Request Forgery):** Mọi biểu mẫu `POST` và request AJAX đều đính kèm và đối chiếu `csrf_token` bằng `hash_equals()`.
4. **Bảo mật Mật khẩu & Phiên:** Sử dụng `password_hash()` / `password_verify()` và gọi `session_regenerate_id(true)` ngay sau khi đăng nhập/đổi mật khẩu.
5. **Bảo mật Upload Ảnh:** Chỉ cho phép `jpg`, `png`, `webp` dung lượng `<= 2MB`, kiểm tra MIME thực tế từ nội dung file bằng `finfo`, tự động đổi tên file ngẫu nhiên.
6. **Hoạt động bền bỉ (Offline Fallback):** Hàm `get_image_url($path, $type)` tự động kiểm tra sự tồn tại của file trên ổ đĩa và trả về ảnh SVG nội bộ (`assets/img/placeholder.svg` hoặc `assets/img/avatar-default.svg`) nếu ảnh chưa tồn tại, tuyệt đối không bị vỡ giao diện khi mất kết nối mạng.
