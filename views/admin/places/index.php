<?php
/**
 * Giao diện Danh sách Quán học bài dành cho Admin (views/admin/places/index.php)
 */
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-shop text-coffee me-2"></i>Quản lý Quán học bài</h1>
        <p class="text-muted small mb-0">Tổng cộng <strong><?= (int) $total ?></strong> địa điểm trong hệ thống</p>
    </div>
    <div>
        <a href="<?= base_url('admin/place/create') ?>" class="btn btn-coffee">
            <i class="bi bi-plus-lg me-1"></i>Thêm quán mới
        </a>
    </div>
</div>

<!-- Bộ lọc & Tìm kiếm nhanh -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/place') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên quán, địa chỉ, quận..." value="<?= e($filters['q'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Mọi trạng thái --</option>
                    <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Đang hiển thị</option>
                    <option value="hidden" <?= (($filters['status'] ?? '') === 'hidden') ? 'selected' : '' ?>>Đang ẩn</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark flex-grow-1">Lọc</button>
                <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách quán -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th width="60" class="ps-3">ID</th>
                    <th width="90">Ảnh bìa</th>
                    <th>Thông tin quán</th>
                    <th>Danh mục &amp; Tiện ích</th>
                    <th class="text-center">Điểm số</th>
                    <th class="text-center">Trạng thái</th>
                    <th width="175" class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($places)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Không tìm thấy quán nào phù hợp với điều kiện lọc.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($places as $p): ?>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">#<?= (int) $p['id'] ?></td>
                            <td>
                                <div class="position-relative d-inline-block">
                                    <img src="<?= get_image_url($p['cover_image'] ?? null, 'place') ?>" alt="<?= e($p['name']) ?>" class="rounded-3 object-fit-cover border" width="72" height="52">
                                    <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75" style="font-size: 0.65rem;">
                                        <?= (int) ($p['image_count'] ?? 0) ?> ảnh
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i><?= e($p['address']) ?>, <strong><?= e($p['district']) ?></strong>
                                </div>
                                <div class="small text-secondary mt-1">
                                    <i class="bi bi-clock me-1"></i><?= e(substr($p['open_time'], 0, 5)) ?> - <?= e(substr($p['close_time'], 0, 5)) ?>
                                    <span class="mx-1">•</span>
                                    <?= e(format_price_range((int) $p['price_range'])) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border mb-1"><?= e($p['category_name']) ?></span>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <?php if (!empty($p['has_wifi'])): ?>
                                        <span class="badge bg-info-subtle text-info-emphasis"><i class="bi bi-wifi"></i> Wifi</span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['has_outlet'])): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis"><i class="bi bi-plug"></i> Ổ cắm</span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['has_ac'])): ?>
                                        <span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-snow"></i> Máy lạnh</span>
                                    <?php endif; ?>
                                    <?php if (is_deadline_friendly($p)): ?>
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-lightning-charge-fill"></i> Deadline</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div><?= render_stars((float) $p['avg_rating']) ?></div>
                                <div class="small text-muted"><?= (int) $p['total_reviews'] ?> đánh giá</div>
                            </td>
                            <td class="text-center">
                                <?php if ($p['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-eye-fill me-1"></i>Hiển thị
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Đang ẩn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?= base_url('place/show/' . (int) $p['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="Xem ngoài trang chủ">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="<?= base_url('admin/place/edit/' . (int) $p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Sửa & Quản lý ảnh">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= base_url('admin/place/toggleStatus/' . (int) $p['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm <?= $p['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $p['status'] === 'active' ? 'Ẩn quán này' : 'Hiện quán này' ?>">
                                            <i class="bi <?= $p['status'] === 'active' ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>
                                    </form>
                                    <form action="<?= base_url('admin/place/delete/' . (int) $p['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Xóa quán này sẽ xóa toàn bộ ảnh, đánh giá và lượt yêu thích liên quan. Bạn chắc chắn chứ?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa vĩnh viễn">
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
</div>

<?= render_pagination($currentPage, $totalPages, 'admin/place') ?>
