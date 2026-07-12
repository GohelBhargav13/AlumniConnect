-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Generation Time: Jul 12, 2026 at 07:45 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42342177_alumniconnect`
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
(1, 'Bhargav', 'gohelbhargav401@gmail.com', '$2y$10$6pd/eG3hfzg3ZB9y3CIsqOroShy4OxADnvVY2BKZrkUjgLjTJtRUi', '2026-01-17 18:05:39', '2026-07-06 08:45:28'),
(2, 'Arya', 'aryal@gmail.com', '$2y$10$d11f2OFoIyZr25J9gUpxs.wPMTUph4hqj9rCgSgRhSFQnPBHdrhqu', '2026-07-06 05:34:23', '2026-07-06 12:35:36'),
(3, 'Nayan', 'nayang@gmail.com', '$2y$10$aVc9SUG7BXnMo7qM3NcYfuioSuXmexJZYFW/Il90Km6llQBDY225i', '2026-07-06 05:34:23', '2026-07-06 12:36:33'),
(4, 'Bhushan', 'bhushanj@gmail.com', '$2y$10$4gPwUXdWe28eLO/0jNoBN.K5JW8PWzgS1D.xmW5ygkjoWIQZw8gYa', '2026-07-06 05:34:50', '2026-07-06 12:37:41'),
(5, 'Avinash', 'avinashc@gmail.com', '$2y$10$KqdKqep6A0sTq6dT4xuBi.3kl16CyMmOtaz7WoPPOxP6IWwLteTR6', '2026-07-12 04:12:49', '2026-07-12 11:25:55');

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
(21, 'Avinash Choudhary', 'avinash.choudhary@example.com', 2026, '240163107001', 'Computer Engineering', NULL, 0, '2026-07-12 10:48:57', '2026-07-12 11:17:59'),
(22, 'Bhargav Gohel', 'bhargav.gohel@example.com', 2026, '240163107002', 'Computer Engineering', NULL, 0, '2026-07-12 10:48:57', '2026-07-12 11:17:56'),
(23, 'Kinjal Shah', 'kinjal.shah@example.com', 2026, '240163107003', 'Information Technology', NULL, 0, '2026-07-12 10:48:57', '2026-07-12 11:17:52'),
(24, 'Nayan Gohel', 'nayan.gohel@example.com', 2026, '240163107004', 'Computer Engineering', '$2y$10$XdzyeUEKjonYAXL2c3T9A.KmWUalzHovEa0Cbv3xCwknuYXmruCIm', 1, '2026-07-12 10:48:57', '2026-07-12 11:18:21'),
(25, 'Bhushan Joshi', 'bhushan.joshi@example.com', 2026, '240163107005', 'Computer Engineering', NULL, 0, '2026-07-12 10:48:57', '2026-07-12 11:18:03'),
(26, 'Arya Lodhari', 'arya.lodhari@example.com', 2026, '240163107006', 'Information Technology', NULL, 0, '2026-07-12 10:48:57', '2026-07-12 11:18:05');

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
(1, 'Sem Exam Schedule', 'This is the exam start schedule for mid exams.', 'Important', '2026-07-07', NULL, 1, '2026-07-04 20:18:07', '2026-07-04 20:18:07');

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
(2, 'Alumni Meet 2026', 'An Alumnimeet for the 2026 passout year studentss.', 'CE/IT department', '2026-07-07', '10:00:00', NULL, 1, '2026-07-04 11:16:08', '2026-07-07 18:44:56');

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
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `alumni_student_master`
--
ALTER TABLE `alumni_student_master`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
