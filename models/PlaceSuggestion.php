<?php
// Đề xuất quán mới của thành viên: gửi lên chờ admin duyệt, duyệt xong sẽ thành một quán thật.
class PlaceSuggestion extends Model
{
    // Tạo đề xuất mới ở trạng thái chờ duyệt, trả id vừa tạo.
    public function create(int $userId, array $data): int
    {
        $this->execute(
            "INSERT INTO place_suggestions (user_id, name, address, district, category_id, description, status, created_at)
             VALUES (:user_id, :name, :address, :district, :category_id, :description, 'pending', NOW())",
            [
                ':user_id'     => $userId,
                ':name'        => $data['name'],
                ':address'     => $data['address'],
                ':district'    => $data['district'],
                ':category_id' => $data['category_id'],
                ':description' => $data['description'],
            ]
        );

        return (int) $this->lastInsertId();
    }

    // Các đề xuất của một thành viên, mới nhất trước, kèm tên danh mục và quán đã tạo (nếu đã duyệt).
    public function getByUser(int $userId): array
    {
        $sql = "SELECT s.*, c.name AS category_name
                FROM place_suggestions s
                LEFT JOIN categories c ON c.id = s.category_id
                WHERE s.user_id = :user_id
                ORDER BY s.created_at DESC, s.id DESC";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    // Danh sách cho admin: đang chờ duyệt xếp trước, kèm tên danh mục và người đề xuất.
    public function getAdminList(): array
    {
        $sql = "SELECT s.*, c.name AS category_name, u.name AS user_name
                FROM place_suggestions s
                LEFT JOIN categories c ON c.id = s.category_id
                INNER JOIN users u ON u.id = s.user_id
                ORDER BY (s.status = 'pending') DESC, s.created_at DESC, s.id DESC";

        return $this->fetchAll($sql);
    }

    // Đếm đề xuất đang chờ duyệt, dùng cho huy hiệu trên menu admin.
    public function countPending(): int
    {
        return (int) $this->fetchColumn("SELECT COUNT(*) FROM place_suggestions WHERE status = 'pending'");
    }

    // Tìm một đề xuất theo id, không có thì trả null.
    public function findById(int $id): ?array
    {
        $row = $this->fetchOne('SELECT * FROM place_suggestions WHERE id = :id', [':id' => $id]);
        return $row ?: null;
    }

    // Chốt trạng thái sau khi admin xử lý; khi duyệt thì ghi kèm id quán vừa tạo.
    public function markReviewed(int $id, string $status, ?int $createdPlaceId = null): bool
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }

        return $this->execute(
            'UPDATE place_suggestions SET status = :status, created_place_id = :place_id, reviewed_at = NOW() WHERE id = :id',
            [':status' => $status, ':place_id' => $createdPlaceId, ':id' => $id]
        );
    }
}
