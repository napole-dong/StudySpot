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

                <!-- Đăng nhập Google: chỉ hiện khi đã cấu hình, chỉ chấp nhận email @agu.edu.vn. -->
                <?php if (!empty($googleLoginEnabled)): ?>
                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1">
                        <span class="px-2 small text-muted">hoặc đăng nhập bằng</span>
                        <hr class="flex-grow-1">
                    </div>
                    <a href="<?= base_url('auth/google') ?>" class="btn btn-light border rounded-pill w-100 py-2 fw-semibold mb-2 d-flex align-items-center justify-content-center gap-2">
                        <!-- Logo Google: SVG 4 màu, tài khoản mới được tạo tự động. -->
                        <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303C33.654,32.657,29.223,36,24,36c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                        Đăng nhập với Google
                    </a>
                    <p class="text-center small text-muted mb-3">Chỉ tài khoản email @<?= e(GOOGLE_ALLOWED_DOMAIN) ?></p>
                <?php endif; ?>

                <div class="text-center small text-muted">
                    Chưa có tài khoản thành viên?
                    <!-- Link dưới form: chưa có tài khoản thì sang trang đăng ký. -->
                    <a href="<?= base_url('auth/register') ?>" class="text-coffee fw-bold text-decoration-none">Đăng ký miễn phí</a>
                </div>

            </div>
        </div>
    </div>
</div>
