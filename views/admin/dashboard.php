<?php
/**
 * Giao diện Admin Dashboard — Phong cách Warm Study Cafe (views/admin/dashboard.php)
 * Bao gồm: 4 thẻ KPI tổng quan thực tế từ CSDL,
 * Bảng Top 5 quán điểm cao nhất tại TP. Long Xuyên & Danh sách nhanh Review chờ duyệt.
 */
?>
<div class="page-head d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
        <h1 class="page-title">Tổng quan</h1>
        <p class="text-muted small mb-0">
            <?php if (!empty($pendingReviews)): ?>
                Có <?= (int) $pendingReviews ?> đánh giá đang chờ duyệt &mdash; xử lý trước nhé.
            <?php else: ?>
                Không còn đánh giá chờ duyệt.
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= base_url('admin/place/create') ?>" class="btn btn-sm btn-coffee rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i>Thêm quán mới
        </a>
        <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm btn-accent rounded-pill px-3 fw-semibold">
            <i class="bi bi-hourglass-split me-1"></i>Duyệt đánh giá (<?= (int) $pendingReviews ?>)
        </a>
    </div>
</div>

<!-- 1. BỐN THẺ KPI THỐNG KÊ TỔNG QUAN (100% COUNT(*) TỪ MYSQL) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="surface-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Tổng quán</div>
                    <div class="h2 fw-bold mb-0 text-coffee"><?= number_format((int) $totalPlaces) ?></div>
                </div>
                <div class="rounded-4 p-3 fs-3" style="background: #F3F4F6; color: #111827;">
                    <i class="bi bi-shop"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/place') ?>" class="small text-decoration-none text-secondary mt-auto pt-2">
                Đang hiển thị: <strong><?= number_format((int) ($activePlaces ?? $totalPlaces)) ?></strong> quán <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="surface-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Đánh giá</div>
                    <div class="h2 fw-bold mb-0" style="color: #111827;"><?= number_format((int) $totalReviews) ?></div>
                </div>
                <div class="rounded-4 p-3 fs-3" style="background: #F3F4F6; color: #111827;">
                    <i class="bi bi-chat-square-quote-fill"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/review') ?>" class="small text-decoration-none text-secondary mt-auto pt-2">
                Quản lý toàn bộ review <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="surface-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Chờ duyệt</div>
                    <div class="h2 fw-bold mb-0" style="color: #D97706;"><?= number_format((int) $pendingReviews) ?></div>
                </div>
                <div class="rounded-4 p-3 fs-3" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/review?status=pending') ?>" class="small text-decoration-none fw-semibold mt-auto pt-2" style="color: #D97706;">
                Kiểm duyệt ngay <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="surface-card p-4 h-100 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Thành viên</div>
                    <div class="h2 fw-bold mb-0" style="color: #111827;"><?= number_format((int) $totalUsers) ?></div>
                </div>
                <div class="rounded-4 p-3 fs-3" style="background: #F3F4F6; color: #111827;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <a href="<?= base_url('admin/user') ?>" class="small text-decoration-none text-secondary mt-auto pt-2">
                Quản lý tài khoản <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- 2. BẢNG TOP 5 QUÁN ĐIỂM CAO NHẤT -->
<div class="row g-4 mb-4">
    <!-- Top 5 quán điểm cao nhất tại Long Xuyên -->
    <div class="col-12">
        <div class="surface-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h6 fw-bold text-coffee mb-1">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 quán điểm cao
                    </h2>
                    <p class="small text-muted mb-0">Theo điểm đánh giá trung bình</p>
                </div>
                <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">Tất cả</a>
            </div>

            <div class="d-flex flex-column gap-3">
                        <?php if (empty($topPlaces)): ?>
                            <p class="text-center text-muted py-4 mb-0">Chưa có dữ liệu quán.</p>
                        <?php else: ?>
                            <?php foreach ($topPlaces as $idx => $tp): ?>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge rounded-pill px-2 py-1 flex-shrink-0" style="<?= $idx === 0 ? 'background:#D97706;color:#fff;' : ($idx === 1 ? 'background:#111827;color:#fff;' : 'background:#F3F4F6;color:#111827;') ?>">#<?= $idx + 1 ?></span>
                                    <img src="<?= get_image_url($tp['cover_image'] ?? null, 'place') ?>" alt="<?= e($tp['name']) ?>" class="rounded-2 object-fit-cover flex-shrink-0" width="56" height="44">
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <a href="<?= base_url('place/show/' . (int) $tp['id']) ?>" class="fw-bold text-dark text-decoration-none d-block" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3;"><?= e($tp['name']) ?></a>
                                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-geo-alt"></i> <?= e($tp['district']) ?> • <?= (int) $tp['total_reviews'] ?> đánh giá</span>
                                    </div>
                                    <div class="text-end flex-shrink-0" style="white-space: nowrap;"><?= render_stars((float) $tp['avg_rating']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
        </div>
    </div>
</div>

<!-- 3. XEM TRƯỚC TỐI ĐA 5 REVIEW ĐANG CHỜ KIỂM DUYỆT (CHỈ ĐỌC, ĐIỀU HƯỚNG SANG TRANG KIỂM DUYỆT) -->
<?php if (!empty($recentPending)): ?>
    <div class="surface-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-coffee mb-1">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>Đánh giá chờ duyệt (<?= count($recentPending) ?>)
                </h2>
                <p class="small text-muted mb-0">Duyệt tại trang Đánh giá</p>
            </div>
            <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm btn-accent rounded-pill px-3 fw-semibold">
                Đi đến trang Kiểm duyệt Review <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table admin-table table-hover align-middle small mb-0">
                <thead>
                    <tr>
                        <th width="65">ID</th>
                        <th width="220">Quán &amp; sinh viên</th>
                        <th width="140">Điểm số</th>
                        <th>Nội dung nhận xét</th>
                        <th width="120">Ngày gửi</th>
                        <th width="110" class="text-end">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPending as $rv): ?>
                        <tr>
                            <td class="fw-semibold text-muted">#<?= (int) $rv['id'] ?></td>
                            <td>
                                <a href="<?= base_url('place/show/' . (int) $rv['place_id']) ?>" class="fw-bold text-dark text-decoration-none d-block">
                                    <?= e($rv['place_name']) ?>
                                </a>
                                <span class="text-muted"><i class="bi bi-person me-1"></i><?= e($rv['user_name']) ?></span>
                            </td>
                            <td><?= render_stars((float) $rv['rating_overall']) ?></td>
                            <td class="text-secondary"><?= e(truncate_text($rv['content'], 110)) ?></td>
                            <td class="text-muted"><?= date('d/m/Y', strtotime($rv['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-2 py-1" title="Kiểm duyệt đánh giá này">
                                    <i class="bi bi-shield-check me-1"></i>Duyệt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
