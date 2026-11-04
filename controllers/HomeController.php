<?php
/**
 * HomeController: Hiển thị Trang chủ StudySpot (Khu vực TP. Long Xuyên, An Giang - ĐH An Giang)
 * Bao gồm:
 * - Thống kê số lượng thực tế từ CSDL (COUNT places, reviews, users)
 * - Banner tìm kiếm nhanh + Nhóm tag lọc thật
 * - Danh mục loại hình kèm số lượng quán active thực tế
 * - Quán nổi bật điểm cao & Quán mới cập nhật tại Long Xuyên
 */
class HomeController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private Favorite $favoriteModel;
    private Review $reviewModel;
    private User $userModel;

    public function __construct()
    {
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->favoriteModel = new Favorite();
        $this->reviewModel = new Review();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $featuredPerPage = 6;
        $featuredTotalPages = max(1, (int) ceil($this->placeModel->countActive() / $featuredPerPage));
        $featuredPage = (int) ($_GET['featured_page'] ?? 1);
        $featuredPage = max(1, min($featuredPage, $featuredTotalPages));
        $featuredPlaces = $this->placeModel->getFeaturedPlaces($featuredPerPage, ($featuredPage - 1) * $featuredPerPage);
        $heroPlace = $this->placeModel->getHeroPlace();
        if ($heroPlace === null) {
            // Khong co quan dat nguong len bia: lay quan noi bat so 1 TOAN CUC (trang 1),
            // khong lay quan dau tien cua trang hien tai, de bia khong doi khi chuyen trang.
            $topFeatured = $this->placeModel->getFeaturedPlaces(1, 0);
            $heroPlace = $topFeatured[0] ?? ($featuredPlaces[0] ?? null);
        }
        $latestPlaces = $this->placeModel->getLatestPlaces(6);
        $categories = $this->categoryModel->getAllWithPlaceCount();
        $districts = $this->placeModel->getDistinctDistricts();

        // Đếm số liệu thực tế 100% từ Cơ sở dữ liệu (tuyệt đối không gắn cứng con số quảng cáo ảo)
        $stats = [
            'total_places'    => $this->placeModel->countActive(),
            'total_reviews'   => $this->reviewModel->countByStatus('approved'),
            'total_users'     => $this->userModel->countAll(),
            'total_districts' => count($districts),
        ];

        $favoriteIds = [];
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $favoriteIds = $this->favoriteModel->getFavoritePlaceIds((int) $currentUser['id']);
        }

        $this->render('home/index', [
            'pageTitle'      => 'Trang chủ - Quán học bài & Cà phê Sinh viên ĐH An Giang (Long Xuyên)',
            'featuredPlaces' => $featuredPlaces,
            'featuredPage'     => $featuredPage,
            'featuredTotalPages' => $featuredTotalPages,
            'heroPlace'      => $heroPlace,
            'latestPlaces'   => $latestPlaces,
            'categories'     => $categories,
            'districts'      => $districts,
            'stats'          => $stats,
            'favoriteIds'    => $favoriteIds,
        ]);
    }
}
