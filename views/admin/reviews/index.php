<?php
// Biến $currentStatus: trạng thái đang lọc từ $filters['status'], rỗng nghĩa là tab “Tất cả”.
$currentStatus = $filters['status'] ?? '';
?>
<!-- Tiêu đề trang kiểm duyệt đánh giá: trên cùng, duyệt/ẩn/xóa sẽ tự cập nhật điểm quán -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-chat-square-quote me-2"></i>Kiểm duyệt đánh giá sinh viên</h1>
        <p class="text-muted small mb-0">Khi duyệt, ẩn hoặc xóa đánh giá, hệ thống tự động cập nhật điểm trung bình và số đánh giá của quán.</p>
    </div>
</div>

<!-- Hàng lọc nhanh theo trạng thái + ô tìm kiếm: dưới tiêu đề trang -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex flex-wrap gap-2">
                <!-- Nút lọc “Tất cả”: số trong ngoặc từ $counts['all'], nút đang chọn tô đậm theo $currentStatus -->
        <a href="<?= base_url('admin/review') ?>" class="btn btn-sm rounded-pill px-3 <?= $currentStatus === '' ? 'btn-coffee' : 'btn-outline-coffee' ?>">
            Tất cả (<?= number_format((int) ($counts['all'] ?? 0)) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm rounded-pill px-3 <?= $currentStatus === 'pending' ? 'btn-accent fw-bold' : 'btn-outline-coffee' ?>">
            <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt (<?= number_format((int) ($counts['pending'] ?? 0)) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=approved') ?>" class="btn btn-sm rounded-pill px-3 <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-coffee' ?>">
            <i class="bi bi-check-circle me-1"></i>Đã duyệt (<?= number_format((int) ($counts['approved'] ?? 0)) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=hidden') ?>" class="btn btn-sm rounded-pill px-3 <?= $currentStatus === 'hidden' ? 'btn-secondary' : 'btn-outline-coffee' ?>">
            <i class="bi bi-eye-slash me-1"></i>Đã ẩn (<?= number_format((int) ($counts['hidden'] ?? 0)) ?>)
        </a>
    </div>

        <!-- Form tìm kiếm review: GET tới admin/review, giữ trạng thái lọc bằng ô ẩn status khi $currentStatus khác rỗng -->
    <form action="<?= base_url('admin/review') ?>" method="GET" class="d-flex gap-2">
        <?php if ($currentStatus !== ''): ?>
            <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
        <?php endif; ?>
        <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control" placeholder="Tìm quán, người gửi..." value="<?= e($filters['q'] ?? '') ?>">
            <button type="submit" class="btn btn-coffee" data-bs-toggle="tooltip" title="Tìm kiếm"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<!-- Bảng danh sách review: dưới hàng lọc, 6 cột (.admin-table, .admin-thumb) -->
<div class="surface-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="65" class="ps-3">ID</th>
                    <th width="220">Quán &amp; người</th>
                    <th width="140">Điểm</th>
                    <th>Nội dung</th>
                    <th width="125" class="text-center">Trạng thái</th>
                    <th width="160" class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                                <!-- Nếu $reviews rỗng thì hiện một dòng gộp 6 cột -->
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-chat-square-dots fs-2 d-block mb-2 text-coffee"></i>
                            Không có đánh giá nào trong mục này.
                        </td>
                    </tr>
                <?php else: ?>
                                        <!-- Vòng lặp danh sách review: $rv là một review trong $reviews -->
                    <?php foreach ($reviews as $rv): ?>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">#<?= (int) $rv['id'] ?></td>
                            <td>
                                <a href="<?= base_url('place/show/' . (int) $rv['place_id']) ?>" class="fw-bold text-dark text-decoration-none d-block">
                                    <?= e($rv['place_name']) ?>
                                </a>
                                <div class="small text-secondary mt-1">
                                    <i class="bi bi-person-circle text-coffee me-1"></i><?= e($rv['user_name']) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                </div>
                            </td>
                            <td>
                                                                <!-- Điểm số: sao từ $rv['rating_overall'], bên dưới là 3 nhãn Wifi, KG, Giá -->
                                <div><?= render_stars((float) $rv['rating_overall']) ?></div>
                                <div class="d-flex flex-wrap gap-1 mt-1" style="font-size: 0.72rem;">
                                    <span class="pill-amenity py-0 px-2">Wifi: <strong><?= (int) $rv['rating_wifi'] ?></strong></span>
                                    <span class="pill-amenity py-0 px-2">KG: <strong><?= (int) $rv['rating_space'] ?></strong></span>
                                    <span class="pill-amenity py-0 px-2">Giá: <strong><?= (int) $rv['rating_price'] ?></strong></span>
                                </div>
                            </td>
                            <td>
                                                                <!-- Nội dung review: e() thoát ký tự đặc biệt, nl2br() giữ xuống dòng, có ảnh đính kèm thì hiện ảnh thu nhỏ -->
                                <div class="small text-dark"><?= nl2br(e($rv['content'])) ?></div>
                                <?php if (!empty($rv['image_path'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= get_image_url($rv['image_path'], 'review') ?>" target="_blank">
                                            <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh review" class="admin-thumb" width="76" height="54">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                                                <!-- Huy hiệu trạng thái: pending là Chờ duyệt, approved là Đã duyệt, nhánh else mặc định là Đã ẩn -->
                                <?php if ($rv['status'] === 'pending'): ?>
                                    <span class="badge rounded-pill px-2 py-1" style="background: rgba(217, 119, 6, 0.16); color: #B45309;">
                                        <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt
                                    </span>
                                <?php elseif ($rv['status'] === 'approved'): ?>
                                    <span class="badge badge-status-open rounded-pill px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đã duyệt
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Đã ẩn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                                                <!-- Cột thao tác: nút icon nhỏ, màu theo ý nghĩa; nút Duyệt/Ẩn chỉ hiện khi chưa duyệt/chưa ẩn, nút Xóa luôn hiện -->
                                <div class="d-inline-flex gap-1">
                                                                        <!-- Form Duyệt: POST tới admin/review/updateStatus/{id} với status=approved -->
                                    <?php if ($rv['status'] !== 'approved'): ?>
                                        <form action="<?= base_url('admin/review/updateStatus/' . (int) $rv['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Duyệt hiển thị đánh giá này">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                                                        <!-- Form Ẩn: POST tới cùng route updateStatus/{id} với status=hidden -->
                                    <?php if ($rv['status'] !== 'hidden'): ?>
                                        <form action="<?= base_url('admin/review/updateStatus/' . (int) $rv['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="hidden">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Ẩn đánh giá này">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                                                        <!-- Form Xóa: POST tới admin/review/delete/{id}, xóa vĩnh viễn và tính lại điểm quán -->
                                    <form action="<?= base_url('admin/review/delete/' . (int) $rv['id']) ?>" method="POST" onsubmit="return confirm('Xóa vĩnh viễn đánh giá này và tính lại điểm quán?');">
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

<!-- Phân trang: render_pagination() từ $currentPage và $totalPages về admin/review -->
<?= render_pagination($currentPage, $totalPages, 'admin/review') ?>
