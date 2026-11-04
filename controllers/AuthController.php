<?php
// Xác thực: đăng nhập, đăng ký, đăng xuất.
class AuthController extends Controller
{
    private User $userModel;

    // Tạo sẵn model User cho đăng nhập và đăng ký.
    public function __construct()
    {
        $this->userModel = new User();
    }

    // Hiển thị và xử lý form Đăng nhập (/auth/login)
    public function login(): void
    {
        // Nếu đã đăng nhập thì chuyển về trang chủ
        if (Session::isLoggedIn()) {
            $this->redirect('');
        }

        $errors = [];
        $oldEmail = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra CSRF trước khi xử lý form.
            $this->verifyCsrfToken();

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $oldEmail = $email;

            if ($email === '') {
                $errors['email'] = 'Vui lòng nhập địa chỉ email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Định dạng email không hợp lệ.';
            }

            if ($password === '') {
                $errors['password'] = 'Vui lòng nhập mật khẩu.';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

            // Sai email hay sai mật khẩu đều báo chung một câu, không lộ email nào tồn tại.
                if (!$user || !password_verify($password, $user['password'])) {
                    $errors['general'] = 'Email hoặc mật khẩu không chính xác.';
            // Tài khoản bị khóa thì không cho vào dù mật khẩu đúng.
                } elseif ($user['status'] === 'locked') {
                    $errors['general'] = 'Tài khoản của bạn hiện đang bị khóa. Vui lòng liên hệ Quản trị viên.';
                } else {
                    // Đổi Session ID sau khi đăng nhập (chống Session Fixation).
                    Session::regenerate();

                    // Lưu thông tin cần thiết vào Session (không lưu password hash)
                    Session::set('user', [
                        'id'     => (int) $user['id'],
                        'name'   => $user['name'],
                        'email'  => $user['email'],
                        'phone'  => $user['phone'],
                        'avatar' => $user['avatar'],
                        'role'   => $user['role'],
                        'status' => $user['status'],
                    ]);

                    Session::flash('alert', 'Chào mừng ' . $user['name'] . ' đã quay trở lại StudySpot!', 'success');

                    // Sau đăng nhập: admin về admin/dashboard, thành viên về trang chủ.
                    if ($user['role'] === 'admin') {
                        $this->redirect('admin/dashboard');
                    }
                    $this->redirect('');
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle' => 'Đăng nhập',
            'errors'    => $errors,
            'oldEmail'  => $oldEmail,
        ]);
    }

    // Hiển thị và xử lý form Đăng ký thành viên mới (/auth/register)
    public function register(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('');
        }

        $errors = [];
        $old = [
            'name'  => '',
            'email' => '',
            'phone' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra CSRF trước khi xử lý form.
            $this->verifyCsrfToken();

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $old = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
            ];

            // Validate Họ và tên
            if ($name === '') {
                $errors['name'] = 'Vui lòng nhập họ và tên của bạn.';
            } elseif (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
                $errors['name'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
            }

            // Validate Email
            if ($email === '') {
                $errors['email'] = 'Vui lòng nhập địa chỉ email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Định dạng email không hợp lệ.';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Email này đã được sử dụng. Vui lòng chọn email khác.';
            }

            // Validate Số điện thoại (nếu có nhập)
            if ($phone !== '' && !preg_match('/^[0-9+\-\s]{9,15}$/', $phone)) {
                $errors['phone'] = 'Số điện thoại không hợp lệ (9-15 chữ số).';
            }

            // Validate Mật khẩu (>= 6 ký tự)
            if ($password === '') {
                $errors['password'] = 'Vui lòng nhập mật khẩu.';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            }

            // Validate Nhập lại mật khẩu
            if ($passwordConfirm === '') {
                $errors['password_confirm'] = 'Vui lòng xác nhận lại mật khẩu.';
            } elseif ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Mật khẩu nhập lại không khớp.';
            }

            if (empty($errors)) {
                $newUserId = $this->userModel->create([
                    'name'     => $name,
                    'email'    => $email,
                    'phone'    => $phone,
                    'password' => $password,
                // Đăng ký luôn gán role là user, không tự tạo được admin.
                    'role'     => 'user',
                    'status'   => 'active',
                ]);

                $newUser = $this->userModel->findById($newUserId);
                if ($newUser) {
                    Session::regenerate();
                    Session::set('user', [
                        'id'     => (int) $newUser['id'],
                        'name'   => $newUser['name'],
                        'email'  => $newUser['email'],
                        'phone'  => $newUser['phone'],
                        'avatar' => $newUser['avatar'],
                        'role'   => $newUser['role'],
                        'status' => $newUser['status'],
                    ]);
                }

                Session::flash('alert', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với cộng đồng StudySpot.', 'success');
                $this->redirect('');
            }
        }

        $this->render('auth/register', [
            'pageTitle' => 'Đăng ký tài khoản',
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    // Đăng xuất khỏi hệ thống (/auth/logout)
    public function logout(): void
    {
        Session::destroy();
        Session::start();
        Session::flash('alert', 'Bạn đã đăng xuất thành công.', 'info');
        $this->redirect('auth/login');
    }
}
