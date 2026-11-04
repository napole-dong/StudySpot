<?php
/**
 * Layout chính (main.php) dành cho giao diện Khách & Thành viên StudySpot
 * Phong cách thiết kế: Warm Study Cafe (#FAF7F2 / #5C3D2E / #D97706)
 */
$flash = Session::flash('alert');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . e(SITE_NAME) : e(SITE_NAME) . ' Long Xuyên - Review quán cà phê & không gian học bài ĐH An Giang' ?></title>
    <!-- Google Fonts: Be Vietnam Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CSS tùy chỉnh của StudySpot (Warm Study Cafe Theme) -->
    <link href="<?= base_url('assets/css/style.css') . '?v=' . filemtime(ROOT_PATH . '/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light-warm" data-base-url="<?= e(BASE_URL) ?>" data-csrf="<?= e(Session::csrfToken()) ?>">

    <?php require ROOT_PATH . '/views/layouts/header.php'; ?>

    <!-- Hiển thị thông báo Flash Message mềm mại, tự động đóng sau 4 giây -->
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show flash-toast-banner d-flex align-items-center mb-0" role="alert">
                <i class="bi <?= ($flash['type'] === 'danger') ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> fs-5 me-2"></i>
                <div class="fw-medium"><?= e($flash['message']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Nội dung chính của từng trang -->
    <main class="flex-grow-1">
        <?= $content ?? '' ?>
    </main>

    <?php require ROOT_PATH . '/views/layouts/footer.php'; ?>

    <!-- Bootstrap 5.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS tùy chỉnh của StudySpot -->
    <script src="<?= base_url('assets/js/main.js') . '?v=' . filemtime(ROOT_PATH . '/assets/js/main.js') ?>"></script>
</body>
</html>
