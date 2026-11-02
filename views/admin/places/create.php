<?php
/**
 * Giao diện Thêm mới Quán học bài kèm Upload nhiều ảnh & Chọn ảnh bìa (views/admin/places/create.php)
 */
$districts = [
    'Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 7',
    'Quận 10', 'Quận Bình Thạnh', 'Quận Phú Nhuận',
    'Quận Tân Bình', 'Quận Gò Vấp', 'TP. Thủ Đức',
];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-plus-circle text-coffee me-2"></i>Thêm Quán học bài mới</h1>
        <p class="text-muted small mb-0">Điền thông tin chi tiết về không gian, tiện ích học tập và tải lên bộ ảnh của quán</p>
    </div>
    <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<form action="<?= base_url('admin/place/create') ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Cột trái: Thông tin cơ bản & mô tả -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-3">1. Thông tin định danh &amp; Địa chỉ</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Tên quán <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="VD: The Library Coffee & Study Hub" value="<?= e($old['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Danh mục loại hình <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Chọn loại hình --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($old['category_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback"><?= e($errors['category_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Địa chỉ số nhà, tên đường, phường <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" placeholder="VD: 24 Đường D2 (Nguyễn Gia Trí), Phường 25" value="<?= e($old['address'] ?? '') ?>" required>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Quận / Khu vực <span class="text-danger">*</span></label>
                        <input type="text" name="district" list="districtOptions" class="form-control <?= isset($errors['district']) ? 'is-invalid' : '' ?>" placeholder="Chọn hoặc nhập quận" value="<?= e($old['district'] ?? '') ?>" required>
                        <datalist id="districtOptions">
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= e($d) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                        <?php if (isset($errors['district'])): ?>
                            <div class="invalid-feedback"><?= e($errors['district']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mô tả chi tiết không gian học tập, bàn ghế, giữ xe <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" placeholder="Mô tả tốc độ wifi, số lượng ổ cắm, ánh sáng, độ yên tĩnh, giá gửi xe..." required><?= e($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= e($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold small">Link nhúng Google Maps (URL trong thuộc tính <code>src</code> của iframe)</label>
                    <input type="text" name="google_map_url" class="form-control" placeholder="https://www.google.com/maps?q=...+Ho+Chi+Minh&output=embed" value="<?= e($old['google_map_url'] ?? '') ?>">
                    <div class="form-text small">Có thể dán link dạng <code>https://www.google.com/maps?q=Ten+Dia+Diem&amp;output=embed</code></div>
                </div>
            </div>

            <!-- Upload nhiều ảnh & Chọn ảnh bìa -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-2">2. Thư viện Hình ảnh &amp; Ảnh bìa</h5>
                <p class="text-muted small mb-3">
                    Chỉ chấp nhận định dạng <strong>JPG, PNG, WEBP</strong> (tối đa 2MB/ảnh). Bạn có thể chọn nhiều ảnh cùng lúc và bấm chọn 1 ảnh làm <strong>Ảnh bìa chính</strong>.
                </p>

                <div class="mb-3">
                    <input type="file" name="images[]" id="imagesInput" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
                </div>

                <input type="hidden" name="cover_index" id="coverIndexInput" value="0">
                <div id="imagePreviewContainer" class="row g-3 mt-1"></div>
            </div>
        </div>

        <!-- Cột phải: Tiện ích học bài, mức giá, giờ mở cửa -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="h6 fw-bold text-uppercase text-coffee mb-3">3. Tiêu chí Học tập &amp; Giá</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mức giá trung bình</label>
                    <select name="price_range" class="form-select">
                        <option value="1" <?= ((int) ($old['price_range'] ?? 2) === 1) ? 'selected' : '' ?>>1 - Dưới 35.000đ ($ - Rất rẻ)</option>
                        <option value="2" <?= ((int) ($old['price_range'] ?? 2) === 2) ? 'selected' : '' ?>>2 - Từ 35.000đ - 50.000đ ($$ - Giá sinh viên)</option>
                        <option value="3" <?= ((int) ($old['price_range'] ?? 2) === 3) ? 'selected' : '' ?>>3 - Từ 50.000đ - 80.000đ ($$$ - Tầm trung)</option>
                        <option value="4" <?= ((int) ($old['price_range'] ?? 2) === 4) ? 'selected' : '' ?>>4 - Trên 80.000đ ($$$$ - Cao cấp)</option>
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
                    <div class="col-12">
                        <div class="form-text small">Mẹo: Nếu quán mở sau 22:00 + có Wifi + có Ổ cắm sẽ tự động được gắn nhãn <strong>"Phù hợp chạy deadline"</strong>.</div>
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
                    <i class="bi bi-check2-circle me-1"></i>Lưu Quán học bài
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('imagesInput')?.addEventListener('change', function (e) {
    const container = document.getElementById('imagePreviewContainer');
    const coverInput = document.getElementById('coverIndexInput');
    container.innerHTML = '';
    coverInput.value = '0';

    const files = Array.from(e.target.files);
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (ev) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            col.innerHTML = `
                <div class="card h-100 border ${index === 0 ? 'border-warning border-2' : ''} preview-card" data-index="${index}" style="cursor:pointer;">
                    <img src="${ev.target.result}" class="card-img-top object-fit-cover" style="height: 120px;" alt="Preview">
                    <div class="card-body p-2 text-center">
                        <div class="form-check d-inline-block mb-0">
                            <input class="form-check-input cover-radio" type="radio" name="cover_radio" value="${index}" ${index === 0 ? 'checked' : ''}>
                            <label class="form-check-label small fw-semibold">Ảnh bìa</label>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(col);

            col.querySelector('.preview-card').addEventListener('click', function () {
                coverInput.value = index;
                document.querySelectorAll('.preview-card').forEach(c => c.classList.remove('border-warning', 'border-2'));
                this.classList.add('border-warning', 'border-2');
                this.querySelector('.cover-radio').checked = true;
            });
        };
        reader.readAsDataURL(file);
    });
});
</script>
