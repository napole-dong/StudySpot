<?php
/**
 * HomeController: Hiển thị Trang chủ StudySpot
 * Bao gồm: Banner tìm kiếm nhanh, Nhóm tag nhanh, Danh mục loại hình, Quán nổi bật (điểm cao) & Quán mới cập nhật
 */
class HomeController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private Favorite $favoriteModel;

    public function __construct()
    {
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->favoriteModel = new Favorite();
    }

    public function index(): void
    {
        $featuredPlaces = $this->placeModel->getFeaturedPlaces(6);
        $latestPlaces = $this->placeModel->getLatestPlaces(6);
        $categories = $this->categoryModel->getAllWithPlaceCount();
        $districts = $this->placeModel->getDistinctDistricts();

        $favoriteIds = [];
        if (Session::isLoggedIn()) {
            $currentUser = Session::user();
            $favoriteIds = $this->favoriteModel->getFavoritePlaceIds((int) $currentUser['id']);
        }

        $this->render('home/index', [
            'pageTitle'      => 'Trang chủ',
            'featuredPlaces' => $featuredPlaces,
            'latestPlaces'   => $latestPlaces,
            'categories'     => $categories,
            'districts'      => $districts,
            'favoriteIds'    => $favoriteIds,
        ]);
    }
}
