-- ============================================================
-- OPERATIONS MODULES PERMISSIONS SEED
-- ============================================================

USE `cms`;

-- Insert Modules
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES
(10, 'Library', 'library', NULL, 'book', 100, 1),
(11, 'Hostel', 'hostel', NULL, 'home', 110, 1),
(12, 'Transport', 'transport', NULL, 'truck', 120, 1),
(13, 'Accounts', 'accounts', NULL, 'credit-card', 130, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(41, 10, 'Manage Library', 'library.manage', 'Manage book catalog and student issue/return ledger'),
(42, 11, 'Manage Hostel', 'hostel.manage', 'Manage hostel blocks, rooms, and room allocations'),
(43, 12, 'Manage Transport', 'transport.manage', 'Manage bus routes, vehicles, and student transport allocations'),
(44, 13, 'Manage Accounts', 'accounts.manage', 'Manage institutional expenses and payroll accounts')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
