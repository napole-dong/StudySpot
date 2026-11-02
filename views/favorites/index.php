<?php
/**
 * Giao diện Danh sách Quán yêu thích đã lưu của thành viên (views/favorites/index.php)
 */
?>
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quán đã lưu</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-coffee mb-0">
                <i class="bi bi-heart-fill text-danger me-2"></i>Quán học bài Yêu thích của tôi
            </h1>
        </div>
        <a href="<?= base_url('place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
            <i class="bi bi-compass me-1"></i>Khám phá thêm quán mới
        </a>
    </div>

    <?php if (empty($places)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="display-4 text-danger opacity-50 mb-3"><i class="bi bi-heart"></i></div>
            <h2 class="h5 fw-bold mb-2">Bạn chưa lưu quán học bài nào</h2>
            <p class="text-muted small mb-4">
                Hãy bấm vào biểu tượng trái tim <i class="bi bi-heart text-danger"></i> trên các quán cà phê để lưu lại danh sách góc học tập yêu thích của riêng bạn.
            </p>
            <div>
                <a href="<?= base_url('place') ?>" class="btn btn-coffee px-4">
                    <i class="bi bi-search me-1"></i>Tìm quán học bài ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4" id="favoritePlacesGrid">
            <?php foreach ($places as $place): ?>
                <?php
                $noiseInfo = format_noise_level($place['noise_level']);
                $isOpen = is_place_open($place['open_time'], $place['close_time']);
                ?>
                <div class="col-md-6 col-lg-4 favorite-item-col" data-place-id="<?= (int) $place['id'] ?>">
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
                                data-remove-card="1"
                                title="Bỏ lưu khỏi danh sách yêu thích"
                            >
                                <i class="bi bi-heart-fill text-danger"></i>
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

                            <p class="small text-muted mb-2 text-truncate">
                                <i class="bi bi-geo-alt-fill text-coffee me-1"></i><?= e($place['address']) ?>, <strong><?= e($place['district']) ?></strong>
                            </p>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <?php if (!empty($place['has_wifi'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-wifi text-info me-1"></i>Wifi</span>
                                <?php endif; ?>
                                <?php if (!empty($place['has_outlet'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-plug text-warning me-1"></i>Ổ cắm</span>
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
    <?php endif; ?>
</div>
