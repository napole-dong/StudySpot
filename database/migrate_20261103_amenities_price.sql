-- Migration 2026-11-03: amenities rieng + gia tu do (min/max VNĐ)
-- Chay 1 lan. Cac cot cu has_wifi/has_outlet/has_ac va price_range duoc giu lai
-- trong buoc chuyen doi, chi xoa sau khi code moi da duoc kiem tra xong.

CREATE TABLE IF NOT EXISTS amenities (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_amenities_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS place_amenities (
    place_id INT UNSIGNED NOT NULL,
    amenity_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (place_id, amenity_id),
    KEY idx_pa_amenity (amenity_id),
    CONSTRAINT fk_pa_place FOREIGN KEY (place_id) REFERENCES places (id) ON DELETE CASCADE,
    CONSTRAINT fk_pa_amenity FOREIGN KEY (amenity_id) REFERENCES amenities (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO amenities (id, name, slug, sort_order) VALUES
    (1, 'Wifi mạnh', 'wifi', 1),
    (2, 'Ổ cắm điện', 'outlet', 2),
    (3, 'Máy lạnh', 'ac', 3);

INSERT IGNORE INTO place_amenities (place_id, amenity_id)
    SELECT id, 1 FROM places WHERE has_wifi = 1;
INSERT IGNORE INTO place_amenities (place_id, amenity_id)
    SELECT id, 2 FROM places WHERE has_outlet = 1;
INSERT IGNORE INTO place_amenities (place_id, amenity_id)
    SELECT id, 3 FROM places WHERE has_ac = 1;

ALTER TABLE places
    ADD COLUMN IF NOT EXISTS price_min INT UNSIGNED NULL AFTER price_range,
    ADD COLUMN IF NOT EXISTS price_max INT UNSIGNED NULL AFTER price_min;

UPDATE places SET price_min = NULL, price_max = 25000 WHERE price_range = 1;
UPDATE places SET price_min = 25000, price_max = 45000 WHERE price_range = 2;
UPDATE places SET price_min = 45000, price_max = 65000 WHERE price_range = 3;
UPDATE places SET price_min = 65000, price_max = NULL WHERE price_range = 4;
