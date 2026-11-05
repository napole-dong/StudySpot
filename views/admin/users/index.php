<?php
// Biến $currentAdminId: ID admin đang đăng nhập từ Session::user()['id'], mặc định là 0.
$currentAdminId = (int) (Session::user()['id'] ?? 0);
?>
<!-- Tiêu đề trang quản lý tài khoản: trên cùng, tổng số từ $total, tài khoản bị khóa sẽ không đăng nhập được -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-people me-2"></i>Quản lý tài khoản người dùng</h1>
        <p class="text-muted small mb-0">Tổng cộng <strong><?= number_format((int) $total) ?></strong> tài khoản trong hệ thống. Tài khoản bị khóa sẽ không thể đăng nhập.</p>
    </div>
</div>

<!-- Form tìm kiếm & lọc người dùng: GET tới admin/user, tìm theo tên, email hoặc số điện thoại -->
<div class="surface-card p-4 mb-4">
    <form action="<?= base_url('admin/user') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-coffee"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo họ tên, email hoặc số điện thoại..." value="<?= e($filters['q'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Mọi trạng thái --</option>
                <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Đang hoạt động (Active)</option>
                <option value="locked" <?= (($filters['status'] ?? '') === 'locked') ? 'selected' : '' ?>>Đang bị khóa (Locked)</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-coffee flex-grow-1"><i class="bi bi-funnel me-1"></i>Tìm kiếm &amp; lọc</button>
            <a href="<?= base_url('admin/user') ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Đặt lại bộ lọc">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Bảng danh sách người dùng: dưới form lọc, 8 cột (.admin-table, .admin-thumb) -->
<div class="surface-card overflow-hidden">
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th width="65" class="ps-3">ID</th>
                    <th>Thành viên</th>
                    <th>Số điện thoại</th>
                    <th class="text-center">Vai trò</th>
                    <th class="text-center">Số đánh giá</th>
                    <th class="text-center">Ngày tạo</th>
                    <th class="text-center">Trạng thái</th>
                    <th width="160" class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                                <!-- Nếu $users rỗng thì hiện một dòng gộp 8 cột -->
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block mb-2 text-coffee"></i>
                            Không tìm thấy người dùng nào phù hợp.
                        </td>
                    </tr>
                <?php else: ?>
                                        <!-- Vòng lặp danh sách người dùng: $u là một tài khoản trong $users; $isSelf là chính mình, $isAdminRole là vai trò admin -->
                    <?php foreach ($users as $u): ?>
                        <?php
                        $uid = (int) $u['id'];
                        $isSelf = ($uid === $currentAdminId);
                        $isAdminRole = ($u['role'] === 'admin');
                        ?>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">#<?= $uid ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                                                        <!-- Ảnh đại diện: get_image_url() từ $u['avatar'], tài khoản là chính mình ($isSelf) thì có nhãn “Bạn” -->
                                    <img src="<?= get_image_url($u['avatar'] ?? null, 'avatar') ?>" alt="<?= e($u['name']) ?>" class="admin-thumb" width="42" height="42">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= e($u['name']) ?>
                                            <?php if ($isSelf): ?>
                                                <span class="pill-amenity ms-1 py-0 px-2">Bạn</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted"><?= e($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="small"><?= e($u['phone'] ?: '—') ?></td>
                            <td class="text-center">
                                                                <!-- Huy hiệu vai trò: $isAdminRole đúng thì hiện “Admin”, còn lại là “Sinh viên” -->
                                <?php if ($isAdminRole): ?>
                                    <span class="badge rounded-pill px-2 py-1" style="background: #111827; color: #fff;"><i class="bi bi-shield-lock me-1"></i>Admin</span>
                                <?php else: ?>
                                    <span class="pill-amenity">Sinh viên</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="pill-amenity"><?= (int) ($u['review_count'] ?? 0) ?></span>
                            </td>
                            <td class="text-center small text-muted">
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            </td>
                                                        <!-- Huy hiệu trạng thái tài khoản: active là Hoạt động, còn lại là Đang khóa -->
                            <td class="text-center">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge badge-status-open rounded-pill px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-status-closed rounded-pill px-2 py-1">
                                        <i class="bi bi-lock-fill me-1"></i>Đang khóa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                                                <!-- Cột thao tác — Được bảo vệ: không hiện nút khóa cho chính mình ($isSelf) hoặc mọi tài khoản admin ($isAdminRole), chỉ hiện “Được bảo vệ” -->
                                <?php if ($isSelf || $isAdminRole): ?>
                                    <span class="small text-muted fst-italic"><i class="bi bi-shield-check text-coffee me-1"></i>Được bảo vệ</span>
                                <?php else: ?>
                                    <!-- Form khóa/mở khóa: chỉ có với tài khoản sinh viên bình thường, POST tới admin/user/toggleStatus/{id} để đảo trạng thái -->
                                    <form
                                        action="<?= base_url('admin/user/toggleStatus/' . $uid) ?>"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('<?= $u['status'] === 'active' ? 'Bạn có chắc muốn KHÓA tài khoản này?' : 'Mở khóa cho tài khoản này?' ?>');"
                                    >
                                        <?= csrf_field() ?>
                                                                                <!-- Nút khóa/mở: đang hoạt động thì nút khóa viền đỏ, đang bị khóa thì nút mở khóa xanh lá -->
                                        <?php if ($u['status'] === 'active'): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Khóa tài khoản thành viên này">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Mở khóa cho tài khoản này">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Phân trang: render_pagination() từ $currentPage và $totalPages về admin/user -->
<?= render_pagination($currentPage, $totalPages, 'admin/user') ?>
