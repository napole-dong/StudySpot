<?php
// Lớp cha của mọi Controller: render, redirect, JSON, kiểm tra đăng nhập/quyền, CSRF, 404.
class Controller
{
        // render(): chạy View con rồi đặt vào Layout và in HTML.
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Ghép đường dẫn tuyệt đối tới file View: thư mục gốc + views/ + tên view + .php.
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        // Nếu file View không tồn tại thì không cố require (sẽ lỗi nặng), mà trả
        if (!file_exists($viewFile)) {
            $this->abort404("Không tìm thấy giao diện: {$view}");
            return;
        }

        // Giải nén mảng dữ liệu thành các biến cục bộ cho View: extract() biến
        extract($data, EXTR_SKIP);

        // Bắt nội dung của View con vào biến $content: ob_start() bật bộ đệm để
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Nạp Layout bao ngoài: Layout sẽ echo biến $content ở đúng vị trí giữa
        $layoutFile = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

        // redirect(): chuyển trang nội bộ theo BASE_URL rồi exit.
    protected function redirect(string $path = ''): never
    {
        $url = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

        // json(): trả JSON cho AJAX, mặc định 200, giữ tiếng Việt có dấu.
    protected function json(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

        // requireLogin(): chặn khi chưa đăng nhập; đọc lại user từ CSDL vì admin có thể vừa khóa tài khoản.
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('alert', 'Vui lòng đăng nhập để thực hiện chức năng này.', 'warning');
            $this->redirect('auth/login');
        }

        // Đọc lại user từ CSDL vì admin có thể vừa khóa tài khoản trong lúc đang đăng nhập.
        $sessionUser = Session::user();
        $userModel = new User();
        $freshUser = $userModel->findById((int) $sessionUser['id']);

        if (!$freshUser || $freshUser['status'] === 'locked') {
            // Hủy session cũ, start lại session trống chỉ để ghi flash báo bị khóa.
            Session::destroy();
            Session::start();
            Session::flash('alert', 'Tài khoản của bạn đã bị khóa hoặc không còn tồn tại.', 'danger');
            $this->redirect('auth/login');
        }
    }

        // requireAdmin(): phải đăng nhập rồi role mới là 'admin'.
    protected function requireAdmin(): void
    {
        $this->requireLogin();

        if (!Session::isAdmin()) {
            Session::flash('alert', 'Bạn không có quyền truy cập vào khu vực Quản trị!', 'danger');
            $this->redirect('');
        }
    }

        // verifyCsrfToken(): chặn POST không có CSRF token đúng (form hoặc header AJAX).
    protected function verifyCsrfToken(): void
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Session::verifyCsrf($token)) {
            Session::flash('alert', 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn (CSRF). Vui lòng thử lại.', 'danger');
            $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
            header('Location: ' . $referer);
            exit;
        }
    }

        // abort404(): trả trang 404 thân thiện với mã HTTP 404.
    public function abort404(string $message = 'Trang bạn tìm kiếm không tồn tại hoặc đã bị xóa.'): void
    {
        http_response_code(404);
        $pageTitle = '404 - Không tìm thấy trang';
        $errorMessage = $message;

        ob_start();
        require ROOT_PATH . '/views/errors/404.php';
        $content = ob_get_clean();

        require ROOT_PATH . '/views/layouts/main.php';
        exit;
    }
}
