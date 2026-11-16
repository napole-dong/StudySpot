<?php
// Thông báo trong web của thành viên: mở một thông báo, đánh dấu đã đọc hết.
class NotificationController extends Controller
{
    private Notification $notificationModel;

    // Tạo sẵn model Notification.
    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    // Mở một thông báo (/notification/open/5): đánh dấu đã đọc rồi chuyển tới trang đích của thông báo.
    public function open(int|string $id = 0): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }

        $this->verifyCsrfToken();

        $user = Session::user();
        $notif = $this->notificationModel->findByIdForUser((int) $id, (int) $user['id']);
        if (!$notif) {
            $this->redirect('');
        }

        $this->notificationModel->markRead((int) $id, (int) $user['id']);
        $this->redirect($notif['link'] ?? '');
    }

    // Đánh dấu tất cả đã đọc (/notification/mark-all-read) rồi quay lại trang đang đứng.
    public function markAllRead(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }

        $this->verifyCsrfToken();

        $user = Session::user();
        $this->notificationModel->markAllRead((int) $user['id']);

        // HTTP_REFERER là URL đầy đủ: cắt bỏ BASE_URL ở đầu để redirect() chạy theo path nội bộ.
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $path = '';
        if ($referer !== '' && str_starts_with($referer, BASE_URL)) {
            $path = trim(substr($referer, strlen(BASE_URL)), '/');
        }
        $this->redirect($path);
    }
}
