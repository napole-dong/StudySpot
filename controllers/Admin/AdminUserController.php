<?php
// Quản trị người dùng: danh sách, khóa/mở khóa.
class AdminUserController extends Controller
{
    private User $userModel;

    // Kiểm tra quyền admin (requireAdmin) trước rồi tạo sẵn model User.
    public function __construct()
    {
        $this->requireAdmin();
        $this->userModel = new User();
    }

    // Danh sách người dùng trong hệ thống (/admin/user)
    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->userModel->getAdminList($keyword, $status, $page, ADMIN_ITEMS_PER_PAGE);

        $this->render('admin/users/index', [
            'pageTitle'   => 'Quản lý Người dùng',
            'users'       => $result['items'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'filters'     => [
                'q'      => $keyword,
                'status' => $status,
            ],
        ], 'admin');
    }

    // Khóa hoặc Mở khóa tài khoản thành viên (/admin/user/toggleStatus/6)
    public function toggleStatus(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/user');
        }

        $this->verifyCsrfToken();

        $targetUserId = (int) $id;
        $targetUser = $this->userModel->findById($targetUserId);

        if (!$targetUser) {
            Session::flash('alert', 'Không tìm thấy tài khoản người dùng.', 'danger');
            $this->redirect('admin/user');
        }

        $currentAdmin = Session::user();
        $currentAdminId = (int) ($currentAdmin['id'] ?? 0);

        // Chặn tự khóa mình.
        if ($targetUserId === $currentAdminId) {
            Session::flash('alert', 'Không thể tự khóa tài khoản của chính bạn!', 'warning');
            $this->redirect('admin/user');
        }

        // Chặn khóa tài khoản admin khác, chỉ khóa được thành viên.
        if ($targetUser['role'] === 'admin') {
            Session::flash('alert', 'Không được phép khóa tài khoản Quản trị viên khác!', 'warning');
            $this->redirect('admin/user');
        }

        // Đảo trạng thái sang giá trị còn lại.
        $newStatus = ($targetUser['status'] === 'active') ? 'locked' : 'active';
        $this->userModel->updateStatus($targetUserId, $newStatus);

        $actionText = ($newStatus === 'locked') ? 'Đã khóa' : 'Đã mở khóa';
        Session::flash(
            'alert',
            $actionText . ' tài khoản "' . $targetUser['name'] . '" (' . $targetUser['email'] . ') thành công.',
            $newStatus === 'locked' ? 'warning' : 'success'
        );

        $this->redirect('admin/user');
    }
}
