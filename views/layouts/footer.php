<?php
/**
 * Thành phần Footer dùng chung cho giao diện người dùng StudySpot
 */
?>
<footer class="bg-dark text-light pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 fw-bold fs-4 text-white mb-3">
                    <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                        <i class="bi bi-cup-hot-fill"></i>
                    </span>
                    <span>StudySpot</span>
                </div>
                <p class="text-secondary small mb-3">
                    <?= e(SITE_SLOGAN) ?>. Tìm kiếm nhanh không gian yên tĩnh, Wifi tốc độ cao, nhiều ổ cắm điện và mức giá hợp túi tiền sinh viên.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-secondary-subtle text-dark"><i class="bi bi-wifi me-1"></i>Wifi mạnh</span>
                    <span class="badge bg-secondary-subtle text-dark"><i class="bi bi-plug me-1"></i>Nhiều ổ cắm</span>
                    <span class="badge bg-secondary-subtle text-dark"><i class="bi bi-volume-mute me-1"></i>Yên tĩnh</span>
                    <span class="badge bg-secondary-subtle text-dark"><i class="bi bi-piggy-bank me-1"></i>Giá sinh viên</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="text-uppercase fw-bold mb-3 text-warning">Khám phá nhanh</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= base_url('place') ?>" class="text-secondary text-decoration-none hover-white">Tất cả địa điểm</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?has_wifi=1') ?>" class="text-secondary text-decoration-none hover-white">Quán Wifi mạnh</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?has_outlet=1') ?>" class="text-secondary text-decoration-none hover-white">Nhiều ổ cắm điện</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?noise_level=yen_tinh') ?>" class="text-secondary text-decoration-none hover-white">Không gian yên tĩnh</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?price_range=2') ?>" class="text-secondary text-decoration-none hover-white">Quán dưới 50.000đ</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="text-uppercase fw-bold mb-3 text-warning">Thông tin đồ án</h6>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><i class="bi bi-journal-code me-2"></i>Học phần: Công nghệ Web – PHP (SEE518)</li>
                    <li class="mb-2"><i class="bi bi-layers me-2"></i>Kiến trúc: Mini-MVC OOP thuần + PDO + Bootstrap 5</li>
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Khu vực: TP. Hồ Chí Minh (Gần các cụm Đại học)</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
            <div>&copy; <?= date('Y') ?> <strong><?= e(SITE_NAME) ?></strong>. Xây dựng phục vụ cộng đồng sinh viên học tập.</div>
            <div class="mt-2 mt-md-0">Thiết kế tối ưu cho cả Điện thoại &amp; Máy tính.</div>
        </div>
    </div>
</footer>
