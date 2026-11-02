<?php
/**
 * Giao diện Quản lý Danh mục loại hình quán (views/admin/categories/index.php)
 */
$isEditing = !empty($editCategory);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-tags text-coffee me-2"></i>Quản lý Danh mục Loại hình</h1>
        <p class="text-muted small mb-0">Phân loại các nhóm địa điểm học bài, cà phê sách, co-working và quán ăn sinh viên</p>
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Form Thêm / Sửa danh mục -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title h6 fw-bold mb-0">
                    <?php if ($isEditing): ?>
                        <i class="bi bi-pencil-square text-warning me-2"></i>Cập nhật danh mục #<?= (int) $editCategory['id'] ?>
                    <?php else: ?>
                        <i class="bi bi-plus-circle text-coffee me-2"></i>Thêm danh mục mới
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= $isEditing ? base_url('admin/category/update/' . (int) $editCategory['id']) : base_url('admin/category/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Tên danh mục <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            placeholder="VD: Cà phê Học bài & Đọc sách"
                            value="<?= e($isEditing ? $editCategory['name'] : '') ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-coffee flex-grow-1">
                            <i class="bi bi-check2-circle me-1"></i><?= $isEditing ? 'Lưu thay đổi' : 'Thêm danh mục' ?>
                        </button>
                        <?php if ($isEditing): ?>
                            <a href="<?= base_url('admin/category') ?>" class="btn btn-outline-secondary">Hủy</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cột phải: Bảng danh sách danh mục -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold small">Danh sách danh mục hiện có (<?= count($categories) ?>)</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th width="70" class="ps-3">ID</th>
                            <th>Tên danh mục loại hình</th>
                            <th class="text-center">Số lượng quán</th>
                            <th width="160" class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">#<?= (int) $cat['id'] ?></td>
                                    <td class="fw-semibold"><?= e($cat['name']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-dark rounded-pill px-3">
                                            <?= (int) $cat['place_count'] ?> quán
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="<?= base_url('admin/category?edit_id=' . (int) $cat['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= base_url('admin/category/delete/' . (int) $cat['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa" <?= ((int) $cat['place_count'] > 0) ? 'disabled' : '' ?>>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light small text-muted py-2 px-3">
                <i class="bi bi-info-circle me-1"></i>Lưu ý: Các danh mục đang có quán trực thuộc sẽ được khóa nút xóa để bảo toàn dữ liệu.
            </div>
        </div>
    </div>
</div>
