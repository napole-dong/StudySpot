<?php
/**
 * Giao diện Chỉnh sửa Hồ sơ Cá nhân, Đổi Avatar & Đổi Mật khẩu (views/profile/edit.php)
 */
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold text-coffee mb-1"><i class="bi bi-person-gear me-2"></i>Cài đặt Hồ sơ Cá nhân</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin liên hệ, ảnh đại diện hoặc thay đổi mật khẩu bảo mật</p>
        </div>
        <a href="<?= base_url('profile') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại Trang cá nhân
        </a>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Cập nhật thông tin & Đổi Avatar -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-3">
                    <i class="bi bi-person-badge me-2"></i>Thông tin cá nhân &amp; Ảnh đại diện
                </h2>

                <form action="<?= base_url('profile/edit') ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="update_info">

                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-4 bg-light-warm border">
                        <img
                            src="<?= get_image_url($user['avatar'] ?? null, 'avatar') ?>"
                            id="avatarPreviewImg"
                            alt="Avatar"
                            class="rounded-circle object-fit-cover border border-2 border-warning"
                            width="76"
                            height="76"
                        >
                        <div class="flex-grow-1">
                            <label for="avatarInput" class="form-label fw-semibold small mb-1">Đổi ảnh đại diện mới</label>
                            <input
                                type="file"
                                class="form-control form-control-sm <?= isset($errors['avatar']) ? 'is-invalid' : '' ?>"
                                id="avatarInput"
                                name="avatar"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                            <?php if (isset($errors['avatar'])): ?>
                                <div class="invalid-feedback"><?= e($errors['avatar']) ?></div>
                            <?php else: ?>
                                <div class="form-text small">Hỗ trợ JPG, PNG, WEBP tối đa 2MB.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Địa chỉ Email (Không thể thay đổi)</label>
                        <input type="email" class="form-control bg-light" value="<?= e($user['email']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Họ và tên hiển thị <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name"
                            name="name"
                            value="<?= e($_POST['name'] ?? $user['name']) ?>"
                            required
                        >
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold small">Số điện thoại liên hệ</label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                            id="phone"
                            name="phone"
                            placeholder="0901234567"
                            value="<?= e($_POST['phone'] ?? $user['phone']) ?>"
                        >
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-coffee px-4">
                        <i class="bi bi-check2-circle me-1"></i>Lưu thông tin cá nhân
                    </button>
                </form>
            </div>
        </div>

        <!-- Cột phải: Đổi mật khẩu -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-3">
                    <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu bảo mật
                </h2>

                <form action="<?= base_url('profile/edit') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="change_password">

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold small">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input
                            type="password"
                            class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>"
                            id="current_password"
                            name="current_password"
                            required
                        >
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="invalid-feedback"><?= e($errors['current_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold small">Mật khẩu mới (Tối thiểu 6 ký tự) <span class="text-danger">*</span></label>
                        <input
                            type="password"
                            class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                            id="new_password"
                            name="new_password"
                            required
                            minlength="6"
                        >
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?= e($errors['new_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold small">Nhập lại mật khẩu mới <span class="text-danger">*</span></label>
                        <input
                            type="password"
                            class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            id="confirm_password"
                            name="confirm_password"
                            required
                            minlength="6"
                        >
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?= e($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-outline-coffee w-100">
                        <i class="bi bi-key-fill me-1"></i>Cập nhật mật khẩu mới
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            document.getElementById('avatarPreviewImg').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
