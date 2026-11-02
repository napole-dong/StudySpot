<?php
/**
 * Giao diện Đăng ký tài khoản thành viên mới (views/auth/register.php)
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <div class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h1 class="h4 fw-bold text-coffee mb-1">Đăng ký thành viên StudySpot</h1>
                    <p class="text-muted small mb-0">Tham gia cộng đồng chia sẻ địa điểm học tập dành cho sinh viên</p>
                </div>

                <form action="<?= base_url('auth/register') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Họ và tên hiển thị <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-coffee"></i></span>
                            <input
                                type="text"
                                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                id="name"
                                name="name"
                                placeholder="VD: Nguyễn Minh Khôi"
                                value="<?= e($old['name'] ?? '') ?>"
                                required
                                minlength="2"
                                maxlength="100"
                            >
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="email" class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope text-coffee"></i></span>
                                <input
                                    type="email"
                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    placeholder="sinhvien@student.edu.vn"
                                    value="<?= e($old['email'] ?? '') ?>"
                                    required
                                >
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="phone" class="form-label fw-semibold small">Số điện thoại <span class="text-muted fw-normal">(Tùy chọn)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone text-coffee"></i></span>
                                <input
                                    type="text"
                                    class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                    id="phone"
                                    name="phone"
                                    placeholder="0901234567"
                                    value="<?= e($old['phone'] ?? '') ?>"
                                >
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key text-coffee"></i></span>
                                <input
                                    type="password"
                                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                    id="password"
                                    name="password"
                                    placeholder="Tối thiểu 6 ký tự"
                                    required
                                    minlength="6"
                                >
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirm" class="form-label fw-semibold small">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-check2-square text-coffee"></i></span>
                                <input
                                    type="password"
                                    class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                    id="password_confirm"
                                    name="password_confirm"
                                    placeholder="Nhập khớp mật khẩu"
                                    required
                                    minlength="6"
                                >
                                <?php if (isset($errors['password_confirm'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['password_confirm']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold mb-3">
                        <i class="bi bi-person-check-fill me-1"></i>Hoàn tất Đăng ký
                    </button>
                </form>

                <div class="text-center small text-muted">
                    Đã có tài khoản?
                    <a href="<?= base_url('auth/login') ?>" class="text-coffee fw-semibold text-decoration-none">Đăng nhập tại đây</a>
                </div>
            </div>
        </div>
    </div>
</div>
