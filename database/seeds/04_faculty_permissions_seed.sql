-- ============================================================
-- FACULTY MODULE PERMISSIONS & INITIAL DESIGNATIONS SEED
-- ============================================================

USE `cms`;

-- Insert Faculty Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (3, 'Faculty Management', 'faculty', NULL, 'user-check', 30, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(17, 3, 'View Faculty List', 'faculty.view', 'View faculty directory and profiles'),
(18, 3, 'Onboard Faculty', 'faculty.create', 'Add new faculty members and auto-create portal account'),
(19, 3, 'Edit Faculty Info', 'faculty.edit', 'Modify faculty profile and designations'),
(20, 3, 'Assign Subjects', 'faculty.assign_subject', 'Allocate subjects and sections to faculty'),
(21, 3, 'Assign HOD', 'faculty.assign_hod', 'Set department Head of Department')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Default Designations
INSERT INTO `designations` (`id`, `college_id`, `name`, `code`, `level`, `status`)
VALUES
(1, 1, 'Professor & HOD', 'prof_hod', 10, 1),
(2, 1, 'Professor', 'professor', 8, 1),
(3, 1, 'Associate Professor', 'assoc_prof', 6, 1),
(4, 1, 'Assistant Professor', 'asst_prof', 4, 1),
(5, 1, 'Lecturer', 'lecturer', 2, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
