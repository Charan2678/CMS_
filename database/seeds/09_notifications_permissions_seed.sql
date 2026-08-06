-- ============================================================
-- NOTIFICATIONS & AUDIT MODULE PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (9, 'Notifications & Audit', 'settings', NULL, 'bell', 90, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(39, 9, 'Manage Announcements', 'notification.announcement', 'Create and broadcast campus announcements'),
(40, 9, 'View Audit Logs', 'audit.view', 'Inspect immutable system activity audit logs')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
