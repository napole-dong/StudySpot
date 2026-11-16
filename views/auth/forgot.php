<?php
// Trang Quên mật khẩu, do AuthController (forgotPassword) render.
// Trang Quên mật khẩu: $errors, $oldEmail; nhập email để nhận link đặt lại mật khẩu.
?>
<div class="container py-4">
    <!-- Bố cục chia đôi: panel ảnh trái, form phải; dùng chung hệ auth-split với login/register. -->
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
                    <h1 class="page-title mb-1">Quên mật khẩu</h1>
                    <p class="text-muted small mb-0">Nhập email đã đăng ký, StudySpot sẽ gởi link đặt lại mật khẩu tới email của bạn.</p>
                </div>
                <!-- Lỗi chung: chỉ hiện khi $errors['general'] không rỗng, đặt trên cùng form. -->
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger rounded-3 d-flex align-items-center py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= e($errors['general']) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Form quên mật khẩu: POST tới auth/forgot-password, novalidate, csrf_field() chống CSRF. -->
                <form action="<?= base_url('auth/forgot-password') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <!-- Trường email: giữ $oldEmail khi nhập sai, e() chống XSS, autofocus. -->
                        <label for="email" class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-coffee"></i></span>
                            <input
                                type="email"
                                class="form-control border-start-0 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email"
                                name="email"
                                placeholder="Nhập email đã đăng ký"
                                value="<?= e($oldEmail ?? '') ?>"
                                required
                                autofocus
                            >
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-coffee rounded-pill w-100 py-2 fw-semibold mb-3">
                        <!-- Nút gửi form: gởi email chứa link đặt lại mật khẩu. -->
                        <i class="bi bi-send me-1"></i>Gởi link đặt lại mật khẩu
                    </button>
                </form>

                <div class="text-center small text-muted">
                    Nhớ ra mật khẩu rồi?
                    <!-- Link dưới form: quay lại trang đăng nhập. -->
                    <a href="<?= base_url('auth/login') ?>" class="text-coffee fw-bold text-decoration-none">Quay lại Đăng nhập</a>
                </div>

            </div>
        </div>
    </div>
</div>
