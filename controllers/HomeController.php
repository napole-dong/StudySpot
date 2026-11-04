<?php
// Trang chủ StudySpot, không cần đăng nhập.
class HomeController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private Favorite $favoriteModel;
    private Review $reviewModel;
    private User $userModel;

    // Tạo sẵn các model cho trang chủ (Place, Category, Favorite, Review, User).
    public function __construct()
    {
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->favoriteModel = new Favorite();
        $this->reviewModel = new Review();
        $this->userModel = new User();
    }

    // Trang chủ (/): quán nổi bật phân trang, ảnh bìa, quán mới, thống kê.
    public function index(): void
    {
        $featuredPerPage = 6;
        // Phân trang quán nổi bật: mỗi trang 6 quán, ít nhất 1 trang.
        $featuredTotalPages = max(1, (int) ceil($this->placeModel->countActive() / $featuredPerPage));
        $featuredPage = (int) ($_GET['featured_page'] ?? 1);
        // Kẹp số trang trong khoảng hợp lệ, không ra trang trống.
        $featuredPage = max(1, min($featuredPage, $featuredTotalPages));
        $featuredPlaces = $this->placeModel->getFeaturedPlaces($featuredPerPage, ($featuredPage - 1) * $featuredPerPage);
        $heroPlace = $this->placeModel->getHeroPlace();
        // Không có ảnh bìa thì lấy quán nổi bật số 1 toàn cục.
        if ($heroPlace === null) {
            $topFeatured = $this->placeModel->getFeaturedPlaces(1, 0);
            $heroPlace = $topFeatured[0] ?? ($featuredPlaces[0] ?? null);
        }
        $latestPlaces = $this->placeModel->getLatestPlaces(6);
        $categories = $this->categoryModel->getAllWithPlaceCount();
        $districts = $this->placeModel->getDistinctDistricts();

        // Đếm số liệu thực tế 100% từ Cơ sở dữ liệu (tuyệt đối không gắn cứng con số quảng cáo ảo)
        $stats = [
            'total_places'    => $this->placeModel->countActive(),
        // Thống kê thật từ CSDL, chỉ đếm review đã duyệt (approved).
            'total_reviews'   => $this->reviewModel->countByStatus('approved'),
            'total_users'     => $this->userModel->countAll(),
            'total_districts' => count($districts),
        ];

        $favoriteIds = [];
        // Chỉ khi đã đăng nhập mới lấy danh sách quán yêu thích.
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
