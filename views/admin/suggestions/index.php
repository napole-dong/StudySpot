<?php
// Trang admin duyệt đề xuất quán: $suggestions (kèm danh mục, người đề xuất), $pending.
?>
<!-- Tiêu đề trang đề xuất quán. -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-plus-circle me-2"></i>Đề xuất quán mới</h1>
        <p class="text-muted small mb-0">Thành viên đề xuất quán học bài mới. Duyệt sẽ tạo quán ngay ở trạng thái hiển thị. Đang chờ duyệt: <strong><?= (int) $pending ?></strong> đề xuất.</p>
    </div>
</div>

<!-- Bảng danh sách đề xuất: đang chờ duyệt xếp trước, mỗi hàng có nút Duyệt & tạo quán / Từ chối. -->
<div class="surface-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="min-width: 320px;">Quán được đề xuất</th>
                    <th>Người đề xuất</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suggestions)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox d-block mb-2" style="font-size: 1.6rem;"></i>
                            Chưa có đề xuất nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suggestions as $sug): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?= e($sug['name']) ?></div>
                                <div class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i><?= e($sug['address']) ?>, <?= e($sug['district']) ?>
                                    <?php if (!empty($sug['category_name'])): ?>
                                        · <?= e($sug['category_name']) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($sug['description'])): ?>
                                    <div class="small text-dark mt-1" style="line-height: 1.55;"><?= e(mb_strimwidth((string) $sug['description'], 0, 160, '…')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark"><?= e($sug['user_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.76rem;"><?= date('d/m/Y H:i', strtotime($sug['created_at'])) ?></div>
                            </td>
                            <td>
                                <?php if ($sug['status'] === 'pending'): ?>
                                    <span class="badge rounded-pill text-bg-warning">Chờ duyệt</span>
                                <?php elseif ($sug['status'] === 'approved'): ?>
                                    <span class="badge rounded-pill text-bg-success">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">Đã từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($sug['status'] === 'pending'): ?>
                                    <div class="d-inline-flex gap-1">
                                        <!-- Duyệt: tạo quán thật đang hiển thị và thông báo cho người đề xuất. -->
                                        <form method="post" action="<?= base_url('admin/suggestion/approve/' . (int) $sug['id']) ?>" onsubmit="return confirm('Duyệt đề xuất và tạo quán này ngay?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3">
                                                <i class="bi bi-check-lg me-1"></i>Duyệt &amp; tạo quán
                                            </button>
                                        </form>
                                        <!-- Từ chối: chỉ chốt trạng thái, không tạo quán. -->
                                        <form method="post" action="<?= base_url('admin/suggestion/reject/' . (int) $sug['id']) ?>" onsubmit="return confirm('Từ chối đề xuất này?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                Từ chối
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($sug['status'] === 'approved' && !empty($sug['created_place_id'])): ?>
                                    <a href="<?= base_url('place/show/' . (int) $sug['created_place_id']) ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                                        <i class="bi bi-shop me-1"></i>Xem quán
                                    </a>
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
