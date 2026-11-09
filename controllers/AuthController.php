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
                    Session::flash('alert', 'Chào mừng ' . $user['name'] . ' đã quay trở lại StudySpot!', 'success');

                    // Đăng nhập xong: lưu session rồi chuyển theo role.
                    $this->startUserSession($user);
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle'          => 'Đăng nhập',
            'errors'             => $errors,
            'oldEmail'           => $oldEmail,
            // Nút Google chỉ hiện khi đã cấu hình Client ID và Secret.
            'googleLoginEnabled' => $this->googleConfigured(),
        ]);
    }

    // Chuyển tới Google để đăng nhập (/auth/google)
    public function google(): void
    {
        // Chưa cấu hình thì báo lỗi, không chuyển đi.
        if (!$this->googleConfigured()) {
            Session::flash('alert', 'Đăng nhập Google chưa được cấu hình. Vui lòng liên hệ Quản trị viên.', 'warning');
            $this->redirect('auth/login');
        }

        // Đã đăng nhập thì về trang chủ.
        if (Session::isLoggedIn()) {
            $this->redirect('');
        }

        // state: mã ngẫu nhiên chống CSRF, Google trả lại y nguyên ở callback.
        $state = bin2hex(random_bytes(16));
        Session::set('google_oauth_state', $state);

        // Link đăng nhập Google: hd giới hạn chỉ tài khoản @agu.edu.vn được chọn.
        $params = [
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
            'hd'            => GOOGLE_ALLOWED_DOMAIN,
            'state'         => $state,
        ];
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
        exit;
    }

    // Google trả về sau khi đăng nhập (/auth/google-callback)
    public function googleCallback(): void
    {
        // Người dùng bấm hủy ở Google thì Google trả error.
        if (!empty($_GET['error'])) {
            Session::flash('alert', 'Bạn đã hủy đăng nhập Google.', 'info');
            $this->redirect('auth/login');
        }

        // Kiểm tra state chống CSRF: phải khớp mã đã lưu lúc chuyển đi.
        $state = $_GET['state'] ?? '';
        $savedState = Session::get('google_oauth_state');
        Session::remove('google_oauth_state');
        if (!$savedState || !hash_equals((string) $savedState, (string) $state)) {
            Session::flash('alert', 'Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.', 'danger');
            $this->redirect('auth/login');
        }

        // Đổi code lấy token, không có code thì lỗi.
        $code = $_GET['code'] ?? '';
        if ($code === '') {
            Session::flash('alert', 'Google không trả mã đăng nhập. Vui lòng thử lại.', 'danger');
            $this->redirect('auth/login');
        }
        $tokens = $this->googleExchangeCode($code);
        if (empty($tokens['access_token'])) {
            Session::flash('alert', 'Không đổi được mã đăng nhập Google. Vui lòng thử lại.', 'danger');
            $this->redirect('auth/login');
        }

        // Lấy hồ sơ Google từ access token.
        $profile = $this->googleFetchProfile($tokens['access_token']);
        if (empty($profile['email'])) {
            Session::flash('alert', 'Không lấy được email Google. Vui lòng thử lại.', 'danger');
            $this->redirect('auth/login');
        }

        // Chỉ chấp nhận email AGU đã xác minh (tên miền agu.edu.vn và cả tên miền con như student.agu.edu.vn).
        $email = strtolower(trim($profile['email']));
        $atPos = strrpos($email, '@');
        $emailDomain = $atPos === false ? '' : substr($email, $atPos + 1);
        $domainOk = $this->googleDomainAllowed($emailDomain);
        $hdOk = empty($profile['hd']) || $this->googleDomainAllowed(strtolower(trim($profile['hd'])));
        $verified = ($profile['verified_email'] ?? false) === true;
        if (!$domainOk || !$hdOk || !$verified) {
            Session::flash('alert', 'Chỉ tài khoản email @' . GOOGLE_ALLOWED_DOMAIN . ' mới được đăng nhập bằng Google.', 'danger');
            $this->redirect('auth/login');
        }

        // Tìm hoặc tạo tài khoản rồi đăng nhập.
        $this->loginGoogleUser($email, trim($profile['name'] ?? '') !== '' ? trim($profile['name']) : $email);
    }

    // Kiểm tra đã cấu hình Google chưa (đủ Client ID và Secret).
    private function googleConfigured(): bool
    {
        return defined('GOOGLE_CLIENT_ID') && defined('GOOGLE_CLIENT_SECRET')
            && GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
    }

    // Kiểm tra tên miền Google: bằng agu.edu.vn hoặc là tên miền con của nó (VD student.agu.edu.vn).
    private function googleDomainAllowed(string $domain): bool
    {
        return $domain === GOOGLE_ALLOWED_DOMAIN
            || str_ends_with($domain, '.' . GOOGLE_ALLOWED_DOMAIN);
    }

    // Đăng nhập xong: đổi Session ID, lưu user vào session, chuyển theo role.
    private function startUserSession(array $user): void
    {
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

        // Sau đăng nhập: admin về admin/dashboard, thành viên về trang chủ.
        if ($user['role'] === 'admin') {
            $this->redirect('admin/dashboard');
        }
        $this->redirect('');
    }

    // Tìm tài khoản theo email Google, chưa có thì tạo mới rồi đăng nhập.
    private function loginGoogleUser(string $email, string $name): void
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // Tài khoản Google mới: tạo role user, mật khẩu ngẫu nhiên không ai đoán được (chỉ đăng nhập bằng Google).
            $newUserId = $this->userModel->create([
                'name'     => $name,
                'email'    => $email,
                'phone'    => '',
                'password' => bin2hex(random_bytes(32)),
                'role'     => 'user',
                'status'   => 'active',
            ]);
            $user = $this->userModel->findById($newUserId);
        }

        if (!$user) {
            Session::flash('alert', 'Không tạo được tài khoản Google. Vui lòng thử lại.', 'danger');
            $this->redirect('auth/login');
        }

        // Tài khoản bị khóa thì không cho vào dù đăng nhập Google đúng.
        if ($user['status'] === 'locked') {
            Session::flash('alert', 'Tài khoản của bạn hiện đang bị khóa. Vui lòng liên hệ Quản trị viên.', 'danger');
            $this->redirect('auth/login');
        }

        Session::flash('alert', 'Chào mừng ' . $user['name'] . ' đã đăng nhập bằng Google!', 'success');
        $this->startUserSession($user);
    }

    // Đổi code lấy access token từ Google (POST oauth2.googleapis.com/token).
    private function googleExchangeCode(string $code): ?array
    {
        $body = http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]);
        $raw = $this->googleHttpPost('https://oauth2.googleapis.com/token', $body);
        if ($raw === null) {
            return null;
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    // Lấy hồ sơ Google (GET userinfo với access token).
    private function googleFetchProfile(string $accessToken): ?array
    {
        $raw = $this->googleHttpGet('https://www.googleapis.com/oauth2/v3/userinfo', $accessToken);
        if ($raw === null) {
            return null;
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    // POST tới Google, trả chuỗi JSON hoặc null khi lỗi mạng.
    private function googleHttpPost(string $url, string $body): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
                'content'       => $body,
                'timeout'       => 15,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        return $raw === false ? null : $raw;
    }

    // GET tới Google kèm Bearer token, trả chuỗi JSON hoặc null khi lỗi mạng.
    private function googleHttpGet(string $url, string $accessToken): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'header'        => "Authorization: Bearer {$accessToken}\r\nAccept: application/json\r\n",
                'timeout'       => 15,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        return $raw === false ? null : $raw;
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
