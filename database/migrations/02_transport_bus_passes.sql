-- ============================================================
-- TRANSPORT DIGITAL BUS PASS MIGRATION
-- ============================================================

USE `cms`;

-- 1. Drop old QR check-in table if present
DROP TABLE IF EXISTS `transport_checkins`;

-- 2. Create transport_bus_passes table
CREATE TABLE IF NOT EXISTS `transport_bus_passes` (
    `id`                INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `allocation_id`     INT UNSIGNED     NOT NULL,
    `student_id`        INT UNSIGNED     NOT NULL,
    `route_id`          INT UNSIGNED     NOT NULL,
    `vehicle_id`        INT UNSIGNED     NOT NULL,
    `payment_id`        INT UNSIGNED     NULL,
    `pass_number`       VARCHAR(40)      NOT NULL UNIQUE,
    `amount_paid`       DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
    `issue_date`        DATE             NOT NULL,
    `valid_from`        DATE             NOT NULL,
    `valid_until`       DATE             NOT NULL,
    `status`            ENUM('active', 'payment_pending', 'expired', 'suspended', 'cancelled') NOT NULL DEFAULT 'payment_pending',
    `suspended_reason`  TEXT             NULL,
    `suspended_by`      INT UNSIGNED     NULL,
    `suspended_at`      DATETIME         NULL,
    `created_at`        DATETIME         NOT NULL,
    `updated_at`        DATETIME         NULL,
    UNIQUE KEY `uk_student_alloc` (`student_id`, `allocation_id`),
    KEY `idx_pass_status` (`status`),
    KEY `idx_pass_student` (`student_id`),
    KEY `idx_pass_route` (`route_id`),
    CONSTRAINT `fk_tbp_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `transport_allocations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tbp_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tbp_route` FOREIGN KEY (`route_id`) REFERENCES `transport_routes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tbp_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
