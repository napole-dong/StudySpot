<?php
// Model Notification: thông báo trong web cho thành viên, bảng notifications.
// Hiện tại dùng cho kết quả kiểm duyệt đánh giá: đã duyệt hoặc bị ẩn.
class Notification extends Model
{
    // Tạo thông báo mới cho một tài khoản, INSERT INTO notifications, mặc định chưa đọc.
    public function create(int $userId, string $type, string $content, string $link): int
    {
        $sql = 'INSERT INTO notifications (user_id, type, content, link, is_read, created_at)
                VALUES (:user_id, :type, :content, :link, 0, NOW())';

        $this->execute($sql, [
            ':user_id' => $userId,
            ':type'    => $type,
            ':content' => $content,
            ':link'    => $link,
        ]);

        return (int) $this->lastInsertId();
    }

    // Gửi cùng một thông báo cho mọi tài khoản admin đang hoạt động (dùng khi có việc mới chờ xử lý).
    public function notifyAdmins(string $type, string $content, string $link): void
    {
        $admins = $this->fetchAll("SELECT id FROM users WHERE role = 'admin' AND status = 'active'");
        foreach ($admins as $admin) {
            $this->create((int) $admin['id'], $type, $content, $link);
        }
    }

    // Lấy các thông báo mới nhất của tài khoản, mới nhất xếp trước, SELECT * FROM notifications theo user_id.
    public function getRecentByUser(int $userId, int $limit = 10): array
    {
        $limit = max(1, min(20, $limit));
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY id DESC LIMIT $limit";
        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    // Đếm số thông báo chưa đọc của tài khoản, để hiện huy hiệu đỏ trên chuông.
    public function countUnread(int $userId): int
    {
        $sql = 'SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0';
        return (int) $this->fetchColumn($sql, [':user_id' => $userId]);
    }

    // Tìm một thông báo đúng chủ sở hữu, tránh mở thông báo của người khác.
    public function findByIdForUser(int $id, int $userId): array|false
    {
        $sql = 'SELECT * FROM notifications WHERE id = :id AND user_id = :user_id LIMIT 1';
        return $this->fetchOne($sql, [':id' => $id, ':user_id' => $userId]);
    }

    // Đánh dấu một thông báo đã đọc, UPDATE notifications SET is_read = 1.
    public function markRead(int $id, int $userId): void
    {
        $sql = 'UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id';
        $this->execute($sql, [':id' => $id, ':user_id' => $userId]);
    }

    // Đánh dấu tất cả thông báo của tài khoản đã đọc.
    public function markAllRead(int $userId): void
    {
        $sql = 'UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0';
        $this->execute($sql, [':user_id' => $userId]);
    }
}
