USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `users` WHERE `id` IN (1,2,3,4,5,6);

INSERT INTO `users` (`id`, `college_id`, `username`, `email`, `password_hash`, `role_id`, `linked_type`, `linked_id`, `is_active`)
VALUES
(1, 1, 'admin', 'admin@kuppam.edu.in', '$2y$10$wCo4DpCCjUlAoyH6zyidie9rPcDwyruPye9/5XZL6oEfWw5YY0sIq', 1, 'admin', 1, 1),
(2, 1, 'admin_user', 'admin_user@kuppam.edu.in', '$2y$10$iys7A7FFyoUmSkBeQ8TsOuUymqZMKElnAjQC7Ol0mN24yQU96VAp2', 2, 'admin', 1, 1),
(3, 1, 'EMP-FAC-001', 'alan.turing@ait.edu.in', '$2y$10$psJNDI1TwhWGloBqG4JXVe.7HP8saBoeVcsJJXpXEIAeG4IJa5Na.', 3, 'faculty', 1, 1),
(4, 1, 'EMP-FAC-002', 'grace.hopper@ait.edu.in', '$2y$10$psJNDI1TwhWGloBqG4JXVe.7HP8saBoeVcsJJXpXEIAeG4IJa5Na.', 4, 'faculty', 2, 1),
(5, 1, 'EMP-STF-001', 'staff@kuppam.edu.in', '$2y$10$dZb4mDD3/0JjxOSW1lcsEOncfOy3UXFhXQZMYWCN5SRlxffpdpPc.', 5, 'staff', 1, 1),
(6, 1, '2026-CSE-001', 'john.doe@ait.edu.in', '$2y$10$Spr3apksGyB0tnIBbSRXXOFXvPHCt/pSJGjazCUD5yq/822bGCTla', 6, 'student', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
