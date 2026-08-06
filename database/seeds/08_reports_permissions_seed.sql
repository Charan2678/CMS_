-- ============================================================
-- REPORTS MODULE PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (8, 'Reports & Analytics', 'reports', NULL, 'bar-chart-2', 80, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(36, 8, 'View Academic Reports', 'reports.academic', 'Access enrollment and examination pass/fail reports'),
(37, 8, 'View Financial Reports', 'reports.financial', 'Access fee collection, revenue, and pending dues reports'),
(38, 8, 'View Attendance Reports', 'reports.attendance', 'Access section attendance trends and shortage lists')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
