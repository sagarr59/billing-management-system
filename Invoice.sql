-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 04:54 PM
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
-- Database: `invoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'admin', '$2y$10$joE.b3w.YHN64IzBMXEY6OTOn5J3hjxdd..rw178maiRRnnkMoRbO');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_data` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `issued_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `invoice_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `reference_no`, `client_name`, `address`, `total_amount`, `discount`, `net_amount`, `issued_date`, `invoice_number`) VALUES
(2, 'REF-E2AEBA', 'Sagar Enterprises', 'Tithana, Chandragiri-15, Kathmandu', 25000.00, 2000.00, 23000.00, '2025-04-16 18:15:00', 'AI-1744866958'),
(5, 'REF-15E6C7', 'Azone IT Hub', 'kalanki', 9000.00, 2200.00, 6800.00, '2025-04-16 18:15:00', 'AI-1744869585'),
(8, 'REF-22DE5D', 'Sagar IT Solutions', 'Naikap, KTM', 25500.00, 1500.00, 24000.00, '2025-04-18 18:15:00', 'AI-1745047922'),
(9, 'REF-76B581', 'Sagar IT Solutions', 'Naikap, KTM', 25500.00, 1500.00, 24000.00, '2025-04-18 18:15:00', 'AI-1745047927'),
(10, 'REF-9BC952', 'Sagar IT Solutions', 'Tinthana', 32000.00, 2000.00, 30000.00, '2025-04-18 18:15:00', 'AI-1745049561'),
(11, 'REF-F9E2F4', 'Sagar IT HUB', 'Tinthana', 29000.00, 1000.00, 28000.00, '2025-04-18 18:15:00', 'AI-1745049727');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_deletion_log`
--

CREATE TABLE `invoice_deletion_log` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `remarks` text NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp(),
  `invoice_number` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_deletion_log`
--

INSERT INTO `invoice_deletion_log` (`id`, `invoice_id`, `remarks`, `deleted_at`, `invoice_number`, `reference_no`, `client_name`, `issued_date`, `total_amount`, `discount`, `net_amount`) VALUES
(1, 1, 'settled', '2025-04-17 12:29:44', 'AI-1744866152', 'REF-843A22', 'Sagar', '2025-04-18', 30200.00, 200.00, 30000.00),
(2, 7, 'wrong', '2025-04-17 14:17:45', 'AI-1744873129', 'REF-9D2466', 'Hari Company', '2025-04-16', 33000.00, 3000.00, 30000.00),
(3, 6, 'Mistake', '2025-04-19 13:26:39', 'AI-1744872986', 'REF-ABBA25', 'Hari Company', '2025-04-16', 33000.00, 3000.00, 30000.00),
(4, 3, 'mistake ', '2025-04-19 13:30:36', 'AI-1744869302', 'REF-694509', 'Sagar Enterprises Co.', '2025-04-12', 9999.00, 0.00, 9999.00),
(5, 4, 'wrong date', '2025-04-19 13:47:52', 'AI-1744869414', 'REF-67498F', 'Sagar Enterprises', '2025-04-21', 9999.00, 0.00, 9999.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_edit_log`
--

CREATE TABLE `invoice_edit_log` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `old_client_name` varchar(255) DEFAULT NULL,
  `old_issued_date` date DEFAULT NULL,
  `old_total` decimal(10,2) DEFAULT NULL,
  `old_discount` decimal(10,2) DEFAULT NULL,
  `old_net` decimal(10,2) DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_edit_log`
--

INSERT INTO `invoice_edit_log` (`id`, `invoice_id`, `invoice_number`, `reference_no`, `old_client_name`, `old_issued_date`, `old_total`, `old_discount`, `old_net`, `edited_by`, `edited_at`) VALUES
(1, 3, 'AI-1744869302', 'REF-694509', 'Sagar Enterprises', '2025-04-21', 9999.00, 0.00, 9999.00, 1, '2025-04-19 13:25:34'),
(2, 5, 'AI-1744869585', 'REF-15E6C7', 'Ram ', '2025-04-17', 9000.00, 2200.00, 6800.00, 1, '2025-04-19 13:26:06'),
(3, 11, 'AI-1745049727', 'REF-F9E2F4', 'Sagar IT Solutions', '2025-04-18', 29000.00, 2000.00, 27000.00, 1, '2025-04-19 13:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_logs`
--

CREATE TABLE `invoice_logs` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_particulars`
--

CREATE TABLE `invoice_particulars` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_particulars`
--

INSERT INTO `invoice_particulars` (`id`, `invoice_id`, `description`, `amount`, `created_at`) VALUES
(3, 2, 'Wordpress Website Development', 25000.00, '2025-04-17 05:15:58'),
(6, 5, 'software', 9000.00, '2025-04-17 05:59:45'),
(9, 8, 'Website Design', 21500.00, '2025-04-19 07:32:02'),
(10, 8, 'SEO Optimization', 4000.00, '2025-04-19 07:32:02'),
(11, 9, 'Website Design', 21500.00, '2025-04-19 07:32:07'),
(12, 9, 'SEO Optimization', 4000.00, '2025-04-19 07:32:07'),
(13, 10, 'Website Design', 26000.00, '2025-04-19 07:59:21'),
(14, 10, 'Maintenance', 6000.00, '2025-04-19 07:59:21'),
(15, 11, 'Wordpress Website', 25000.00, '2025-04-19 08:02:07'),
(16, 11, 'SEO Optimization', 4000.00, '2025-04-19 08:02:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `invoice_deletion_log`
--
ALTER TABLE `invoice_deletion_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_edit_log`
--
ALTER TABLE `invoice_edit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_logs`
--
ALTER TABLE `invoice_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoice_particulars`
--
ALTER TABLE `invoice_particulars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `invoice_deletion_log`
--
ALTER TABLE `invoice_deletion_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoice_edit_log`
--
ALTER TABLE `invoice_edit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice_logs`
--
ALTER TABLE `invoice_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_particulars`
--
ALTER TABLE `invoice_particulars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice_logs`
--
ALTER TABLE `invoice_logs`
  ADD CONSTRAINT `invoice_logs_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_particulars`
--
ALTER TABLE `invoice_particulars`
  ADD CONSTRAINT `invoice_particulars_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
