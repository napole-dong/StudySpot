<?php
// Model Place: quản lý bảng places (quán học bài / cà phê), avg_rating và total_reviews chỉ tính review đã duyệt.
// Model này được controller người dùng (trang chủ, Khám phá, chi tiết) và controller quản trị gọi.
class Place extends Model
{
    // Gán tiện ích vào từng quán qua Amenity::getForPlaces, thêm amenities, amenity_ids, amenity_slugs.
    private function attachAmenities(array $places): array
    {
        if (empty($places)) {
            return $places;
        }
        $ids = array_values(array_unique(array_map(fn($pl) => (int) ($pl['id'] ?? 0), $places)));
        $map = (new Amenity())->getForPlaces($ids);
        foreach ($places as &$pl) {
            $pid = (int) ($pl['id'] ?? 0);
            $pl['amenities'] = $map[$pid] ?? [];
            $pl['amenity_ids'] = array_map(fn($a) => (int) $a['id'], $pl['amenities']);
            $pl['amenity_slugs'] = array_map(fn($a) => $a['slug'], $pl['amenities']);
        }
        unset($pl);
        return $places;
    }

    // Suy mức giá 1-4 từ price_min/price_max, ngưỡng 25000/45000/65000; giá trần dùng <=, giá sàn dùng < nên không đối xứng.
    private function derivePriceRange($min, $max): int
    {
        $min = ($min === '' || $min === null) ? null : max(0, (int) $min);
        $max = ($max === '' || $max === null) ? null : max(0, (int) $max);
        if ($max !== null) {
            return $max <= 25000 ? 1 : ($max <= 45000 ? 2 : ($max <= 65000 ? 3 : 4));
        }
        if ($min !== null) {
            return $min < 25000 ? 1 : ($min < 45000 ? 2 : ($min < 65000 ? 3 : 4));
        }
        return 0;
    }

    // Lấy quán nổi bật điểm cao nhất cho trang chủ, WHERE p.status = 'active', xếp avg_rating, total_reviews, id DESC.
    public function getFeaturedPlaces(int $limit = 6, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);
        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'
                ORDER BY p.avg_rating DESC, p.total_reviews DESC, p.id DESC
                LIMIT {$limit} OFFSET {$offset}";
        return $this->attachAmenities($this->fetchAll($sql));
    }

    // Lấy quán hero: status = 'active', avg_rating >= 4.5, total_reviews >= 3 (chỉ đếm review đã duyệt), có ảnh, LIMIT 1.
    public function getHeroPlace(): ?array
    {
        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image,
                       (SELECT COUNT(DISTINCT f.user_id) FROM favorites f WHERE f.place_id = p.id) AS favorite_count
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'
                  AND p.avg_rating >= 4.5
                  AND p.total_reviews >= 3
                  AND EXISTS (SELECT 1 FROM place_images pi WHERE pi.place_id = p.id)
                ORDER BY p.avg_rating DESC, favorite_count DESC, p.total_reviews DESC, p.id DESC
                LIMIT 1";
        $row = $this->fetchOne($sql);
        if (!$row) {
            return null;
        }
        $rows = $this->attachAmenities([$row]);
        return $rows[0] ?? null;
    }

    // Lấy quán mới cập nhật cho trang chủ, WHERE p.status = 'active', xếp created_at DESC, id DESC.
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
        return $this->attachAmenities($this->fetchAll($sql));
    }

    // Lấy quán tương tự: cùng danh mục hoặc cùng phường, loại quán đang xem, ưu tiên cùng phường trước rồi cùng danh mục.
    public function getRelatedPlaces(int $excludePlaceId, int $categoryId, string $district, int $limit = 4): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT p.*, c.name AS category_name,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS cover_image
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.status = 'active'
                  AND p.id != :exclude_id
                  AND (p.category_id = :cat_id OR p.district = :district)
                ORDER BY (p.district = :district_prio) DESC,
                         (p.category_id = :cat_prio) DESC,
                         p.avg_rating DESC,
                         p.total_reviews DESC
                LIMIT {$limit}";

        return $this->fetchAll($sql, [
            ':exclude_id'    => $excludePlaceId,
            ':cat_id'        => $categoryId,
            ':district'      => $district,
            ':district_prio' => $district,
            ':cat_prio'      => $categoryId,
        ]);
    }

    // Lấy các phường đang có quán hoạt động, SELECT DISTINCT district FROM places WHERE status = 'active' AND district != ''.
    public function getDistinctDistricts(): array
    {
        $sql = "SELECT DISTINCT district FROM places WHERE status = 'active' AND district != '' ORDER BY district ASC";
        $rows = $this->fetchAll($sql);
        return array_column($rows, 'district');
    }

    // Lọc đa tiêu chí trang Khám phá: luôn chỉ lấy p.status = 'active', tìm kiếm, lọc, sắp xếp, phân trang (chi tiết lọc xem 1-6 bên dưới).
    public function getFilteredPlaces(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $conditions = ["p.status = 'active'"];
        $params = [];

        // Tìm theo tên, địa chỉ hoặc phường bằng LIKE (OR).
        if (!empty($filters['q'])) {
            $kw = '%' . trim($filters['q']) . '%';
            $conditions[] = '(p.name LIKE :kw1 OR p.address LIKE :kw2 OR p.district LIKE :kw3)';
            $params[':kw1'] = $kw;
            $params[':kw2'] = $kw;
            $params[':kw3'] = $kw;
        }

        // Lọc category_id đúng bằng danh mục đã chọn.
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int) $filters['category_id'];
        }

        // Lọc district đúng bằng phường đã chọn.
        if (!empty($filters['district'])) {
            $conditions[] = 'p.district = :district';
            $params[':district'] = trim($filters['district']);
        }

        // Lọc giá: price_range từ 1 tới mức đã chọn, 0 (chưa có giá) bị loại.
        if (!empty($filters['price_range'])) {
            $price = (int) $filters['price_range'];
            $conditions[] = '(p.price_range >= 1 AND p.price_range <= :price_range)';
            $params[':price_range'] = $price;
        }

        // Lọc tiện ích kiểu AND: quán phải đủ mọi slug đã chọn, EXISTS place_amenities + amenities is_active = 1.
        $amenitySlugs = $filters['amenity'] ?? [];
        if (!is_array($amenitySlugs)) {
            $amenitySlugs = [$amenitySlugs];
        }
        $amenitySlugs = array_values(array_unique(array_filter(array_map('trim', $amenitySlugs))));
        foreach ($amenitySlugs as $idx => $slug) {
            $key = ':amenity_' . $idx;
            $conditions[] = "EXISTS (SELECT 1 FROM place_amenities pa
                                INNER JOIN amenities a ON a.id = pa.amenity_id
                                WHERE pa.place_id = p.id AND a.slug = {$key} AND a.is_active = 1)";
            $params[$key] = $slug;
        }

        // Lọc độ ồn: chỉ yen_tinh, vua, on, giá trị lạ bỏ qua.
        if (!empty($filters['noise_level']) && in_array($filters['noise_level'], ['yen_tinh', 'vua', 'on'], true)) {
            $conditions[] = 'p.noise_level = :noise_level';
            $params[':noise_level'] = $filters['noise_level'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        // Sắp xếp theo sort: newest, price_asc, price_desc, mặc định rating_desc.
        $sort = $filters['sort'] ?? 'rating_desc';
        $orderBy = match ($sort) {
            'newest'     => 'p.created_at DESC, p.id DESC',
            'price_asc'  => 'p.price_range ASC, p.avg_rating DESC',
            'price_desc' => 'p.price_range DESC, p.avg_rating DESC',
            default      => 'p.avg_rating DESC, p.total_reviews DESC, p.id DESC',
        };

        // Đếm tổng số với cùng WHERE để tính số trang.
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
            'items'       => $this->attachAmenities($this->fetchAll($sql, $params)),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    // Lấy danh sách quán cho admin: không ép status = 'active', lọc status chỉ nhận 'active'/'hidden', kèm image_count.
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
            'items'       => $this->attachAmenities($this->fetchAll($sql, $params)),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    // Tìm chi tiết một quán theo id, JOIN categories + users (author_name), onlyActive thì thêm p.status = 'active'.
    public function findById(int $id, bool $onlyActive = false): array|false
    {
        $statusCond = $onlyActive ? "AND p.status = 'active'" : '';
        $sql = "SELECT p.*, c.name AS category_name, u.name AS author_name
                FROM places p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN users u ON u.id = p.created_by
                WHERE p.id = :id {$statusCond}
                LIMIT 1";
        $place = $this->fetchOne($sql, [':id' => $id]);
        if ($place === false) {
            return false;
        }
        return $this->attachAmenities([$place])[0];
    }

    // Tạo slug duy nhất cho quán từ tên, trùng thì thêm -1, -2..., excludeId loại chính quán đang sửa ra.
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

    // Kiểm tra slug quán đã tồn tại chưa, SELECT COUNT(*) FROM places WHERE slug = :slug, excludeId loại chính nó ra.
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT COUNT(*) FROM places WHERE slug = :slug AND id != :id';
            return (int) $this->fetchColumn($sql, [':slug' => $slug, ':id' => $excludeId]) > 0;
        }

        $sql = 'SELECT COUNT(*) FROM places WHERE slug = :slug';
        return (int) $this->fetchColumn($sql, [':slug' => $slug]) > 0;
    }

    // Thêm quán mới, price_range do derivePriceRange suy ra từ price_min/price_max, xong syncForPlace ghi bảng nối.
    public function create(array $data): int
    {
        $slug = $this->generateUniqueSlug($data['name']);

        $sql = 'INSERT INTO places (
                    category_id, name, slug, address, district, description,
                    price_range, price_min, price_max, noise_level,
                    open_time, close_time, google_map_url, status, created_by, created_at
                ) VALUES (
                    :category_id, :name, :slug, :address, :district, :description,
                    :price_range, :price_min, :price_max, :noise_level,
                    :open_time, :close_time, :google_map_url, :status, :created_by, NOW()
                )';

        $this->execute($sql, [
            ':category_id'    => (int) $data['category_id'],
            ':name'           => trim($data['name']),
            ':slug'           => $slug,
            ':address'        => trim($data['address']),
            ':district'       => trim($data['district']),
            ':description'    => trim($data['description']),
            ':price_range'    => $this->derivePriceRange($data['price_min'] ?? null, $data['price_max'] ?? null),
            ':price_min'      => ($data['price_min'] ?? null) === '' || ($data['price_min'] ?? null) === null ? null : max(0, (int) $data['price_min']),
            ':price_max'      => ($data['price_max'] ?? null) === '' || ($data['price_max'] ?? null) === null ? null : max(0, (int) $data['price_max']),
            ':noise_level'    => $data['noise_level'],
            ':open_time'      => $data['open_time'],
            ':close_time'     => $data['close_time'],
            ':google_map_url' => !empty($data['google_map_url']) ? trim($data['google_map_url']) : null,
            ':status'         => $data['status'] ?? 'active',
            ':created_by'     => $data['created_by'] ?? null,
        ]);

        $placeId = (int) $this->lastInsertId();
        (new Amenity())->syncForPlace($placeId, $data['amenity_ids'] ?? []);
        return $placeId;
    }

    // Sửa quán, sinh lại slug duy nhất (loại chính nó), xong syncForPlace đồng bộ lại tiện ích.
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
                    price_min = :price_min,
                    price_max = :price_max,
                    noise_level = :noise_level,
                    open_time = :open_time,
                    close_time = :close_time,
                    google_map_url = :google_map_url,
                    status = :status
                WHERE id = :id';

        $this->execute($sql, [
            ':category_id'    => (int) $data['category_id'],
            ':name'           => trim($data['name']),
            ':slug'           => $slug,
            ':address'        => trim($data['address']),
            ':district'       => trim($data['district']),
            ':description'    => trim($data['description']),
            ':price_range'    => $this->derivePriceRange($data['price_min'] ?? null, $data['price_max'] ?? null),
            ':price_min'      => ($data['price_min'] ?? null) === '' || ($data['price_min'] ?? null) === null ? null : max(0, (int) $data['price_min']),
            ':price_max'      => ($data['price_max'] ?? null) === '' || ($data['price_max'] ?? null) === null ? null : max(0, (int) $data['price_max']),
            ':noise_level'    => $data['noise_level'],
            ':open_time'      => $data['open_time'],
            ':close_time'     => $data['close_time'],
            ':google_map_url' => !empty($data['google_map_url']) ? trim($data['google_map_url']) : null,
            ':status'         => $data['status'] ?? 'active',
            ':id'             => $id,
        ]);
        (new Amenity())->syncForPlace($id, $data['amenity_ids'] ?? []);
        return true;
    }

    // Đổi nhanh trạng thái quán 'active'/'hidden', UPDATE places SET status WHERE id = :id.
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

    // Xóa quán, DELETE FROM places WHERE id = :id, chỉ xóa bảng places (ảnh, favorites, review do bên gọi / CSDL dọn).
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM places WHERE id = :id', [':id' => $id]) > 0;
    }

    // Tính lại avg_rating + total_reviews của quán, chỉ tính review status = 'approved'; recalculateRating chạy sau duyệt/ẩn/sửa/xóa review.
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

    // Đếm tổng số quán, SELECT COUNT(*) FROM places (cả quán ẩn).
    public function countAll(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM places');
    }

    // Đếm số quán đang hoạt động, SELECT COUNT(*) FROM places WHERE status = 'active'.
    public function countActive(): int
    {
        return (int) $this->fetchColumn("SELECT COUNT(*) FROM places WHERE status = 'active'");
    }

    // Lấy Top 5 quán điểm cao nhất cho Admin Dashboard; Top 5 không lọc quán ẩn (không có WHERE status).
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
