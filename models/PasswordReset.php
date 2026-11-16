<?php
// Model PasswordReset: quản lý link đặt lại mật khẩu, bảng password_resets.
// Chỉ lưu mã token đã băm SHA-256, token gốc chỉ có trong email của người dùng.
class PasswordReset extends Model
{
    // Tạo token mới cho tài khoản, xóa token cũ của tài khoản đó trước (mỗi lúc chỉ 1 link còn hiệu lực).
    public function create(int $userId, string $tokenHash, string $expiresAt): int
    {
        $this->deleteByUser($userId);

        $sql = 'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at)
                VALUES (:user_id, :token_hash, :expires_at, NOW())';

        $this->execute($sql, [
            ':user_id'    => $userId,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
        ]);

        return (int) $this->lastInsertId();
    }

    // Tìm token theo mã băm, SELECT * FROM password_resets WHERE token_hash = :token_hash LIMIT 1.
    public function findByTokenHash(string $tokenHash): array|false
    {
        $sql = 'SELECT * FROM password_resets WHERE token_hash = :token_hash LIMIT 1';
        return $this->fetchOne($sql, [':token_hash' => $tokenHash]);
    }

    // Xóa mọi token của một tài khoản, DELETE FROM password_resets WHERE user_id = :user_id.
    public function deleteByUser(int $userId): int
    {
        $sql = 'DELETE FROM password_resets WHERE user_id = :user_id';
        return $this->execute($sql, [':user_id' => $userId]);
    }

    // Xóa token đã hết hạn, dọn dẹp định kỳ mỗi khi có người xin link mới.
    public function deleteExpired(): void
    {
        $this->execute('DELETE FROM password_resets WHERE expires_at < NOW()');
    }
}
