<?php
// Trang cá nhân thành viên: xem, sửa hồ sơ, đổi avatar, đổi mật khẩu.
class ProfileController extends Controller
{
    private User $userModel;
    private Favorite $favoriteModel;
    private Review $reviewModel;

    // Yêu cầu đăng nhập (requireLogin) rồi tạo sẵn model User, Favorite, Review.
    public function __construct()
    {
        $this->requireLogin();
        $this->userModel = new User();
        $this->favoriteModel = new Favorite();
        $this->reviewModel = new Review();
    }

    // Trang tổng quan hồ sơ cá nhân (/profile)
    public function index(): void
    {
        $sessionUser = Session::user();
        $userId = (int) $sessionUser['id'];

        $user = $this->userModel->findById($userId);
        $favoritePlaces = $this->favoriteModel->getFavoritePlacesByUser($userId);
        $myReviews = $this->reviewModel->getByUserId($userId);

        $this->render('profile/index', [
            'pageTitle'      => 'Trang cá nhân của tôi',
            'user'           => $user,
            'favoritePlaces' => $favoritePlaces,
            'myReviews'      => $myReviews,
        ]);
    }

    // Trang chỉnh sửa thông tin cá nhân, đổi Avatar & đổi Mật khẩu (/profile/edit)
    public function edit(): void
    {
        $sessionUser = Session::user();
        $userId = (int) $sessionUser['id'];
        $user = $this->userModel->findById($userId);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();

            // form_type phân biệt sửa thông tin và đổi mật khẩu, mặc định update_info.
            $formType = $_POST['form_type'] ?? 'update_info';

            // 1. Xử lý Cập nhật thông tin hồ sơ & đổi Avatar
            if ($formType === 'update_info') {
                $name = trim($_POST['name'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $avatarPath = $user['avatar'];

                // Họ tên 2–100 ký tự, giống quy tắc lúc đăng ký.
                if ($name === '' || mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
                    $errors['name'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
                }

                if ($phone !== '' && !preg_match('/^[0-9+\-\s]{9,15}$/', $phone)) {
                    $errors['phone'] = 'Số điện thoại không hợp lệ (9-15 chữ số).';
                }

                // Kiểm tra nếu người dùng có chọn upload Avatar mới
                if (!empty($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $uploadResult = upload_image($_FILES['avatar'], 'avatars');
                    if (!$uploadResult['success']) {
                        $errors['avatar'] = $uploadResult['error'];
                    } else {
                        // Chỉ xóa avatar cũ khi avatar mới đã upload thành công.
                        delete_uploaded_file($user['avatar']);
                        $avatarPath = $uploadResult['path'];
                    }
                }

                if (empty($errors)) {
                    $this->userModel->updateProfile($userId, [
                        'name'   => $name,
                        'phone'  => $phone,
                        'avatar' => $avatarPath,
                    ]);

                    // Đồng bộ lại thông tin trong Session để Navbar hiển thị ngay lập tức
                    $updatedUser = $this->userModel->findById($userId);
                    // Đồng bộ session sau khi sửa hồ sơ để navbar hiện ngay.
                    Session::set('user', [
                        'id'     => (int) $updatedUser['id'],
                        'name'   => $updatedUser['name'],
                        'email'  => $updatedUser['email'],
                        'phone'  => $updatedUser['phone'],
                        'avatar' => $updatedUser['avatar'],
                        'role'   => $updatedUser['role'],
                        'status' => $updatedUser['status'],
                    ]);

                    Session::flash('alert', 'Đã cập nhật thông tin cá nhân thành công!', 'success');
                    $this->redirect('profile');
                }
            }
            // 2. Xử lý Đổi mật khẩu
            elseif ($formType === 'change_password') {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                // Đổi mật khẩu phải biết mật khẩu cũ và khớp hash.
                if ($currentPassword === '' || !password_verify($currentPassword, $user['password'])) {
                    $errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
                }

                if ($newPassword === '' || strlen($newPassword) < 6) {
                    $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
                }

                if ($confirmPassword !== $newPassword) {
                    $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp.';
                }

                if (empty($errors)) {
                    // Mật khẩu mới do model băm (hash) trước khi lưu.
                    $this->userModel->updatePassword($userId, $newPassword);
                    Session::regenerate();
                    Session::flash('alert', 'Đã đổi mật khẩu thành công!', 'success');
                    $this->redirect('profile');
                }
            }
        }

        $this->render('profile/edit', [
            'pageTitle' => 'Chỉnh sửa Hồ sơ & Đổi mật khẩu',
            'user'      => $user,
            'errors'    => $errors,
        ]);
    }
}
