<?php
// Người dùng xem quán: danh sách, gợi ý, chi tiết.
class PlaceController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private PlaceImage $placeImageModel;
    private Review $reviewModel;
    private Favorite $favoriteModel;

    // Tạo sẵn các model xem quán (Place, Category, PlaceImage, Review, Favorite).
    public function __construct()
    {
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->placeImageModel = new PlaceImage();
        $this->reviewModel = new Review();
        $this->favoriteModel = new Favorite();
    }

    // Trang Danh sách & Khám phá quán (/place)
    public function index(): void
    {
        $amenityParam = $_GET['amenity'] ?? [];
        // Chuẩn hóa tham số amenity về mảng trước khi lọc.
        if (!is_array($amenityParam)) {
            $amenityParam = [$amenityParam];
        }
        $filters = [
            'q'           => trim($_GET['q'] ?? ''),
            'category_id' => (int) ($_GET['category_id'] ?? 0),
            'district'    => trim($_GET['district'] ?? ''),
            'price_range' => (int) ($_GET['price_range'] ?? 0),
        // Lọc sạch mảng tiện ích: bỏ trống, bỏ trùng, đánh lại chỉ số.
            'amenity'     => array_values(array_unique(array_filter(array_map('trim', $amenityParam)))),
            'noise_level' => trim($_GET['noise_level'] ?? ''),
            'sort'        => trim($_GET['sort'] ?? 'rating_desc'),
        ];

        // Phân trang: số trang nhỏ nhất là 1.
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->placeModel->getFilteredPlaces($filters, $page, ITEMS_PER_PAGE);

        $categories = $this->categoryModel->getAll();
        $districts = $this->placeModel->getDistinctDistricts();
        $amenities = (new Amenity())->getAll();

        $favoriteIds = [];
        // Chỉ khi đã đăng nhập mới kiểm tra yêu thích và review của mình.
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $favoriteIds = $this->favoriteModel->getFavoritePlaceIds((int) $currentUser['id']);
        }

        $this->render('places/index', [
            'pageTitle'   => 'Khám phá Quán học bài tại TP. Long Xuyên',
            'places'      => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'categories'  => $categories,
            'districts'   => $districts,
            'amenities'   => $amenities,
            'filters'     => $filters,
            'favoriteIds' => $favoriteIds,
        ]);
    }


    // Gợi ý tìm kiếm trực tiếp (/place/suggest?q=...): trả JSON, tối đa 6 quán, không cần đăng nhập
    public function suggest(): void
    {
        $q = trim($_GET['q'] ?? '');
        header('Content-Type: application/json; charset=utf-8');
        // Gợi ý chỉ khi đủ 2 ký tự, dưới 2 ký tự thì trả rỗng.
        if (mb_strlen($q) < 2) {
            echo json_encode(['items' => [], 'total' => 0]);
            return;
        }
        // Gợi ý giới hạn 6 kết quả, trang 1, điểm cao trước.
        $result = $this->placeModel->getFilteredPlaces(['q' => $q, 'sort' => 'rating_desc'], 1, 6);
        $items = [];
        foreach ($result['items'] as $pl) {
            $items[] = [
                'id' => (int) $pl['id'],
                'name' => $pl['name'],
                'district' => $pl['district'],
                'category' => $pl['category_name'],
                'rating' => (float) $pl['avg_rating'],
                'reviews' => (int) $pl['total_reviews'],
                'url' => base_url('place/show/' . (int) $pl['id']),
            ];
        }
        echo json_encode(['items' => $items, 'total' => (int) $result['total']]);
    }

    // Trang Chi tiết một quán học bài (/place/show/5)
    public function show(int|string $id = 0): void
    {
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId, true);

        // Quán không tồn tại hoặc tạm ẩn thì trả trang 404.
        if (!$place) {
            $this->abort404('Địa điểm bạn tìm kiếm không tồn tại hoặc đang tạm ẩn.');
            return;
        }

        $images = $this->placeImageModel->getByPlaceId($placeId);
        $criteriaStats = $this->reviewModel->getCriteriaAverages($placeId);

        // Lấy 3-4 quán tương tự cùng danh mục hoặc cùng Phường tại TP. Long Xuyên (trừ quán hiện tại)
        $relatedPlaces = $this->placeModel->getRelatedPlaces(
            $placeId,
            (int) $place['category_id'],
            (string) $place['district'],
            3
        );

        // Phân trang review: số trang nhỏ nhất là 1.
        $reviewPage = max(1, (int) ($_GET['page'] ?? 1));
        $reviewsData = $this->reviewModel->getApprovedByPlace($placeId, $reviewPage, REVIEWS_PER_PAGE);

        // Kiểm tra thông tin yêu thích và review của người dùng đang đăng nhập
        $isFavorited = false;
        $favoriteIds = [];
        $myReview = null;
        // Chỉ khi đã đăng nhập mới kiểm tra yêu thích và review của mình.
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $userId = (int) $currentUser['id'];
            $isFavorited = $this->favoriteModel->isFavorited($userId, $placeId);
            $favoriteIds = $this->favoriteModel->getFavoritePlaceIds($userId);
            $myReview = $this->reviewModel->findByPlaceAndUser($placeId, $userId);
        }

        $this->render('places/show', [
            'pageTitle'     => $place['name'] . ' - ' . $place['district'],
            'place'         => $place,
            'images'        => $images,
            'criteriaStats' => $criteriaStats,
            'relatedPlaces' => $relatedPlaces,
            'reviews'       => $reviewsData['items'],
            'reviewTotal'   => $reviewsData['total'],
            'reviewPage'    => $reviewsData['currentPage'],
            'reviewPages'   => $reviewsData['totalPages'],
            'isFavorited'   => $isFavorited,
            'favoriteIds'   => $favoriteIds,
            'myReview'      => $myReview,
        ]);
    }
}
