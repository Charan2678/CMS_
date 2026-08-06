-- ============================================================
-- FINANCE & FEE MODULE PERMISSIONS & CATEGORIES SEED
-- ============================================================

USE `cms`;

-- Insert Module
INSERT INTO `modules` (`id`, `name`, `code`, `parent_id`, `icon`, `sort_order`, `status`)
VALUES (7, 'Finance & Fee Management', 'fee', NULL, 'dollar-sign', 70, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Permissions
INSERT INTO `permissions` (`id`, `module_id`, `name`, `code`, `description`)
VALUES
(31, 7, 'Manage Fee Categories', 'fee.category', 'Create and manage fee categories'),
(32, 7, 'Manage Fee Structures', 'fee.structure', 'Set fee structure per course, semester, and year'),
(33, 7, 'Assign Student Fees', 'fee.assign', 'Assign fee structure and discounts to students'),
(34, 7, 'Process Payments', 'fee.payment', 'Collect payments and record transactions'),
(35, 7, 'View Fee Receipts', 'fee.receipt', 'Generate and print official fee payment receipts')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert Default Fee Categories
INSERT INTO `fee_categories` (`id`, `college_id`, `name`, `code`, `is_refundable`, `status`)
VALUES
(1, 1, 'Tuition Fee', 'tuition_fee', 0, 1),
(2, 1, 'Laboratory & Practical Fee', 'lab_fee', 0, 1),
(3, 1, 'Library Fee', 'library_fee', 0, 1),
(4, 1, 'Examination Fee', 'exam_fee', 0, 1),
(5, 1, 'Caution Deposit', 'caution_deposit', 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
