<?php
/**
 * AdminDashboardController: Trang tổng quan Quản trị hệ thống StudySpot
 * Hiển thị: Tổng quán, Tổng review, Tổng user, Review chờ duyệt, Top 5 quán điểm cao & Biểu đồ Chart.js số review theo tháng
 */
class AdminDashboardController extends Controller
{
    private Place $placeModel;
    private Review $reviewModel;
    private User $userModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->placeModel = new Place();
        $this->reviewModel = new Review();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $totalPlaces = $this->placeModel->countAll();
        $totalReviews = $this->reviewModel->countByStatus(null);
        $pendingReviews = $this->reviewModel->countByStatus('pending');
        $totalUsers = $this->userModel->countAll();

        $topPlaces = $this->placeModel->getTopRatedPlaces(5);
        $monthlyStats = $this->reviewModel->getMonthlyStats(6);

        $chartLabels = array_column($monthlyStats, 'label');
        $chartValues = array_map('intval', array_column($monthlyStats, 'total'));

        $this->render('admin/dashboard', [
            'pageTitle'      => 'Bảng điều khiển Quản trị',
            'totalPlaces'    => $totalPlaces,
            'totalReviews'   => $totalReviews,
            'pendingReviews' => $pendingReviews,
            'totalUsers'     => $totalUsers,
            'topPlaces'      => $topPlaces,
            'chartLabels'    => $chartLabels,
            'chartValues'    => $chartValues,
        ], 'admin');
    }
}
