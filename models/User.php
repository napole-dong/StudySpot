<?php
/**
 * Model User: Quản lý toàn bộ truy vấn CSDL liên quan đến bảng users
 */
class User extends Model
{
    /**
     * Tìm thông tin người dùng theo địa chỉ Email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        return $this->fetchOne($sql, [':email' => trim($email)]);
    }

    /**
     * Tìm thông tin người dùng theo ID
     */
    public function findById(int $id): array|false
    {
        $sql = 'SELECT * FROM users WHERE id = :id LIMIT 1';
        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Kiểm tra Email đã tồn tại trong hệ thống chưa (hỗ trợ loại trừ ID hiện tại khi sửa)
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM users WHERE email = :email AND id != :id';
            return (int) $this->fetchColumn($sql, [
                ':email' => trim($email),
                ':id'    => $excludeId,
            ]) > 0;
        }

        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        return (int) $this->fetchColumn($sql, [':email' => trim($email)]) > 0;
    }

    /**
     * Tạo mới tài khoản thành viên (Đăng ký)
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password, phone, avatar, role, status, created_at)
                VALUES (:name, :email, :password, :phone, :avatar, :role, :status, NOW())';

        $this->execute($sql, [
            ':name'     => trim($data['name']),
            ':email'    => trim($data['email']),
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':phone'    => !empty($data['phone']) ? trim($data['phone']) : null,
            ':avatar'   => $data['avatar'] ?? null,
            ':role'     => $data['role'] ?? 'user',
            ':status'   => $data['status'] ?? 'active',
        ]);

        return (int) $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân (Họ tên, Số điện thoại, Ảnh đại diện)
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $sql = 'UPDATE users
                SET name = :name, phone = :phone, avatar = :avatar
                WHERE id = :id';

        return $this->execute($sql, [
            ':name'   => trim($data['name']),
            ':phone'  => !empty($data['phone']) ? trim($data['phone']) : null,
            ':avatar' => $data['avatar'] ?? null,
            ':id'     => $userId,
        ]) >= 0;
    }

    /**
     * Đổi mật khẩu của người dùng
     */
    public function updatePassword(int $userId, string $newPlainPassword): bool
    {
        $sql = 'UPDATE users SET password = :password WHERE id = :id';
        return $this->execute($sql, [
            ':password' => password_hash($newPlainPassword, PASSWORD_DEFAULT),
            ':id'       => $userId,
        ]) > 0;
    }

    /**
     * Lấy danh sách người dùng cho trang Quản trị (có tìm kiếm, lọc trạng thái và phân trang)
     */
    public function getAdminList(string $keyword = '', string $status = '', int $page = 1, int $perPage = ADMIN_ITEMS_PER_PAGE): array
    {
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = '(u.name LIKE :kw1 OR u.email LIKE :kw2 OR u.phone LIKE :kw3)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
        }

        if (in_array($status, ['active', 'locked'], true)) {
            $conditions[] = 'u.status = :status';
            $params[':status'] = $status;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Đếm tổng số bản ghi
        $countSql = "SELECT COUNT(*) FROM users u {$whereClause}";
        $total = (int) $this->fetchColumn($countSql, $params);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT u.*,
                       (SELECT COUNT(*) FROM reviews r WHERE r.user_id = u.id) AS review_count
                FROM users u
                {$whereClause}
                ORDER BY u.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'items'       => $this->fetchAll($sql, $params),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    /**
     * Cập nhật trạng thái tài khoản ('active' hoặc 'locked')
     */
    public function updateStatus(int $userId, string $status): bool
    {
        if (!in_array($status, ['active', 'locked'], true)) {
            return false;
        }

        $sql = 'UPDATE users SET status = :status WHERE id = :id';
        return $this->execute($sql, [
            ':status' => $status,
            ':id'     => $userId,
        ]) > 0;
    }

    /**
     * Đếm tổng số người dùng trong hệ thống
     */
    public function countAll(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM users');
    }
}
