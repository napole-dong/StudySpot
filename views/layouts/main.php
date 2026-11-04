<?php
// Layout chính cho khách và thành viên.
// Layout chính: $pageTitle, $content, $flash chỉ hiện 1 lần.
$flash = Session::flash('alert');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tiêu đề tab: có $pageTitle thì ghép StudySpot, không thì câu mặc định. -->
    <!-- Tiêu đề qua e() chống XSS. -->
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . e(SITE_NAME) : e(SITE_NAME) . ' Long Xuyên - Review quán cà phê & không gian học bài ĐH An Giang' ?></title>
    <!-- Google Fonts: Be Vietnam Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CSS riêng: filemtime() làm ?v=... để tải lại CSS mới, chống cache cũ. -->
    <link href="<?= base_url('assets/css/style.css') . '?v=' . filemtime(ROOT_PATH . '/assets/css/style.css') ?>" rel="stylesheet">
</head>
<!-- Thẻ body: data-base-url và data-csrf cho JS gọi AJAX. -->
<body class="d-flex flex-column min-vh-100 bg-light-warm" data-base-url="<?= e(BASE_URL) ?>" data-csrf="<?= e(Session::csrfToken()) ?>">

    <!-- Đầu trang: nhúng header.php dùng chung. -->
    <?php require ROOT_PATH . '/views/layouts/header.php'; ?>

    <!-- Khối flash: chỉ hiện khi $flash không rỗng. -->
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show flash-toast-banner d-flex align-items-center mb-0" role="alert">
                <i class="bi <?= ($flash['type'] === 'danger') ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> fs-5 me-2"></i>
                <div class="fw-medium"><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Nội dung chính: in $content, flex-grow-1 đẩy footer xuống cuối. -->
    <main class="flex-grow-1">
        <?= $content ?? '' ?>
    </main>

    <!-- Cuối trang: nhúng footer.php dùng chung. -->
    <?php require ROOT_PATH . '/views/layouts/footer.php'; ?>

    <!-- JS Bootstrap: dropdown, sidebar mobile, tab, thông báo đóng được. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS riêng: tim yêu thích, widget sao, xem trước ảnh, tự đóng thông báo; ?v=filemtime chống cache. -->
    <script src="<?= base_url('assets/js/main.js') . '?v=' . filemtime(ROOT_PATH . '/assets/js/main.js') ?>"></script>
</body>
</html>
