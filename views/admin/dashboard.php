<?php
/**
 * Giao diện Admin Dashboard hoàn chỉnh (views/admin/dashboard.php)
 * Bao gồm: 4 thẻ KPI tổng quan, Biểu đồ Chart.js số review theo tháng & Bảng Top 5 quán điểm cao nhất
 */
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-speedometer2 text-coffee me-2"></i>Tổng quan Hệ thống StudySpot</h1>
        <p class="text-muted small mb-0">Số liệu thống kê thời gian thực về địa điểm học bài, đánh giá sinh viên và thành viên</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/place/create') ?>" class="btn btn-sm btn-coffee">
            <i class="bi bi-plus-lg me-1"></i>Thêm quán mới
        </a>
        <a href="<?= base_url('admin/review?status=pending') ?>" class="btn btn-sm btn-warning fw-semibold">
            <i class="bi bi-hourglass-split me-1"></i>Duyệt Review (<?= (int) $pendingReviews ?>)
        </a>
    </div>
</div>

<!-- 1. BỐN THẺ THỐNG KÊ TỔNG QUAN -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Tổng số Quán học bài</div>
                    <div class="h2 fw-bold mb-1 text-coffee"><?= (int) $totalPlaces ?></div>
                    <a href="<?= base_url('admin/place') ?>" class="small text-decoration-none text-secondary">
                        Quản lý quán <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="rounded-4 bg-light p-3 text-coffee fs-3">
                    <i class="bi bi-shop"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Tổng số Đánh giá</div>
                    <div class="h2 fw-bold mb-1 text-primary"><?= (int) $totalReviews ?></div>
                    <a href="<?= base_url('admin/review') ?>" class="small text-decoration-none text-secondary">
                        Xem tất cả <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="rounded-4 bg-primary-subtle p-3 text-primary fs-3">
                    <i class="bi bi-chat-square-quote-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Review Chờ duyệt</div>
                    <div class="h2 fw-bold mb-1 text-warning-emphasis"><?= (int) $pendingReviews ?></div>
                    <a href="<?= base_url('admin/review?status=pending') ?>" class="small text-decoration-none text-warning-emphasis fw-semibold">
                        Kiểm duyệt ngay <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="rounded-4 bg-warning-subtle p-3 text-warning-emphasis fs-3">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Tổng số Người dùng</div>
                    <div class="h2 fw-bold mb-1 text-success"><?= (int) $totalUsers ?></div>
                    <a href="<?= base_url('admin/user') ?>" class="small text-decoration-none text-secondary">
                        Quản lý thành viên <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="rounded-4 bg-success-subtle p-3 text-success fs-3">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. BIỂU ĐỒ CHART.JS & BẢNG TOP 5 QUÁN ĐIỂM CAO NHẤT -->
<div class="row g-4">
    <!-- Biểu đồ số lượng Review theo tháng -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-0">
                    <i class="bi bi-bar-chart-line-fill me-2"></i>Biểu đồ Số lượng Review theo tháng
                </h2>
                <span class="badge bg-light text-secondary border">6 tháng gần nhất</span>
            </div>
            <div style="position: relative; height: 290px;">
                <canvas id="monthlyReviewsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 quán điểm cao nhất -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-0">
                    <i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Quán Điểm Cao Nhất
                </h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hạng</th>
                            <th>Tên quán &amp; Khu vực</th>
                            <th class="text-end">Điểm TB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPlaces as $idx => $tp): ?>
                            <tr>
                                <td width="45">
                                    <span class="badge <?= $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary' : 'bg-light text-dark border') ?> rounded-pill">
                                        #<?= $idx + 1 ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('place/show/' . (int) $tp['id']) ?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block">
                                        <?= e($tp['name']) ?>
                                    </a>
                                    <span class="text-muted"><?= e($tp['district']) ?> • <?= (int) $tp['total_reviews'] ?> đánh giá</span>
                                </td>
                                <td class="text-end">
                                    <?= render_stars((float) $tp['avg_rating']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Nhúng thư viện Chart.js từ CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('monthlyReviewsChart');
    if (!ctx || typeof Chart === 'undefined') {
        return;
    }

    const labels = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($chartValues, JSON_UNESCAPED_UNICODE) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Số lượng đánh giá (Reviews)',
                data: values,
                backgroundColor: 'rgba(111, 78, 55, 0.82)',
                borderColor: '#4b3424',
                borderWidth: 1,
                borderRadius: 8,
                maxBarThickness: 44
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
