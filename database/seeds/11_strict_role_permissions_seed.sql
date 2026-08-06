-- ============================================================
-- STRICT ROLE-BASED PERMISSION ASSIGNMENTS SEED
-- ============================================================

USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

-- Clear previous broad permissions
TRUNCATE TABLE `role_permissions`;

-- 1. Super Admin (Role 1) & Admin (Role 2) -> ALL permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 1, id, 1 FROM `permissions`;

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 2, id, 1 FROM `permissions`;

-- 2. HOD (Role 3) -> Academic & Departmental Management
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 3, id, 1 FROM `permissions`
WHERE code IN (
    'student.view', 'faculty.view',
    'attendance.mark', 'attendance.view',
    'timetable.manage', 'marks.internal', 'marks.external', 'result.publish',
    'reports.academic', 'reports.attendance', 'notification.announcement'
);

-- 3. Faculty (Role 4) -> Attendance & Marks Entry
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 4, id, 1 FROM `permissions`
WHERE code IN (
    'student.view',
    'attendance.mark', 'attendance.view',
    'timetable.manage', 'marks.internal',
    'notification.announcement'
);

-- 4. Staff (Role 5) -> Fee Collection, Accounts & Facilities Management
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 5, id, 1 FROM `permissions`
WHERE code IN (
    'staff.view', 'student.view',
    'fee.category', 'fee.structure', 'fee.assign', 'fee.payment', 'fee.receipt',
    'library.manage', 'hostel.manage', 'transport.manage', 'accounts.manage',
    'reports.financial', 'notification.announcement'
);

-- 5. Student (Role 6) -> Self-Service Portal Only
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted`)
SELECT 6, id, 1 FROM `permissions`
WHERE code IN (
    'attendance.view', 'result.publish', 'fee.receipt', 'notification.announcement'
);

SET FOREIGN_KEY_CHECKS = 1;
