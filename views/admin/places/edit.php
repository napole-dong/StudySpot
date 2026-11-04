<?php
/**
 * Giao diện Chỉnh sửa Quán học bài tại TP. Long Xuyên & Quản lý Thư viện Ảnh (views/admin/places/edit.php)
 */
$districts = $districts ?? [
    'Phường Đông Xuyên',
    'Phường Mỹ Xuyên',
    'Phường Mỹ Long',
    'Phường Mỹ Bình',
    'Phường Mỹ Quý',
    'Phường Bình Khánh',
];
$placeId = (int) $place['id'];
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa quán #<?= $placeId ?>: <?= e($place['name']) ?></h1>
        <p class="text-muted small mb-0">Slug đường dẫn: <code><?= e($place['slug']) ?></code> • Khu vực: <strong><?= e($place['district']) ?></strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('place/show/' . $placeId) ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i>Xem trang chi tiết
        </a>
        <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>
</div>

<!-- Form cập nhật thông tin quán & tải thêm ảnh mới -->
<form action="<?= base_url('admin/place/edit/' . $placeId) ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="surface-card p-4 mb-4">
                <h6 class="h6 fw-bold text-coffee mb-3">Thông tin &amp; địa chỉ</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Tên quán <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($old['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Danh mục loại hình <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($old['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold small">Địa chỉ (Số nhà &amp; Tên đường) <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" placeholder="VD: Số 123 Ung Văn Khiêm hoặc Hẻm 181 Võ Thị Sáu" value="<?= e($old['address'] ?? '') ?>" required>
                        <div class="form-text small text-muted">Chỉ nhập số nhà, tên đường hoặc hẻm (không cần gõ lại Phường / TP. Long Xuyên).</div>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Phường / Khu vực (TP. Long Xuyên) <span class="text-danger">*</span></label>
                        <select name="district" class="form-select <?= isset($errors['district']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Chọn Phường tại Long Xuyên --</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= e($d) ?>" <?= (($old['district'] ?? '') === $d) ? 'selected' : '' ?>>
                                    <?= e($d) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-muted">Chọn đúng phường thực tế để phục vụ bộ lọc tìm kiếm.</div>
                        <?php if (isset($errors['district'])): ?>
                            <div class="invalid-feedback"><?= e($errors['district']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mô tả chi tiết không gian học tập <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" required><?= e($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= e($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Cấu hình nâng cao thu gọn: Google Maps -->
                <div class="border rounded-3 p-3 bg-light">
                    <a class="text-decoration-none d-flex justify-content-between align-items-center text-secondary small fw-semibold" data-bs-toggle="collapse" href="#advancedMapSettings" role="button" aria-expanded="false" aria-controls="advancedMapSettings">
                        <span><i class="bi bi-gear-fill me-1 text-coffee"></i>Cấu hình nâng cao: tùy biến link nhúng Google Maps</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse mt-3 <?= !empty($old['google_map_url']) ? 'show' : '' ?>" id="advancedMapSettings">
                        <label class="form-label fw-semibold small">Link iframe Google Maps tùy biến (tùy chọn)</label>
                        <input type="text" name="google_map_url" class="form-control form-control-sm" placeholder="https://maps.google.com/maps?q=...&output=embed" value="<?= e($old['google_map_url'] ?? '') ?>">
                        <div class="form-text small text-muted">
                            <i class="bi bi-info-circle me-1"></i>Mặc định hệ thống đã <strong>tự động ghim bản đồ chính xác theo Tên quán + Địa chỉ + Phường tại Long Xuyên</strong>. Bạn chỉ cần điền ô này nếu muốn chỉ định tọa độ hoặc góc nhìn iframe riêng biệt.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tải thêm ảnh mới kèm Live Preview -->
            <div class="surface-card p-4 mb-4">
                <h6 class="h6 fw-bold text-coffee mb-2">2. Tải thêm ảnh mới cho quán</h5>
                <p class="text-muted small mb-3">Chọn một hoặc nhiều file ảnh (JPG, PNG, WEBP, tối đa 2MB/ảnh) để bổ sung vào album của quán.</p>
                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple data-image-preview="#editImagePreview">
                <div id="editImagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="surface-card p-4 mb-4">
                <h6 class="h6 fw-bold text-coffee mb-3">3. Tiêu chí học tập &amp; giá</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Giá thấp nhất (VNĐ)</label>
                    <input type="number" name="price_min" class="form-control" min="0" step="1000" placeholder="VD: 25000 (để trống nếu không giới hạn)" value="<?= e($old['price_min'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Giá cao nhất (VNĐ)</label>
                    <input type="number" name="price_max" class="form-control" min="0" step="1000" placeholder="VD: 45000 (để trống nếu không giới hạn)" value="<?= e($old['price_max'] ?? '') ?>">
                    <?php if (!empty($errors['price_max'])): ?><div class="text-danger small mt-1"><?= e($errors['price_max']) ?></div><?php endif; ?>
                </div>
                <p class="text-muted small mb-3">Nhập giá tự do theo VNĐ, không còn chọn mức cố định. Thẻ quán và trang chi tiết sẽ hiển thị đúng khoảng giá này.</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mức độ tiếng ồn</label>
                    <select name="noise_level" class="form-select">
                        <option value="yen_tinh" <?= (($old['noise_level'] ?? '') === 'yen_tinh') ? 'selected' : '' ?>>Yên tĩnh (học tập / đọc sách)</option>
                        <option value="vua" <?= (($old['noise_level'] ?? '') === 'vua') ? 'selected' : '' ?>>Độ ồn vừa (Thảo luận nhóm)</option>
                        <option value="on" <?= (($old['noise_level'] ?? '') === 'on') ? 'selected' : '' ?>>Khá nhộn nhịp (thư giãn / giao lưu)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block">Tiện ích tại quán</label>
                    <?php if (empty($amenities)): ?>
                        <p class="text-muted small mb-0">Chưa có tiện ích nào. Thêm tiện ích mới ở trang Quản trị &gt; Tiện ích.</p>
                    <?php else: ?>
                        <?php foreach ($amenities as $am): ?>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="amenity_<?= (int) $am['id'] ?>" name="amenity_ids[]" value="<?= (int) $am['id'] ?>" <?= in_array((int) $am['id'], array_map('intval', $old['amenity_ids'] ?? []), true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="amenity_<?= (int) $am['id'] ?>"><?= e($am['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <hr>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Giờ mở cửa</label>
                        <input type="time" name="open_time" class="form-control" value="<?= e(substr($old['open_time'] ?? '06:30', 0, 5)) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Giờ đóng cửa</label>
                        <input type="time" name="close_time" class="form-control" value="<?= e(substr($old['close_time'] ?? '22:00', 0, 5)) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Trạng thái hiển thị</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Hiển thị công khai (Active)</option>
                        <option value="hidden" <?= (($old['status'] ?? '') === 'hidden') ? 'selected' : '' ?>>Tạm ẩn (Hidden)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold rounded-pill">
                    <i class="bi bi-save me-1"></i>Lưu cập nhật
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Khu vực quản lý các ảnh hiện có (Chọn ảnh bìa / Xóa từng ảnh) -->
<div class="surface-card p-4 mt-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="h6 fw-bold text-coffee mb-0">
            <i class="bi bi-images me-2"></i>Danh sách ảnh hiện có của quán (<?= count($images) ?> ảnh)
        </h5>
    </div>

    <?php if (empty($images)): ?>
        <div class="alert alert-light border text-center mb-0">
            Quán hiện chưa có ảnh nào. Hệ thống đang hiển thị ảnh <code>placeholder.svg</code> mặc định.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($images as $img): ?>
                <?php $isCover = ((int) $img['is_cover'] === 1); ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="surface-card h-100 <?= $isCover ? 'border-warning border-2' : '' ?> overflow-hidden">
                        <div class="position-relative">
                            <img src="<?= get_image_url($img['image_path'], 'place') ?>" class="w-100 object-fit-cover" style="height: 155px;" alt="Ảnh quán">
                            <?php if ($isCover): ?>
                                <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark shadow-sm">
                                    <i class="bi bi-star-fill me-1"></i>Ảnh bìa chính
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-2 d-flex flex-column justify-content-between">
                            <div class="small text-muted text-truncate mb-2" title="<?= e($img['image_path']) ?>">
                                <?= e(basename($img['image_path'])) ?>
                            </div>
                            <div class="d-flex gap-1">
                                <?php if (!$isCover): ?>
                                    <form action="<?= base_url('admin/place/setCover/' . $placeId . '/' . (int) $img['id']) ?>" method="POST" class="flex-grow-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark w-100" data-bs-toggle="tooltip" title="Đặt ảnh này làm ảnh đại diện chính của quán">
                                            <i class="bi bi-star me-1"></i>Đặt làm bìa
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-warning flex-grow-1 disabled">
                                        <i class="bi bi-check2 me-1"></i>Đang là bìa
                                    </button>
                                <?php endif; ?>

                                <form action="<?= base_url('admin/place/deleteImage/' . $placeId . '/' . (int) $img['id']) ?>" method="POST" onsubmit="return confirm('Xóa ảnh này khỏi quán?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Xóa ảnh này">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
