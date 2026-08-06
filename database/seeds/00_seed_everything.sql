-- ============================================================
-- FULL ENTERPRISE MASTER SEED DATA
-- ============================================================

USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. College Info
INSERT INTO `colleges` (`id`, `name`, `code`, `email`, `phone`, `website`, `address`, `status`)
VALUES (1, 'Apex Institute of Technology', 'AIT', 'info@ait.edu.in', '+91 98765 43210', 'https://ait.edu.in', '100 Innovation Campus Road, Tech City', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. Academic Years
INSERT INTO `academic_years` (`id`, `college_id`, `name`, `start_date`, `end_date`, `is_current`, `status`)
VALUES
(1, 1, '2025-2026', '2025-07-01', '2026-06-30', 0, 1),
(2, 1, '2026-2027', '2026-07-01', '2027-06-30', 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_current` = VALUES(`is_current`);

-- 3. Departments
INSERT INTO `departments` (`id`, `college_id`, `name`, `code`, `status`)
VALUES
(1, 1, 'Computer Science & Engineering', 'CSE', 1),
(2, 1, 'Electronics & Communication Engineering', 'ECE', 1),
(3, 1, 'Mechanical Engineering', 'ME', 1),
(4, 1, 'Master of Business Administration', 'MBA', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. Courses
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `degree_type`, `duration_years`, `total_semesters`, `status`)
VALUES
(1, 1, 'B.Tech Computer Science & Engineering', 'BTECH-CSE', 'UG', 4, 8, 1),
(2, 2, 'B.Tech Electronics & Communication', 'BTECH-ECE', 'UG', 4, 8, 1),
(3, 3, 'B.Tech Mechanical Engineering', 'BTECH-ME', 'UG', 4, 8, 1),
(4, 4, 'Master of Business Administration', 'MBA-GEN', 'PG', 2, 4, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 5. Semesters for BTECH-CSE (Course 1) & MBA (Course 4)
INSERT INTO `semesters` (`id`, `course_id`, `number`, `name`, `status`)
VALUES
(1, 1, 1, 'Semester 1', 1),
(2, 1, 2, 'Semester 2', 1),
(3, 1, 3, 'Semester 3', 1),
(4, 1, 4, 'Semester 4', 1),
(5, 1, 5, 'Semester 5', 1),
(6, 1, 6, 'Semester 6', 1),
(7, 1, 7, 'Semester 7', 1),
(8, 1, 8, 'Semester 8', 1),
(9, 4, 1, 'Semester 1', 1),
(10, 4, 2, 'Semester 2', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 6. Sections
INSERT INTO `sections` (`id`, `semester_id`, `academic_year_id`, `name`, `max_strength`, `status`)
VALUES
(1, 1, 2, 'Section A', 60, 1),
(2, 1, 2, 'Section B', 60, 1),
(3, 3, 2, 'Section A', 60, 1),
(4, 9, 2, 'Section A', 50, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 7. Subjects
INSERT INTO `subjects` (`id`, `semester_id`, `name`, `code`, `type`, `credits`, `max_internal_marks`, `max_external_marks`, `pass_internal_marks`, `pass_external_marks`, `status`)
VALUES
(1, 1, 'Programming & Problem Solving in C', 'CS101', 'theory', 4.0, 25, 75, 10, 30, 1),
(2, 1, 'Engineering Mathematics I', 'MA101', 'theory', 4.0, 25, 75, 10, 30, 1),
(3, 1, 'C Programming Lab', 'CS101L', 'practical', 2.0, 50, 50, 20, 20, 1),
(4, 3, 'Data Structures & Algorithms', 'CS301', 'theory', 4.0, 25, 75, 10, 30, 1),
(5, 3, 'Database Management Systems', 'CS302', 'theory', 4.0, 25, 75, 10, 30, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 8. Faculty Designations
INSERT INTO `designations` (`id`, `college_id`, `name`, `code`, `level`)
VALUES
(1, 1, 'Professor', 'prof', 1),
(2, 1, 'Associate Professor', 'assoc_prof', 2),
(3, 1, 'Assistant Professor', 'asst_prof', 3),
(4, 1, 'Head of Department', 'hod', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 9. Faculty Members & Users
INSERT INTO `faculty` (`id`, `college_id`, `department_id`, `designation_id`, `employee_id`, `first_name`, `last_name`, `gender`, `mobile`, `email`, `joining_date`, `status`)
VALUES
(1, 1, 1, 4, 'EMP-FAC-001', 'Dr. Alan', 'Turing', 'male', '9876511111', 'alan.turing@ait.edu.in', '2020-01-15', 'active'),
(2, 1, 1, 3, 'EMP-FAC-002', 'Grace', 'Hopper', 'female', '9876522222', 'grace.hopper@ait.edu.in', '2021-06-01', 'active')
ON DUPLICATE KEY UPDATE `first_name` = VALUES(`first_name`);

INSERT INTO `users` (`id`, `college_id`, `username`, `email`, `password_hash`, `role_id`, `linked_type`, `linked_id`, `is_active`)
VALUES
(2, 1, 'EMP-FAC-001', 'alan.turing@ait.edu.in', '$2y$12$e2g1bCjGZ2D3k4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x', 3, 'faculty', 1, 1),
(3, 1, 'EMP-FAC-002', 'grace.hopper@ait.edu.in', '$2y$12$e2g1bCjGZ2D3k4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x', 4, 'faculty', 2, 1)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- 10. Sample Students
INSERT INTO `students` (`id`, `college_id`, `roll_number`, `admission_number`, `first_name`, `last_name`, `date_of_birth`, `gender`, `email`, `mobile`, `admission_date`, `status`)
VALUES
(1, 1, '2026-CSE-001', 'ADM-2026-001', 'John', 'Doe', '2004-05-15', 'male', 'john.doe@ait.edu.in', '9999911111', '2026-07-15', 'active'),
(2, 1, '2026-CSE-002', 'ADM-2026-002', 'Jane', 'Smith', '2004-08-20', 'female', 'jane.smith@ait.edu.in', '9999922222', '2026-07-15', 'active')
ON DUPLICATE KEY UPDATE `first_name` = VALUES(`first_name`);

INSERT INTO `student_academics` (`student_id`, `academic_year_id`, `department_id`, `course_id`, `semester_id`, `section_id`, `is_current`)
VALUES
(1, 2, 1, 1, 1, 1, 1),
(2, 2, 1, 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE `is_current` = 1;

-- 11. Role Permissions Safeguard
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 1, id, 1 FROM permissions
ON DUPLICATE KEY UPDATE granted = 1;

SET FOREIGN_KEY_CHECKS = 1;
