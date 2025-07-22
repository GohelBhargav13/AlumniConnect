-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 07:47 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
  `alumni_city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumnimaster`
--

INSERT INTO `alumnimaster` (`alumni_id`, `Enrollment_No`, `alumni_name`, `alumni_bio`, `alumni_email`, `alumni_add_year`, `alumni_pass_year`, `alumni_phone_no`, `alumni_password`, `alumni_department`, `alumni_college`, `alumni_company_name`, `created_at`, `alumni_githublink`, `alumni_linkedIn`, `alumni_city`) VALUES
(1, 216270307036, 'Nayan Gohel', 'i am software developer', 'nayan@gamil.com', 2020, 2024, 2147483647, '$2y$10$HtaeYRomv9RQ2ybhKh4QNOM6A8NujAPAaEfwCpXHx0ET510uqI0N2', 'computer Engineering', 'GEC MODASA', 'ISO', '2025-07-20 13:15:30', 'https://github.com/aryalodhari', 'https://www.linkedin.com/in/arya-lodhari-842679336/', 'porbandar'),
(2, 216270307064, 'Arya lodhari', 'i am application developer', 'aryalodhari224@gmaiil.com', 2025, 2028, 9974265344, '$2y$10$BsEnybRLm4O/b5OaimYZw.nrIZAuzE5L2O0NxGl/h7zYunOVdoPqC', 'computer Engineering', 'GEC MODASA', 'ISO', '2025-07-20 17:48:56', 'https://github.com/aryalodhari', '', '');

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
(1, 'Backend Intern', 'qweqwrwrwrwfs', 'Ahemdabad - remote', 'http://localhost:3000', 'JS,PYTHON,PHP', 'Job', 'qrqrwqrrwerdasda', '2025-07-22 13:52:29', 2);

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
  `student_college` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentmaster`
--

INSERT INTO `studentmaster` (`student_id`, `Enrollment_no`, `student_name`, `student_email`, `student_phone_no`, `student_add_year`, `student_pass_year`, `student_bio`, `student_password`, `created_at`, `student_github`, `student_linkedIn`, `student_city`, `student_department`, `student_college`) VALUES
(2, 216270307013, 'Bhargav Gohel', 'gohelbhargav401@gmail.com', 7621828163, 2021, 2024, 'i am MERN stack developer..', '$2y$10$IsZN/T.If5fI/HYc5yx03u8qMucTKY49.nHkbcSt9gYOA7OSO3cee', '2025-07-19 11:41:59', 'https://github.com/GohelBhargav13', '', 'porbanda', 'Computer Engineering', 'GEC MODASA');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumnimaster`
--
ALTER TABLE `alumnimaster`
  ADD PRIMARY KEY (`alumni_id`),
  ADD UNIQUE KEY `Enrollment_No` (`Enrollment_No`);

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
-- AUTO_INCREMENT for table `postmaster`
--
ALTER TABLE `postmaster`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `studentmaster`
--
ALTER TABLE `studentmaster`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
