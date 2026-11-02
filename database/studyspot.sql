-- =========================================================================
-- ĐỒ ÁN HỌC PHẦN: CÔNG NGHỆ WEB - PHP (SEE518)
-- DỰ ÁN: StudySpot - Website review quán ăn & cà phê học bài cho sinh viên
-- Hệ quản trị CSDL: MySQL / MariaDB (XAMPP) - Charset: utf8mb4
-- =========================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';
SET FOREIGN_KEY_CHECKS = 0;

-- 1. KHỞI TẠO CƠ SỞ DỮ LIỆU
CREATE DATABASE IF NOT EXISTS `studyspot`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `studyspot`;

-- Xóa các bảng cũ nếu tồn tại để tránh lỗi khi import lại
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `place_images`;
DROP TABLE IF EXISTS `places`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- =========================================================================
-- 2. ĐỊNH NGHĨA CẤU TRÚC CÁC BẢNG (6 BẢNG)
-- =========================================================================

-- 2.1. Bảng người dùng (users)
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Họ và tên hiển thị',
  `email` VARCHAR(150) NOT NULL COMMENT 'Email đăng nhập duy nhất',
  `password` VARCHAR(255) NOT NULL COMMENT 'Mật khẩu mã hóa bằng password_hash',
  `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
  `avatar` VARCHAR(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện',
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user' COMMENT 'Vai trò người dùng',
  `status` ENUM('active', 'locked') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role_status` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2. Bảng danh mục loại hình quán (categories)
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Tên danh mục loại hình',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3. Bảng địa điểm / quán học bài (places)
CREATE TABLE `places` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL COMMENT 'Khóa ngoại tới categories',
  `name` VARCHAR(180) NOT NULL COMMENT 'Tên quán',
  `slug` VARCHAR(200) NOT NULL COMMENT 'Đường dẫn thân thiện SEO',
  `address` VARCHAR(255) NOT NULL COMMENT 'Địa chỉ chi tiết',
  `district` VARCHAR(100) NOT NULL COMMENT 'Quận / Khu vực đại học',
  `description` TEXT NOT NULL COMMENT 'Mô tả chi tiết không gian, menu, chỗ gửi xe',
  `price_range` TINYINT UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Mức giá: 1 (<35k), 2 (35-50k), 3 (50-80k), 4 (>80k)',
  `has_wifi` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Có Wifi mạnh, 0: Không',
  `has_outlet` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Nhiều ổ cắm điện, 0: Ít/Không',
  `has_ac` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Có máy lạnh, 0: Không gian mở',
  `noise_level` ENUM('yen_tinh', 'vua', 'on') NOT NULL DEFAULT 'yen_tinh' COMMENT 'Mức độ tiếng ồn',
  `open_time` TIME NOT NULL DEFAULT '07:00:00' COMMENT 'Giờ mở cửa',
  `close_time` TIME NOT NULL DEFAULT '22:30:00' COMMENT 'Giờ đóng cửa',
  `google_map_url` TEXT DEFAULT NULL COMMENT 'URL nhúng iframe Google Maps',
  `avg_rating` DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm trung bình từ các review đã duyệt',
  `total_reviews` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Tổng số review đã duyệt',
  `status` ENUM('active', 'hidden') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái hiển thị quán',
  `created_by` INT UNSIGNED DEFAULT NULL COMMENT 'Người tạo địa điểm',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_places_slug` (`slug`),
  KEY `idx_places_category` (`category_id`),
  KEY `idx_places_district` (`district`),
  KEY `idx_places_status` (`status`),
  KEY `idx_places_filter` (`status`, `category_id`, `district`, `price_range`, `noise_level`),
  KEY `fk_places_created_by` (`created_by`),
  CONSTRAINT `fk_places_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_places_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.4. Bảng hình ảnh của quán (place_images)
CREATE TABLE `place_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `place_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn ảnh trong assets/uploads/places/',
  `is_cover` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Ảnh bìa chính, 0: Ảnh phụ',
  PRIMARY KEY (`id`),
  KEY `idx_place_images_place` (`place_id`, `is_cover`),
  CONSTRAINT `fk_place_images_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.5. Bảng đánh giá của sinh viên (reviews)
CREATE TABLE `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `place_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `rating_overall` TINYINT UNSIGNED NOT NULL COMMENT 'Điểm tổng thể 1-5 sao',
  `rating_wifi` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Điểm tốc độ Wifi 1-5 sao',
  `rating_space` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Điểm không gian & bàn ghế 1-5 sao',
  `rating_price` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Điểm mức giá hợp lý 1-5 sao',
  `content` TEXT NOT NULL COMMENT 'Nội dung nhận xét chi tiết',
  `image_path` VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh chụp thực tế đính kèm (tùy chọn)',
  `status` ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái kiểm duyệt',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reviews_place_user` (`place_id`, `user_id`),
  KEY `idx_reviews_status` (`status`),
  KEY `idx_reviews_user` (`user_id`),
  KEY `idx_reviews_created_at` (`created_at`),
  CONSTRAINT `fk_reviews_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.6. Bảng danh sách quán yêu thích đã lưu (favorites)
CREATE TABLE `favorites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `place_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_favorites_user_place` (`user_id`, `place_id`),
  KEY `idx_favorites_place` (`place_id`),
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favorites_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- 3. DỮ LIỆU MẪU (SEED DATA)
-- =========================================================================

-- 3.1. Dữ liệu bảng users (1 Admin + 5 Sinh viên)
-- Lưu ý: Cột password đang chứa giá trị tạm 'TEMP_PASSWORD_RUN_SEED_USERS_PHP'.
-- Hãy chạy http://localhost/studyspot/database/seed_users.php để cập nhật bcrypt thật của '123456'.
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `avatar`, `role`, `status`, `created_at`) VALUES
(1, 'Quản Trị Viên StudySpot', 'admin@studyspot.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0901234567', NULL, 'admin', 'active', '2026-08-01 08:00:00'),
(2, 'Nguyễn Minh Khôi', 'khoi.nguyen@student.edu.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0912345678', NULL, 'user', 'active', '2026-08-10 09:30:00'),
(3, 'Trần Thảo Vy', 'vy.tran@student.edu.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0987654321', NULL, 'user', 'active', '2026-08-12 14:15:00'),
(4, 'Lê Hoàng Nam', 'nam.le@student.edu.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0933445566', NULL, 'user', 'active', '2026-08-20 19:45:00'),
(5, 'Phạm Ngọc Ánh', 'anh.pham@student.edu.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0977889900', NULL, 'user', 'active', '2026-09-02 10:20:00'),
(6, 'Võ Quốc Bảo', 'bao.vo@student.edu.vn', 'TEMP_PASSWORD_RUN_SEED_USERS_PHP', '0966112233', NULL, 'user', 'locked', '2026-09-05 16:00:00');

-- 3.2. Dữ liệu bảng categories (5 danh mục thực tế)
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Cà phê Học bài & Đọc sách'),
(2, 'Cà phê Mở xuyên đêm 24/7'),
(3, 'Co-working & Study Hub'),
(4, 'Trà sữa & Bánh ngọt Học nhóm'),
(5, 'Quán ăn nhẹ & Cơm trưa Sinh viên');

-- 3.3. Dữ liệu bảng places (9 quán thực tế gần các cụm trường Đại học)
INSERT INTO `places` (`id`, `category_id`, `name`, `slug`, `address`, `district`, `description`, `price_range`, `has_wifi`, `has_outlet`, `has_ac`, `noise_level`, `open_time`, `close_time`, `google_map_url`, `avg_rating`, `total_reviews`, `status`, `created_by`, `created_at`) VALUES
(1, 1, 'The Library Coffee & Study Hub', 'the-library-coffee-study-hub', '24 Đường D2 (Nguyễn Gia Trí), Phường 25', 'Quận Bình Thạnh', 'Không gian thiết kế theo mô hình thư viện mở cực kỳ yên tĩnh, nằm ngay sát cụm trường ĐH HUTECH, ĐH Giao Thông Vận Tải và ĐH Ngoại Thương. Mỗi bàn đều được trang bị 2 ổ cắm điện 3 chấu, đèn vàng chống mỏi mắt và tốc độ Wifi cáp quang 300Mbps riêng cho từng tầng. Giữ xe miễn phí có bảo vệ trông coi.', 2, 1, 1, 1, 'yen_tinh', '07:00:00', '23:00:00', 'https://www.google.com/maps?q=Nguyen+Gia+Tri+Binh+Thanh+Ho+Chi+Minh&output=embed', 5.00, 2, 'active', 1, '2026-08-05 08:30:00'),

(2, 2, 'Cú Trên Cây Coffee & Beer 24/7', 'cu-tren-cay-coffee-24-7', '128 Khu phố 6, Làng Đại Học Quốc Gia', 'TP. Thủ Đức', 'Điểm hẹn chạy đồ án quen thuộc của sinh viên Bách Khoa, Tự Nhiên, CNTT tại Làng Đại Học Thủ Đức. Quán mở cửa xuyên đêm, khu vực tầng 2 có phòng lạnh yên tĩnh trải thảm và bàn dài cho nhóm 6-8 bạn làm tiểu luận. Menu nước giá sinh viên chỉ từ 25k, có bán kèm mì trộn và bánh mì nóng ban đêm.', 1, 1, 1, 1, 'vua', '00:00:00', '23:59:00', 'https://www.google.com/maps?q=Lang+Dai+Hoc+Thu+Duc+Ho+Chi+Minh&output=embed', 4.50, 2, 'active', 1, '2026-08-08 10:00:00'),

(3, 3, 'WorkFlow Space & Roastery', 'workflow-space-roastery', '195/10 Điện Biên Phủ, Phường 15', 'Quận Bình Thạnh', 'Mô hình kết hợp giữa quán cà phê Specialty và không gian làm việc Co-working dành cho sinh viên làm khóa luận tốt nghiệp hoặc freelancer. Ghế công thái học ngồi lâu không đau lưng, có bảng trắng (whiteboard) cho nhóm brainstorm và khu vực gọi điện/họp Zoom riêng biệt.', 3, 1, 1, 1, 'yen_tinh', '07:30:00', '22:30:00', 'https://www.google.com/maps?q=195+Dien+Bien+Phu+Binh+Thanh+Ho+Chi+Minh&output=embed', 5.00, 1, 'active', 1, '2026-08-12 11:20:00'),

(4, 1, 'Lạc Concept - Góc Nhỏ Học Bài', 'lac-concept-goc-nho-hoc-bai', '310/15 Sư Vạn Hạnh, Phường 12', 'Quận 10', 'Nằm trong con hẻm yên bình ngay gần ĐH Ngoại Ngữ - Tin Học (HUFLIT) và ĐH Kinh Tế (UEH). Quán mang phong cách tối giản kiểu Nhật, nhạc Lofi không lời âm lượng vừa phải, cực kỳ hợp để ngồi giải đề TOEIC/IELTS hoặc code đồ án suốt cả buổi chiều.', 2, 1, 1, 1, 'yen_tinh', '08:00:00', '22:30:00', 'https://www.google.com/maps?q=310+Su+Van+Hanh+Quan+10+Ho+Chi+Minh&output=embed', 4.50, 2, 'active', 1, '2026-08-15 14:00:00'),

(5, 2, 'Thức Coffee - Chạy Deadline Xuyên Đêm', 'thuc-coffee-chay-deadline-xuyen-dem', '42 Nguyễn Thị Minh Khai, Phường Đa Kao', 'Quận 1', 'Địa điểm cứu cánh cho những đêm chạy deadline gấp trước ngày bảo vệ đồ án. Quán sáng đèn 24/24, luôn có nhân viên và bảo vệ trực đêm an toàn. Tầng lửng có máy lạnh và ổ điện dọc theo tường, cà phê sữa đá đậm vị giúp tỉnh táo suốt đêm.', 2, 1, 1, 1, 'vua', '00:00:00', '23:59:00', 'https://www.google.com/maps?q=42+Nguyen+Thi+Minh+Khai+Quan+1+Ho+Chi+Minh&output=embed', 4.00, 1, 'active', 1, '2026-08-18 16:40:00'),

(6, 4, 'Trạm Trà & Bánh Ngọt Học Nhóm', 'tram-tra-banh-ngot-hoc-nhom', '85 Nguyễn Hữu Thọ, Phường Tân Phong', 'Quận 7', 'Cách cổng trường ĐH Tôn Đức Thắng và RMIT chỉ 3 phút đi bộ. Không gian rộng rãi, thoáng đãng với nhiều bàn tròn và bàn dài 10 người, rất thích hợp để họp nhóm thuyết trình hoặc thảo luận bài tập lớn. Combo trà trái cây + bánh sừng bò giảm 15% khi xuất trình thẻ sinh viên.', 2, 1, 1, 1, 'vua', '07:00:00', '22:30:00', 'https://www.google.com/maps?q=85+Nguyen+Huu+Tho+Quan+7+Ho+Chi+Minh&output=embed', 4.00, 1, 'active', 1, '2026-08-22 09:15:00'),

(7, 5, 'Bếp Sinh Viên & Cà Phê Sách', 'bep-sinh-vien-ca-phe-sach', '56 Hoàng Diệu 2, Phường Linh Chiểu', 'TP. Thủ Đức', 'Quán tích hợp ăn trưa văn phòng - sinh viên và cà phê học bài buổi chiều. Sinh viên có thể ăn cơm phần sạch sẽ chỉ 32k rồi ở lại tầng 2 có máy lạnh mát rượi để ôn bài đến tối mà không cần di chuyển nhiều nơi.', 1, 1, 1, 1, 'vua', '06:30:00', '22:00:00', 'https://www.google.com/maps?q=56+Hoang+Dieu+2+Thu+Duc+Ho+Chi+Minh&output=embed', 5.00, 1, 'active', 1, '2026-08-26 13:00:00'),

(8, 3, 'Nấp Sài Gòn - Study & Chill', 'nap-sai-gon-study-chill', '3/5 Nguyễn Văn Thủ, Phường Đa Kao', 'Quận 1', 'Không gian xanh mát tách biệt khỏi tiếng còi xe ồn ào, nổi tiếng với món cà phê trứng và cacao nóng. Tầng trên được quy hoạch thành khu Quiet Zone (Hạn chế nói chuyện lớn tiếng), cực kỳ lý tưởng cho các bạn cần sự tập trung tuyệt đối.', 3, 1, 1, 1, 'yen_tinh', '08:00:00', '21:30:00', 'https://www.google.com/maps?q=3+Nguyen+Van+Thu+Quan+1+Ho+Chi+Minh&output=embed', 5.00, 1, 'active', 1, '2026-09-01 08:45:00'),

(9, 4, 'Góc Sân Thượng Acoustic & Tea', 'goc-san-thuong-acoustic-tea', '212 Lê Văn Sỹ, Phường 10', 'Quận Phú Nhuận', 'Quán nằm trên tầng thượng thoáng gió, buổi sáng và chiều khá mát mẻ, tuy nhiên buổi tối từ 19h30 có giao lưu nhạc Acoustic nên hơi ồn, phù hợp để thư giãn sau giờ thi hơn là ngồi học bài cần sự yên tĩnh.', 3, 1, 0, 0, 'on', '15:00:00', '23:00:00', 'https://www.google.com/maps?q=212+Le+Van+Sy+Phu+Nhuan+Ho+Chi+Minh&output=embed', 3.00, 1, 'active', 1, '2026-09-04 17:10:00');

-- 3.4. Dữ liệu bảng place_images (Ảnh mẫu của các quán - Hệ thống sẽ tự động fallback về placeholder.svg nếu file chưa có trên đĩa)
INSERT INTO `place_images` (`id`, `place_id`, `image_path`, `is_cover`) VALUES
(1, 1, 'uploads/places/sample_library_1.jpg', 1),
(2, 1, 'uploads/places/sample_library_2.jpg', 0),
(3, 2, 'uploads/places/sample_cutrencay_1.jpg', 1),
(4, 2, 'uploads/places/sample_cutrencay_2.jpg', 0),
(5, 3, 'uploads/places/sample_workflow_1.jpg', 1),
(6, 4, 'uploads/places/sample_lac_1.jpg', 1),
(7, 5, 'uploads/places/sample_thuc_1.jpg', 1),
(8, 6, 'uploads/places/sample_tramtra_1.jpg', 1),
(9, 7, 'uploads/places/sample_bepsv_1.jpg', 1),
(10, 8, 'uploads/places/sample_napsg_1.jpg', 1),
(11, 9, 'uploads/places/sample_santhuong_1.jpg', 1);

-- 3.5. Dữ liệu bảng reviews (18 đánh giá chia đều: 12 approved + 6 pending, rải qua các tháng để vẽ biểu đồ Chart.js)
INSERT INTO `reviews` (`id`, `place_id`, `user_id`, `rating_overall`, `rating_wifi`, `rating_space`, `rating_price`, `content`, `image_path`, `status`, `created_at`) VALUES
-- Các review ĐÃ DUYỆT (approved) -> khớp chính xác với avg_rating và total_reviews của từng quán
(1, 1, 2, 5, 5, 5, 5, 'Quán ruột của mình mỗi mùa thi cuối kỳ! Bàn học rộng rãi, ổ điện ngay tầm tay không cần cúi xuống gầm bàn. Wifi tải tài liệu và xem bài giảng video cực mượt.', NULL, 'approved', '2026-06-15 14:20:00'),
(2, 1, 3, 5, 5, 5, 4, 'Không gian tầng 2 yên tĩnh tuyệt đối, mọi người đều tự giác giữ trật tự. Nước uống tầm 40k-45k nhưng ngồi học từ sáng tới tối vẫn rất thoải mái, nhân viên châm trà đá liên tục.', NULL, 'approved', '2026-07-10 09:40:00'),
(3, 2, 2, 5, 4, 5, 5, 'Đúng chuẩn quán quốc dân ở Làng Đại Học. Tụi mình ngồi code đồ án PHP tới 3h sáng vẫn rất đông các bạn sinh viên khác cùng chạy deadline nên có động lực hẳn!', NULL, 'approved', '2026-07-22 23:15:00'),
(4, 2, 4, 4, 4, 4, 5, 'Giá nước siêu hạt dẻ cho sinh viên, chỉ 28k ly bạc xỉu lớn. Giờ cao điểm buổi tối hơi đông nên bạn nào đi nhóm nhớ tới sớm trước 19h để chọn bàn dài nhé.', NULL, 'approved', '2026-08-05 20:10:00'),
(5, 3, 3, 5, 5, 5, 4, 'Ghế ngồi cực êm, điều hòa mát lạnh và có sẵn bảng trắng cho nhóm thảo luận. Giá nước nhỉnh hơn một chút nhưng chất lượng không gian hoàn toàn xứng đáng.', NULL, 'approved', '2026-08-19 15:30:00'),
(6, 4, 5, 5, 5, 5, 5, 'Quán nhỏ xinh trong hẻm Sư Vạn Hạnh nên cực kỳ yên tĩnh. Nhạc Lofi nhẹ nhàng, ánh sáng đèn vàng ấm áp, ngồi luyện đề Reading rất vào.', NULL, 'approved', '2026-09-03 10:05:00'),
(7, 4, 2, 4, 5, 4, 4, 'Wifi mạnh, mỗi bàn đều có ổ cắm. Điểm trừ nhỏ là chỗ để xe trong hẻm hơi hẹp vào giờ chiều, nhưng bác bảo vệ dắt xe giúp rất nhiệt tình.', NULL, 'approved', '2026-09-14 16:50:00'),
(8, 5, 4, 4, 4, 4, 4, 'Mở xuyên đêm ở ngay trung tâm Quận 1 nên rất tiện. Phù hợp khi cần hoàn thiện gấp slide báo cáo ban đêm, cà phê đậm đà giúp tỉnh ngủ.', NULL, 'approved', '2026-09-25 01:30:00'),
(9, 6, 3, 4, 4, 5, 4, 'Bàn dài rộng rãi, nhóm mình 7 người họp làm tiểu luận thoải mái không sợ ảnh hưởng bàn bên cạnh. Trà đào cam sả uống rất tươi mát.', NULL, 'approved', '2026-10-04 14:00:00'),
(10, 7, 5, 5, 5, 4, 5, 'Cơm trưa sinh viên 32k đầy đặn, ăn xong lên lầu 2 bật laptop học bài tới chiều luôn. Tiết kiệm được rất nhiều thời gian và chi phí cho sinh viên Thủ Đức.', NULL, 'approved', '2026-10-12 12:30:00'),
(11, 8, 4, 5, 5, 5, 4, 'Khu Quiet Zone trên lầu thực sự đỉnh cao cho ai cần tập trung 100%. Mùi cà phê thơm dịu, bàn gỗ cao vừa tầm gõ phím.', NULL, 'approved', '2026-10-20 09:15:00'),
(12, 9, 5, 3, 3, 3, 3, 'Cảnh hoàng hôn trên sân thượng đẹp nhưng không có ổ cắm điện ở khu ngoài trời và buổi tối nhạc khá lớn, chỉ hợp đi chill cuối tuần chứ khó học bài.', NULL, 'approved', '2026-10-28 19:45:00'),

-- Các review CHỜ DUYỆT (pending) -> để Admin kiểm thử chức năng Duyệt / Ẩn / Xóa và tự động tính lại điểm
(13, 1, 4, 4, 5, 4, 4, 'Hôm qua mình mới ghé học thử, tầng 3 mới mở thêm nhiều bàn đơn có vách ngăn riêng tư rất thích.', NULL, 'pending', '2026-11-01 08:30:00'),
(14, 3, 2, 5, 5, 5, 4, 'Quán mới nâng cấp đường truyền Wifi 5GHz riêng cho từng lầu, họp Google Meet 2 tiếng liền không bị giật lag chút nào.', NULL, 'pending', '2026-11-01 11:00:00'),
(15, 5, 3, 5, 4, 4, 5, 'Đêm qua nhóm mình ngồi sửa báo cáo tới 4h sáng, nhân viên vẫn vui vẻ hỗ trợ mượn ổ điện nối dài.', NULL, 'pending', '2026-11-01 15:20:00'),
(16, 6, 2, 4, 4, 5, 5, 'Có chương trình giảm giá 15% khi đưa thẻ sinh viên nên tính ra mỗi đứa chỉ tốn hơn 30k cho cả buổi chiều họp nhóm.', NULL, 'pending', '2026-11-01 18:10:00'),
(17, 7, 3, 5, 5, 5, 5, 'Món bún thịt nướng buổi trưa rất ngon, trà tắc khổng lồ chỉ 20k, ổ cắm điện có ở mọi góc tường.', NULL, 'pending', '2026-11-02 07:45:00'),
(18, 8, 2, 4, 5, 5, 4, 'Cà phê trứng béo thơm, không gian cực kỳ yên tĩnh, sẽ quay lại vào tuần ôn thi sắp tới.', NULL, 'pending', '2026-11-02 09:00:00');

-- 3.6. Dữ liệu bảng favorites (Quán yêu thích của các tài khoản mẫu)
INSERT INTO `favorites` (`id`, `user_id`, `place_id`, `created_at`) VALUES
(1, 2, 1, '2026-09-10 10:00:00'),
(2, 2, 2, '2026-09-12 11:30:00'),
(3, 2, 4, '2026-09-15 14:20:00'),
(4, 3, 1, '2026-09-18 09:00:00'),
(5, 3, 3, '2026-09-20 16:45:00'),
(6, 4, 2, '2026-10-01 21:00:00'),
(7, 5, 7, '2026-10-15 13:10:00');

SET FOREIGN_KEY_CHECKS = 1;
