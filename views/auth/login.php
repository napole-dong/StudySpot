<?php
// Trang Đăng nhập, do AuthController (login) render.
// Trang Đăng nhập: $errors, $oldEmail; chung form cho thành viên và quản trị.
?>
<div class="container py-4">
    <!-- Bố cục chia đôi: panel ảnh trái, form phải; login/register cao bằng nhau. -->
    <div class="auth-split">
            <!-- Panel ảnh trái: ảnh cafe_4.jpg chỉ trang trí, không phải ảnh 1 quán. -->
            <div class="auth-panel auth-panel-image">
                    <img src="<?= base_url('assets/img/places/cafe_4.jpg') ?>" alt="Không gian quán cà phê học bài">
                    <div class="auth-panel-brand">
                        <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white"><i class="bi bi-cup-hot-fill"></i></span>
                        <span>StudySpot <span class="fw-normal opacity-75">Long Xuyên</span></span>
                    </div>
                </div>
        <div class="auth-form">
                <div class="mb-4">
                    <h1 class="page-title mb-1">Đăng nhập</h1>
                    <p class="text-muted small mb-0">Nhập email và mật khẩu để tiếp tục.</p>
                </div>
                <!-- Lỗi chung: chỉ hiện khi $errors['general'] không rỗng, đặt trên cùng form. -->
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger rounded-3 d-flex align-items-center py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= e($errors['general']) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Form đăng nhập: POST tới auth/login, novalidate, csrf_field() chống CSRF. -->
                <form action="<?= base_url('auth/login') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <!-- Trường email: giữ $oldEmail khi nhập sai, e() chống XSS, autofocus. -->
                        <label for="email" class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-coffee"></i></span>
                            <input
                                type="email"
                                class="form-control border-start-0 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email"
                                name="email"
                                placeholder="sinhvien@student.edu.vn"
                                value="<?= e($oldEmail ?? '') ?>"
                                required
                                autofocus
                            >
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <!-- Trường mật khẩu: type password, không giữ giá trị cũ khi nhập sai. -->
                        <label for="password" class="form-label fw-semibold small">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-coffee"></i></span>
                            <input
                                type="password"
                                class="form-control border-start-0 <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                id="password"
                                name="password"
                                placeholder="Nhập mật khẩu của bạn"
                                required
                            >
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-coffee rounded-pill w-100 py-2 fw-semibold mb-3">
                        <!-- Nút gửi form: gửi email + mật khẩu lên server để kiểm tra. -->
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập hệ thống
                    </button>
                </form>

                <div class="text-center small text-muted">
                    Chưa có tài khoản thành viên?
                    <!-- Link dưới form: chưa có tài khoản thì sang trang đăng ký. -->
                    <a href="<?= base_url('auth/register') ?>" class="text-coffee fw-bold text-decoration-none">Đăng ký miễn phí</a>
                </div>

            </div>
        </div>
    </div>
</div>
