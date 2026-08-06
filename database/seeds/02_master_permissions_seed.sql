-- ============================================================
-- MASTER DATA PERMISSIONS SEED
-- Seed modules and granular permissions for Master Data management
-- ============================================================

USE `cms`;

-- Insert Master Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (1, 'Master Data', 'master', NULL, 'database', 10, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(1, 1, 'View College Info', 'master.college.view', 'View college profile information'),
(2, 1, 'Edit College Info', 'master.college.edit', 'Modify college profile information'),
(3, 1, 'View Academic Years', 'master.academicyear.view', 'View academic years list'),
(4, 1, 'Create Academic Year', 'master.academicyear.create', 'Add and update academic years'),
(5, 1, 'View Departments', 'master.department.view', 'View departments list'),
(6, 1, 'Create Department', 'master.department.create', 'Add and edit departments'),
(7, 1, 'View Courses', 'master.course.view', 'View courses and semesters'),
(8, 1, 'Create Course', 'master.course.create', 'Add courses and generate semesters'),
(9, 1, 'View Sections', 'master.section.view', 'View sections list'),
(10, 1, 'Create Section', 'master.section.create', 'Add sections'),
(11, 1, 'View Subjects', 'master.subject.view', 'View curriculum subjects'),
(12, 1, 'Create Subject', 'master.subject.create', 'Add subjects')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
