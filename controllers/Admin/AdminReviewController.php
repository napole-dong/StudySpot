<?php
/**
 * AdminReviewController: Quản lý & Kiểm duyệt Đánh giá (Review) dành cho Admin
 * Khi Duyệt / Ẩn / Xóa review sẽ tự động tính lại avg_rating và total_reviews của quán
 */
class AdminReviewController extends Controller
{
    private Review $reviewModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->reviewModel = new Review();
    }

    /**
     * Danh sách review có lọc theo trạng thái (pending / approved / hidden) và tìm kiếm (/admin/review)
     */
    public function index(): void
    {
        $status = trim($_GET['status'] ?? '');
        $keyword = trim($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->reviewModel->getAdminList($status, $keyword, $page, ADMIN_ITEMS_PER_PAGE);

        $counts = [
            'all'      => $this->reviewModel->countByStatus(null),
            'pending'  => $this->reviewModel->countByStatus('pending'),
            'approved' => $this->reviewModel->countByStatus('approved'),
            'hidden'   => $this->reviewModel->countByStatus('hidden'),
        ];

        $this->render('admin/reviews/index', [
            'pageTitle'   => 'Kiểm duyệt Đánh giá (Reviews)',
            'reviews'     => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'counts'      => $counts,
            'filters'     => [
                'status' => $status,
                'q'      => $keyword,
            ],
        ], 'admin');
    }

    /**
     * Duyệt hoặc Ẩn một review (/admin/review/updateStatus/13)
     * Tự động tính lại điểm trung bình của quán thông qua Review::updateStatus()
     */
    public function updateStatus(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/review');
        }

        $this->verifyCsrfToken();
        $reviewId = (int) $id;
        $newStatus = trim($_POST['status'] ?? '');

        if (!$this->reviewModel->updateStatus($reviewId, $newStatus)) {
            Session::flash('alert', 'Không thể cập nhật trạng thái đánh giá.', 'danger');
            $this->redirect('admin/review');
        }

        $statusLabel = match ($newStatus) {
            'approved' => 'Đã duyệt hiển thị',
            'hidden'   => 'Đã ẩn',
            default    => 'Chờ duyệt',
        };

        Session::flash(
            'alert',
            'Đã chuyển đánh giá #' . $reviewId . ' sang trạng thái "' . $statusLabel . '" và tự động cập nhật lại điểm của quán!',
            'success'
        );

        $returnUrl = $_POST['return_url'] ?? 'admin/review';
        $this->redirect($returnUrl);
    }

    /**
     * Xóa vĩnh viễn một review (/admin/review/delete/13)
     * Tự động tính lại điểm trung bình của quán thông qua Review::delete()
     */
    public function delete(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/review');
        }

        $this->verifyCsrfToken();
        $reviewId = (int) $id;

        if ($this->reviewModel->delete($reviewId)) {
            Session::flash('alert', 'Đã xóa đánh giá #' . $reviewId . ' và cập nhật lại điểm trung bình của quán.', 'success');
        } else {
            Session::flash('alert', 'Không tìm thấy đánh giá cần xóa.', 'danger');
        }

        $this->redirect('admin/review');
    }
}
