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

                <hr class="my-4">
                <div class="rounded-4 p-3" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                    <div class="small fw-bold text-coffee mb-2">
                        <!-- Hộp điền nhanh chỉ kiểm thử: 3 nút chỉ điền sẵn, không tự đăng nhập. -->
                        <i class="bi bi-lightning-charge-fill text-accent me-1"></i>Điền nhanh tài khoản thử nghiệm (Mật khẩu: <code>123456</code>):
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-coffee rounded-pill py-1 px-3" onclick="fillDemoAccount('admin@studyspot.vn', '123456')">
                            <i class="bi bi-shield-lock me-1"></i>Admin AGU
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-coffee rounded-pill py-1 px-3" onclick="fillDemoAccount('khoi.nguyen@student.edu.vn', '123456')">
                            <i class="bi bi-person me-1"></i>SV Minh Khôi
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3" onclick="fillDemoAccount('bao.vo@student.edu.vn', '123456')">
                            <i class="bi bi-lock-fill me-1"></i>Tài khoản khóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JS điền nhanh: gán email/mật khẩu vào 2 ô email, password.
function fillDemoAccount(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
