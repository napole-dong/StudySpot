<?php
/**
 * Giao diện Chỉnh sửa Quán học bài & Quản lý Thư viện Ảnh (views/admin/places/edit.php)
 */
$districts = [
    'Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 7',
    'Quận 10', 'Quận Bình Thạnh', 'Quận Phú Nhuận',
    'Quận Tân Bình', 'Quận Gò Vấp', 'TP. Thủ Đức',
];
$placeId = (int) $place['id'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-pencil-square text-coffee me-2"></i>Chỉnh sửa Quán #<?= $placeId ?></h1>
        <p class="text-muted small mb-0">Slug tự động: <code><?= e($place['slug']) ?></code></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('place/show/' . $placeId) ?>" target="_blank" class="btn btn-sm btn-outline-dark">
            <i class="bi bi-box-arrow-up-right me-1"></i>Xem trang chi tiết
        </a>
        <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>
</div>

<!-- Form cập nhật thông tin quán & tải thêm ảnh mới -->
<form action="<?= base_url('admin/place/edit/' . $placeId) ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-3">1. Thông tin định danh &amp; Địa chỉ</h5>

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
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" value="<?= e($old['address'] ?? '') ?>" required>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Quận / Khu vực <span class="text-danger">*</span></label>
                        <input type="text" name="district" list="districtOptions" class="form-control <?= isset($errors['district']) ? 'is-invalid' : '' ?>" value="<?= e($old['district'] ?? '') ?>" required>
                        <datalist id="districtOptions">
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= e($d) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mô tả chi tiết không gian học tập <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" required><?= e($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= e($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold small">Link nhúng Google Maps (iframe src)</label>
                    <input type="text" name="google_map_url" class="form-control" value="<?= e($old['google_map_url'] ?? '') ?>">
                </div>
            </div>

            <!-- Tải thêm ảnh mới -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-2">2. Tải thêm ảnh mới cho quán</h5>
                <p class="text-muted small mb-3">Chọn một hoặc nhiều file ảnh (JPG, PNG, WEBP, tối đa 2MB/ảnh) để bổ sung vào album của quán.</p>
                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-3">3. Tiêu chí Học tập &amp; Giá</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mức giá trung bình</label>
                    <select name="price_range" class="form-select">
                        <option value="1" <?= ((int) ($old['price_range'] ?? 2) === 1) ? 'selected' : '' ?>>1 - Dưới 35.000đ ($)</option>
                        <option value="2" <?= ((int) ($old['price_range'] ?? 2) === 2) ? 'selected' : '' ?>>2 - Từ 35.000đ - 50.000đ ($$)</option>
                        <option value="3" <?= ((int) ($old['price_range'] ?? 2) === 3) ? 'selected' : '' ?>>3 - Từ 50.000đ - 80.000đ ($$$)</option>
                        <option value="4" <?= ((int) ($old['price_range'] ?? 2) === 4) ? 'selected' : '' ?>>4 - Trên 80.000đ ($$$$)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mức độ tiếng ồn</label>
                    <select name="noise_level" class="form-select">
                        <option value="yen_tinh" <?= (($old['noise_level'] ?? '') === 'yen_tinh') ? 'selected' : '' ?>>Yên tĩnh (Học tập / Đọc sách)</option>
                        <option value="vua" <?= (($old['noise_level'] ?? '') === 'vua') ? 'selected' : '' ?>>Độ ồn vừa (Thảo luận nhóm)</option>
                        <option value="on" <?= (($old['noise_level'] ?? '') === 'on') ? 'selected' : '' ?>>Khá nhộn nhịp (Thư giãn / Giao lưu)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small d-block">Tiện ích tại quán</label>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="has_wifi" name="has_wifi" value="1" <?= !empty($old['has_wifi']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="has_wifi"><i class="bi bi-wifi text-info me-1"></i>Có Wifi tốc độ cao</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="has_outlet" name="has_outlet" value="1" <?= !empty($old['has_outlet']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="has_outlet"><i class="bi bi-plug text-warning me-1"></i>Nhiều ổ cắm điện tại bàn</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_ac" name="has_ac" value="1" <?= !empty($old['has_ac']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="has_ac"><i class="bi bi-snow text-primary me-1"></i>Không gian có Máy lạnh</label>
                    </div>
                </div>

                <hr>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Giờ mở cửa</label>
                        <input type="time" name="open_time" class="form-control" value="<?= e(substr($old['open_time'] ?? '07:00', 0, 5)) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Giờ đóng cửa</label>
                        <input type="time" name="close_time" class="form-control" value="<?= e(substr($old['close_time'] ?? '22:30', 0, 5)) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Trạng thái hiển thị</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Hiển thị công khai (Active)</option>
                        <option value="hidden" <?= (($old['status'] ?? '') === 'hidden') ? 'selected' : '' ?>>Tạm ẩn (Hidden)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold">
                    <i class="bi bi-save me-1"></i>Lưu cập nhật
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Khu vực quản lý các ảnh hiện có (Chọn ảnh bìa / Xóa từng ảnh) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mt-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="h6 fw-bold text-uppercase text-coffee mb-0">
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
                    <div class="card h-100 <?= $isCover ? 'border-warning border-2 shadow-sm' : 'border' ?> rounded-3 overflow-hidden">
                        <div class="position-relative">
                            <img src="<?= get_image_url($img['image_path'], 'place') ?>" class="card-img-top object-fit-cover" style="height: 150px;" alt="Ảnh quán">
                            <?php if ($isCover): ?>
                                <span class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark shadow-sm">
                                    <i class="bi bi-star-fill me-1"></i>Ảnh bìa chính
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                            <div class="small text-muted text-truncate mb-2" title="<?= e($img['image_path']) ?>">
                                <?= e(basename($img['image_path'])) ?>
                            </div>
                            <div class="d-flex gap-1">
                                <?php if (!$isCover): ?>
                                    <form action="<?= base_url('admin/place/setCover/' . $placeId . '/' . (int) $img['id']) ?>" method="POST" class="flex-grow-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-xs btn-sm btn-outline-warning text-dark w-100">
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
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa ảnh">
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
