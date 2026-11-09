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
                                    placeholder="@gmail.com"
                                    value="<?= e($old['email'] ?? '') ?>"
                                    required
                                >
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>
                            <!-- Chỉ chấp nhận email @gmail.com; sinh viên AGU đăng nhập bằng Google. -->
                            <div class="form-text">Chỉ chấp nhận email @gmail.com. Sinh viên AGU đăng nhập bằng Google.</div>
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

                <!-- Đăng ký bằng Google: chỉ hiện khi đã cấu hình, chỉ chấp nhận email @agu.edu.vn. -->
                <?php if (!empty($googleLoginEnabled)): ?>
                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1">
                        <span class="px-2 small text-muted">hoặc đăng ký bằng</span>
                        <hr class="flex-grow-1">
                    </div>
                    <a href="<?= base_url('auth/google') ?>" class="btn btn-light border rounded-pill w-100 py-2 fw-semibold mb-2 d-flex align-items-center justify-content-center gap-2">
                        <!-- Logo Google: SVG 4 màu, tài khoản mới được tạo tự động. -->
                        <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303C33.654,32.657,29.223,36,24,36c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                        Đăng ký bằng Google
                    </a>
                    <p class="text-center small text-muted mb-3">Chỉ tài khoản email @<?= e(GOOGLE_ALLOWED_DOMAIN) ?></p>
                <?php endif; ?>

                <div class="text-center small text-muted">
                    Đã có tài khoản thành viên?
                    <!-- Link dưới form: đã có tài khoản thì sang trang đăng nhập. -->
                    <a href="<?= base_url('auth/login') ?>" class="text-coffee fw-bold text-decoration-none">Đăng nhập tại đây</a>
                </div>
            </div>
        </div>
    </div>
</div>
