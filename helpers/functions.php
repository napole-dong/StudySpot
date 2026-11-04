<?php
// Helper dùng chung: chống XSS, URL, CSRF, ảnh, slug, giá, giờ mở cửa, sao, phân trang.

// e(): chống XSS bằng htmlspecialchars, đổi cả nháy đơn và kép, giữ tiếng Việt UTF-8.
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// base_url(): ghép BASE_URL với đường dẫn, cắt dấu / thừa.
function base_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// csrf_field(): in input ẩn csrf_token cho mọi form POST.
function csrf_field(): string
{
    $token = Session::csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

// get_image_url(): ưu tiên ảnh thật, rồi avatar mặc định, ảnh quán mẫu cafe_1..8, cuối cùng SVG giữ chỗ.
function get_image_url(?string $path, string $type = 'place', int|string|null $seed = null): string
{
    // Ảnh giữ chỗ cuối cùng: avatar dùng hình đại diện mặc định, các loại còn lại dùng hình giữ chỗ chung; chỉ tới đây khi mọi nguồn ảnh khác đều thất bại.
    $defaultSvg = ($type === 'avatar')
        ? 'assets/img/avatar-default.svg'
        : 'assets/img/placeholder.svg';

    // Nhánh 1: nếu CSDL có lưu đường dẫn ảnh thì thử dùng ảnh thật trước.
    if (!empty($path)) {
        // Chuẩn hóa đường dẫn tương đối trong thư mục assets/: cắt dấu / ở đầu,
        $cleanPath = ltrim($path, '/');
        if (!str_starts_with($cleanPath, 'assets/')) {
            $cleanPath = 'assets/' . $cleanPath;
        }

        // Chỉ dùng ảnh thật khi file thật sự tồn tại trên ổ đĩa (kiểm tra cả
        $fullDiskPath = ROOT_PATH . '/' . $cleanPath;
        if (is_file($fullDiskPath) && file_exists($fullDiskPath)) {
            return base_url($cleanPath);
        }
    }

    // Nhánh 2: avatar không có ảnh thật thì trả ngay ảnh đại diện mặc định, không đi vào logic chọn ảnh quán mẫu bên dưới (vì avatar không phải ảnh quán).
    if ($type === 'avatar') {
        return base_url('assets/img/avatar-default.svg');
    }

    // Nhánh 3, chỉ còn ảnh quán/review không có ảnh thật: luân phiên giữa 8 ảnh
    $index = 1;
    // Cách tính $index: nếu có seed thì dùng seed (seed là số thì lấy trực tiếp,
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

// upload_image(): lưu ảnh vào assets/uploads/, kiểm tra lỗi, 2MB, MIME thật bằng finfo, đổi tên ngẫu nhiên.
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

    // Chỉ chấp nhận ảnh JPG/PNG/WEBP, kiểm tra MIME thật bằng finfo, không tin đuôi file.
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

    // Đặt tên file ngẫu nhiên để chống ghi đè và lộ tên file gốc: uniqid() tạo
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

// delete_uploaded_file(): chỉ xóa file trong assets/uploads/ nếu file thật sự tồn tại.
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

// create_slug(): đổi tiếng Việt có dấu thành slug không dấu, nối bằng '-'.
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

// format_price_range(): đổi mức giá 1..4 thành chữ, $short=true thì dạng ngắn cho thẻ quán.
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

// format_price_range_from_min_max(): đổi price_min/price_max thành 'min - max', 'Dưới max', 'Từ min' hoặc 'Đang cập nhật'.
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

// format_noise_level(): đổi mã yen_tinh/vua/on thành nhãn, class CSS và icon.
function format_noise_level(string $noiseLevel): array
{
    return match ($noiseLevel) {
        'yen_tinh' => ['label' => 'Yên tĩnh', 'class' => 'pill-amenity pill-quiet', 'icon' => 'bi-volume-mute-fill'],
        'vua'      => ['label' => 'Độ ồn vừa', 'class' => 'pill-amenity pill-moderate', 'icon' => 'bi-volume-down-fill'],
        'on'       => ['label' => 'Khá nhộn nhịp', 'class' => 'pill-amenity pill-lively', 'icon' => 'bi-volume-up-fill'],
        default    => ['label' => 'Không rõ', 'class' => 'pill-amenity', 'icon' => 'bi-question-circle'],
    };
}

// is_place_open(): đang mở khi giờ hiện tại nằm giữa giờ mở/đóng, kể cả mở xuyên đêm.
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

// is_late_night_friendly(): tag 'Phù hợp học khuya' khi có wifi + ổ cắm và đóng từ 22:00 trở đi hoặc xuyên đêm.
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

// is_student_price(): tag 'Giá sinh viên' khi price_range là 1 hoặc 2.
function is_student_price(array $place): bool
{
    return isset($place['price_range']) && (int) $place['price_range'] >= 1 && (int) $place['price_range'] <= 2;
}

// get_google_map_embed_url(): tạo link nhúng Google Maps theo tên + địa chỉ quán ở Long Xuyên, zoom 16.
function get_google_map_embed_url(array $place): string
{
    $query = trim(($place['name'] ?? '') . ' ' . ($place['address'] ?? 'TP. Long Xuyên, An Giang'));
    return 'https://maps.google.com/maps?q=' . urlencode($query) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
}

// render_stars(): vẽ 5 sao (đầy, nửa, rỗng) từ điểm 0..5, $showNumber thì in thêm con số.
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

// render_pagination(): vẽ phân trang Bootstrap 5, giữ nguyên query lọc, cửa sổ trang hiện tại ±2.
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

    // Nút Trang trước: nếu đang ở trang 1 thì gán class 'disabled' (không bấm được) và link là '#', ngược lại link tới trang hiện tại trừ 1.
    $prevDisabled = ($currentPage <= 1) ? 'disabled' : '';
    $prevUrl = ($currentPage > 1) ? e($buildPageUrl($currentPage - 1)) : '#';
    $html .= '<li class="page-item ' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '"><i class="bi bi-chevron-left"></i></a></li>';

    // Các trang ở giữa: $start và $end là cửa sổ trang hiện tại +- 2, kẹp trong
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

    // Nút Trang sau: ngược với nút Trang trước, nếu đang ở trang cuối thì disabled và link '#', ngược lại link tới trang hiện tại cộng 1.
    $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextUrl = ($currentPage < $totalPages) ? e($buildPageUrl($currentPage + 1)) : '#';
    $html .= '<li class="page-item ' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '"><i class="bi bi-chevron-right"></i></a></li>';

    $html .= '</ul></nav>';
    return $html;
}

// truncate_text(): cắt chuỗi quá $limit ký tự (mb_substr giữ tiếng Việt) rồi thêm '...'.
function truncate_text(?string $text, int $limit = 100, string $ellipsis = '...'): string
{
    $text = trim((string) $text);
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit, 'UTF-8') . $ellipsis;
}

// get_card_amenities(): lấy tối đa 3 tiện ích đầu tiên của quán (bảng amenities) để hiện trên thẻ quán.
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
