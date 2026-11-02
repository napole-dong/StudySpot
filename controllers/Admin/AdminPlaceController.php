<?php
/**
 * AdminPlaceController: Quản lý Quán học bài dành cho Admin
 * Bao gồm: Danh sách (tìm kiếm, lọc, phân trang), Thêm mới, Sửa, Xóa, Ẩn/Hiện, Upload nhiều ảnh & Chọn ảnh bìa
 */
class AdminPlaceController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private PlaceImage $placeImageModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->placeImageModel = new PlaceImage();
    }

    /**
     * Danh sách quán trong trang Quản trị (/admin/place)
     */
    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $status = trim($_GET['status'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->placeModel->getAdminList($keyword, $categoryId, $status, $page, ADMIN_ITEMS_PER_PAGE);
        $categories = $this->categoryModel->getAll();

        $this->render('admin/places/index', [
            'pageTitle'   => 'Quản lý Quán học bài',
            'places'      => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'categories'  => $categories,
            'filters'     => [
                'q'           => $keyword,
                'category_id' => $categoryId,
                'status'      => $status,
            ],
        ], 'admin');
    }

    /**
     * Thêm quán mới kèm upload nhiều ảnh và chọn ảnh bìa (/admin/place/create)
     */
    public function create(): void
    {
        $categories = $this->categoryModel->getAll();
        $errors = [];
        $old = [
            'category_id'    => '',
            'name'           => '',
            'address'        => '',
            'district'       => 'Quận Bình Thạnh',
            'description'    => '',
            'price_range'    => 2,
            'has_wifi'       => 1,
            'has_outlet'     => 1,
            'has_ac'         => 1,
            'noise_level'    => 'yen_tinh',
            'open_time'      => '07:00',
            'close_time'     => '22:30',
            'google_map_url' => '',
            'status'         => 'active',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();

            $old = $this->collectInput();
            $errors = $this->validateInput($old);

            if (empty($errors)) {
                $currentUser = Session::user();
                $old['created_by'] = $currentUser['id'] ?? null;

                $placeId = $this->placeModel->create($old);

                // Xử lý upload nhiều ảnh nếu Admin có chọn file
                $coverIndex = isset($_POST['cover_index']) ? (int) $_POST['cover_index'] : 0;
                $uploadErrors = $this->handleMultipleUploads($placeId, $coverIndex, true);

                if (!empty($uploadErrors)) {
                    Session::flash(
                        'alert',
                        'Đã tạo quán mới, nhưng một số ảnh gặp lỗi: ' . implode('; ', $uploadErrors),
                        'warning'
                    );
                } else {
                    Session::flash('alert', 'Đã thêm quán "' . $old['name'] . '" thành công!', 'success');
                }

                $this->redirect('admin/place');
            }
        }

        $this->render('admin/places/create', [
            'pageTitle'  => 'Thêm Quán học bài mới',
            'categories' => $categories,
            'errors'     => $errors,
            'old'        => $old,
        ], 'admin');
    }

    /**
     * Chỉnh sửa thông tin quán, quản lý danh sách ảnh & chọn ảnh bìa (/admin/place/edit/3)
     */
    public function edit(int|string $id = 0): void
    {
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId);

        if (!$place) {
            Session::flash('alert', 'Không tìm thấy quán cần chỉnh sửa.', 'danger');
            $this->redirect('admin/place');
        }

        $categories = $this->categoryModel->getAll();
        $images = $this->placeImageModel->getByPlaceId($placeId);
        $errors = [];
        $old = $place;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();

            $old = $this->collectInput();
            $errors = $this->validateInput($old);

            if (empty($errors)) {
                $this->placeModel->update($placeId, $old);

                // Nếu Admin chọn đổi ảnh bìa trong số các ảnh hiện có
                if (!empty($_POST['existing_cover_id'])) {
                    $this->placeImageModel->setCover($placeId, (int) $_POST['existing_cover_id']);
                }

                // Nếu Admin upload thêm ảnh mới
                $hasExistingImages = !empty($images);
                $uploadErrors = $this->handleMultipleUploads($placeId, 0, !$hasExistingImages);

                // Đảm bảo luôn có ít nhất 1 ảnh bìa
                $this->placeImageModel->ensureCoverExists($placeId);

                if (!empty($uploadErrors)) {
                    Session::flash(
                        'alert',
                        'Đã cập nhật thông tin quán, nhưng một số ảnh tải lên bị lỗi: ' . implode('; ', $uploadErrors),
                        'warning'
                    );
                } else {
                    Session::flash('alert', 'Đã cập nhật thông tin quán "' . $old['name'] . '" thành công!', 'success');
                }

                $this->redirect('admin/place/edit/' . $placeId);
            }
        }

        $this->render('admin/places/edit', [
            'pageTitle'  => 'Chỉnh sửa Quán: ' . $place['name'],
            'place'      => $place,
            'categories' => $categories,
            'images'     => $images,
            'errors'     => $errors,
            'old'        => $old,
        ], 'admin');
    }

    /**
     * Đặt nhanh 1 ảnh làm ảnh bìa (/admin/place/setCover/3/10)
     */
    public function setCover(int|string $placeId = 0, int|string $imageId = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/place');
        }

        $this->verifyCsrfToken();
        $pId = (int) $placeId;
        $imgId = (int) $imageId;

        if ($this->placeImageModel->setCover($pId, $imgId)) {
            Session::flash('alert', 'Đã cập nhật ảnh bìa của quán thành công.', 'success');
        } else {
            Session::flash('alert', 'Không thể đặt ảnh bìa được chọn.', 'danger');
        }

        $this->redirect('admin/place/edit/' . $pId);
    }

    /**
     * Xóa 1 ảnh của quán (/admin/place/deleteImage/3/10)
     */
    public function deleteImage(int|string $placeId = 0, int|string $imageId = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/place');
        }

        $this->verifyCsrfToken();
        $pId = (int) $placeId;
        $imgId = (int) $imageId;

        $image = $this->placeImageModel->findById($imgId);
        if (!$image || (int) $image['place_id'] !== $pId) {
            Session::flash('alert', 'Hình ảnh không tồn tại hoặc không thuộc quán này.', 'danger');
            $this->redirect('admin/place/edit/' . $pId);
        }

        $this->placeImageModel->delete($imgId);
        Session::flash('alert', 'Đã xóa hình ảnh thành công.', 'info');
        $this->redirect('admin/place/edit/' . $pId);
    }

    /**
     * Ẩn / Hiện nhanh quán trên danh sách (/admin/place/toggleStatus/3)
     */
    public function toggleStatus(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/place');
        }

        $this->verifyCsrfToken();
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId);

        if (!$place) {
            Session::flash('alert', 'Không tìm thấy quán yêu cầu.', 'danger');
            $this->redirect('admin/place');
        }

        $newStatus = ($place['status'] === 'active') ? 'hidden' : 'active';
        $this->placeModel->updateStatus($placeId, $newStatus);

        $statusLabel = ($newStatus === 'active') ? 'Hiển thị công khai' : 'Tạm ẩn';
        Session::flash('alert', 'Đã chuyển trạng thái quán "' . $place['name'] . '" sang: ' . $statusLabel . '.', 'success');
        $this->redirect('admin/place');
    }

    /**
     * Xóa hoàn toàn một quán (/admin/place/delete/3)
     */
    public function delete(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/place');
        }

        $this->verifyCsrfToken();
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId);

        if (!$place) {
            Session::flash('alert', 'Không tìm thấy quán cần xóa.', 'danger');
            $this->redirect('admin/place');
        }

        // Xóa các file ảnh vật lý trên ổ đĩa trước khi xóa bản ghi CSDL
        $this->placeImageModel->deleteAllFilesByPlaceId($placeId);
        $this->placeModel->delete($placeId);

        Session::flash('alert', 'Đã xóa quán "' . $place['name'] . '" và toàn bộ dữ liệu liên quan.', 'success');
        $this->redirect('admin/place');
    }

    /**
     * Thu thập và chuẩn hóa dữ liệu từ form POST
     */
    private function collectInput(): array
    {
        return [
            'category_id'    => (int) ($_POST['category_id'] ?? 0),
            'name'           => trim($_POST['name'] ?? ''),
            'address'        => trim($_POST['address'] ?? ''),
            'district'       => trim($_POST['district'] ?? ''),
            'description'    => trim($_POST['description'] ?? ''),
            'price_range'    => (int) ($_POST['price_range'] ?? 2),
            'has_wifi'       => isset($_POST['has_wifi']) ? 1 : 0,
            'has_outlet'     => isset($_POST['has_outlet']) ? 1 : 0,
            'has_ac'         => isset($_POST['has_ac']) ? 1 : 0,
            'noise_level'    => trim($_POST['noise_level'] ?? 'yen_tinh'),
            'open_time'      => trim($_POST['open_time'] ?? '07:00'),
            'close_time'     => trim($_POST['close_time'] ?? '22:30'),
            'google_map_url' => trim($_POST['google_map_url'] ?? ''),
            'status'         => in_array($_POST['status'] ?? '', ['active', 'hidden'], true) ? $_POST['status'] : 'active',
        ];
    }

    /**
     * Kiểm tra tính hợp lệ của dữ liệu quán phía Server
     */
    private function validateInput(array $data): array
    {
        $errors = [];

        if ($data['category_id'] <= 0 || !$this->categoryModel->findById($data['category_id'])) {
            $errors['category_id'] = 'Vui lòng chọn danh mục loại hình hợp lệ.';
        }

        if ($data['name'] === '') {
            $errors['name'] = 'Vui lòng nhập tên quán.';
        } elseif (mb_strlen($data['name'], 'UTF-8') < 3 || mb_strlen($data['name'], 'UTF-8') > 180) {
            $errors['name'] = 'Tên quán phải từ 3 đến 180 ký tự.';
        }

        if ($data['address'] === '') {
            $errors['address'] = 'Vui lòng nhập địa chỉ chi tiết của quán.';
        }

        if ($data['district'] === '') {
            $errors['district'] = 'Vui lòng nhập hoặc chọn Quận / Khu vực.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'Vui lòng nhập mô tả không gian học tập của quán.';
        }

        if (!in_array($data['price_range'], [1, 2, 3, 4], true)) {
            $errors['price_range'] = 'Mức giá không hợp lệ.';
        }

        if (!in_array($data['noise_level'], ['yen_tinh', 'vua', 'on'], true)) {
            $errors['noise_level'] = 'Mức độ tiếng ồn không hợp lệ.';
        }

        if ($data['open_time'] === '' || $data['close_time'] === '') {
            $errors['time'] = 'Vui lòng nhập đầy đủ giờ mở cửa và giờ đóng cửa.';
        }

        return $errors;
    }

    /**
     * Xử lý upload nhiều ảnh từ input name="images[]"
     */
    private function handleMultipleUploads(int $placeId, int $coverIndex = 0, bool $setCoverFromBatch = false): array
    {
        $uploadErrors = [];

        if (empty($_FILES['images']) || !is_array($_FILES['images']['name'])) {
            return $uploadErrors;
        }

        $fileCount = count($_FILES['images']['name']);
        $uploadedIndex = 0;

        for ($i = 0; $i < $fileCount; $i++) {
            if (($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $singleFile = [
                'name'     => $_FILES['images']['name'][$i],
                'type'     => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];

            $result = upload_image($singleFile, 'places');
            if ($result['success'] && !empty($result['path'])) {
                $isCover = ($setCoverFromBatch && $uploadedIndex === $coverIndex) ? 1 : 0;
                $this->placeImageModel->create($placeId, $result['path'], $isCover);
                $uploadedIndex++;
            } else {
                $uploadErrors[] = $singleFile['name'] . ': ' . ($result['error'] ?? 'Lỗi không xác định');
            }
        }

        $this->placeImageModel->ensureCoverExists($placeId);
        return $uploadErrors;
    }
}
