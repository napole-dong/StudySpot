<?php
// Form thêm quán — AdminPlaceController render
$districts = $districts ?? [
    'Phường Đông Xuyên',
    'Phường Mỹ Xuyên',
    'Phường Mỹ Long',
    'Phường Mỹ Bình',
    'Phường Mỹ Quý',
    'Phường Bình Khánh',
];
?>
<!-- Tiêu đề form thêm quán: trên cùng -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-plus-circle me-2"></i>Thêm quán học bài mới tại TP. Long Xuyên</h1>
        <p class="text-muted small mb-0">Điền thông tin chi tiết về không gian, địa chỉ phường tại Long Xuyên, tiện ích học tập và tải lên bộ ảnh của quán</p>
    </div>
        <!-- Nút Quay lại danh sách: về admin/place, không lưu những gì vừa nhập -->
    <a href="<?= base_url('admin/place') ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<!-- Form thêm quán: một form duy nhất, POST tới admin/place/create, enctype multipart/form-data để tải ảnh -->
<form action="<?= base_url('admin/place/create') ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="row g-4">
                <!-- Cột trái: Thông tin cơ bản & mô tả (thẻ Thông tin & địa chỉ, thẻ Thư viện ảnh) -->
        <div class="col-lg-8">
            <div class="surface-card p-4 mb-4">
                <h6 class="h6 fw-bold text-coffee mb-3">Thông tin &amp; địa chỉ</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                                                <!-- Trường tên quán name: bắt buộc, giữ lại từ $old['name'], lỗi hiện dưới trường từ $errors['name'] -->
                        <label class="form-label fw-semibold small">Tên quán <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="VD: NHÀ KHO Coffee hoặc Cồ-Lá Coffee" value="<?= e($old['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                                                <!-- Trường danh mục category_id: bắt buộc, lựa chọn từ $categories, giữ lựa chọn cũ từ $old['category_id'] -->
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
                    <div class="col-md-7">
                                                <!-- Trường địa chỉ address: bắt buộc, chỉ nhập số nhà và tên đường, không gõ lại phường hay TP. Long Xuyên -->
                        <label class="form-label fw-semibold small">Địa chỉ (Số nhà &amp; Tên đường) <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" placeholder="VD: Số 123 Ung Văn Khiêm hoặc Hẻm 181 Võ Thị Sáu" value="<?= e($old['address'] ?? '') ?>" required>
                        <div class="form-text small text-muted">Chỉ nhập số nhà, tên đường hoặc hẻm (không cần gõ lại Phường / TP. Long Xuyên).</div>
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-5">
                                                <!-- Trường phường district: bắt buộc, mặc định chọn sẵn Phường Đông Xuyên khi mở form lần đầu -->
                        <label class="form-label fw-semibold small">Phường / Khu vực (TP. Long Xuyên) <span class="text-danger">*</span></label>
                        <select name="district" class="form-select <?= isset($errors['district']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Chọn Phường tại Long Xuyên --</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= e($d) ?>" <?= (($old['district'] ?? 'Phường Đông Xuyên') === $d) ? 'selected' : '' ?>>
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
                    <label class="form-label fw-semibold small">Mô tả chi tiết không gian học tập, bàn ghế, giữ xe <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" placeholder="Mô tả tốc độ wifi, số lượng ổ cắm, độ yên tĩnh cho sinh viên AGU chạy đồ án, thực đơn và giá nước..." required><?= e($old['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= e($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                                <!-- Cấu hình nâng cao thu gọn: Google Maps (chỉ điền google_map_url khi muốn link riêng) -->
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

                        <!-- Thẻ thư viện hình ảnh & ảnh bìa: chọn nhiều ảnh (images[]), ô ẩn cover_index chỉ ảnh bìa -->
            <div class="surface-card p-4">
                <h6 class="h6 fw-bold text-coffee mb-2">2. Thư viện hình ảnh &amp; ảnh bìa</h5>
                <p class="text-muted small mb-3">
                    Chỉ chấp nhận định dạng <strong>JPG, PNG, WEBP</strong> (tối đa 2MB/ảnh). Bạn có thể chọn nhiều ảnh cùng lúc, xem trước ngay bên dưới và bấm chọn 1 ảnh làm <strong>Ảnh bìa chính</strong>.
                </p>

                <div class="mb-3">
                    <input type="file" name="images[]" id="imagesInput" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
                </div>

                <input type="hidden" name="cover_index" id="coverIndexInput" value="0">
                <div id="imagePreviewContainer" class="row g-3 mt-1"></div>
            </div>
        </div>

                <!-- Cột phải: Tiêu chí học tập & giá, tiện ích, giờ mở cửa, trạng thái và nút lưu -->
        <div class="col-lg-4">
            <div class="surface-card p-4 mb-4">
                <h6 class="h6 fw-bold text-coffee mb-3">3. Tiêu chí học tập &amp; giá</h5>

                <div class="mb-3">
                                        <!-- Trường giá thấp nhất price_min: nhập tự do theo VNĐ, để trống là không giới hạn dưới -->
                    <label class="form-label fw-semibold small">Giá thấp nhất (VNĐ)</label>
                    <input type="number" name="price_min" class="form-control" min="0" step="1000" placeholder="VD: 25000 (để trống nếu không giới hạn)" value="<?= e($old['price_min'] ?? '') ?>">
                </div>
                <div class="mb-3">
                                        <!-- Trường giá cao nhất price_max: kiểm tra max >= min do controller làm, view chỉ hiện lỗi từ $errors['price_max'] -->
                    <label class="form-label fw-semibold small">Giá cao nhất (VNĐ)</label>
                    <input type="number" name="price_max" class="form-control" min="0" step="1000" placeholder="VD: 45000 (để trống nếu không giới hạn)" value="<?= e($old['price_max'] ?? '') ?>">
                    <?php if (!empty($errors['price_max'])): ?><div class="text-danger small mt-1"><?= e($errors['price_max']) ?></div><?php endif; ?>
                </div>
                <p class="text-muted small mb-3">Nhập giá tự do theo VNĐ, không còn chọn mức cố định. Thẻ quán và trang chi tiết sẽ hiển thị đúng khoảng giá này.</p>
                </div>

                <div class="mb-3">
                                        <!-- Trường mức độ tiếng ồn noise_level: yen_tinh (yên tĩnh), vua (vừa), on (ồn) -->
                    <label class="form-label fw-semibold small">Mức độ tiếng ồn</label>
                    <select name="noise_level" class="form-select">
                        <option value="yen_tinh" <?= (($old['noise_level'] ?? '') === 'yen_tinh') ? 'selected' : '' ?>>Yên tĩnh (học tập / đọc sách)</option>
                        <option value="vua" <?= (($old['noise_level'] ?? '') === 'vua') ? 'selected' : '' ?>>Độ ồn vừa (Thảo luận nhóm)</option>
                        <option value="on" <?= (($old['noise_level'] ?? '') === 'on') ? 'selected' : '' ?>>Khá nhộn nhịp (thư giãn / giao lưu)</option>
                    </select>
                </div>

                <div class="mb-3">
                                        <!-- Nhóm checkbox tiện ích amenity_ids[]: công tắc lấy từ $amenities, tick sẵn theo $old['amenity_ids'] -->
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
                                                <!-- Cặp giờ mở/đóng cửa open_time và close_time: mặc định 06:30 và 22:00, nhãn “Phù hợp học khuya” do hệ thống tự gắn -->
                        <label class="form-label fw-semibold small">Giờ mở cửa</label>
                        <input type="time" name="open_time" class="form-control" value="<?= e(substr($old['open_time'] ?? '06:30', 0, 5)) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Giờ đóng cửa</label>
                        <input type="time" name="close_time" class="form-control" value="<?= e(substr($old['close_time'] ?? '22:00', 0, 5)) ?>" required>
                    </div>
                    <div class="col-12">
                        <div class="form-text small">Mẹo: Nếu quán mở từ 22:00 trở đi + có Wifi + có ổ cắm sẽ tự động được gắn nhãn <strong>"Phù hợp học khuya"</strong>.</div>
                    </div>
                </div>

                <div class="mb-4">
                                        <!-- Trường trạng thái status: active là hiển thị công khai, hidden là tạm ẩn, mặc định là active -->
                    <label class="form-label fw-semibold small">Trạng thái hiển thị</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Hiển thị công khai (Active)</option>
                        <option value="hidden" <?= (($old['status'] ?? '') === 'hidden') ? 'selected' : '' ?>>Tạm ẩn (Hidden)</option>
                    </select>
                </div>

                                <!-- Nút Lưu Quán học bài: nút submit duy nhất, gửi toàn bộ form về admin/place/create -->
                <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold rounded-pill">
                    <i class="bi bi-check2-circle me-1"></i>Lưu Quán học bài
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Đoạn JavaScript xem trước ảnh: chạy trên trình duyệt, giải thích bằng comment // trong script -->
<script>
// Khi chọn file ảnh (change của imagesInput): dọn khung xem trước cũ, cover_index về 0, vẽ thẻ xem trước bằng FileReader.
document.getElementById('imagesInput')?.addEventListener('change', function (e) {
    const container = document.getElementById('imagePreviewContainer');
    const coverInput = document.getElementById('coverIndexInput');
    container.innerHTML = '';
    coverInput.value = '0';

    const files = Array.from(e.target.files);
    files.forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4';
            col.innerHTML = `
                <div class="surface-card h-100 p-2 ${index === 0 ? 'border-warning border-2' : ''} preview-card" data-index="${index}" style="cursor:pointer;">
                    <img src="${ev.target.result}" class="w-100 admin-thumb mb-2" style="height: 120px;" alt="Preview">
                    <div class="text-center">
                        <div class="form-check d-inline-block mb-0">
                            <input class="form-check-input cover-radio" type="radio" name="cover_radio" value="${index}" ${index === 0 ? 'checked' : ''}>
                            <label class="form-check-label small fw-semibold">Đặt làm ảnh bìa</label>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(col);

            col.querySelector('.preview-card').addEventListener('click', function () {
                coverInput.value = index;
// Khi bấm một thẻ xem trước: thẻ đó nhận viền vàng, radio bìa được tick, cover_index cập nhật theo vị trí ảnh.
                document.querySelectorAll('.preview-card').forEach(c => c.classList.remove('border-warning', 'border-2'));
                this.classList.add('border-warning', 'border-2');
                this.querySelector('.cover-radio').checked = true;
            });
        };
        reader.readAsDataURL(file);
    });
});
</script>
