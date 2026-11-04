<?php
// Trang Đăng ký, do AuthController (register) render.
// Trang Đăng ký: $errors, $old (tên, email, số điện thoại giữ lại khi nhập sai).
?>
<div class="container py-4">
    <!-- Bố cục chia đôi giống login: panel ảnh trái, form phải; 2 trang cao bằng nhau. -->
    <div class="auth-split">
            <!-- Panel ảnh trái: cùng ảnh cafe_4.jpg với login để 2 trang đồng bộ. -->
            <div class="auth-panel auth-panel-image">
                <img src="<?= base_url('assets/img/places/cafe_4.jpg') ?>" alt="Không gian quán cà phê học bài">
                <div class="auth-panel-brand">
                    <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white"><i class="bi bi-cup-hot-fill"></i></span>
                    <span>StudySpot <span class="fw-normal opacity-75">Long Xuyên</span></span>
                </div>
            </div>
        <div class="auth-form">
            <div class="p-0">
                <div class="text-center mb-4">
                    <div class="brand-badge d-inline-flex align-items-center justify-content-center rounded-4 text-white mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h1 class="page-title">Đăng ký thành viên StudySpot</h1>
                    <p class="text-muted small mb-0">Tham gia cộng đồng review góc học tập dành cho sinh viên tại TP. Long Xuyên</p>
                </div>

                <!-- Form đăng ký: POST tới auth/register, novalidate, csrf_field() chống CSRF. -->
                <form action="<?= base_url('auth/register') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <!-- Trường họ tên: bắt buộc, 2-100 ký tự, giữ $old['name'], e() chống XSS. -->
                        <label for="name" class="form-label fw-semibold small">Họ và tên hiển thị <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-coffee"></i></span>
                            <input
                                type="text"
                                class="form-control border-start-0 <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                id="name"
                                name="name"
                                placeholder="Nhập họ tên của bạn"
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
                            <!-- Trường email: bắt buộc, type email; đây là tài khoản đăng nhập sau này. -->
                            <label for="email" class="form-label fw-semibold small">Địa chỉ Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-coffee"></i></span>
                                <input
                                    type="email"
                                    class="form-control border-start-0 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
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
                            <!-- Trường số điện thoại: tùy chọn, gợi ý 10 chữ số; để trống vẫn đăng ký được. -->
                            <label for="phone" class="form-label fw-semibold small">Số điện thoại <span class="text-muted fw-normal">(Tùy chọn)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-coffee"></i></span>
                                <input
                                    type="text"
                                    class="form-control border-start-0 <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                    id="phone"
                                    name="phone"
                                    placeholder="10 chữ số"
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
                            <!-- Trường mật khẩu: bắt buộc, tối thiểu 6 ký tự, type password. -->
                            <label for="password" class="form-label fw-semibold small">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-coffee"></i></span>
                                <input
                                    type="password"
                                    class="form-control border-start-0 <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
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
                            <!-- Trường nhập lại mật khẩu: bắt buộc, tối thiểu 6 ký tự; server đối chiếu để bắt lỗi gõ sai. -->
                            <label for="password_confirm" class="form-label fw-semibold small">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-check2-square text-coffee"></i></span>
                                <input
                                    type="password"
                                    class="form-control border-start-0 <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                    id="password_confirm"
                                    name="password_confirm"
                                    placeholder="Nhập lại mật khẩu"
                                    required
                                    minlength="6"
                                >
                                <?php if (isset($errors['password_confirm'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['password_confirm']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-coffee rounded-pill w-100 py-2 fw-semibold mb-3">
                        <!-- Nút gửi form: gửi toàn bộ thông tin để tạo tài khoản; có lỗi thì quay lại kèm lỗi từng trường. -->
                        <i class="bi bi-person-check-fill me-1"></i>Hoàn tất Đăng ký
                    </button>
                </form>

                <div class="text-center small text-muted">
                    Đã có tài khoản thành viên?
                    <!-- Link dưới form: đã có tài khoản thì sang trang đăng nhập. -->
                    <a href="<?= base_url('auth/login') ?>" class="text-coffee fw-bold text-decoration-none">Đăng nhập tại đây</a>
                </div>
            </div>
        </div>
    </div>
</div>
