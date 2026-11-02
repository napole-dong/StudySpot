<?php
/**
 * Lớp Controller cơ sở: Cung cấp render view, redirect, trả JSON và kiểm tra phân quyền (middleware)
 */
class Controller
{
    /**
     * Hiển thị một View nằm trong một Layout
     *
     * @param string $view   Đường dẫn tương đối trong thư mục views/ (VD: 'home/index')
     * @param array  $data   Mảng dữ liệu truyền sang View
     * @param string $layout Tên layout trong views/layouts/ ('main' hoặc 'admin')
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            $this->abort404("Không tìm thấy giao diện: {$view}");
            return;
        }

        // Giải nén mảng dữ liệu thành các biến cục bộ cho View
        extract($data, EXTR_SKIP);

        // Bắt nội dung của View con vào biến $content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Nạp Layout bao ngoài
        $layoutFile = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Chuyển hướng tới một đường dẫn nội bộ (tự động ghép BASE_URL)
     */
    protected function redirect(string $path = ''): never
    {
        $url = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }

    /**
     * Trả về phản hồi dạng JSON (phục vụ AJAX như Yêu thích)
     */
    protected function json(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Middleware: Yêu cầu người dùng phải đăng nhập và tài khoản đang hoạt động (active)
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('alert', 'Vui lòng đăng nhập để thực hiện chức năng này.', 'warning');
            $this->redirect('auth/login');
        }

        // Kiểm tra lại trạng thái mới nhất từ CSDL phòng trường hợp tài khoản vừa bị Admin khóa
        $sessionUser = Session::user();
        $userModel = new User();
        $freshUser = $userModel->findById((int) $sessionUser['id']);

        if (!$freshUser || $freshUser['status'] === 'locked') {
            Session::destroy();
            Session::start();
            Session::flash('alert', 'Tài khoản của bạn đã bị khóa hoặc không còn tồn tại.', 'danger');
            $this->redirect('auth/login');
        }
    }

    /**
     * Middleware: Chặn truy cập khu vực Admin nếu không có quyền admin
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();

        if (!Session::isAdmin()) {
            Session::flash('alert', 'Bạn không có quyền truy cập vào khu vực Quản trị!', 'danger');
            $this->redirect('');
        }
    }

    /**
     * Kiểm tra CSRF Token cho mọi request POST
     */
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

    /**
     * Hiển thị trang lỗi 404 thân thiện
     */
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
