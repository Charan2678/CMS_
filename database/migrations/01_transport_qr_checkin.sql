-- ============================================================
-- TRANSPORT BUS QR CHECK-IN MIGRATION
-- ============================================================

USE `cms`;

-- 1. Ensure vehicles table has qr_token and qr_status columns
SET @dbname = DATABASE();
SET @tablename = 'vehicles';
SET @columnname = 'qr_token';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `vehicles` ADD COLUMN `qr_token` VARCHAR(64) NULL UNIQUE AFTER `status`, ADD COLUMN `qr_status` ENUM("active", "inactive") DEFAULT "active" AFTER `qr_token`;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Create transport_checkins table
CREATE TABLE IF NOT EXISTS `transport_checkins` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id`      INT UNSIGNED     NOT NULL,
    `vehicle_id`      INT UNSIGNED     NOT NULL,
    `route_id`        INT UNSIGNED     NOT NULL,
    `checkin_date`    DATE             NOT NULL,
    `checkin_time`    TIME             NOT NULL,
    `checkin_method`  VARCHAR(20)      NOT NULL DEFAULT 'QR',
    `qr_token`        VARCHAR(64)      NOT NULL,
    `device_info`     VARCHAR(255)     NULL,
    `status`          ENUM('checked_in', 'cancelled') NOT NULL DEFAULT 'checked_in',
    `created_at`      DATETIME         NOT NULL,
    UNIQUE KEY `uk_student_date` (`student_id`, `checkin_date`),
    KEY `idx_vehicle_date` (`vehicle_id`, `checkin_date`),
    KEY `idx_route_date` (`route_id`, `checkin_date`),
    CONSTRAINT `fk_tc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tc_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tc_route` FOREIGN KEY (`route_id`) REFERENCES `transport_routes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
