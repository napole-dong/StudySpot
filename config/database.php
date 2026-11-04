<?php
// Kết nối MySQL XAMPP: DB_HOST, DB_NAME studyspot, bảng mã utf8mb4.

// Máy chủ MySQL: 'localhost' nghĩa là MySQL chạy cùng máy với web server (XAMPP).
define('DB_HOST', 'localhost');
// Tên cơ sở dữ liệu phải tạo sẵn trong phpMyAdmin/MySQL trước khi chạy web.
define('DB_NAME', 'studyspot');
// Tài khoản MySQL mặc định của XAMPP (chưa đặt mật khẩu, chỉ dùng ở máy dev).
define('DB_USER', 'root');
// Mật khẩu trống là mặc định của tài khoản root trên XAMPP; khi đưa lên máy chủ thật phải đổi thành mật khẩu mạnh, không để trống như ở đây.
define('DB_PASS', '');
// Bảng mã utf8mb4: lưu được tiếng Việt có dấu đầy đủ và cả emoji, tránh lỗi chữ bị biến thành dấu hỏi khi ghi tên quán hay nội dung đánh giá.
define('DB_CHARSET', 'utf8mb4');
