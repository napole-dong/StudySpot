<?php
/**
 * Partial View: Danh sách các đánh giá đã được duyệt của quán (views/reviews/_list.php)
 */
?>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
        <h3 class="h5 fw-bold text-coffee mb-0">
            <i class="bi bi-chat-square-quote-fill me-2"></i>Nhận xét từ sinh viên (<?= (int) $reviewTotal ?>)
        </h3>
    </div>

    <?php if (empty($reviews)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-dots fs-2 d-block mb-2"></i>
            Chưa có đánh giá nào được duyệt cho địa điểm này. Hãy là người đầu tiên chia sẻ trải nghiệm nhé!
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($reviews as $rv): ?>
                <div class="border rounded-4 p-3 bg-light-warm">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= get_image_url($rv['user_avatar'] ?? null, 'avatar') ?>" alt="<?= e($rv['user_name']) ?>" class="rounded-circle object-fit-cover border" width="42" height="42">
                            <div>
                                <div class="fw-bold small text-dark"><?= e($rv['user_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.78rem;">
                                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <?= render_stars((float) $rv['rating_overall']) ?>
                        </div>
                    </div>

                    <!-- Điểm thành phần từng tiêu chí -->
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-white text-dark border fw-normal">
                            <i class="bi bi-wifi text-info me-1"></i>Wifi: <strong><?= (int) $rv['rating_wifi'] ?>/5</strong>
                        </span>
                        <span class="badge bg-white text-dark border fw-normal">
                            <i class="bi bi-lamp text-coffee me-1"></i>Không gian: <strong><?= (int) $rv['rating_space'] ?>/5</strong>
                        </span>
                        <span class="badge bg-white text-dark border fw-normal">
                            <i class="bi bi-piggy-bank text-success me-1"></i>Giá cả: <strong><?= (int) $rv['rating_price'] ?>/5</strong>
                        </span>
                    </div>

                    <p class="mb-2 text-dark small" style="line-height: 1.6;"><?= nl2br(e($rv['content'])) ?></p>

                    <?php if (!empty($rv['image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh thực tế từ sinh viên" class="rounded-3 border object-fit-cover" style="max-height: 180px; max-width: 100%;">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?= render_pagination($reviewPage, $reviewPages, 'place/show/' . (int) $place['id']) ?>
    <?php endif; ?>
</div>
