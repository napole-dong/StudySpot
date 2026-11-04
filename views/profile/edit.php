<?php
// Trang Cài đặt hồ sơ, do ProfileController (edit) render.
// Trang Cài đặt hồ sơ: $user, $errors; 2 form cùng route profile/edit, phân biệt form_type.
?>
<div class="container py-4">
    <!-- Đầu trang: tiêu đề cài đặt hồ sơ, nút về profile. -->
    <div class="d-flex justify-content-between align-items-center mb-4 profile-edit-head">
        <div>
            <h1 class="page-title"><i class="bi bi-person-gear me-2"></i>Cài đặt hồ sơ cá nhân</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin, ảnh đại diện hoặc mật khẩu.</p>
        </div>
        <a href="<?= base_url('profile') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i>Quay lại Trang cá nhân
        </a>
    </div>

    <div class="row g-4">
        <!-- Cột trái (rộng): thẻ thông tin cá nhân và đổi avatar. -->
        <div class="col-lg-8">
            <div class="card surface-card p-4">
                <h2 class="h6 fw-bold text-coffee mb-3">
                    <i class="bi bi-person-badge-fill me-2"></i>Thông tin cá nhân
                </h2>

                <!-- Form cập nhật: POST tới profile/edit, enctype multipart vì upload avatar, form_type=update_info. -->
                <form action="<?= base_url('profile/edit') ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="update_info">

                    <!-- Khối đổi avatar: ảnh id avatarPreviewImg để JS xem trước, 'Chọn ảnh' là label ô file ẩn, tên file ở avatarFileName. -->
                    <!-- Ô avatar: chỉ JPG/PNG/WEBP; có lỗi thì hiện lỗi, không thì ghi chú tối đa 2MB. -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-4 avatar-upload" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                        <img
                            src="<?= get_image_url($user['avatar'] ?? null, 'avatar') ?>"
                            id="avatarPreviewImg"
                            alt="<?= e($user['name']) ?>"
                            class="rounded-circle object-fit-cover shadow-sm"
                            style="border: 2px solid #E5E7EB;"
                            width="80"
                            height="80"
                        >
                        <div class="flex-grow-1">
                            <label for="avatarInput" class="form-label fw-semibold small mb-1">Đổi ảnh đại diện</label>
                            <div class="d-flex align-items-center gap-2">
                                <label for="avatarInput" class="btn btn-sm btn-outline-coffee rounded-pill px-3 mb-0 flex-shrink-0">Chọn ảnh</label>
                                <span id="avatarFileName" class="text-muted small text-truncate">Chưa chọn ảnh mới</span>
                            </div>
                            <input
                                type="file"
                                class="visually-hidden <?= isset($errors['avatar']) ? 'is-invalid' : '' ?>"
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
                        <!-- Ô email: chỉ hiển thị, disabled nên trình duyệt không gửi kèm form, không đổi email ở đây. -->
                        <label class="form-label fw-semibold small">Địa chỉ Email đăng nhập</label>
                        <input type="email" class="form-control bg-light" value="<?= e($user['email']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <!-- Ô họ tên: bắt buộc; ưu tiên $_POST['name'] rồi mới $user['name'] để gửi lỗi không mất chữ. -->
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
                        <!-- Ô số điện thoại: không bắt buộc, gợi ý 10 chữ số; cũng giữ $_POST khi gửi lỗi. -->
                        <label for="phone" class="form-label fw-semibold small">Số điện thoại liên hệ</label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                            id="phone"
                            name="phone"
                            placeholder="Nhập số điện thoại 10 chữ số"
                            value="<?= e($_POST['phone'] ?? $user['phone']) ?>"
                        >
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-coffee rounded-pill px-4 py-2">
                        <!-- Nút lưu cột trái: chỉ gửi form thông tin và avatar, không động tới mật khẩu. -->
                        <i class="bi bi-check2-circle me-1"></i>Lưu thông tin cá nhân
                    </button>
                </form>
            </div>
        </div>

        <!-- Cột phải (hẹp): thẻ đổi mật khẩu, tách riêng khỏi form thông tin. -->
        <div class="col-lg-4">
            <div class="card surface-card p-4">
                <h2 class="h6 fw-bold text-coffee mb-3">
                    <i class="bi bi-shield-lock-fill me-2"></i>Đổi mật khẩu
                </h2>

                <form action="<?= base_url('profile/edit') ?>" method="POST" novalidate>
                    <?= csrf_field() ?>
                    <!-- Form đổi mật khẩu: POST cùng route profile/edit, form_type=change_password, không enctype. -->
                    <input type="hidden" name="form_type" value="change_password">

                    <div class="mb-3">
                        <!-- Ô mật khẩu hiện tại: bắt buộc, xác thực đúng chủ tài khoản trước khi đổi. -->
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
                        <!-- Ô mật khẩu mới: bắt buộc, tối thiểu 6 ký tự. -->
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
                        <!-- Ô nhập lại mật khẩu mới: bắt buộc, tối thiểu 6 ký tự; server đối chiếu để bắt lỗi gõ sai. -->
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

                    <button type="submit" class="btn btn-outline-coffee rounded-pill w-100 py-2">
                        <i class="bi bi-key-fill me-1"></i>Cập nhật mật khẩu mới
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
