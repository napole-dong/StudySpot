<?php
// Partial danh sách review đã duyệt, nhúng dưới form review ở trang chi tiết.
// Partial _list.php: $reviews, $reviewTotal, $reviewPage, $reviewPages, $place; chỉ review đã duyệt.
?>
<!-- Thẻ danh sách: tiêu đề kèm tổng số $reviewTotal và nhãn 'Đã kiểm duyệt'. -->
<div class="card surface-card p-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
        <h3 class="h6 fw-bold text-coffee mb-0">
            <i class="bi bi-chat-square-quote-fill me-2"></i>Nhận xét từ sinh viên (<?= (int) $reviewTotal ?>)
        </h3>
        <span class="pill-amenity">Đã kiểm duyệt</span>
    </div>

    <!-- Trạng thái trống: chưa có review đã duyệt thì mời làm người đầu tiên chia sẻ. -->
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
            <!-- Vòng lặp $reviews: mỗi review 1 thẻ (avatar, tên, ngày, sao, 3 pill điểm, nội dung, ảnh). -->
            <?php foreach ($reviews as $rv): ?>
                <!-- Mỗi review: avatar qua get_image_url() (chưa có thì ảnh mặc định), tên qua e() chống XSS. -->
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

                    <!-- 3 pill điểm thành phần: wifi, không gian, giá, dạng số/5. -->
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

                    <!-- Nội dung review: e() chống XSS, nl2br() giữ xuống dòng. -->
                    <p class="mb-2 text-dark small" style="line-height: 1.65;"><?= nl2br(e($rv['content'])) ?></p>

                    <!-- Ảnh review: chỉ hiện khi có image_path, cao tối đa 190px. -->
                    <?php if (!empty($rv['image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh thực tế từ sinh viên" class="rounded-3 border object-fit-cover" style="max-height: 190px; max-width: 100%;">
                        </div>
                    <?php endif; ?>

                    <!-- Hàng hành động dưới review: nút Hữu ích (AJAX cho thành viên, khách thì dẫn sang đăng nhập) và nút Báo cáo. -->
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <?php if (Session::isLoggedIn()): ?>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill btn-helpful-toggle <?= !empty($rv['helped_by_me']) ? 'active-helpful' : '' ?>" data-review-id="<?= (int) $rv['id'] ?>">
                                <i class="bi bi-hand-thumbs-up<?= !empty($rv['helped_by_me']) ? '-fill' : '' ?>"></i>
                                Hữu ích (<span class="helpful-count"><?= (int) ($rv['helpful_count'] ?? 0) ?></span>)
                            </button>
                        <?php else: ?>
                            <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-light border rounded-pill text-decoration-none">
                                <i class="bi bi-hand-thumbs-up"></i>
                                Hữu ích (<?= (int) ($rv['helpful_count'] ?? 0) ?>)
                            </a>
                        <?php endif; ?>
                        <?php if (Session::isLoggedIn() && (int) ($rv['user_id'] ?? 0) !== (int) (Session::user()['id'] ?? 0)): ?>
                            <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none btn-report-review" data-bs-toggle="modal" data-bs-target="#reportReviewModal" data-review-id="<?= (int) $rv['id'] ?>" data-review-author="<?= e($rv['user_name']) ?>">
                                <i class="bi bi-flag me-1"></i>Báo cáo
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang review: render_pagination() với route place/show/{id quán}, vẫn ở trang chi tiết. -->
        <?= render_pagination($reviewPage, $reviewPages, 'place/show/' . (int) $place['id']) ?>
    <?php endif; ?>
</div>

<!-- Modal báo cáo đánh giá: 1 modal dùng chung cho mọi review, main.js gán action theo review vừa bấm. -->
<div class="modal fade" id="reportReviewModal" tabindex="-1" aria-labelledby="reportReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="#" id="reportReviewForm" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="reportReviewModalLabel"><i class="bi bi-flag me-2"></i>Báo cáo đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Đánh giá của <strong id="reportReviewAuthor" class="text-dark"></strong> sẽ được quản trị viên xem xét. Vui lòng chọn lý do phù hợp nhất:</p>
                <?php foreach (ReviewReport::REASONS as $reasonKey => $reasonLabel): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reason" id="reason-<?= e($reasonKey) ?>" value="<?= e($reasonKey) ?>" <?= $reasonKey === 'spam' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="reason-<?= e($reasonKey) ?>"><?= e($reasonLabel) ?></label>
                    </div>
                <?php endforeach; ?>
                <label for="reportNote" class="form-label small mt-3">Ghi chú thêm (không bắt buộc)</label>
                <textarea class="form-control" id="reportNote" name="note" rows="2" maxlength="255" placeholder="Mô tả ngắn gọn vấn đề..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-dark rounded-pill px-3">Gửi báo cáo</button>
            </div>
        </form>
    </div>
</div>
