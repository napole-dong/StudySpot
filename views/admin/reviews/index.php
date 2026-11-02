<?php
/**
 * Giao diện Kiểm duyệt Đánh giá dành cho Admin (views/admin/reviews/index.php)
 */
$currentStatus = $filters['status'] ?? '';
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-chat-square-quote text-coffee me-2"></i>Kiểm duyệt Đánh giá Sinh viên</h1>
        <p class="text-muted small mb-0">Khi Duyệt, Ẩn hoặc Xóa đánh giá, hệ thống sẽ tự động tính toán lại <code>avg_rating</code> và <code>total_reviews</code> của quán.</p>
    </div>
</div>

<!-- Các nút lọc nhanh theo trạng thái kèm số đếm -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="btn-group shadow-sm">
        <a href="<?= base_url('admin/review') ?>" class="btn btn-sm <?= $currentStatus === '' ? 'btn-dark' : 'btn-white bg-white border' ?>">
            Tất cả (<?= (int) ($counts['all'] ?? 0) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm <?= $currentStatus === 'pending' ? 'btn-warning fw-bold' : 'btn-white bg-white border' ?>">
            <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt (<?= (int) ($counts['pending'] ?? 0) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=approved') ?>" class="btn btn-sm <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-white bg-white border' ?>">
            <i class="bi bi-check-circle me-1"></i>Đã duyệt (<?= (int) ($counts['approved'] ?? 0) ?>)
        </a>
        <a href="<?= base_url('admin/review?status=hidden') ?>" class="btn btn-sm <?= $currentStatus === 'hidden' ? 'btn-secondary' : 'btn-white bg-white border' ?>">
            <i class="bi bi-eye-slash me-1"></i>Đã ẩn (<?= (int) ($counts['hidden'] ?? 0) ?>)
        </a>
    </div>

    <form action="<?= base_url('admin/review') ?>" method="GET" class="d-flex gap-2">
        <?php if ($currentStatus !== ''): ?>
            <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
        <?php endif; ?>
        <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control" placeholder="Tìm tên quán, người gửi, nội dung..." value="<?= e($filters['q'] ?? '') ?>">
            <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<!-- Bảng danh sách Review -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th width="65" class="ps-3">ID</th>
                    <th width="210">Quán &amp; Người đánh giá</th>
                    <th width="165">Điểm đánh giá</th>
                    <th>Nội dung nhận xét</th>
                    <th width="120" class="text-center">Trạng thái</th>
                    <th width="210" class="text-end pe-3">Hành động kiểm duyệt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            Không có đánh giá nào trong mục này.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $rv): ?>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">#<?= (int) $rv['id'] ?></td>
                            <td>
                                <a href="<?= base_url('place/show/' . (int) $rv['place_id']) ?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block">
                                    <?= e($rv['place_name']) ?>
                                </a>
                                <div class="small text-secondary mt-1">
                                    <i class="bi bi-person me-1"></i><?= e($rv['user_name']) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                </div>
                            </td>
                            <td>
                                <div><?= render_stars((float) $rv['rating_overall']) ?></div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                    Wifi: <strong><?= (int) $rv['rating_wifi'] ?></strong> •
                                    KG: <strong><?= (int) $rv['rating_space'] ?></strong> •
                                    Giá: <strong><?= (int) $rv['rating_price'] ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark"><?= nl2br(e($rv['content'])) ?></div>
                                <?php if (!empty($rv['image_path'])): ?>
                                    <div class="mt-2">
                                        <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh review" class="rounded border object-fit-cover" width="70" height="50">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($rv['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt
                                    </span>
                                <?php elseif ($rv['status'] === 'approved'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đã duyệt
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        <i class="bi bi-eye-slash-fill me-1"></i>Đã ẩn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <?php if ($rv['status'] !== 'approved'): ?>
                                        <form action="<?= base_url('admin/review/updateStatus/' . (int) $rv['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" title="Duyệt hiển thị">
                                                <i class="bi bi-check-lg me-1"></i>Duyệt
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($rv['status'] !== 'hidden'): ?>
                                        <form action="<?= base_url('admin/review/updateStatus/' . (int) $rv['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="hidden">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Ẩn đánh giá này">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="<?= base_url('admin/review/delete/' . (int) $rv['id']) ?>" method="POST" onsubmit="return confirm('Xóa vĩnh viễn đánh giá này và tính lại điểm quán?');">
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

<?= render_pagination($currentPage, $totalPages, 'admin/review') ?>
