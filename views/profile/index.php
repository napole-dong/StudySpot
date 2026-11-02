<?php
/**
 * Giao diện Trang cá nhân của thành viên (views/profile/index.php)
 * Hiển thị: Thông tin tài khoản, Danh sách quán yêu thích đã lưu & Lịch sử các review đã viết
 */
?>
<div class="container py-4">
    <div class="row g-4">
        <!-- Cột trái: Thẻ thông tin cá nhân -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                <div class="mb-3">
                    <img
                        src="<?= get_image_url($user['avatar'] ?? null, 'avatar') ?>"
                        alt="<?= e($user['name']) ?>"
                        class="rounded-circle object-fit-cover border border-3 border-warning shadow-sm"
                        width="110"
                        height="110"
                    >
                </div>
                <h1 class="h5 fw-bold text-dark mb-1"><?= e($user['name']) ?></h1>
                <p class="text-muted small mb-2"><?= e($user['email']) ?></p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-danger"><i class="bi bi-shield-lock me-1"></i>Quản trị viên</span>
                    <?php else: ?>
                        <span class="badge bg-coffee"><i class="bi bi-mortarboard me-1"></i>Thành viên Sinh viên</span>
                    <?php endif; ?>
                </div>

                <hr>

                <div class="text-start small mb-3">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted"><i class="bi bi-telephone me-2"></i>Số điện thoại:</span>
                        <span class="fw-semibold"><?= e($user['phone'] ?: 'Chưa cập nhật') ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2"></i>Ngày tham gia:</span>
                        <span class="fw-semibold"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted"><i class="bi bi-heart-fill text-danger me-2"></i>Quán đã lưu:</span>
                        <span class="fw-bold"><?= count($favoritePlaces) ?> quán</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted"><i class="bi bi-chat-square-quote-fill text-coffee me-2"></i>Đánh giá đã viết:</span>
                        <span class="fw-bold"><?= count($myReviews) ?> đánh giá</span>
                    </div>
                </div>

                <a href="<?= base_url('profile/edit') ?>" class="btn btn-coffee w-100">
                    <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa hồ sơ &amp; Đổi mật khẩu
                </a>
            </div>
        </div>

        <!-- Cột phải: Tabs Quán Yêu Thích & Lịch sử Review -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <ul class="nav nav-pills mb-4 gap-2" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button" role="tab">
                            <i class="bi bi-chat-square-quote me-1"></i>Lịch sử Đánh giá (<?= count($myReviews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites-pane" type="button" role="tab">
                            <i class="bi bi-heart me-1"></i>Quán Yêu thích (<?= count($favoritePlaces) ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabContent">
                    <!-- TAB 1: LỊCH SỬ REVIEW -->
                    <div class="tab-pane fade show active" id="reviews-pane" role="tabpanel">
                        <?php if (empty($myReviews)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                                Bạn chưa gửi đánh giá nào. Hãy khám phá quán và chia sẻ cảm nhận đầu tiên nhé!
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($myReviews as $rv): ?>
                                    <div class="border rounded-4 p-3 bg-light-warm">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div>
                                                <a href="<?= base_url('place/show/' . (int) $rv['place_id']) ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= e($rv['place_name']) ?>
                                                </a>
                                                <span class="text-muted small ms-1">(<?= e($rv['place_district']) ?>)</span>
                                                <div class="text-muted" style="font-size: 0.78rem;">
                                                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if ($rv['status'] === 'approved'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Đã duyệt</span>
                                                <?php elseif ($rv['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border">Đang ẩn</span>
                                                <?php endif; ?>
                                                <?= render_stars((float) $rv['rating_overall']) ?>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-2 small">
                                            <span class="badge bg-white text-dark border fw-normal">Wifi: <?= (int) $rv['rating_wifi'] ?>/5</span>
                                            <span class="badge bg-white text-dark border fw-normal">Không gian: <?= (int) $rv['rating_space'] ?>/5</span>
                                            <span class="badge bg-white text-dark border fw-normal">Giá: <?= (int) $rv['rating_price'] ?>/5</span>
                                        </div>

                                        <p class="small text-dark mb-2"><?= nl2br(e($rv['content'])) ?></p>

                                        <?php if (!empty($rv['image_path'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh đính kèm" class="rounded border object-fit-cover" width="90" height="65">
                                            </div>
                                        <?php endif; ?>

                                        <div class="d-flex justify-content-end gap-2 mt-2">
                                            <a href="<?= base_url('place/show/' . (int) $rv['place_id'] . '#review-form-section') ?>" class="btn btn-xs btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i>Sửa tại trang quán
                                            </a>
                                            <form action="<?= base_url('review/delete/' . (int) $rv['id']) ?>" method="POST" onsubmit="return confirm('Xóa đánh giá này của bạn?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="redirect_to" value="profile">
                                                <button type="submit" class="btn btn-xs btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash me-1"></i>Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: QUÁN YÊU THÍCH ĐÃ LƯU -->
                    <div class="tab-pane fade" id="favorites-pane" role="tabpanel">
                        <?php if (empty($favoritePlaces)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-heart fs-2 d-block mb-2"></i>
                                Danh sách yêu thích của bạn đang trống.
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($favoritePlaces as $fp): ?>
                                    <div class="col-md-6 favorite-item-col" data-place-id="<?= (int) $fp['id'] ?>">
                                        <div class="border rounded-4 p-2 d-flex align-items-center gap-3 bg-light-warm">
                                            <a href="<?= base_url('place/show/' . (int) $fp['id']) ?>">
                                                <img src="<?= get_image_url($fp['cover_image'] ?? null, 'place') ?>" alt="<?= e($fp['name']) ?>" class="rounded-3 object-fit-cover" width="80" height="70">
                                            </a>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <a href="<?= base_url('place/show/' . (int) $fp['id']) ?>" class="fw-bold small text-dark text-decoration-none d-block text-truncate">
                                                    <?= e($fp['name']) ?>
                                                </a>
                                                <div class="text-muted small text-truncate"><?= e($fp['district']) ?></div>
                                                <div><?= render_stars((float) $fp['avg_rating']) ?></div>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light rounded-circle border btn-favorite-toggle"
                                                data-place-id="<?= (int) $fp['id'] ?>"
                                                data-remove-card="1"
                                                title="Bỏ lưu"
                                            >
                                                <i class="bi bi-heart-fill text-danger"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
