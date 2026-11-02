<?php
/**
 * Model PlaceImage: Quản lý hình ảnh của quán (bảng place_images)
 * Hỗ trợ nhiều ảnh trên 1 quán và chọn 1 ảnh làm ảnh bìa (is_cover = 1)
 */
class PlaceImage extends Model
{
    /**
     * Lấy toàn bộ ảnh của một quán (ưu tiên ảnh bìa đứng đầu)
     */
    public function getByPlaceId(int $placeId): array
    {
        $sql = 'SELECT * FROM place_images
                WHERE place_id = :place_id
                ORDER BY is_cover DESC, id ASC';
        return $this->fetchAll($sql, [':place_id' => $placeId]);
    }

    /**
     * Tìm một ảnh theo ID
     */
    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM place_images WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    /**
     * Lấy đường dẫn ảnh bìa của một quán
     */
    public function getCoverPath(int $placeId): ?string
    {
        $sql = 'SELECT image_path FROM place_images
                WHERE place_id = :place_id
                ORDER BY is_cover DESC, id ASC
                LIMIT 1';
        $path = $this->fetchColumn($sql, [':place_id' => $placeId]);
        return $path ? (string) $path : null;
    }

    /**
     * Thêm một ảnh mới cho quán
     */
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

    /**
     * Đặt một ảnh làm ảnh bìa cho quán (đồng thời bỏ cờ is_cover của các ảnh khác cùng quán)
     */
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

    /**
     * Đảm bảo quán luôn có ít nhất 1 ảnh được đánh dấu is_cover = 1 (nếu quán có ảnh)
     */
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

    /**
     * Xóa một ảnh (trên cả CSDL và file vật lý)
     */
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

    /**
     * Xóa toàn bộ file ảnh vật lý của một quán trước khi xóa quán
     */
    public function deleteAllFilesByPlaceId(int $placeId): void
    {
        $images = $this->getByPlaceId($placeId);
        foreach ($images as $img) {
            delete_uploaded_file($img['image_path']);
        }
    }
}
