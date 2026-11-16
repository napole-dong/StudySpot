<?php
// Trang Đề xuất quán mới của thành viên: $categories, $districts, $suggestions (đề xuất của chính mình).
?>
<div class="container py-4">
    <!-- Đầu trang: breadcrumb + tiêu đề. -->
    <div class="page-head d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-coffee">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đề xuất quán mới</li>
                </ol>
            </nav>
            <h1 class="page-title">
                <i class="bi bi-plus-circle me-2"></i>Đề xuất quán mới
            </h1>
            <p class="text-muted small mb-0">Biết quán nào học bài tốt ở Long Xuyên mà StudySpot chưa có? Gửi đề xuất, quản trị viên sẽ xem xét và bạn nhận thông báo ngay khi có kết quả.</p>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <!-- Cột trái: form gửi đề xuất. -->
        <div class="col-lg-5">
            <div class="card surface-card p-4">
                <h2 class="h6 fw-bold text-dark mb-3"><i class="bi bi-send me-2"></i>Gửi đề xuất mới</h2>
                <form method="post" action="<?= base_url('suggestion/store') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="sugName" class="form-label small fw-semibold">Tên quán <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sugName" name="name" maxlength="150" required placeholder="VD: Cà phê Sách An Nhiên">
                    </div>
                    <div class="mb-3">
                        <label for="sugAddress" class="form-label small fw-semibold">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sugAddress" name="address" maxlength="255" required placeholder="VD: 123 Trần Hưng Đạo">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="sugDistrict" class="form-label small fw-semibold">Phường / Khu vực <span class="text-danger">*</span></label>
                            <select class="form-select" id="sugDistrict" name="district" required>
                                <option value="">-- Chọn phường --</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?= e($district) ?>"><?= e($district) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sugCategory" class="form-label small fw-semibold">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="sugCategory" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int) $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="sugDescription" class="form-label small fw-semibold">Vì sao quán hợp để học tập?</label>
                        <textarea class="form-control" id="sugDescription" name="description" rows="3" maxlength="1000" placeholder="VD: Wifi mạnh, nhiều ổ cắm, yên tĩnh, mở cửa khuya..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-coffee rounded-pill px-4">
                        <i class="bi bi-send me-1"></i>Gửi đề xuất
                    </button>
                </form>
            </div>
        </div>

        <!-- Cột phải: các đề xuất đã gửi của chính mình và trạng thái duyệt. -->
        <div class="col-lg-7">
            <h2 class="h6 fw-bold text-dark mb-3"><i class="bi bi-list-check me-2"></i>Đề xuất của tôi (<?= count($suggestions) ?>)</h2>
            <?php if (empty($suggestions)): ?>
                <div class="card surface-card p-4 text-center text-muted">
                    <i class="bi bi-inbox d-block mb-2" style="font-size: 1.8rem;"></i>
                    Bạn chưa gửi đề xuất nào. Hãy là người đầu tiên giới thiệu một quán học bài tốt nhé!
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($suggestions as $sug): ?>
                        <div class="card surface-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-bold text-dark"><?= e($sug['name']) ?></div>
                                    <div class="small text-muted">
                                        <i class="bi bi-geo-alt me-1"></i><?= e($sug['address']) ?>, <?= e($sug['district']) ?>
                                        <?php if (!empty($sug['category_name'])): ?>
                                            · <?= e($sug['category_name']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.76rem;">
                                        <i class="bi bi-calendar3 me-1"></i>Gửi ngày <?= date('d/m/Y H:i', strtotime($sug['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <?php if ($sug['status'] === 'pending'): ?>
                                        <span class="badge rounded-pill text-bg-warning">Chờ duyệt</span>
                                    <?php elseif ($sug['status'] === 'approved'): ?>
                                        <span class="badge rounded-pill text-bg-success">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill text-bg-secondary">Không được duyệt</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($sug['description'])): ?>
                                <p class="small text-dark mb-0 mt-2" style="line-height: 1.6;"><?= nl2br(e($sug['description'])) ?></p>
                            <?php endif; ?>
                            <?php if ($sug['status'] === 'approved' && !empty($sug['created_place_id'])): ?>
                                <div class="mt-2">
                                    <a href="<?= base_url('place/show/' . (int) $sug['created_place_id']) ?>" class="btn btn-sm btn-outline-coffee rounded-pill px-3">
                                        <i class="bi bi-shop me-1"></i>Xem quán đã được thêm
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
