-- ============================================================
-- STUDENT MODULE PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Student Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (2, 'Student Management', 'student', NULL, 'users', 20, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(13, 2, 'View Students List', 'student.view', 'View students directory and profiles'),
(14, 2, 'Admit New Student', 'student.create', 'Execute student admission workflow'),
(15, 2, 'Edit Student Info', 'student.edit', 'Modify student personal and academic details'),
(16, 2, 'Delete / Drop Student', 'student.delete', 'Deactivate or delete student record')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
