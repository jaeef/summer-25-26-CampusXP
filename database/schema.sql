-- ==============================================================================
-- CampusXP Database DDL & Seed Script (MySQL 8.x InnoDB)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `CampusXP_aiub` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `CampusXP_aiub`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student','club_exec','recruiter','admin') NOT NULL DEFAULT 'student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Student Profiles Table (Entity 1)
CREATE TABLE IF NOT EXISTS `student_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `aiub_id` VARCHAR(20) NOT NULL UNIQUE,
  `department` VARCHAR(50) NOT NULL,
  `cgpa` DECIMAL(3,2) NOT NULL,
  `skills` TEXT NULL,
  `cv_url` VARCHAR(255) NOT NULL,
  `default_sop` TEXT NOT NULL,
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Opportunity Bookmarks Table (Entity 2)
CREATE TABLE IF NOT EXISTS `opportunity_bookmarks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `item_type` ENUM('event','club_drive','corporate_drive') NOT NULL,
  `item_id` INT NOT NULL,
  `saved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bookmark_student` FOREIGN KEY (`student_id`) REFERENCES `student_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Club Drives Table (Entity 4)
CREATE TABLE IF NOT EXISTS `club_drives` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exec_user_id` INT NOT NULL,
  `club_name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `category` ENUM('recruitment','seminar','workshop','fest') DEFAULT 'recruitment',
  `requirements` TEXT NOT NULL,
  `deadline` DATETIME NOT NULL,
  `location` VARCHAR(100) DEFAULT 'AIUB Campus',
  `status` ENUM('pending_approval','active','closed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_drive_exec` FOREIGN KEY (`exec_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Club Applications Table (Entity 3)
CREATE TABLE IF NOT EXISTS `club_applications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `drive_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `cv_link` VARCHAR(255) NOT NULL,
  `statement_of_purpose` TEXT NOT NULL,
  `applied_role` VARCHAR(100) DEFAULT 'Sub-Executive Member',
  `status` ENUM('Applied','Under Review','Interview','Selected','Rejected') DEFAULT 'Applied',
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_app_drive` FOREIGN KEY (`drive_id`) REFERENCES `club_drives`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_student` FOREIGN KEY (`student_id`) REFERENCES `student_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Interview Slots Table (Entity 5)
CREATE TABLE IF NOT EXISTS `interview_slots` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `drive_id` INT NOT NULL,
  `slot_datetime` DATETIME NOT NULL,
  `duration_mins` INT DEFAULT 15,
  `room_no` VARCHAR(50) NOT NULL,
  `assigned_application_id` INT NULL,
  `is_booked` TINYINT(1) DEFAULT 0,
  CONSTRAINT `fk_slot_drive` FOREIGN KEY (`drive_id`) REFERENCES `club_drives`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_slot_app` FOREIGN KEY (`assigned_application_id`) REFERENCES `club_applications`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Candidate Evaluations Table (Entity 6)
CREATE TABLE IF NOT EXISTS `candidate_evaluations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `application_id` INT NOT NULL,
  `evaluator_user_id` INT NOT NULL,
  `rating_score` INT NOT NULL CHECK (`rating_score` BETWEEN 1 AND 5),
  `dept_fit_notes` TEXT NULL,
  `evaluated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_eval_app` FOREIGN KEY (`application_id`) REFERENCES `club_applications`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_eval_user` FOREIGN KEY (`evaluator_user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- 8. Corporate Drives Table (Entity 7)
CREATE TABLE IF NOT EXISTS `corporate_drives` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `recruiter_user_id` INT NOT NULL,
  `company_name` VARCHAR(120) NOT NULL,
  `job_title` VARCHAR(150) NOT NULL,
  `job_type` ENUM('Full-Time','Part-Time','Internship','Trainee') DEFAULT 'Internship',
  `requirements` TEXT NOT NULL,
  `min_cgpa` DECIMAL(3,2) DEFAULT 3.00,
  `deadline` DATETIME NOT NULL,
  `status` ENUM('pending_approval','active','closed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_corp_recruiter` FOREIGN KEY (`recruiter_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Custom Form Fields Table (Entity 7 Component)
CREATE TABLE IF NOT EXISTS `custom_form_fields` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `corporate_drive_id` INT NOT NULL,
  `field_label` VARCHAR(150) NOT NULL,
  `field_type` ENUM('text','textarea','number','file','url','select') DEFAULT 'text',
  `is_required` TINYINT(1) DEFAULT 1,
  CONSTRAINT `fk_custom_drive` FOREIGN KEY (`corporate_drive_id`) REFERENCES `corporate_drives`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Recruiter Shortlists Table (Entity 8)
CREATE TABLE IF NOT EXISTS `recruiter_shortlists` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `recruiter_user_id` INT NOT NULL,
  `bucket_name` VARCHAR(100) NOT NULL,
  `filter_cgpa_min` DECIMAL(3,2) DEFAULT 3.00,
  `filter_dept` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_shortlist_recruiter` FOREIGN KEY (`recruiter_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Recruiter Outreach Logs Table (Entity 9)
CREATE TABLE IF NOT EXISTS `recruiter_outreach_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `shortlist_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `response_status` ENUM('Sent','Delivered','Accepted','Declined') DEFAULT 'Sent',
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_outreach_shortlist` FOREIGN KEY (`shortlist_id`) REFERENCES `recruiter_shortlists`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_outreach_student` FOREIGN KEY (`student_id`) REFERENCES `student_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Approval Requests Table (Entity 10)
CREATE TABLE IF NOT EXISTS `approval_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `target_type` ENUM('club_drive','corporate_drive','event') NOT NULL,
  `target_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `submitted_by` VARCHAR(100) NOT NULL,
  `status` ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  `admin_comments` TEXT NULL,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 13. Venue Booth Allocations Table (Entity 11)
CREATE TABLE IF NOT EXISTS `venue_booth_allocations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `approval_id` INT NULL,
  `campus_location` VARCHAR(100) NOT NULL,
  `booth_number` VARCHAR(20) NOT NULL UNIQUE,
  `assigned_club_name` VARCHAR(100) NULL,
  `allocated_date` DATE NOT NULL,
  `is_occupied` TINYINT(1) DEFAULT 0,
  CONSTRAINT `fk_booth_approval` FOREIGN KEY (`approval_id`) REFERENCES `approval_requests`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 14. Corporate Applications Table
CREATE TABLE IF NOT EXISTS `corporate_applications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `corporate_drive_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `applied_role` VARCHAR(100) NOT NULL,
  `cv_link` VARCHAR(255) NOT NULL,
  `statement_of_purpose` TEXT NOT NULL,
  `custom_answers` TEXT NULL,
  `status` ENUM('Applied','Under Review','Interview','Selected','Rejected') DEFAULT 'Applied',
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_corp_app_drive` FOREIGN KEY (`corporate_drive_id`) REFERENCES `corporate_drives`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_corp_app_student` FOREIGN KEY (`student_id`) REFERENCES `student_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Logistics Requests Table (Entity 12)
CREATE TABLE IF NOT EXISTS `logistics_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `approval_id` INT NULL,
  `user_id` INT NULL,
  `requester_role` ENUM('club_exec','recruiter','admin') DEFAULT 'club_exec',
  `club_or_org` VARCHAR(100) NOT NULL,
  `item_name` VARCHAR(100) NOT NULL,
  `quantity` INT NOT NULL,
  `purpose` VARCHAR(255) NULL,
  `date_needed` DATE NULL,
  `dispatch_status` ENUM('Requested','Approved','Dispatched','Returned','Rejected') DEFAULT 'Requested',
  `admin_notes` TEXT NULL,
  `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_logistics_approval` FOREIGN KEY (`approval_id`) REFERENCES `approval_requests`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==============================================================================
-- Demo Seed Data (Pre-hashed password is 'password123' -> $2y$10$e7f09W5X17P...)
-- ==============================================================================

-- 1. Users
INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`) VALUES
(1, 'Samiul Chowdhury', 'student@aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'student'),
(2, 'Tanvir Ahmed', 'exec@aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'club_exec'),
(3, 'Sabrina Khan', 'recruiter@brainstation-23.com', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'recruiter'),
(4, 'Administration', 'admin@aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'admin'),
(5, 'Ayesha Rahman', 'ayesha@student.aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'student'),
(6, 'Mahmudul Hasan', 'mahmud@student.aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'student'),
(7, 'Nusrat Jahan', 'nusrat@student.aiub.edu', '$2y$10$OLKO2Y7Kk.1BdqmG.S09LewVMbw.SDLnQpPLTn1nNdZn4IVaervR6', 'student');

-- 2. Student Profiles
INSERT INTO `student_profiles` (`id`, `user_id`, `aiub_id`, `department`, `cgpa`, `skills`, `cv_url`, `default_sop`) VALUES
(1, 1, '22-46812-1', 'CSE', 3.85, 'PHP, JavaScript, MySQL, Python, UI/UX', 'https://drive.google.com/file/d/aiub-cv-22-46812-1/view', 'I am a passionate 3rd-year CS student eager to contribute technical development and event leadership skills to advance AIUB campus innovation.'),
(2, 5, '20-43891-3', 'BBA', 3.92, 'Marketing, Public Relations, Financial Modeling, Event Hosting', 'https://drive.google.com/file/d/aiub-cv-20-43891-3/view', 'Aiming to lead high-impact marketing and corporate communications initiatives across AIUB campus drives.'),
(3, 6, '21-45001-1', 'CSE', 3.78, 'React, Node.js, Cloud Architecture, Docker', 'https://drive.google.com/file/d/aiub-cv-21-45001-1/view', 'Full-stack software developer focused on architecting scalable web platforms for university ecosystems.'),
(4, 7, '23-50119-2', 'EEE', 3.42, 'Circuit Prototyping, Embedded C, Robotics, IoT', 'https://drive.google.com/file/d/aiub-cv-23-50119-2/view', 'Hardware prototyping enthusiast seeking hands-on technical sub-executive roles in student branches.');

-- 3. Club Drives
INSERT INTO `club_drives` (`id`, `exec_user_id`, `club_name`, `title`, `category`, `requirements`, `deadline`, `location`, `status`) VALUES
(1, 2, 'AIUB Computer Club (ACC)', 'Executive Recruitment Drive 2026', 'recruitment', 'Minimum CGPA 3.00, active involvement in software dev, graphics design, or event management.', '2026-09-15 23:59:59', 'Annex 1 Plaza Booth B-01', 'active'),
(2, 2, 'IEEE AIUB Student Branch', 'Hands-on IoT & Microcontrollers Workshop', 'workshop', 'Open to all CSE and EEE students. Basic C programming knowledge required.', '2026-09-10 18:00:00', 'Annex 2 Lab 401', 'active'),
(3, 2, 'AIUB Drama Club', 'Annual Stage Production Auditions', 'fest', 'Passionate about acting, stage lighting, scriptwriting, and backstage management.', '2026-09-20 17:00:00', 'AIUB Amphitheater', 'active');

-- 4. Corporate Drives
INSERT INTO `corporate_drives` (`id`, `recruiter_user_id`, `company_name`, `job_title`, `job_type`, `requirements`, `min_cgpa`, `deadline`, `status`) VALUES
(1, 3, 'Brain Station 23', 'Software Engineer Trainee (Web & Cloud)', 'Trainee', 'Strong foundation in OOP, Database Design (SQL), and JavaScript / PHP. Final year students or fresh graduates.', 3.50, '2026-09-30 23:59:59', 'active'),
(2, 3, 'Enosis Solutions', 'Associate Software Quality Assurance Engineer', 'Full-Time', 'Solid knowledge of manual and automated testing, test case documentation, and API testing.', 3.25, '2026-10-05 23:59:59', 'active');

-- 5. Custom Form Fields
INSERT INTO `custom_form_fields` (`corporate_drive_id`, `field_label`, `field_type`, `is_required`) VALUES
(1, 'GitHub / GitLab Profile URL', 'url', 1),
(1, 'Preferred Backend Language (PHP, Node, Python, Java)', 'text', 1),
(1, 'Live Project Portfolio Link', 'url', 0),
(2, 'Automated Testing Experience (Selenium, Postman)', 'textarea', 1);

-- 6. Club Applications
INSERT INTO `club_applications` (`id`, `drive_id`, `student_id`, `cv_link`, `statement_of_purpose`, `applied_role`, `status`, `applied_at`) VALUES
(1, 1, 1, 'https://drive.google.com/file/d/aiub-cv-22-46812-1/view', 'I am a passionate 3rd-year CS student eager to contribute technical development...', 'Tech Lead Sub-Executive', 'Interview', '2026-08-25 14:30:00'),
(2, 1, 3, 'https://drive.google.com/file/d/aiub-cv-21-45001-1/view', 'Full-stack software developer focused on architecting scalable web platforms...', 'Software Dev Executive', 'Under Review', '2026-08-26 11:20:00'),
(3, 1, 4, 'https://drive.google.com/file/d/aiub-cv-23-50119-2/view', 'Hardware prototyping enthusiast seeking hands-on technical sub-executive roles...', 'Hardware Wing Coordinator', 'Applied', '2026-08-27 16:45:00'),
(4, 1, 2, 'https://drive.google.com/file/d/aiub-cv-20-43891-3/view', 'Aiming to lead high-impact marketing and corporate communications initiatives...', 'PR & Communications Lead', 'Selected', '2026-08-24 09:15:00');

-- 7. Interview Slots
INSERT INTO `interview_slots` (`id`, `drive_id`, `slot_datetime`, `duration_mins`, `room_no`, `assigned_application_id`, `is_booked`) VALUES
(1, 1, '2026-09-03 14:00:00', 15, 'Annex 1 - Room 302', NULL, 0),
(2, 1, '2026-09-03 14:15:00', 15, 'Annex 1 - Room 302', NULL, 0),
(3, 1, '2026-09-03 14:30:00', 15, 'Annex 1 - Room 302', 1, 1),
(4, 1, '2026-09-03 14:45:00', 15, 'Annex 1 - Room 302', NULL, 0),
(5, 1, '2026-09-03 15:00:00', 15, 'Annex 1 - Room 302', NULL, 0);

-- 8. Opportunity Bookmarks
INSERT INTO `opportunity_bookmarks` (`student_id`, `item_type`, `item_id`) VALUES
(1, 'club_drive', 1),
(1, 'corporate_drive', 1);

-- 9. Recruiter Shortlists & Outreach
INSERT INTO `recruiter_shortlists` (`id`, `recruiter_user_id`, `bucket_name`, `filter_cgpa_min`, `filter_dept`) VALUES
(1, 3, 'Top AIUB CSE Seniors (2026)', 3.50, 'CSE'),
(2, 3, 'High Distinction Business Leads', 3.75, 'BBA');

INSERT INTO `recruiter_outreach_logs` (`shortlist_id`, `student_id`, `message`, `response_status`) VALUES
(1, 1, 'You have been pre-shortlisted for Brain Station 23 Software Engineer Trainee Drive. Please review the details and schedule your technical interview.', 'Delivered');

-- 10. Moderation Requests
INSERT INTO `approval_requests` (`id`, `target_type`, `target_id`, `title`, `submitted_by`, `status`, `admin_comments`) VALUES
(1, 'club_drive', 1, 'ACC Executive Recruitment Drive 2026', 'AIUB Computer Club (ACC)', 'Approved', 'Approved for Annex 1 Plaza booth allocation.'),
(2, 'club_drive', 2, 'IEEE Hands-on IoT Workshop', 'IEEE AIUB Student Branch', 'Approved', 'Lab 401 reserved for Sept 10.'),
(3, 'corporate_drive', 1, 'Brain Station 23 Software Trainee Drive', 'Brain Station 23 HR', 'Approved', 'Verified corporate partner posting.');

-- 11. Venue Booth Allocations
INSERT INTO `venue_booth_allocations` (`id`, `approval_id`, `campus_location`, `booth_number`, `assigned_club_name`, `allocated_date`, `is_occupied`) VALUES
(1, 1, 'Annex 1 Ground Plaza', 'Booth B-01', 'AIUB Computer Club', '2026-09-01', 1),
(2, 2, 'D-Building Canopy', 'Booth B-02', 'IEEE AIUB Student Branch', '2026-09-02', 1),
(3, NULL, 'Amphitheater Lawn', 'Booth B-03', NULL, '2026-09-03', 0),
(4, NULL, 'Annex 2 Court', 'Booth B-04', NULL, '2026-09-04', 0);

-- 12. Logistics Requests
INSERT INTO `logistics_requests` (`id`, `approval_id`, `club_or_org`, `item_name`, `quantity`, `dispatch_status`) VALUES
(1, 1, 'AIUB Computer Club', 'Sound System & 2 Wireless Mics', 1, 'Dispatched'),
(2, 1, 'AIUB Computer Club', 'Display Table & 6 Chairs', 4, 'Dispatched'),
(3, 2, 'IEEE AIUB Branch', 'Projector Screen & Extension Cable', 2, 'Approved'),
(4, NULL, 'AIUB Drama Club', 'Stage Spotlights & Sound Mixer', 1, 'Requested');
