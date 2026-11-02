<?php
/**
 * PlaceController: Xử lý Danh sách quán (lọc đa tiêu chí, tìm kiếm, sắp xếp, phân trang)
 * và Chi tiết quán (slider ảnh, tiện ích, giờ mở cửa tự động, Google Maps iframe, điểm thành phần & danh sách review)
 */
class PlaceController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private PlaceImage $placeImageModel;
    private Review $reviewModel;
    private Favorite $favoriteModel;

    public function __construct()
    {
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->placeImageModel = new PlaceImage();
        $this->reviewModel = new Review();
        $this->favoriteModel = new Favorite();
    }

    /**
     * Trang Danh sách & Khám phá quán (/place)
     */
    public function index(): void
    {
        $filters = [
            'q'           => trim($_GET['q'] ?? ''),
            'category_id' => (int) ($_GET['category_id'] ?? 0),
            'district'    => trim($_GET['district'] ?? ''),
            'price_range' => (int) ($_GET['price_range'] ?? 0),
            'has_wifi'    => !empty($_GET['has_wifi']) ? 1 : 0,
            'has_outlet'  => !empty($_GET['has_outlet']) ? 1 : 0,
            'has_ac'      => !empty($_GET['has_ac']) ? 1 : 0,
            'noise_level' => trim($_GET['noise_level'] ?? ''),
            'sort'        => trim($_GET['sort'] ?? 'rating_desc'),
        ];

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->placeModel->getFilteredPlaces($filters, $page, ITEMS_PER_PAGE);

        $categories = $this->categoryModel->getAll();
        $districts = $this->placeModel->getDistinctDistricts();

        $favoriteIds = [];
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $favoriteIds = $this->favoriteModel->getFavoritePlaceIds((int) $currentUser['id']);
        }

        $this->render('places/index', [
            'pageTitle'   => 'Khám phá Quán học bài',
            'places'      => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'categories'  => $categories,
            'districts'   => $districts,
            'filters'     => $filters,
            'favoriteIds' => $favoriteIds,
        ]);
    }

    /**
     * Trang Chi tiết một quán học bài (/place/show/5)
     */
    public function show(int|string $id = 0): void
    {
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId, true);

        if (!$place) {
            $this->abort404('Địa điểm bạn tìm kiếm không tồn tại hoặc đang tạm ẩn.');
            return;
        }

        $images = $this->placeImageModel->getByPlaceId($placeId);
        $criteriaStats = $this->reviewModel->getCriteriaAverages($placeId);

        // Phân trang danh sách review đã duyệt
        $reviewPage = max(1, (int) ($_GET['page'] ?? 1));
        $reviewsData = $this->reviewModel->getApprovedByPlace($placeId, $reviewPage, REVIEWS_PER_PAGE);

        // Kiểm tra thông tin yêu thích và review của người dùng đang đăng nhập
        $isFavorited = false;
        $myReview = null;
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $userId = (int) $currentUser['id'];
            $isFavorited = $this->favoriteModel->isFavorited($userId, $placeId);
            $myReview = $this->reviewModel->findByPlaceAndUser($placeId, $userId);
        }

        $this->render('places/show', [
            'pageTitle'     => $place['name'],
            'place'         => $place,
            'images'        => $images,
            'criteriaStats' => $criteriaStats,
            'reviews'       => $reviewsData['items'],
            'reviewTotal'   => $reviewsData['total'],
            'reviewPage'    => $reviewsData['currentPage'],
            'reviewPages'   => $reviewsData['totalPages'],
            'isFavorited'   => $isFavorited,
            'myReview'      => $myReview,
        ]);
    }
}
