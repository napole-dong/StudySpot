<?php
// Model AuthAttempt: đếm số lần thử đăng nhập và xin link quên mật khẩu, bảng auth_attempts.
// Dùng để chặn dò mật khẩu và spam email: vượt quá số lần cho phép trong cửa sổ thì tạm dừng.
class AuthAttempt extends Model
{
    // Đếm số lần thử của một email trong cửa sổ phút gần nhất, SELECT COUNT(*) FROM auth_attempts theo action + subject.
    public function countBySubject(string $action, string $subject, int $minutes): int
    {
        $minutes = max(1, $minutes);
        $sql = "SELECT COUNT(*) FROM auth_attempts
                WHERE action = :action AND subject = :subject
                  AND created_at > (NOW() - INTERVAL $minutes MINUTE)";
        return (int) $this->fetchColumn($sql, [':action' => $action, ':subject' => $subject]);
    }

    // Đếm số lần thử từ một IP trong cửa sổ phút gần nhất, SELECT COUNT(*) FROM auth_attempts theo action + ip.
    public function countByIp(string $action, string $ip, int $minutes): int
    {
        $minutes = max(1, $minutes);
        $sql = "SELECT COUNT(*) FROM auth_attempts
                WHERE action = :action AND ip = :ip
                  AND created_at > (NOW() - INTERVAL $minutes MINUTE)";
        return (int) $this->fetchColumn($sql, [':action' => $action, ':ip' => $ip]);
    }

    // Ghi lại một lần thử, đồng thời dọn các dòng cũ hơn 1 ngày cho bảng luôn gọn.
    public function record(string $action, string $subject, string $ip): void
    {
        $this->execute('DELETE FROM auth_attempts WHERE created_at < (NOW() - INTERVAL 1 DAY)');

        $sql = 'INSERT INTO auth_attempts (action, subject, ip, created_at)
                VALUES (:action, :subject, :ip, NOW())';
        $this->execute($sql, [':action' => $action, ':subject' => $subject, ':ip' => $ip]);
    }

    // Xóa lịch sử thử của một email, gọi khi đăng nhập thành công để không bị khóa oan vì vài lần gõ sai trước đó.
    public function clearBySubject(string $action, string $subject): void
    {
        $sql = 'DELETE FROM auth_attempts WHERE action = :action AND subject = :subject';
        $this->execute($sql, [':action' => $action, ':subject' => $subject]);
    }
}
