<?php
/**
 * Layout khu vực Quản trị — dùng ĐÚNG khung header người dùng (1 hàng, sticky),
 * chỉ đổi nhãn nav sang quản trị và nút chuyển thành "Trang người dùng".
 * Logo, nav, tài khoản, footer ở cùng vị trí với trang người dùng.
 */
$adminUser = Session::user();
$flash = Session::flash('alert');
$currentUrl = $_GET['url'] ?? '';

$pendingReviewBadgeCount = 0;
try {
    if (class_exists('Review')) {
        $pendingReviewBadgeCount = (new Review())->countByStatus('pending');
    }
} catch (Throwable $e) {
    $pendingReviewBadgeCount = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | Quản trị ' . e(SITE_NAME) : 'Quản trị | ' . e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') . '?v=' . filemtime(ROOT_PATH . '/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light-warm" data-base-url="<?= e(BASE_URL) ?>" data-csrf="<?= e(Session::csrfToken()) ?>">
    <header class="sticky-top navbar-warm">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none me-2" href="<?= base_url('admin/dashboard') ?>">
                    <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                        <i class="bi bi-cup-hot-fill"></i>
                    </span>
                    <div class="d-flex flex-column brand-title-group">
                        <span class="brand-title">StudySpot</span>
                        <small class="brand-subtitle">LONG XUYÊN • AGU</small>
                    </div>
                </a>

                <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Mở menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse d-none d-lg-flex" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1 flex-lg-nowrap">
                        <li class="nav-item">
                            <a class="nav-link text-nowrap <?= ($currentUrl === 'admin' || str_starts_with($currentUrl, 'admin/dashboard')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-1"></i>Tổng quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap <?= str_starts_with($currentUrl, 'admin/place') ? 'active' : '' ?>" href="<?= base_url('admin/place') ?>">
                                <i class="bi bi-shop me-1"></i>Quán
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap <?= str_starts_with($currentUrl, 'admin/review') ? 'active' : '' ?>" href="<?= base_url('admin/review') ?>">
                                <i class="bi bi-chat-square-quote me-1"></i>Đánh giá<?php if ($pendingReviewBadgeCount > 0): ?> <span class="badge rounded-pill badge-pending-count"><?= $pendingReviewBadgeCount ?></span><?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap <?= str_starts_with($currentUrl, 'admin/user') ? 'active' : '' ?>" href="<?= base_url('admin/user') ?>">
                                <i class="bi bi-people me-1"></i>Người dùng
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-nowrap <?= (str_starts_with($currentUrl, 'admin/category') || str_starts_with($currentUrl, 'admin/amenity')) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tags me-1"></i>Phân loại
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item <?= str_starts_with($currentUrl, 'admin/category') ? 'active' : '' ?>" href="<?= base_url('admin/category') ?>">Danh mục</a></li>
                                <li><a class="dropdown-item <?= str_starts_with($currentUrl, 'admin/amenity') ? 'active' : '' ?>" href="<?= base_url('admin/amenity') ?>">Tiện ích</a></li>
                            </ul>
                        </li>
                    </ul>

                    <div class="d-none d-lg-flex align-items-center gap-2 mt-2 mt-lg-0 flex-nowrap flex-shrink-0 border-start ps-3">
                        <a href="<?= base_url() ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3 text-nowrap flex-shrink-0 header-switch-btn" title="Về trang người dùng">
                            <i class="bi bi-house-door me-1"></i>Trang người dùng
                        </a>
                        <?php if (!empty($adminUser)): ?>
                            <div class="dropdown flex-shrink-0">
                                <a class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border rounded-pill px-2 py-1 bg-white shadow-xs text-nowrap header-account-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= get_image_url($adminUser['avatar'] ?? null, 'avatar') ?>" alt="<?= e($adminUser['name']) ?>" class="rounded-circle object-fit-cover border" width="28" height="28">
                                    <span class="small fw-semibold text-truncate d-inline-block" style="max-width: 90px;"><?= e($adminUser['name']) ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2">
                                    <li class="px-3 py-2 border-bottom mb-1">
                                        <div class="fw-bold small text-dark text-truncate"><?= e($adminUser['name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= e($adminUser['email'] ?? '') ?></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-medium" href="<?= base_url() ?>">
                                            <i class="bi bi-house-door me-2 text-coffee"></i>Trang người dùng
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-medium" href="<?= base_url('profile') ?>">
                                            <i class="bi bi-person-circle me-2 text-coffee"></i>Hồ sơ cá nhân
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 small fw-semibold text-danger" href="<?= base_url('auth/logout') ?>">
                                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </nav>
            <div class="offcanvas offcanvas-end mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">StudySpot <span class="fs-6 fw-normal text-muted">Quản trị</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column gap-3">
                <nav class="d-flex flex-column gap-1">
                    <a class="sidebar-link <?= ($currentUrl === 'admin' || str_starts_with($currentUrl, 'admin/dashboard')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i>Tổng quan</a>
                    <a class="sidebar-link <?= str_starts_with($currentUrl, 'admin/place') ? 'active' : '' ?>" href="<?= base_url('admin/place') ?>"><i class="bi bi-shop"></i>Quán</a>
                    <a class="sidebar-link <?= str_starts_with($currentUrl, 'admin/review') ? 'active' : '' ?>" href="<?= base_url('admin/review') ?>"><i class="bi bi-chat-square-quote"></i>Đánh giá<?php if ($pendingReviewBadgeCount > 0): ?> <span class="badge rounded-pill badge-pending-count"><?= $pendingReviewBadgeCount ?></span><?php endif; ?></a>
                    <a class="sidebar-link <?= str_starts_with($currentUrl, 'admin/user') ? 'active' : '' ?>" href="<?= base_url('admin/user') ?>"><i class="bi bi-people"></i>Người dùng</a>
                    <?php $isTaxonomy = str_starts_with($currentUrl, 'admin/category') || str_starts_with($currentUrl, 'admin/amenity'); ?>
                    <a class="sidebar-link <?= $isTaxonomy ? 'active' : '' ?>" data-bs-toggle="collapse" href="#sidebarTaxonomy" role="button" aria-expanded="<?= $isTaxonomy ? 'true' : 'false' ?>" aria-controls="sidebarTaxonomy"><i class="bi bi-tags"></i>Phân loại<i class="bi bi-chevron-down ms-auto sidebar-chevron"></i></a>
                    <div class="collapse <?= $isTaxonomy ? 'show' : '' ?>" id="sidebarTaxonomy">
                        <a class="sidebar-link ps-4 <?= str_starts_with($currentUrl, 'admin/category') ? 'active' : '' ?>" href="<?= base_url('admin/category') ?>">Danh mục</a>
                        <a class="sidebar-link ps-4 <?= str_starts_with($currentUrl, 'admin/amenity') ? 'active' : '' ?>" href="<?= base_url('admin/amenity') ?>">Tiện ích</a>
                    </div>
                </nav>
                <?php if (!empty($adminUser)): ?>
                <div class="p-3 rounded-4 border bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= get_image_url($adminUser['avatar'] ?? null, 'avatar') ?>" alt="<?= e($adminUser['name']) ?>" class="rounded-circle object-fit-cover border" width="40" height="40">
                        <div class="min-w-0">
                            <div class="fw-bold small text-dark text-truncate"><?= e($adminUser['name']) ?></div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;"><?= e($adminUser['email'] ?? '') ?></div>
                        </div>
                    </div>
                    <a href="<?= base_url() ?>" class="btn btn-coffee btn-sm w-100 py-2 fw-semibold mb-2"><i class="bi bi-house-door me-1"></i>Trang người dùng</a>
                    <a href="<?= base_url('profile') ?>" class="d-block py-2 small fw-medium text-dark text-decoration-none border-top"><i class="bi bi-person-circle me-2 text-coffee"></i>Hồ sơ cá nhân</a>
                    <a href="<?= base_url('auth/logout') ?>" class="d-block py-2 small fw-semibold text-danger text-decoration-none border-top"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
</header>

    <main class="flex-grow-1">
        <div class="container py-4">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show flash-toast-banner d-flex align-items-center mb-4" role="alert">
                    <i class="bi <?= ($flash['type'] === 'danger') ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' ?> fs-5 me-2"></i>
                    <div class="fw-medium"><?= e($flash['message']) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </div>
    </main>

    <?php require ROOT_PATH . '/views/layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') . '?v=' . filemtime(ROOT_PATH . '/assets/js/main.js') ?>"></script>
</body>
</html>
