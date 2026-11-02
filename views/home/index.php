<?php
/**
 * Giao diện Trang chủ StudySpot hoàn chỉnh (views/home/index.php)
 */
?>
<div class="container py-4">
    <!-- 1. HERO BANNER + Ô TÌM KIẾM NHANH + NHÓM TAG NHANH -->
    <div class="hero-banner p-4 p-lg-5 mb-5 shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-mortarboard-fill me-1"></i>Cộng đồng Sinh viên Review Học Tập
                </span>
                <h1 class="display-6 fw-bold mb-3">Tìm góc học bài &amp; chạy deadline lý tưởng cùng StudySpot</h1>
                <p class="lead opacity-90 mb-4">
                    Khám phá hàng loạt quán cà phê sách, co-working space và quán mở xuyên đêm 24/7 gần trường đại học với đánh giá thực tế về tốc độ Wifi, ổ cắm điện và độ yên tĩnh.
                </p>

                <!-- Form tìm kiếm nhanh trên Banner -->
                <form action="<?= base_url('place') ?>" method="GET" class="bg-white p-2 rounded-4 shadow-sm mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-coffee"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-0 shadow-none" placeholder="Nhập tên quán, tên đường (D2, Sư Vạn Hạnh)...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="district" class="form-select border-0 shadow-none text-secondary">
                                <option value="">Tất cả khu vực</option>
                                <?php foreach ($districts as $dist): ?>
                                    <option value="<?= e($dist) ?>"><?= e($dist) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-coffee w-100 rounded-3 py-2 fw-semibold">
                                <i class="bi bi-compass me-1"></i>Tìm quán ngay
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Nhóm Tag nhanh theo đúng đặc tả -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="small text-white-50 me-1"><i class="bi bi-tags-fill me-1"></i>Gợi ý nhanh:</span>
                    <a href="<?= base_url('place?has_wifi=1') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                        <i class="bi bi-wifi text-info me-1"></i>Wifi mạnh
                    </a>
                    <a href="<?= base_url('place?has_outlet=1') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                        <i class="bi bi-plug-fill text-warning me-1"></i>Nhiều ổ cắm
                    </a>
                    <a href="<?= base_url('place?noise_level=yen_tinh') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                        <i class="bi bi-volume-mute-fill text-success me-1"></i>Yên tĩnh
                    </a>
                    <a href="<?= base_url('place?price_range=2') ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                        <i class="bi bi-piggy-bank-fill text-danger me-1"></i>Dưới 50k
                    </a>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block text-center">
                <img src="<?= get_image_url(null, 'place') ?>" alt="StudySpot Banner" class="img-fluid rounded-4 shadow" style="max-height: 260px;">
            </div>
        </div>
    </div>

    <!-- 2. KHÁM PHÁ THEO DANH MỤC LOẠI HÌNH -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="h4 fw-bold text-coffee mb-1"><i class="bi bi-grid-1x2-fill me-2"></i>Danh mục không gian học tập</h2>
                <p class="text-muted small mb-0">Chọn nhanh mô hình quán phù hợp với nhu cầu ôn thi hoặc làm việc nhóm của bạn</p>
            </div>
        </div>
        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-4 col-lg">
                    <a href="<?= base_url('place?category_id=' . (int) $cat['id']) ?>" class="card h-100 border-0 shadow-sm rounded-4 text-decoration-none place-card p-3 text-center bg-white">
                        <div class="brand-badge rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center text-white">
                            <i class="bi bi-cup-hot"></i>
                        </div>
                        <div class="fw-bold text-dark small mb-1"><?= e($cat['name']) ?></div>
                        <span class="badge bg-light text-secondary border"><?= (int) ($cat['active_place_count'] ?? 0) ?> địa điểm</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 3. QUÁN NỔI BẬT (ĐIỂM ĐÁNH GIÁ CAO NHẤT) -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="h4 fw-bold text-coffee mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Quán nổi bật điểm cao</h2>
                <p class="text-muted small mb-0">Những địa điểm được cộng đồng sinh viên đánh giá tốt nhất về Wifi, ổ điện và không gian</p>
            </div>
            <a href="<?= base_url('place?sort=rating_desc') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredPlaces as $place): ?>
                <?php
                $noiseInfo = format_noise_level($place['noise_level']);
                $isOpen = is_place_open($place['open_time'], $place['close_time']);
                $isFav = in_array((int) $place['id'], $favoriteIds, true);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden place-card bg-white">
                        <div class="position-relative">
                            <a href="<?= base_url('place/show/' . (int) $place['id']) ?>">
                                <img src="<?= get_image_url($place['cover_image'] ?? null, 'place') ?>" class="card-img-top place-card-img" alt="<?= e($place['name']) ?>">
                            </a>
                            <!-- Badge trạng thái mở cửa -->
                            <span class="position-absolute top-0 start-0 m-3 badge <?= $isOpen ? 'bg-success' : 'bg-secondary' ?> shadow-sm">
                                <i class="bi <?= $isOpen ? 'bi-door-open-fill' : 'bi-door-closed-fill' ?> me-1"></i><?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?>
                            </span>

                            <!-- Nút Yêu thích AJAX -->
                            <button
                                type="button"
                                class="btn btn-sm btn-light rounded-circle shadow-sm position-absolute top-0 end-0 m-3 btn-favorite-toggle"
                                data-place-id="<?= (int) $place['id'] ?>"
                                title="Lưu vào quán yêu thích"
                            >
                                <i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' ?>"></i>
                            </button>

                            <!-- Badge Danh mục -->
                            <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75">
                                <?= e($place['category_name']) ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <!-- Các tag thông minh tự động -->
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <?php if (is_deadline_friendly($place)): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-lightning-charge-fill me-1"></i>Phù hợp chạy deadline
                                    </span>
                                <?php endif; ?>
                                <?php if (is_student_price($place)): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-piggy-bank-fill me-1"></i>Giá sinh viên
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="h6 fw-bold mb-1">
                                <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none">
                                    <?= e($place['name']) ?>
                                </a>
                            </h3>

                            <p class="small text-muted mb-2 text-truncate" title="<?= e($place['address'] . ', ' . $place['district']) ?>">
                                <i class="bi bi-geo-alt-fill text-coffee me-1"></i><?= e($place['address']) ?>, <strong><?= e($place['district']) ?></strong>
                            </p>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <?php if (!empty($place['has_wifi'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-wifi text-info me-1"></i>Wifi</span>
                                <?php endif; ?>
                                <?php if (!empty($place['has_outlet'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-plug text-warning me-1"></i>Ổ cắm</span>
                                <?php endif; ?>
                                <?php if (!empty($place['has_ac'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-snow text-primary me-1"></i>Máy lạnh</span>
                                <?php endif; ?>
                                <span class="badge border <?= e($noiseInfo['class']) ?>">
                                    <i class="bi <?= e($noiseInfo['icon']) ?> me-1"></i><?= e($noiseInfo['label']) ?>
                                </span>
                            </div>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <?= render_stars((float) $place['avg_rating']) ?>
                                    <span class="text-muted small ms-1">(<?= (int) $place['total_reviews'] ?>)</span>
                                </div>
                                <span class="small fw-semibold text-coffee"><?= e(format_price_range((int) $place['price_range'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 4. QUÁN MỚI CẬP NHẬT -->
    <section class="mb-3">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="h4 fw-bold text-coffee mb-1"><i class="bi bi-stars text-success me-2"></i>Địa điểm mới lên sóng</h2>
                <p class="text-muted small mb-0">Cập nhật liên tục các quán học bài mới mở quanh các trường đại học</p>
            </div>
            <a href="<?= base_url('place?sort=newest') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                Xem thêm <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($latestPlaces as $place): ?>
                <?php
                $isOpen = is_place_open($place['open_time'], $place['close_time']);
                $isFav = in_array((int) $place['id'], $favoriteIds, true);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden place-card bg-white">
                        <div class="row g-0 h-100">
                            <div class="col-4 position-relative">
                                <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="d-block h-100">
                                    <img src="<?= get_image_url($place['cover_image'] ?? null, 'place') ?>" class="w-100 h-100 object-fit-cover" style="min-height: 145px;" alt="<?= e($place['name']) ?>">
                                </a>
                            </div>
                            <div class="col-8">
                                <div class="card-body p-3 d-flex flex-column h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="badge bg-light text-secondary border small"><?= e($place['district']) ?></span>
                                        <?= render_stars((float) $place['avg_rating']) ?>
                                    </div>
                                    <h3 class="h6 fw-bold mb-1 text-truncate">
                                        <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none">
                                            <?= e($place['name']) ?>
                                        </a>
                                    </h3>
                                    <p class="small text-muted mb-2 text-truncate"><?= e($place['address']) ?></p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="badge <?= $isOpen ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                            <?= $isOpen ? 'Đang mở' : 'Đã đóng' ?>
                                        </span>
                                        <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="small fw-semibold text-coffee text-decoration-none">
                                            Chi tiết <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
