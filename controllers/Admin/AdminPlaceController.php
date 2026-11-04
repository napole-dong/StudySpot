<?php
// Quản trị quán: danh sách, thêm, sửa, xóa, ẩn/hiện, ảnh.
class AdminPlaceController extends Controller
{
    private Place $placeModel;
    private Category $categoryModel;
    private PlaceImage $placeImageModel;
    private Amenity $amenityModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn model.
    public function __construct()
    {
        $this->requireAdmin();
        $this->placeModel = new Place();
        $this->categoryModel = new Category();
        $this->placeImageModel = new PlaceImage();
        $this->amenityModel = new Amenity();
    }

    // Lấy danh sách Phường chuẩn tại TP. Long Xuyên kết hợp dữ liệu thực tế từ CSDL
    private function getLongXuyenDistricts(): array
    {
        $standardWards = [
            'Phường Đông Xuyên',
            'Phường Mỹ Xuyên',
            'Phường Mỹ Long',
            'Phường Mỹ Bình',
            'Phường Mỹ Quý',
            'Phường Bình Khánh',
        ];
        $dbDistricts = $this->placeModel->getDistinctDistricts();
        // Gộp phường chuẩn và phường trong CSDL rồi bỏ trùng.
        return array_values(array_unique(array_merge($standardWards, $dbDistricts)));
    }

    // Danh sách quán trong trang Quản trị (/admin/place)
    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $status = trim($_GET['status'] ?? '');
        // PHÂN TRANG ADMIN: số trang ít nhất là 1, tránh số âm hoặc 0.
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->placeModel->getAdminList($keyword, $categoryId, $status, $page, ADMIN_ITEMS_PER_PAGE);
        $categories = $this->categoryModel->getAll();

        $this->render('admin/places/index', [
            'pageTitle'   => 'Quản lý Quán học bài — TP. Long Xuyên',
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

    // Thêm quán mới kèm upload nhiều ảnh và chọn ảnh bìa (/admin/place/create)
    public function create(): void
    {
        $categories = $this->categoryModel->getAll();
        $districts = $this->getLongXuyenDistricts();
        $errors = [];
        $old = [
            'category_id'    => '',
            'name'           => '',
            'address'        => '',
            'district'       => 'Phường Đông Xuyên',
            'description'    => '',
            'price_min'      => '',
            'price_max'      => '',
            'amenity_ids'    => [],
            'noise_level'    => 'yen_tinh',
            'open_time'      => '06:30',
            'close_time'     => '22:00',
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

                // Ảnh bìa khi thêm mới: vị trí đã chọn, mặc định là ảnh đầu tiên.
                $coverIndex = isset($_POST['cover_index']) ? (int) $_POST['cover_index'] : 0;
                // Quán mới được lấy bìa từ đợt upload này.
                $uploadErrors = $this->handleMultipleUploads($placeId, $coverIndex, true);

                if (!empty($uploadErrors)) {
                    Session::flash(
                        'alert',
                        'Đã tạo quán mới, nhưng một số ảnh gặp lỗi: ' . implode('; ', $uploadErrors),
                        'warning'
                    );
                } else {
                    Session::flash('alert', 'Đã thêm quán "' . $old['name'] . '" tại TP. Long Xuyên thành công!', 'success');
                }

                $this->redirect('admin/place');
            }
        }

        $amenities = $this->amenityModel->getAll();
        $this->render('admin/places/create', [
            'pageTitle'  => 'Thêm Quán học bài mới — TP. Long Xuyên',
            'categories' => $categories,
            'districts'  => $districts,
            'amenities'  => $amenities,
            'errors'     => $errors,
            'old'        => $old,
        ], 'admin');
    }

    // Chỉnh sửa thông tin quán, quản lý danh sách ảnh & chọn ảnh bìa (/admin/place/edit/3)
    public function edit(int|string $id = 0): void
    {
        $placeId = (int) $id;
        $place = $this->placeModel->findById($placeId);

        if (!$place) {
            Session::flash('alert', 'Không tìm thấy quán cần chỉnh sửa.', 'danger');
            $this->redirect('admin/place');
        }

        $categories = $this->categoryModel->getAll();
        $districts = $this->getLongXuyenDistricts();
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
                // Sửa quán: chỉ lấy bìa từ đợt upload mới khi trước đó chưa có ảnh.
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

        $amenities = $this->amenityModel->getAll();
        $this->render('admin/places/edit', [
            'pageTitle'  => 'Chỉnh sửa Quán: ' . $place['name'],
            'place'      => $place,
            'categories' => $categories,
            'districts'  => $districts,
            'amenities'  => $amenities,
            'images'     => $images,
            'errors'     => $errors,
            'old'        => $old,
        ], 'admin');
    }

    // Đặt nhanh 1 ảnh làm ảnh bìa (/admin/place/setCover/3/10)
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

    // Xóa 1 ảnh của quán (/admin/place/deleteImage/3/10)
    public function deleteImage(int|string $placeId = 0, int|string $imageId = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/place');
        }

        $this->verifyCsrfToken();
        $pId = (int) $placeId;
        $imgId = (int) $imageId;

        $image = $this->placeImageModel->findById($imgId);
        // Ảnh phải tồn tại và thuộc đúng quán trên URL mới được xóa.
        if (!$image || (int) $image['place_id'] !== $pId) {
            Session::flash('alert', 'Hình ảnh không tồn tại hoặc không thuộc quán này.', 'danger');
            $this->redirect('admin/place/edit/' . $pId);
        }

        $this->placeImageModel->delete($imgId);
        Session::flash('alert', 'Đã xóa hình ảnh thành công.', 'info');
        $this->redirect('admin/place/edit/' . $pId);
    }

    // Ẩn / Hiện nhanh quán trên danh sách (/admin/place/toggleStatus/3)
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

        // Đảo trạng thái sang giá trị còn lại.
        $newStatus = ($place['status'] === 'active') ? 'hidden' : 'active';
        $this->placeModel->updateStatus($placeId, $newStatus);

        $statusLabel = ($newStatus === 'active') ? 'Hiển thị công khai' : 'Tạm ẩn';
        Session::flash('alert', 'Đã chuyển trạng thái quán "' . $place['name'] . '" sang: ' . $statusLabel . '.', 'success');
        $this->redirect('admin/place');
    }

    // Xóa hoàn toàn một quán (/admin/place/delete/3)
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

        // Xóa file ảnh trên ổ đĩa trước khi xóa bản ghi CSDL.
        $this->placeImageModel->deleteAllFilesByPlaceId($placeId);
        $this->placeModel->delete($placeId);

        Session::flash('alert', 'Đã xóa quán "' . $place['name'] . '" và toàn bộ dữ liệu liên quan.', 'success');
        $this->redirect('admin/place');
    }

    // Thu thập và chuẩn hóa dữ liệu từ form POST
    private function collectInput(): array
    {
        return [
            'category_id'    => (int) ($_POST['category_id'] ?? 0),
            'name'           => trim($_POST['name'] ?? ''),
            'address'        => trim($_POST['address'] ?? ''),
            'district'       => trim($_POST['district'] ?? ''),
            'description'    => trim($_POST['description'] ?? ''),
            // Giá trống thì lưu null, giá âm thì về 0.
            'price_min'      => trim($_POST['price_min'] ?? '') === '' ? null : max(0, (int) $_POST['price_min']),
            'price_max'      => trim($_POST['price_max'] ?? '') === '' ? null : max(0, (int) $_POST['price_max']),
            'amenity_ids'    => array_values(array_unique(array_map('intval', is_array($_POST['amenity_ids'] ?? []) ? $_POST['amenity_ids'] : []))),
            'noise_level'    => trim($_POST['noise_level'] ?? 'yen_tinh'),
            'open_time'      => trim($_POST['open_time'] ?? '06:30'),
            'close_time'     => trim($_POST['close_time'] ?? '22:00'),
            'google_map_url' => trim($_POST['google_map_url'] ?? ''),
            // Trạng thái chỉ nhận active hoặc hidden, giá trị lạ đều về active.
            'status'         => in_array($_POST['status'] ?? '', ['active', 'hidden'], true) ? $_POST['status'] : 'active',
        ];
    }

    // Kiểm tra tính hợp lệ của dữ liệu quán phía Server
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
            $errors['address'] = 'Vui lòng nhập địa chỉ chi tiết của quán tại TP. Long Xuyên.';
        }

        if ($data['district'] === '') {
            $errors['district'] = 'Vui lòng nhập hoặc chọn Phường / Khu vực tại TP. Long Xuyên.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'Vui lòng nhập mô tả không gian học tập của quán.';
        }

        // Khoảng giá hợp lệ: giá cao nhất không nhỏ hơn giá thấp nhất.
        if (($data['price_min'] ?? null) !== null && ($data['price_max'] ?? null) !== null && (int) $data['price_max'] < (int) $data['price_min']) {
            $errors['price_max'] = 'Giá cao nhất phải lớn hơn hoặc bằng giá thấp nhất.';
        }

        if (!in_array($data['noise_level'], ['yen_tinh', 'vua', 'on'], true)) {
            $errors['noise_level'] = 'Mức độ tiếng ồn không hợp lệ.';
        }

        if ($data['open_time'] === '' || $data['close_time'] === '') {
            $errors['time'] = 'Vui lòng nhập đầy đủ giờ mở cửa và giờ đóng cửa.';
        }

        return $errors;
    }

    // Xử lý upload nhiều ảnh từ input name="images[]"
    private function handleMultipleUploads(int $placeId, int $coverIndex = 0, bool $setCoverFromBatch = false): array
    {
        $uploadErrors = [];

        if (empty($_FILES['images']) || !is_array($_FILES['images']['name'])) {
            return $uploadErrors;
        }

        $fileCount = count($_FILES['images']['name']);
        $uploadedIndex = 0;

        for ($i = 0; $i < $fileCount; $i++) {
            // Bỏ qua ô file trống, không tính là lỗi upload.
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
                // Chỉ 1 ảnh làm bìa: ảnh thành công thứ coverIndex trong đợt này.
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
