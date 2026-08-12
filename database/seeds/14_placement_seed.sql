-- ============================================================
-- Seed 14 — Placement Companies, Recruitment Drives & Preparation
-- ============================================================

INSERT INTO `companies` (`id`, `name`, `industry`, `website`, `hr_name`, `hr_email`, `hr_phone`, `status`, `created_at`) VALUES
(1, 'Tata Consultancy Services (TCS)', 'IT & Software Services', 'https://www.tcs.com', 'Rajesh Kumar', 'campus.tcs@tcs.com', '+91 9876543210', 'active', NOW()),
(2, 'Infosys', 'Information Technology', 'https://www.infosys.com', 'Priya Sharma', 'careers@infosys.com', '+91 9876543211', 'active', NOW()),
(3, 'Wipro', 'IT Consulting', 'https://www.wipro.com', 'Anil Verma', 'campus@wipro.com', '+91 9876543212', 'active', NOW()),
(4, 'Amazon', 'Cloud & E-Commerce', 'https://www.amazon.jobs', 'Sarah Jenkins', 'university-recruiting@amazon.com', '+91 9876543213', 'active', NOW()),
(5, 'Accenture', 'Management Consulting & IT', 'https://www.accenture.com', 'Vikram Singh', 'india.campus@accenture.com', '+91 9876543214', 'active', NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `placement_drives` (`id`, `company_id`, `title`, `designation`, `ctc_lpa`, `eligibility_cgpa`, `max_backlogs`, `drive_date`, `location`, `status`, `created_at`) VALUES
(1, 1, 'TCS Ninja & Digital Drive 2026', 'System Engineer', 7.50, 6.50, 1, '2026-09-15', 'Main Auditorium & CS Labs', 'scheduled', NOW()),
(2, 2, 'Infosys Specialist Programmer', 'Software Engineer', 9.50, 7.00, 0, '2026-09-22', 'Placement Center', 'scheduled', NOW()),
(3, 4, 'Amazon SDE-1 Campus Drive', 'Software Development Engineer', 18.50, 8.00, 0, '2026-10-05', 'Virtual / Online Test', 'scheduled', NOW()),
(4, 3, 'Wipro Elite National Talent Hunt', 'Project Engineer', 4.50, 6.00, 2, '2026-10-12', 'Placement Block B', 'scheduled', NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `placement_applications` (`drive_id`, `student_id`, `applied_at`, `status`, `remarks`) VALUES
(1, 1, NOW(), 'applied', 'Eligible & registered for TCS Digital')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);

INSERT INTO `placement_trainings` (`id`, `title`, `trainer_name`, `topic`, `scheduled_date`, `venue`, `target_year`, `created_at`) VALUES
(1, 'Aptitude & Technical Problem Solving Bootcamp', 'Dr. K. Srinivas', 'Quantitative Aptitude, Data Structures & Algorithms', '2026-08-20 10:00:00', 'Seminar Hall A', 4, NOW()),
(2, 'Corporate Soft Skills & Mock Interview Series', 'Ms. Anita Roy', 'Resume Building, Group Discussion & Technical Interviews', '2026-08-25 14:00:00', 'Placement Training Room 2', 4, NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);
