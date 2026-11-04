<?php
/**
 * Giao diện trang lỗi 404 thân thiện với người dùng
 */
?>
<div class="container py-4 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card surface-card rounded-4 p-4">
                <div class="display-1 fw-bold text-coffee mb-2">404</div>
                <h1 class="page-title">Không tìm thấy trang yêu cầu</h1>
                <p class="text-muted mb-4">
                    <?= e($errorMessage ?? 'Đường dẫn bạn truy cập không tồn tại, đã thay đổi hoặc tạm thời bị ẩn.') ?>
                </p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="<?= base_url() ?>" class="btn btn-coffee px-4">
                        <i class="bi bi-house-door me-1"></i>Về Trang chủ
                    </a>
                    <a href="<?= base_url('place') ?>" class="btn btn-outline-coffee px-4">
                        <i class="bi bi-compass me-1"></i>Khám phá quán
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
