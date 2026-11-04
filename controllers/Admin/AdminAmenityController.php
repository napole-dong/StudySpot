<?php
/**
 * AdminAmenityController: Quản lý Tiện ích (amenities) — thêm/sửa/xóa.
 * Thêm tiện ích mới ở đây là tự xuất hiện ở form quán, bộ lọc và thẻ quán, không cần sửa code.
 * Tên class có tiền tố Admin theo quy tắc: AdminAmenityController
 */
class AdminAmenityController extends Controller
{
    private Amenity $amenityModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->amenityModel = new Amenity();
    }

    /**
     * Hiển thị danh sách tiện ích và form thêm/sửa (/admin/amenity)
     */
    public function index(): void
    {
        $amenities = $this->amenityModel->getAllWithPlaceCount();
        $editAmenity = null;

        if (!empty($_GET['edit_id'])) {
            $editAmenity = $this->amenityModel->findById((int) $_GET['edit_id']);
        }

        $this->render('admin/amenities/index', [
            'pageTitle'   => 'Quản lý Tiện ích',
            'amenities'   => $amenities,
            'editAmenity' => $editAmenity,
        ], 'admin');
    }

    /**
     * Xử lý thêm tiện ích mới (/admin/amenity/store)
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/amenity');
        }

        $this->verifyCsrfToken();
        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            Session::flash('alert', 'Tên tiện ích không được để trống.', 'danger');
            $this->redirect('admin/amenity');
        }

        if (mb_strlen($name, 'UTF-8') > 100) {
            Session::flash('alert', 'Tên tiện ích không được vượt quá 100 ký tự.', 'danger');
            $this->redirect('admin/amenity');
        }

        if ($this->amenityModel->nameExists($name)) {
            Session::flash('alert', 'Tên tiện ích này đã tồn tại trong hệ thống.', 'warning');
            $this->redirect('admin/amenity');
        }

        $this->amenityModel->create($name);
        Session::flash('alert', 'Đã thêm tiện ích "' . $name . '" thành công! Tiện ích mới đã xuất hiện ở form quán và bộ lọc.', 'success');
        $this->redirect('admin/amenity');
    }

    /**
     * Xử lý cập nhật tên tiện ích (/admin/amenity/update/5)
     */
    public function update(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/amenity');
        }

        $this->verifyCsrfToken();
        $amenityId = (int) $id;
        $amenity = $this->amenityModel->findById($amenityId);

        if (!$amenity) {
            Session::flash('alert', 'Không tìm thấy tiện ích cần cập nhật.', 'danger');
            $this->redirect('admin/amenity');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            Session::flash('alert', 'Tên tiện ích không được để trống.', 'danger');
            $this->redirect('admin/amenity?edit_id=' . $amenityId);
        }

        if ($this->amenityModel->nameExists($name, $amenityId)) {
            Session::flash('alert', 'Tên tiện ích "' . $name . '" đã bị trùng với tiện ích khác.', 'warning');
            $this->redirect('admin/amenity?edit_id=' . $amenityId);
        }

        $this->amenityModel->update($amenityId, $name);
        Session::flash('alert', 'Đã cập nhật tên tiện ích thành công!', 'success');
        $this->redirect('admin/amenity');
    }

    /**
     * Xử lý xóa tiện ích (/admin/amenity/delete/5)
     */
    public function delete(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/amenity');
        }

        $this->verifyCsrfToken();
        $amenityId = (int) $id;
        $amenity = $this->amenityModel->findById($amenityId);

        if (!$amenity) {
            Session::flash('alert', 'Tiện ích không tồn tại.', 'danger');
            $this->redirect('admin/amenity');
        }

        // Kiểm tra ràng buộc: không cho xóa nếu đang có quán dùng tiện ích này
        $placeCount = $this->amenityModel->countPlaces($amenityId);
        if ($placeCount > 0) {
            Session::flash('alert', 'Không thể xóa tiện ích "' . $amenity['name'] . '" vì đang có ' . $placeCount . ' quán sử dụng. Hãy bỏ tiện ích này khỏi các quán trước.', 'warning');
            $this->redirect('admin/amenity');
        }

        $this->amenityModel->delete($amenityId);
        Session::flash('alert', 'Đã xóa tiện ích "' . $amenity['name'] . '" thành công!', 'success');
        $this->redirect('admin/amenity');
    }
}
