-- ============================================================
-- Migration: Timetable Student & Staff Separation & Publishing
-- Date: 2026-08-12
-- ============================================================

USE `cms`;

ALTER TABLE `timetable`
  ADD COLUMN IF NOT EXISTS `timetable_type` ENUM('STUDENT', 'STAFF') NOT NULL DEFAULT 'STUDENT',
  ADD COLUMN IF NOT EXISTS `department_id` INT(10) UNSIGNED NULL AFTER `academic_year_id`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('DRAFT', 'PUBLISHED') NOT NULL DEFAULT 'DRAFT',
  ADD COLUMN IF NOT EXISTS `created_by` INT(10) UNSIGNED NULL,
  ADD COLUMN IF NOT EXISTS `updated_by` INT(10) UNSIGNED NULL;

CREATE TABLE IF NOT EXISTS `timetable_publications` (
  `id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `timetable_type` ENUM('STUDENT', 'STAFF') NOT NULL DEFAULT 'STUDENT',
  `academic_year_id` INT(10) UNSIGNED NOT NULL,
  `section_id` INT(10) UNSIGNED NULL,
  `faculty_id` INT(10) UNSIGNED NULL,
  `status` ENUM('DRAFT', 'PUBLISHED') NOT NULL DEFAULT 'DRAFT',
  `published_at` DATETIME NULL,
  `published_by` INT(10) UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_pub_student` (`timetable_type`, `academic_year_id`, `section_id`),
  UNIQUE KEY `uq_pub_staff` (`timetable_type`, `academic_year_id`, `faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
