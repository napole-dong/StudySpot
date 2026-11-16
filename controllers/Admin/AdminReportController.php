<?php
// Xử lý báo cáo vi phạm về đánh giá: ẩn review vi phạm hoặc bỏ qua báo cáo.
class AdminReportController extends Controller
{
    private ReviewReport $reportModel;
    private Review $reviewModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn các model.
    public function __construct()
    {
        $this->requireAdmin();
        $this->reportModel = new ReviewReport();
        $this->reviewModel = new Review();
    }

    // Danh sách báo cáo, đang chờ xử lý xếp trước (/admin/report)
    public function index(): void
    {
        $this->render('admin/reports/index', [
            'pageTitle' => 'Báo cáo vi phạm',
            'reports'   => $this->reportModel->getAdminList(),
            'pending'   => $this->reportModel->countPending(),
        ], 'admin');
    }

    // Ẩn review bị báo cáo và chốt mọi báo cáo đang chờ của review đó (/admin/report/hide/12)
    public function hide(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/report');
        }

        $this->verifyCsrfToken();
        $report = $this->reportModel->findById((int) $id);

        if (!$report) {
            Session::flash('alert', 'Không tìm thấy báo cáo.', 'danger');
            $this->redirect('admin/report');
        }

        $reviewId = (int) $report['review_id'];
        // updateStatus('hidden') tự tính lại điểm quán và gửi thông báo cho tác giả review.
        $this->reviewModel->updateStatus($reviewId, 'hidden');
        $this->reportModel->resolveAllForReview($reviewId);
        $this->notifyReporter($report, true);

        Session::flash('alert', 'Đã ẩn đánh giá #' . $reviewId . ' và chốt các báo cáo liên quan.', 'success');
        $this->redirect('admin/report');
    }

    // Bỏ qua một báo cáo, review vẫn hiển thị bình thường (/admin/report/dismiss/12)
    public function dismiss(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/report');
        }

        $this->verifyCsrfToken();
        $report = $this->reportModel->findById((int) $id);

        if (!$report) {
            Session::flash('alert', 'Không tìm thấy báo cáo.', 'danger');
            $this->redirect('admin/report');
        }

        $this->reportModel->updateStatus((int) $id, 'dismissed');
        $this->notifyReporter($report, false);

        Session::flash('alert', 'Đã bỏ qua báo cáo #' . (int) $id . '.', 'success');
        $this->redirect('admin/report');
    }

    // Gửi thông báo trong web cho người báo cáo khi báo cáo của họ đã được xử lý xong.
    private function notifyReporter(array $report, bool $reviewHidden): void
    {
        $review = $this->reviewModel->findById((int) $report['review_id']);
        $placeId = $review ? (int) $review['place_id'] : 0;

        (new Notification())->create(
            (int) $report['user_id'],
            $reviewHidden ? 'report_resolved' : 'report_dismissed',
            $reviewHidden
                ? 'Báo cáo của bạn đã được xử lý: đánh giá vi phạm đã bị ẩn khỏi trang quán.'
                : 'Báo cáo của bạn đã được xem xét: đánh giá không vi phạm nên vẫn được hiển thị.',
            $placeId > 0 ? 'place/show/' . $placeId : ''
        );
    }
}
