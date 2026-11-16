<?php
// Đề xuất quán mới của thành viên: gửi đề xuất và theo dõi trạng thái duyệt của chính mình.
class SuggestionController extends Controller
{
    private PlaceSuggestion $suggestionModel;
    private Category $categoryModel;
    private Place $placeModel;

    // Yêu cầu đăng nhập (requireLogin) rồi tạo sẵn các model.
    public function __construct()
    {
        $this->requireLogin();
        $this->suggestionModel = new PlaceSuggestion();
        $this->categoryModel = new Category();
        $this->placeModel = new Place();
    }

    // Trang đề xuất: form gửi kèm danh sách đề xuất của chính mình (/suggestion)
    public function index(): void
    {
        $userId = (int) (Session::user()['id'] ?? 0);

        $this->render('suggestions/index', [
            'pageTitle'   => 'Đề xuất quán mới',
            'categories'  => $this->categoryModel->getAll(),
            'districts'   => $this->districts(),
            'suggestions' => $this->suggestionModel->getByUser($userId),
        ]);
    }

    // Nhận form đề xuất, kiểm tra dữ liệu rồi lưu ở trạng thái chờ duyệt (/suggestion/store)
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('suggestion');
        }

        $this->verifyCsrfToken();

        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 150) {
            Session::flash('alert', 'Tên quán cần từ 3 đến 150 ký tự.', 'warning');
            $this->redirect('suggestion');
        }

        if ($address === '' || mb_strlen($address) > 255) {
            Session::flash('alert', 'Vui lòng nhập địa chỉ quán (tối đa 255 ký tự).', 'warning');
            $this->redirect('suggestion');
        }

        if (!in_array($district, $this->districts(), true)) {
            Session::flash('alert', 'Vui lòng chọn Phường / Khu vực của quán.', 'warning');
            $this->redirect('suggestion');
        }

        if ($categoryId <= 0 || !$this->categoryModel->findById($categoryId)) {
            Session::flash('alert', 'Vui lòng chọn danh mục cho quán.', 'warning');
            $this->redirect('suggestion');
        }

        $this->suggestionModel->create((int) (Session::user()['id'] ?? 0), [
            'name'        => $name,
            'address'     => $address,
            'district'    => $district,
            'category_id' => $categoryId,
            'description' => $description !== '' ? mb_substr($description, 0, 1000) : null,
        ]);

        Session::flash('alert', 'Đã gửi đề xuất! Quản trị viên sẽ xem xét và thông báo cho bạn khi có kết quả.', 'success');
        $this->redirect('suggestion');
    }

    // Danh sách phường Long Xuyên: các phường chuẩn gộp với phường đang có trong CSDL (giống trang admin quán).
    private function districts(): array
    {
        $standardWards = [
            'Phường Đông Xuyên',
            'Phường Mỹ Xuyên',
            'Phường Mỹ Long',
            'Phường Mỹ Bình',
            'Phường Mỹ Quý',
            'Phường Bình Khánh',
        ];

        return array_values(array_unique(array_merge($standardWards, $this->placeModel->getDistinctDistricts())));
    }
}
