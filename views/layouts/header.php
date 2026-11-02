<?php
/**
 * Thành phần Header & Navbar dùng chung cho giao diện người dùng StudySpot
 */
$currentUser = Session::user();
$currentUrl = $_GET['url'] ?? '';
?>
<header class="sticky-top bg-white shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light py-2">
        <div class="container">
            <!-- Logo thương hiệu StudySpot -->
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-coffee fs-4" href="<?= base_url() ?>">
                <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                    <i class="bi bi-cup-hot-fill"></i>
                </span>
                <span>StudySpot</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Menu điều hướng chính -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentUrl === '' || $currentUrl === 'home') ? 'active fw-semibold' : '' ?>" href="<?= base_url() ?>">
                            <i class="bi bi-house-door me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($currentUrl, 'place') ? 'active fw-semibold' : '' ?>" href="<?= base_url('place') ?>">
                            <i class="bi bi-compass me-1"></i>Khám phá quán
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('place?has_wifi=1&has_outlet=1') ?>">
                            <i class="bi bi-lightning-charge me-1 text-warning"></i>Chạy Deadline
                        </a>
                    </li>
                    <?php if (Session::isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= str_starts_with($currentUrl, 'favorite') ? 'active fw-semibold' : '' ?>" href="<?= base_url('favorite') ?>">
                                <i class="bi bi-heart me-1 text-danger"></i>Yêu thích
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Ô tìm kiếm nhanh trên thanh điều hướng -->
                <form class="d-flex me-lg-3 my-2 my-lg-0" action="<?= base_url('place') ?>" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control border-end-0" placeholder="Tìm tên quán, đường, quận..." value="<?= e($_GET['q'] ?? '') ?>">
                        <button class="btn btn-outline-secondary border-start-0 bg-white" type="submit" title="Tìm kiếm">
                            <i class="bi bi-search text-coffee"></i>
                        </button>
                    </div>
                </form>

                <!-- Khu vực tài khoản người dùng -->
                <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                    <?php if (Session::isLoggedIn()): ?>
                        <?php if (Session::isAdmin()): ?>
                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-speedometer2 me-1"></i>Quản trị
                            </a>
                        <?php endif; ?>

                        <div class="dropdown">
                            <a class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border rounded-pill px-2 py-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?= get_image_url($currentUser['avatar'] ?? null, 'avatar') ?>" alt="Avatar" class="rounded-circle object-fit-cover" width="28" height="28">
                                <span class="small fw-semibold text-truncate" style="max-width: 130px;"><?= e($currentUser['name'] ?? 'Thành viên') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                                <li>
                                    <a class="dropdown-item py-2" href="<?= base_url('profile') ?>">
                                        <i class="bi bi-person-circle me-2 text-coffee"></i>Trang cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= base_url('favorite') ?>">
                                        <i class="bi bi-heart-fill me-2 text-danger"></i>Quán đã lưu
                                    </a>
                                </li>
                                <?php if (Session::isAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item py-2" href="<?= base_url('admin/dashboard') ?>">
                                            <i class="bi bi-shield-lock me-2 text-primary"></i>Trang Quản trị (Admin)
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="<?= base_url('auth/logout') ?>">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-outline-coffee px-3">Đăng nhập</a>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-sm btn-coffee px-3">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
