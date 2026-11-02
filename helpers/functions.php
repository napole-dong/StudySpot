<?php
/**
 * Các hàm tiện ích dùng chung toàn hệ thống (Helpers)
 */

/**
 * Làm sạch chuỗi trước khi xuất ra HTML (chống XSS)
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Tạo URL đầy đủ từ đường dẫn tương đối dựa trên hằng số BASE_URL
 */
function base_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * In ra thẻ input ẩn chứa CSRF Token cho mọi form POST
 */
function csrf_field(): string
{
    $token = Session::csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Lấy đường dẫn hiển thị ảnh an toàn (Fallback về ảnh SVG cục bộ nếu file không tồn tại trên ổ đĩa)
 *
 * @param string|null $path Đường dẫn tương đối lưu trong CSDL (VD: 'uploads/places/cafe1.jpg')
 * @param string      $type Loại ảnh: 'place' | 'review' | 'avatar'
 * @return string URL đầy đủ tới file ảnh hoặc ảnh placeholder cục bộ
 */
function get_image_url(?string $path, string $type = 'place'): string
{
    $defaultSvg = ($type === 'avatar')
        ? 'assets/img/avatar-default.svg'
        : 'assets/img/placeholder.svg';

    if (empty($path)) {
        return base_url($defaultSvg);
    }

    // Chuẩn hóa đường dẫn tương đối trong thư mục assets/
    $cleanPath = ltrim($path, '/');
    if (!str_starts_with($cleanPath, 'assets/')) {
        $cleanPath = 'assets/' . $cleanPath;
    }

    $fullDiskPath = ROOT_PATH . '/' . $cleanPath;
    if (is_file($fullDiskPath) && file_exists($fullDiskPath)) {
        return base_url($cleanPath);
    }

    return base_url($defaultSvg);
}

/**
 * Upload 1 file ảnh an toàn (kiểm tra dung lượng <= 2MB, kiểm tra MIME thật bằng finfo, đổi tên ngẫu nhiên)
 *
 * @param array  $file   Mảng phần tử từ $_FILES['ten_input']
 * @param string $folder Thư mục con trong assets/uploads/ ('places', 'avatars', 'reviews')
 * @return array ['success' => bool, 'path' => ?string, 'error' => ?string]
 */
function upload_image(array $file, string $folder = 'places'): array
{
    if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'path' => null, 'error' => 'Không có file nào được chọn.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'Lỗi trong quá trình tải file lên máy chủ.'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'path' => null, 'error' => 'Kích thước ảnh vượt quá giới hạn 2MB.'];
    }

    // Kiểm tra MIME type thực tế bằng finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!isset(ALLOWED_MIME_TYPES[$mimeType])) {
        return ['success' => false, 'path' => null, 'error' => 'Chỉ chấp nhận định dạng ảnh JPG, PNG hoặc WEBP.'];
    }

    $extension = ALLOWED_MIME_TYPES[$mimeType];
    $targetDir = UPLOAD_PATH . '/' . trim($folder, '/');

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Đặt tên file ngẫu nhiên để chống ghi đè và lộ tên file gốc
    $fileName = uniqid($folder . '_', true) . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = $targetDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'path' => null, 'error' => 'Không thể lưu file ảnh vào thư mục đích.'];
    }

    return [
        'success' => true,
        'path'    => 'uploads/' . trim($folder, '/') . '/' . $fileName,
        'error'   => null,
    ];
}

/**
 * Xóa file ảnh cũ trên ổ đĩa (nếu tồn tại và nằm trong thư mục uploads)
 */
function delete_uploaded_file(?string $path): void
{
    if (empty($path)) {
        return;
    }
    $cleanPath = ltrim($path, '/');
    if (!str_starts_with($cleanPath, 'assets/')) {
        $cleanPath = 'assets/' . $cleanPath;
    }
    $fullDiskPath = ROOT_PATH . '/' . $cleanPath;
    if (str_contains($cleanPath, 'assets/uploads/') && is_file($fullDiskPath)) {
        @unlink($fullDiskPath);
    }
}

/**
 * Chuyển chuỗi tiếng Việt có dấu thành slug chuẩn SEO
 */
function create_slug(string $str): string
{
    $str = mb_strtolower(trim($str), 'UTF-8');
    $vietnameseMap = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'đ'=>'d',
    ];
    $str = strtr($str, $vietnameseMap);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Định dạng mức giá (1 -> 4) thành chuỗi dễ hiểu cho sinh viên
 */
function format_price_range(int $priceRange): string
{
    return match ($priceRange) {
        1 => 'Dưới 35.000đ ($)',
        2 => '35.000đ - 50.000đ ($$)',
        3 => '50.000đ - 80.000đ ($$$)',
        4 => 'Trên 80.000đ ($$$$)',
        default => 'Đang cập nhật',
    };
}

/**
 * Định dạng nhãn độ ồn
 */
function format_noise_level(string $noiseLevel): array
{
    return match ($noiseLevel) {
        'yen_tinh' => ['label' => 'Yên tĩnh', 'class' => 'bg-success-subtle text-success border-success-subtle', 'icon' => 'bi-volume-mute-fill'],
        'vua'      => ['label' => 'Độ ồn vừa', 'class' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle', 'icon' => 'bi-volume-down-fill'],
        'on'       => ['label' => 'Khá nhộn nhịp', 'class' => 'bg-danger-subtle text-danger border-danger-subtle', 'icon' => 'bi-volume-up-fill'],
        default    => ['label' => 'Không rõ', 'class' => 'bg-secondary-subtle text-secondary', 'icon' => 'bi-question-circle'],
    };
}

/**
 * Kiểm tra quán hiện tại đang Mở cửa hay Đã đóng cửa dựa trên open_time và close_time
 */
function is_place_open(?string $openTime, ?string $closeTime): bool
{
    if (empty($openTime) || empty($closeTime)) {
        return false;
    }

    $now = date('H:i:s');
    // Trường hợp mở cửa trong ngày (VD: 07:00:00 -> 22:30:00)
    if ($openTime <= $closeTime) {
        return ($now >= $openTime && $now <= $closeTime);
    }

    // Trường hợp mở xuyên đêm (VD: 08:00:00 -> 02:00:00 sáng hôm sau)
    return ($now >= $openTime || $now <= $closeTime);
}

/**
 * Kiểm tra quán có đạt tiêu chí tag tự động "Phù hợp chạy deadline" không
 * Điều kiện: Mở sau 22:00 (hoặc xuyên đêm qua 00:00) + có Wifi + có Ổ cắm
 */
function is_deadline_friendly(array $place): bool
{
    $hasWifi = !empty($place['has_wifi']);
    $hasOutlet = !empty($place['has_outlet']);
    if (!$hasWifi || !$hasOutlet) {
        return false;
    }

    $openTime = $place['open_time'] ?? '07:00:00';
    $closeTime = $place['close_time'] ?? '22:00:00';

    // Nếu giờ đóng cửa > 22:00:00 hoặc đóng cửa qua nửa đêm (close_time < open_time)
    return ($closeTime > '22:00:00' || $closeTime < $openTime);
}

/**
 * Kiểm tra quán có đạt tiêu chí tag tự động "Giá sinh viên" không (price_range <= 2)
 */
function is_student_price(array $place): bool
{
    return isset($place['price_range']) && (int) $place['price_range'] <= 2;
}

/**
 * Hiển thị chuỗi icon ngôi sao (Bootstrap Icons) từ điểm số (0 - 5)
 */
function render_stars(float $rating, bool $showNumber = true): string
{
    $rating = max(0, min(5, $rating));
    $html = '<span class="text-warning d-inline-flex align-items-center gap-1">';

    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $html .= '<i class="bi bi-star-fill"></i>';
        } elseif ($rating >= $i - 0.5) {
            $html .= '<i class="bi bi-star-half"></i>';
        } else {
            $html .= '<i class="bi bi-star"></i>';
        }
    }

    if ($showNumber) {
        $html .= '<strong class="text-dark ms-1 small">' . number_format($rating, 1) . '</strong>';
    }

    $html .= '</span>';
    return $html;
}

/**
 * Tạo thanh phân trang chuẩn Bootstrap 5 (giữ nguyên các query string lọc hiện tại)
 */
function render_pagination(int $currentPage, int $totalPages, string $basePath = ''): string
{
    if ($totalPages <= 1) {
        return '';
    }

    $queryParams = $_GET;
    unset($queryParams['url']);

    $buildPageUrl = function (int $page) use ($basePath, $queryParams): string {
        $queryParams['page'] = $page;
        $queryString = http_build_query($queryParams);
        $target = $basePath !== '' ? base_url($basePath) : strtok($_SERVER['REQUEST_URI'], '?');
        return $target . ($queryString ? '?' . $queryString : '');
    };

    $html = '<nav aria-label="Phân trang"><ul class="pagination justify-content-center mt-4">';

    // Nút Trang trước
    $prevDisabled = ($currentPage <= 1) ? 'disabled' : '';
    $prevUrl = ($currentPage > 1) ? e($buildPageUrl($currentPage - 1)) : '#';
    $html .= '<li class="page-item ' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '"><i class="bi bi-chevron-left"></i></a></li>';

    // Các trang ở giữa
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($buildPageUrl(1)) . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i === $currentPage) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . e($buildPageUrl($i)) . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . e($buildPageUrl($totalPages)) . '">' . $totalPages . '</a></li>';
    }

    // Nút Trang sau
    $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextUrl = ($currentPage < $totalPages) ? e($buildPageUrl($currentPage + 1)) : '#';
    $html .= '<li class="page-item ' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '"><i class="bi bi-chevron-right"></i></a></li>';

    $html .= '</ul></nav>';
    return $html;
}
