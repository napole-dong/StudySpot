<?php
/**
 * ReviewController: Xử lý Thêm mới, Chỉnh sửa và Xóa đánh giá (Review) của thành viên
 * Đảm bảo mỗi user chỉ review 1 lần/quán, tự động tính lại avg_rating & total_reviews của quán
 */
class ReviewController extends Controller
{
    private Review $reviewModel;
    private Place $placeModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->reviewModel = new Review();
        $this->placeModel = new Place();
    }

    /**
     * Gửi đánh giá mới cho một quán (/review/store)
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('place');
        }

        $this->verifyCsrfToken();

        $currentUser = Session::user();
        $userId = (int) $currentUser['id'];
        $placeId = (int) ($_POST['place_id'] ?? 0);

        $place = $this->placeModel->findById($placeId, true);
        if (!$place) {
            Session::flash('alert', 'Địa điểm bạn đánh giá không tồn tại.', 'danger');
            $this->redirect('place');
        }

        // Kiểm tra ràng buộc: mỗi thành viên chỉ được đánh giá 1 lần cho 1 quán
        $existing = $this->reviewModel->findByPlaceAndUser($placeId, $userId);
        if ($existing) {
            Session::flash('alert', 'Bạn đã gửi đánh giá cho quán này trước đó. Bạn có thể chỉnh sửa đánh giá của mình.', 'warning');
            $this->redirect('place/show/' . $placeId . '#review-form-section');
        }

        $data = $this->collectAndValidate($placeId);

        // Xử lý upload ảnh đính kèm (nếu có chọn file)
        $imagePath = null;
        if (!empty($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = upload_image($_FILES['image'], 'reviews');
            if (!$uploadResult['success']) {
                Session::flash('alert', 'Lỗi ảnh đính kèm: ' . $uploadResult['error'], 'danger');
                $this->redirect('place/show/' . $placeId . '#review-form-section');
            }
            $imagePath = $uploadResult['path'];
        }

        $isAdmin = Session::isAdmin();

        $this->reviewModel->create([
            'place_id'       => $placeId,
            'user_id'        => $userId,
            'status'         => $isAdmin ? 'approved' : 'pending',
            'rating_overall' => $data['rating_overall'],
            'rating_wifi'    => $data['rating_wifi'],
            'rating_space'   => $data['rating_space'],
            'rating_price'   => $data['rating_price'],
            'content'        => $data['content'],
            'image_path'     => $imagePath,
        ]);

        Session::flash(
            'alert',
            $isAdmin
                ? 'Đánh giá của bạn đã được đăng ngay vì bạn là quản trị viên.'
                : 'Cảm ơn bạn đã gửi đánh giá! Nhận xét của bạn đang ở trạng thái Chờ duyệt (pending) và sẽ hiển thị sau khi Quản trị viên kiểm duyệt.',
            'success'
        );
        $this->redirect('place/show/' . $placeId . '#review-form-section');
    }

    /**
     * Sửa đánh giá của chính mình (/review/update/12)
     */
    public function update(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('place');
        }

        $this->verifyCsrfToken();

        $reviewId = (int) $id;
        $review = $this->reviewModel->findById($reviewId);

        if (!$review) {
            Session::flash('alert', 'Không tìm thấy đánh giá cần chỉnh sửa.', 'danger');
            $this->redirect('place');
        }

        $currentUser = Session::user();
        $userId = (int) $currentUser['id'];
        $placeId = (int) $review['place_id'];

        // Chỉ cho phép chính tác giả chỉnh sửa review của mình
        if ((int) $review['user_id'] !== $userId) {
            Session::flash('alert', 'Bạn không có quyền chỉnh sửa đánh giá của người khác.', 'danger');
            $this->redirect('place/show/' . $placeId);
        }

        $data = $this->collectAndValidate($placeId);

        $imagePath = $review['image_path'];
        if (!empty($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = upload_image($_FILES['image'], 'reviews');
            if (!$uploadResult['success']) {
                Session::flash('alert', 'Lỗi ảnh đính kèm: ' . $uploadResult['error'], 'danger');
                $this->redirect('place/show/' . $placeId . '#review-form-section');
            }
            // Xóa ảnh cũ trên ổ đĩa trước khi thay ảnh mới
            delete_uploaded_file($review['image_path']);
            $imagePath = $uploadResult['path'];
        }

        $wasAdmin = Session::isAdmin();

        $this->reviewModel->updateByUser($reviewId, [
            'place_id'       => $placeId,
            'rating_overall' => $data['rating_overall'],
            'rating_wifi'    => $data['rating_wifi'],
            'rating_space'   => $data['rating_space'],
            'rating_price'   => $data['rating_price'],
            'content'        => $data['content'],
            'image_path'     => $imagePath,
        ]);

        if ($wasAdmin) {
            $this->reviewModel->updateStatus($reviewId, 'approved');
        }

        Session::flash(
            'alert',
            $wasAdmin
                ? 'Đã cập nhật đánh giá của bạn thành công! Vì bạn là quản trị viên nên đánh giá hiển thị ngay, không cần chờ duyệt lại.'
                : 'Đã cập nhật đánh giá của bạn thành công! Đánh giá đã được chuyển sang chờ duyệt lại.',
            'info'
        );
        $this->redirect('place/show/' . $placeId . '#review-form-section');
    }

    /**
     * Xóa đánh giá của chính mình (/review/delete/12)
     */
    public function delete(int|string $id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('place');
        }

        $this->verifyCsrfToken();

        $reviewId = (int) $id;
        $review = $this->reviewModel->findById($reviewId);

        if (!$review) {
            Session::flash('alert', 'Đánh giá không tồn tại.', 'danger');
            $this->redirect('place');
        }

        $currentUser = Session::user();
        $userId = (int) $currentUser['id'];
        $placeId = (int) $review['place_id'];

        // Chỉ tác giả hoặc Admin mới được xóa
        if ((int) $review['user_id'] !== $userId && !Session::isAdmin()) {
            Session::flash('alert', 'Bạn không có quyền xóa đánh giá này.', 'danger');
            $this->redirect('place/show/' . $placeId);
        }

        // Xóa review (Model Review::delete tự động gọi Place::recalculateRating)
        $this->reviewModel->delete($reviewId);

        Session::flash('alert', 'Đã xóa đánh giá của bạn và cập nhật lại điểm của quán.', 'success');

        $redirectTo = $_POST['redirect_to'] ?? ('place/show/' . $placeId . '#review-form-section');
        $this->redirect($redirectTo);
    }

    /**
     * Thu thập và kiểm tra dữ liệu điểm số (1-5) + nội dung nhận xét
     */
    private function collectAndValidate(int $placeId): array
    {
        $ratingOverall = (int) ($_POST['rating_overall'] ?? 5);
        $ratingWifi = (int) ($_POST['rating_wifi'] ?? 5);
        $ratingSpace = (int) ($_POST['rating_space'] ?? 5);
        $ratingPrice = (int) ($_POST['rating_price'] ?? 5);
        $content = trim($_POST['content'] ?? '');

        $clampStar = static fn(int $val): int => max(1, min(5, $val));

        if ($content === '' || mb_strlen($content, 'UTF-8') < 10) {
            Session::flash('alert', 'Nội dung đánh giá phải có ít nhất 10 ký tự.', 'danger');
            $this->redirect('place/show/' . $placeId . '#review-form-section');
        }

        if (mb_strlen($content, 'UTF-8') > 2000) {
            Session::flash('alert', 'Nội dung đánh giá không được vượt quá 2000 ký tự.', 'danger');
            $this->redirect('place/show/' . $placeId . '#review-form-section');
        }

        return [
            'rating_overall' => $clampStar($ratingOverall),
            'rating_wifi'    => $clampStar($ratingWifi),
            'rating_space'   => $clampStar($ratingSpace),
            'rating_price'   => $clampStar($ratingPrice),
            'content'        => $content,
        ];
    }
}
