<?php
/**
 * Lớp Session: Quản lý phiên làm việc, đăng nhập, phân quyền, Flash Message và CSRF Token
 */
class Session
{
    /**
     * Khởi tạo session an toàn
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Lưu giá trị vào session
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Lấy giá trị từ session
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Kiểm tra sự tồn tại của key trong session
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Xóa một key khỏi session
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Hủy toàn bộ session (dùng khi đăng xuất)
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Làm mới Session ID sau khi đăng nhập thành công (chống Session Fixation)
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Thiết lập hoặc lấy thông báo Flash Message (chỉ hiển thị 1 lần)
     */
    public static function flash(string $key, ?string $message = null, string $type = 'success'): ?array
    {
        if ($message !== null) {
            $_SESSION['_flash'][$key] = [
                'message' => $message,
                'type'    => $type,
            ];
            return null;
        }

        if (isset($_SESSION['_flash'][$key])) {
            $flash = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $flash;
        }

        return null;
    }

    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Lấy thông tin người dùng đang đăng nhập
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Kiểm tra người dùng hiện tại có phải Admin không
     */
    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    /**
     * Sinh hoặc lấy CSRF Token hiện tại
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Kiểm tra tính hợp lệ của CSRF Token gửi từ form POST
     */
    public static function verifyCsrf(?string $token): bool
    {
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}
