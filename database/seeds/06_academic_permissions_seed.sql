-- ============================================================
-- ACADEMIC MODULES PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Modules
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES
(5, 'Attendance', 'attendance', NULL, 'check-square', 50, 1),
(6, 'Results & Timetable', 'result', NULL, 'award', 60, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(25, 5, 'Mark Attendance', 'attendance.mark', 'Mark student daily subject attendance'),
(26, 5, 'View Attendance', 'attendance.view', 'View attendance reports and logs'),
(27, 6, 'Manage Timetable', 'timetable.manage', 'Create and modify section class timetables'),
(28, 6, 'Enter Internal Marks', 'marks.internal', 'Record CIA and assignment marks'),
(29, 6, 'Enter External Marks', 'marks.external', 'Record semester exam marks'),
(30, 6, 'Publish Results', 'result.publish', 'Calculate and publish semester results')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
