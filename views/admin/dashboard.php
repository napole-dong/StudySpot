<?php
// Trang quản trị: Tổng quan — AdminDashboardController render
?>
<!-- Tiêu đề trang Tổng quan: trên cùng, dòng nhắc đổi theo $pendingReviews -->
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
                <!-- Nút Thêm quán mới: sang form thêm quán (admin/place/create) -->
        <a href="<?= base_url('admin/place/create') ?>" class="btn btn-sm btn-coffee rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i>Thêm quán mới
        </a>
                <!-- Nút Duyệt đánh giá: sang trang kiểm duyệt đã lọc status=pending ($pendingReviews) -->
        <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm btn-accent rounded-pill px-3 fw-semibold">
            <i class="bi bi-hourglass-split me-1"></i>Duyệt đánh giá (<?= (int) $pendingReviews ?>)
        </a>
    </div>
</div>

<!-- Hàng 4 thẻ KPI: cao bằng nhau, link ở đáy thẻ (d-flex flex-column + mt-auto) -->
<!-- Thẻ KPI Tổng quán: $totalPlaces, link đáy thẻ sang admin/place ($activePlaces) -->
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
                                        <!-- Thẻ KPI Đánh giá: $totalReviews, link đáy thẻ sang admin/review -->
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
                                        <!-- Thẻ KPI Chờ duyệt: $pendingReviews, link đáy thẻ sang admin/review?status=pending -->
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
                                        <!-- Thẻ KPI Thành viên: $totalUsers, link đáy thẻ sang admin/user -->
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

<!-- Bảng Top 5: dưới hàng KPI, full chiều rộng sau khi gỡ biểu đồ -->
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
                                                <!-- Nếu $topPlaces rỗng thì hiện “Chưa có dữ liệu quán.” -->
                        <?php if (empty($topPlaces)): ?>
                            <p class="text-center text-muted py-4 mb-0">Chưa có dữ liệu quán.</p>
                        <?php else: ?>
                                                        <!-- Vòng lặp Top 5: $topPlaces đã xếp theo điểm giảm dần, hạng hiển thị là $idx + 1 -->
                            <?php foreach ($topPlaces as $idx => $tp): ?>
                                <div class="d-flex align-items-center gap-3">
                                                                        <!-- Huy hiệu hạng: hạng 1 vàng cam, hạng 2 màu mực, còn lại xám nhạt -->
                                    <span class="badge rounded-pill px-2 py-1 flex-shrink-0" style="<?= $idx === 0 ? 'background:#D97706;color:#fff;' : ($idx === 1 ? 'background:#111827;color:#fff;' : 'background:#F3F4F6;color:#111827;') ?>">#<?= $idx + 1 ?></span>
                                    <img src="<?= get_image_url($tp['cover_image'] ?? null, 'place') ?>" alt="<?= e($tp['name']) ?>" class="rounded-2 object-fit-cover flex-shrink-0" width="56" height="44">
                                    <div class="flex-grow-1" style="min-width: 0;">
                                                                                <!-- Tên quán Top 5: bấm sang trang chi tiết phía người dùng (place/show/{id}) -->
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

<!-- Khối xem trước review chờ duyệt: chỉ hiện khi $recentPending không rỗng, nút Duyệt chỉ là link sang trang kiểm duyệt -->
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
                                        <!-- Vòng lặp review chờ duyệt: $recentPending tối đa 5 review, nội dung rút gọn 110 ký tự -->
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
