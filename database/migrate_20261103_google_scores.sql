-- Khoi diem Google cho trang chi tiet quan (hien thi khi co du lieu, khong bat buoc).
ALTER TABLE places ADD COLUMN google_rating DECIMAL(3,2) NULL DEFAULT NULL,
ADD COLUMN google_rating_count INT UNSIGNED NULL DEFAULT NULL,
ADD COLUMN google_maps_place_url TEXT NULL DEFAULT NULL;
