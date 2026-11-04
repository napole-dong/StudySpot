<?php
// Session: gói $_SESSION thành hàm tĩnh — lưu/lấy, đăng nhập, flash message, CSRF.
class Session
{
        // start(): mở session nếu chưa chạy, tránh gọi session_start() 2 lần.
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

        // set(): lưu 1 cặp key => value vào $_SESSION.
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

        // get(): lấy giá trị theo key, chưa có thì trả $default.
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

        // has(): kiểm tra key tồn tại và khác null.
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

        // remove(): xóa 1 key khỏi $_SESSION.
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

        // destroy(): đăng xuất sạch — xóa $_SESSION, xóa cookie session id, hủy phiên.
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

        // regenerate(): đổi session id mới sau khi đăng nhập, chống Session Fixation.
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

        // flash(): ghi thì lưu vào _flash, đọc thì lấy xong xóa ngay nên chỉ hiện 1 lần.
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

        // isLoggedIn(): đã đăng nhập khi session có user và id khác rỗng.
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

        // user(): trả mảng user trong session, hoặc null; đây là bản lúc đăng nhập, có thể đã cũ.
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

        // isAdmin(): đã đăng nhập và role trong session bằng 'admin'.
    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
    }

        // csrfToken(): sinh token ngẫu nhiên 32 byte nếu chưa có, có rồi thì dùng lại.
    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

        // verifyCsrf(): so token bằng hash_equals (thời gian không đổi) để chống đoán token qua thời gian.
    public static function verifyCsrf(?string $token): bool
    {
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}
