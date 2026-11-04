<?php
// Model Amenity: quản lý tiện ích của quán, bảng amenities + bảng nối place_amenities nhiều-nhiều.
// Model này được controller quản trị và model Place gọi khi tạo/sửa/hiển thị quán.
class Amenity extends Model
{
    // Lấy tất cả tiện ích từ amenities, chỉ lấy is_active = 1 nếu onlyActive.
    public function getAll(bool $onlyActive = true): array
    {
        $where = $onlyActive ? 'WHERE is_active = 1' : '';
        return $this->fetchAll("SELECT * FROM amenities {$where} ORDER BY sort_order ASC, id ASC");
    }

    // Lấy tiện ích kèm số quán đang dùng, LEFT JOIN place_amenities để tiện ích chưa ai dùng vẫn hiện (place_count = 0).
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

    // Tìm một tiện ích theo id, SELECT * FROM amenities WHERE id = :id LIMIT 1.
    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM amenities WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    // Tìm một tiện ích theo slug, SELECT * FROM amenities WHERE slug = :slug LIMIT 1.
    public function findBySlug(string $slug): array|false
    {
        return $this->fetchOne('SELECT * FROM amenities WHERE slug = :slug LIMIT 1', [':slug' => trim($slug)]);
    }

    // Kiểm tra tên đã tồn tại chưa, so sánh LOWER(name) không phân biệt hoa/thường, excludeId loại chính nó ra.
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM amenities WHERE LOWER(name) = LOWER(:name) AND id != :id';
            return (int) $this->fetchColumn($sql, [':name' => trim($name), ':id' => $excludeId]) > 0;
        }
        $sql = 'SELECT COUNT(*) FROM amenities WHERE LOWER(name) = LOWER(:name)';
        return (int) $this->fetchColumn($sql, [':name' => trim($name)]) > 0;
    }

    // Kiểm tra slug đã tồn tại chưa, slug duy nhất, excludeId loại chính nó ra.
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM amenities WHERE slug = :slug AND id != :id';
            return (int) $this->fetchColumn($sql, [':slug' => trim($slug), ':id' => $excludeId]) > 0;
        }
        $sql = 'SELECT COUNT(*) FROM amenities WHERE slug = :slug';
        return (int) $this->fetchColumn($sql, [':slug' => trim($slug)]) > 0;
    }

    // Thêm tiện ích mới, tự sinh slug duy nhất từ tên (trùng thì thêm -2, -3...), sort_order = MAX + 1.
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

    // Đổi tên tiện ích, sinh lại slug duy nhất từ tên mới, UPDATE amenities WHERE id = :id.
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

    // Đếm số quán đang dùng một tiện ích, SELECT COUNT(*) FROM place_amenities WHERE amenity_id = :id.
    public function countPlaces(int $amenityId): int
    {
        $sql = 'SELECT COUNT(*) FROM place_amenities WHERE amenity_id = :id';
        return (int) $this->fetchColumn($sql, [':id' => $amenityId]);
    }

    // Xóa một tiện ích, DELETE FROM amenities WHERE id = :id (không tự xóa dòng nối place_amenities).
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM amenities WHERE id = :id', [':id' => $id]) > 0;
    }

    // Lấy tiện ích của một quán, INNER JOIN place_amenities, chỉ lấy is_active = 1, xếp theo sort_order.
    public function getForPlace(int $placeId): array
    {
        $sql = "SELECT a.* FROM amenities a
                INNER JOIN place_amenities pa ON pa.amenity_id = a.id
                WHERE pa.place_id = :id AND a.is_active = 1
                ORDER BY a.sort_order ASC, a.id ASC";
        return $this->fetchAll($sql, [':id' => $placeId]);
    }

    // Lấy tiện ích của nhiều quán trong 1 truy vấn, trả về map place_id => danh sách tiện ích.
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

    // Lấy chỉ danh sách id tiện ích của một quán, để đánh dấu checkbox trên form sửa quán.
    public function getIdsForPlace(int $placeId): array
    {
        $rows = $this->getForPlace($placeId);
        return array_map(fn($row) => (int) $row['id'], $rows);
    }

    // Đồng bộ tiện ích của quán: xóa hết dòng nối cũ rồi chèn lại, chỉ chèn id có thật trong amenities.
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
