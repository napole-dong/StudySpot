<?php
// Báo cáo vi phạm của thành viên về một review: mỗi người chỉ báo cáo 1 lần cho 1 review.
class ReviewReport extends Model
{
    // Các lý do báo cáo hợp lệ: khóa lưu trong DB => nhãn hiển thị cho người dùng và admin.
    public const REASONS = [
        'spam'      => 'Spam / quảng cáo',
        'offensive' => 'Nội dung thô tục, xúc phạm',
        'fake'      => 'Thông tin sai sự thật',
        'other'     => 'Lý do khác',
    ];

    // Tạo báo cáo mới ở trạng thái chờ xử lý; người này đã báo cáo review này rồi thì trả false.
    public function create(int $reviewId, int $userId, string $reason, ?string $note): bool
    {
        $exists = (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM review_reports WHERE review_id = :review_id AND user_id = :user_id',
            [':review_id' => $reviewId, ':user_id' => $userId]
        );
        if ($exists > 0) {
            return false;
        }

        $this->execute(
            "INSERT INTO review_reports (review_id, user_id, reason, note, status, created_at)
             VALUES (:review_id, :user_id, :reason, :note, 'pending', NOW())",
            [':review_id' => $reviewId, ':user_id' => $userId, ':reason' => $reason, ':note' => $note]
        );

        // Báo cho admin biết có báo cáo vi phạm mới đang chờ xử lý.
        $placeName = (string) $this->fetchColumn(
            'SELECT p.name FROM reviews r INNER JOIN places p ON p.id = r.place_id WHERE r.id = :id',
            [':id' => $reviewId]
        );
        (new Notification())->notifyAdmins(
            'report_new',
            'Có báo cáo vi phạm mới về một đánh giá' . ($placeName !== '' ? ' tại quán "' . $placeName . '"' : '') . ', đang chờ xử lý.',
            'admin/report'
        );

        return true;
    }

    // Danh sách báo cáo cho admin: đang chờ xử lý xếp trước, kèm nội dung review, quán, tác giả và người báo cáo.
    public function getAdminList(): array
    {
        $sql = "SELECT rp.*, r.content AS review_content, r.status AS review_status, r.place_id,
                       p.name AS place_name, au.name AS author_name, ru.name AS reporter_name
                FROM review_reports rp
                INNER JOIN reviews r ON r.id = rp.review_id
                INNER JOIN places p ON p.id = r.place_id
                INNER JOIN users au ON au.id = r.user_id
                INNER JOIN users ru ON ru.id = rp.user_id
                ORDER BY (rp.status = 'pending') DESC, rp.created_at DESC, rp.id DESC";

        return $this->fetchAll($sql);
    }

    // Đếm báo cáo đang chờ xử lý, dùng cho huy hiệu trên menu admin.
    public function countPending(): int
    {
        return (int) $this->fetchColumn("SELECT COUNT(*) FROM review_reports WHERE status = 'pending'");
    }

    // Tìm một báo cáo theo id, không có thì trả null.
    public function findById(int $id): ?array
    {
        $row = $this->fetchOne('SELECT * FROM review_reports WHERE id = :id', [':id' => $id]);
        return $row ?: null;
    }

    // Đổi trạng thái một báo cáo: resolved (đã xử lý) hoặc dismissed (bỏ qua).
    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'resolved', 'dismissed'], true)) {
            return false;
        }

        return $this->execute('UPDATE review_reports SET status = :status WHERE id = :id', [
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    // Khi admin ẩn review bị báo cáo: chốt mọi báo cáo đang chờ của review đó thành đã xử lý.
    public function resolveAllForReview(int $reviewId): void
    {
        $this->execute("UPDATE review_reports SET status = 'resolved' WHERE review_id = :review_id AND status = 'pending'", [
            ':review_id' => $reviewId,
        ]);
    }
}
