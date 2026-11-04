<?php
// Trang chủ, do HomeController (index) render.
// Trang chủ: $districts, $heroPlace, $featuredPlaces, $favoriteIds, phân trang nổi bật.
?>
<div class="container py-4">
    <!-- Hero chia đôi: trái chữ + tìm kiếm, phải ảnh quán nổi bật. -->
    <section class="hero-split">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-3 mb-3" style="background: #F3F4F6; border: 1px solid #E5E7EB; color: #374151;">
                <i class="bi bi-geo-alt-fill text-warning"></i>
                <span class="small fw-semibold">Cộng đồng Sinh viên Đại học An Giang (AGU) • TP. Long Xuyên</span>
            </div>
            <h1 class="fw-bold mb-3" style="font-size: 2.25rem; line-height: 1.15; letter-spacing: -0.02em;">
                Tìm góc cà phê học bài lý tưởng tại Long Xuyên
            </h1>
            <p class="text-muted mb-4" style="font-size: 1.05rem;">
                Khám phá các quán cà phê yên tĩnh, nhiều ổ cắm điện, Wifi mạnh, giá hạt dẻ quanh khu vực Ung Văn Khiêm, Đông Xuyên, Mỹ Xuyên, Mỹ Quý.
            </p>
            <!-- Form tìm kiếm: GET sang place với q và district. -->
            <form action="<?= base_url('place') ?>" method="GET" class="bg-white p-3 rounded-3 border mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" class="form-control border-0 shadow-none" placeholder="Tên quán, đường...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Ô chọn phường: vòng lặp $districts, giá trị qua e() chống XSS. -->
                        <select name="district" class="form-select border-0 shadow-none text-secondary">
                            <option value="">Tất cả phường</option>
                            <?php foreach ($districts as $dist): ?>
                                <option value="<?= e($dist) ?>"><?= e($dist) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold">
                            <i class="bi bi-compass me-1"></i>Tìm quán
                        </button>
                    </div>
                </div>
            </form>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="small text-muted me-1"><i class="bi bi-funnel-fill me-1"></i>Gợi ý nhanh:</span>
                <!-- Gợi ý nhanh: mỗi tag là link sang place với bộ lọc chọn sẵn. -->
                <a href="<?= BASE_URL . '/place?amenity[]=wifi' ?>" class="hero-quick-tag">Wifi mạnh</a>
                <a href="<?= BASE_URL . '/place?amenity[]=outlet' ?>" class="hero-quick-tag">Nhiều ổ cắm</a>
                <a href="<?= BASE_URL . '/place?noise_level=yen_tinh' ?>" class="hero-quick-tag">Yên tĩnh</a>
                <a href="<?= BASE_URL . '/place?price_range=2' ?>" class="hero-quick-tag">Giá sinh viên</a>
            </div>
        </div>
        <!-- Ảnh hero: chỉ hiện khi $heroPlace không rỗng, bấm sang trang chi tiết quán đó. -->
        <?php if (!empty($heroPlace)): ?>
            <a href="<?= base_url('place/show/' . (int) $heroPlace['id']) ?>" class="text-decoration-none">
                <img src="<?= get_image_url($heroPlace['cover_image'] ?? null, 'place', (int) $heroPlace['id']) ?>" class="hero-split-img" alt="<?= e($heroPlace['name']) ?>">
                <div class="d-flex justify-content-between align-items-center mt-2 small">
                    <span class="fw-semibold text-dark"><?= e($heroPlace['name']) ?></span>
                    <span class="text-muted"><?= render_stars((float) $heroPlace['avg_rating']) ?> <?= number_format((float) $heroPlace['avg_rating'], 1) ?></span>
                </div>
            </a>
        <?php endif; ?>
    </section>

    <!-- Khối Quán nổi bật dưới hero; Xem tất cả sang place sort rating_desc. -->
    <section class="mb-4" id="featured">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="h4 fw-bold text-coffee mb-1"><i class="bi bi-trophy-fill text-accent me-2"></i>Quán nổi bật quanh Đại học An Giang</h2>
                <p class="text-muted small mb-0">Những tọa độ học bài &amp; họp nhóm tại TP. Long Xuyên được sinh viên AGU đánh giá cao nhất</p>
            </div>
            <a href="<?= base_url('place?sort=rating_desc') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="d-flex flex-column gap-4">
            <!-- Vòng lặp $featuredPlaces: mỗi quán 1 thẻ place-row. -->
            <?php foreach ($featuredPlaces as $place): ?>
                <?php
// Thẻ quán: $isOpen, $favoriteIds, get_card_amenities() tối đa 3 tiện ích.
                $isOpen = is_place_open($place['open_time'], $place['close_time']);
                $isFav = in_array((int) $place['id'], $favoriteIds, true);
                $cardAmenities = get_card_amenities($place);
                ?>
                <article class="place-row">
                    <a href="<?= base_url('place/show/' . (int) $place['id']) ?>">
                        <img src="<?= get_image_url($place['cover_image'] ?? null, 'place', (int) $place['id']) ?>" class="place-row-img" alt="<?= e($place['name']) ?>">
                    </a>
                    <div class="place-row-body">
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <span class="pill-amenity"><?= e($place['category_name']) ?></span>
                            <span class="<?= $isOpen ? 'badge-status-open' : 'badge-status-closed' ?>"><?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?></span>
                        </div>
                        <h3 class="h6 fw-bold mb-1">
                            <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none"><?= e($place['name']) ?></a>
                        </h3>
                        <p class="small text-muted mb-2 text-truncate"><i class="bi bi-geo-alt-fill me-1"></i><?= e($place['address']) ?></p>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($cardAmenities as $amenity): ?>
                                <span class="<?= e($amenity['class']) ?>"><?= e($amenity['label']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Cột phải thẻ quán: sao + số review đã duyệt, badge giá, tim, Xem chi tiết. -->
                    <div class="place-row-side">
                        <div>
                            <?= render_stars((float) $place['avg_rating']) ?>
                            <span class="small text-muted">(<?= (int) $place['total_reviews'] ?>)</span>
                        </div>
                        <div class="place-price-badge"><?= e(format_price_range_from_min_max($place['price_min'] ?? null, $place['price_max'] ?? null)) ?></div>
                        <div class="d-flex gap-2">
                            <!-- Nút tim: JS main.js bắt data-place-id gửi AJAX, không tải lại trang. -->
                            <button type="button" class="btn btn-sm btn-outline-coffee btn-favorite-toggle" data-place-id="<?= (int) $place['id'] ?>" title="Lưu vào quán yêu thích"><i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i></button>
                            <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="btn btn-sm btn-coffee">Xem chi tiết</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang nổi bật: chỉ hiện khi tổng số trang > 1, link có #featured. -->
        <?php if (($featuredTotalPages ?? 1) > 1): ?>
            <?php $fp = $featuredPage ?? 1; $ftp = $featuredTotalPages; $fStart = max(1, $fp - 2); $fEnd = min($ftp, $fp + 2); ?>
            <!-- Thanh phân trang: cửa sổ 5 trang quanh trang hiện tại, ... khi bỏ qua nhiều trang. -->
            <nav aria-label="Phân trang quán nổi bật"><ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $fp <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $fp > 1 ? e('?featured_page=' . ($fp - 1) . '#featured') : '#' ?>"><i class="bi bi-chevron-left"></i></a></li>
                <?php if ($fStart > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= e('?featured_page=1#featured') ?>">1</a></li>
                    <?php if ($fStart > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $fStart; $i <= $fEnd; $i++): ?>
                    <li class="page-item <?= $i === $fp ? 'active' : '' ?>"><a class="page-link" href="<?= e('?featured_page=' . $i . '#featured') ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <?php if ($fEnd < $ftp): ?>
                    <?php if ($fEnd < $ftp - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?= e('?featured_page=' . $ftp . '#featured') ?>"><?= $ftp ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $fp >= $ftp ? 'disabled' : '' ?>"><a class="page-link" href="<?= $fp < $ftp ? e('?featured_page=' . ($fp + 1) . '#featured') : '#' ?>"><i class="bi bi-chevron-right"></i></a></li>
            </ul></nav>
        <?php endif; ?>
    </section>
</div>
