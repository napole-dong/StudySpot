<?php
// Model Review: quản lý đánh giá, bảng reviews, status pending (chờ duyệt) / approved (hiện công khai, tính điểm) / hidden (ẩn).
// Model này được trang chi tiết quán, controller gửi/sửa/xóa review và controller quản trị gọi; một người một quán chỉ một review.
class Review extends Model
{
    // Lấy review đã duyệt của quán có phân trang, WHERE place_id = :place_id AND status = 'approved', mới nhất trước.
    public function getApprovedByPlace(int $placeId, int $page = 1, int $perPage = REVIEWS_PER_PAGE): array
    {
        $countSql = "SELECT COUNT(*) FROM reviews WHERE place_id = :place_id AND status = 'approved'";
        $total = (int) $this->fetchColumn($countSql, [':place_id' => $placeId]);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT r.*, u.name AS user_name, u.avatar AS user_avatar
                FROM reviews r
                INNER JOIN users u ON u.id = r.user_id
                WHERE r.place_id = :place_id AND r.status = 'approved'
                ORDER BY r.created_at DESC, r.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'items'       => $this->fetchAll($sql, [':place_id' => $placeId]),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    // Tính điểm trung bình từng tiêu chí (overall, wifi, space, price), chỉ tính review đã duyệt, làm tròn 1 chữ số.
    public function getCriteriaAverages(int $placeId): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(ROUND(AVG(rating_overall), 1), 0.0) AS avg_overall,
                    COALESCE(ROUND(AVG(rating_wifi), 1), 0.0) AS avg_wifi,
                    COALESCE(ROUND(AVG(rating_space), 1), 0.0) AS avg_space,
                    COALESCE(ROUND(AVG(rating_price), 1), 0.0) AS avg_price
                FROM reviews
                WHERE place_id = :place_id AND status = 'approved'";

        $row = $this->fetchOne($sql, [':place_id' => $placeId]);
        return [
            'total'       => (int) ($row['total'] ?? 0),
            'avg_overall' => (float) ($row['avg_overall'] ?? 0.0),
            'avg_wifi'    => (float) ($row['avg_wifi'] ?? 0.0),
            'avg_space'   => (float) ($row['avg_space'] ?? 0.0),
            'avg_price'   => (float) ($row['avg_price'] ?? 0.0),
        ];
    }

    // Tìm review của một người tại một quán, WHERE place_id + user_id LIMIT 1, để chặn gửi review thứ hai.
    public function findByPlaceAndUser(int $placeId, int $userId): array|false
    {
        $sql = 'SELECT * FROM reviews WHERE place_id = :place_id AND user_id = :user_id LIMIT 1';
        return $this->fetchOne($sql, [
            ':place_id' => $placeId,
            ':user_id'  => $userId,
        ]);
    }

    // Tìm review theo id, JOIN places + users lấy place_name và user_name.
    public function findById(int $id): array|false
    {
        $sql = 'SELECT r.*, p.name AS place_name, u.name AS user_name
                FROM reviews r
                INNER JOIN places p ON p.id = r.place_id
                INNER JOIN users u ON u.id = r.user_id
                WHERE r.id = :id LIMIT 1';
        return $this->fetchOne($sql, [':id' => $id]);
    }

    // Thêm review mới, mặc định status = 'pending', admin tạo thì 'approved' ngay; chỉ khi approved mới gọi recalculateRating.
    public function create(array $data): int
    {
        $sql = "INSERT INTO reviews (
                    place_id, user_id, rating_overall, rating_wifi, rating_space, rating_price,
                    content, image_path, status, created_at
                ) VALUES (
                    :place_id, :user_id, :rating_overall, :rating_wifi, :rating_space, :rating_price,
                    :content, :image_path, :status, NOW()
                )";

        $this->execute($sql, [
            ':place_id'       => (int) $data['place_id'],
            ':user_id'        => (int) $data['user_id'],
            ':rating_overall' => (int) $data['rating_overall'],
            ':rating_wifi'    => (int) $data['rating_wifi'],
            ':rating_space'   => (int) $data['rating_space'],
            ':rating_price'   => (int) $data['rating_price'],
            ':content'        => trim($data['content']),
            ':image_path'     => $data['image_path'] ?? null,
            ':status'         => $data['status'] ?? 'pending',
        ]);

        $newId = (int) $this->lastInsertId();

        if (($data['status'] ?? 'pending') === 'approved') {
            (new Place())->recalculateRating((int) $data['place_id']);
        }

        return $newId;
    }

    // Sửa review của chính người dùng, ghi cùng status = 'pending' để duyệt lại, xong gọi recalculateRating.
    public function updateByUser(int $reviewId, array $data): bool
    {
        $sql = "UPDATE reviews SET
                    rating_overall = :rating_overall,
                    rating_wifi = :rating_wifi,
                    rating_space = :rating_space,
                    rating_price = :rating_price,
                    content = :content,
                    image_path = :image_path,
                    status = 'pending'
                WHERE id = :id";

        $updated = $this->execute($sql, [
            ':rating_overall' => (int) $data['rating_overall'],
            ':rating_wifi'    => (int) $data['rating_wifi'],
            ':rating_space'   => (int) $data['rating_space'],
            ':rating_price'   => (int) $data['rating_price'],
            ':content'        => trim($data['content']),
            ':image_path'     => $data['image_path'] ?? null,
            ':id'             => $reviewId,
        ]) >= 0;

        if (!empty($data['place_id'])) {
            (new Place())->recalculateRating((int) $data['place_id']);
        }

        return $updated;
    }

    // Đổi trạng thái kiểm duyệt review (pending/approved/hidden), xong tự động gọi recalculateRating của quán.
    public function updateStatus(int $reviewId, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'hidden'], true)) {
            return false;
        }

        $review = $this->findById($reviewId);
        if (!$review) {
            return false;
        }

        $this->execute('UPDATE reviews SET status = :status WHERE id = :id', [
            ':status' => $status,
            ':id'     => $reviewId,
        ]);

        // Tự động cập nhật avg_rating và total_reviews của quán
        (new Place())->recalculateRating((int) $review['place_id']);
        return true;
    }

    // Xóa review cả file ảnh đính kèm, xong gọi recalculateRating nếu xóa thành công.
    public function delete(int $reviewId): bool
    {
        $review = $this->findById($reviewId);
        if (!$review) {
            return false;
        }

        if (!empty($review['image_path'])) {
            delete_uploaded_file($review['image_path']);
        }

        $deleted = $this->execute('DELETE FROM reviews WHERE id = :id', [':id' => $reviewId]) > 0;
        if ($deleted) {
            (new Place())->recalculateRating((int) $review['place_id']);
        }

        return $deleted;
    }

    // Lấy lịch sử review của một thành viên, không lọc status để chủ review thấy cả review chờ duyệt và bị ẩn.
    public function getByUserId(int $userId): array
    {
        $sql = 'SELECT r.*, p.name AS place_name, p.district AS place_district,
                       (SELECT pi.image_path FROM place_images pi
                        WHERE pi.place_id = p.id
                        ORDER BY pi.is_cover DESC, pi.id ASC LIMIT 1) AS place_cover
                FROM reviews r
                INNER JOIN places p ON p.id = r.place_id
                WHERE r.user_id = :user_id
                ORDER BY r.created_at DESC, r.id DESC';
        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    // Lấy review cho admin, lọc status + từ khóa, xếp pending trước rồi approved rồi hidden, mới nhất trước trong cùng trạng thái.
    public function getAdminList(string $status = '', string $keyword = '', int $page = 1, int $perPage = ADMIN_ITEMS_PER_PAGE): array
    {
        $conditions = [];
        $params = [];

        if (in_array($status, ['pending', 'approved', 'hidden'], true)) {
            $conditions[] = 'r.status = :status';
            $params[':status'] = $status;
        }

        if ($keyword !== '') {
            $conditions[] = '(p.name LIKE :kw1 OR u.name LIKE :kw2 OR r.content LIKE :kw3)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $countSql = "SELECT COUNT(*)
                     FROM reviews r
                     INNER JOIN places p ON p.id = r.place_id
                     INNER JOIN users u ON u.id = r.user_id
                     {$whereClause}";
        $total = (int) $this->fetchColumn($countSql, $params);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT r.*, p.name AS place_name, u.name AS user_name, u.email AS user_email
                FROM reviews r
                INNER JOIN places p ON p.id = r.place_id
                INNER JOIN users u ON u.id = r.user_id
                {$whereClause}
                ORDER BY FIELD(r.status, 'pending', 'approved', 'hidden'), r.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'items'       => $this->fetchAll($sql, $params),
            'total'       => $total,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }

    // Đếm số review, có status thì đếm theo status, không có thì đếm tất cả.
    public function countByStatus(?string $status = null): int
    {
        if ($status !== null) {
            return (int) $this->fetchColumn('SELECT COUNT(*) FROM reviews WHERE status = :status', [':status' => $status]);
        }
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM reviews');
    }

    // Ghi chú: không có hàm thống kê review theo tháng trong file này, docblock cũ về biểu đồ Chart.js không kèm hàm nào.
}
