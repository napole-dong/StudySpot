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
 * Lấy đường dẫn hiển thị ảnh an toàn (Fallback về ảnh quán thực tế đa dạng hoặc SVG cục bộ)
 *
 * @param string|null     $path Đường dẫn tương đối lưu trong CSDL (VD: 'uploads/places/cafe1.jpg')
 * @param string          $type Loại ảnh: 'place' | 'review' | 'avatar'
 * @param int|string|null $seed Mã ID hoặc chuỗi phân biệt để chọn ảnh quán tương thích, tránh trùng lặp
 * @return string URL đầy đủ tới file ảnh
 */
function get_image_url(?string $path, string $type = 'place', int|string|null $seed = null): string
{
    $defaultSvg = ($type === 'avatar')
        ? 'assets/img/avatar-default.svg'
        : 'assets/img/placeholder.svg';

    if (!empty($path)) {
        // Chuẩn hóa đường dẫn tương đối trong thư mục assets/
        $cleanPath = ltrim($path, '/');
        if (!str_starts_with($cleanPath, 'assets/')) {
            $cleanPath = 'assets/' . $cleanPath;
        }

        $fullDiskPath = ROOT_PATH . '/' . $cleanPath;
        if (is_file($fullDiskPath) && file_exists($fullDiskPath)) {
            return base_url($cleanPath);
        }
    }

    if ($type === 'avatar') {
        return base_url('assets/img/avatar-default.svg');
    }

    // Nếu là ảnh quán (place): Luân phiên giữa 8 ảnh không gian quán thực tế độ nét cao, không bao giờ trùng nhau
    $index = 1;
    if ($seed !== null && $seed !== '') {
        $index = (abs(is_numeric($seed) ? (int) $seed : crc32((string) $seed)) % 8) + 1;
    } elseif (!empty($path)) {
        $index = (abs(crc32($path)) % 8) + 1;
    } else {
        static $placeImgCounter = 0;
        $placeImgCounter++;
        $index = ($placeImgCounter % 8) + 1;
    }

    $photoPath = 'assets/img/places/cafe_' . $index . '.jpg';
    if (file_exists(ROOT_PATH . '/' . $photoPath)) {
        return base_url($photoPath);
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
 * Định dạng mức giá (1 -> 4) thành chuỗi dễ hiểu cho sinh viên (hỗ trợ dạng ngắn gọn cho thẻ card)
 */
function format_price_range(int $priceRange, bool $short = false): string
{
    if ($short) {
        return match ($priceRange) {
            1 => 'Dưới 25.000₫',
            2 => '25.000₫ - 45.000₫',
            3 => '45.000₫ - 65.000₫',
            4 => 'Trên 65.000₫',
            default => 'Giá sinh viên',
        };
    }

    return match ($priceRange) {
        1 => 'Dưới 25.000₫ (Bình dân sinh viên)',
        2 => '25.000₫ - 45.000₫ (Giá sinh viên AGU)',
        3 => '45.000₫ - 65.000₫ (Tầm trung)',
        4 => 'Trên 65.000₫ (Không gian lớn)',
        default => 'Đang cập nhật',
    };
}

/**
 * Định dạng giá tự do (VNĐ) từ price_min/price_max: "25.000₫ - 45.000₫", "Dưới 25.000₫", "Từ 65.000₫", "Đang cập nhật"
 */
function format_price_range_from_min_max($min, $max): string
{
    $min = ($min === '' || $min === null) ? null : (int) $min;
    $max = ($max === '' || $max === null) ? null : (int) $max;
    $fmt = fn($v) => number_format($v, 0, ',', '.') . '₫';
    if ($min !== null && $max !== null) {
        if ($min === $max) {
            return $fmt($min);
        }
        return $fmt($min) . ' - ' . $fmt($max);
    }
    if ($max !== null) {
        return 'Dưới ' . $fmt($max);
    }
    if ($min !== null) {
        return 'Từ ' . $fmt($min);
    }
    return 'Đang cập nhật';
}

/**
 * Định dạng nhãn độ ồn theo bảng màu Warm Study Cafe
 */
function format_noise_level(string $noiseLevel): array
{
    return match ($noiseLevel) {
        'yen_tinh' => ['label' => 'Yên tĩnh', 'class' => 'pill-amenity pill-quiet', 'icon' => 'bi-volume-mute-fill'],
        'vua'      => ['label' => 'Độ ồn vừa', 'class' => 'pill-amenity pill-moderate', 'icon' => 'bi-volume-down-fill'],
        'on'       => ['label' => 'Khá nhộn nhịp', 'class' => 'pill-amenity pill-lively', 'icon' => 'bi-volume-up-fill'],
        default    => ['label' => 'Không rõ', 'class' => 'pill-amenity', 'icon' => 'bi-question-circle'],
    };
}

/**
 * Kiểm tra quán hiện tại đang Mở cửa hay Đã đóng cửa dựa trên open_time và close_time
 * Xử lý chính xác cả trường hợp mở trong ngày và mở xuyên đêm (close_time < open_time)
 */
function is_place_open(?string $openTime, ?string $closeTime): bool
{
    if (empty($openTime) || empty($closeTime)) {
        return false;
    }

    $now = date('H:i:s');
    // Trường hợp mở cửa trong ngày (VD: 06:00:00 -> 22:30:00)
    if ($openTime <= $closeTime) {
        return ($now >= $openTime && $now <= $closeTime);
    }

    // Trường hợp mở xuyên đêm qua 00:00 (VD: 18:00:00 -> 02:00:00 sáng hôm sau)
    return ($now >= $openTime || $now <= $closeTime);
}

/**
 * Kiểm tra quán có đạt tiêu chí tag tự động "Phù hợp học khuya" không
 * Điều kiện: Mở từ 22:00 trở đi (hoặc xuyên đêm) + có Wifi + có Ổ cắm
 */
function is_late_night_friendly(array $place): bool
{
    $slugs = $place['amenity_slugs'] ?? array_map(fn($a) => $a['slug'] ?? '', $place['amenities'] ?? []);
    $hasWifi = in_array('wifi', $slugs, true) || !empty($place['has_wifi']);
    $hasOutlet = in_array('outlet', $slugs, true) || !empty($place['has_outlet']);
    if (!$hasWifi || !$hasOutlet) {
        return false;
    }

    $openTime = $place['open_time'] ?? '07:00:00';
    $closeTime = $place['close_time'] ?? '22:00:00';

    return ($closeTime >= '22:00:00' || $closeTime < $openTime);
}

/**
 * Kiểm tra quán có đạt tiêu chí tag tự động "Giá sinh viên" không (price_range <= 2)
 */
function is_student_price(array $place): bool
{
    return isset($place['price_range']) && (int) $place['price_range'] >= 1 && (int) $place['price_range'] <= 2;
}

/**
 * Tạo URL nhúng bản đồ Google Maps thật theo tên + địa chỉ quán tại TP. Long Xuyên, An Giang
 */
function get_google_map_embed_url(array $place): string
{
    $query = trim(($place['name'] ?? '') . ' ' . ($place['address'] ?? 'TP. Long Xuyên, An Giang'));
    return 'https://maps.google.com/maps?q=' . urlencode($query) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
}

/**
 * Hiển thị chuỗi icon ngôi sao (Bootstrap Icons) từ điểm số (0 - 5)
 */
function render_stars(float $rating, bool $showNumber = true): string
{
    $rating = max(0, min(5, $rating));
    $html = '<span class="star-display d-inline-flex align-items-center gap-1">';

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

/**
 * Rút gọn chuỗi văn bản nếu vượt quá độ dài tối đa (giữ nguyên độ dài ký tự an toàn tiếng Việt UTF-8)
 */
function truncate_text(?string $text, int $limit = 100, string $ellipsis = '...'): string
{
    $text = trim((string) $text);
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit, 'UTF-8') . $ellipsis;
}

/**
 * Trả về tối đa 3 tiện ích quan trọng nhất để hiển thị gọn gàng trên thẻ quán (place card),
 * tránh tràn dòng và loại bỏ sự rườm rà.
 *
 * @param array $place Dữ liệu địa điểm từ CSDL
 * @return array Mảng tối đa 3 tiện ích [['icon' => string, 'label' => string, 'class' => string]]
 */
function get_card_amenities(array $place): array
{
    // Tiện ích lấy từ bảng amenities (đã gắn sẵn bởi Place model, hoặc truy vấn trực tiếp nếu chưa có)
    $list = $place['amenities'] ?? null;
    if (!is_array($list) && !empty($place['id'])) {
        $list = (new Amenity())->getForPlace((int) $place['id']);
    }
    if (!is_array($list)) {
        return [];
    }
    $amenities = [];
    foreach ($list as $am) {
        $amenities[] = [
            'icon'  => '',
            'label' => $am['name'] ?? ($am['label'] ?? ''),
            'class' => 'pill-amenity',
        ];
    }
    return array_slice($amenities, 0, 3);
}
