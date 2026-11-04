<?php
// Trang cá nhân, do ProfileController (index) render.
// Trang cá nhân: $user, $favoritePlaces, $myReviews.
?>
<div class="container py-4">
    <!-- Dải hồ sơ trên, toàn chiều rộng: avatar 96px, tên, email, phone (nếu có), badge vai trò, thống kê, nút sửa. -->
    <section class="profile-band">
        <img src="<?= get_image_url($user['avatar'] ?? null, 'avatar') ?>" alt="<?= e($user['name']) ?>" class="rounded-circle object-fit-cover" style="border: 3px solid #E5E7EB;" width="96" height="96">
        <div class="flex-grow-1" style="min-width: 220px;">
            <h1 class="page-title"><?= e($user['name']) ?></h1>
            <p class="text-muted small mb-2"><?= e($user['email']) ?><?php if (!empty($user['phone'])): ?> • <?= e($user['phone']) ?><?php endif; ?></p>
            <div>
                <!-- Badge vai trò: admin thì 'Quản trị viên StudySpot', còn lại 'Sinh viên / Thành viên'. -->
                <?php if ($user['role'] === 'admin'): ?>
                    <span class="badge rounded-pill px-3 py-1" style="background-color: #D97706;"><i class="bi bi-shield-lock-fill me-1"></i>Quản trị viên StudySpot</span>
                <?php else: ?>
                    <span class="pill-amenity px-3 py-1"><i class="bi bi-mortarboard-fill"></i>Sinh viên / Thành viên</span>
                <?php endif; ?>
            </div>
        </div>
        <!-- 3 thống kê: count $favoritePlaces, count $myReviews, ngày tham gia d/m/Y. -->
        <div class="profile-stats">
            <div class="profile-stat"><div class="stat-num"><?= count($favoritePlaces) ?></div><div class="stat-label">Quán đã lưu</div></div>
            <div class="profile-stat"><div class="stat-num"><?= count($myReviews) ?></div><div class="stat-label">Đánh giá đã gửi</div></div>
            <div class="profile-stat"><div class="stat-num"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div><div class="stat-label">Ngày tham gia</div></div>
        </div>
        <!-- Nút Chỉnh sửa hồ sơ: sang profile/edit để đổi thông tin, avatar, mật khẩu. -->
        <a href="<?= base_url('profile/edit') ?>" class="btn btn-coffee px-4 py-2"><i class="bi bi-pencil-square me-1"></i>Chỉnh sửa hồ sơ</a>
    </section>

    <div class="row g-4">
        <div class="col-12">
            <div class="card surface-card p-4">
                <!-- 2 tab dưới: Lịch sử Đánh giá (mở sẵn) và Quán Yêu thích; số trong ngoặc là count tương ứng. -->
                <ul class="nav nav-pills mb-4 gap-2" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <!-- Tab lịch sử: liệt kê mọi review đã gửi, kể cả chờ duyệt và đang ẩn, khác danh sách công khai ở trang chi tiết. -->
                        <button class="nav-link active rounded-pill px-4" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button" role="tab">
                            <i class="bi bi-chat-square-quote me-1"></i>Lịch sử Đánh giá (<?= count($myReviews) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <!-- Tab quán yêu thích: chỉ tóm tắt số lượng và 2 nút sang trang Quán yêu thích và khám phá, không liệt kê lại. -->
                        <button class="nav-link rounded-pill px-4" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites-pane" type="button" role="tab">
                            <i class="bi bi-heart-fill text-crimson me-1"></i>Quán Yêu thích (<?= count($favoritePlaces) ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabContent">
                    <!-- TAB 1: LỊCH SỬ REVIEW THỰC TẾ -->
                    <div class="tab-pane fade show active" id="reviews-pane" role="tabpanel">
                        <!-- Trạng thái trống: chưa gửi review nào thì hiện nút sang trang khám phá. -->
                        <?php if (empty($myReviews)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-journal-text fs-1 d-block mb-2 text-coffee opacity-50"></i>
                                <div class="fw-semibold text-dark mb-1">Bạn chưa gửi đánh giá nào</div>
                                <p class="small mb-3">Hãy khám phá các quán học bài tại Long Xuyên và chia sẻ cảm nhận đầu tiên nhé!</p>
                                <a href="<?= base_url('place') ?>" class="btn btn-sm btn-coffee rounded-pill px-4">Khám phá quán ngay</a>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <!-- Vòng lặp $myReviews: mỗi review 1 thẻ (tên quán link chi tiết, phường, ngày, badge, sao, 3 điểm, nội dung, ảnh). -->
                                <?php foreach ($myReviews as $rv): ?>
                                    <div class="rounded-4 p-3" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div>
                                                <a href="<?= base_url('place/show/' . (int) $rv['place_id']) ?>" class="fw-bold text-coffee text-decoration-none">
                                                    <?= e($rv['place_name']) ?>
                                                </a>
                                                <span class="pill-amenity ms-2"><?= e($rv['place_district']) ?></span>
                                                <div class="text-muted mt-1" style="font-size: 0.76rem;">
                                                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if ($rv['status'] === 'approved'): ?>
                                                    <span class="badge bg-matcha rounded-pill">Đã duyệt</span>
                                                <?php elseif ($rv['status'] === 'pending'): ?>
                                                    <span class="badge rounded-pill" style="background-color: #D97706;">Chờ duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill">Đang ẩn</span>
                                                <?php endif; ?>
                                                <?= render_stars((float) $rv['rating_overall']) ?>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="pill-amenity">Wifi: <?= (int) $rv['rating_wifi'] ?>/5</span>
                                            <span class="pill-amenity">Không gian: <?= (int) $rv['rating_space'] ?>/5</span>
                                            <span class="pill-amenity">Giá: <?= (int) $rv['rating_price'] ?>/5</span>
                                        </div>

                                        <p class="small text-dark mb-2" style="line-height: 1.65;"><?= nl2br(e($rv['content'])) ?></p>

                                        <?php if (!empty($rv['image_path'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= get_image_url($rv['image_path'], 'review') ?>" alt="Ảnh đính kèm" class="rounded-3 border object-fit-cover" width="96" height="70">
                                            </div>
                                        <?php endif; ?>

                                        <!-- Hàng nút dưới review: 'Sửa tại trang quán' tới place/show/{id}#review-form-section; nút Xóa là form riêng. -->
                                        <div class="d-flex gap-2 mt-2 review-actions">
                                            <a href="<?= base_url('place/show/' . (int) $rv['place_id'] . '#review-form-section') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                                                <i class="bi bi-pencil me-1"></i>Sửa tại trang quán
                                            </a>
                                            <!-- Form xóa review từ hồ sơ: POST review/delete/{id}, redirect_to=profile, onsubmit hỏi xác nhận. -->
                                            <form action="<?= base_url('review/delete/' . (int) $rv['id']) ?>" method="POST" onsubmit="return confirm('Xóa đánh giá này của bạn?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="redirect_to" value="profile">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="bi bi-trash me-1"></i>Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 2: tóm tắt quán yêu thích, dẫn sang trang riêng thay vì liệt kê trùng. -->
                    <div class="tab-pane fade" id="favorites-pane" role="tabpanel">
                        <div class="p-4 rounded-4 text-center" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 68px; height: 68px; background-color: #FBE9E9; color: #BC4749; font-size: 1.8rem;">
                                <i class="bi bi-heart-fill"></i>
                            </div>

                            <h2 class="h5 fw-bold text-dark mb-2">
                                Bạn đã lưu <?= count($favoritePlaces) ?> địa điểm học bài yêu thích
                            </h2>

                            <p class="text-muted small mb-4 mx-auto" style="max-width: 500px;">
                                Toàn bộ các góc cà phê học bài và quán mở khuya quanh ĐH An Giang mà bạn đã bấm lưu đều được quản lý tập trung tại trang Quán Yêu Thích riêng biệt.
                            </p>

                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="<?= base_url('favorite') ?>" class="btn btn-coffee rounded-pill px-4 py-2 fw-semibold">
                                    <i class="bi bi-heart-fill text-crimson me-2"></i>Mở danh sách Quán yêu thích
                                </a>
                                <a href="<?= base_url('place') ?>" class="btn btn-outline-coffee rounded-pill px-4 py-2">
                                    <i class="bi bi-compass me-1"></i>Khám phá thêm quán mới
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
