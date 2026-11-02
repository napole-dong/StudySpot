<?php
/**
 * File cấu hình chung của hệ thống StudySpot
 * Mọi đường dẫn gốc đều lấy từ hằng số BASE_URL duy nhất tại đây
 */

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Đường dẫn gốc của website (không có dấu / ở cuối)
define('BASE_URL', 'http://localhost/studyspot');

// Đường dẫn thư mục gốc trên ổ đĩa
define('ROOT_PATH', dirname(__DIR__));

// Thông tin thương hiệu website
define('SITE_NAME', 'StudySpot');
define('SITE_SLOGAN', 'Cộng đồng review quán cà phê & không gian học bài dành cho sinh viên');

// Cấu hình Upload ảnh
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // Giới hạn 2MB
define('ALLOWED_MIME_TYPES', [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
]);

// Cấu hình phân trang mặc định
define('ITEMS_PER_PAGE', 9);
define('ADMIN_ITEMS_PER_PAGE', 10);
define('REVIEWS_PER_PAGE', 5);
