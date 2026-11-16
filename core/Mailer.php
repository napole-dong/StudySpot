<?php
// Mailer: gởi email qua Gmail SMTP viết tay (STARTTLS + AUTH LOGIN), dùng chung cho mọi controller.
// Tài khoản gởi nằm trong config/mail.php (MAIL_USERNAME, MAIL_PASSWORD), nạp qua config/config.php.
class Mailer
{
    // Đã cấu hình gởi email chưa (đủ MAIL_USERNAME và MAIL_PASSWORD).
    public static function configured(): bool
    {
        return defined('MAIL_USERNAME') && defined('MAIL_PASSWORD')
            && MAIL_USERNAME !== '' && MAIL_PASSWORD !== '';
    }

    // Gởi 1 email 2 phần (chữ + HTML) tới 1 người nhận, thành công trả true.
    public static function send(string $toEmail, string $toName, string $subject, string $text, string $html): bool
    {
        if (!self::configured()) {
            return false;
        }

        // Ghép thư 2 phần (chữ + HTML) theo chuẩn MIME multipart/alternative.
        $boundary = '=_StudySpot_' . bin2hex(random_bytes(8));
        $headers = 'From: ' . self::mimeEncode(MAIL_FROM_NAME) . ' <' . trim(MAIL_USERNAME) . ">\r\n"
            . 'To: ' . self::mimeEncode($toName) . ' <' . $toEmail . ">\r\n"
            . 'Subject: ' . self::mimeEncode($subject) . "\r\n"
            . "MIME-Version: 1.0\r\n"
            . 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $body = "--{$boundary}\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($text)) . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($html)) . "\r\n"
            . "--{$boundary}--";

        return self::smtpSend(trim(MAIL_USERNAME), $toEmail, $headers . "\r\n\r\n" . $body);
    }

    // Gởi email thông báo mật khẩu vừa thay đổi (sau khi đặt lại qua email hoặc tự đổi trong hồ sơ).
    public static function sendPasswordChangedNotice(string $toEmail, string $toName, string $via): bool
    {
        $subject = 'Mật khẩu của bạn đã được thay đổi';
        $time = date('H:i') . ' ngày ' . date('d/m/Y');
        $forgotUrl = BASE_URL . '/auth/forgot-password';

        // Bản chữ thuần cho trình đọc email không hiện HTML.
        $text = "Xin chào {$toName},\r\n\r\n"
            . 'Mật khẩu tài khoản ' . SITE_NAME . " của bạn vừa được thay đổi thành công {$via} lúc {$time}.\r\n\r\n"
            . "Nếu bạn không thực hiện việc này, tài khoản của bạn có thể đã bị người lạ xâm nhập. Hãy đặt lại mật khẩu ngay tại:\r\n{$forgotUrl}\r\n"
            . 'và liên hệ quản trị viên để được hỗ trợ.';

        // Bản HTML: cùng nội dung, link đặt lại mật khẩu đặt trong nút bấm.
        $html = '<p>Xin chào <strong>' . htmlspecialchars($toName, ENT_QUOTES, 'UTF-8') . ',</strong></p>'
            . '<p>Mật khẩu tài khoản ' . SITE_NAME . ' của bạn vừa được thay đổi thành công ' . htmlspecialchars($via, ENT_QUOTES, 'UTF-8') . ' lúc ' . $time . '.</p>'
            . '<p>Nếu bạn không thực hiện việc này, tài khoản của bạn có thể đã bị người lạ xâm nhập. Hãy đặt lại mật khẩu ngay và liên hệ quản trị viên để được hỗ trợ.</p>'
            . '<p><a href="' . $forgotUrl . '" style="display:inline-block;padding:10px 22px;background:#111827;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:bold;">Đặt lại mật khẩu</a></p>';

        return self::send($toEmail, $toName, $subject, $text, $html);
    }

    // Gởi email chào mừng khi đăng ký tài khoản mới thành công.
    public static function sendWelcomeEmail(string $toEmail, string $toName): bool
    {
        $subject = 'Chào mừng bạn đến với ' . SITE_NAME;
        $homeUrl = BASE_URL;

        // Bản chữ thuần cho trình đọc email không hiện HTML.
        $text = "Xin chào {$toName},\r\n\r\n"
            . 'Chúc mừng bạn đã đăng ký tài khoản ' . SITE_NAME . " thành công với email {$toEmail}.\r\n"
            . "Từ giờ bạn có thể tìm quán cà phê học bài tại Long Xuyên, lưu quán yêu thích và viết đánh giá chia sẻ cùng cộng đồng sinh viên AGU.\r\n\r\n"
            . "Khám phá ngay tại:\r\n{$homeUrl}\r\n\r\n"
            . 'Nếu bạn không thực hiện đăng ký này, xin vui lòng bỏ qua email và báo lại cho quản trị hệ thống.';

        // Bản HTML: cùng nội dung, link trang chủ đặt trong nút bấm.
        $html = '<p>Xin chào <strong>' . htmlspecialchars($toName, ENT_QUOTES, 'UTF-8') . ',</strong></p>'
            . '<p>Chúc mừng bạn đã đăng ký tài khoản ' . SITE_NAME . ' thành công với email ' . htmlspecialchars($toEmail, ENT_QUOTES, 'UTF-8') . '.</p>'
            . '<p>Từ giờ bạn có thể tìm quán cà phê học bài tại Long Xuyên, lưu quán yêu thích và viết đánh giá chia sẻ cùng cộng đồng sinh viên AGU.</p>'
            . '<p><a href="' . $homeUrl . '" style="display:inline-block;padding:10px 22px;background:#111827;color:#ffffff;text-decoration:none;border-radius:999px;font-weight:bold;">Khám phá ' . SITE_NAME . '</a></p>'
            . '<p>Nếu bạn không thực hiện đăng ký này, xin vui lòng bỏ qua email và báo lại cho quản trị hệ thống.</p>';

        return self::send($toEmail, $toName, $subject, $text, $html);
    }

    // Mật khẩu ứng dụng Gmail đã bỏ mọi dấu cách (Google hiện mật khẩu theo nhóm 4 ký tự, copy vào hay dính dấu cách làm sai mật khẩu).
    private static function appPassword(): string
    {
        return str_replace(' ', '', MAIL_PASSWORD);
    }

    // Mã hóa tiêu đề email có dấu tiếng Việt theo chuẩn RFC 2047 (dạng =?UTF-8?B?...?=).
    private static function mimeEncode(string $text): string
    {
        return '=?UTF-8?B?' . base64_encode($text) . '?=';
    }

    // Nói chuyện SMTP với Gmail (smtp.gmail.com:587, STARTTLS + AUTH LOGIN), viết tay không dùng thư viện.
    private static function smtpSend(string $from, string $to, string $message): bool
    {
        $socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 15);
        if ($socket === false) {
            return false;
        }
        stream_set_timeout($socket, 15);

        try {
            if (!self::smtpExpect($socket, [220])) return false;

            fwrite($socket, "EHLO localhost\r\n");
            if (!self::smtpExpect($socket, [250])) return false;

            fwrite($socket, "STARTTLS\r\n");
            if (!self::smtpExpect($socket, [220])) return false;
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return false;

            fwrite($socket, "EHLO localhost\r\n");
            if (!self::smtpExpect($socket, [250])) return false;

            fwrite($socket, "AUTH LOGIN\r\n");
            if (!self::smtpExpect($socket, [334])) return false;
            fwrite($socket, base64_encode(MAIL_USERNAME) . "\r\n");
            if (!self::smtpExpect($socket, [334])) return false;
            fwrite($socket, base64_encode(self::appPassword()) . "\r\n");
            if (!self::smtpExpect($socket, [235])) return false;

            fwrite($socket, "MAIL FROM:<{$from}>\r\n");
            if (!self::smtpExpect($socket, [250])) return false;
            fwrite($socket, "RCPT TO:<{$to}>\r\n");
            if (!self::smtpExpect($socket, [250, 251])) return false;

            fwrite($socket, "DATA\r\n");
            if (!self::smtpExpect($socket, [354])) return false;
            fwrite($socket, $message . "\r\n.\r\n");
            if (!self::smtpExpect($socket, [250])) return false;

            fwrite($socket, "QUIT\r\n");
            return true;
        } finally {
            fclose($socket);
        }
    }

    // Đọc phản hồi SMTP (có thể nhiều dòng), đúng mã mong đợi thì trả true.
    private static function smtpExpect($socket, array $codes): bool
    {
        $response = '';
        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;
            // Dòng cuối của phản hồi có dấu cách ở ký tự thứ 4, các dòng trước là dấu gạch ngang.
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return in_array((int) substr($response, 0, 3), $codes, true);
    }
}