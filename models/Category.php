<?php
/**
 * Model Category: Quản lý truy vấn bảng categories (Danh mục loại hình quán)
 */
class Category extends Model
{
    /**
     * Lấy toàn bộ danh mục kèm số lượng quán thực tế thuộc từng danh mục (dùng LEFT JOIN + COUNT)
     */
    public function getAllWithPlaceCount(): array
    {
        $sql = "SELECT c.id, c.name,
                       COUNT(p.id) AS place_count,
                       SUM(CASE WHEN p.status = 'active' THEN 1 ELSE 0 END) AS active_place_count
                FROM categories c
                LEFT JOIN places p ON p.category_id = c.id
                GROUP BY c.id, c.name
                ORDER BY c.id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách tất cả danh mục đơn giản (dùng cho thẻ select/dropdown)
     */
    public function getAll(): array
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY id ASC');
    }

    /**
     * Tìm danh mục theo ID
     */
    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    /**
     * Kiểm tra tên danh mục đã tồn tại chưa
     */
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(:name) AND id != :id';
            return (int) $this->fetchColumn($sql, [
                ':name' => trim($name),
                ':id'   => $excludeId,
            ]) > 0;
        }

        $sql = 'SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(:name)';
        return (int) $this->fetchColumn($sql, [':name' => trim($name)]);
    }

    /**
     * Thêm danh mục mới
     */
    public function create(string $name): int
    {
        $sql = 'INSERT INTO categories (name) VALUES (:name)';
        $this->execute($sql, [':name' => trim($name)]);
        return (int) $this->lastInsertId();
    }

    /**
     * Cập nhật tên danh mục
     */
    public function update(int $id, string $name): bool
    {
        $sql = 'UPDATE categories SET name = :name WHERE id = :id';
        return $this->execute($sql, [
            ':name' => trim($name),
            ':id'   => $id,
        ]) >= 0;
    }

    /**
     * Đếm số quán đang thuộc danh mục này (để kiểm tra trước khi xóa)
     */
    public function countPlaces(int $categoryId): int
    {
        $sql = 'SELECT COUNT(*) FROM places WHERE category_id = :category_id';
        return (int) $this->fetchColumn($sql, [':category_id' => $categoryId]);
    }

    /**
     * Xóa danh mục theo ID
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM categories WHERE id = :id', [':id' => $id]) > 0;
    }
}
