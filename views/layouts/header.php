<?php
// Header dùng chung cho trang người dùng, Long Xuyên • AGU.
// Header dùng chung: main.php nhúng, $currentUser, $currentUrl để active menu.
$currentUser = Session::user();
$currentUrl = $_GET['url'] ?? '';
?>
<header class="sticky-top navbar-warm">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container">
            <!-- Logo: bấm về trang chủ. -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none me-2" href="<?= base_url() ?>">
                <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                    <i class="bi bi-cup-hot-fill"></i>
                </span>
                <div class="d-flex flex-column brand-title-group">
                    <span class="brand-title">StudySpot</span>
                    <small class="brand-subtitle">LONG XUYÊN • AGU</small>
                </div>
            </a>

            <!-- Nút hamburger mobile: mở sidebar trượt (offcanvas). -->
            <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse d-none d-lg-flex" id="mainNavbar">
                <!-- Menu desktop: Trang chủ, Khám phá quán, Đã lưu; khớp $currentUrl thì active. -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1 flex-lg-nowrap">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap <?= ($currentUrl === '' || $currentUrl === 'home') ? 'active' : '' ?>" href="<?= base_url() ?>">
                            <i class="bi bi-house-door me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap <?= str_starts_with($currentUrl, 'place') ? 'active' : '' ?>" href="<?= base_url('place') ?>">
                            <i class="bi bi-compass me-1"></i>Khám phá quán
                        </a>
                    </li>
                    <?php if (Session::isLoggedIn()): ?>
                        <li class="nav-item">
                            <!-- Mục Đã lưu chỉ hiện khi đã đăng nhập (Session::isLoggedIn()). -->
                            <a class="nav-link text-nowrap <?= str_starts_with($currentUrl, 'favorite') ? 'active' : '' ?>" href="<?= base_url('favorite') ?>">
                                <i class="bi bi-heart-fill me-1 text-crimson"></i>Đã lưu
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>


                <!-- Cụm tài khoản desktop: mobile dùng thẻ tài khoản riêng ở sidebar. -->
                <div class="d-none d-lg-flex align-items-center gap-2 mt-2 mt-lg-0 flex-nowrap flex-shrink-0">
                    <?php if (Session::isLoggedIn() && !empty($currentUser)): ?>
                        <?php if (Session::isAdmin()): ?>
                            <!-- Nút Trang quản lý chỉ hiện khi là admin (Session::isAdmin()). -->
                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3 text-nowrap flex-shrink-0 header-switch-btn" title="Vào trang quản lí">
                                <i class="bi bi-speedometer2 me-1"></i>Trang quản lí
                            </a>
                        <?php endif; ?>

                        <div class="dropdown flex-shrink-0">
                            <!-- Nút tài khoản: avatar qua get_image_url() (chưa có thì ảnh mặc định), tên qua e() chống XSS. -->
                            <a class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border rounded-pill px-2 py-1 bg-white shadow-xs text-nowrap header-account-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?= get_image_url($currentUser['avatar'] ?? null, 'avatar') ?>" alt="<?= e($currentUser['name']) ?>" class="rounded-circle object-fit-cover border" width="28" height="28">
                                <span class="small fw-semibold text-truncate d-inline-block" style="max-width: 90px;"><?= e($currentUser['name']) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold small text-dark text-truncate"><?= e($currentUser['name']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= e($currentUser['email'] ?? '') ?></div>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-medium" href="<?= base_url('profile') ?>">
                                        <i class="bi bi-person-circle me-2 text-coffee"></i>Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-medium" href="<?= base_url('favorite') ?>">
                                        <i class="bi bi-heart-fill me-2 text-crimson"></i>Quán yêu thích đã lưu
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
                    <?php else: ?>
                        <!-- Chưa đăng nhập: 2 nút Đăng nhập và Đăng ký thay dropdown. -->
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3 text-nowrap">Đăng nhập</a>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-sm btn-coffee rounded-pill px-3 text-nowrap">Đăng ký</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>
        <!-- Menu mobile là sidebar trượt từ phải sang. -->
        <div class="offcanvas offcanvas-end mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">StudySpot <span class="fs-6 fw-normal text-muted">Long Xuyên</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column gap-3">                <nav class="d-flex flex-column gap-1">
                    <a class="sidebar-link <?= ($currentUrl === '' || $currentUrl === 'home') ? 'active' : '' ?>" href="<?= base_url() ?>"><i class="bi bi-house-door"></i>Trang chủ</a>
                    <a class="sidebar-link <?= str_starts_with($currentUrl, 'place') ? 'active' : '' ?>" href="<?= base_url('place') ?>"><i class="bi bi-compass"></i>Khám phá quán</a>
                    <?php if (Session::isLoggedIn()): ?>
                    <a class="sidebar-link <?= str_starts_with($currentUrl, 'favorite') ? 'active' : '' ?>" href="<?= base_url('favorite') ?>"><i class="bi bi-heart-fill text-crimson"></i>Đã lưu</a>
                    <?php endif; ?>
                </nav>
                <?php if (Session::isLoggedIn() && !empty($currentUser)): ?>
                <!-- Thẻ tài khoản mobile: dạng phẳng, hiện luôn avatar, tên, email. -->
                <div class="p-3 rounded-4 border bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= get_image_url($currentUser['avatar'] ?? null, 'avatar') ?>" alt="<?= e($currentUser['name']) ?>" class="rounded-circle object-fit-cover border" width="40" height="40">
                        <div class="min-w-0">
                            <div class="fw-bold small text-dark text-truncate"><?= e($currentUser['name']) ?></div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;"><?= e($currentUser['email'] ?? '') ?></div>
                        </div>
                    </div>
                    <?php if (Session::isAdmin()): ?>
                    <!-- Nút Trang quản lý mobile: nút to full chiều rộng, chỉ admin. -->
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-coffee btn-sm w-100 py-2 fw-semibold mb-2"><i class="bi bi-speedometer2 me-1"></i>Trang quản lí</a>
                    <?php endif; ?>
                    <a href="<?= base_url('profile') ?>" class="d-block py-2 small fw-medium text-dark text-decoration-none border-top"><i class="bi bi-person-circle me-2 text-coffee"></i>Hồ sơ cá nhân</a>
                    <a href="<?= base_url('favorite') ?>" class="d-block py-2 small fw-medium text-dark text-decoration-none border-top"><i class="bi bi-heart-fill me-2 text-crimson"></i>Quán yêu thích đã lưu</a>
                    <a href="<?= base_url('auth/logout') ?>" class="d-block py-2 small fw-semibold text-danger text-decoration-none border-top"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a>
                </div>
                <?php else: ?>
                <!-- Chưa đăng nhập trên mobile: 2 nút Đăng nhập / Đăng ký rộng bằng nhau. -->
                <div class="d-flex gap-2">
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3 py-2 fw-semibold flex-fill text-center">Đăng nhập</a>
                    <a href="<?= base_url('auth/register') ?>" class="btn btn-sm btn-coffee rounded-pill px-3 py-2 fw-semibold flex-fill text-center">Đăng ký</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
</header>
