<?php
// Chuông thông báo: dùng chung cho header desktop và mobile.
// Dữ liệu $headerNotifications và $headerUnreadCount do Controller::render() nạp sẵn cho layout main.
$notifItems = $headerNotifications ?? [];
$notifUnread = $headerUnreadCount ?? 0;
?>
<div class="dropdown flex-shrink-0">
    <!-- Nút chuông: có thông báo chưa đọc thì hiện huy hiệu đỏ đếm số lượng ở góc. -->
    <button class="btn btn-light btn-sm border rounded-circle bg-white shadow-xs notif-bell-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Thông báo">
        <i class="bi bi-bell"></i>
        <?php if ($notifUnread > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge"><?= $notifUnread > 9 ? '9+' : $notifUnread ?></span>
        <?php endif; ?>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2 notif-menu">
        <div class="d-flex align-items-center justify-content-between px-2 pt-1 pb-2 border-bottom mb-1">
            <span class="fw-bold small">Thông báo</span>
            <?php if ($notifUnread > 0): ?>
                <!-- Nút đánh dấu đã đọc hết: gởi POST về NotificationController. -->
                <form method="post" action="<?= base_url('notification/mark-all-read') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-link btn-sm p-0 small text-decoration-none">Đánh dấu đã đọc hết</button>
                </form>
            <?php endif; ?>
        </div>
        <?php if (empty($notifItems)): ?>
            <div class="px-2 py-3 small text-muted text-center">Chưa có thông báo nào.</div>
        <?php else: ?>
            <?php foreach ($notifItems as $notif): ?>
                <!-- Mỗi thông báo là một form POST nhỏ: bấm vào là đánh dấu đã đọc rồi đi tới trang đích. -->
                <form method="post" action="<?= base_url('notification/open/' . (int) $notif['id']) ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="dropdown-item rounded-3 px-2 py-2 small text-start notif-item <?= empty($notif['is_read']) ? 'notif-unread' : '' ?>">
                        <span class="d-block"><?= e($notif['content']) ?></span>
                        <span class="d-block text-muted notif-time"><?= e(time_ago($notif['created_at'] ?? null)) ?></span>
                    </button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
