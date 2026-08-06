-- ============================================================
-- STAFF MODULE PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Staff Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (4, 'Non-Faculty Staff', 'staff', NULL, 'briefcase', 40, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(22, 4, 'View Staff Directory', 'staff.view', 'View non-faculty staff directory and profiles'),
(23, 4, 'Onboard Staff Member', 'staff.create', 'Add new non-faculty personnel and create portal account'),
(24, 4, 'Edit Staff Info', 'staff.edit', 'Modify non-faculty staff profile and status')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
