<?php
// Model PlaceImage: quản lý ảnh của quán, bảng place_images, mỗi quán tối đa một ảnh bìa is_cover = 1.
// Model này được controller quản trị và trang chi tiết quán gọi.
class PlaceImage extends Model
{
    // Lấy ảnh của quán, ORDER BY is_cover DESC, id ASC (ảnh bìa đứng đầu).
    public function getByPlaceId(int $placeId): array
    {
        $sql = 'SELECT * FROM place_images
                WHERE place_id = :place_id
                ORDER BY is_cover DESC, id ASC';
        return $this->fetchAll($sql, [':place_id' => $placeId]);
    }

    // Tìm một ảnh theo id, SELECT * FROM place_images WHERE id = :id LIMIT 1.
    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM place_images WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    // Lấy đường dẫn ảnh bìa của quán, ảnh bìa trước rồi ảnh đầu tiên, LIMIT 1, chưa có ảnh thì null.
    public function getCoverPath(int $placeId): ?string
    {
        $sql = 'SELECT image_path FROM place_images
                WHERE place_id = :place_id
                ORDER BY is_cover DESC, id ASC
                LIMIT 1';
        $path = $this->fetchColumn($sql, [':place_id' => $placeId]);
        return $path ? (string) $path : null;
    }

    // Thêm ảnh mới, nếu là ảnh bìa thì hạ cờ is_cover của ảnh cũ cùng quán xuống 0 trước.
    public function create(int $placeId, string $imagePath, int $isCover = 0): int
    {
        if ($isCover === 1) {
            $this->execute('UPDATE place_images SET is_cover = 0 WHERE place_id = :place_id', [
                ':place_id' => $placeId,
            ]);
        }

        $sql = 'INSERT INTO place_images (place_id, image_path, is_cover)
                VALUES (:place_id, :image_path, :is_cover)';
        $this->execute($sql, [
            ':place_id'   => $placeId,
            ':image_path' => $imagePath,
            ':is_cover'   => $isCover ? 1 : 0,
        ]);

        return (int) $this->lastInsertId();
    }

    // Đặt ảnh làm ảnh bìa, kiểm tra ảnh thuộc đúng quán, hạ cờ ảnh cũ rồi đặt is_cover = 1 cho ảnh này.
    public function setCover(int $placeId, int $imageId): bool
    {
        $image = $this->findById($imageId);
        if (!$image || (int) $image['place_id'] !== $placeId) {
            return false;
        }

        $this->execute('UPDATE place_images SET is_cover = 0 WHERE place_id = :place_id', [
            ':place_id' => $placeId,
        ]);

        return $this->execute('UPDATE place_images SET is_cover = 1 WHERE id = :id AND place_id = :place_id', [
            ':id'       => $imageId,
            ':place_id' => $placeId,
        ]) > 0;
    }

    // Đảm bảo quán có ảnh bìa: nếu chưa có thì đôn ảnh cũ nhất lên làm bìa.
    public function ensureCoverExists(int $placeId): void
    {
        $hasCover = (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM place_images WHERE place_id = :place_id AND is_cover = 1',
            [':place_id' => $placeId]
        );

        if ($hasCover === 0) {
            $firstImage = $this->fetchOne(
                'SELECT id FROM place_images WHERE place_id = :place_id ORDER BY id ASC LIMIT 1',
                [':place_id' => $placeId]
            );
            if ($firstImage) {
                $this->execute('UPDATE place_images SET is_cover = 1 WHERE id = :id', [
                    ':id' => (int) $firstImage['id'],
                ]);
            }
        }
    }

    // Xóa ảnh cả CSDL lẫn file vật lý, nếu xóa ảnh bìa thì đôn ảnh khác lên bìa.
    public function delete(int $imageId): bool
    {
        $image = $this->findById($imageId);
        if (!$image) {
            return false;
        }

        delete_uploaded_file($image['image_path']);
        $deleted = $this->execute('DELETE FROM place_images WHERE id = :id', [':id' => $imageId]) > 0;

        if ($deleted && (int) $image['is_cover'] === 1) {
            $this->ensureCoverExists((int) $image['place_id']);
        }

        return $deleted;
    }

    // Xóa toàn bộ file ảnh vật lý của quán, chỉ xóa file (không xóa dòng CSDL, không xóa quán).
    public function deleteAllFilesByPlaceId(int $placeId): void
    {
        $images = $this->getByPlaceId($placeId);
        foreach ($images as $img) {
            delete_uploaded_file($img['image_path']);
        }
    }
}
