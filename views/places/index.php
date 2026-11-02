<?php
/**
 * Giao diện Danh sách & Khám phá Quán học bài (views/places/index.php)
 * Hỗ trợ: Tìm kiếm tên/địa chỉ, Lọc đa tiêu chí (Danh mục, Quận, Mức giá, Wifi, Ổ cắm, Máy lạnh, Độ ồn), Sắp xếp & Phân trang
 */
?>
<div class="container py-4">
    <!-- Tiêu đề trang & Thanh sắp xếp nhanh -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Khám phá quán học bài</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-coffee mb-0">
                <i class="bi bi-compass me-2"></i>Khám phá Không gian Học tập
            </h1>
        </div>
        <div class="text-muted small">
            Tìm thấy <strong class="text-dark fs-6"><?= (int) $total ?></strong> địa điểm phù hợp
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: BỘ LỌC ĐA TIÊU CHÍ -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 sticky-lg-top bg-white" style="top: 80px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h2 class="h6 fw-bold mb-0 text-coffee">
                        <i class="bi bi-funnel-fill me-1"></i>Bộ lọc tìm kiếm
                    </h2>
                    <a href="<?= base_url('place') ?>" class="small text-decoration-none text-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                    </a>
                </div>

                <form action="<?= base_url('place') ?>" method="GET">
                    <!-- 1. Từ khóa tên quán / địa chỉ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Từ khóa tìm kiếm</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-coffee"></i></span>
                            <input type="text" name="q" class="form-control" placeholder="Tên quán, đường, quận..." value="<?= e($filters['q'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- 2. Loại hình quán -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Loại hình không gian</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="0">-- Tất cả loại hình --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Quận / Khu vực -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Quận / Khu vực</label>
                        <select name="district" class="form-select form-select-sm">
                            <option value="">-- Tất cả khu vực --</option>
                            <?php foreach ($districts as $dist): ?>
                                <option value="<?= e($dist) ?>" <?= (($filters['district'] ?? '') === $dist) ? 'selected' : '' ?>>
                                    <?= e($dist) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 4. Mức giá tối đa -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mức giá đồ uống</label>
                        <select name="price_range" class="form-select form-select-sm">
                            <option value="0">-- Mọi mức giá --</option>
                            <option value="1" <?= ((int) ($filters['price_range'] ?? 0) === 1) ? 'selected' : '' ?>>Dưới 35.000đ ($)</option>
                            <option value="2" <?= ((int) ($filters['price_range'] ?? 0) === 2) ? 'selected' : '' ?>>Dưới 50.000đ ($$ - Giá sinh viên)</option>
                            <option value="3" <?= ((int) ($filters['price_range'] ?? 0) === 3) ? 'selected' : '' ?>>Dưới 80.000đ ($$$)</option>
                            <option value="4" <?= ((int) ($filters['price_range'] ?? 0) === 4) ? 'selected' : '' ?>>Tất cả (Kể cả > 80.000đ)</option>
                        </select>
                    </div>

                    <!-- 5. Độ ồn -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mức độ yên tĩnh</label>
                        <select name="noise_level" class="form-select form-select-sm">
                            <option value="">-- Mọi mức độ --</option>
                            <option value="yen_tinh" <?= (($filters['noise_level'] ?? '') === 'yen_tinh') ? 'selected' : '' ?>>Yên tĩnh (Học tập / Đọc sách)</option>
                            <option value="vua" <?= (($filters['noise_level'] ?? '') === 'vua') ? 'selected' : '' ?>>Độ ồn vừa (Họp nhóm)</option>
                            <option value="on" <?= (($filters['noise_level'] ?? '') === 'on') ? 'selected' : '' ?>>Nhộn nhịp (Thư giãn)</option>
                        </select>
                    </div>

                    <!-- 6. Tiện ích bắt buộc -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-block">Tiện ích học bài</label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="filterWifi" name="has_wifi" value="1" <?= !empty($filters['has_wifi']) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="filterWifi">
                                <i class="bi bi-wifi text-info me-1"></i>Có Wifi mạnh
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="filterOutlet" name="has_outlet" value="1" <?= !empty($filters['has_outlet']) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="filterOutlet">
                                <i class="bi bi-plug text-warning me-1"></i>Nhiều ổ cắm điện
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="filterAc" name="has_ac" value="1" <?= !empty($filters['has_ac']) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="filterAc">
                                <i class="bi bi-snow text-primary me-1"></i>Có máy lạnh
                            </label>
                        </div>
                    </div>

                    <!-- 7. Sắp xếp -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Sắp xếp theo</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="rating_desc" <?= (($filters['sort'] ?? 'rating_desc') === 'rating_desc') ? 'selected' : '' ?>>Điểm đánh giá cao nhất</option>
                            <option value="newest" <?= (($filters['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Mới cập nhật nhất</option>
                            <option value="price_asc" <?= (($filters['sort'] ?? '') === 'price_asc') ? 'selected' : '' ?>>Mức giá: Thấp đến Cao</option>
                            <option value="price_desc" <?= (($filters['sort'] ?? '') === 'price_desc') ? 'selected' : '' ?>>Mức giá: Cao đến Thấp</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-coffee btn-sm w-100 py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Áp dụng bộ lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH QUÁN & PHÂN TRANG -->
        <div class="col-lg-9">
            <?php if (empty($places)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="display-4 text-muted mb-3"><i class="bi bi-cup"></i></div>
                    <h3 class="h5 fw-bold mb-2">Không tìm thấy địa điểm nào phù hợp</h3>
                    <p class="text-muted small mb-4">Bạn hãy thử nới lỏng điều kiện lọc hoặc tìm kiếm với từ khóa khác nhé.</p>
                    <div>
                        <a href="<?= base_url('place') ?>" class="btn btn-coffee px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Xem tất cả các quán
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($places as $place): ?>
                        <?php
                        $noiseInfo = format_noise_level($place['noise_level']);
                        $isOpen = is_place_open($place['open_time'], $place['close_time']);
                        $isFav = in_array((int) $place['id'], $favoriteIds, true);
                        ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden place-card bg-white">
                                <div class="position-relative">
                                    <a href="<?= base_url('place/show/' . (int) $place['id']) ?>">
                                        <img src="<?= get_image_url($place['cover_image'] ?? null, 'place') ?>" class="card-img-top place-card-img" alt="<?= e($place['name']) ?>">
                                    </a>
                                    <span class="position-absolute top-0 start-0 m-3 badge <?= $isOpen ? 'bg-success' : 'bg-secondary' ?> shadow-sm">
                                        <i class="bi <?= $isOpen ? 'bi-door-open-fill' : 'bi-door-closed-fill' ?> me-1"></i><?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?>
                                    </span>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light rounded-circle shadow-sm position-absolute top-0 end-0 m-3 btn-favorite-toggle"
                                        data-place-id="<?= (int) $place['id'] ?>"
                                        title="Lưu vào quán yêu thích"
                                    >
                                        <i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' ?>"></i>
                                    </button>

                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75">
                                        <?= e($place['category_name']) ?>
                                    </span>
                                </div>

                                <div class="card-body d-flex flex-column p-3">
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

                                    <h2 class="h6 fw-bold mb-1">
                                        <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none">
                                            <?= e($place['name']) ?>
                                        </a>
                                    </h2>

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

                <!-- Thanh phân trang giữ nguyên điều kiện lọc -->
                <?= render_pagination($currentPage, $totalPages, 'place') ?>
            <?php endif; ?>
        </div>
    </div>
</div>
