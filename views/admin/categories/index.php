<?php
// Biến $isEditing: đúng khi $editCategory không rỗng (đang sửa), sai khi thêm mới.
$isEditing = !empty($editCategory);
?>
<!-- Tiêu đề trang quản lý danh mục: trên cùng, không có nút thêm riêng vì form thêm ở cột trái -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-tags me-2"></i>Quản lý danh mục loại hình</h1>
        <p class="text-muted small mb-0">Phân loại các nhóm địa điểm học bài, cà phê sách và co-working tại TP. Long Xuyên</p>
    </div>
</div>

<div class="row g-4">
        <!-- Cột trái: Form thêm/sửa danh mục (tiêu đề và nút đổi theo $isEditing) -->
    <div class="col-lg-4">
        <div class="surface-card overflow-hidden">
            <div class="p-3 border-bottom" style="background: #EFECE6;">
                <h5 class="h6 fw-bold mb-0 text-coffee">
                    <?php if ($isEditing): ?>
                        <i class="bi bi-pencil-square text-warning me-2"></i>Cập nhật danh mục #<?= (int) $editCategory['id'] ?>
                    <?php else: ?>
                        <i class="bi bi-plus-circle me-2"></i>Thêm danh mục mới
                    <?php endif; ?>
                </h5>
            </div>
            <div class="p-4">
                                <!-- Form danh mục: đang sửa thì POST tới admin/category/update/{id}, đang thêm thì POST tới admin/category/store -->
                <form action="<?= $isEditing ? base_url('admin/category/update/' . (int) $editCategory['id']) : base_url('admin/category/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                                                <!-- Trường tên danh mục name: bắt buộc, tối đa 100 ký tự, đang sửa thì điền sẵn $editCategory['name'] -->
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
                                                <!-- Nút gửi form: đang sửa là “Lưu thay đổi”, đang thêm là “Thêm danh mục”; nút Hủy chỉ hiện khi đang sửa, về admin/category -->
                        <button type="submit" class="btn btn-coffee rounded-pill flex-grow-1">
                            <i class="bi bi-check2-circle me-1"></i><?= $isEditing ? 'Lưu thay đổi' : 'Thêm danh mục' ?>
                        </button>
                        <?php if ($isEditing): ?>
                            <a href="<?= base_url('admin/category') ?>" class="btn btn-outline-secondary rounded-pill px-3">Hủy</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Cột phải: Bảng danh sách danh mục (tổng số từ count($categories)) -->
    <div class="col-lg-8">
        <div class="surface-card overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-coffee">Danh sách danh mục hiện có (<?= count($categories) ?>)</span>
            </div>
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="70" class="ps-3">ID</th>
                            <th>Danh mục</th>
                            <th class="text-center">Số quán</th>
                            <th width="160" class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                                                <!-- Nếu $categories rỗng thì hiện một dòng gộp 4 cột -->
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                            </tr>
                        <?php else: ?>
                                                        <!-- Vòng lặp danh mục: $cat là một danh mục trong $categories -->
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">#<?= (int) $cat['id'] ?></td>
                                    <td class="fw-semibold text-dark"><?= e($cat['name']) ?></td>
                                    <td class="text-center">
                                        <span class="pill-amenity px-3">
                                            <?= (int) $cat['place_count'] ?> quán
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                                                                <!-- Nút Sửa: tải lại chính trang này với ?edit_id={id} để form cột trái chuyển sang chế độ sửa -->
                                        <a href="<?= base_url('admin/category?edit_id=' . (int) $cat['id']) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Sửa tên danh mục">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                                                                <!-- Form xóa danh mục: POST tới admin/category/delete/{id}, nút xóa bị vô hiệu hóa khi $cat['place_count'] > 0 -->
                                        <form action="<?= base_url('admin/category/delete/' . (int) $cat['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm <?= ((int) $cat['place_count'] > 0) ? 'btn-outline-secondary' : 'btn-outline-danger' ?>" data-bs-toggle="tooltip" title="<?= ((int) $cat['place_count'] > 0) ? 'Đang có quán thuộc danh mục này' : 'Xóa danh mục' ?>" <?= ((int) $cat['place_count'] > 0) ? 'disabled' : '' ?>>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-2 px-3 border-top small text-muted" style="background: #F9FAFB;">
                <i class="bi bi-info-circle text-coffee me-1"></i>Lưu ý: Các danh mục đang có quán trực thuộc sẽ được khóa nút xóa để bảo toàn dữ liệu.
            </div>
        </div>
    </div>
</div>
