<?php
// Trang quản lý quán — AdminPlaceController render
?>
<!-- Tiêu đề trang quản lý quán: trên cùng, tổng số từ $total -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-shop me-2"></i>Quản lý quán học bài &amp; ăn uống</h1>
        <p class="text-muted small mb-0">Tổng cộng <strong><?= number_format((int) $total) ?></strong> địa điểm tại TP. Long Xuyên, An Giang trong hệ thống</p>
    </div>
    <div>
        <!-- NÚT "Thêm quán mới": bấm vào thì sang form thêm quán (admin/place/create). -->
        <a href="<?= base_url('admin/place/create') ?>" class="btn btn-coffee rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i>Thêm quán mới
        </a>
    </div>
</div>

<!-- Bộ lọc & tìm kiếm: form GET tới admin/place, điều kiện hiện trên đường dẫn -->
<div class="surface-card p-4 mb-4">
    <form action="<?= base_url('admin/place') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-coffee"></i></span>
                                <!-- Ô tìm kiếm q: giữ lại từ khóa cũ từ $filters['q'] -->
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo tên quán, đường Ung Văn Khiêm, Phường Đông Xuyên..." value="<?= e($filters['q'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3">
                        <!-- Ô lọc danh mục category_id: 0 là tất cả, giữ lựa chọn cũ từ $filters['category_id'] -->
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
                        <!-- Ô lọc trạng thái status: trống là mọi trạng thái, active là đang hiển thị, hidden là đang ẩn -->
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Mọi trạng thái --</option>
                <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Đang hiển thị</option>
                <option value="hidden" <?= (($filters['status'] ?? '') === 'hidden') ? 'selected' : '' ?>>Đang ẩn</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
                        <!-- Nút Lọc gửi form GET; nút tròn bên cạnh là link về admin/place để xóa bộ lọc -->
            <button type="submit" class="btn btn-sm btn-coffee flex-grow-1"><i class="bi bi-funnel me-1"></i>Lọc</button>
            <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xóa bộ lọc">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Bảng danh sách quán: dưới form lọc, 7 cột (.admin-table, .admin-thumb) -->
<div class="surface-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="60" class="ps-3">ID</th>
                    <th width="95">Ảnh bìa</th>
                    <th>Quán &amp; địa chỉ</th>
                    <th>Danh mục &amp; tiện ích</th>
                    <th class="text-center">Điểm số</th>
                    <th class="text-center">Trạng thái</th>
                    <th width="175" class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                                <!-- Nếu $places rỗng thì hiện một dòng gộp 7 cột kèm nút Xóa bộ lọc -->
                <?php if (empty($places)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-coffee"></i>
                            Không tìm thấy quán nào phù hợp với điều kiện lọc.
                            <div class="mt-2">
                                <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">Xóa bộ lọc</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                                        <!-- Vòng lặp danh sách quán: $p là một quán trong $places -->
                    <?php foreach ($places as $p): ?>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">#<?= (int) $p['id'] ?></td>
                            <td>
                                <div class="position-relative d-inline-block">
                                                                        <!-- Ảnh bìa: get_image_url() từ $p['cover_image'], huy hiệu đếm $p['image_count'] ảnh -->
                                    <img src="<?= get_image_url($p['cover_image'] ?? null, 'place') ?>" alt="<?= e($p['name']) ?>" class="admin-thumb" width="76" height="54">
                                    <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75" style="font-size: 0.65rem;">
                                        <?= (int) ($p['image_count'] ?? 0) ?> ảnh
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt-fill text-coffee me-1"></i><?= e($p['address']) ?> • <strong class="text-coffee"><?= e($p['district']) ?></strong>
                                </div>
                                <div class="small text-secondary mt-1">
                                                                        <!-- Giờ mở cửa & khoảng giá: substr lấy 5 ký tự đầu của giờ, giá từ price_min/price_max -->
                                    <i class="bi bi-clock me-1"></i><?= e(substr($p['open_time'], 0, 5)) ?> - <?= e(substr($p['close_time'], 0, 5)) ?>
                                    <span class="mx-1">•</span>
                                    <?= e(format_price_range_from_min_max($p['price_min'] ?? null, $p['price_max'] ?? null)) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill mb-1" style="background: #F3F4F6; color: #111827; font-weight: 600;">
                                    <?= e($p['category_name']) ?>
                                </span>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                                        <!-- Vòng lặp tiện ích của quán: $p['amenities'], thêm nhãn Mở khuya nếu is_late_night_friendly($p) -->
                                    <?php foreach ($p['amenities'] ?? [] as $am): ?>
                                        <span class="pill-amenity"><?= e($am['name']) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (is_late_night_friendly($p)): ?>
                                        <span class="pill-amenity" style="background: rgba(217, 119, 6, 0.14); color: #B45309;"> Mở khuya</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                                                <!-- Điểm số: render_stars() từ $p['avg_rating'], bên dưới là $p['total_reviews'] đánh giá -->
                                <div><?= render_stars((float) $p['avg_rating']) ?></div>
                                <div class="small text-muted"><?= (int) $p['total_reviews'] ?> đánh giá</div>
                            </td>
                            <td class="text-center">
                                                                <!-- Huy hiệu trạng thái: active là Hiển thị, còn lại là Đang ẩn -->
                                <?php if ($p['status'] === 'active'): ?>
                                    <span class="badge badge-status-open rounded-pill px-2 py-1">
                                        <i class="bi bi-eye-fill me-1"></i>Hiển thị
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Đang ẩn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                                                <!-- Cột thao tác: nút icon nhỏ, màu theo ý nghĩa (xám xem/sửa/ẩn, đỏ xóa) -->
                                <div class="d-inline-flex gap-1">
                                                                        <!-- Nút Xem: sang trang chi tiết phía người dùng (place/show/{id}) -->
                                    <a href="<?= base_url('place/show/' . (int) $p['id']) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xem trang chi tiết">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                                                        <!-- Nút Sửa: sang form sửa quán (admin/place/edit/{id}) -->
                                    <a href="<?= base_url('admin/place/edit/' . (int) $p['id']) ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Chỉnh sửa & quản lý ảnh">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                                                        <!-- Form Ẩn/Hiện: POST tới admin/place/toggleStatus/{id} để đảo trạng thái -->
                                    <form action="<?= base_url('admin/place/toggleStatus/' . (int) $p['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="<?= $p['status'] === 'active' ? 'Tạm ẩn quán này' : 'Hiển thị quán này' ?>">
                                            <i class="bi <?= $p['status'] === 'active' ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>
                                    </form>
                                                                        <!-- Form Xóa: POST tới admin/place/delete/{id}, xóa vĩnh viễn kèm ảnh, đánh giá và lượt yêu thích -->
                                    <form action="<?= base_url('admin/place/delete/' . (int) $p['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Xóa quán này sẽ xóa toàn bộ ảnh, đánh giá và lượt yêu thích liên quan. Bạn chắc chắn chứ?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Xóa vĩnh viễn">
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

<!-- Phân trang: render_pagination() từ $currentPage và $totalPages về admin/place -->
<?= render_pagination($currentPage, $totalPages, 'admin/place') ?>
