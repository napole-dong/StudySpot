<?php
/**
 * Lớp Router: Phân tích URL và điều phối tới Controller@action tương ứng
 * Hỗ trợ cả 2 chế độ:
 * 1. URL đẹp qua .htaccess: /place/show/5 hoặc /admin/place/edit/3
 * 2. Query string dự phòng: ?c=place&a=show&id=5 hoặc ?module=admin&c=place&a=edit&id=3
 */
class Router
{
    public function dispatch(): void
    {
        $isAdmin = false;
        $controllerSegment = 'home';
        $actionSegment = 'index';
        $params = [];

        // Trường hợp 1: Có biến url từ .htaccess (RewriteRule ^(.*)$ index.php?url=$1)
        if (!empty($_GET['url'])) {
            $url = trim(filter_var($_GET['url'], FILTER_SANITIZE_URL), '/');
            $segments = $url !== '' ? explode('/', $url) : [];

            if (!empty($segments) && strtolower($segments[0]) === 'admin') {
                $isAdmin = true;
                array_shift($segments);
                $controllerSegment = !empty($segments[0]) ? array_shift($segments) : 'dashboard';
                $actionSegment = !empty($segments[0]) ? array_shift($segments) : 'index';
                $params = $segments;
            } else {
                $controllerSegment = !empty($segments[0]) ? array_shift($segments) : 'home';
                $actionSegment = !empty($segments[0]) ? array_shift($segments) : 'index';
                $params = $segments;
            }
        }
        // Trường hợp 2: Phương án dự phòng khi không có mod_rewrite (?c=place&a=show&id=5)
        elseif (!empty($_GET['c']) || !empty($_GET['module'])) {
            $module = strtolower(trim($_GET['module'] ?? ''));
            $c = trim($_GET['c'] ?? '');

            if ($module === 'admin' || str_starts_with(strtolower($c), 'admin/')) {
                $isAdmin = true;
                if (str_starts_with(strtolower($c), 'admin/')) {
                    $c = substr($c, 6);
                }
                $controllerSegment = $c !== '' ? $c : 'dashboard';
            } else {
                $controllerSegment = $c !== '' ? $c : 'home';
            }

            $actionSegment = !empty($_GET['a']) ? trim($_GET['a']) : 'index';
            if (isset($_GET['id'])) {
                $params[] = $_GET['id'];
            }
        }

        // Chuẩn hóa tên class Controller theo đúng quy tắc đặt tên
        $cleanController = $this->studlyCase($controllerSegment);
        if ($isAdmin) {
            $controllerClass = 'Admin' . $cleanController . 'Controller';
        } else {
            $controllerClass = $cleanController . 'Controller';
        }

        // Chuẩn hóa tên phương thức (action)
        $actionMethod = $this->camelCase($actionSegment);

        // Kiểm tra tồn tại class Controller
        if (!class_exists($controllerClass)) {
            (new Controller())->abort404("Đường dẫn bạn truy cập không tồn tại, đã thay đổi hoặc tạm thời bị ẩn.");
            return;
        }

        $controllerObject = new $controllerClass();

        // Kiểm tra tồn tại phương thức public trong Controller
        if (!method_exists($controllerObject, $actionMethod)) {
            (new Controller())->abort404("Không tìm thấy chức năng yêu cầu.");
            return;
        }

        $reflection = new ReflectionMethod($controllerObject, $actionMethod);
        if (!$reflection->isPublic()) {
            (new Controller())->abort404("Chức năng này không được phép truy cập trực tiếp.");
            return;
        }

        // Gọi Controller@action và truyền các tham số trên URL
        call_user_func_array([$controllerObject, $actionMethod], $params);
    }

    /**
     * Chuyển chuỗi 'place-image' hoặc 'place' thành 'PlaceImage' / 'Place'
     */
    private function studlyCase(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', strtolower($value));
        return str_replace(' ', '', ucwords($value));
    }

    /**
     * Chuyển chuỗi 'toggle-status' thành 'toggleStatus'
     */
    private function camelCase(string $value): string
    {
        return lcfirst($this->studlyCase($value));
    }
}
