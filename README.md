# StudySpot – Website Review Quán Nước & Cà Phê Học Bài Dành Cho Sinh Viên

## Thông Tin Đồ Án & Học Phần
- **Cơ sở đào tạo:** Trường Đại học An Giang (AGU) – Đại học Quốc gia TP. Hồ Chí Minh
- **Khoa:** Khoa Công nghệ Thông tin
- **Học phần:** Công nghệ Web – PHP (Mã học phần: **SEE518**)
- **Đề tài:** StudySpot – Nền tảng Review Quán Nước & Cà Phê Học Bài Sinh Viên
- **Địa bàn triển khai thực tế:** Thành phố Long Xuyên, Tỉnh An Giang (Khu vực quanh Trường Đại học An Giang - AGU)
- **Kiến trúc & Công nghệ:** PHP 8 MVC OOP thuần + PDO MySQL + Bootstrap 5.3
- **Môi trường chạy:** XAMPP (Apache 2.4 + MySQL/MariaDB + PHP 8.2+)

---

## 1. Giới thiệu Dự án

**StudySpot** là nền tảng cộng đồng giúp sinh viên tìm kiếm, tra cứu và đánh giá các quán cà phê, trà sữa và quán nước có không gian học bài tại TP. Long Xuyên (khu vực quanh Trường Đại học An Giang - AGU), bao gồm cả quán mở khuya và quán mở 24/24.

### Các tính năng nổi bật:
- **Khách (Chưa đăng nhập):**
  - Trang chủ: Hero tìm kiếm nhanh với ảnh bìa quán nổi bật (tự chọn theo điểm cao + lượt lưu), danh sách Quán nổi bật có phân trang.
  - Khám phá & Lọc đa tiêu chí: Tìm theo tên/địa chỉ; lọc theo danh mục, phường, khoảng giá (VNĐ), độ ồn, tiện ích (Wifi mạnh, Ổ cắm điện, Máy lạnh, Có chỗ đậu xe, Có thú cưng); sắp xếp theo điểm cao nhất/mới nhất; phân trang giữ trạng thái lọc.
  - Trang chi tiết quán: Thư viện ảnh, tự động so sánh giờ hiện tại để hiển thị badge **"Đang mở cửa"** hoặc **"Đã đóng cửa"**, bản đồ Google Maps nhúng `iframe`, điểm trung bình tổng + 3 thanh điểm thành phần (*Tốc độ Wifi, Không gian & Bàn ghế, Mức giá hợp lý*), danh sách đánh giá công khai có phân trang, khối điểm Google khi quán có dữ liệu Google.
  - Nhãn tự động thông minh: **"Phù hợp học khuya"** (mở khuya + có Wifi + có Ổ cắm) và **"Giá sinh viên"** (`price_range <= 2`).
  - Giao diện: Khung 1200px, hệ padding đồng đều, menu mobile dạng sidebar trượt, header người dùng và quản trị đồng nhất.
- **Thành viên (Đã đăng nhập):**
  - Đăng ký, Đăng nhập, Đăng xuất an toàn (`password_hash` / `password_verify`, `session_regenerate_id`, chặn tài khoản bị khóa).
  - Viết đánh giá (Review) kèm ảnh chụp thực tế: mỗi thành viên đánh giá 1 lần/quán, tự sửa/xóa đánh giá của chính mình. Review mới ở trạng thái `pending` chờ Admin duyệt.
  - Lưu / Bỏ lưu quán Yêu thích bằng **AJAX (`fetch` API)** không tải lại trang.
  - Trang cá nhân: Cập nhật thông tin, đổi Avatar, đổi mật khẩu, xem lịch sử đánh giá và tổng số quán yêu thích (liên kết sang trang Yêu thích).
  - Giao diện đăng nhập/đăng ký: Panel ảnh quán cà phê phủ kín, 2 trang cao bằng nhau.
- **Quản trị viên (Admin):**
  - **Dashboard:** 4 thẻ thống kê KPI tổng quan và bảng Top 5 quán điểm cao nhất (full chiều rộng).
  - **Quản lý Quán học bài:** Tìm kiếm, lọc, phân trang, thêm/sửa/xóa, ẩn/hiện nhanh, nhập giá thấp nhất/cao nhất bằng VNĐ, chọn tiện ích từ bảng riêng, upload nhiều ảnh cùng lúc, chọn ảnh bìa chính.
  - **Quản lý Danh mục:** Thêm, sửa, xóa (chặn xóa danh mục đang có quán trực thuộc).
  - **Quản lý Tiện ích:** Thêm, sửa, xóa tiện ích (chặn xóa tiện ích đang được quán sử dụng); tiện ích lưu ở bảng riêng và tự động cập nhật trên form, bộ lọc và thẻ quán.
  - **Giao diện quản trị:** Không sidebar, dùng top-nav 2 hàng chung khung với trang người dùng; Danh mục và Tiện ích gộp trong dropdown **Phân loại**; cột thao tác thống nhất (nút icon nhỏ + tooltip).
  - **Kiểm duyệt Review:** Lọc theo trạng thái (`pending`/`approved`/`hidden`), Duyệt / Ẩn / Xóa kèm cơ chế **tự động tính lại điểm trung bình (`avg_rating`) và tổng số đánh giá (`total_reviews`)** của quán ngay trong tầng Model. Review do Admin tự viết được đăng ngay, review của thành viên chờ duyệt.
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
│   ├── Amenity.php
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
│       ├── AdminAmenityController.php
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
│   └── admin/ (dashboard.php, places/, categories/, amenities/, reviews/, users/)
├── helpers/
│   └── functions.php        # Hàm e() chống XSS, upload_image() với finfo, get_image_url() fallback SVG...
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   ├── img/ (placeholder.svg, avatar-default.svg, places/ — ảnh quán cà phê cho panel đăng nhập/đăng ký)
│   └── uploads/ (places/, avatars/, reviews/)
├── database/
│   ├── studyspot.sql        # 8 bảng + khóa ngoại + dữ liệu thật Long Xuyên, đồng bộ ngày 2026-11-04 (33 quán, 4 danh mục, 5 tiện ích, 21 đánh giá)
│   ├── migrate_20261103_*.sql # Lịch sử thay đổi bảng (setup mới không cần chạy, seed đã bao gồm)
│   └── seed_users.php       # Tiện ích đặt lại mật khẩu mẫu (không bắt buộc: seed đã chứa hash thật)
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
4. Kéo xuống dưới và bấm nút **Import** (Thực hiện). Hệ thống sẽ tự động tạo CSDL `studyspot` (`utf8mb4`) cùng đầy đủ 8 bảng và dữ liệu thật Long Xuyên (33 quán, 4 danh mục, 5 tiện ích, 21 đánh giá; seed đồng bộ ngày 2026-11-04).

### Bước 3: Truy cập Website
(Không cần chạy thêm bước nào: seed đã chứa mật khẩu mã hóa Bcrypt thật của 6 tài khoản mẫu, đăng nhập ngay bằng mật khẩu `123456` ở mục 4. File `database/seed_users.php` chỉ còn dùng khi muốn đặt lại mật khẩu mẫu.)
- **Trang chủ Người dùng:** `http://localhost/studyspot/`
- **Trang Đăng nhập:** `http://localhost/studyspot/auth/login`
- **Trang Quản trị (Admin):** `http://localhost/studyspot/admin/dashboard`

*(Lưu ý: Nếu cần đổi cổng Apache khác 80 hoặc đổi tên thư mục, bạn chỉ cần sửa duy nhất hằng số `BASE_URL` trong file `config/config.php`).*

---

## 4. Danh sách Tài khoản Mẫu (Mật khẩu gốc: `123456`)

Tất cả tài khoản mẫu dưới đây đều có chung mật khẩu là **`123456`** (đã có sẵn trong seed):

| STT | Họ và tên | Email đăng nhập | Mật khẩu | Vai trò | Trạng thái | Ghi chú kiểm thử |
| :-: | :--- | :--- | :---: | :---: | :---: | :--- |
| 1 | **Quản Trị Viên StudySpot AGU** | `admin@studyspot.vn` | `123456` | `admin` | Hoạt động | Toàn quyền truy cập `/admin/dashboard` |
| 2 | Nguyễn Minh Khôi (DH23TH) | `khoi.nguyen@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên (có sẵn review & quán yêu thích) |
| 3 | Trần Thảo Vy (DH23QT) | `vy.tran@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 4 | Lê Hoàng Nam (DH22PM) | `nam.le@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 5 | Phạm Ngọc Ánh (DH24AV) | `anh.pham@student.edu.vn` | `123456` | `user` | Hoạt động | Sinh viên |
| 6 | Võ Quốc Bảo (DH23KT) | `bao.vo@student.edu.vn` | `123456` | `user` | **Đang khóa** | Dùng để kiểm thử tính năng chặn đăng nhập |

---

## 5. Điểm nhấn Bảo mật & Chất lượng Kỹ thuật

1. **Chống SQL Injection:** 100% truy vấn CSDL đi qua PDO Prepared Statements (`PDO::ATTR_EMULATE_PREPARES => false`).
2. **Chống XSS (Cross-Site Scripting):** Mọi dữ liệu xuất ra HTML đều được lọc qua hàm helper `e()` (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`).
3. **Chống CSRF (Cross-Site Request Forgery):** Mọi biểu mẫu `POST` và request AJAX đều đính kèm và đối chiếu `csrf_token` bằng `hash_equals()`.
4. **Bảo mật Mật khẩu & Phiên:** Sử dụng `password_hash()` / `password_verify()` và gọi `session_regenerate_id(true)` ngay sau khi đăng nhập/đổi mật khẩu.
5. **Bảo mật Upload Ảnh:** Chỉ cho phép `jpg`, `png`, `webp` dung lượng `<= 2MB`, kiểm tra MIME thực tế từ nội dung file bằng `finfo`, tự động đổi tên file ngẫu nhiên.
6. **Hoạt động bền bỉ (Offline Fallback):** Hàm `get_image_url($path, $type)` tự động kiểm tra sự tồn tại của file trên ổ đĩa và trả về ảnh SVG nội bộ (`assets/img/placeholder.svg` hoặc `assets/img/avatar-default.svg`) nếu ảnh chưa tồn tại, tuyệt đối không bị vỡ giao diện khi mất kết nối mạng.
