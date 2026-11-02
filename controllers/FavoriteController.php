<?php
/**
 * FavoriteController: Quản lý danh sách Quán yêu thích của thành viên
 * Hỗ trợ: Trang danh sách yêu thích (/favorite) và API AJAX thêm/bỏ yêu thích không reload trang (/favorite/toggle)
 */
class FavoriteController extends Controller
{
    private Favorite $favoriteModel;
    private Place $placeModel;

    public function __construct()
    {
        $this->favoriteModel = new Favorite();
        $this->placeModel = new Place();
    }

    /**
     * Hiển thị danh sách các quán đã lưu yêu thích (/favorite)
     */
    public function index(): void
    {
        $this->requireLogin();

        $currentUser = Session::user();
        $userId = (int) $currentUser['id'];
        $places = $this->favoriteModel->getFavoritePlacesByUser($userId);

        $this->render('favorites/index', [
            'pageTitle' => 'Quán học bài Yêu thích đã lưu',
            'places'    => $places,
        ]);
    }

    /**
     * Endpoint xử lý AJAX bằng fetch() để thêm / bỏ yêu thích (/favorite/toggle)
     */
    public function toggle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Phương thức không hợp lệ.'], 405);
        }

        // Nếu chưa đăng nhập thì trả JSON báo frontend chuyển hướng tới trang đăng nhập
        if (!Session::isLoggedIn()) {
            $this->json([
                'success'       => false,
                'require_login' => true,
                'login_url'     => base_url('auth/login'),
                'message'       => 'Vui lòng đăng nhập để lưu quán vào danh sách yêu thích.',
            ], 401);
        }

        // Kiểm tra CSRF Token từ Header X-CSRF-Token hoặc POST body
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Session::verifyCsrf($token)) {
            $this->json([
                'success' => false,
                'message' => 'Phiên làm việc không hợp lệ (CSRF). Vui lòng tải lại trang.',
            ], 403);
        }

        $placeId = (int) ($_POST['place_id'] ?? 0);
        $place = $this->placeModel->findById($placeId, true);

        if (!$place) {
            $this->json([
                'success' => false,
                'message' => 'Địa điểm không tồn tại hoặc đã bị ẩn.',
            ], 404);
        }

        $currentUser = Session::user();
        $userId = (int) $currentUser['id'];
        $actionStatus = $this->favoriteModel->toggle($userId, $placeId);

        $message = ($actionStatus === 'added')
            ? 'Đã lưu "' . $place['name'] . '" vào danh sách Yêu thích!'
            : 'Đã bỏ "' . $place['name'] . '" khỏi danh sách Yêu thích.';

        $this->json([
            'success'  => true,
            'status'   => $actionStatus,
            'place_id' => $placeId,
            'message'  => $message,
        ]);
    }
}
