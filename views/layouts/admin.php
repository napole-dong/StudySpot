<?php
/**
 * Layout dành riêng cho khu vực Quản trị viên (admin.php)
 */
$adminUser = Session::user();
$flash = Session::flash('alert');
$currentUrl = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | Quản trị ' . e(SITE_NAME) : 'Quản trị hệ thống | ' . e(SITE_NAME) ?></title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CSS tùy chỉnh của StudySpot -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-light" data-base-url="<?= e(BASE_URL) ?>" data-csrf="<?= e(Session::csrfToken()) ?>">

    <!-- Thanh điều hướng trên cùng cho mobile & desktop -->
    <nav class="navbar navbar-dark bg-dark sticky-top shadow-sm px-3">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light btn-sm d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 mb-0" href="<?= base_url('admin/dashboard') ?>">
                <i class="bi bi-cup-hot-fill text-warning"></i>
                <span>StudySpot <span class="badge bg-warning text-dark ms-1">Admin</span></span>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url() ?>" class="btn btn-sm btn-outline-light" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>Xem trang chủ
            </a>
            <div class="text-light small d-none d-sm-block">
                Xin chào, <strong><?= e($adminUser['name'] ?? 'Admin') ?></strong>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-right me-1"></i>Thoát
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Quản trị -->
            <div class="col-lg-2 px-0 bg-white border-end min-vh-100 offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar">
                <div class="offcanvas-header border-bottom d-lg-none">
                    <h5 class="offcanvas-title fw-bold">Menu Quản trị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column p-3">
                    <div class="text-uppercase text-muted small fw-bold mb-2 px-2">Bảng điều khiển</div>
                    <ul class="nav nav-pills flex-column gap-1 mb-auto">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= ($currentUrl === 'admin' || str_starts_with($currentUrl, 'admin/dashboard')) ? 'active bg-coffee' : 'text-dark' ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Tổng quan (Dashboard)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/place') ?>" class="nav-link <?= str_starts_with($currentUrl, 'admin/place') ? 'active bg-coffee' : 'text-dark' ?>">
                                <i class="bi bi-shop me-2"></i>Quản lý Quán học
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/category') ?>" class="nav-link <?= str_starts_with($currentUrl, 'admin/category') ? 'active bg-coffee' : 'text-dark' ?>">
                                <i class="bi bi-tags me-2"></i>Quản lý Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/review') ?>" class="nav-link <?= str_starts_with($currentUrl, 'admin/review') ? 'active bg-coffee' : 'text-dark' ?>">
                                <i class="bi bi-chat-square-quote me-2"></i>Kiểm duyệt Review
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/user') ?>" class="nav-link <?= str_starts_with($currentUrl, 'admin/user') ? 'active bg-coffee' : 'text-dark' ?>">
                                <i class="bi bi-people me-2"></i>Quản lý Người dùng
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Nội dung trang Quản trị -->
            <div class="col-lg-10 p-4">
                <?php if (!empty($flash)): ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i><?= e($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
