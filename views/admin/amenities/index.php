<?php
/**
 * Giao diện Quản lý tiện ích (views/admin/amenities/index.php)
 * Thêm tiện ích mới ở đây là tự xuất hiện ở form quán, bộ lọc trang Khám phá và thẻ quán.
 */
$isEditing = !empty($editAmenity);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Quản lý tiện ích</h1>
        <p class="text-muted small mb-0">Tiện ích là tính năng của quán (Wifi, ổ cắm, máy lạnh...). Thêm tiện ích mới không cần sửa code, không cần icon — tiện ích chỉ hiển thị bằng chữ.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Form Thêm / Sửa tiện ích -->
    <div class="col-lg-4">
        <div class="surface-card overflow-hidden">
            <div class="p-3 border-bottom" style="background: #EFECE6;">
                <h5 class="h6 fw-bold mb-0 text-coffee">
                    <?php if ($isEditing): ?>
                        Cập nhật tiện ích #<?= (int) $editAmenity['id'] ?>
                    <?php else: ?>
                        Thêm tiện ích mới
                    <?php endif; ?>
                </h5>
            </div>
            <div class="p-4">
                <form action="<?= $isEditing ? base_url('admin/amenity/update/' . (int) $editAmenity['id']) : base_url('admin/amenity/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Tên tiện ích <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            placeholder="VD: Có chỗ đậu xe"
                            value="<?= e($isEditing ? $editAmenity['name'] : '') ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-coffee rounded-pill flex-grow-1">
                            <?= $isEditing ? 'Lưu thay đổi' : 'Thêm tiện ích' ?>
                        </button>
                        <?php if ($isEditing): ?>
                            <a href="<?= base_url('admin/amenity') ?>" class="btn btn-outline-secondary rounded-pill px-3">Hủy</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cột phải: Bảng danh sách tiện ích -->
    <div class="col-lg-8">
        <div class="surface-card overflow-hidden">
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="ps-3">ID</th>
                            <th>Tên tiện ích</th>
                            <th>Slug (dùng cho bộ lọc)</th>
                            <th class="text-center">Số quán đang dùng</th>
                            <th width="150" class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($amenities)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có tiện ích nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($amenities as $am): ?>
                                <tr>
                                    <td class="ps-3"><?= (int) $am['id'] ?></td>
                                    <td class="fw-semibold"><?= e($am['name']) ?></td>
                                    <td class="text-muted"><?= e($am['slug']) ?></td>
                                    <td class="text-center"><?= (int) $am['place_count'] ?> quán</td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                        <a href="<?= base_url('admin/amenity?edit_id=' . (int) $am['id']) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Sửa tên tiện ích">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ((int) $am['place_count'] > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled data-bs-toggle="tooltip" title="Đang có quán sử dụng, không thể xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <form action="<?= base_url('admin/amenity/delete/' . (int) $am['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Xóa tiện ích &quot;<?= e($am['name']) ?>&quot;?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Xóa tiện ích">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-muted small mt-3">Lưu ý: Tiện ích đang có quán sử dụng sẽ bị khóa nút xóa để bảo toàn dữ liệu. Hãy bỏ tiện ích đó khỏi các quán trước rồi mới xóa được.</p>
    </div>
</div>
