-- =========================================================================
-- ĐỒ ÁN HỌC PHẦN: CÔNG NGHỆ WEB - PHP (SEE518)
-- DỰ ÁN: StudySpot - Website review quán nước tại TP. Long Xuyên, An Giang (Khu ĐH An Giang - AGU)
-- Hệ quản trị CSDL: MySQL / MariaDB (XAMPP) - Charset: utf8mb4
-- File này được ĐỒNG BỘ từ database live ngày 2026-11-04 (33 quán, 4 danh mục, 5 tiện ích).
-- Seed này ĐÃ BAO GỒM bảng amenities, place_amenities, cột price_min/price_max
-- và cột google_rating/google_rating_count/google_maps_place_url,
-- nên khi setup mới chỉ cần import file này, KHÔNG cần chạy các file migrate_20261103 nữa.
-- =========================================================================

-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: studyspot
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `studyspot`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `studyspot` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `studyspot`;

--
-- Table structure for table `amenities`
--

DROP TABLE IF EXISTS `amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amenities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_amenities_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amenities`
--

LOCK TABLES `amenities` WRITE;
/*!40000 ALTER TABLE `amenities` DISABLE KEYS */;
INSERT INTO `amenities` VALUES (1,'Wifi mạnh','wifi',1,1,'2026-11-03 18:10:19'),(2,'Ổ cắm điện','outlet',2,1,'2026-11-03 18:10:19'),(3,'Máy lạnh','ac',3,1,'2026-11-03 18:10:19'),(5,'Có chỗ đậu xe','co-cho-dau-xe',4,1,'2026-11-03 18:20:04'),(6,'Có thú cưng','co-thu-cung',5,1,'2026-11-03 18:20:23');
/*!40000 ALTER TABLE `amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Tên danh mục loại hình',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Cà phê Học bài & Đọc sách'),(2,'Cà phê Mở khuya'),(3,'Không gian Yên tĩnh & Học nhóm'),(4,'Trà sữa & Bánh ngọt Sinh viên');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favorites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `place_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_favorites_user_place` (`user_id`,`place_id`),
  KEY `idx_favorites_place` (`place_id`),
  CONSTRAINT `fk_favorites_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (1,2,1,'2026-11-02 20:12:28'),(2,2,2,'2026-11-02 20:12:28'),(3,2,6,'2026-11-02 20:12:28'),(4,2,10,'2026-11-02 20:12:28'),(5,3,1,'2026-11-02 20:12:28'),(6,3,3,'2026-11-02 20:12:28'),(7,3,4,'2026-11-02 20:12:28'),(8,4,1,'2026-11-02 20:12:28'),(9,4,6,'2026-11-02 20:12:28'),(11,5,2,'2026-11-02 20:12:28'),(12,5,7,'2026-11-02 20:12:28'),(13,5,9,'2026-11-02 20:12:28'),(14,1,1,'2026-11-03 17:57:42');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place_amenities`
--

DROP TABLE IF EXISTS `place_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `place_amenities` (
  `place_id` int(10) unsigned NOT NULL,
  `amenity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`place_id`,`amenity_id`),
  KEY `idx_pa_amenity` (`amenity_id`),
  CONSTRAINT `fk_pa_amenity` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pa_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place_amenities`
--

LOCK TABLES `place_amenities` WRITE;
/*!40000 ALTER TABLE `place_amenities` DISABLE KEYS */;
INSERT INTO `place_amenities` VALUES (1,1),(1,2),(1,3),(2,1),(2,2),(2,3),(3,1),(3,2),(3,3),(4,1),(4,2),(4,3),(5,1),(5,2),(5,3),(6,1),(6,2),(6,3),(7,1),(7,2),(8,5),(9,1),(9,2),(9,3),(10,1),(10,2),(10,3),(11,1),(11,2),(11,3),(16,1),(16,2),(16,3),(17,1),(17,2),(17,3);
/*!40000 ALTER TABLE `place_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place_images`
--

DROP TABLE IF EXISTS `place_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `place_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `place_id` int(10) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL COMMENT 'Đường dẫn ảnh trong assets/uploads/places/',
  `is_cover` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1: Ảnh bìa chính, 0: Ảnh phụ',
  PRIMARY KEY (`id`),
  KEY `idx_place_images_place` (`place_id`,`is_cover`),
  CONSTRAINT `fk_place_images_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place_images`
--

LOCK TABLES `place_images` WRITE;
/*!40000 ALTER TABLE `place_images` DISABLE KEYS */;
INSERT INTO `place_images` VALUES (1,1,'uploads/places/sample_library_1.jpg',1),(2,2,'uploads/places/sample_lac_1.jpg',1),(3,3,'uploads/places/sample_tramtra_1.jpg',1),(4,4,'uploads/places/sample_napsg_1.jpg',1),(5,5,'uploads/places/sample_workflow_1.jpg',1),(6,6,'uploads/places/sample_thuc_1.jpg',1),(7,7,'uploads/places/sample_lac_1.jpg',1),(8,8,'uploads/places/sample_cutrencay_1.jpg',1),(9,9,'uploads/places/sample_workflow_1.jpg',1),(10,10,'uploads/places/sample_cutrencay_2.jpg',1),(11,11,'uploads/places/sample_library_2.jpg',1),(16,16,'uploads/places/sample_library_1.jpg',1),(17,17,'uploads/places/sample_tramtra_1.jpg',1);
/*!40000 ALTER TABLE `place_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `places` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL COMMENT 'Khóa ngoại tới categories',
  `name` varchar(180) NOT NULL COMMENT 'Tên quán',
  `slug` varchar(200) NOT NULL COMMENT 'Đường dẫn thân thiện SEO',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ chi tiết',
  `district` varchar(100) NOT NULL COMMENT 'Quận / Khu vực đại học',
  `description` text NOT NULL COMMENT 'Mô tả chi tiết không gian, menu, chỗ gửi xe',
  `price_range` tinyint(3) unsigned NOT NULL DEFAULT 2 COMMENT 'Mức giá theo giá tối đa: 1 (<=25k), 2 (<=45k), 3 (<=65k), 4 (>65k)',
  `price_min` int(10) unsigned DEFAULT NULL,
  `price_max` int(10) unsigned DEFAULT NULL,
  `noise_level` enum('yen_tinh','vua','on') NOT NULL DEFAULT 'yen_tinh' COMMENT 'Mức độ tiếng ồn',
  `open_time` time NOT NULL DEFAULT '07:00:00' COMMENT 'Giờ mở cửa',
  `close_time` time NOT NULL DEFAULT '22:30:00' COMMENT 'Giờ đóng cửa',
  `google_map_url` text DEFAULT NULL COMMENT 'URL nhúng iframe Google Maps',
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm trung bình từ các review đã duyệt',
  `total_reviews` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Tổng số review đã duyệt',
  `status` enum('active','hidden') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái hiển thị quán',
  `created_by` int(10) unsigned DEFAULT NULL COMMENT 'Người tạo địa điểm',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `google_rating` decimal(3,2) DEFAULT NULL,
  `google_rating_count` int(10) unsigned DEFAULT NULL,
  `google_maps_place_url` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_places_slug` (`slug`),
  KEY `idx_places_category` (`category_id`),
  KEY `idx_places_district` (`district`),
  KEY `idx_places_status` (`status`),
  KEY `idx_places_filter` (`status`,`category_id`,`district`,`price_range`,`noise_level`),
  KEY `fk_places_created_by` (`created_by`),
  CONSTRAINT `fk_places_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_places_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `places`
--

LOCK TABLES `places` WRITE;
/*!40000 ALTER TABLE `places` DISABLE KEYS */;
INSERT INTO `places` VALUES (1,1,'NHÀ KHO Coffee','nha-kho-coffee','Đường Ung Văn Khiêm, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Tọa lạc ngay trục đường Ung Văn Khiêm sầm uất sát cổng Trường Đại học An Giang (AGU), NHÀ KHO Coffee là \"trạm đóng quân\" quen thuộc của sinh viên mỗi mùa thi học kỳ. Không gian decor kiểu Industrial rộng thoáng, bàn gỗ dài thoải mái đặt laptop và giáo trình, Wifi cáp quang mạnh ổn định cùng hệ thống ổ cắm điện bố trí dày đặc dọc các dãy bàn.',2,25000,45000,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=NH%C3%80+KHO+Coffee+%C4%90%C6%B0%E1%BB%9Dng+Ung+V%C4%83n+Khi%C3%AAm%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,3,'active',1,'2026-09-28 20:12:28',NULL,NULL,NULL),(2,1,'Cồ-Lá Coffee','co-la-coffee','Đường Ung Văn Khiêm, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Không gian xanh mát, yên tĩnh nằm trên đường Ung Văn Khiêm ngay gần Trường Đại học An Giang. Cồ-Lá Coffee sở hữu nhiều góc ngồi khuất gió, ánh sáng tự nhiên dịu mắt kết hợp đèn vàng ấm áp, rất lý tưởng để sinh viên AGU ngồi đọc tài liệu, ôn luyện ngoại ngữ hoặc gõ code đồ án suốt cả buổi chiều.',2,17000,35000,'yen_tinh','06:00:00','22:30:00','https://maps.google.com/maps?q=C%E1%BB%93-L%C3%A1+Coffee+%C4%90%C6%B0%E1%BB%9Dng+Ung+V%C4%83n+Khi%C3%AAm%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',4.50,2,'active',1,'2026-09-29 20:12:28',NULL,NULL,NULL),(3,4,'Mia Maia Café & Savoury','mia-maia-cafe-savoury','Số 3 Lý Thái Tổ, Phường Mỹ Long, TP. Long Xuyên, An Giang','Phường Mỹ Long','Tiệm bánh ngọt & cà phê học nhóm cực kỳ nổi tiếng tại số 3 Lý Thái Tổ, Phường Mỹ Long. Phòng máy lạnh mát rượi, hương thơm bánh nướng bơ sữa dịu nhẹ cùng những bộ bàn ghế êm ái giúp các nhóm sinh viên thảo luận thuyết trình, làm bài tập lớn trong không gian hiện đại và truyền cảm hứng.',2,25000,45000,'vua','07:30:00','22:30:00','https://maps.google.com/maps?q=Mia+Maia+Caf%C3%A9+%26+Savoury+S%E1%BB%91+3+L%C3%BD+Th%C3%A1i+T%E1%BB%95%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Long%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,2,'active',1,'2026-09-30 20:12:28',NULL,NULL,NULL),(4,1,'Cafe 1985 in An Giang','cafe-1985-in-an-giang','33/3 Trần Hưng Đạo, Phường Mỹ Xuyên, TP. Long Xuyên, An Giang','Phường Mỹ Xuyên','Nằm nép mình trong Hẻm 3 Trần Hưng Đạo (Phường Mỹ Xuyên), Cafe 1985 in An Giang tách biệt hẳn khỏi tiếng còi xe ồn ào ngoài trục đường lớn. Quán mang đậm phong cách Vintage hoài cổ, nhạc không lời du dương, nhiều bàn đơn và bàn đôi yên tĩnh tuyệt đối cho những bạn cần sự tập trung cao độ.',2,20000,35000,'yen_tinh','06:30:00','22:00:00','https://maps.google.com/maps?q=Cafe+1985+in+An+Giang+H%E1%BA%BBm+3+Tr%E1%BA%A7n+H%C6%B0ng+%C4%90%E1%BA%A1o%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,2,'active',1,'2026-10-01 20:12:28',NULL,NULL,NULL),(5,3,'Panda Cafe','panda-cafe','Số 9/9 Trần Hưng Đạo, Phường Mỹ Xuyên, TP. Long Xuyên, An Giang','Phường Mỹ Xuyên','Tọa lạc tại số 9/9 Trần Hưng Đạo, Phường Mỹ Xuyên, Panda Cafe nổi bật với không gian sân vườn kết hợp tầng cao thoáng rộng nhìn ra toàn cảnh thành phố Long Xuyên. Quán có cả khu phòng lạnh yên tĩnh để học bài lẫn khu vực bàn dài rộng rãi cho các câu lạc bộ, nhóm sinh viên AGU tổ chức họp mặt.',2,25000,45000,'vua','06:30:00','22:00:00','https://maps.google.com/maps?q=Panda+Cafe+S%E1%BB%91+9%2F9+Tr%E1%BA%A7n+H%C6%B0ng+%C4%90%E1%BA%A1o%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',4.00,1,'active',1,'2026-10-02 20:12:28',4.46,110,'https://maps.google.com/maps?q=Panda+Cafe+S%E1%BB%91+9%2F9+Tr%E1%BA%A7n+H%C6%B0ng+%C4%90%E1%BA%A1o%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed'),(6,2,'Mo Đen Café','mo-den-cafe','710/11 Bùi Văn Danh, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Địa điểm cứu cánh cho những đêm chạy deadline đồ án của sinh viên khu vực Đông Xuyên. Nằm tại hẻm 710/11 Bùi Văn Danh sát bên trường, Mo Đen Café mở cửa tới 22:30 đêm với không gian yên tĩnh, cà phê pha phin và pha máy đậm vị giúp giữ sự tỉnh táo suốt buổi tối.',1,NULL,25000,'yen_tinh','06:30:00','22:30:00','https://maps.google.com/maps?q=Mo+%C4%90en+Caf%C3%A9+710%2F11+B%C3%B9i+V%C4%83n+Danh%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,2,'active',1,'2026-10-03 20:12:28',NULL,NULL,NULL),(7,1,'Quán Bình Yên','quan-binh-yen','521A Hẻm Võ Thị Sáu, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Đúng như tên gọi \"Bình Yên\", quán nằm tại 521A Hẻm Võ Thị Sáu (Phường Đông Xuyên) mang lại cảm giác thư thái, nhẹ nhàng ngay từ khi bước vào. Không gian nhiều cây xanh, bàn học sạch sẽ, ổ điện bố trí thuận tiện và mức giá nước uống rất mềm cho sinh viên ngồi đọc sách cả ngày.',1,20000,25000,'yen_tinh','14:00:00','21:00:00','https://maps.google.com/maps?q=Qu%C3%A1n+B%C3%ACnh+Y%C3%AAn+521A+H%E1%BA%BBm+V%C3%B5+Th%E1%BB%8B+S%C3%A1u%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,1,'active',1,'2026-10-04 20:12:28',NULL,NULL,NULL),(8,1,'Cà phê vợt Hai Ngầu','ca-phe-vot-hai-ngau','Số 9 Bùi Văn Danh, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Quán cà phê vợt truyền thống lâu đời tại số 9 Bùi Văn Danh, Phường Đông Xuyên, ngay sát khu giảng đường Đại học An Giang. Hương vị cà phê vợt thơm lừng đặc trưng miền Tây với mức giá siêu bình dân dưới 25.000đ, thích hợp cho sinh viên ghé uống cà phê sáng và ôn bài nhanh trước giờ vào lớp.',1,10000,20000,'on','04:00:00','19:00:00','https://maps.google.com/maps?q=C%C3%A0+ph%C3%AA+v%E1%BB%A3t+Hai+Ng%E1%BA%A7u+S%E1%BB%91+9+B%C3%B9i+V%C4%83n+Danh%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',4.00,1,'active',1,'2026-10-05 20:12:28',NULL,NULL,NULL),(9,3,'Đĩa Than Coffee','dia-than-coffee','Đường Số 12, KDC Tây Sông Hậu, Phường Mỹ Quý, TP. Long Xuyên, An Giang','Phường Mỹ Quý','Nằm trong Khu dân cư Tây Sông Hậu yên tĩnh (Phường Mỹ Quý), Đĩa Than Coffee gây ấn tượng với bộ sưu tập đĩa than cổ điển, âm nhạc Acoustic/Jazz nhẹ nhàng và hệ thống bàn gỗ nguyên tấm cực rộng để đặt laptop, bản vẽ hay tài liệu khóa luận tốt nghiệp.',3,25000,50000,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=%C4%90%C4%A9a+Than+Coffee+S%E1%BB%91+C12+%C4%90%C6%B0%E1%BB%9Dng+S%E1%BB%91+12%2C+KDC+T%C3%A2y+S%C3%B4ng+H%E1%BA%ADu%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Qu%C3%BD%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,2,'active',1,'2026-10-06 20:12:28',NULL,NULL,NULL),(10,2,'Pi Kafé Long Xuyên','pi-kafe-long-xuyen','Đường Số 5G, Phường Mỹ Quý, TP. Long Xuyên, An Giang','Phường Mỹ Quý','Một trong những quán cà phê học bài mở khuya tới 23:00 đêm tại khu vực Phường Mỹ Quý, TP. Long Xuyên. Pi Kafé được thiết kế theo phong cách tối giản hiện đại, ánh sáng trắng - vàng tiêu chuẩn chống mỏi mắt, Wifi tốc độ cao và ổ cắm điện tại mọi góc ngồi cho các \"cú đêm\" AGU chạy đồ án.',3,30000,50000,'yen_tinh','07:00:00','23:00:00','https://maps.google.com/maps?q=Pi+Kaf%C3%A9+Long+Xuy%C3%AAn+%C4%90%C6%B0%E1%BB%9Dng+S%E1%BB%91+5G%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+Qu%C3%BD%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,2,'active',1,'2026-10-07 20:12:28',NULL,NULL,NULL),(11,2,'Cafe Lá','cafe-la','Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Quán cà phê lá đối diện bệnh viện đa khoa mới của tỉnh, không gian rộng rãi mát mẻ, phía sau có khu cà phê võng thoáng mát thích hợp nghỉ trưa, có phục vụ cờ tướng. Giá mỗi ly cà phê 10.000₫, nằm võng 12.000₫.',1,10000,12000,'vua','07:00:00','22:00:00','https://maps.google.com/maps?q=Cafe+L%C3%A1+Ng%C3%A3+4+Ung+V%C4%83n+Khi%C3%AAm+-+L%C3%BD+Th%C3%A1i+T%E1%BB%95%2C+Ph%C6%B0%E1%BB%9Dng+%C4%90%C3%B4ng+Xuy%C3%AAn%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',4.00,1,'active',1,'2026-10-08 20:12:28',NULL,NULL,NULL),(16,3,'LangThang Coffee & Study','langthang-coffee-study','Số 18 Tôn Đức Thắng, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Không gian học bài và làm việc yên tĩnh ngay trung tâm Phường Mỹ Bình, gần Công viên Nguyễn Du và bờ hồ Nguyễn Du. Quán có phòng máy lạnh rộng rãi, bàn học cao đúng chuẩn công thái học, rất hợp cho sinh viên AGU đổi gió ngồi làm khóa luận cuối tuần.',2,25000,45000,'yen_tinh','06:30:00','22:30:00','https://maps.google.com/maps?q=LangThang+Coffee+%26+Study+S%E1%BB%91+18+T%C3%B4n+%C4%90%E1%BB%A9c+Th%E1%BA%AFng%2C+Ph%C6%B0%E1%BB%9Dng+M%E1%BB%B9+B%C3%ACnh%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',5.00,1,'active',1,'2026-10-13 20:12:28',NULL,NULL,NULL),(17,4,'Tiệm Trà Tháng Tư - Bình Khánh','tiem-tra-thang-tu-binh-khanh','Số 245 Hàm Nghi, Phường Bình Khánh, TP. Long Xuyên, An Giang','Phường Bình Khánh','Góc trà trái cây & cà phê học bài thoáng mát trên đường Hàm Nghi, Phường Bình Khánh. Phù hợp cho các bạn sinh viên nhà ở khu vực Bình Khánh tụ họp ôn bài nhóm, có đầy đủ ổ điện 3 chấu và Wifi 5GHz tốc độ cao.',1,NULL,25000,'vua','07:00:00','22:00:00','https://maps.google.com/maps?q=Ti%E1%BB%87m+Tr%C3%A0+Th%C3%A1ng+T%C6%B0+-+B%C3%ACnh+Kh%C3%A1nh+S%E1%BB%91+245+H%C3%A0m+Nghi%2C+Ph%C6%B0%E1%BB%9Dng+B%C3%ACnh+Kh%C3%A1nh%2C+TP.+Long+Xuy%C3%AAn%2C+An+Giang&t=&z=16&ie=UTF8&iwloc=&output=embed',4.00,1,'active',1,'2026-10-14 20:12:28',NULL,NULL,NULL),(18,4,'Yotsubato Cakes & Coffee','yotsubato-cakes-coffee','Số 9 Ung Văn Khiêm, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Quán cà phê và bánh tại đường Ung Văn Khiêm, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=Yotsubato%20Cakes%20%26%20Coffee%209%20Ung%20V%C4%83n%20Khi%C3%AAm%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(19,1,'BỐI Coffee & Tea','boi-coffee-tea','Số 95 Bùi Văn Danh, Phường Mỹ Xuyên, TP. Long Xuyên, An Giang','Phường Mỹ Xuyên','Quán cà phê và trà tại đường Bùi Văn Danh, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=B%E1%BB%90I%20Coffee%20%26%20Tea%2095%20B%C3%B9i%20V%C4%83n%20Danh%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(20,1,'Méc Coffee','mec-coffee','Đường D5, KDC Tây Sông Hậu, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Quán chuyên cà phê tại KDC Tây Sông Hậu, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=M%C3%A9c%20Coffee%20M%C3%A9c%20Coffee%20T%C3%A2y%20S%C3%B4ng%20H%E1%BA%ADu%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(21,1,'Art Coffee Shop','art-coffee-shop','Số 9A Lương Văn Cù, Phường Mỹ Long, TP. Long Xuyên, An Giang','Phường Mỹ Long','Quán cà phê tại đường Lương Văn Cù, TP. Long Xuyên. Có phục vụ kèm một số món ăn.',0,NULL,NULL,'yen_tinh','07:00:00','21:00:00','https://maps.google.com/maps?q=Art%20Coffee%20Shop%209A%20L%C6%B0%C6%A1ng%20V%C4%83n%20C%C3%B9%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(22,1,'Cây Mận Cà Phê','cay-man-ca-phe','Hẻm 1 Nguyễn Du, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Quán cà phê tại đường Nguyễn Du, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=C%C3%A2y%20M%E1%BA%ADn%20C%C3%A0%20Ph%C3%AA%20C%C3%A2y%20M%E1%BA%ADn%20C%C3%A0%20Ph%C3%AA%20Nguy%E1%BB%85n%20Du%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(23,1,'Cà phê Nhà 57','ca-phe-nha-57','Số 57/1 Cô Bắc, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Quán cà phê tại đường Cô Bắc, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=C%C3%A0%20ph%C3%AA%20Nh%C3%A0%2057%2057/1%20C%C3%B4%20B%E1%BA%AFc%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(24,1,'Café Đại Lộc','cafe-dai-loc','Số 96 Lê Lợi, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Quán cà phê tại đường Lê Lợi, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','21:00:00','https://maps.google.com/maps?q=Caf%C3%A9%20%C4%90%E1%BA%A1i%20L%E1%BB%99c%2096%20L%C3%AA%20L%E1%BB%A3i%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(25,1,'Indochine Cafe','indochine-cafe','Số 57-58 Nguyễn Văn Linh, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Quán cà phê tại đường Nguyễn Văn Linh, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=Indochine%20Cafe%2057%20Nguy%E1%BB%85n%20V%C4%83n%20Linh%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(26,1,'Cafe Thảo Mộc','cafe-thao-moc','Số 3/6 Tôn Đức Thắng, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Quán cà phê tại đường Tôn Đức Thắng, TP. Long Xuyên. Có thêm chi nhánh tại 92 Tôn Đức Thắng.',0,NULL,NULL,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=Cafe%20Th%E1%BA%A3o%20M%E1%BB%99c%20Th%E1%BA%A3o%20M%E1%BB%99c%20Modern%20T%C3%B4n%20%C4%90%E1%BB%A9c%20Th%E1%BA%AFng%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',4.00,610,'https://maps.google.com/maps?q=Cafe%20Th%E1%BA%A3o%20M%E1%BB%99c%20Th%E1%BA%A3o%20M%E1%BB%99c%20Modern%20T%C3%B4n%20%C4%90%E1%BB%A9c%20Th%E1%BA%AFng%20Long%20Xuy%C3%AAn'),(27,1,'Lang Thang An Giang Cafe','lang-thang-an-giang-cafe','Số 3E3 Lý Thái Tổ, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Quán cà phê vintage tại đường Lý Thái Tổ, TP. Long Xuyên. Có đêm acoustic tối thứ 3 hằng tuần.',2,15000,30000,'yen_tinh','07:00:00','22:00:00','https://maps.google.com/maps?q=Lang%20Thang%20An%20Giang%20Cafe%203E3%20L%C3%BD%20Th%C3%A1i%20T%E1%BB%95%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(28,1,'Coffee Y5','coffee-y5','G1-G2 Lý Thái Tổ, Phường Mỹ Long, TP. Long Xuyên, An Giang','Phường Mỹ Long','Quán cà phê tại đường Lý Thái Tổ, gần Phà An Hòa, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','21:30:00','https://maps.google.com/maps?q=Coffee%20Y5%20Coffee%20Y5%20L%C3%BD%20Th%C3%A1i%20T%E1%BB%95%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(29,1,'Windows Coffee','windows-coffee','Số 1 Đường Số 8, Diamond City, TP. Long Xuyên, An Giang','Long Xuyên','Quán cà phê tại khu Diamond City, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=Windows%20Coffee%20Windows%20Coffee%20Diamond%20City%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(30,1,'Golden Coffee','golden-coffee','Số 30 Trần Hưng Đạo, TP. Long Xuyên, An Giang','Long Xuyên','Quán cà phê tại đường Trần Hưng Đạo, gần tượng đài Bác Tôn, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=Golden%20Coffee%20Golden%20Coffee%20Tr%E1%BA%A7n%20H%C6%B0ng%20%C4%90%E1%BA%A1o%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(31,4,'Trà Sữa Mayzi','tra-sua-mayzi','Số 1116 Trần Hưng Đạo, Phường Mỹ Bình, TP. Long Xuyên, An Giang','Phường Mỹ Bình','Quán trà sữa tại đường Trần Hưng Đạo, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=Tr%C3%A0%20S%E1%BB%AFa%20Mayzi%20Tr%C3%A0%20S%E1%BB%AFa%20Mayzi%20Tr%E1%BA%A7n%20H%C6%B0ng%20%C4%90%E1%BA%A1o%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(32,4,'Tiệm Nhà Tô Coffee & Matcha','tiem-nha-to-coffee-matcha','Số 29 Thành Thái, Phường Bình Khánh, TP. Long Xuyên, An Giang','Phường Bình Khánh','Quán cà phê và matcha tại đường Thành Thái, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=Ti%E1%BB%87m%20Nh%C3%A0%20T%C3%B4%20Coffee%20%26%20Matcha%2029%20Th%C3%A0nh%20Th%C3%A1i%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(33,1,'Cũ Café','cu-cafe','Hẻm 9 Trần Hưng Đạo, Phường Mỹ Xuyên, TP. Long Xuyên, An Giang','Phường Mỹ Xuyên','Quán cà phê tại hẻm 9 Trần Hưng Đạo, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','07:00:00','22:30:00','https://maps.google.com/maps?q=C%C5%A9%20Caf%C3%A9%20C%C5%A9%20Caf%C3%A9%20Tr%E1%BA%A7n%20H%C6%B0ng%20%C4%90%E1%BA%A1o%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(34,1,'Hương Vị Quê Nhà Coffee','huong-vi-que-nha-coffee','Số 51 Rạch Cái Sơn, Phường Mỹ Phước, TP. Long Xuyên, An Giang','Phường Mỹ Phước','Quán cà phê tại đường Rạch Cái Sơn, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','05:30:00','22:00:00','https://maps.google.com/maps?q=H%C6%B0%C6%A1ng%20V%E1%BB%8B%20Qu%C3%AA%20Nh%C3%A0%20Coffee%2051%20R%E1%BA%A1ch%20C%C3%A1i%20S%C6%A1n%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(35,1,'Trung Nguyên E-Coffee - Fam Garden','trung-nguyen-e-coffee-fam-garden','Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Quán cà phê nhượng quyền Trung Nguyên E-Coffee tại đường Ung Văn Khiêm, gần cổng ĐHAG mới, TP. Long Xuyên.',0,NULL,NULL,'yen_tinh','06:00:00','22:00:00','https://maps.google.com/maps?q=Trung%20Nguy%C3%AAn%20E-Coffee%20-%20Fam%20Garden%20Trung%20Nguy%C3%AAn%20E-Coffee%20Ung%20V%C4%83n%20Khi%C3%AAm%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 20:25:28',NULL,NULL,NULL),(36,1,'La Kaffé','la-kaffe','Số 12 Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang','Phường Đông Xuyên','Quán cà phê trên đường Ung Văn Khiêm, TP. Long Xuyên. Không gian rộng rãi, phù hợp sinh viên, cặp đôi, gia đình và nhóm bạn.',2,12000,35000,'yen_tinh','07:00:00','22:00:00','https://maps.google.com/maps?q=La%20Kaff%C3%A9%2012%20Ung%20V%C4%83n%20Khi%C3%AAm%20Long%20Xuy%C3%AAn',0.00,0,'active',NULL,'2026-11-03 21:06:32',NULL,NULL,NULL),(37,1,'LÀ CAFE – LÀ GAME','la-cafe-la-game','Đường Nguyễn Văn Linh, TP. Long Xuyên, An Giang','Long Xuyên','Quán cà phê kết hợp game, sách và không gian nghỉ ngơi tại TP. Long Xuyên. Có khu gaming máy cấu hình cao, tường sách nhiều tầng để check-in và sleep pod. Quán vừa tái khai trương với diện mạo mới sau 8 năm, từng tổ chức giải đấu Liên Minh Huyền Thoại. Mở cửa 24/24.',0,NULL,NULL,'yen_tinh','00:00:00','23:59:59','https://maps.google.com/maps?q=L%C3%80%20CAFE%20L%C3%80%20GAME%20Long%20Xuy%C3%AAn',0.00,0,'hidden',NULL,'2026-11-03 21:12:15',NULL,NULL,NULL);
/*!40000 ALTER TABLE `places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `place_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating_overall` tinyint(3) unsigned NOT NULL COMMENT 'Điểm tổng thể 1-5 sao',
  `rating_wifi` tinyint(3) unsigned NOT NULL DEFAULT 5 COMMENT 'Điểm tốc độ Wifi 1-5 sao',
  `rating_space` tinyint(3) unsigned NOT NULL DEFAULT 5 COMMENT 'Điểm không gian & bàn ghế 1-5 sao',
  `rating_price` tinyint(3) unsigned NOT NULL DEFAULT 5 COMMENT 'Điểm mức giá hợp lý 1-5 sao',
  `content` text NOT NULL COMMENT 'Nội dung nhận xét chi tiết',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Ảnh chụp thực tế đính kèm (tùy chọn)',
  `status` enum('pending','approved','hidden') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái kiểm duyệt',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reviews_place_user` (`place_id`,`user_id`),
  KEY `idx_reviews_status` (`status`),
  KEY `idx_reviews_user` (`user_id`),
  KEY `idx_reviews_created_at` (`created_at`),
  CONSTRAINT `fk_reviews_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,2,5,5,5,5,'Quán ruột của tụi mình bên khoa CNTT ĐH An Giang! Tan ca học ở khu giảng đường đi bộ qua đường Ung Văn Khiêm vài bước là tới NHÀ KHO. Bàn gỗ rộng để vừa 4 cái laptop, ổ cắm điện ngay tầm tay, Wifi kéo source code và xem bài giảng mượt cực kỳ.',NULL,'approved','2026-06-12 14:20:00'),(2,1,3,5,5,5,4,'Không gian ở NHÀ KHO Coffee rất hợp để ngồi giải đề TOEIC và làm đồ án môn Công nghệ Web. Nước uống pha ngon, giá tầm 30k-40k mà ngồi máy lạnh mát rượi từ trưa tới tối không bị thúc giục.',NULL,'approved','2026-06-25 09:40:00'),(3,2,4,5,5,5,5,'Cồ-Lá Coffee trên đường Ung Văn Khiêm đúng chuẩn góc trốn ồn ào cho sinh viên AGU. Cây xanh nhiều, ánh sáng dịu mắt, ly bạc xỉu đá chỉ 25k siêu hạt dẻ cho túi tiền sinh viên cuối tháng.',NULL,'approved','2026-07-05 15:10:00'),(4,2,5,4,5,4,5,'Quán gần trường nên trưa nào học 2 ca liên tiếp mình cũng ghé Cồ-Lá nghỉ trưa rồi ôn bài luôn. Các bạn nhân viên toàn là sinh viên làm part-time nên dễ thương và nhiệt tình lắm.',NULL,'approved','2026-07-18 13:05:00'),(5,3,3,5,5,5,4,'Bánh ngọt ở Mia Maia Lý Thái Tổ ngon xuất sắc! Nhóm mình 5 đứa bên khoa Kinh tế - Quản trị kinh doanh AGU hay hẹn ở đây làm slide thuyết trình, phòng lạnh thơm mùi bơ sữa dễ chịu vô cùng.',NULL,'approved','2026-07-29 16:30:00'),(6,4,2,5,5,5,5,'Ai cần sự yên tĩnh tuyệt đối để viết khóa luận tốt nghiệp thì cứ chui vào Hẻm 3 Trần Hưng Đạo ghé Cafe 1985 nhé. Không gian vintage hoài niệm, nhạc nhẹ nhàng, mọi người vào quán đều tự giác giữ trật tự.',NULL,'approved','2026-08-04 10:15:00'),(7,4,5,5,4,5,5,'Góc ngồi cạnh cửa sổ ở Cafe 1985 in An Giang ánh sáng rất đẹp. Wifi ổn định, ổ điện có sẵn dưới chân bàn, cà phê sữa pha đậm đà đúng gu miền Tây.',NULL,'approved','2026-08-14 14:50:00'),(8,5,4,4,4,5,4,'Panda Cafe ở hẻm 9/9 Trần Hưng Đạo có khu sân vườn trên cao thoáng gió cực kỳ. Buổi chiều ngồi học nhóm xong ngắm hoàng hôn Long Xuyên rất chill, giữ xe dưới tầng trệt rộng rãi có chú bảo vệ dắt phụ.',NULL,'approved','2026-08-22 17:20:00'),(9,6,2,5,5,5,5,'Mo Đen Café bên Bùi Văn Danh đúng là cứu tinh mùa chạy deadline đồ án! Quán mở tới 22h30 đêm, yên tĩnh, ổ điện nhiều, tụi mình ngồi debug PHP tới lúc quán đóng cửa vẫn được anh chủ hỗ trợ nhiệt tình.',NULL,'approved','2026-09-02 21:10:00'),(10,7,3,5,4,5,5,'Quán Bình Yên trong hẻm 181 Võ Thị Sáu đúng như cái tên, yên ắng và mát mẻ lắm. Sinh viên Sư phạm tụi mình hay ra đây soạn giáo án và đọc sách chuyên ngành, giá nước siêu rẻ.',NULL,'approved','2026-09-11 09:30:00'),(11,8,4,4,3,4,5,'Sáng nào trước khi vào tiết 1 ở AGU cũng ghé Cà phê vợt Hai Ngầu bên số 9 Bùi Văn Danh làm một ly cà phê sữa nóng. Giá bình dân chỉ mười mấy ngàn mà thơm nức mũi, đúng chất Long Xuyên xưa.',NULL,'approved','2026-09-19 06:45:00'),(12,9,5,5,5,5,4,'Đĩa Than Coffee bên KDC Tây Sông Hậu (Phường Mỹ Quý) bàn gỗ siêu rộng, để laptop 16 inch với đống tài liệu vẫn dư chỗ. Nhạc đĩa than nhẹ nhàng, không gian lịch sự cực hợp để học sâu.',NULL,'approved','2026-09-27 15:00:00'),(13,10,2,5,5,5,5,'Ở Long Xuyên hiếm quán nào mở tới 23h đêm mà chuẩn gu học bài như Pi Kafé bên đường 5G Mỹ Quý. Đèn sáng rõ không bị tối mờ, Wifi 5GHz căng đét, ổ cắm điện từng bàn luôn.',NULL,'approved','2026-10-03 21:40:00'),(14,11,3,4,5,4,5,'Cafe Lá ngay ngã 4 Ung Văn Khiêm - Lý Thái Tổ quá tiện cho sinh viên AGU tụi mình. Chiều tan học ghé uống ly trà đào cam sả rồi ngồi gõ báo cáo thực tập tới tối mới về nhà trọ.',NULL,'approved','2026-10-09 17:15:00'),(19,16,4,5,5,5,4,'LangThang Coffee bên Phường Mỹ Bình yên tĩnh, bàn ghế ngồi lâu không mỏi lưng. Cuối tuần mình hay chạy từ Đông Xuyên qua đây đổi gió ngồi ôn thi.',NULL,'approved','2026-10-30 14:10:00'),(20,17,5,4,5,4,5,'Tiệm Trà Tháng Tư ở đường Hàm Nghi, Phường Bình Khánh tiện cho mấy đứa ở trọ khu Bình Khánh như mình, đỡ phải chạy xa mà vẫn có chỗ học nhóm máy lạnh mát mẻ.',NULL,'approved','2026-10-31 16:00:00'),(21,1,4,5,5,5,5,'Hôm qua nhóm mình vừa ngồi ôn thi ở NHÀ KHO Coffee tới 21h30, quán mới lắp thêm dãy ổ cắm điện mới ở khu bàn dài rất tiện cho sinh viên AGU.',NULL,'approved','2026-11-01 10:20:00'),(22,3,2,5,5,5,4,'Bánh tiramisu và trà bá tước ở Mia Maia Lý Thái Tổ rất hợp để vừa nhâm nhi vừa làm bài tập nhóm. Mong quán mở thêm tới 22h30.',NULL,'approved','2026-11-01 15:45:00'),(23,6,5,5,5,4,5,'Mo Đen Café tối qua yên tĩnh cực kỳ, mình ngồi hoàn thành xong toàn bộ phần giao diện Bootstrap cho đồ án mà không bị xao nhãng.',NULL,'approved','2026-11-01 20:10:00'),(24,9,3,5,5,5,4,'Không gian ở Đĩa Than Coffee KDC Tây Sông Hậu thực sự rất chất lượng, bàn gỗ rộng rãi và ánh sáng ấm áp.',NULL,'approved','2026-11-02 08:30:00'),(25,10,4,5,5,5,5,'Pi Kafé mở tới 23h đêm nên rất hợp cho anh em IT Đại học An Giang thức chạy đồ án cuối kỳ.',NULL,'approved','2026-11-02 09:15:00');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Họ và tên hiển thị',
  `email` varchar(150) NOT NULL COMMENT 'Email đăng nhập duy nhất',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu mã hóa bằng password_hash',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện',
  `role` enum('user','admin') NOT NULL DEFAULT 'user' COMMENT 'Vai trò người dùng',
  `status` enum('active','locked') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role_status` (`role`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Quản Trị Viên StudySpot AGU','admin@studyspot.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0901234567',NULL,'admin','active','2026-06-01 08:00:00'),(2,'Nguyễn Minh Khôi (DH23TH)','khoi.nguyen@student.edu.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0912345678',NULL,'user','active','2026-06-05 09:30:00'),(3,'Trần Thảo Vy (DH23QT)','vy.tran@student.edu.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0987654321',NULL,'user','active','2026-06-10 14:15:00'),(4,'Lê Hoàng Nam (DH22PM)','nam.le@student.edu.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0933445566',NULL,'user','active','2026-06-18 19:45:00'),(5,'Phạm Ngọc Ánh (DH24AV)','anh.pham@student.edu.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0977889900',NULL,'user','active','2026-07-02 10:20:00'),(6,'Võ Quốc Bảo (DH23KT)','bao.vo@student.edu.vn','$2y$10$Bn3yo9RyTN353VEE3GQzr.F0/x.Mlu52nxXBfeLzQWv2reVU8nVGK','0966112233',NULL,'user','locked','2026-07-05 16:00:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-04 16:47:38
