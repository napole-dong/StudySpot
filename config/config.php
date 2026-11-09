<?php
// Cấu hình chung toàn hệ thống: hằng số BASE_URL, đường dẫn, upload, phân trang.

// Thiết lập múi giờ Việt Nam: để hàm date() và giờ mở/đóng quán tính theo giờ Việt Nam, không bị lệch sang giờ UTC của máy chủ.
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Đường dẫn gốc của website (không có dấu / ở cuối): mọi link và hàm base_url() đều ghép từ hằng số này, nên nếu đổi domain/thư mục thì chỉ sửa một chỗ.
define('BASE_URL', 'http://localhost/studyspot');

// Đường dẫn thư mục gốc trên ổ đĩa: dirname(__DIR__) nghĩa là đi lên một cấp từ thư mục config/ ra thư mục gốc project, dùng để require file và đọc file ảnh.
define('ROOT_PATH', dirname(__DIR__));

// Thông tin thương hiệu website: tên và slogan hiển thị ở tiêu đề trang, header, footer.
define('SITE_NAME', 'StudySpot');
define('SITE_SLOGAN', 'Cộng đồng review quán cà phê & không gian học bài dành cho sinh viên');

// Cấu hình Upload ảnh: ảnh người dùng tải lên được lưu vào thư mục này, hàm upload_image() trong helpers/functions.php sẽ đọc đúng 3 hằng số dưới đây.
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads');
// Giới hạn 2MB: 2 * 1024 * 1024 byte (2 megabyte), ảnh nặng hơn sẽ bị từ chối.
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // Giới hạn 2MB
// Danh sách định dạng ảnh cho phép, dạng cặp "kiểu MIME thật => đuôi file":
define('ALLOWED_MIME_TYPES', [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
]);

// Cấu hình phân trang mặc định: số dòng hiển thị trên một trang, tránh trang
define('ITEMS_PER_PAGE', 9);
// Trang quản trị: 10 dòng mỗi trang (bảng admin thường hiển thị dạng danh sách).
define('ADMIN_ITEMS_PER_PAGE', 10);
// Trang chi tiết quán: 5 đánh giá mỗi trang, để phần đánh giá không lấn phần thông tin quán.
define('REVIEWS_PER_PAGE', 5);

// Đăng nhập Google: Client ID và Secret lấy từ Google Cloud Console (APIs & Services > Credentials > OAuth client ID, loại Web application).
// Điền 2 giá trị thật vào đây trên máy dev; KHÔNG đẩy giá trị thật lên GitHub công khai.
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');
// Chỉ chấp nhận tài khoản Google thuộc tên miền này (email sinh viên/cán bộ AGU).
define('GOOGLE_ALLOWED_DOMAIN', 'agu.edu.vn');
// Link Google trả về sau khi đăng nhập: phải trùng y hệt link đã đăng ký trong Google Cloud Console.
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google-callback');
