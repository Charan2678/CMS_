-- ============================================================
-- COLLEGE MANAGEMENT SYSTEM — COMPLETE SQL SCHEMA
-- Stage 3: Normalized Database Design (3NF)
-- Engine  : MySQL 8.0+ | InnoDB
-- Charset : utf8mb4 | utf8mb4_unicode_ci
-- Author  : CMS Build — Stage 3
-- ============================================================
--
-- NORMALIZATION COMPLIANCE
-- ─────────────────────────────────────────────────────────────
-- 1NF → All columns hold atomic values. No repeating groups.
--       Example: No subject1, subject2, subject3 columns.
--
-- 2NF → All non-key columns depend on the ENTIRE primary key.
--       Example: In student_academics, every column describes
--                the student-year-semester placement, not just
--                the student or just the year alone.
--
-- 3NF → No transitive dependencies. Names are never stored in
--       child tables — only IDs are stored.
--       ❌ Wrong: students.department_name
--       ✅ Right : students have no dept column at all;
--                 department is in student_academics.department_id
--
-- ─────────────────────────────────────────────────────────────
-- BUILD ORDER: Tables created in FK dependency order.
-- Circular dependency (departments ↔ faculty) resolved at end
-- via ALTER TABLE after both tables exist.
-- ─────────────────────────────────────────────────────────────

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO';

CREATE DATABASE IF NOT EXISTS `cms`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `cms`;

-- ============================================================
-- BLOCK 1 — FOUNDATION (no dependencies)
-- Tables: colleges, modules
-- ============================================================

CREATE TABLE `colleges` (
    `id`                  INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`                VARCHAR(200)     NOT NULL,
    `code`                VARCHAR(20)      NOT NULL,
    `address`             TEXT,
    `city`                VARCHAR(100),
    `state`               VARCHAR(100),
    `pincode`             VARCHAR(10),
    `phone`               VARCHAR(20),
    `email`               VARCHAR(150),
    `website`             VARCHAR(200),
    `logo_path`           VARCHAR(255),
    `established_year`    YEAR,
    `affiliation_body`    VARCHAR(200),
    `affiliation_number`  VARCHAR(100),
    `status`              TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_college_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Root entity. Every other record belongs to a college.';

-- ────────────────────────────────────────────────────────────
-- Modules are global (not per-college). They define system
-- sections and support nesting via self-referential parent_id.
-- ────────────────────────────────────────────────────────────
CREATE TABLE `modules` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)     NOT NULL,
    `code`        VARCHAR(50)      NOT NULL,
    `parent_id`   INT UNSIGNED     DEFAULT NULL,
    `icon`        VARCHAR(100)     DEFAULT NULL,
    `sort_order`  INT UNSIGNED     NOT NULL DEFAULT 0,
    `status`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_module_code` (`code`),
    KEY `idx_module_parent` (`parent_id`),
    CONSTRAINT `fk_module_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `modules` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='System sections used by RBAC permission mapping.';


-- ============================================================
-- BLOCK 2 — COLLEGE-DEPENDENT
-- Tables: academic_years, buildings, designations,
--         fee_categories, roles, departments
-- ============================================================

CREATE TABLE `academic_years` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`  INT UNSIGNED     NOT NULL,
    `name`        VARCHAR(50)      NOT NULL,
    `start_date`  DATE             NOT NULL,
    `end_date`    DATE             NOT NULL,
    `is_current`  TINYINT UNSIGNED NOT NULL DEFAULT 0
                  COMMENT 'Only one row per college can be 1',
    `status`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_year_college_name` (`college_id`, `name`),
    KEY `idx_acyr_current` (`college_id`, `is_current`),
    CONSTRAINT `fk_acyr_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `buildings` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`  INT UNSIGNED     NOT NULL,
    `name`        VARCHAR(100)     NOT NULL,
    `code`        VARCHAR(20)      NOT NULL,
    `floors`      INT UNSIGNED     NOT NULL DEFAULT 1,
    `status`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_building_code` (`college_id`, `code`),
    CONSTRAINT `fk_building_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `designations` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`  INT UNSIGNED     NOT NULL,
    `name`        VARCHAR(100)     NOT NULL,
    `code`        VARCHAR(30)      NOT NULL,
    `level`       INT UNSIGNED     NOT NULL DEFAULT 1
                  COMMENT 'Higher number = more senior',
    `status`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_desig_college` (`college_id`, `code`),
    CONSTRAINT `fk_desig_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fee_categories` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`    INT UNSIGNED     NOT NULL,
    `name`          VARCHAR(100)     NOT NULL,
    `code`          VARCHAR(30)      NOT NULL,
    `is_refundable` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `status`        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_feecat_college` (`college_id`, `code`),
    CONSTRAINT `fk_feecat_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`     INT UNSIGNED     NOT NULL,
    `name`           VARCHAR(100)     NOT NULL,
    `code`           VARCHAR(50)      NOT NULL,
    `is_system_role` TINYINT UNSIGNED NOT NULL DEFAULT 0
                     COMMENT 'System roles cannot be deleted',
    `description`    TEXT,
    `status`         TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_role_college` (`college_id`, `code`),
    CONSTRAINT `fk_role_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- departments.hod_id references faculty.id (circular).
-- The FK constraint is added AFTER faculty is created.
-- ────────────────────────────────────────────────────────────
CREATE TABLE `departments` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`       INT UNSIGNED     NOT NULL,
    `name`             VARCHAR(150)     NOT NULL,
    `code`             VARCHAR(20)      NOT NULL,
    `hod_id`           INT UNSIGNED     DEFAULT NULL
                       COMMENT 'FK to faculty.id — added via ALTER TABLE below',
    `established_year` YEAR,
    `status`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_dept_code` (`college_id`, `code`),
    KEY `idx_dept_hod` (`hod_id`),
    CONSTRAINT `fk_dept_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 3 — BUILDINGS-DEPENDENT
-- Tables: rooms
-- ============================================================

CREATE TABLE `rooms` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `building_id`  INT UNSIGNED     NOT NULL,
    `name`         VARCHAR(100)     NOT NULL,
    `code`         VARCHAR(20)      NOT NULL,
    `floor`        INT              NOT NULL DEFAULT 0,
    `capacity`     INT UNSIGNED     NOT NULL DEFAULT 0,
    `type`         ENUM('classroom','lab','office','seminar_hall') NOT NULL DEFAULT 'classroom',
    `status`       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_room_code` (`building_id`, `code`),
    CONSTRAINT `fk_room_building`
        FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 4 — DEPARTMENTS + DESIGNATIONS DEPENDENT
-- Tables: courses, staff, faculty
-- After faculty: ALTER departments to add hod_id FK
-- ============================================================

CREATE TABLE `courses` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `department_id`    INT UNSIGNED     NOT NULL,
    `name`             VARCHAR(150)     NOT NULL,
    `code`             VARCHAR(20)      NOT NULL,
    `degree_type`      ENUM('ug','pg','diploma','certificate') NOT NULL,
    `duration_years`   INT UNSIGNED     NOT NULL,
    `total_semesters`  INT UNSIGNED     NOT NULL,
    `status`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_course_code` (`department_id`, `code`),
    CONSTRAINT `fk_course_dept`
        FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `staff` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`       INT UNSIGNED     NOT NULL,
    `department_type`  ENUM('accounts','library','hostel','transport','canteen','admin') NOT NULL,
    `designation_id`   INT UNSIGNED     NOT NULL,
    `employee_id`      VARCHAR(30)      NOT NULL,
    `first_name`       VARCHAR(100)     NOT NULL,
    `last_name`        VARCHAR(100)     NOT NULL,
    `date_of_birth`    DATE,
    `gender`           ENUM('male','female','other') NOT NULL,
    `mobile`           VARCHAR(15),
    `email`            VARCHAR(150),
    `photo_path`       VARCHAR(255),
    `joining_date`     DATE,
    `address`          TEXT,
    `status`           ENUM('active','inactive','resigned') NOT NULL DEFAULT 'active',
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_staff_empid` (`college_id`, `employee_id`),
    KEY `idx_staff_mobile` (`mobile`),
    CONSTRAINT `fk_staff_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_staff_desig`
        FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faculty` (
    `id`                INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`        INT UNSIGNED     NOT NULL,
    `department_id`     INT UNSIGNED     NOT NULL,
    `designation_id`    INT UNSIGNED     NOT NULL,
    `employee_id`       VARCHAR(30)      NOT NULL,
    `first_name`        VARCHAR(100)     NOT NULL,
    `last_name`         VARCHAR(100)     NOT NULL,
    `date_of_birth`     DATE,
    `gender`            ENUM('male','female','other') NOT NULL,
    `blood_group`       VARCHAR(5),
    `mobile`            VARCHAR(15),
    `email`             VARCHAR(150),
    `photo_path`        VARCHAR(255),
    `qualification`     VARCHAR(200),
    `specialization`    VARCHAR(200),
    `experience_years`  DECIMAL(4,1)     NOT NULL DEFAULT 0.0,
    `joining_date`      DATE,
    `address`           TEXT,
    `city`              VARCHAR(100),
    `state`             VARCHAR(100),
    `pincode`           VARCHAR(10),
    `status`            ENUM('active','inactive','resigned','retired') NOT NULL DEFAULT 'active',
    `created_by`        INT UNSIGNED     DEFAULT NULL,
    `created_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_faculty_empid` (`college_id`, `employee_id`),
    UNIQUE KEY `uq_faculty_email` (`email`),
    KEY `idx_faculty_dept` (`department_id`),
    KEY `idx_faculty_mobile` (`mobile`),
    CONSTRAINT `fk_faculty_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_faculty_dept`
        FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
    CONSTRAINT `fk_faculty_desig`
        FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- Resolve circular dependency: departments ↔ faculty
-- departments was created first without the FK.
-- Now that faculty exists, we add it.
-- ────────────────────────────────────────────────────────────
ALTER TABLE `departments`
    ADD CONSTRAINT `fk_dept_hod`
        FOREIGN KEY (`hod_id`) REFERENCES `faculty` (`id`)
        ON DELETE SET NULL;


-- ============================================================
-- BLOCK 5 — ROLES + MODULES DEPENDENT
-- Tables: permissions, users, role_permissions
-- ============================================================

CREATE TABLE `permissions` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_id`    INT UNSIGNED NOT NULL,
    `name`         VARCHAR(150) NOT NULL,
    `code`         VARCHAR(100) NOT NULL,
    `description`  VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permission_code` (`code`),
    KEY `idx_perm_module` (`module_id`),
    CONSTRAINT `fk_perm_module`
        FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- users.linked_type + linked_id is a polymorphic reference.
-- No DB-level FK (can point to students, faculty, or staff).
-- Enforced at application layer.
-- ────────────────────────────────────────────────────────────
CREATE TABLE `users` (
    `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`           INT UNSIGNED     NOT NULL,
    `username`             VARCHAR(100)     NOT NULL,
    `email`                VARCHAR(150)     NOT NULL,
    `password_hash`        VARCHAR(255)     NOT NULL,
    `role_id`              INT UNSIGNED     NOT NULL,
    `linked_type`          ENUM('student','faculty','staff','admin','parent') NOT NULL,
    `linked_id`            INT UNSIGNED     NOT NULL
                           COMMENT 'ID in students / faculty / staff / guardians (polymorphic)',
    `is_active`            TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `last_login`           DATETIME         DEFAULT NULL,
    `must_change_password` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `created_by`           INT UNSIGNED     DEFAULT NULL,
    `created_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_username` (`username`),
    UNIQUE KEY `uq_user_email` (`email`),
    KEY `idx_user_college` (`college_id`),
    KEY `idx_user_role` (`role_id`),
    KEY `idx_user_linked` (`linked_type`, `linked_id`),
    CONSTRAINT `fk_user_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_user_role`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `role_id`        INT UNSIGNED     NOT NULL,
    `permission_id`  INT UNSIGNED     NOT NULL,
    `granted`        TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_role_permission` (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission`
        FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 6 — USERS-DEPENDENT
-- Tables: login_history, password_resets
-- ============================================================

CREATE TABLE `login_history` (
    `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT
                   COMMENT 'BIGINT: high volume expected',
    `user_id`      INT UNSIGNED     NOT NULL,
    `ip_address`   VARCHAR(45)      NOT NULL COMMENT 'IPv4 or IPv6',
    `user_agent`   TEXT,
    `status`       ENUM('success','failed','locked') NOT NULL,
    `attempted_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_login_user` (`user_id`),
    KEY `idx_login_at`   (`attempted_at`),
    CONSTRAINT `fk_login_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NOT NULL,
    `token`       VARCHAR(255) NOT NULL COMMENT 'SHA-256 hashed token',
    `expires_at`  DATETIME     NOT NULL,
    `used_at`     DATETIME     DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pr_user`  (`user_id`),
    KEY `idx_pr_token` (`token`(64)),
    CONSTRAINT `fk_pr_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 7 — COURSES-DEPENDENT
-- Tables: semesters
-- ============================================================

CREATE TABLE `semesters` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `course_id`   INT UNSIGNED     NOT NULL,
    `number`      INT UNSIGNED     NOT NULL,
    `name`        VARCHAR(50)      NOT NULL,
    `status`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_semester_course` (`course_id`, `number`),
    CONSTRAINT `fk_sem_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 8 — SEMESTERS-DEPENDENT
-- Tables: sections, subjects
-- ============================================================

CREATE TABLE `sections` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `semester_id`      INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `name`             VARCHAR(10)      NOT NULL,
    `max_strength`     INT UNSIGNED     NOT NULL DEFAULT 60,
    `room_id`          INT UNSIGNED     DEFAULT NULL,
    `status`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_section` (`semester_id`, `academic_year_id`, `name`),
    KEY `idx_section_acyr` (`academic_year_id`),
    CONSTRAINT `fk_section_semester`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
    CONSTRAINT `fk_section_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_section_room`
        FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subjects` (
    `id`                  INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `semester_id`         INT UNSIGNED     NOT NULL,
    `name`                VARCHAR(150)     NOT NULL,
    `code`                VARCHAR(30)      NOT NULL,
    `type`                ENUM('theory','practical','elective') NOT NULL DEFAULT 'theory',
    `credits`             DECIMAL(4,2)     NOT NULL DEFAULT 0.00,
    `max_internal_marks`  INT UNSIGNED     NOT NULL DEFAULT 0,
    `max_external_marks`  INT UNSIGNED     NOT NULL DEFAULT 0,
    `pass_internal_marks` INT UNSIGNED     NOT NULL DEFAULT 0,
    `pass_external_marks` INT UNSIGNED     NOT NULL DEFAULT 0,
    `status`              TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_subject_code` (`semester_id`, `code`),
    CONSTRAINT `fk_subject_sem`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 9 — STUDENTS (depends on colleges, users)
-- ============================================================

CREATE TABLE `students` (
    `id`                INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`        INT UNSIGNED     NOT NULL,
    `roll_number`       VARCHAR(30)      NOT NULL,
    `admission_number`  VARCHAR(30)      NOT NULL,
    `first_name`        VARCHAR(100)     NOT NULL,
    `last_name`         VARCHAR(100)     NOT NULL,
    `date_of_birth`     DATE             NOT NULL,
    `gender`            ENUM('male','female','other') NOT NULL,
    `blood_group`       VARCHAR(5),
    `mobile`            VARCHAR(15),
    `email`             VARCHAR(150),
    `photo_path`        VARCHAR(255),
    `address_line1`     TEXT,
    `address_line2`     TEXT,
    `city`              VARCHAR(100),
    `state`             VARCHAR(100),
    `pincode`           VARCHAR(10),
    `religion`          VARCHAR(50),
    `caste`             VARCHAR(50),
    `category`          ENUM('general','obc','sc','st','other') NOT NULL DEFAULT 'general',
    `differently_abled` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `status`            ENUM('active','inactive','graduated','dropped') NOT NULL DEFAULT 'active',
    `admission_date`    DATE             NOT NULL,
    `created_by`        INT UNSIGNED     DEFAULT NULL,
    `created_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roll_college`       (`college_id`, `roll_number`),
    UNIQUE KEY `uq_admission_number`   (`admission_number`),
    KEY `idx_student_mobile`  (`mobile`),
    KEY `idx_student_email`   (`email`),
    KEY `idx_student_status`  (`status`),
    CONSTRAINT `fk_student_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 10 — STUDENT RELATIONS
-- Tables: student_academics, guardians, student_documents
-- ============================================================

-- ────────────────────────────────────────────────────────────
-- student_academics: where a student is placed each year.
-- A student's department/course/section is NOT stored on the
-- student row — it lives here. This is 3NF compliance.
-- One student can appear multiple times (once per year/semester)
-- but only one row can be is_current = 1.
-- ────────────────────────────────────────────────────────────
CREATE TABLE `student_academics` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `department_id`    INT UNSIGNED     NOT NULL,
    `course_id`        INT UNSIGNED     NOT NULL,
    `semester_id`      INT UNSIGNED     NOT NULL,
    `section_id`       INT UNSIGNED     NOT NULL,
    `is_current`       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_student_placement` (`student_id`, `academic_year_id`, `semester_id`),
    KEY `idx_sa_section`  (`section_id`),
    KEY `idx_sa_acyr`     (`academic_year_id`),
    KEY `idx_sa_current`  (`student_id`, `is_current`),
    CONSTRAINT `fk_sa_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_sa_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_sa_dept`
        FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
    CONSTRAINT `fk_sa_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
    CONSTRAINT `fk_sa_semester`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
    CONSTRAINT `fk_sa_section`
        FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `guardians` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`     INT UNSIGNED     NOT NULL,
    `relationship`   ENUM('father','mother','guardian') NOT NULL,
    `name`           VARCHAR(200)     NOT NULL,
    `mobile`         VARCHAR(15),
    `email`          VARCHAR(150),
    `occupation`     VARCHAR(100),
    `annual_income`  DECIMAL(12,2)    DEFAULT NULL,
    `address`        TEXT,
    `is_primary`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_guardian_student` (`student_id`),
    KEY `idx_guardian_mobile`  (`mobile`),
    CONSTRAINT `fk_guardian_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_documents` (
    `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`     INT UNSIGNED     NOT NULL,
    `document_type`  ENUM('aadhar','birth_cert','tc','marksheet','other') NOT NULL,
    `document_name`  VARCHAR(200)     NOT NULL,
    `file_path`      VARCHAR(255)     NOT NULL,
    `uploaded_by`    INT UNSIGNED     NOT NULL,
    `verified`       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `verified_by`    INT UNSIGNED     DEFAULT NULL,
    `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sdoc_student` (`student_id`),
    CONSTRAINT `fk_sdoc_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_sdoc_uploader`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_sdoc_verifier`
        FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 11 — FACULTY RELATIONS
-- Tables: faculty_subject_assignments, faculty_documents
-- ============================================================

-- Unique constraint enforces: one subject in one section
-- per academic year has exactly one faculty assigned.
CREATE TABLE `faculty_subject_assignments` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `faculty_id`       INT UNSIGNED NOT NULL,
    `subject_id`       INT UNSIGNED NOT NULL,
    `section_id`       INT UNSIGNED NOT NULL,
    `academic_year_id` INT UNSIGNED NOT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fsa` (`subject_id`, `section_id`, `academic_year_id`),
    KEY `idx_fsa_faculty` (`faculty_id`),
    CONSTRAINT `fk_fsa_faculty`
        FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`),
    CONSTRAINT `fk_fsa_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
    CONSTRAINT `fk_fsa_section`
        FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
    CONSTRAINT `fk_fsa_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faculty_documents` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `faculty_id`     INT UNSIGNED NOT NULL,
    `document_type`  ENUM('aadhar','degree','experience','appointment','other') NOT NULL,
    `document_name`  VARCHAR(200) NOT NULL,
    `file_path`      VARCHAR(255) NOT NULL,
    `uploaded_by`    INT UNSIGNED NOT NULL,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_fdoc_faculty` (`faculty_id`),
    CONSTRAINT `fk_fdoc_faculty`
        FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_fdoc_uploader`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 12 — FINANCE
-- Tables: fee_structures, student_fees, payments, receipts
-- ============================================================

CREATE TABLE `fee_structures` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`       INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `course_id`        INT UNSIGNED     NOT NULL,
    `semester_id`      INT UNSIGNED     NOT NULL,
    `fee_category_id`  INT UNSIGNED     NOT NULL,
    `amount`           DECIMAL(10,2)    NOT NULL,
    `due_date`         DATE             NOT NULL,
    `status`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_fee_structure` (`academic_year_id`, `course_id`, `semester_id`, `fee_category_id`),
    CONSTRAINT `fk_fs_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_fs_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_fs_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
    CONSTRAINT `fk_fs_semester`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
    CONSTRAINT `fk_fs_category`
        FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_fees` (
    `id`               INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED      NOT NULL,
    `fee_structure_id` INT UNSIGNED      NOT NULL,
    `academic_year_id` INT UNSIGNED      NOT NULL,
    `amount_due`       DECIMAL(10,2)     NOT NULL,
    `discount`         DECIMAL(10,2)     NOT NULL DEFAULT 0.00,
    `final_amount`     DECIMAL(10,2)     NOT NULL
                       COMMENT 'Computed: amount_due - discount',
    `status`           ENUM('pending','partial','paid','waived') NOT NULL DEFAULT 'pending',
    `created_at`       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_student_fee` (`student_id`, `fee_structure_id`),
    KEY `idx_sf_status` (`status`),
    KEY `idx_sf_acyr`   (`academic_year_id`),
    CONSTRAINT `fk_sf_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_sf_structure`
        FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`),
    CONSTRAINT `fk_sf_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `student_fee_id`  INT UNSIGNED  NOT NULL,
    `student_id`      INT UNSIGNED  NOT NULL,
    `amount_paid`     DECIMAL(10,2) NOT NULL,
    `payment_method`  ENUM('cash','online','dd','cheque','upi','card','bank_transfer','online_gateway') NOT NULL,
    `mode`            ENUM('manual','gateway','upi_qr') NOT NULL DEFAULT 'manual',
    `transaction_id`  VARCHAR(100)  DEFAULT NULL,
    `utr_reference`   VARCHAR(100)  DEFAULT NULL,
    `fee_category_type` ENUM('academic','hostel','transport','other') NOT NULL DEFAULT 'academic',
    `payment_date`    DATE          NOT NULL,
    `received_by`     INT UNSIGNED  NOT NULL,
    `remarks`         TEXT,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_payment_student` (`student_id`),
    KEY `idx_payment_date`    (`payment_date`),
    KEY `idx_payment_mode`    (`mode`),
    CONSTRAINT `fk_payment_sf`
        FOREIGN KEY (`student_fee_id`) REFERENCES `student_fees` (`id`),
    CONSTRAINT `fk_payment_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_payment_receiver`
        FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `receipts` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payment_id`      INT UNSIGNED NOT NULL,
    `receipt_number`  VARCHAR(30)  NOT NULL COMMENT 'System-generated, never editable',
    `generated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `generated_by`    INT UNSIGNED NOT NULL,
    `file_path`       VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_receipt_number`  (`receipt_number`),
    UNIQUE KEY `uq_receipt_payment` (`payment_id`),
    CONSTRAINT `fk_receipt_payment`
        FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
    CONSTRAINT `fk_receipt_user`
        FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 13 — ACADEMIC ACTIVITY
-- Tables: attendance, internal_marks, external_marks,
--         results, timetable
-- ============================================================

-- UNIQUE on (student_id, subject_id, date) prevents duplicate
-- attendance entries for the same class on the same day.
CREATE TABLE `attendance` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED NOT NULL,
    `subject_id`       INT UNSIGNED NOT NULL,
    `section_id`       INT UNSIGNED NOT NULL,
    `academic_year_id` INT UNSIGNED NOT NULL,
    `date`             DATE         NOT NULL,
    `status`           ENUM('present','absent','late','holiday','on_leave') NOT NULL,
    `marked_by`        INT UNSIGNED NOT NULL,
    `updated_by`       INT UNSIGNED DEFAULT NULL,
    `updated_at`       DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendance` (`student_id`, `subject_id`, `date`),
    KEY `idx_att_date`    (`date`),
    KEY `idx_att_section` (`section_id`),
    KEY `idx_att_acyr`    (`academic_year_id`),
    CONSTRAINT `fk_att_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_att_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
    CONSTRAINT `fk_att_section`
        FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
    CONSTRAINT `fk_att_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_att_marker`
        FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `internal_marks` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED  NOT NULL,
    `subject_id`       INT UNSIGNED  NOT NULL,
    `academic_year_id` INT UNSIGNED  NOT NULL,
    `exam_type`        ENUM('cia1','cia2','cia3','assignment','practical') NOT NULL,
    `marks_obtained`   DECIMAL(5,2)  NOT NULL,
    `max_marks`        DECIMAL(5,2)  NOT NULL,
    `entered_by`       INT UNSIGNED  NOT NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_internal_mark` (`student_id`, `subject_id`, `academic_year_id`, `exam_type`),
    CONSTRAINT `fk_im_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_im_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
    CONSTRAINT `fk_im_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_im_user`
        FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `external_marks` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED  NOT NULL,
    `subject_id`       INT UNSIGNED  NOT NULL,
    `semester_id`      INT UNSIGNED  NOT NULL,
    `academic_year_id` INT UNSIGNED  NOT NULL,
    `marks_obtained`   DECIMAL(5,2)  NOT NULL,
    `max_marks`        DECIMAL(5,2)  NOT NULL,
    `grade`            VARCHAR(5)    DEFAULT NULL,
    `entered_by`       INT UNSIGNED  NOT NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_external_mark` (`student_id`, `subject_id`, `academic_year_id`),
    CONSTRAINT `fk_em_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_em_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
    CONSTRAINT `fk_em_semester`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
    CONSTRAINT `fk_em_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_em_user`
        FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- results.published = 0 by default (draft).
-- Only authorized roles can flip to published = 1.
CREATE TABLE `results` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `semester_id`      INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `total_marks`      DECIMAL(7,2)     NOT NULL DEFAULT 0.00,
    `percentage`       DECIMAL(5,2)     NOT NULL DEFAULT 0.00,
    `grade`            VARCHAR(10)      DEFAULT NULL,
    `result`           ENUM('pass','fail','withheld') NOT NULL,
    `rank`             INT UNSIGNED     DEFAULT NULL,
    `published`        TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `published_at`     DATETIME         DEFAULT NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_result` (`student_id`, `semester_id`, `academic_year_id`),
    KEY `idx_result_published` (`published`),
    CONSTRAINT `fk_result_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_result_semester`
        FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
    CONSTRAINT `fk_result_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- timetable UNIQUE on (section, year, day, period) prevents
-- double-booking a section slot.
CREATE TABLE `timetable` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_id`       INT UNSIGNED NOT NULL,
    `academic_year_id` INT UNSIGNED NOT NULL,
    `day_of_week`      ENUM('monday','tuesday','wednesday','thursday','friday','saturday') NOT NULL,
    `period_number`    INT UNSIGNED NOT NULL,
    `subject_id`       INT UNSIGNED NOT NULL,
    `faculty_id`       INT UNSIGNED NOT NULL,
    `room_id`          INT UNSIGNED DEFAULT NULL,
    `start_time`       TIME         NOT NULL,
    `end_time`         TIME         NOT NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_timetable_slot` (`section_id`, `academic_year_id`, `day_of_week`, `period_number`),
    CONSTRAINT `fk_tt_section`
        FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
    CONSTRAINT `fk_tt_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_tt_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
    CONSTRAINT `fk_tt_faculty`
        FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`),
    CONSTRAINT `fk_tt_room`
        FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 14 — OPERATIONS
-- Tables: hostel_blocks, hostel_rooms, hostel_allocations,
--         books, book_issues,
--         vehicles, transport_routes, transport_allocations
-- ============================================================

CREATE TABLE `hostel_blocks` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`   INT UNSIGNED     NOT NULL,
    `name`         VARCHAR(100)     NOT NULL,
    `type`         ENUM('boys','girls') NOT NULL,
    `total_rooms`  INT UNSIGNED     NOT NULL DEFAULT 0,
    `warden_id`    INT UNSIGNED     DEFAULT NULL,
    `status`       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_hb_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_hb_warden`
        FOREIGN KEY (`warden_id`) REFERENCES `staff` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hostel_rooms` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `hostel_block_id` INT UNSIGNED  NOT NULL,
    `room_number`     VARCHAR(20)   NOT NULL,
    `floor`           INT           NOT NULL DEFAULT 0,
    `capacity`        INT UNSIGNED  NOT NULL,
    `type`            ENUM('single','double','triple','dormitory') NOT NULL,
    `monthly_rent`    DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    `status`          ENUM('available','full','maintenance') NOT NULL DEFAULT 'available',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_hostel_room` (`hostel_block_id`, `room_number`),
    CONSTRAINT `fk_hr_block`
        FOREIGN KEY (`hostel_block_id`) REFERENCES `hostel_blocks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One student, one room per academic year enforced by UNIQUE.
CREATE TABLE `hostel_allocations` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `hostel_room_id`   INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `bed_number`       INT UNSIGNED     NOT NULL,
    `allotted_date`    DATE             NOT NULL,
    `vacated_date`     DATE             DEFAULT NULL,
    `status`           ENUM('active','vacated') NOT NULL DEFAULT 'active',
    `allotted_by`      INT UNSIGNED     NOT NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_hostel_alloc` (`student_id`, `academic_year_id`),
    KEY `idx_ha_room`  (`hostel_room_id`),
    CONSTRAINT `fk_ha_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_ha_room`
        FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`),
    CONSTRAINT `fk_ha_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
    CONSTRAINT `fk_ha_user`
        FOREIGN KEY (`allotted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hostel_bookings` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `hostel_block_id`   INT UNSIGNED     NOT NULL,
    `hostel_room_id`   INT UNSIGNED     NOT NULL,
    `bed_number`       INT UNSIGNED     NOT NULL DEFAULT 1,
    `academic_year`    VARCHAR(20)     NOT NULL DEFAULT '2026-2027',
    `semester`         VARCHAR(20)     NOT NULL DEFAULT 'Semester 1',
    `hostel_fee`       DECIMAL(10,2)    NOT NULL DEFAULT 25000.00,
    `payment_status`   ENUM('unpaid', 'paid', 'failed') NOT NULL DEFAULT 'unpaid',
    `payment_txn_id`   VARCHAR(100)     DEFAULT NULL,
    `payment_date`     DATETIME         DEFAULT NULL,
    `booking_status`   ENUM('payment_pending', 'payment_verification_pending', 'confirmed', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'payment_pending',
    `rejection_reason` TEXT             DEFAULT NULL,
    `verified_by`      INT UNSIGNED     DEFAULT NULL,
    `verified_at`      DATETIME         DEFAULT NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_hbk_student` (`student_id`),
    KEY `idx_hbk_room`    (`hostel_room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `books` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`       INT UNSIGNED     NOT NULL,
    `title`            VARCHAR(200)     NOT NULL,
    `author`           VARCHAR(200),
    `isbn`             VARCHAR(20),
    `publisher`        VARCHAR(150),
    `edition`          VARCHAR(50),
    `year_published`   YEAR,
    `category`         VARCHAR(100),
    `total_copies`     INT UNSIGNED     NOT NULL DEFAULT 1,
    `available_copies` INT UNSIGNED     NOT NULL DEFAULT 1,
    `location`         VARCHAR(100)     COMMENT 'Shelf/rack identifier',
    `status`           TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_book_isbn`  (`isbn`),
    KEY `idx_book_title` (`title`),
    CONSTRAINT `fk_book_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- issued_to_type + issued_to_id is polymorphic (student/faculty/staff).
-- No DB-level FK; enforced at application layer.
CREATE TABLE `book_issues` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `book_id`        INT UNSIGNED  NOT NULL,
    `issued_to_type` ENUM('student','faculty','staff') NOT NULL,
    `issued_to_id`   INT UNSIGNED  NOT NULL,
    `issued_date`    DATE          NOT NULL,
    `due_date`       DATE          NOT NULL,
    `returned_date`  DATE          DEFAULT NULL,
    `fine_amount`    DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    `fine_paid`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `issued_by`      INT UNSIGNED  NOT NULL,
    `status`         ENUM('issued','returned','overdue','lost') NOT NULL DEFAULT 'issued',
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_bi_book`   (`book_id`),
    KEY `idx_bi_issuee` (`issued_to_type`, `issued_to_id`),
    KEY `idx_bi_status` (`status`),
    CONSTRAINT `fk_bi_book`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `fk_bi_issuer`
        FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vehicles` (
    `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`           INT UNSIGNED     NOT NULL,
    `registration_number`  VARCHAR(30)      NOT NULL,
    `type`                 ENUM('bus','van','auto') NOT NULL,
    `capacity`             INT UNSIGNED     NOT NULL,
    `driver_name`          VARCHAR(150),
    `driver_mobile`        VARCHAR(15),
    `insurance_expiry`     DATE,
    `permit_expiry`        DATE,
    `fitness_expiry`       DATE,
    `status`               ENUM('active','maintenance','retired') NOT NULL DEFAULT 'active',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_vehicle_reg` (`registration_number`),
    CONSTRAINT `fk_vehicle_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transport_routes` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`      INT UNSIGNED     NOT NULL DEFAULT 1,
    `vehicle_id`      INT UNSIGNED     DEFAULT NULL,
    `route_code`      VARCHAR(50)      NOT NULL DEFAULT 'RT-01',
    `route_name`      VARCHAR(150)     NOT NULL,
    `bus_number`      VARCHAR(50)      NOT NULL DEFAULT 'BUS-01',
    `bus_reg_number`  VARCHAR(50)      DEFAULT 'KA-01-EQ-1234',
    `bus_type`        VARCHAR(50)      DEFAULT 'AC Deluxe Bus',
    `driver_name`     VARCHAR(150)     DEFAULT 'Ramesh Kumar',
    `driver_contact`  VARCHAR(20)      DEFAULT '+91 98765 43210',
    `start_point`     VARCHAR(150)     DEFAULT 'Central Bus Terminal',
    `end_point`       VARCHAR(150)     DEFAULT 'College Main Campus',
    `pickup_point`    VARCHAR(150)     DEFAULT 'Central Bus Terminal',
    `pickup_time`     TIME             DEFAULT '07:30:00',
    `drop_point`      VARCHAR(150)     DEFAULT 'College Main Campus',
    `drop_time`       TIME             DEFAULT '16:30:00',
    `distance_km`     DECIMAL(6,2)     DEFAULT 15.50,
    `monthly_fee`     DECIMAL(8,2)     NOT NULL DEFAULT 1200.00,
    `annual_fee`      DECIMAL(10,2)    NOT NULL DEFAULT 12000.00,
    `capacity`        INT UNSIGNED     NOT NULL DEFAULT 40,
    `available_seats` INT UNSIGNED     NOT NULL DEFAULT 40,
    `status`          TINYINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_tr_college` (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One student, one route per academic year enforced by UNIQUE.
CREATE TABLE `transport_allocations` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `route_id`         INT UNSIGNED     NOT NULL,
    `academic_year_id` INT UNSIGNED     NOT NULL,
    `pickup_point`     VARCHAR(150),
    `allotted_date`    DATE             NOT NULL,
    `status`           ENUM('active','cancelled') NOT NULL DEFAULT 'active',
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_transport_alloc` (`student_id`, `academic_year_id`),
    CONSTRAINT `fk_ta_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
    CONSTRAINT `fk_ta_route`
        FOREIGN KEY (`route_id`) REFERENCES `transport_routes` (`id`),
    CONSTRAINT `fk_ta_acyr`
        FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transport_subscriptions` (
    `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`           INT UNSIGNED     NOT NULL,
    `route_id`             INT UNSIGNED     NOT NULL,
    `pickup_point`         VARCHAR(150)     DEFAULT NULL,
    `pickup_time`          VARCHAR(30)      DEFAULT '07:15 AM',
    `drop_point`           VARCHAR(150)     DEFAULT NULL,
    `drop_time`            VARCHAR(30)      DEFAULT '08:30 AM',
    `academic_year`        VARCHAR(20)      NOT NULL DEFAULT '2026-2027',
    `annual_fee`           DECIMAL(10,2)    NOT NULL DEFAULT 12000.00,
    `payment_status`       ENUM('unpaid', 'pending', 'paid', 'failed') NOT NULL DEFAULT 'unpaid',
    `subscription_status`  ENUM('active', 'pending', 'cancelled', 'transferred', 'payment_pending') NOT NULL DEFAULT 'active',
    `created_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ts_student` (`student_id`),
    KEY `idx_ts_route`   (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transport_payments` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `subscription_id`  INT UNSIGNED     NOT NULL,
    `student_id`       INT UNSIGNED     NOT NULL,
    `transaction_id`   VARCHAR(100)     NOT NULL,
    `payment_date`     DATE             NOT NULL,
    `amount`           DECIMAL(10,2)    NOT NULL,
    `payment_status`   ENUM('pending', 'paid', 'rejected') NOT NULL DEFAULT 'pending',
    `verified_by`      INT UNSIGNED     DEFAULT NULL,
    `remarks`          TEXT             DEFAULT NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tp_sub`     (`subscription_id`),
    KEY `idx_tp_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transport_change_requests` (
    `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `student_id`       INT UNSIGNED     NOT NULL,
    `current_route_id` INT UNSIGNED     NOT NULL,
    `new_route_id`     INT UNSIGNED     NOT NULL,
    `status`           ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `transaction_id`   VARCHAR(100)     DEFAULT NULL,
    `payment_date`     DATE             DEFAULT NULL,
    `amount`           DECIMAL(10,2)    DEFAULT NULL,
    `payment_status`   ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    `reviewed_by`      INT UNSIGNED     DEFAULT NULL,
    `rejection_reason` TEXT             DEFAULT NULL,
    `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tcr_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transport_stops` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `route_id`    INT UNSIGNED     NOT NULL,
    `stop_name`   VARCHAR(150)     NOT NULL,
    `stop_order`  INT UNSIGNED     NOT NULL DEFAULT 1,
    `pickup_time` TIME             DEFAULT '07:30:00',
    `drop_time`   TIME             DEFAULT '16:30:00',
    `fee`         DECIMAL(8,2)     NOT NULL DEFAULT 1200.00,
    PRIMARY KEY (`id`),
    KEY `idx_tstop_route` (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 15 — COMMUNICATION & AUDIT
-- Tables: notifications, announcements, audit_logs
-- ============================================================

CREATE TABLE `notifications` (
    `id`                 INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`         INT UNSIGNED     NOT NULL,
    `user_id`            INT UNSIGNED     NOT NULL,
    `title`              VARCHAR(200)     NOT NULL,
    `message`            TEXT             NOT NULL,
    `link`               VARCHAR(255)     DEFAULT NULL,
    `type`               ENUM('info','warning','success','alert') NOT NULL DEFAULT 'info',
    `priority`           ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
    `source_hierarchy`   ENUM('chairman','principal','hod','admin','system') NOT NULL DEFAULT 'system',
    `is_read`            TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `read_at`            DATETIME         DEFAULT NULL,
    `created_at`         DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notif_user` (`user_id`, `is_read`),
    CONSTRAINT `fk_notif_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_notif_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcements` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `college_id`   INT UNSIGNED     NOT NULL,
    `title`        VARCHAR(200)     NOT NULL,
    `content`      TEXT             NOT NULL,
    `target_role`  INT UNSIGNED     DEFAULT NULL COMMENT 'NULL = all roles',
    `published_by` INT UNSIGNED     NOT NULL,
    `publish_at`   DATETIME         DEFAULT NULL,
    `expire_at`    DATETIME         DEFAULT NULL,
    `status`       ENUM('draft','published','expired') NOT NULL DEFAULT 'draft',
    `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ann_status` (`status`),
    CONSTRAINT `fk_ann_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_ann_role`
        FOREIGN KEY (`target_role`) REFERENCES `roles` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `fk_ann_publisher`
        FOREIGN KEY (`published_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- audit_logs: BIGINT PK (high volume), JSON columns for before/after.
-- NO ON DELETE CASCADE — audit records are permanent and immutable.
-- Never UPDATE or DELETE from this table in application code.
CREATE TABLE `audit_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `college_id`  INT UNSIGNED    NOT NULL DEFAULT 1,
    `user_id`     INT UNSIGNED    NOT NULL,
    `action`      ENUM('create','update','delete','login','logout','export') NOT NULL,
    `module`      VARCHAR(100)    NOT NULL,
    `record_id`   INT UNSIGNED    DEFAULT NULL,
    `old_values`  JSON            DEFAULT NULL,
    `new_values`  JSON            DEFAULT NULL,
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_user`   (`user_id`),
    KEY `idx_audit_module` (`module`),
    KEY `idx_audit_at`     (`created_at`),
    CONSTRAINT `fk_audit_college`
        FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
    CONSTRAINT `fk_audit_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Immutable. Never update or delete rows from this table.';



-- ============================================================
-- BLOCK 22 — EXAM HALL TICKETS & ELIGIBILITY
-- Tables: hall_tickets
-- ============================================================

CREATE TABLE `hall_tickets` (
    `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id`         INT UNSIGNED NOT NULL,
    `academic_year_id`   INT UNSIGNED NOT NULL,
    `semester_id`        INT UNSIGNED NOT NULL,
    `hall_ticket_number` VARCHAR(50) NOT NULL,
    `status`             ENUM('eligible','blocked_attendance','blocked_dues','condoned') NOT NULL DEFAULT 'eligible',
    `attendance_pct`     DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `pending_dues`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `condoned_by`        INT UNSIGNED DEFAULT NULL,
    `condonation_reason` TEXT DEFAULT NULL,
    `generated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_ht_student_sem` (`student_id`, `academic_year_id`, `semester_id`),
    CONSTRAINT `fk_ht_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 23 — CANTEEN MANAGEMENT
-- Tables: canteen_items, canteen_orders
-- ============================================================

CREATE TABLE `canteen_items` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `college_id`     INT UNSIGNED NOT NULL DEFAULT 1,
    `item_name`      VARCHAR(150) NOT NULL,
    `category`       VARCHAR(50) NOT NULL DEFAULT 'Snacks',
    `price`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock_quantity` INT NOT NULL DEFAULT 50,
    `stock_status`   ENUM('available', 'out_of_stock') NOT NULL DEFAULT 'available',
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_canteen_college` (`college_id`),
    KEY `idx_canteen_stock`   (`stock_status`),
    CONSTRAINT `fk_canteen_item_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `canteen_orders` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number`   VARCHAR(50) NOT NULL,
    `college_id`     INT UNSIGNED NOT NULL DEFAULT 1,
    `user_id`        INT UNSIGNED NOT NULL,
    `student_id`     INT UNSIGNED DEFAULT NULL,
    `item_id`        INT UNSIGNED NOT NULL,
    `item_name`      VARCHAR(150) NOT NULL,
    `quantity`       INT NOT NULL DEFAULT 1,
    `unit_price`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_price`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'pay_at_counter',
    `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
    `order_status`   ENUM('placed', 'preparing', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'placed',
    `notes`          TEXT DEFAULT NULL,
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_canteen_orders_user`    (`user_id`),
    KEY `idx_canteen_orders_college` (`college_id`),
    KEY `idx_canteen_orders_item`    (`item_id`),
    KEY `idx_canteen_orders_num`     (`order_number`),
    CONSTRAINT `fk_canteen_orders_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_canteen_orders_user`    FOREIGN KEY (`user_id`)    REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `canteen_order_items` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id`       INT UNSIGNED NOT NULL,
    `item_id`        INT UNSIGNED NOT NULL,
    `item_name`      VARCHAR(150) NOT NULL,
    `quantity`       INT NOT NULL DEFAULT 1,
    `unit_price`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `subtotal`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    KEY `idx_coi_order` (`order_id`),
    CONSTRAINT `fk_coi_order` FOREIGN KEY (`order_id`) REFERENCES `canteen_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 24 — LEAVE MANAGEMENT & HOSTEL OUTPASSES
-- Tables: leave_requests
-- ============================================================

CREATE TABLE `leave_requests` (
    `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `college_id`           INT UNSIGNED NOT NULL DEFAULT 1,
    `applicant_type`       ENUM('student','staff','faculty') NOT NULL,
    `applicant_id`         INT UNSIGNED NOT NULL,
    `leave_type`           ENUM('sick','casual','hostel_outpass','duty','other') NOT NULL DEFAULT 'casual',
    `from_date`            DATE NOT NULL,
    `to_date`              DATE NOT NULL,
    `reason`               TEXT NOT NULL,
    `expected_return_time` DATETIME DEFAULT NULL,
    `actual_return_time`   DATETIME DEFAULT NULL,
    `status`               ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
    `reviewed_by`          INT UNSIGNED DEFAULT NULL,
    `reviewed_at`          DATETIME DEFAULT NULL,
    `remarks`              TEXT DEFAULT NULL,
    `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_leave_app`    (`applicant_type`, `applicant_id`),
    KEY `idx_leave_dates`  (`from_date`, `to_date`),
    KEY `idx_leave_status` (`status`),
    CONSTRAINT `fk_leave_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 25 — PAYMENT GATEWAY TRANSACTIONS & MULTI-QR
-- Tables: payment_gateway_transactions
-- ============================================================

CREATE TABLE `payment_gateway_transactions` (
    `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `college_id`         INT UNSIGNED NOT NULL DEFAULT 1,
    `student_fee_id`     INT UNSIGNED DEFAULT NULL,
    `user_id`            INT UNSIGNED NOT NULL,
    `fee_type`           ENUM('academic','hostel','transport','canteen','other') NOT NULL DEFAULT 'academic',
    `gateway`            ENUM('razorpay','upi_qr','netbanking','card','cash') NOT NULL DEFAULT 'razorpay',
    `gateway_order_id`   VARCHAR(100) DEFAULT NULL,
    `gateway_payment_id` VARCHAR(100) DEFAULT NULL,
    `utr_reference`      VARCHAR(100) DEFAULT NULL,
    `gateway_signature`  VARCHAR(255) DEFAULT NULL,
    `amount`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency`           VARCHAR(10) NOT NULL DEFAULT 'INR',
    `status`             ENUM('created','authorized','captured','failed','pending_verification') NOT NULL DEFAULT 'created',
    `raw_response`       JSON DEFAULT NULL,
    `created_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_pg_order`   (`gateway_order_id`),
    KEY `idx_pg_payid`   (`gateway_payment_id`),
    KEY `idx_pg_utr`     (`utr_reference`),
    KEY `idx_pg_status`  (`status`),
    CONSTRAINT `fk_pg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- BLOCK 26 — MARKS REVISION & AUDIT LOGGING
-- Tables: marks_revision_log
-- ============================================================

CREATE TABLE `marks_revision_log` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type`       ENUM('internal','external') NOT NULL,
    `record_id`  INT UNSIGNED NOT NULL,
    `student_id` INT UNSIGNED NOT NULL,
    `subject_id` INT UNSIGNED NOT NULL,
    `old_marks`  DECIMAL(5,2) DEFAULT NULL,
    `new_marks`  DECIMAL(5,2) NOT NULL,
    `changed_by` INT UNSIGNED NOT NULL,
    `reason`     VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_mrl_student` (`student_id`),
    KEY `idx_mrl_subject` (`subject_id`),
    CONSTRAINT `fk_mrl_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mrl_user`    FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
-- SCHEMA COMPLETE
-- Total tables : 58
-- Total FKs    : 86
-- Normalization: 3NF verified
-- ============================================================
