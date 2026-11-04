<?php
/**
 * Giao diện Chi tiết Quán học bài tại TP. Long Xuyên, An Giang (views/places/show.php)
 * Bao gồm:
 * - Slider ảnh, Thông tin địa chỉ & Phường thực tế tại Long Xuyên
 * - Trạng thái Đang mở cửa / Đã đóng cửa tính toán thật theo date('H:i:s')
 * - Điểm trung bình tổng + từng tiêu chí (AVG Wifi, Không gian, Giá cả) từ review đã approved
 * - Bản đồ Google Maps nhúng động theo tên + địa chỉ thật tại Long Xuyên
 * - Quán tương tự / Gợi ý cùng khu vực tại TP. Long Xuyên
 */
$placeId = (int) $place['id'];
$isOpen = is_place_open($place['open_time'], $place['close_time']);
$noiseInfo = format_noise_level($place['noise_level']);
$dynamicMapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode($place['name'] . ' ' . $place['address']) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
?>
<div class="container py-4">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
            <li class="breadcrumb-item d-none d-sm-inline"><a href="<?= base_url('place') ?>" class="text-decoration-none text-coffee">Khám phá Long Xuyên</a></li>
            <li class="breadcrumb-item d-none d-sm-inline"><a href="<?= base_url('place?district=' . urlencode($place['district'])) ?>" class="text-decoration-none text-coffee"><?= e($place['district']) ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 280px;" aria-current="page"><?= e($place['name']) ?></li>
        </ol>
    </nav>

    <!-- Header: tiêu đề + badge + nút, không còn thẻ card bao quanh -->
    <div class="page-head">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <a href="<?= base_url('place?category_id=' . (int) $place['category_id']) ?>" class="badge bg-coffee text-decoration-none px-3 py-2 rounded-pill"><?= e($place['category_name']) ?></a>
            <span class="<?= $isOpen ? 'badge-status-open' : 'badge-status-closed' ?> px-3 py-2"><i class="bi <?= $isOpen ? 'bi-door-open-fill' : 'bi-door-closed-fill' ?> me-1"></i><?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?> (<?= e(substr($place['open_time'], 0, 5)) ?> - <?= e(substr($place['close_time'], 0, 5)) ?>)</span>
            <?php if (is_late_night_friendly($place)): ?><span class="pill-late-night px-3 py-1"><i class="bi bi-lightning-charge-fill me-1"></i>Phù hợp học khuya</span><?php endif; ?>
            <?php if (is_student_price($place)): ?><span class="pill-amenity pill-quiet px-3 py-1">Giá sinh viên AGU</span><?php endif; ?>
        </div>
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
            <div>
                <h1 class="page-title"><?= e($place['name']) ?></h1>
                <p class="text-muted mb-2"><i class="bi bi-geo-alt-fill me-1"></i><?= e($place['address']) ?></p>
                <div class="d-flex flex-wrap align-items-center gap-3 small">
                    <div><?= render_stars((float) $place['avg_rating']) ?> <span class="text-muted ms-1">(<?= (int) $place['total_reviews'] ?> đánh giá đã duyệt)</span></div>
                    <span class="text-secondary">•</span>
                    <div class="fw-semibold"><i class="bi bi-cash-stack me-1"></i>Mức giá: <?= e(format_price_range_from_min_max($place['price_min'] ?? null, $place['price_max'] ?? null)) ?></div>
                </div>
                <?php if (!empty($place['google_rating']) || !empty($place['google_maps_place_url'])): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 small mt-2">
                    <span class="fw-semibold">Google:</span>
                    <?php if (!empty($place['google_rating'])): ?>
                    <span><?= render_stars((float) $place['google_rating']) ?></span>
                    <span class="text-muted"><?= number_format((float) $place['google_rating'], 1) ?><?php if (!empty($place['google_rating_count'])): ?> (<?= (int) $place['google_rating_count'] ?> đánh giá)<?php endif; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($place['google_maps_place_url'])): ?><a href="<?= e($place['google_maps_place_url']) ?>" target="_blank" rel="noopener" class="text-decoration-none">Xem trên Google Maps</a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 flex-shrink-0 flex-wrap">
                <button type="button" class="btn <?= $isFavorited ? 'btn-danger' : 'btn-outline-danger' ?> px-4 btn-favorite-toggle" data-place-id="<?= $placeId ?>" data-with-label="1"><i class="bi <?= $isFavorited ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i><span class="fav-label"><?= $isFavorited ? 'Đã lưu yêu thích' : 'Lưu vào yêu thích' ?></span></button>
                <a href="#review-form-section" class="btn btn-coffee px-4"><i class="bi bi-star-fill me-1 text-warning"></i>Viết đánh giá</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: SLIDER ẢNH + MÔ TẢ + FORM & DANH SÁCH REVIEW -->
        <div class="col-lg-8">
            <!-- 1. GALLERY: ảnh lớn trái, ảnh nhỏ phải -->
            <div class="gallery-grid">
                <?php if (empty($images)): ?>
                    <img src="<?= get_image_url(null, 'place', $placeId) ?>" class="gallery-main" style="grid-column: 1 / -1;" alt="<?= e($place['name']) ?>">
                <?php else: ?>
                    <img src="<?= get_image_url($images[0]['image_path'], 'place', $placeId) ?>" class="gallery-main" alt="<?= e($place['name']) ?>">
                    <div class="gallery-side">
                        <?php foreach (array_slice($images, 1, 2) as $img): ?>
                            <img src="<?= get_image_url($img['image_path'], 'place', $placeId) ?>" alt="<?= e($place['name']) ?>">
                        <?php endforeach; ?>
                        <?php if (count($images) === 1): ?>
                            <img src="<?= get_image_url($images[0]['image_path'], 'place', $placeId) ?>" alt="<?= e($place['name']) ?>">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. TIỆN ÍCH HỌC TẬP & MÔ TẢ CHI TIẾT -->
            <div class="card surface-card p-4 mb-4">
                <h2 class="h6 fw-bold text-coffee mb-3">
                    <i class="bi bi-card-checklist me-2"></i>Tiện ích học tập &amp; Không gian tại quán
                </h2>

                <?php if (!empty($place['amenities'])): ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($place['amenities'] as $am): ?>
                            <span class="pill-amenity px-3 py-2"><?= e($am['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-3">Chưa cập nhật tiện ích cho quán này.</p>
                <?php endif; ?>
                <p class="small text-muted mb-4">Mức độ tiếng ồn: <strong class="text-dark"><?= e($noiseInfo['label']) ?></strong></p>

                <h3 class="h6 fw-bold text-dark mb-2">Giới thiệu chi tiết</h3>
                <div class="text-secondary" style="line-height: 1.8;">
                    <?= nl2br(e($place['description'])) ?>
                </div>
            </div>

            <!-- 3. FORM VIẾT / SỬA ĐÁNH GIÁ (KÈM WIDGET CHỌN 5 NGÔI SAO & LIVE PREVIEW) -->
            <?php require ROOT_PATH . '/views/reviews/_form.php'; ?>

            <!-- 4. DANH SÁCH ĐÁNH GIÁ ĐÃ DUYỆT -->
            <?php require ROOT_PATH . '/views/reviews/_list.php'; ?>
        </div>

        <!-- CỘT PHẢI: ĐIỂM TRUNG BÌNH TỪNG TIÊU CHÍ (SQL AVG THẬT) + BẢN ĐỒ LONG XUYÊN -->
        <div class="col-lg-4"><div class="sticky-card d-flex flex-column gap-4">
            <!-- Khối Điểm trung bình tổng + từng tiêu chí từ AVG(rating_*) -->
            <div class="card surface-card p-4 mb-4">
                <h2 class="h6 fw-bold text-coffee mb-3">
                    <i class="bi bi-bar-chart-fill me-2"></i>Điểm đánh giá từ Sinh viên
                </h2>

                <div class="d-flex align-items-center gap-3 p-3 rounded-4 mb-3" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                    <div class="display-5 fw-bold text-coffee mb-0">
                        <?= number_format((float) $place['avg_rating'], 1) ?>
                    </div>
                    <div>
                        <div><?= render_stars((float) $place['avg_rating'], false) ?></div>
                        <div class="small text-muted">Từ <strong><?= (int) ($criteriaStats['total'] ?? $place['total_reviews']) ?></strong> đánh giá đã duyệt</div>
                    </div>
                </div>

                <!-- Thanh điểm 3 tiêu chí thành phần tính 100% bằng SQL AVG() -->
                <?php
                $wifiScore   = (float) ($criteriaStats['avg_wifi'] ?? 0.0);
                $spaceScore  = (float) ($criteriaStats['avg_space'] ?? 0.0);
                $priceScore  = (float) ($criteriaStats['avg_price'] ?? 0.0);

                $wifiPercent  = min(100, max(0, round(($wifiScore / 5) * 100, 1)));
                $spacePercent = min(100, max(0, round(($spaceScore / 5) * 100, 1)));
                $pricePercent = min(100, max(0, round(($priceScore / 5) * 100, 1)));
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">Tốc độ Wifi</span>
                        <strong class="text-coffee"><?= number_format($wifiScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress progress-warm">
                        <div class="progress-bar" role="progressbar" style="width: <?= $wifiPercent ?>%" aria-valuenow="<?= $wifiScore ?>" aria-valuemin="0" aria-valuemax="5"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">Không gian &amp; Bàn ghế</span>
                        <strong class="text-coffee"><?= number_format($spaceScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress progress-warm">
                        <div class="progress-bar" role="progressbar" style="width: <?= $spacePercent ?>%" aria-valuenow="<?= $spaceScore ?>" aria-valuemin="0" aria-valuemax="5"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-medium">Mức giá hợp lý</span>
                        <strong class="text-coffee"><?= number_format($priceScore, 1) ?> / 5.0</strong>
                    </div>
                    <div class="progress progress-warm">
                        <div class="progress-bar" role="progressbar" style="width: <?= $pricePercent ?>%" aria-valuenow="<?= $priceScore ?>" aria-valuemin="0" aria-valuemax="5"></div>
                    </div>
                </div>
            </div>

            <!-- Khối Thông tin giờ hoạt động & Bản đồ Google Maps thật tại TP. Long Xuyên -->
            <div class="card surface-card p-4 mb-4">
                <h2 class="h6 fw-bold text-coffee mb-3">
                    <i class="bi bi-geo-alt-fill me-2"></i>Vị trí tại TP. Long Xuyên &amp; Giờ mở cửa
                </h2>

                <ul class="list-unstyled small mb-3">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-clock me-2"></i>Khung giờ mở cửa:</span>
                        <span class="fw-bold"><?= e(substr($place['open_time'], 0, 5)) ?> - <?= e(substr($place['close_time'], 0, 5)) ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-activity me-2"></i>Hiện tại (<?= date('H:i') ?>):</span>
                        <span class="fw-bold" style="color: <?= $isOpen ? '#2D6A4F' : '#BC4749' ?>;">
                            <?= $isOpen ? 'Đang mở cửa đón khách' : 'Đã đóng cửa' ?>
                        </span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-pin-map me-2"></i>Khu vực:</span>
                        <span class="fw-semibold"><?= e($place['district']) ?>, Long Xuyên</span>
                    </li>
                </ul>

                <div class="ratio ratio-4x3 rounded-4 overflow-hidden border mb-2">
                    <iframe
                        src="https://maps.google.com/maps?q=<?= urlencode($place['name'] . ' ' . $place['address']) ?>&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ <?= e($place['name']) ?> tại TP. Long Xuyên"
                    ></iframe>
                </div>
                <div class="text-end">
                    <a
                        href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($place['name'] . ' ' . $place['address']) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="small text-coffee fw-semibold text-decoration-none"
                    >
                        <i class="bi bi-box-arrow-up-right me-1"></i>Mở chỉ đường trên Google Maps
                    </a>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- 5. QUÁN TƯƠNG TỰ / GỢI Ý CÙNG KHU VỰC TẠI TP. LONG XUYÊN (QUERY THẬT TỪ CSDL) -->
    <?php if (!empty($relatedPlaces)): ?>
        <section class="mt-5 pt-2">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                    <h2 class="h4 fw-bold text-coffee mb-1">
                        <i class="bi bi-signpost-split-fill text-accent me-2"></i>Quán tương tự &amp; Gợi ý cùng khu vực Long Xuyên
                    </h2>
                    <p class="text-muted small mb-0">
                        Các địa điểm khác thuộc <strong><?= e($place['district']) ?></strong> hoặc cùng danh mục <strong><?= e($place['category_name']) ?></strong>
                    </p>
                </div>
                <a href="<?= base_url('place?district=' . urlencode($place['district'])) ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                    Xem thêm tại <?= e($place['district']) ?> <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-3">
                <?php foreach ($relatedPlaces as $rel): ?>
                    <?php
                    $relOpen = is_place_open($rel['open_time'], $rel['close_time']);
                    $relFav = in_array((int) $rel['id'], $favoriteIds ?? [], true);
                    $relAmenities = get_card_amenities($rel);
                    ?>
                    <article class="place-row">
                        <a href="<?= base_url('place/show/' . (int) $rel['id']) ?>"><img src="<?= get_image_url($rel['cover_image'] ?? null, 'place', (int) $rel['id']) ?>" class="place-row-img" alt="<?= e($rel['name']) ?>"></a>
                        <div class="place-row-body">
                            <div class="d-flex flex-wrap gap-2 mb-1"><span class="pill-amenity"><?= e($rel['category_name']) ?></span><span class="<?= $relOpen ? 'badge-status-open' : 'badge-status-closed' ?>"><?= $relOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?></span></div>
                            <h3 class="h6 fw-bold mb-1"><a href="<?= base_url('place/show/' . (int) $rel['id']) ?>" class="text-dark text-decoration-none"><?= e($rel['name']) ?></a></h3>
                            <p class="small text-muted mb-2 text-truncate"><i class="bi bi-geo-alt-fill me-1"></i><?= e($rel['address']) ?></p>
                            <div class="d-flex flex-wrap gap-1"><?php foreach ($relAmenities as $amenity): ?><span class="<?= e($amenity['class']) ?>"><?= e($amenity['label']) ?></span><?php endforeach; ?></div>
                        </div>
                        <div class="place-row-side">
                            <div><?= render_stars((float) $rel['avg_rating']) ?> <span class="small text-muted">(<?= (int) $rel['total_reviews'] ?>)</span></div>
                            <div class="place-price-badge"><?= e(format_price_range_from_min_max($rel['price_min'] ?? null, $rel['price_max'] ?? null)) ?></div>
                            <div class="d-flex gap-2"><button type="button" class="btn btn-sm btn-outline-coffee btn-favorite-toggle" data-place-id="<?= (int) $rel['id'] ?>" title="Lưu vào quán yêu thích"><i class="bi <?= $relFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i></button><a href="<?= base_url('place/show/' . (int) $rel['id']) ?>" class="btn btn-sm btn-coffee">Xem chi tiết</a></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
