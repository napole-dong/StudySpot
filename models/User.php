<?php
// Model User: quản lý tài khoản, bảng users, role user/admin, status active (được đăng nhập) / locked (bị khóa).
// Model này được controller đăng ký/đăng nhập, hồ sơ cá nhân và quản trị gọi.
class User extends Model
{
    // Tìm người dùng theo email, SELECT * FROM users WHERE email = :email LIMIT 1.
    public function findByEmail(string $email): array|false
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        return $this->fetchOne($sql, [':email' => trim($email)]);
    }

    // Tìm người dùng theo id, SELECT * FROM users WHERE id = :id LIMIT 1.
    public function findById(int $id): array|false
    {
        $sql = 'SELECT * FROM users WHERE id = :id LIMIT 1';
        return $this->fetchOne($sql, [':id' => $id]);
    }

    // Kiểm tra email đã tồn tại chưa, excludeId loại chính tài khoản đang sửa ra.
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

    // Tạo tài khoản mới, mật khẩu chỉ lưu dạng băm password_hash, không bao giờ lưu mật khẩu gốc.
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

    // Sửa hồ sơ cá nhân, chỉ đổi name/phone/avatar, không đổi email, mật khẩu, role hay status qua hàm này.
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

    // Đổi mật khẩu, lưu password_hash của mật khẩu mới, UPDATE users SET password WHERE id = :id.
    public function updatePassword(int $userId, string $newPlainPassword): bool
    {
        $sql = 'UPDATE users SET password = :password WHERE id = :id';
        return $this->execute($sql, [
            ':password' => password_hash($newPlainPassword, PASSWORD_DEFAULT),
            ':id'       => $userId,
        ]) > 0;
    }

    // Lấy danh sách người dùng cho admin, tìm theo tên/email/phone, lọc status active/locked, phân trang; User::getAdminList đếm review không lọc trạng thái.
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

    // Đổi trạng thái tài khoản active/locked, giá trị lạ trả false ngay, UPDATE users SET status WHERE id = :id.
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

    // Đếm tổng số người dùng, SELECT COUNT(*) FROM users (cả tài khoản bị khóa).
    public function countAll(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM users');
    }
}
