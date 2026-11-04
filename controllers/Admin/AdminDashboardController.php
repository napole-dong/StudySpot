<?php
/**
 * AdminDashboardController: Trang tổng quan Quản trị hệ thống StudySpot — TP. Long Xuyên, An Giang
 * Hiển thị: Tổng quán, Tổng review, Tổng user, Review chờ duyệt, Top 5 quán điểm cao
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
        $activePlaces = $this->placeModel->countActive();
        $totalReviews = $this->reviewModel->countByStatus(null);
        $pendingReviews = $this->reviewModel->countByStatus('pending');
        $totalUsers = $this->userModel->countAll();

        $topPlaces = $this->placeModel->getTopRatedPlaces(5);
        $recentPending = $this->reviewModel->getAdminList('pending', '', 1, 5)['items'];

        $this->render('admin/dashboard', [
            'pageTitle'      => 'Bảng điều khiển Quản trị — StudySpot Long Xuyên',
            'totalPlaces'    => $totalPlaces,
            'activePlaces'   => $activePlaces,
            'totalReviews'   => $totalReviews,
            'pendingReviews' => $pendingReviews,
            'totalUsers'     => $totalUsers,
            'topPlaces'      => $topPlaces,
            'recentPending'  => $recentPending,
        ], 'admin');
    }
}
