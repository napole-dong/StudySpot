<?php
/**
 * Model Favorite: Quản lý danh sách quán yêu thích của người dùng (bảng favorites)
 */
class Favorite extends Model
{
    /**
     * Kiểm tra người dùng đã lưu quán này vào Yêu thích chưa
     */
    public function isFavorited(int $userId, int $placeId): bool
    {
        $sql = 'SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND place_id = :place_id';
        return (int) $this->fetchColumn($sql, [
            ':user_id'  => $userId,
            ':place_id' => $placeId,
        ]) > 0;
    }

    /**
     * Lấy mảng các place_id mà người dùng đã yêu thích (để tô đỏ nút trái tim trên danh sách quán)
     */
    public function getFavoritePlaceIds(int $userId): array
    {
        $sql = 'SELECT place_id FROM favorites WHERE user_id = :user_id';
        $rows = $this->fetchAll($sql, [':user_id' => $userId]);
        return array_map('intval', array_column($rows, 'place_id'));
    }

    /**
     * Thêm hoặc bỏ yêu thích (Toggle) - Trả về trạng thái mới ('added' hoặc 'removed')
     */
    public function toggle(int $userId, int $placeId): string
    {
        if ($this->isFavorited($userId, $placeId)) {
            $this->execute('DELETE FROM favorites WHERE user_id = :user_id AND place_id = :place_id', [
                ':user_id'  => $userId,
                ':place_id' => $placeId,
            ]);
            return 'removed';
        }

        $this->execute('INSERT INTO favorites (user_id, place_id, created_at) VALUES (:user_id, :place_id, NOW())', [
            ':user_id'  => $userId,
            ':place_id' => $placeId,
        ]);
        return 'added';
    }

    /**
     * Lấy danh sách chi tiết các quán đã yêu thích của một người dùng
     */
    public function getFavoritePlacesByUser(int $userId): array
    {
        $sql = "SELECT p.*, c.name AS category_name, f.created_at AS favorited_at,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM favorites f
                INNER JOIN places p ON p.id = f.place_id
                INNER JOIN categories c ON c.id = p.category_id
                WHERE f.user_id = :user_id AND p.status = 'active'
                ORDER BY f.created_at DESC, f.id DESC";
        return $this->fetchAll($sql, [':user_id' => $userId]);
    }
}
