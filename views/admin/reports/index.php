<?php
// Trang admin xử lý báo cáo vi phạm: $reports (kèm review, quán, tác giả, người báo cáo), $pending.
?>
<!-- Tiêu đề trang báo cáo vi phạm. -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-flag me-2"></i>Báo cáo vi phạm</h1>
        <p class="text-muted small mb-0">Thành viên báo cáo các đánh giá vi phạm. Đang chờ xử lý: <strong><?= (int) $pending ?></strong> báo cáo.</p>
    </div>
</div>

<!-- Bảng danh sách báo cáo: báo cáo đang chờ xếp trước, mỗi hàng có nút Ẩn đánh giá / Bỏ qua. -->
<div class="surface-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="min-width: 300px;">Đánh giá bị báo cáo</th>
                    <th>Người báo cáo</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-flag d-block mb-2" style="font-size: 1.6rem;"></i>
                            Chưa có báo cáo nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $rp): ?>
                        <tr>
                            <td>
                                <div class="small text-dark" style="line-height: 1.55;"><?= e(mb_strimwidth((string) $rp['review_content'], 0, 140, '…')) ?></div>
                                <div class="small text-muted mt-1">
                                    Quán: <a href="<?= base_url('place/show/' . (int) $rp['place_id']) ?>" class="text-decoration-none"><?= e($rp['place_name']) ?></a>
                                    · Tác giả: <?= e($rp['author_name']) ?>
                                    <?php if ($rp['review_status'] === 'hidden'): ?>
                                        · <span class="text-secondary fw-semibold">Đánh giá đã bị ẩn</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark"><?= e($rp['reporter_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.76rem;"><?= date('d/m/Y H:i', strtotime($rp['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="small text-dark"><?= e(ReviewReport::REASONS[$rp['reason']] ?? $rp['reason']) ?></div>
                                <?php if (!empty($rp['note'])): ?>
                                    <div class="small text-muted fst-italic">"<?= e($rp['note']) ?>"</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($rp['status'] === 'pending'): ?>
                                    <span class="badge rounded-pill text-bg-warning">Chờ xử lý</span>
                                <?php elseif ($rp['status'] === 'resolved'): ?>
                                    <span class="badge rounded-pill text-bg-success">Đã xử lý</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">Đã bỏ qua</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($rp['status'] === 'pending'): ?>
                                    <div class="d-inline-flex gap-1">
                                        <!-- Ẩn luôn review vi phạm: tự cập nhật điểm quán, thông báo cho tác giả và người báo cáo. -->
                                        <form method="post" action="<?= base_url('admin/report/hide/' . (int) $rp['id']) ?>" onsubmit="return confirm('Ẩn đánh giá này khỏi trang quán?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                                                <i class="bi bi-eye-slash me-1"></i>Ẩn đánh giá
                                            </button>
                                        </form>
                                        <!-- Bỏ qua: review không vi phạm, vẫn hiển thị bình thường. -->
                                        <form method="post" action="<?= base_url('admin/report/dismiss/' . (int) $rp['id']) ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                Bỏ qua
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
