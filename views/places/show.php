<?php
/**
 * Giao diện Chi tiết Quán học bài (views/places/show.php)
 * Bao gồm: Slider ảnh, Thông tin, Tiện ích badge, Giờ mở cửa tự động, Google Maps iframe,
 * Điểm trung bình tổng + từng tiêu chí, Nút Yêu thích AJAX, Form & Danh sách Review phân trang.
 */
$placeId = (int) $place['id'];
$isOpen = is_place_open($place['open_time'], $place['close_time']);
$noiseInfo = format_noise_level($place['noise_level']);
?>
<div class="container py-4">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('place') ?>" class="text-decoration-none text-coffee">Khám phá quán</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('place?category_id=' . (int) $place['category_id']) ?>" class="text-decoration-none text-coffee"><?= e($place['category_name']) ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 260px;" aria-current="page"><?= e($place['name']) ?></li>
        </ol>
    </nav>

    <!-- Header thông tin chính của quán -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-coffee px-3 py-1"><?= e($place['category_name']) ?></span>

                    <!-- Badge Giờ mở cửa tự động so sánh giờ hiện tại -->
                    <span class="badge <?= $isOpen ? 'bg-success' : 'bg-secondary' ?> px-3 py-1">
                        <i class="bi <?= $isOpen ? 'bi-door-open-fill' : 'bi-door-closed-fill' ?> me-1"></i>
                        <?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?>
                        (<?= e(substr($place['open_time'], 0, 5)) ?> - <?= e(substr($place['close_time'], 0, 5)) ?>)
                    </span>

                    <!-- Các Tag tự động nâng cao -->
                    <?php if (is_deadline_friendly($place)): ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                            <i class="bi bi-lightning-charge-fill me-1"></i>Phù hợp chạy deadline
                        </span>
                    <?php endif; ?>

                    <?php if (is_student_price($place)): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                            <i class="bi bi-piggy-bank-fill me-1"></i>Giá sinh viên
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="h3 fw-bold text-dark mb-2"><?= e($place['name']) ?></h1>

                <p class="text-muted mb-2">
                    <i class="bi bi-geo-alt-fill text-coffee me-1"></i>
                    <?= e($place['address']) ?>, <strong><?= e($place['district']) ?></strong>
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3 small">
                    <div>
                        <?= render_stars((float) $place['avg_rating']) ?>
                        <span class="text-muted ms-1">(<?= (int) $place['total_reviews'] ?> đánh giá đã duyệt)</span>
                    </div>
                    <span class="text-secondary">•</span>
                    <div class="fw-semibold text-coffee">
                        <i class="bi bi-cash-stack me-1"></i>Mức giá: <?= e(format_price_range((int) $place['price_range'])) ?>
                    </div>
                </div>
            </div>

            <!-- Nút Yêu thích AJAX & Chia sẻ -->
            <div class="d-flex gap-2 flex-shrink-0">
                <button
                    type="button"
                    class="btn <?= $isFavorited ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill px-4 btn-favorite-toggle"
                    data-place-id="<?= $placeId ?>"
                    data-with-label="1"
                >
                    <i class="bi <?= $isFavorited ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
                    <span class="fav-label"><?= $isFavorited ? 'Đã lưu yêu thích' : 'Lưu vào Yêu thích' ?></span>
                </button>
                <a href="#review-form-section" class="btn btn-coffee rounded-pill px-4">
                    <i class="bi bi-star me-1"></i>Viết đánh giá
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: SLIDER ẢNH + MÔ TẢ + FORM & DANH SÁCH REVIEW -->
        <div class="col-lg-8">
            <!-- 1. SLIDER ẢNH CỦA QUÁN (Bootstrap 5 Carousel) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <?php if (empty($images)): ?>
                    <img src="<?= get_image_url(null, 'place') ?>" class="w-100 object-fit-cover" style="height: 380px;" alt="<?= e($place['name']) ?>">
                <?php else: ?>
                    <div id="placeGalleryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <?php if (count($images) > 1): ?>
                            <div class="carousel-indicators">
                                <?php foreach ($images as $idx => $img): ?>
                                    <button type="button" data-bs-target="#placeGalleryCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="carousel-inner">
                            <?php foreach ($images as $idx => $img): ?>
                                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                    <img src="<?= get_image_url($img['image_path'], 'place') ?>" class="d-block w-100 object-fit-cover" style="height: 380px;" alt="<?= e($place['name']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#placeGalleryCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Trước</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#placeGalleryCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Tiếp</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. TIỆN ÍCH DẠNG BADGE & MÔ TẢ CHI TIẾT -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h2 class="h5 fw-bold text-coffee mb-3">
                    <i class="bi bi-card-checklist me-2"></i>Tiện ích học tập &amp; Giới thiệu không gian
                </h2>

                <div class="row g-2 mb-4">
                    <div class="col-6 col-sm-3">
                        <div class="p-3 rounded-3 border text-center <?= !empty($place['has_wifi']) ? 'bg-info-subtle border-info-subtle' : 'bg-light text-muted' ?>">
                            <i class="bi bi-wifi fs-4 d-block mb-1 <?= !empty($place['has_wifi']) ? 'text-info' : '' ?>"></i>
                            <span class="small fw-semibold"><?= !empty($place['has_wifi']) ? 'Wifi mạnh' : 'Không có Wifi' ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 rounded-3 border text-center <?= !empty($place['has_outlet']) ? 'bg-warning-subtle border-warning-subtle' : 'bg-light text-muted' ?>">
                            <i class="bi bi-plug-fill fs-4 d-block mb-1 <?= !empty($place['has_outlet']) ? 'text-warning-emphasis' : '' ?>"></i>
                            <span class="small fw-semibold"><?= !empty($place['has_outlet']) ? 'Nhiều ổ cắm' : 'Ít ổ cắm' ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 rounded-3 border text-center <?= !empty($place['has_ac']) ? 'bg-primary-subtle border-primary-subtle' : 'bg-light text-muted' ?>">
                            <i class="bi bi-snow fs-4 d-block mb-1 <?= !empty($place['has_ac']) ? 'text-primary' : '' ?>"></i>
                            <span class="small fw-semibold"><?= !empty($place['has_ac']) ? 'Có máy lạnh' : 'Không gian mở' ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 rounded-3 border text-center <?= e($noiseInfo['class']) ?>">
                            <i class="bi <?= e($noiseInfo['icon']) ?> fs-4 d-block mb-1"></i>
                            <span class="small fw-semibold"><?= e($noiseInfo['label']) ?></span>
                        </div>
                    </div>
                </div>

                <h3 class="h6 fw-bold text-dark mb-2">Mô tả chi tiết</h3>
                <div class="text-secondary" style="line-height: 1.75;">
                    <?= nl2br(e($place['description'])) ?>
                </div>
            </div>

            <!-- 3. FORM VIẾT / SỬA ĐÁNH GIÁ -->
            <?php require ROOT_PATH . '/views/reviews/_form.php'; ?>

            <!-- 4. DANH SÁCH ĐÁNH GIÁ ĐÃ DUYỆT -->
            <?php require ROOT_PATH . '/views/reviews/_list.php'; ?>
        </div>

        <!-- CỘT PHẢI: ĐIỂM TRUNG BÌNH TỪNG TIÊU CHÍ + BẢN ĐỒ GOOGLE MAPS IFRAME -->
        <div class="col-lg-4">
            <!-- Khối Điểm trung bình tổng + từng tiêu chí -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-3">
                    <i class="bi bi-bar-chart-fill me-2"></i>Điểm đánh giá chi tiết
                </h2>

                <div class="d-flex align-items-center gap-3 bg-light-warm p-3 rounded-4 mb-3 border">
                    <div class="display-5 fw-bold text-coffee mb-0">
                        <?= number_format((float) $place['avg_rating'], 1) ?>
                    </div>
                    <div>
                        <div><?= render_stars((float) $place['avg_rating'], false) ?></div>
                        <div class="small text-muted">Dựa trên <strong><?= (int) $place['total_reviews'] ?></strong> đánh giá đã duyệt</div>
                    </div>
                </div>

                <!-- Thanh điểm 3 tiêu chí thành phần -->
                <?php
                $wifiScore = (float) ($criteriaStats['avg_wifi'] ?? 0);
                $spaceScore = (float) ($criteriaStats['avg_space'] ?? 0);
                $priceScore = (float) ($criteriaStats['avg_price'] ?? 0);
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bi bi-wifi text-info me-1"></i>Tốc độ Wifi</span>
                        <strong><?= number_format($wifiScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?= ($wifiScore / 5) * 100 ?>%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bi bi-lamp text-coffee me-1"></i>Không gian &amp; Bàn ghế</span>
                        <strong><?= number_format($spaceScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?= ($spaceScore / 5) * 100 ?>%"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bi bi-piggy-bank text-success me-1"></i>Mức giá hợp lý</span>
                        <strong><?= number_format($priceScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= ($priceScore / 5) * 100 ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Khối Thông tin giờ hoạt động & Bản đồ Google Maps nhúng iframe -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h2 class="h6 fw-bold text-uppercase text-coffee mb-3">
                    <i class="bi bi-geo-alt-fill me-2"></i>Vị trí &amp; Giờ mở cửa
                </h2>

                <ul class="list-unstyled small mb-3">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-clock me-2"></i>Giờ hoạt động:</span>
                        <span class="fw-semibold"><?= e(substr($place['open_time'], 0, 5)) ?> - <?= e(substr($place['close_time'], 0, 5)) ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-activity me-2"></i>Trạng thái hiện tại:</span>
                        <span class="fw-bold <?= $isOpen ? 'text-success' : 'text-secondary' ?>">
                            <?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?>
                        </span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-pin-map me-2"></i>Khu vực:</span>
                        <span class="fw-semibold"><?= e($place['district']) ?></span>
                    </li>
                </ul>

                <?php
                $mapUrl = !empty($place['google_map_url'])
                    ? $place['google_map_url']
                    : 'https://www.google.com/maps?q=' . urlencode($place['address'] . ', ' . $place['district'] . ', TP.HCM') . '&output=embed';
                ?>
                <div class="ratio ratio-4x3 rounded-3 overflow-hidden border">
                    <iframe
                        src="<?= e($mapUrl) ?>"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ <?= e($place['name']) ?>"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
