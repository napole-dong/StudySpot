<?php
// Quản trị danh mục quán.
class AdminCategoryController extends Controller
{
    private Category $categoryModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn model Category.
    public function __construct()
    {
        $this->requireAdmin();
        $this->categoryModel = new Category();
    }

    // Hiển thị danh sách danh mục và form thêm/sửa (/admin/category)
    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithPlaceCount();
        $editCategory = null;

        if (!empty($_GET['edit_id'])) {
            $editCategory = $this->categoryModel->findById((int) $_GET['edit_id']);
        }

        $this->render('admin/categories/index', [
            'pageTitle'    => 'Quản lý Danh mục loại hình',
            'categories'   => $categories,
            'editCategory' => $editCategory,
        ], 'admin');
    }

    // Xử lý thêm danh mục mới (/admin/category/store)
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/category');
        }

        $this->verifyCsrfToken();
        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            Session::flash('alert', 'Tên danh mục không được để trống.', 'danger');
            $this->redirect('admin/category');
        }

        if (mb_strlen($name, 'UTF-8') > 100) {
            Session::flash('alert', 'Tên danh mục không được vượt quá 100 ký tự.', 'danger');
            $this->redirect('admin/category');
        }

        // Chặn trùng tên khi thêm.
        if ($this->categoryModel->nameExists($name)) {
            Session::flash('alert', 'Tên danh mục này đã tồn tại trong hệ thống.', 'warning');
            $this->redirect('admin/category');
        }

        $this->categoryModel->create($name);
        Session::flash('alert', 'Đã thêm danh mục "' . $name . '" thành công!', 'success');
        $this->redirect('admin/category');
    }

    // Xử lý cập nhật tên danh mục (/admin/category/update/5)
    public function update(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/category');
        }

        $this->verifyCsrfToken();
        $categoryId = (int) $id;
        $category = $this->categoryModel->findById($categoryId);

        if (!$category) {
            Session::flash('alert', 'Không tìm thấy danh mục cần cập nhật.', 'danger');
            $this->redirect('admin/category');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            Session::flash('alert', 'Tên danh mục không được để trống.', 'danger');
            $this->redirect('admin/category?edit_id=' . $categoryId);
        }

        // Chặn trùng tên khi sửa, trừ chính nó ra.
        if ($this->categoryModel->nameExists($name, $categoryId)) {
            Session::flash('alert', 'Tên danh mục "' . $name . '" đã bị trùng với danh mục khác.', 'warning');
            $this->redirect('admin/category?edit_id=' . $categoryId);
        }

        $this->categoryModel->update($categoryId, $name);
        Session::flash('alert', 'Đã cập nhật tên danh mục thành công!', 'success');
        $this->redirect('admin/category');
    }

    // Xử lý xóa danh mục (/admin/category/delete/5)
    public function delete(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/category');
        }

        $this->verifyCsrfToken();
        $categoryId = (int) $id;
        $category = $this->categoryModel->findById($categoryId);

        if (!$category) {
            Session::flash('alert', 'Danh mục không tồn tại.', 'danger');
            $this->redirect('admin/category');
        }

        // Chặn xóa khi còn quán thuộc danh mục này.
        $placeCount = $this->categoryModel->countPlaces($categoryId);
        if ($placeCount > 0) {
            Session::flash(
                'alert',
                'Không thể xóa danh mục "' . $category['name'] . '" vì đang có ' . $placeCount . ' quán thuộc danh mục này.',
                'warning'
            );
            $this->redirect('admin/category');
        }

        $this->categoryModel->delete($categoryId);
        Session::flash('alert', 'Đã xóa danh mục "' . $category['name'] . '" thành công.', 'success');
        $this->redirect('admin/category');
    }
}
