-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2026 at 10:29 AM
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
-- Database: `alumniconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminmaster`
--

CREATE TABLE `adminmaster` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_At` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminmaster`
--

INSERT INTO `adminmaster` (`admin_id`, `admin_name`, `admin_email`, `password`, `created_At`, `update_at`) VALUES
(1, 'Bhargav', 'gohelbhargav401@gmail.com', '$2y$10$qWAJ3w517JSQX8BLpFVHNuqOpkzQW/WoWgYdaJCzT/JonsXJ4gWVC', '2026-01-17 18:05:39', '2026-07-03 17:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_profile`
--

CREATE TABLE `alumni_profile` (
  `p_id` int(11) NOT NULL,
  `alumni_id` int(11) DEFAULT NULL,
  `alumni_phone_no` varchar(255) DEFAULT NULL,
  `alumni_address` varchar(255) DEFAULT NULL,
  `alumni_batch` varchar(255) DEFAULT NULL,
  `alumni_company` varchar(255) DEFAULT NULL,
  `alumni_designation` varchar(255) DEFAULT NULL,
  `alumni_city` varchar(255) DEFAULT NULL,
  `alumni_linkedin_link` varchar(255) DEFAULT NULL,
  `alumni_github_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_profile`
--

INSERT INTO `alumni_profile` (`p_id`, `alumni_id`, `alumni_phone_no`, `alumni_address`, `alumni_batch`, `alumni_company`, `alumni_designation`, `alumni_city`, `alumni_linkedin_link`, `alumni_github_link`, `created_at`, `updated_at`) VALUES
(14, 12, '01234567890', '', '2019', 'eSparkbiz', 'Software Engineer', 'porbandar', 'https://www.linkedin.com/in/bhargav-gohel-968919303', 'https://github.com/GohelBhargav13', '2026-07-05 08:17:15', '2026-07-05 08:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_student_master`
--

CREATE TABLE `alumni_student_master` (
  `alumni_id` int(11) NOT NULL,
  `alumni_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passout_year` int(11) NOT NULL,
  `enrollment_no` varchar(30) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `is_registered` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_student_master`
--

INSERT INTO `alumni_student_master` (`alumni_id`, `alumni_name`, `email`, `passout_year`, `enrollment_no`, `branch`, `password_hash`, `is_registered`, `created_at`, `updated_at`) VALUES
(11, 'Aarav Patel', 'aarav.patel@example.com', 2022, '22CE001', 'Computer Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(12, 'Bhargav Gohel', 'bhargav.gohel@example.com', 2023, '23CE015', 'Computer Engineering', '$2y$10$T9qXl92xXVT0btPEnolQpeEFiJzOJSmX52ZmkoiOcuNO3YFG8xb5q', 1, '2026-07-05 08:10:08', '2026-07-05 08:16:29'),
(13, 'Kinjal Shah', 'kinjal.shah@example.com', 2021, '21IT045', 'Information Technology', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(14, 'Riya Mehta', 'riya.mehta@example.com', 2020, '20EC032', 'Electronics & Communication', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(15, 'Dev Joshi', 'dev.joshi@example.com', 2024, '24ME010', 'Mechanical Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(16, 'Nisha Desai', 'nisha.desai@example.com', 2019, '19CV078', 'Civil Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(17, 'Yash Trivedi', 'yash.trivedi@example.com', 2018, '18EE056', 'Electrical Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(18, 'Priya Modi', 'priya.modi@example.com', 2022, '22CE089', 'Computer Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(19, 'Harsh Rana', 'harsh.rana@example.com', 2023, '23IT021', 'Information Technology', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08'),
(20, 'Sneha Parmar', 'sneha.parmar@example.com', 2021, '21CE067', 'Computer Engineering', NULL, 0, '2026-07-05 08:10:08', '2026-07-05 08:10:08');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_master`
--

CREATE TABLE `announcement_master` (
  `anno_id` int(11) NOT NULL,
  `anno_title` varchar(255) NOT NULL,
  `anno_desc` varchar(255) NOT NULL,
  `anno_type` enum('Normal','Important','Urgent','') NOT NULL DEFAULT 'Normal',
  `anno_show_until` date NOT NULL,
  `anno_additional_links` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_master`
--

INSERT INTO `announcement_master` (`anno_id`, `anno_title`, `anno_desc`, `anno_type`, `anno_show_until`, `anno_additional_links`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Sem Exam Schedule', 'This is the exam start schedule for mid exams.', 'Important', '2026-07-07', NULL, 1, '2026-07-04 20:18:07', '2026-07-04 20:18:07'),
(2, 'Sem Exam Schedule - 2025', 'This is exam schedule for the 2025 mid sems', 'Normal', '2025-07-05', NULL, 1, '2026-07-04 20:41:42', '2026-07-04 20:43:13'),
(3, 'Sports Event - 2024', 'The Sports event in the college for the students.', 'Normal', '2024-06-03', NULL, 1, '2026-07-04 20:42:20', '2026-07-04 20:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `event_master`
--

CREATE TABLE `event_master` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_desc` varchar(255) DEFAULT NULL,
  `event_venue` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `event_additional_links` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_master`
--

INSERT INTO `event_master` (`event_id`, `event_name`, `event_desc`, `event_venue`, `event_date`, `event_time`, `event_additional_links`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Alumni Meet 2026', 'An Alumni meet for passout students', 'CE/IT department', '2026-07-05', '11:00:00', NULL, 1, '2026-07-04 11:16:08', '2026-07-04 11:16:08'),
(3, 'Alumni Meet 2025', 'An alumni meet for the passout students', 'CE/IT department', '2025-07-05', '11:00:00', NULL, 1, '2026-07-04 12:04:25', '2026-07-04 12:05:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminmaster`
--
ALTER TABLE `adminmaster`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `alumni_student_master`
--
ALTER TABLE `alumni_student_master`
  ADD PRIMARY KEY (`alumni_id`),
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`);

--
-- Indexes for table `announcement_master`
--
ALTER TABLE `announcement_master`
  ADD PRIMARY KEY (`anno_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `event_master`
--
ALTER TABLE `event_master`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `alumni_student_master`
--
ALTER TABLE `alumni_student_master`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `announcement_master`
--
ALTER TABLE `announcement_master`
  MODIFY `anno_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_master`
--
ALTER TABLE `event_master`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  ADD CONSTRAINT `alumni_profile_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumni_student_master` (`alumni_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcement_master`
--
ALTER TABLE `announcement_master`
  ADD CONSTRAINT `announcement_master_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `adminmaster` (`admin_id`);

--
-- Constraints for table `event_master`
--
ALTER TABLE `event_master`
  ADD CONSTRAINT `event_master_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `adminmaster` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
