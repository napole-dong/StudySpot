<?php
// Duyệt đề xuất quán của thành viên: duyệt sẽ tạo quán thật, từ chối thì chỉ chốt trạng thái.
class AdminSuggestionController extends Controller
{
    private PlaceSuggestion $suggestionModel;
    private Place $placeModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn các model.
    public function __construct()
    {
        $this->requireAdmin();
        $this->suggestionModel = new PlaceSuggestion();
        $this->placeModel = new Place();
    }

    // Danh sách đề xuất, đang chờ duyệt xếp trước (/admin/suggestion)
    public function index(): void
    {
        $this->render('admin/suggestions/index', [
            'pageTitle'   => 'Đề xuất quán mới',
            'suggestions' => $this->suggestionModel->getAdminList(),
            'pending'     => $this->suggestionModel->countPending(),
        ], 'admin');
    }

    // Duyệt đề xuất: tạo quán thật đang hiển thị rồi thông báo cho người đề xuất (/admin/suggestion/approve/12)
    public function approve(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/suggestion');
        }

        $this->verifyCsrfToken();
        $suggestion = $this->suggestionModel->findById((int) $id);

        if (!$suggestion || $suggestion['status'] !== 'pending') {
            Session::flash('alert', 'Đề xuất không tồn tại hoặc đã được xử lý.', 'danger');
            $this->redirect('admin/suggestion');
        }

        // Danh mục của đề xuất có thể đã bị xóa: rơi về danh mục đầu tiên còn lại.
        $categoryId = (int) ($suggestion['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $categories = (new Category())->getAll();
            $categoryId = (int) ($categories[0]['id'] ?? 0);
        }
        if ($categoryId <= 0) {
            Session::flash('alert', 'Chưa có danh mục nào để tạo quán.', 'danger');
            $this->redirect('admin/suggestion');
        }

        // Tạo quán với thông tin cốt lõi từ đề xuất; giá và giờ mở cửa dùng mặc định, admin bổ sung sau nếu cần.
        $placeId = $this->placeModel->create([
            'category_id'    => $categoryId,
            'name'           => $suggestion['name'],
            'address'        => $suggestion['address'],
            'district'       => $suggestion['district'],
            'description'    => $suggestion['description'] ?? '',
            'price_min'      => null,
            'price_max'      => null,
            'noise_level'    => 'yen_tinh',
            'open_time'      => '07:00',
            'close_time'     => '22:30',
            'google_map_url' => null,
            'status'         => 'active',
            'created_by'     => (int) $suggestion['user_id'],
            'amenity_ids'    => [],
        ]);

        $this->suggestionModel->markReviewed((int) $id, 'approved', $placeId);

        (new Notification())->create(
            (int) $suggestion['user_id'],
            'suggestion_approved',
            'Đề xuất của bạn đã được duyệt! Quán "' . $suggestion['name'] . '" đã có mặt trên StudySpot.',
            'place/show/' . $placeId
        );

        Session::flash('alert', 'Đã duyệt đề xuất và tạo quán "' . $suggestion['name'] . '" (ID ' . $placeId . ').', 'success');
        $this->redirect('admin/suggestion');
    }

    // Từ chối đề xuất và thông báo cho người đề xuất (/admin/suggestion/reject/12)
    public function reject(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/suggestion');
        }

        $this->verifyCsrfToken();
        $suggestion = $this->suggestionModel->findById((int) $id);

        if (!$suggestion || $suggestion['status'] !== 'pending') {
            Session::flash('alert', 'Đề xuất không tồn tại hoặc đã được xử lý.', 'danger');
            $this->redirect('admin/suggestion');
        }

        $this->suggestionModel->markReviewed((int) $id, 'rejected');

        (new Notification())->create(
            (int) $suggestion['user_id'],
            'suggestion_rejected',
            'Đề xuất quán "' . $suggestion['name'] . '" chưa được duyệt lần này. Cảm ơn bạn đã đóng góp cho StudySpot!',
            'suggestion'
        );

        Session::flash('alert', 'Đã từ chối đề xuất "' . $suggestion['name'] . '".', 'success');
        $this->redirect('admin/suggestion');
    }
}
