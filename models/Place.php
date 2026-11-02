<?php
/**
 * Model Place: Quản lý toàn bộ truy vấn CSDL của bảng places
 * Bao gồm: CRUD Admin, Lọc đa tiêu chí phía người dùng, Tìm kiếm, Phân trang và Tính lại điểm trung bình
 */
class Place extends Model
{
    /**
     * Lấy danh sách quán nổi bật (điểm cao nhất) cho Trang chủ
     */
    public function getFeaturedPlaces(int $limit = 6): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'
                ORDER BY p.avg_rating DESC, p.total_reviews DESC, p.id DESC
                LIMIT {$limit}";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách quán mới cập nhật cho Trang chủ
     */
    public function getLatestPlaces(int $limit = 6): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT {$limit}";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách các Quận / Khu vực hiện có trong CSDL để hiển thị bộ lọc
     */
    public function getDistinctDistricts(): array
    {
        $sql = "SELECT DISTINCT district FROM places WHERE status = 'active' AND district != '' ORDER BY district ASC";
        $rows = $this->fetchAll($sql);
        return array_column($rows, 'district');
    }

    /**
     * Lọc đa tiêu chí, tìm kiếm, sắp xếp và phân trang danh sách quán phía Người dùng
     */
    public function getFilteredPlaces(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $conditions = ["p.status = 'active'"];
        $params = [];

        // 1. Tìm kiếm theo tên quán hoặc địa chỉ
        if (!empty($filters['q'])) {
            $kw = '%' . trim($filters['q']) . '%';
            $conditions[] = '(p.name LIKE :kw1 OR p.address LIKE :kw2 OR p.district LIKE :kw3)';
            $params[':kw1'] = $kw;
            $params[':kw2'] = $kw;
            $params[':kw3'] = $kw;
        }

        // 2. Lọc theo danh mục loại hình
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int) $filters['category_id'];
        }

        // 3. Lọc theo quận / khu vực
        if (!empty($filters['district'])) {
            $conditions[] = 'p.district = :district';
            $params[':district'] = trim($filters['district']);
        }

        // 4. Lọc theo mức giá (<= price_range để hỗ trợ tag "Dưới 50k" khi price_range=2)
        if (!empty($filters['price_range'])) {
            $price = (int) $filters['price_range'];
            if ($price === 2 && !empty($filters['under_50k'])) {
                $conditions[] = 'p.price_range <= 2';
            } else {
                $conditions[] = 'p.price_range <= :price_range';
                $params[':price_range'] = $price;
            }
        }

        // 5. Lọc tiện ích: Wifi, Ổ cắm, Máy lạnh
        if (!empty($filters['has_wifi'])) {
            $conditions[] = 'p.has_wifi = 1';
        }
        if (!empty($filters['has_outlet'])) {
            $conditions[] = 'p.has_outlet = 1';
        }
        if (!empty($filters['has_ac'])) {
            $conditions[] = 'p.has_ac = 1';
        }

        // 6. Lọc độ ồn
        if (!empty($filters['noise_level']) && in_array($filters['noise_level'], ['yen_tinh', 'vua', 'on'], true)) {
            $conditions[] = 'p.noise_level = :noise_level';
            $params[':noise_level'] = $filters['noise_level'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        // Sắp xếp
        $sort = $filters['sort'] ?? 'rating_desc';
        $orderBy = match ($sort) {
            'newest'     => 'p.created_at DESC, p.id DESC',
            'price_asc'  => 'p.price_range ASC, p.avg_rating DESC',
            'price_desc' => 'p.price_range DESC, p.avg_rating DESC',
            default      => 'p.avg_rating DESC, p.total_reviews DESC, p.id DESC',
        };

        // Đếm tổng số kết quả
        $countSql = "SELECT COUNT(*) FROM places p {$whereClause}";
        $total = (int) $this->fetchColumn($countSql, $params);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                {$whereClause}
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'items'       => $this->fetchAll($sql, $params),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    /**
     * Lấy danh sách quán cho trang Quản trị (Admin) kèm tìm kiếm, lọc và phân trang
     */
    public function getAdminList(string $keyword = '', int $categoryId = 0, string $status = '', int $page = 1, int $perPage = ADMIN_ITEMS_PER_PAGE): array
    {
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = '(p.name LIKE :kw1 OR p.address LIKE :kw2 OR p.district LIKE :kw3)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
        }

        if ($categoryId > 0) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if (in_array($status, ['active', 'hidden'], true)) {
            $conditions[] = 'p.status = :status';
            $params[':status'] = $status;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "SELECT COUNT(*) FROM places p {$whereClause}";
        $total = (int) $this->fetchColumn($countSql, $params);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image,
                       (SELECT COUNT(*) FROM place_images pi2 WHERE pi2.place_id = p.id) AS image_count
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                {$whereClause}
                ORDER BY p.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'items'       => $this->fetchAll($sql, $params),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    /**
     * Tìm thông tin chi tiết 1 quán theo ID (dùng cho cả User và Admin)
     */
    public function findById(int $id, bool $onlyActive = false): array|false
    {
        $statusCond = $onlyActive ? "AND p.status = 'active'" : '';
        $sql = "SELECT p.*, c.name AS category_name, u.name AS author_name
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN users u ON u.id = p.created_by
                WHERE p.id = :id {$statusCond}
                LIMIT 1";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Tạo slug duy nhất (tự động nối số nếu bị trùng slug)
     */
    public function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = create_slug($name);
        if ($baseSlug === '') {
            $baseSlug = 'study-spot';
        }

        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Kiểm tra slug đã tồn tại chưa
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM places WHERE slug = :slug AND id != :id';
            return (int) $this->fetchColumn($sql, [':slug' => $slug, ':id' => $excludeId]) > 0;
        }

        $sql = 'SELECT COUNT(*) FROM places WHERE slug = :slug';
        return (int) $this->fetchColumn($sql, [':slug' => $slug]) > 0;
    }

    /**
     * Thêm mới một quán học bài
     */
    public function create(array $data): int
    {
        $slug = $this->generateUniqueSlug($data['name']);

        $sql = 'INSERT INTO places (
                    category_id, name, slug, address, district, description,
                    price_range, has_wifi, has_outlet, has_ac, noise_level,
                    open_time, close_time, google_map_url, status, created_by, created_at
                ) VALUES (
                    :category_id, :name, :slug, :address, :district, :description,
                    :price_range, :has_wifi, :has_outlet, :has_ac, :noise_level,
                    :open_time, :close_time, :google_map_url, :status, :created_by, NOW()
                )';

        $this->execute($sql, [
            ':category_id'    => (int) $data['category_id'],
            ':name'           => trim($data['name']),
            ':slug'           => $slug,
            ':address'        => trim($data['address']),
            ':district'       => trim($data['district']),
            ':description'    => trim($data['description']),
            ':price_range'    => (int) $data['price_range'],
            ':has_wifi'       => !empty($data['has_wifi']) ? 1 : 0,
            ':has_outlet'     => !empty($data['has_outlet']) ? 1 : 0,
            ':has_ac'         => !empty($data['has_ac']) ? 1 : 0,
            ':noise_level'    => $data['noise_level'],
            ':open_time'      => $data['open_time'],
            ':close_time'     => $data['close_time'],
            ':google_map_url' => !empty($data['google_map_url']) ? trim($data['google_map_url']) : null,
            ':status'         => $data['status'] ?? 'active',
            ':created_by'     => $data['created_by'] ?? null,
        ]);

        return (int) $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin quán học bài
     */
    public function update(int $id, array $data): bool
    {
        $slug = $this->generateUniqueSlug($data['name'], $id);

        $sql = 'UPDATE places SET
                    category_id = :category_id,
                    name = :name,
                    slug = :slug,
                    address = :address,
                    district = :district,
                    description = :description,
                    price_range = :price_range,
                    has_wifi = :has_wifi,
                    has_outlet = :has_outlet,
                    has_ac = :has_ac,
                    noise_level = :noise_level,
                    open_time = :open_time,
                    close_time = :close_time,
                    google_map_url = :google_map_url,
                    status = :status
                WHERE id = :id';

        return $this->execute($sql, [
            ':category_id'    => (int) $data['category_id'],
            ':name'           => trim($data['name']),
            ':slug'           => $slug,
            ':address'        => trim($data['address']),
            ':district'       => trim($data['district']),
            ':description'    => trim($data['description']),
            ':price_range'    => (int) $data['price_range'],
            ':has_wifi'       => !empty($data['has_wifi']) ? 1 : 0,
            ':has_outlet'     => !empty($data['has_outlet']) ? 1 : 0,
            ':has_ac'         => !empty($data['has_ac']) ? 1 : 0,
            ':noise_level'    => $data['noise_level'],
            ':open_time'      => $data['open_time'],
            ':close_time'     => $data['close_time'],
            ':google_map_url' => !empty($data['google_map_url']) ? trim($data['google_map_url']) : null,
            ':status'         => $data['status'] ?? 'active',
            ':id'             => $id,
        ]) >= 0;
    }

    /**
     * Đổi nhanh trạng thái hiển thị ('active' <-> 'hidden')
     */
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['active', 'hidden'], true)) {
            return false;
        }
        return $this->execute('UPDATE places SET status = :status WHERE id = :id', [
            ':status' => $status,
            ':id'     => $id,
        ]) > 0;
    }

    /**
     * Xóa quán học bài theo ID
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM places WHERE id = :id', [':id' => $id]) > 0;
    }

    /**
     * Tự động tính toán và cập nhật lại avg_rating + total_reviews của quán
     * dựa trên các review có trạng thái status = 'approved' (xử lý trong Model, không dùng Trigger)
     */
    public function recalculateRating(int $placeId): void
    {
        $stats = $this->fetchOne(
            "SELECT COUNT(*) AS total_reviews,
                    COALESCE(ROUND(AVG(rating_overall), 2), 0.00) AS avg_rating
             FROM reviews
             WHERE place_id = :place_id AND status = 'approved'",
            [':place_id' => $placeId]
        );

        $totalReviews = (int) ($stats['total_reviews'] ?? 0);
        $avgRating = (float) ($stats['avg_rating'] ?? 0.00);

        $this->execute(
            'UPDATE places SET avg_rating = :avg_rating, total_reviews = :total_reviews WHERE id = :id',
            [
                ':avg_rating'    => $avgRating,
                ':total_reviews' => $totalReviews,
                ':id'            => $placeId,
            ]
        );
    }

    /**
     * Đếm tổng số quán trong hệ thống
     */
    public function countAll(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM places');
    }

    /**
     * Lấy Top 5 quán có điểm đánh giá cao nhất cho Admin Dashboard
     */
    public function getTopRatedPlaces(int $limit = 5): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT p.id, p.name, p.district, p.avg_rating, p.total_reviews, c.name AS category_name
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                ORDER BY p.avg_rating DESC, p.total_reviews DESC, p.id ASC
                LIMIT {$limit}";
        return $this->fetchAll($sql);
    }
}
