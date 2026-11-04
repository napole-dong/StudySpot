<?php
// Model Favorite: quản lý quán yêu thích (nút trái tim), bảng favorites (user_id, place_id, created_at).
// Model này được controller nút trái tim (AJAX) và trang Quán yêu thích đã lưu gọi.
class Favorite extends Model
{
    // Kiểm tra người dùng đã lưu quán chưa, SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND place_id = :place_id.
    public function isFavorited(int $userId, int $placeId): bool
    {
        $sql = 'SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND place_id = :place_id';
        return (int) $this->fetchColumn($sql, [
            ':user_id'  => $userId,
            ':place_id' => $placeId,
        ]) > 0;
    }

    // Lấy các place_id người dùng đã lưu, để tô đỏ nút trái tim trên danh sách quán.
    public function getFavoritePlaceIds(int $userId): array
    {
        $sql = 'SELECT place_id FROM favorites WHERE user_id = :user_id';
        $rows = $this->fetchAll($sql, [':user_id' => $userId]);
        return array_map('intval', array_column($rows, 'place_id'));
    }

    // Bấm nút trái tim: đã lưu thì DELETE trả 'removed', chưa lưu thì INSERT trả 'added'.
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

    // Lấy quán đã lưu của người dùng, JOIN places + categories + ảnh bìa, chỉ lấy p.status = 'active', mới lưu trước.
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
