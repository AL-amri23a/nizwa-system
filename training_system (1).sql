-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2026 at 06:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `training_system`
--
CREATE DATABASE IF NOT EXISTS `training_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `training_system`;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late') DEFAULT 'Absent',
  `time` time NOT NULL DEFAULT curtime(),
  `checkout_time` time DEFAULT NULL,
  `exit_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `trainee_id`, `date`, `status`, `time`, `checkout_time`, `exit_time`) VALUES
(3, 31, '2025-11-13', 'Present', '22:02:53', NULL, NULL),
(4, 31, '2025-11-14', 'Present', '01:07:21', NULL, NULL),
(5, 31, '2025-11-15', 'Present', '14:25:26', NULL, NULL),
(7, 31, '2025-11-16', 'Present', '15:01:09', NULL, NULL),
(8, 31, '2025-11-23', 'Absent', '13:48:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `certificate_number` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `trainee_id`, `file_path`, `certificate_number`, `created_at`) VALUES
(3, 32, 'certificates/certificate_32_1767439762.pdf', 'CERT-32-1767439762', '2026-01-03 15:29:22'),
(4, 31, 'certificates/certificate_31_1767439824.pdf', 'CERT-31-1767439824', '2026-01-03 15:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `hours` int(11) DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `trainee_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Pending','Ongoing','Completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 0, 'المتدرب مصطفى أرسل مشروعاً جديداً: pro', 0, '2025-11-15 14:09:14'),
(2, 0, 'قام المتدرب مصطفى بإرسال مشروع جديد.', 0, '2025-11-15 17:10:51'),
(3, 0, 'قام المتدرب مصطفى بإرسال مشروع جديد.', 0, '2025-11-15 17:11:14'),
(4, 0, 'المتدرب مصطفى أرسل مشروع جديد: شئ', 0, '2025-11-15 17:30:04'),
(5, 0, 'المتدرب مصطفى أرسل مشروع جديد: ي', 0, '2025-11-15 17:44:58'),
(6, 31, 'تمت إضافة شهادة جديدة لك.', 0, '2025-11-15 20:15:44'),
(7, 0, 'المتدرب مصطفى أرسل مشروع جديد: jjj', 0, '2025-11-16 14:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('مستلم','تمت المراجعة','مرفوض') NOT NULL DEFAULT 'مستلم',
  `feedback` text DEFAULT NULL,
  `trainee_note` text DEFAULT NULL,
  `instructor_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `trainee_id`, `title`, `file_path`, `submitted_at`, `status`, `feedback`, `trainee_note`, `instructor_note`) VALUES
(12, 31, 'ي', '1763214298_1763201354_1762849980_submisson_041125093751.docx', '2025-11-15 17:44:58', '', 'جميل', NULL, NULL),
(13, 34, 'jjj', '1763290311_1763214298_1763201354_1762849980_submisson_041125093751.docx', '2025-11-16 14:51:51', 'مستلم', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainee_projects`
--

CREATE TABLE `trainee_projects` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('مستلم','تمت المراجعة','مرفوض') NOT NULL DEFAULT 'مستلم',
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainee_trainings`
--

CREATE TABLE `trainee_trainings` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trainee_trainings`
--

INSERT INTO `trainee_trainings` (`id`, `trainee_id`, `trainer_id`, `start_date`, `end_date`) VALUES
(3, 31, 0, '2025-11-13', '2025-12-16'),
(4, 31, 32, '2025-11-13', '2025-12-16');

-- --------------------------------------------------------

--
-- Table structure for table `training_requests`
--

CREATE TABLE `training_requests` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) NOT NULL,
  `request_type` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `pdf_file` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `submitted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `civil_id` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','trainee') NOT NULL DEFAULT 'trainee',
  `preferred_lang` enum('ar','en') DEFAULT 'ar',
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `national_id` varchar(20) DEFAULT NULL,
  `university` varchar(100) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `education_status` enum('طالب','خريج') DEFAULT 'طالب'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `civil_id`, `email`, `phone`, `password`, `role`, `preferred_lang`, `otp_code`, `otp_expiry`, `created_at`, `national_id`, `university`, `specialization`, `education_status`) VALUES
(31, 'مصطفى', NULL, 'collegeme23@gmail.com', NULL, '$2y$10$OWTXZPDngcNyHMWKUM4SGuUERrD9a.Qg4pB1ARkajqh6QpPa.WHZW', 'trainee', 'ar', '971541', '2025-11-15 15:02:46', '2025-11-13 18:02:07', NULL, NULL, NULL, 'طالب'),
(32, 'علي', NULL, 'ALayham@gmail.com', NULL, '$2y$10$u4DCHerDw3nngLHuvmnWluht/0wxKFO2D1Yn..Ezd5KXxUvDqa7XK', 'admin', 'ar', NULL, NULL, '2025-11-13 18:04:43', NULL, NULL, NULL, 'طالب'),
(33, 'مصطفى', NULL, 'reef@gmail.com', NULL, '$2y$10$htnZH1sEyveaiz06saEJR.HTl0MzfbA2cO2ODGUaOHaxVowzsRl3a', 'trainee', 'ar', NULL, NULL, '2025-11-16 10:44:59', NULL, NULL, NULL, 'طالب');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`trainee_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `trainee_projects`
--
ALTER TABLE `trainee_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `trainee_trainings`
--
ALTER TABLE `trainee_trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `training_requests`
--
ALTER TABLE `training_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `trainee_projects`
--
ALTER TABLE `trainee_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainee_trainings`
--
ALTER TABLE `trainee_trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_requests`
--
ALTER TABLE `training_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainee_projects`
--
ALTER TABLE `trainee_projects`
  ADD CONSTRAINT `fk_trainee` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainee_trainings`
--
ALTER TABLE `trainee_trainings`
  ADD CONSTRAINT `trainee_trainings_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_requests`
--
ALTER TABLE `training_requests`
  ADD CONSTRAINT `training_requests_ibfk_1` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
