-- CMS Database Cleanup Migration — Remove all sample/dummy records
-- Keeps database structure, master schema, and role/user login accounts intact.

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Library Sample Data
TRUNCATE TABLE book_issues;
TRUNCATE TABLE books;

-- 2. Canteen Sample Data
TRUNCATE TABLE canteen_order_items;
TRUNCATE TABLE canteen_orders;
TRUNCATE TABLE canteen_items;

-- 3. Hostel Sample Data
TRUNCATE TABLE hostel_allocations;
TRUNCATE TABLE hostel_bookings;
TRUNCATE TABLE hostel_rooms;
TRUNCATE TABLE hostel_blocks;
TRUNCATE TABLE hostel_payment_settings;

-- 4. Transport Sample Data
TRUNCATE TABLE transport_allocations;
TRUNCATE TABLE transport_change_requests;
TRUNCATE TABLE transport_payments;
TRUNCATE TABLE transport_subscriptions;
TRUNCATE TABLE transport_stops;
TRUNCATE TABLE transport_routes;
TRUNCATE TABLE vehicles;

-- 5. Leave & Outpass Sample Data
TRUNCATE TABLE leave_requests;

-- 6. Financial Ledger & Payments Sample Data
TRUNCATE TABLE receipts;
TRUNCATE TABLE payment_gateway_transactions;
TRUNCATE TABLE payments;

-- 7. Timetable Sample Data
TRUNCATE TABLE timetable_publications;
TRUNCATE TABLE timetable;

-- 8. Examination & Results Sample Data
TRUNCATE TABLE external_marks;
TRUNCATE TABLE internal_marks;
TRUNCATE TABLE results;
TRUNCATE TABLE hall_tickets;
TRUNCATE TABLE marks_revision_log;

-- 9. Attendance Sample Data
TRUNCATE TABLE attendance;

-- 10. Student Sample Data
TRUNCATE TABLE student_documents;
TRUNCATE TABLE student_fees;
TRUNCATE TABLE student_academics;
TRUNCATE TABLE guardians;
TRUNCATE TABLE students;

-- 11. Faculty & Staff Sample Data
TRUNCATE TABLE faculty_documents;
TRUNCATE TABLE faculty_subject_assignments;
TRUNCATE TABLE faculty;
TRUNCATE TABLE staff;

-- 12. System Communication & Audit Logs
TRUNCATE TABLE announcements;
TRUNCATE TABLE notifications;
TRUNCATE TABLE audit_logs;

-- Clear entity links on users table so login accounts remain active for real data entry
UPDATE users SET linked_id = NULL WHERE linked_type IN ('student', 'faculty', 'staff', 'parent');

SET FOREIGN_KEY_CHECKS = 1;
