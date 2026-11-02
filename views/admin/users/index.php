<?php
/**
 * Giao diện Quản lý Người dùng dành cho Admin (views/admin/users/index.php)
 */
$currentAdminId = (int) (Session::user()['id'] ?? 0);
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1"><i class="bi bi-people text-coffee me-2"></i>Quản lý Tài khoản Người dùng</h1>
        <p class="text-muted small mb-0">Tổng cộng <strong><?= (int) $total ?></strong> tài khoản trong hệ thống. Tài khoản bị khóa sẽ không thể đăng nhập.</p>
    </div>
</div>

<!-- Bộ lọc tìm kiếm người dùng -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/user') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo họ tên, email hoặc số điện thoại..." value="<?= e($filters['q'] ?? '') ?>">
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
                <button type="submit" class="btn btn-sm btn-dark flex-grow-1">Tìm kiếm &amp; Lọc</button>
                <a href="<?= base_url('admin/user') ?>" class="btn btn-sm btn-outline-secondary" title="Đặt lại">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách người dùng -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th width="65" class="ps-3">ID</th>
                    <th>Thành viên</th>
                    <th>Số điện thoại</th>
                    <th class="text-center">Vai trò</th>
                    <th class="text-center">Số Review</th>
                    <th class="text-center">Ngày tạo</th>
                    <th class="text-center">Trạng thái</th>
                    <th width="160" class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">Không tìm thấy người dùng nào phù hợp.</td>
                    </tr>
                <?php else: ?>
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
                                    <img src="<?= get_image_url($u['avatar'] ?? null, 'avatar') ?>" alt="<?= e($u['name']) ?>" class="rounded-circle object-fit-cover border" width="40" height="40">
                                    <div>
                                        <div class="fw-bold text-dark">
                                            <?= e($u['name']) ?>
                                            <?php if ($isSelf): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis ms-1">Bạn</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted"><?= e($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="small"><?= e($u['phone'] ?: '—') ?></td>
                            <td class="text-center">
                                <?php if ($isAdminRole): ?>
                                    <span class="badge bg-danger px-2 py-1"><i class="bi bi-shield-lock me-1"></i>Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-dark border px-2 py-1">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?= (int) ($u['review_count'] ?? 0) ?></span>
                            </td>
                            <td class="text-center small text-muted">
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-lock-fill me-1"></i>Đang khóa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <?php if ($isSelf || $isAdminRole): ?>
                                    <span class="small text-muted fst-italic">Được bảo vệ</span>
                                <?php else: ?>
                                    <form
                                        action="<?= base_url('admin/user/toggleStatus/' . $uid) ?>"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('<?= $u['status'] === 'active' ? 'Bạn có chắc muốn KHÓA tài khoản này?' : 'Mở khóa cho tài khoản này?' ?>');"
                                    >
                                        <?= csrf_field() ?>
                                        <?php if ($u['status'] === 'active'): ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-lock me-1"></i>Khóa TK
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-unlock me-1"></i>Mở khóa
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

<?= render_pagination($currentPage, $totalPages, 'admin/user') ?>
