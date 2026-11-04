<?php
// Front controller: nạp config, autoload, session, rồi Router điều phối.

// 1. Nạp các file cấu hình và hàm tiện ích: phải nạp TRƯỚC autoload vì các
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// 2. Đăng ký Autoload: khi code viết "new PlaceController()" mà class chưa được
spl_autoload_register(function (string $className): void {
    $directories = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/controllers/Admin/',
    ];

    // Duyệt từng thư mục theo thứ tự core -> models -> controllers -> Admin:
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 3. Khởi động Session cho toàn hệ thống: phải chạy TRƯỚC Router vì Router/
Session::start();

// 4. Điều phối request thông qua Router: tạo Router rồi gọi dispatch(), từ đây
$router = new Router();
$router->dispatch();
