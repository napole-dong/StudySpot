<?php
/**
 * Partial View: Biểu mẫu Viết hoặc Chỉnh sửa Review của thành viên (views/reviews/_form.php)
 * Mỗi sinh viên chỉ được đánh giá 1 lần cho 1 quán. Nếu đã viết thì hiển thị form Cập nhật / Xóa.
 */
$placeId = (int) $place['id'];
?>
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" id="review-form-section">
    <h3 class="h5 fw-bold text-coffee mb-3">
        <i class="bi bi-pencil-square me-2"></i>Đánh giá trải nghiệm học tập của bạn
    </h3>

    <?php if (!Session::isLoggedIn()): ?>
        <div class="alert alert-light border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-0">
            <div class="small">
                <i class="bi bi-info-circle-fill text-coffee me-2"></i>
                Vui lòng đăng nhập tài khoản thành viên để gửi đánh giá và chia sẻ hình ảnh thực tế tại quán.
            </div>
            <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-coffee px-3 flex-shrink-0">
                <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập ngay
            </a>
        </div>
    <?php elseif (!empty($myReview)): ?>
        <!-- Trường hợp thành viên ĐÃ có review tại quán này -> Cho phép Sửa hoặc Xóa -->
        <div class="alert <?= $myReview['status'] === 'approved' ? 'alert-success' : ($myReview['status'] === 'pending' ? 'alert-warning' : 'alert-secondary') ?> py-2 px-3 small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="bi bi-check-circle-fill me-1"></i>
                Bạn đã gửi đánh giá cho quán này vào ngày <?= date('d/m/Y H:i', strtotime($myReview['created_at'])) ?>.
                Trạng thái hiện tại:
                <?php if ($myReview['status'] === 'approved'): ?>
                    <strong>Đã được duyệt hiển thị</strong>
                <?php elseif ($myReview['status'] === 'pending'): ?>
                    <strong>Đang chờ Quản trị viên duyệt</strong>
                <?php else: ?>
                    <strong>Đang tạm ẩn</strong>
                <?php endif; ?>
            </div>
            <button class="btn btn-xs btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#editMyReviewCollapse">
                <i class="bi bi-pencil me-1"></i>Sửa đánh giá của tôi
            </button>
        </div>

        <div class="collapse mt-3" id="editMyReviewCollapse">
            <form action="<?= base_url('review/update/' . (int) $myReview['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="place_id" value="<?= $placeId ?>">

                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Điểm tổng thể <span class="text-danger">*</span></label>
                        <select name="rating_overall" class="form-select form-select-sm" required>
                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                <option value="<?= $s ?>" <?= ((int) $myReview['rating_overall'] === $s) ? 'selected' : '' ?>><?= $s ?> sao</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Tốc độ Wifi</label>
                        <select name="rating_wifi" class="form-select form-select-sm">
                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                <option value="<?= $s ?>" <?= ((int) $myReview['rating_wifi'] === $s) ? 'selected' : '' ?>><?= $s ?> sao</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Không gian &amp; Bàn ghế</label>
                        <select name="rating_space" class="form-select form-select-sm">
                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                <option value="<?= $s ?>" <?= ((int) $myReview['rating_space'] === $s) ? 'selected' : '' ?>><?= $s ?> sao</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Mức giá đồ uống</label>
                        <select name="rating_price" class="form-select form-select-sm">
                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                <option value="<?= $s ?>" <?= ((int) $myReview['rating_price'] === $s) ? 'selected' : '' ?>><?= $s ?> sao</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nội dung nhận xét <span class="text-danger">*</span></label>
                    <textarea name="content" rows="3" class="form-control" required minlength="10"><?= e($myReview['content']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Đổi ảnh đính kèm (Tùy chọn - JPG, PNG, WEBP tối đa 2MB)</label>
                    <input type="file" name="image" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp">
                    <?php if (!empty($myReview['image_path'])): ?>
                        <div class="mt-2">
                            <img src="<?= get_image_url($myReview['image_path'], 'review') ?>" alt="Ảnh review" class="rounded border object-fit-cover" width="90" height="65">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-sm btn-coffee px-3">
                        <i class="bi bi-save me-1"></i>Cập nhật đánh giá
                    </button>
                </div>
            </form>

            <hr class="my-3">
            <form action="<?= base_url('review/delete/' . (int) $myReview['id']) ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá của mình tại quán này?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Xóa đánh giá của tôi
                </button>
            </form>
        </div>
    <?php else: ?>
        <!-- Trường hợp thành viên CHƯA review quán này -> Hiển thị form viết review mới -->
        <form action="<?= base_url('review/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="place_id" value="<?= $placeId ?>">

            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-star-fill text-warning me-1"></i>Tổng thể <span class="text-danger">*</span></label>
                    <select name="rating_overall" class="form-select form-select-sm" required>
                        <option value="5" selected>5 sao - Tuyệt vời</option>
                        <option value="4">4 sao - Rất tốt</option>
                        <option value="3">3 sao - Khá ổn</option>
                        <option value="2">2 sao - Kém</option>
                        <option value="1">1 sao - Tệ</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-wifi text-info me-1"></i>Tốc độ Wifi</label>
                    <select name="rating_wifi" class="form-select form-select-sm">
                        <option value="5" selected>5 sao - Cực mạnh</option>
                        <option value="4">4 sao - Ổn định</option>
                        <option value="3">3 sao - Trung bình</option>
                        <option value="2">2 sao - Chập chờn</option>
                        <option value="1">1 sao - Không dùng được</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-lamp text-coffee me-1"></i>Không gian</label>
                    <select name="rating_space" class="form-select form-select-sm">
                        <option value="5" selected>5 sao - Rộng &amp; êm</option>
                        <option value="4">4 sao - Thoải mái</option>
                        <option value="3">3 sao - Vừa phải</option>
                        <option value="2">2 sao - Hơi chật</option>
                        <option value="1">1 sao - Khó ngồi học</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold"><i class="bi bi-piggy-bank text-success me-1"></i>Mức giá</label>
                    <select name="rating_price" class="form-select form-select-sm">
                        <option value="5" selected>5 sao - Rất hợp ví SV</option>
                        <option value="4">4 sao - Hợp lý</option>
                        <option value="3">3 sao - Chấp nhận được</option>
                        <option value="2">2 sao - Hơi cao</option>
                        <option value="1">1 sao - Quá đắt</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Chia sẻ trải nghiệm thực tế của bạn <span class="text-danger">*</span></label>
                <textarea
                    name="content"
                    rows="3"
                    class="form-control"
                    placeholder="Bàn ghế ngồi lâu có mỏi lưng không? Ổ cắm điện có dễ tìm không? Wifi học online mượt không?..."
                    required
                    minlength="10"
                ></textarea>
            </div>

            <div class="row g-3 align-items-center">
                <div class="col-md-7">
                    <label class="form-label small fw-semibold mb-1">Đính kèm ảnh chụp thực tế (Tùy chọn, tối đa 2MB)</label>
                    <input type="file" name="image" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp">
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-4">
                    <button type="submit" class="btn btn-coffee px-4">
                        <i class="bi bi-send-fill me-1"></i>Gửi đánh giá
                    </button>
                </div>
            </div>
            <div class="form-text small mt-2">
                <i class="bi bi-shield-check me-1"></i>Đánh giá mới sẽ ở trạng thái <strong>Chờ duyệt (pending)</strong> trước khi hiển thị công khai và cộng vào điểm trung bình của quán.
            </div>
        </form>
    <?php endif; ?>
</div>
