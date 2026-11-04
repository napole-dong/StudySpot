<?php
// Model Category: quản lý danh mục loại hình quán, bảng categories, một danh mục có nhiều quán.
// Model này được controller quản trị và controller phía người dùng gọi để vẽ ô lọc và tên danh mục.
class Category extends Model
{
    // Lấy danh mục kèm số quán, LEFT JOIN places, place_count đếm cả quán ẩn, active_place_count chỉ đếm status = 'active'.
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

    // Lấy tất cả danh mục từ categories, ORDER BY id ASC, dùng cho dropdown.
    public function getAll(): array
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY id ASC');
    }

    // Tìm một danh mục theo id, SELECT * FROM categories WHERE id = :id LIMIT 1.
    public function findById(int $id): array|false
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = :id LIMIT 1', [':id' => $id]);
    }

    // Kiểm tra tên danh mục đã tồn tại chưa, so sánh LOWER(name), excludeId loại chính nó ra.
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

    // Thêm danh mục mới, INSERT INTO categories (name).
    public function create(string $name): int
    {
        $sql = 'INSERT INTO categories (name) VALUES (:name)';
        $this->execute($sql, [':name' => trim($name)]);
        return (int) $this->lastInsertId();
    }

    // Đổi tên danh mục, UPDATE categories SET name WHERE id = :id.
    public function update(int $id, string $name): bool
    {
        $sql = 'UPDATE categories SET name = :name WHERE id = :id';
        return $this->execute($sql, [
            ':name' => trim($name),
            ':id'   => $id,
        ]) >= 0;
    }

    // Đếm số quán thuộc danh mục, SELECT COUNT(*) FROM places WHERE category_id = :category_id, kiểm tra trước khi xóa.
    public function countPlaces(int $categoryId): int
    {
        $sql = 'SELECT COUNT(*) FROM places WHERE category_id = :category_id';
        return (int) $this->fetchColumn($sql, [':category_id' => $categoryId]);
    }

    // Xóa danh mục, DELETE FROM categories WHERE id = :id, không tự xóa hay chuyển quán thuộc nó.
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM categories WHERE id = :id', [':id' => $id]) > 0;
    }
}
