<?php
// Trang Đặt lại mật khẩu, do AuthController (resetPassword) render.
// Trang Đặt lại mật khẩu: $errors, $token; token nằm trong link ở email, hết hạn sau 60 phút.
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
                    <h1 class="page-title mb-1">Đặt lại mật khẩu</h1>
                    <p class="text-muted small mb-0">Nhập mật khẩu mới cho tài khoản của bạn.</p>
                </div>
                <!-- Lỗi chung: chỉ hiện khi $errors['general'] không rỗng, đặt trên cùng form. -->
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger rounded-3 d-flex align-items-center py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= e($errors['general']) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Form đặt lại mật khẩu: POST tới auth/reset-password, token gửi kèm trong ô ẩn, csrf_field() chống CSRF. -->
                <form action="<?= base_url('auth/reset-password') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

                    <div class="mb-3">
                        <!-- Trường mật khẩu mới: type password, không giữ giá trị cũ khi nhập sai. -->
                        <label for="password" class="form-label fw-semibold small">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-coffee"></i></span>
                            <input
                                type="password"
                                class="form-control border-start-0 <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                id="password"
                                name="password"
                                placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)"
                                required
                                autofocus
                            >
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <!-- Trường nhập lại mật khẩu: phải khớp với mật khẩu mới. -->
                        <label for="password_confirm" class="form-label fw-semibold small">Nhập lại mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-coffee"></i></span>
                            <input
                                type="password"
                                class="form-control border-start-0 <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                id="password_confirm"
                                name="password_confirm"
                                placeholder="Nhập lại mật khẩu mới"
                                required
                            >
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="invalid-feedback"><?= e($errors['password_confirm']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-coffee rounded-pill w-100 py-2 fw-semibold mb-3">
                        <!-- Nút gửi form: lưu mật khẩu mới rồi về trang đăng nhập. -->
                        <i class="bi bi-key me-1"></i>Đặt lại mật khẩu
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
