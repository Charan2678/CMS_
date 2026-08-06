-- ============================================================
-- INITIAL SEED DATA
-- Stage 6: Default College, Roles, and Super Admin User
-- Default Credentials:
--   Username: admin
--   Password: Password123! (hash: $2y$12$NqBqH9eE4D7l8J6Q1bK0v.S5e7vF6g8h9i0j1k2l3m4n5o6p7q8r9)
-- ============================================================

USE `cms`;

-- 1. Default College
INSERT INTO `colleges` (`id`, `name`, `code`, `email`, `phone`, `status`, `created_at`)
VALUES (1, 'Global Institute of Technology & Management', 'GITM', 'admin@gitm.edu', '+91 9876543210', 1, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. System Roles
INSERT INTO `roles` (`id`, `college_id`, `name`, `code`, `is_system_role`, `description`, `status`, `created_at`)
VALUES
(1, 1, 'Super Admin', 'super_admin', 1, 'Full system-wide administrative access', 1, NOW()),
(2, 1, 'Admin',       'admin',       1, 'College level administration', 1, NOW()),
(3, 1, 'HOD',         'hod',         1, 'Head of Department', 1, NOW()),
(4, 1, 'Faculty',     'faculty',     1, 'Teaching staff', 1, NOW()),
(5, 1, 'Staff',       'staff',       1, 'Non-teaching staff', 1, NOW()),
(6, 1, 'Student',     'student',     1, 'Enrolled student', 1, NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. Super Admin User (password: Password123!)
-- Hash generated via password_hash('Password123!', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO `users` (`id`, `college_id`, `username`, `email`, `password_hash`, `role_id`, `linked_type`, `linked_id`, `is_active`, `must_change_password`, `created_at`)
VALUES
(1, 1, 'admin', 'admin@gitm.edu', '$2y$12$k/xleE9oNC8xCXfwT55kx.fZRBh6bBglINLQcHyM/YlzCk64iTB0C', 1, 'admin', 1, 1, 0, NOW())
ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `username` = VALUES(`username`);
