<?php
// Trang tổng quan của admin.
class AdminDashboardController extends Controller
{
    private Place $placeModel;
    private Review $reviewModel;
    private User $userModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn model.
    public function __construct()
    {
        // Chỉ role admin mới dùng được controller này (requireAdmin).
        $this->requireAdmin();
        $this->placeModel = new Place();
        $this->reviewModel = new Review();
        $this->userModel = new User();
    }

    // Trang tổng quan admin (/admin/dashboard): thống kê và Top 5 quán.
    public function index(): void
    {
        $totalPlaces = $this->placeModel->countAll();
        $activePlaces = $this->placeModel->countActive();
        // Đếm tất cả review: truyền null là không lọc trạng thái.
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
