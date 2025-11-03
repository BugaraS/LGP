-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 06:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lgp`
--

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `college` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `laptop_type` varchar(100) DEFAULT NULL,
  `laptop_serial` varchar(100) DEFAULT NULL,
  `qr_code` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `fname`, `mname`, `lname`, `college`, `position`, `phone`, `photo`, `laptop_type`, `laptop_serial`, `qr_code`, `username`) VALUES
(12255555, 'ASasaa', 'asas', 'Teseteas', 'College of Business & Economics', 'as', '+251987146615', '', 'Delli', 'we123', 'retert4564623', 'User'),
(23232323, 'ASas', 'BB', 'asddas', 'School of Computing Science', 'dd', '+251987146615', '', 'Dell', 'we123', '000002222', 'Admin'),
(236548888, 'Bogale', 'Sddfdf', 'Shigult', 'School of Computing Science', 'dsfsdfds', '+251987146615', '', 'dsfdsf', '31sdada', '123sadasdsadsadsa', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `college` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `laptop_type` varchar(100) DEFAULT NULL,
  `laptop_serial` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `fname`, `mname`, `lname`, `college`, `department`, `year`, `phone`, `photo`, `laptop_type`, `laptop_serial`, `qr_code`) VALUES
(15, 'DawitWW', 'dsf', 'Abiye', 'Computing', 'IS', 4, '0987146615', '', 'Dell', '12312qweq34', '13132134234345'),
(21, 'AAAAAAAAAAA', 'BBBBBAA', 'CCCCCAA', 'computing', 'Information Systems', 4, '+251987146615234223', '1761200056_1.PNG', 'Delll', 'de123333323', 'erwerwe123212');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`id`, `fullname`, `username`, `password`, `email`, `phone`, `position`, `role`, `created_at`) VALUES
(2, 'Abebe Kebede', 'User', '123', 'Bogaleshigult@gmail.com', '0987146615', 'dd', 'user', '2025-09-07 11:32:39'),
(3, 'Best', 'Admin', '123', 'Bogaleshigult@gmail.com', '0987146615', 'ddsds', 'admin', '2025-09-07 11:38:00'),
(5, 'AAA', 'AdminT', '$2y$10$YwAK/xLhQSarjLs6P2Bzkei98CpTdkAEy9CEnkqSa1qksx/sgWCaO', 'belaytesete@gmail.com', '0987146615', '1qwe', 'admin', '2025-10-23 17:13:18'),
(8, 'Test Test TestW', 'Administrator', '123', 'belaytesete@gmail.com', '0987146615', 'Security', 'admin', '2025-10-23 17:29:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
