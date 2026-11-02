<?php
/**
 * Giao diện Đăng nhập thành viên & quản trị viên (views/auth/login.php)
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <div class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-cup-hot-fill"></i>
                    </div>
                    <h1 class="h4 fw-bold text-coffee mb-1">Đăng nhập StudySpot</h1>
                    <p class="text-muted small mb-0">Khám phá và lưu lại những góc học tập lý tưởng nhất</p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger d-flex align-items-center py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= e($errors['general']) ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/login') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-coffee"></i></span>
                            <input
                                type="email"
                                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
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
                        <label for="password" class="form-label fw-semibold small">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock text-coffee"></i></span>
                            <input
                                type="password"
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
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

                    <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập ngay
                    </button>
                </form>

                <div class="text-center small text-muted">
                    Chưa có tài khoản thành viên?
                    <a href="<?= base_url('auth/register') ?>" class="text-coffee fw-semibold text-decoration-none">Đăng ký miễn phí</a>
                </div>

                <!-- Hộp hỗ trợ điền nhanh tài khoản mẫu phục vụ kiểm thử / chấm đồ án -->
                <hr class="my-4">
                <div class="bg-light rounded-3 p-3 border">
                    <div class="small fw-bold text-secondary mb-2">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Điền nhanh tài khoản mẫu (Mật khẩu: <code>123456</code>):
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-xs btn-outline-danger btn-sm py-1 px-2" onclick="fillDemoAccount('admin@studyspot.vn', '123456')">
                            <i class="bi bi-shield-lock me-1"></i>Admin
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark btn-sm py-1 px-2" onclick="fillDemoAccount('khoi.nguyen@student.edu.vn', '123456')">
                            <i class="bi bi-person me-1"></i>Sinh viên (Active)
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-sm py-1 px-2" onclick="fillDemoAccount('bao.vo@student.edu.vn', '123456')">
                            <i class="bi bi-lock-fill me-1"></i>Tài khoản bị khóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemoAccount(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
