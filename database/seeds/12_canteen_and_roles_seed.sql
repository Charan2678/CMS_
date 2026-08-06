-- ============================================================
-- CANTEEN MODULE & NON-FACULTY ROLES SEED
-- ============================================================

USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create Canteen Items Table
CREATE TABLE IF NOT EXISTS `canteen_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `college_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `item_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Snacks',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock_status` ENUM('available', 'out_of_stock') NOT NULL DEFAULT 'available',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default canteen items
INSERT INTO `canteen_items` (`id`, `college_id`, `item_name`, `category`, `price`, `stock_status`)
VALUES
(1, 1, 'Masala Dosa', 'Breakfast', 40.00, 'available'),
(2, 1, 'Veg Thali Combo', 'Lunch', 80.00, 'available'),
(3, 1, 'Cold Coffee', 'Beverages', 35.00, 'available'),
(4, 1, 'Samosa (2 pcs)', 'Snacks', 20.00, 'available')
ON DUPLICATE KEY UPDATE `item_name` = VALUES(`item_name`);

-- 2. Insert Canteen Module & Permission
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (14, 'Canteen', 'canteen', NULL, 'coffee', 140, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES (45, 14, 'Manage Canteen', 'canteen.manage', 'Manage canteen menu items and daily sales ledger')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. Setup Roles (Super Admin, Admin, HOD, Faculty, Accounts, Librarian, Warden, Transport, Canteen, Student)
TRUNCATE TABLE `roles`;

INSERT INTO `roles` (`id`, `college_id`, `name`, `code`, `is_system_role`, `description`) VALUES
(1, 1, 'Super Admin', 'super_admin', 1, 'Full Unrestricted System Administrator'),
(2, 1, 'Admin', 'admin', 1, 'Institutional Principal & Chairman Administrator'),
(3, 1, 'HOD', 'hod', 0, 'Head of Academic Department'),
(4, 1, 'Faculty', 'faculty', 0, 'Teaching Faculty Member'),
(5, 1, 'Accounts Staff', 'accounts_staff', 0, 'Institutional Accounts & Finance Officer'),
(6, 1, 'Librarian', 'librarian', 0, 'Library Manager'),
(7, 1, 'Hostel Warden', 'hostel_warden', 0, 'Hostel & Facility Warden'),
(8, 1, 'Transport Manager', 'transport_manager', 0, 'Transport & Fleet Manager'),
(9, 1, 'Canteen Manager', 'canteen_manager', 0, 'Canteen & Mess Operations Manager'),
(10, 1, 'Student', 'student', 0, 'Enrolled Student');

-- 4. Setup Role Permissions
TRUNCATE TABLE `role_permissions`;

-- Super Admin (Role 1) & Admin (Role 2) -> ALL
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 1, id, 1 FROM `permissions`;

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 2, id, 1 FROM `permissions`;

-- HOD (Role 3)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 3, id, 1 FROM `permissions` WHERE code IN ('student.view', 'faculty.view', 'attendance.mark', 'attendance.view', 'timetable.manage', 'marks.internal', 'marks.external', 'result.publish', 'reports.academic', 'reports.attendance', 'notification.announcement');

-- Faculty (Role 4)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 4, id, 1 FROM `permissions` WHERE code IN ('student.view', 'attendance.mark', 'attendance.view', 'timetable.manage', 'marks.internal', 'notification.announcement');

-- Accounts Staff (Role 5)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 5, id, 1 FROM `permissions` WHERE code IN ('staff.view', 'fee.category', 'fee.structure', 'fee.assign', 'fee.payment', 'fee.receipt', 'accounts.manage', 'reports.financial', 'notification.announcement');

-- Librarian (Role 6)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 6, id, 1 FROM `permissions` WHERE code IN ('student.view', 'library.manage', 'notification.announcement');

-- Hostel Warden (Role 7)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 7, id, 1 FROM `permissions` WHERE code IN ('student.view', 'hostel.manage', 'notification.announcement');

-- Transport Manager (Role 8)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 8, id, 1 FROM `permissions` WHERE code IN ('student.view', 'transport.manage', 'notification.announcement');

-- Canteen Manager (Role 9)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 9, id, 1 FROM `permissions` WHERE code IN ('student.view', 'canteen.manage', 'notification.announcement');

-- Student (Role 10)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 10, id, 1 FROM `permissions` WHERE code IN ('attendance.view', 'result.publish', 'fee.receipt', 'notification.announcement');

-- 5. Seed Users for ALL 10 Roles
DELETE FROM `users` WHERE `id` IN (1,2,3,4,5,6,7,8,9,10);

INSERT INTO `users` (`id`, `college_id`, `username`, `email`, `password_hash`, `role_id`, `linked_type`, `linked_id`, `is_active`)
VALUES
(1, 1, 'admin', 'admin@kuppam.edu.in', '$2y$10$wCo4DpCCjUlAoyH6zyidie9rPcDwyruPye9/5XZL6oEfWw5YY0sIq', 1, 'admin', 1, 1),
(2, 1, 'admin_user', 'admin_user@kuppam.edu.in', '$2y$10$iys7A7FFyoUmSkBeQ8TsOuUymqZMKElnAjQC7Ol0mN24yQU96VAp2', 2, 'admin', 1, 1),
(3, 1, 'EMP-FAC-001', 'alan.turing@ait.edu.in', '$2y$10$psJNDI1TwhWGloBqG4JXVe.7HP8saBoeVcsJJXpXEIAeG4IJa5Na.', 3, 'faculty', 1, 1),
(4, 1, 'EMP-FAC-002', 'grace.hopper@ait.edu.in', '$2y$10$psJNDI1TwhWGloBqG4JXVe.7HP8saBoeVcsJJXpXEIAeG4IJa5Na.', 4, 'faculty', 2, 1),
(5, 1, 'accounts_user', 'accounts@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 5, 'staff', 1, 1),
(6, 1, 'librarian_user', 'library@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 6, 'staff', 2, 1),
(7, 1, 'warden_user', 'hostel@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 7, 'staff', 3, 1),
(8, 1, 'transport_user', 'transport@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 8, 'staff', 4, 1),
(9, 1, 'canteen_user', 'canteen@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 9, 'staff', 5, 1),
(10, 1, '2026-CSE-001', 'john.doe@ait.edu.in', '$2y$10$Spr3apksGyB0tnIBbSRXXOFXvPHCt/pSJGjazCUD5yq/822bGCTla', 10, 'student', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
