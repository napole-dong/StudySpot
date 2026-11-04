<?php
/**
 * Giao diện Danh sách & Khám phá Quán học bài tại TP. Long Xuyên, An Giang (views/places/index.php)
 * Hỗ trợ: Tìm kiếm tên/địa chỉ, Lọc đa tiêu chí (Danh mục, Phường thực tế từ CSDL, Mức giá, Wifi, Ổ cắm, Máy lạnh, Độ ồn), Sắp xếp & Phân trang
 */
$hasActiveFilter = !empty($filters['q'])
    || !empty($filters['category_id'])
    || !empty($filters['district'])
    || !empty($filters['price_range'])
    || !empty($filters['noise_level'])
    || !empty($filters['amenity']);
?>
<div class="container py-4">
    <!-- Tiêu đề trang & Tổng số kết quả tìm thấy thực tế -->
    <div class="page-head d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Khám phá quán tại Long Xuyên</li>
                </ol>
            </nav>
            <h1 class="page-title">
                <i class="bi bi-compass-fill me-2"></i>Khám phá Quán cà phê học bài tại TP. Long Xuyên
            </h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="pill-amenity px-3 py-2 fs-6 text-nowrap">
                Tìm thấy <strong class="mx-1"><?= (int) $total ?></strong> địa điểm phù hợp
            </span>
            <?php if ($hasActiveFilter): ?>
                <a href="<?= base_url('place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3 text-nowrap">
                    <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- BỘ LỌC Ở TRÊN, toàn chiều rộng — không còn cột trái -->
    <section class="filter-bar">
        <form action="<?= base_url('place') ?>" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-12 col-xl-4">
                    <label class="form-label fw-semibold small">Từ khóa tìm kiếm</label>
                    <input type="text" name="q" class="form-control" placeholder="NHÀ KHO, Ung Văn Khiêm..." value="<?= e($filters['q'] ?? '') ?>">
                </div>
                <div class="col-md-6 col-xl-4">
                    <label class="form-label fw-semibold small">Loại hình</label>
                    <select name="category_id" class="form-select">
                        <option value="0">Tất cả</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($filters['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-4">
                    <label class="form-label fw-semibold small">Phường / Khu vực</label>
                    <select name="district" class="form-select">
                        <option value="">Tất cả phường</option>
                        <?php foreach ($districts as $dist): ?>
                            <option value="<?= e($dist) ?>" <?= (($filters['district'] ?? '') === $dist) ? 'selected' : '' ?>><?= e($dist) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                
            </div>
            <div class="row g-3 align-items-end mt-1">
<div class="col-md-6 col-xl-4">
                    <label class="form-label fw-semibold small">Mức giá sinh viên</label>
                    <select name="price_range" class="form-select">
                        <option value="0">Mọi mức giá</option>
                        <option value="1" <?= ((int) ($filters['price_range'] ?? 0) === 1) ? 'selected' : '' ?>>Dưới 25.000₫</option>
                        <option value="2" <?= ((int) ($filters['price_range'] ?? 0) === 2) ? 'selected' : '' ?>>Dưới 45.000₫</option>
                        <option value="3" <?= ((int) ($filters['price_range'] ?? 0) === 3) ? 'selected' : '' ?>>Dưới 65.000₫</option>
                        <option value="4" <?= ((int) ($filters['price_range'] ?? 0) === 4) ? 'selected' : '' ?>>Mọi mức giá</option>
                    </select>
                </div>
<div class="col-md-6 col-xl-4">
                    <label class="form-label fw-semibold small">Sắp xếp theo</label>
                    <select name="sort" class="form-select">
                        <option value="rating_desc" <?= (($filters['sort'] ?? 'rating_desc') === 'rating_desc') ? 'selected' : '' ?>>Điểm cao nhất</option>
                        <option value="newest" <?= (($filters['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Mới cập nhật nhất</option>
                        <option value="price_asc" <?= (($filters['sort'] ?? '') === 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                        <option value="price_desc" <?= (($filters['sort'] ?? '') === 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                    </select>
                </div>
<div class="col-md-12 col-xl-4">
                    <label class="form-label fw-semibold small">Không gian &amp; Mức độ yên tĩnh</label>
                    <select name="noise_level" class="form-select">
                        <option value="">-- Mọi mức độ --</option>
                        <option value="yen_tinh" <?= (($filters['noise_level'] ?? '') === 'yen_tinh') ? 'selected' : '' ?>>Yên tĩnh</option>
                        <option value="vua" <?= (($filters['noise_level'] ?? '') === 'vua') ? 'selected' : '' ?>>Vừa phải</option>
                        <option value="on" <?= (($filters['noise_level'] ?? '') === 'on') ? 'selected' : '' ?>>Nhộn nhịp</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 align-items-end mt-1">
                
                <div class="col-md-8 d-flex flex-wrap gap-3 align-items-center pt-2">
                    <?php foreach ($amenities as $am): ?>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="filterAmenity<?= (int) $am['id'] ?>" name="amenity[]" value="<?= e($am['slug']) ?>" <?= in_array($am['slug'], $filters['amenity'] ?? [], true) ? 'checked' : '' ?>><label class="form-check-label small" for="filterAmenity<?= (int) $am['id'] ?>"><?= e($am['name']) ?></label></div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= base_url('place') ?>" class="btn btn-outline-coffee">Đặt lại</a>
                    <button type="submit" class="btn btn-coffee px-4 text-nowrap"><i class="bi bi-funnel-fill me-1"></i>Áp dụng bộ lọc</button>
                </div>
            </div>
        </form>
    </section>

    <?php if ((int) $total === 0 || empty($places)): ?>
        <div class="card surface-card p-5 text-center">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 76px; height: 76px; background-color: #F3F4F6; color: #111827; font-size: 2rem;"><i class="bi bi-cup-hot"></i></div>
            <h3 class="h5 fw-bold text-dark mb-2">Không tìm thấy địa điểm nào khớp với bộ lọc</h3>
            <p class="text-muted small mb-4 mx-auto" style="max-width: 460px;">Hiện chưa có quán nào tại TP. Long Xuyên thỏa mãn đồng thời các tiêu chí bạn vừa chọn. Hãy thử nới lỏng điều kiện hoặc xóa bộ lọc để xem toàn bộ danh sách nhé!</p>
            <div><a href="<?= base_url('place') ?>" class="btn btn-coffee px-4 py-2"><i class="bi bi-arrow-counterclockwise me-1"></i>Xóa bộ lọc &amp; Xem tất cả quán</a></div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($places as $place): ?>
                <?php
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
                        <h2 class="h6 fw-bold mb-1"><a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none"><?= e($place['name']) ?></a></h2>
                        <p class="small text-muted mb-2 text-truncate"><i class="bi bi-geo-alt-fill me-1"></i><?= e($place['address']) ?></p>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($cardAmenities as $amenity): ?>
                                <span class="<?= e($amenity['class']) ?>"><?= e($amenity['label']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="place-row-side">
                        <div><?= render_stars((float) $place['avg_rating']) ?> <span class="small text-muted">(<?= (int) $place['total_reviews'] ?>)</span></div>
                        <div class="place-price-badge"><?= e(format_price_range_from_min_max($place['price_min'] ?? null, $place['price_max'] ?? null)) ?></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-coffee btn-favorite-toggle" data-place-id="<?= (int) $place['id'] ?>" title="Lưu vào quán yêu thích"><i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i></button>
                            <a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="btn btn-sm btn-coffee">Xem chi tiết</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?= render_pagination($currentPage, $totalPages, 'place') ?>
    <?php endif; ?>
</div>
</div>
