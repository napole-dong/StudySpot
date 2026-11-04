<?php
/**
 * Giao diện Danh sách Quán yêu thích đã lưu của thành viên (views/favorites/index.php)
 * Thiết kế đồng nhất theo phong cách Warm Study Cafe
 */
?>
<div class="container py-4">
    <div class="page-head d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quán đã lưu</li>
                </ol>
            </nav>
            <h1 class="page-title">
                <i class="bi bi-heart-fill text-crimson me-2"></i>Quán học bài yêu thích tại Long Xuyên (<?= count($places) ?>)
            </h1>
        </div>
        <a href="<?= base_url('place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
            <i class="bi bi-compass me-1"></i>Khám phá thêm quán mới
        </a>
    </div>

    <?php if (empty($places)): ?>
        <div class="card surface-card p-5 text-center">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background-color: #FBE9E9; color: #BC4749; font-size: 2rem;">
                <i class="bi bi-heart"></i>
            </div>
            <h2 class="h5 fw-bold mb-2">Bạn chưa lưu quán học bài nào</h2>
            <p class="text-muted small mb-4 mx-auto" style="max-width: 450px;">
                Hãy bấm vào biểu tượng trái tim <i class="bi bi-heart-fill text-crimson"></i> ở góc phải trên ảnh các quán cà phê tại Long Xuyên để lưu lại danh sách góc học tập quen thuộc của riêng bạn.
            </p>
            <div>
                <a href="<?= base_url('place') ?>" class="btn btn-coffee rounded-pill px-4 py-2">
                    <i class="bi bi-search me-1"></i>Tìm quán học bài ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4" id="favoritePlacesGrid">
            <?php foreach ($places as $place): ?>
                <?php
                $isOpen = is_place_open($place['open_time'], $place['close_time']);
                $cardAmenities = get_card_amenities($place);
                ?>
                <article class="place-row favorite-item-col" data-place-id="<?= (int) $place['id'] ?>">
                    <a href="<?= base_url('place/show/' . (int) $place['id']) ?>"><img src="<?= get_image_url($place['cover_image'] ?? null, 'place', (int) $place['id']) ?>" class="place-row-img" alt="<?= e($place['name']) ?>"></a>
                    <div class="place-row-body">
                        <div class="d-flex flex-wrap gap-2 mb-1"><span class="pill-amenity"><?= e($place['category_name']) ?></span><span class="<?= $isOpen ? 'badge-status-open' : 'badge-status-closed' ?>"><?= $isOpen ? 'Đang mở cửa' : 'Đã đóng cửa' ?></span></div>
                        <h2 class="h6 fw-bold mb-1"><a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="text-dark text-decoration-none"><?= e($place['name']) ?></a></h2>
                        <p class="small text-muted mb-2 text-truncate"><i class="bi bi-geo-alt-fill me-1"></i><?= e($place['address']) ?></p>
                        <div class="d-flex flex-wrap gap-1"><?php foreach ($cardAmenities as $amenity): ?><span class="<?= e($amenity['class']) ?>"><?= e($amenity['label']) ?></span><?php endforeach; ?></div>
                    </div>
                    <div class="place-row-side">
                        <div><?= render_stars((float) $place['avg_rating']) ?> <span class="small text-muted">(<?= (int) $place['total_reviews'] ?>)</span></div>
                        <div class="place-price-badge"><?= e(format_price_range_from_min_max($place['price_min'] ?? null, $place['price_max'] ?? null)) ?></div>
                        <div class="d-flex gap-2"><button type="button" class="btn btn-sm btn-outline-coffee btn-favorite-toggle" data-place-id="<?= (int) $place['id'] ?>" data-remove-card="1" title="Bỏ lưu khỏi danh sách yêu thích"><i class="bi bi-heart-fill text-danger"></i></button><a href="<?= base_url('place/show/' . (int) $place['id']) ?>" class="btn btn-sm btn-coffee">Xem chi tiết</a></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
