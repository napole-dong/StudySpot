<?php
// Router: đổi URL đẹp thành Controller@action + tham số, không khớp thì trả 404.
class Router
{
    // Hàm điều phối chính: không nhận tham số (đọc trực tiếp từ $_GET), không trả giá trị; nhiệm vụ duy nhất là tìm đúng Controller@action và gọi nó.
    public function dispatch(): void
    {
        // Giá trị mặc định khi URL trống (mở trang chủ): không phải admin,
        $isAdmin = false;
        $controllerSegment = 'home';
        $actionSegment = 'index';
        $params = [];

        // Trường hợp 1: Có biến url từ .htaccess (RewriteRule ^(.*)$ index.php?url=$1): đây là chế độ chính, $_GET['url'] chứa toàn bộ phần sau /studyspot/.
        if (!empty($_GET['url'])) {
            // Làm sạch url: FILTER_SANITIZE_URL gỡ ký tự lạ khỏi URL, trim() cắt
            $url = trim(filter_var($_GET['url'], FILTER_SANITIZE_URL), '/');
            $segments = $url !== '' ? explode('/', $url) : [];

            // Nhận biết khu vực admin: đoạn đầu tiên là 'admin' (so sánh không phân
            if (!empty($segments) && strtolower($segments[0]) === 'admin') {
                $isAdmin = true;
                array_shift($segments);
                // Khu admin: đoạn tiếp theo là controller (nếu không có, ví dụ chỉ
                $controllerSegment = !empty($segments[0]) ? array_shift($segments) : 'dashboard';
                $actionSegment = !empty($segments[0]) ? array_shift($segments) : 'index';
                $params = $segments;
            } else {
                // Khu người dùng: tương tự, chỉ khác controller mặc định là 'home'
                $controllerSegment = !empty($segments[0]) ? array_shift($segments) : 'home';
                $actionSegment = !empty($segments[0]) ? array_shift($segments) : 'index';
                $params = $segments;
            }
        }
        // Trường hợp 2: Phương án dự phòng khi không có mod_rewrite (?c=place&a=show&id=5): chỉ chạy khi không có $_GET['url'] mà có c hoặc module.
        elseif (!empty($_GET['c']) || !empty($_GET['module'])) {
            $module = strtolower(trim($_GET['module'] ?? ''));
            $c = trim($_GET['c'] ?? '');

            // Khu admin ở chế độ dự phòng có 2 cách viết: module=admin, hoặc c bắt
            if ($module === 'admin' || str_starts_with(strtolower($c), 'admin/')) {
                $isAdmin = true;
                if (str_starts_with(strtolower($c), 'admin/')) {
                    $c = substr($c, 6);
                }
                $controllerSegment = $c !== '' ? $c : 'dashboard';
            } else {
                $controllerSegment = $c !== '' ? $c : 'home';
            }

            // Chế độ dự phòng: action lấy từ tham số a (mặc định 'index' nếu không
            $actionSegment = !empty($_GET['a']) ? trim($_GET['a']) : 'index';
            if (isset($_GET['id'])) {
                $params[] = $_GET['id'];
            }
        }

        // Chuẩn hóa tên class Controller theo đúng quy tắc đặt tên: đoạn URL viết
        $cleanController = $this->studlyCase($controllerSegment);
        if ($isAdmin) {
            $controllerClass = 'Admin' . $cleanController . 'Controller';
        } else {
            $controllerClass = $cleanController . 'Controller';
        }

        // Chuẩn hóa tên phương thức (action): đổi đoạn URL thành camelCase, tức chữ
        $actionMethod = $this->camelCase($actionSegment);

        // Kiểm tra tồn tại class Controller: nếu không có class tương ứng (do URL gõ
        if (!class_exists($controllerClass)) {
            (new Controller())->abort404("Đường dẫn bạn truy cập không tồn tại, đã thay đổi hoặc tạm thời bị ẩn.");
            return;
        }

        $controllerObject = new $controllerClass();

        // Kiểm tra tồn tại phương thức trong Controller: method_exists() chỉ biết phương thức có tồn tại hay không, kể cả phương thức protected/private.
        if (!method_exists($controllerObject, $actionMethod)) {
            (new Controller())->abort404("Không tìm thấy chức năng yêu cầu.");
            return;
        }

        // Kiểm tra quyền truy cập trực tiếp: dùng Reflection để soi phương thức, nếu
        $reflection = new ReflectionMethod($controllerObject, $actionMethod);
        if (!$reflection->isPublic()) {
            (new Controller())->abort404("Chức năng này không được phép truy cập trực tiếp.");
            return;
        }

        // Gọi Controller@action và truyền các tham số trên URL: call_user_func_array()
        call_user_func_array([$controllerObject, $actionMethod], $params);
    }

        // studlyCase(): 'place-image' thành 'PlaceImage' để làm tên class.
    private function studlyCase(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', strtolower($value));
        return str_replace(' ', '', ucwords($value));
    }

        // camelCase(): 'toggle-status' thành 'toggleStatus' để làm tên phương thức.
    private function camelCase(string $value): string
    {
        return lcfirst($this->studlyCase($value));
    }
}
