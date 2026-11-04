<?php
/**
 * Footer dùng chung — bản chuyên nghiệp: nền trắng, viền trên mảnh,
 * 3 nhóm link gọn, chữ mực / xám, không nền tối, không tiêu đề vàng.
 */
?>
<footer class="footer-warm pt-4 pb-4 mt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 fw-bold fs-4 text-dark mb-3">
                    <span class="brand-badge d-inline-flex align-items-center justify-content-center rounded-3 text-white">
                        <i class="bi bi-cup-hot-fill"></i>
                    </span>
                    <span>StudySpot <span class="fs-6 fw-normal text-muted">Long Xuyên</span></span>
                </div>
                <p class="small mb-3 text-muted">
                    <?= e(SITE_SLOGAN) ?> tại TP. Long Xuyên, An Giang. Khám phá nhanh góc cà phê học bài quanh Trường Đại học An Giang (AGU).
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('place?amenity[]=wifi') ?>" class="pill-amenity text-decoration-none">Wifi mạnh</a>
                    <a href="<?= base_url('place?amenity[]=outlet') ?>" class="pill-amenity text-decoration-none">Nhiều ổ cắm</a>
                    <a href="<?= base_url('place?noise_level=yen_tinh') ?>" class="pill-amenity text-decoration-none">Yên tĩnh</a>
                    <a href="<?= base_url('place?price_range=2') ?>" class="pill-amenity text-decoration-none">Giá sinh viên AGU</a>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.82rem; letter-spacing: 0.06em;">KHU VỰC LONG XUYÊN</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= base_url('place?district=' . urlencode('Phường Đông Xuyên')) ?>" class="text-muted text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Phường Đông Xuyên (Khu AGU)</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?district=' . urlencode('Phường Mỹ Xuyên')) ?>" class="text-muted text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Phường Mỹ Xuyên</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?district=' . urlencode('Phường Mỹ Long')) ?>" class="text-muted text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Phường Mỹ Long</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?district=' . urlencode('Phường Mỹ Quý')) ?>" class="text-muted text-decoration-none"><i class="bi bi-geo-alt me-1"></i>Phường Mỹ Quý (KDC Tây Sông Hậu)</a></li>
                    <li class="mb-2"><a href="<?= base_url('place') ?>" class="text-muted text-decoration-none"><i class="bi bi-compass me-1"></i>Tất cả địa điểm tại Long Xuyên</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="fw-bold mb-3 text-dark" style="font-size: 0.82rem; letter-spacing: 0.06em;">KHÁM PHÁ THEO NHU CẦU</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= base_url('place?category_id=1') ?>" class="text-muted text-decoration-none">Cà phê Học bài &amp; Đọc sách</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?category_id=2') ?>" class="text-muted text-decoration-none">Cà phê Mở khuya</a></li>
                    <li class="mb-2"><a href="<?= base_url('place?category_id=3') ?>" class="text-muted text-decoration-none">Không gian Yên tĩnh &amp; Học nhóm</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-4" style="border-color: #E5E7EB;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <div>&copy; <?= date('Y') ?> <strong class="text-dark"><?= e(SITE_NAME) ?> Long Xuyên</strong>. Xây dựng phục vụ cộng đồng sinh viên Đại học An Giang.</div>
            <div class="mt-2 mt-md-0">Long Xuyên &bull; Đại học An Giang</div>
        </div>
    </div>
</footer>
