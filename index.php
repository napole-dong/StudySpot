<?php
/**
 * Front Controller duy nhất của hệ thống StudySpot
 * Mọi request đều đi qua index.php -> Router -> Controller@action -> View
 */

// 1. Nạp các file cấu hình và hàm tiện ích
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// 2. Đăng ký Autoload tự động nạp class trong core/, models/, controllers/ và controllers/Admin/
spl_autoload_register(function (string $className): void {
    $directories = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/controllers/Admin/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 3. Khởi động Session cho toàn hệ thống
Session::start();

// 4. Điều phối request thông qua Router
$router = new Router();
$router->dispatch();
