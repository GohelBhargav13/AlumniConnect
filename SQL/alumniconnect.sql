-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2026 at 10:04 AM
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
(1, 'Bhargav', 'gohelbhargav401@gmail.com', '$2y$10$rV1YIjOT4Fnuknip/pR5GO9Va71MlFP1rgg1Tw1/UnAoNJmiMnuV2', '2026-01-17 18:05:39', '2026-07-02 18:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `alumnimaster`
--

CREATE TABLE `alumnimaster` (
  `alumni_id` int(11) NOT NULL,
  `Enrollment_No` bigint(20) NOT NULL,
  `alumni_name` varchar(255) NOT NULL,
  `alumni_bio` varchar(100) DEFAULT NULL,
  `alumni_email` varchar(255) NOT NULL,
  `alumni_add_year` int(11) NOT NULL,
  `alumni_pass_year` int(11) NOT NULL,
  `alumni_phone_no` bigint(20) NOT NULL,
  `alumni_password` varchar(255) NOT NULL,
  `alumni_department` enum('computer Engineering','information technology') NOT NULL DEFAULT 'computer Engineering',
  `alumni_college` enum('GEC MODASA') NOT NULL,
  `alumni_company_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `alumni_githublink` varchar(255) DEFAULT NULL,
  `alumni_linkedIn` varchar(255) DEFAULT NULL,
  `alumni_city` varchar(100) DEFAULT NULL,
  `req_status` enum('pending','accepted','rejected','') NOT NULL DEFAULT 'pending',
  `ID_Card` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumnimaster`
--

INSERT INTO `alumnimaster` (`alumni_id`, `Enrollment_No`, `alumni_name`, `alumni_bio`, `alumni_email`, `alumni_add_year`, `alumni_pass_year`, `alumni_phone_no`, `alumni_password`, `alumni_department`, `alumni_college`, `alumni_company_name`, `created_at`, `alumni_githublink`, `alumni_linkedIn`, `alumni_city`, `req_status`, `ID_Card`) VALUES
(1, 216270307036, 'Nayan Gohel', 'i am software developer', 'nayan@gamil.com', 2020, 2024, 2147483647, '$2y$10$HtaeYRomv9RQ2ybhKh4QNOM6A8NujAPAaEfwCpXHx0ET510uqI0N2', 'computer Engineering', 'GEC MODASA', 'ISO', '2025-07-20 13:15:30', 'https://github.com/aryalodhari', 'https://www.linkedin.com/in/arya-lodhari-842679336/', 'porbandar', 'accepted', ''),
(2, 216270307064, 'Arya lodhari', 'I am python developer.', 'gohelbhargav02@gmail.com', 2025, 2028, 9974265344, '$2y$10$BsEnybRLm4O/b5OaimYZw.nrIZAuzE5L2O0NxGl/h7zYunOVdoPqC', 'computer Engineering', 'GEC MODASA', 'ISO', '2025-07-20 17:48:56', 'https://github.com/aryalodhari', 'https://www.linkedin.com/in/bhargav-gohel-968919303/', 'porbandar', 'accepted', '');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_student_master`
--

CREATE TABLE `alumni_student_master` (
  `alumni_id` int(11) NOT NULL,
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

INSERT INTO `alumni_student_master` (`alumni_id`, `email`, `passout_year`, `enrollment_no`, `branch`, `password_hash`, `is_registered`, `created_at`, `updated_at`) VALUES
(1, 'aarav.patel@example.com', 2022, '22CE001', 'Computer Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(2, 'bhargav.gohel@example.com', 2023, '23CE015', 'Computer Engineering', '$2y$10$dc5ZEGL.R.EkuehM3lZcXO3.a/QMs/b6Bqi2rO7U6HeOg1u383uUm', 1, '2026-07-02 15:25:11', '2026-07-02 19:56:27'),
(3, 'kinjal.shah@example.com', 2021, '21IT045', 'Information Technology', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(4, 'riya.mehta@example.com', 2020, '20EC032', 'Electronics & Communication', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(5, 'dev.joshi@example.com', 2024, '24ME010', 'Mechanical Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(6, 'nisha.desai@example.com', 2019, '19CV078', 'Civil Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(7, 'yash.trivedi@example.com', 2018, '18EE056', 'Electrical Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(8, 'priya.modi@example.com', 2022, '22CE089', 'Computer Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(9, 'harsh.rana@example.com', 2023, '23IT021', 'Information Technology', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11'),
(10, 'sneha.parmar@example.com', 2021, '21CE067', 'Computer Engineering', NULL, 0, '2026-07-02 15:25:11', '2026-07-02 15:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `applystudentmaster`
--

CREATE TABLE `applystudentmaster` (
  `app_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `apply_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applystudentmaster`
--

INSERT INTO `applystudentmaster` (`app_id`, `student_id`, `post_id`, `apply_at`) VALUES
(3, 5, 8, '2026-01-17 12:47:08');

-- --------------------------------------------------------

--
-- Table structure for table `connectionmaster`
--

CREATE TABLE `connectionmaster` (
  `conn_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `connection_status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `connectionmaster`
--

INSERT INTO `connectionmaster` (`conn_id`, `sender_id`, `receiver_id`, `connection_status`) VALUES
(13, 5, 2, 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `postmaster`
--

CREATE TABLE `postmaster` (
  `post_id` int(11) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `post_desc` varchar(255) NOT NULL,
  `post_location` varchar(255) NOT NULL,
  `post_ref_link` varchar(255) NOT NULL,
  `post_req_skill` varchar(255) NOT NULL,
  `post_job_type` enum('Internship','Job','Part-time') DEFAULT NULL,
  `post_ded_roadmap` varchar(255) NOT NULL,
  `post_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postmaster`
--

INSERT INTO `postmaster` (`post_id`, `post_title`, `post_desc`, `post_location`, `post_ref_link`, `post_req_skill`, `post_job_type`, `post_ded_roadmap`, `post_created_at`, `created_by`) VALUES
(8, 'Frontend Intern', 'This is internship for the frontend dev for make a dynamic website using above Skills', 'Ahemdabad ', 'https://www.bing.com/search?q=frontend+internship&FORM=AWRE', 'HTML,CSS,JS,REACT JS,VUE JS', 'Internship', 'This is dedicated roadmap for this internship', '2025-07-28 16:04:29', 2),
(9, 'Frontend Intern', 'This is special for that student that having above skills\r\nfor getting this internship ', 'Ahemdabad ', 'https://www.bing.com/search?q=frontend+internship&FORM=AWRE', 'HTML,CSS,JS,REACT JS,VUE JS, Angular JS', 'Internship', 'This is the roadmap for this', '2025-07-29 13:58:20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `studentmaster`
--

CREATE TABLE `studentmaster` (
  `student_id` int(11) NOT NULL,
  `Enrollment_no` bigint(20) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_phone_no` bigint(20) NOT NULL,
  `student_add_year` int(4) NOT NULL,
  `student_pass_year` int(4) NOT NULL,
  `student_bio` varchar(255) NOT NULL,
  `student_password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_github` varchar(255) NOT NULL,
  `student_linkedIn` varchar(255) NOT NULL,
  `student_city` varchar(255) NOT NULL,
  `student_department` varchar(255) NOT NULL,
  `student_college` varchar(255) NOT NULL,
  `req_status` enum('pending','accepted','rejected','') NOT NULL DEFAULT 'pending',
  `ID_Card` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentmaster`
--

INSERT INTO `studentmaster` (`student_id`, `Enrollment_no`, `student_name`, `student_email`, `student_phone_no`, `student_add_year`, `student_pass_year`, `student_bio`, `student_password`, `created_at`, `student_github`, `student_linkedIn`, `student_city`, `student_department`, `student_college`, `req_status`, `ID_Card`) VALUES
(4, 216270307017, 'Chhayank Thanki', 'bhargavgoheldev@gmail.com', 123456789, 2024, 2027, '', '$2y$10$AifEZiM2r6jO/Zsk6ZY74.Bqcu9YiPPqSDC2.zmVB5Yz6zrMTPPy.', '2025-07-28 08:15:54', '', 'https://www.linkedin.com/in/bhargav-gohel-968919303/', '', 'Computer Engineering', 'GEC MODASA', 'accepted', NULL),
(5, 216270307013, 'Bhargav Gohel', 'gohelbhargav401@gmail.com', 7621828163, 2021, 2024, '', '$2y$10$Ew45p9MUkIBDNlPxo9MZFOVX1kNRYNM8oUW54i.LXDM7xHqRUmqVy', '2026-01-17 12:45:28', 'https://github.com/GohelBhargav13', 'https://linkedin.com/GohelBhargav13', 'porbandar', 'Computer Engineering', 'GEC MODASA', 'accepted', '216270307013_idcard.pdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminmaster`
--
ALTER TABLE `adminmaster`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `alumnimaster`
--
ALTER TABLE `alumnimaster`
  ADD PRIMARY KEY (`alumni_id`),
  ADD UNIQUE KEY `Enrollment_No` (`Enrollment_No`);

--
-- Indexes for table `alumni_student_master`
--
ALTER TABLE `alumni_student_master`
  ADD PRIMARY KEY (`alumni_id`),
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`);

--
-- Indexes for table `applystudentmaster`
--
ALTER TABLE `applystudentmaster`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `connectionmaster`
--
ALTER TABLE `connectionmaster`
  ADD PRIMARY KEY (`conn_id`);

--
-- Indexes for table `postmaster`
--
ALTER TABLE `postmaster`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `studentmaster`
--
ALTER TABLE `studentmaster`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `Enrollment_no` (`Enrollment_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumnimaster`
--
ALTER TABLE `alumnimaster`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `alumni_student_master`
--
ALTER TABLE `alumni_student_master`
  MODIFY `alumni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `applystudentmaster`
--
ALTER TABLE `applystudentmaster`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `connectionmaster`
--
ALTER TABLE `connectionmaster`
  MODIFY `conn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `postmaster`
--
ALTER TABLE `postmaster`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `studentmaster`
--
ALTER TABLE `studentmaster`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `postmaster`
--
ALTER TABLE `postmaster`
  ADD CONSTRAINT `postmaster_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `alumnimaster` (`alumni_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
