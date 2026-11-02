<?php
/**
 * Script khởi tạo mật khẩu thật (bcrypt) cho toàn bộ tài khoản mẫu trong bảng users
 * Đường dẫn chạy 1 lần trên trình duyệt: http://localhost/studyspot/database/seed_users.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/helpers/functions.php';

$defaultPasswordPlain = '123456';
$hashedPassword = password_hash($defaultPasswordPlain, PASSWORD_DEFAULT);

$error = null;
$updatedCount = 0;
$users = [];

try {
    $pdo = Model::getConnection();

    // Cập nhật mật khẩu hash chuẩn PHP cho tất cả tài khoản mẫu hiện có
    $stmt = $pdo->prepare('UPDATE users SET password = :password');
    $stmt->execute([':password' => $hashedPassword]);
    $updatedCount = $stmt->rowCount();

    // Lấy danh sách tài khoản sau khi cập nhật để hiển thị xác nhận
    $users = $pdo->query('SELECT id, name, email, role, status FROM users ORDER BY id ASC')->fetchAll();
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khởi tạo mật khẩu tài khoản mẫu | StudySpot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h1 class="h4 fw-bold mb-1">
                        <i class="bi bi-shield-check text-warning me-2"></i>Công cụ Khởi tạo Mật khẩu Tài khoản Mẫu (Seed Users)
                    </h1>
                    <p class="small text-secondary mb-0">Dự án StudySpot – Mã học phần SEE518</p>
                </div>

                <div class="card-body p-4">
                    <?php if ($error !== null): ?>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Lỗi kết nối hoặc chưa import CSDL!</h5>
                            <p class="mb-2">Vui lòng mở <strong>phpMyAdmin</strong> và import file <code>database/studyspot.sql</code> trước khi chạy công cụ này.</p>
                            <hr>
                            <div class="small font-monospace"><?= e($error) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <h5 class="alert-heading fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i>Đã mã hóa mật khẩu thành công cho <?= count($users) ?> tài khoản!
                            </h5>
                            <p class="mb-1">
                                Mật khẩu gốc dùng chung: <code class="fs-5 fw-bold text-dark"><?= e($defaultPasswordPlain) ?></code>
                            </p>
                            <p class="small mb-0 text-muted">
                                Chuỗi Hash sinh bởi <code>password_hash('123456', PASSWORD_DEFAULT)</code>:
                                <br><code><?= e($hashedPassword) ?></code>
                            </p>
                        </div>

                        <h6 class="fw-bold mt-4 mb-3">Danh sách tài khoản mẫu sẵn sàng đăng nhập:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email đăng nhập</th>
                                        <th>Mật khẩu</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?= (int) $u['id'] ?></td>
                                            <td class="fw-semibold"><?= e($u['name']) ?></td>
                                            <td><code><?= e($u['email']) ?></code></td>
                                            <td><code>123456</code></td>
                                            <td>
                                                <?php if ($u['role'] === 'admin'): ?>
                                                    <span class="badge bg-danger">Admin</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">User</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Đang khóa (Test chặn login)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                            <i class="bi bi-exclamation-octagon-fill fs-4 me-3 text-danger"></i>
                            <div class="small">
                                <strong>LƯU Ý BẢO MẬT QUAN TRỌNG:</strong> Sau khi chạy file này 1 lần để cập nhật mật khẩu thật vào CSDL, bạn <strong>nên xóa hoặc đổi tên file <code>database/seed_users.php</code></strong> trước khi đưa lên môi trường thực tế để tránh bị người khác reset lại mật khẩu.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Đã cập nhật CSDL: <strong><?= e(DB_NAME) ?></strong></span>
                    <a href="<?= base_url() ?>" class="btn btn-dark btn-sm px-3">
                        <i class="bi bi-house-door me-1"></i>Quay về Trang chủ StudySpot
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
