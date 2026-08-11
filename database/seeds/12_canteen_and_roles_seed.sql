-- ============================================================
-- CANTEEN MODULE & NON-FACULTY ROLES SEED
-- ============================================================

USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create Canteen Items & Orders Tables
CREATE TABLE IF NOT EXISTS `canteen_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `college_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `item_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Snacks',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock_status` ENUM('available', 'out_of_stock') NOT NULL DEFAULT 'available',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `canteen_orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL,
  `college_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `user_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED DEFAULT NULL,
  `item_id` INT UNSIGNED NOT NULL,
  `item_name` VARCHAR(150) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'pay_at_counter',
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `order_status` ENUM('placed', 'preparing', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'placed',
  `notes` TEXT DEFAULT NULL,
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

-- Seed sample canteen orders
INSERT INTO `canteen_orders` (`id`, `order_number`, `college_id`, `user_id`, `student_id`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `payment_method`, `payment_status`, `order_status`)
VALUES
(1, 'ORD-20260811-1001', 1, 10, 1, 1, 'Masala Dosa', 1, 40.00, 40.00, 'pay_at_counter', 'paid', 'completed'),
(2, 'ORD-20260811-1002', 1, 10, 1, 3, 'Cold Coffee', 2, 35.00, 70.00, 'online_upi', 'paid', 'ready')
ON DUPLICATE KEY UPDATE `order_number` = VALUES(`order_number`);

-- 2. Insert Canteen & Leave Modules and Permissions
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES 
(14, 'Canteen', 'canteen', NULL, 'coffee', 140, 1),
(15, 'Leave Management', 'leave', NULL, 'calendar', 150, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES 
(45, 14, 'Manage Canteen', 'canteen.manage', 'Manage canteen menu items and daily sales ledger'),
(46, 15, 'Apply Leave & Outpass', 'leave.apply', 'Submit student, staff, faculty leave and hostel outpasses'),
(47, 15, 'Review & Approve Leave', 'leave.approve', 'Review and approve/reject leave requests and outpasses'),
(48, 11, 'Allocate Hostel Rooms', 'hostel.allocate', 'Allocate students to hostel rooms and manage checkouts'),
(49, 8,  'Pay Fee Online',       'fee.pay_online', 'Initiate online payment gateway and UPI checkout')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. Setup Roles (Super Admin, Admin, HOD, Faculty, Accounts, Librarian, Warden, Transport, Canteen, Student, Parent)
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
(10, 1, 'Student', 'student', 0, 'Enrolled Student'),
(11, 1, 'Parent', 'parent', 1, 'Guardian & Parent Portal Access');

-- 4. Setup Role Permissions
TRUNCATE TABLE `role_permissions`;

-- Super Admin (Role 1) & Admin (Role 2) -> ALL
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 1, id, 1 FROM `permissions`;

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 2, id, 1 FROM `permissions`;

-- HOD (Role 3)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 3, id, 1 FROM `permissions` WHERE code IN ('student.view', 'faculty.view', 'attendance.mark', 'attendance.view', 'timetable.manage', 'marks.internal', 'marks.external', 'result.publish', 'reports.academic', 'reports.attendance', 'notification.announcement', 'leave.apply', 'leave.approve');

-- Faculty (Role 4)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 4, id, 1 FROM `permissions` WHERE code IN ('student.view', 'attendance.mark', 'attendance.view', 'timetable.manage', 'marks.internal', 'notification.announcement', 'leave.apply', 'leave.approve');

-- Accounts Staff (Role 5)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 5, id, 1 FROM `permissions` WHERE code IN ('staff.view', 'fee.category', 'fee.structure', 'fee.assign', 'fee.payment', 'fee.receipt', 'accounts.manage', 'reports.financial', 'notification.announcement', 'leave.apply');

-- Librarian (Role 6)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 6, id, 1 FROM `permissions` WHERE code IN ('student.view', 'library.manage', 'notification.announcement', 'leave.apply');

-- Hostel Warden (Role 7)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 7, id, 1 FROM `permissions` WHERE code IN ('student.view', 'hostel.manage', 'hostel.allocate', 'notification.announcement', 'leave.apply', 'leave.approve');

-- Transport Manager (Role 8)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 8, id, 1 FROM `permissions` WHERE code IN ('student.view', 'transport.manage', 'notification.announcement', 'leave.apply');

-- Canteen Manager (Role 9)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 9, id, 1 FROM `permissions` WHERE code IN ('student.view', 'canteen.manage', 'notification.announcement', 'leave.apply');

-- Student (Role 10)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 10, id, 1 FROM `permissions` WHERE code IN ('attendance.view', 'result.publish', 'fee.receipt', 'fee.pay_online', 'notification.announcement', 'leave.apply');

-- Parent (Role 11)
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 11, id, 1 FROM `permissions` WHERE code IN ('attendance.view', 'result.publish', 'fee.receipt', 'fee.pay_online', 'notification.announcement', 'leave.apply');

-- 5. Seed Users for ALL 11 Roles
DELETE FROM `users` WHERE `id` IN (1,2,3,4,5,6,7,8,9,10,11);

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
(10, 1, '2026-CSE-001', 'john.doe@ait.edu.in', '$2y$10$Spr3apksGyB0tnIBbSRXXOFXvPHCt/pSJGjazCUD5yq/822bGCTla', 10, 'student', 1, 1),
(11, 1, '9999911111', 'parent.john@kuppam.edu.in', '$2y$10$Spr3apksGyB0tnIBbSRXXOFXvPHCt/pSJGjazCUD5yq/822bGCTla', 11, 'parent', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
