<?php
/**
 * Model Amenity: Quản lý bảng tiện ích riêng (amenities) và bảng nối (place_amenities).
 * Một quán có nhiều tiện ích, một tiện ích thuộc nhiều quán (many-to-many).
 * Thêm tiện ích mới không cần sửa code: chỉ thêm dòng trong bảng amenities.
 */
class Amenity extends Model
{
    /**
     * Lấy tất cả tiện ích, sắp theo thứ tự hiển thị rồi id
     */
    public function getAll(bool $onlyActive = true): array
    {
        $where = $onlyActive ? 'WHERE is_active = 1' : '';
        return $this->fetchAll("SELECT * FROM amenities {$where} ORDER BY sort_order ASC, id ASC");
    }

    /**
     * Lấy tất cả tiện ích kèm số quán đang dùng (cho trang Quản trị)
     */
    public function getAllWithPlaceCount(): array
    {
        $sql = "SELECT a.id, a.name, a.slug, a.sort_order, a.is_active,
                       COUNT(pa.place_id) AS place_count
                FROM amenities a
                LEFT JOIN place_amenities pa ON pa.amenity_id = a.id
                GROUP BY a.id, a.name, a.slug, a.sort_order, a.is_active
                ORDER BY a.sort_order ASC, a.id ASC";
        return $this->fetchAll($sql);
    }

    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM amenities WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    public function findBySlug(string $slug): array|false
    {
        return $this->fetchOne('SELECT * FROM amenities WHERE slug = :slug LIMIT 1', [':slug' => trim($slug)]);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM amenities WHERE LOWER(name) = LOWER(:name) AND id != :id';
            return (int) $this->fetchColumn($sql, [':name' => trim($name), ':id' => $excludeId]) > 0;
        }
        $sql = 'SELECT COUNT(*) FROM amenities WHERE LOWER(name) = LOWER(:name)';
        return (int) $this->fetchColumn($sql, [':name' => trim($name)]) > 0;
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM amenities WHERE slug = :slug AND id != :id';
            return (int) $this->fetchColumn($sql, [':slug' => trim($slug), ':id' => $excludeId]) > 0;
        }
        $sql = 'SELECT COUNT(*) FROM amenities WHERE slug = :slug';
        return (int) $this->fetchColumn($sql, [':slug' => trim($slug)]) > 0;
    }

    /**
     * Thêm tiện ích mới, tự sinh slug duy nhất từ tên
     */
    public function create(string $name): int
    {
        $name = trim($name);
        $slug = create_slug($name);
        if ($slug === '') {
            $slug = 'tien-ich';
        }
        $baseSlug = $slug;
        $counter = 2;
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $maxOrder = (int) $this->fetchColumn('SELECT COALESCE(MAX(sort_order), 0) FROM amenities');
        $sql = 'INSERT INTO amenities (name, slug, sort_order) VALUES (:name, :slug, :sort_order)';
        $this->execute($sql, [':name' => $name, ':slug' => $slug, ':sort_order' => $maxOrder + 1]);
        return (int) $this->lastInsertId();
    }

    /**
     * Đổi tên tiện ích, slug sinh lại từ tên mới (giữ duy nhất)
     */
    public function update(int $id, string $name): bool
    {
        $name = trim($name);
        $slug = create_slug($name);
        if ($slug === '') {
            $slug = 'tien-ich';
        }
        $baseSlug = $slug;
        $counter = 2;
        while ($this->slugExists($slug, $id)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $sql = 'UPDATE amenities SET name = :name, slug = :slug WHERE id = :id';
        return $this->execute($sql, [':name' => $name, ':slug' => $slug, ':id' => $id]) >= 0;
    }

    public function countPlaces(int $amenityId): int
    {
        $sql = 'SELECT COUNT(*) FROM place_amenities WHERE amenity_id = :id';
        return (int) $this->fetchColumn($sql, [':id' => $amenityId]);
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM amenities WHERE id = :id', [':id' => $id]) > 0;
    }

    /**
     * Tiện ích của 1 quán, sắp theo thứ tự hiển thị
     */
    public function getForPlace(int $placeId): array
    {
        $sql = "SELECT a.* FROM amenities a
                INNER JOIN place_amenities pa ON pa.amenity_id = a.id
                WHERE pa.place_id = :id AND a.is_active = 1
                ORDER BY a.sort_order ASC, a.id ASC";
        return $this->fetchAll($sql, [':id' => $placeId]);
    }

    /**
     * Tiện ích của nhiều quán trong 1 truy vấn, trả về map place_id => danh sách tiện ích
     */
    public function getForPlaces(array $placeIds): array
    {
        $placeIds = array_values(array_unique(array_map('intval', $placeIds)));
        $placeIds = array_filter($placeIds, fn($id) => $id > 0);
        if (empty($placeIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($placeIds), '?'));
        $sql = "SELECT pa.place_id, a.* FROM amenities a
                INNER JOIN place_amenities pa ON pa.amenity_id = a.id
                WHERE pa.place_id IN ({$placeholders}) AND a.is_active = 1
                ORDER BY a.sort_order ASC, a.id ASC";
        $rows = $this->fetchAll($sql, $placeIds);
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['place_id']][] = $row;
        }
        return $map;
    }

    public function getIdsForPlace(int $placeId): array
    {
        $rows = $this->getForPlace($placeId);
        return array_map(fn($row) => (int) $row['id'], $rows);
    }

    /**
     * Đồng bộ tiện ích của 1 quán: xóa hết rồi chèn lại chỉ các id hợp lệ đang có trong bảng amenities
     */
    public function syncForPlace(int $placeId, array $amenityIds): void
    {
        $this->execute('DELETE FROM place_amenities WHERE place_id = :id', [':id' => $placeId]);
        $ids = array_values(array_unique(array_map('intval', $amenityIds)));
        $ids = array_filter($ids, fn($id) => $id > 0);
        if (empty($ids)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $valid = $this->fetchAll("SELECT id FROM amenities WHERE id IN ({$placeholders})", $ids);
        foreach ($valid as $row) {
            $this->execute(
                'INSERT INTO place_amenities (place_id, amenity_id) VALUES (:place_id, :amenity_id)',
                [':place_id' => $placeId, ':amenity_id' => (int) $row['id']]
            );
        }
    }
}
