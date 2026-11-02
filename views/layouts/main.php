<?php
/**
 * Layout chính (main.php) dành cho giao diện Khách & Thành viên
 */
$flash = Session::flash('alert');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . e(SITE_NAME) : e(SITE_NAME) . ' - Review quán cà phê & không gian học bài sinh viên' ?></title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CSS tùy chỉnh của StudySpot -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light-warm" data-base-url="<?= e(BASE_URL) ?>" data-csrf="<?= e(Session::csrfToken()) ?>">

    <?php require ROOT_PATH . '/views/layouts/header.php'; ?>

    <!-- Hiển thị thông báo Flash Message nhất quán -->
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm mb-0" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?= e($flash['message']) ?>
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
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
