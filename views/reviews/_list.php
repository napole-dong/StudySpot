<?php
/**
 * Partial View: Danh sách các đánh giá đã được duyệt của quán (views/reviews/_list.php)
 */
?>
<div class="card surface-card p-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
        <h3 class="h6 fw-bold text-coffee mb-0">
            <i class="bi bi-chat-square-quote-fill me-2"></i>Nhận xét từ sinh viên (<?= (int) $reviewTotal ?>)
        </h3>
        <span class="pill-amenity">Đã kiểm duyệt</span>
    </div>

    <?php if (empty($reviews)): ?>
        <div class="text-center py-5 text-muted">
            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #F3F4F6; color: #111827; font-size: 1.5rem;">
                <i class="bi bi-chat-dots"></i>
            </div>
            <div class="fw-semibold text-dark mb-1">Chưa có đánh giá nào được duyệt</div>
            <div class="small">Hãy là sinh viên đầu tiên chia sẻ cảm nhận thực tế về quán nhé!</div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($reviews as $rv): ?>
                <div class="rounded-4 p-3" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= get_image_url($rv['user_avatar'] ?? null, 'avatar') ?>" alt="<?= e($rv['user_name']) ?>" class="rounded-circle object-fit-cover border" width="44" height="44">
                            <div>
                                <div class="fw-bold small text-dark"><?= e($rv['user_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.76rem;">
                                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <?= render_stars((float) $rv['rating_overall']) ?>
                        </div>
                    </div>

                    <!-- Điểm thành phần từng tiêu chí dạng Pill kem -->
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="pill-amenity">
                            <i class="bi bi-wifi"></i>Wifi: <strong><?= (int) $rv['rating_wifi'] ?>/5</strong>
                        </span>
                        <span class="pill-amenity">
                            <i class="bi bi-lamp-fill"></i>Không gian: <strong><?= (int) $rv['rating_space'] ?>/5</strong>
                        </span>
                        <span class="pill-amenity">
                            <i class="bi bi-piggy-bank-fill"></i>Giá cả: <strong><?= (int) $rv['rating_price'] ?>/5</strong>
                        </span>
                    </div>

                    <p class="mb-2 text-dark small" style="line-height: 1.65;"><?= nl2br(e($rv['content'])) ?></p>

                    <?php if (!empty($rv['image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh thực tế từ sinh viên" class="rounded-3 border object-fit-cover" style="max-height: 190px; max-width: 100%;">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?= render_pagination($reviewPage, $reviewPages, 'place/show/' . (int) $place['id']) ?>
    <?php endif; ?>
</div>
