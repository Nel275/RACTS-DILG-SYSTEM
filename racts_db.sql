-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2026 at 09:29 AM
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
-- Database: `racts_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `log_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `log_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Recorded \"Kasambahay Report\" for Centro West — March 2026 (Submitted (On Time)).', '2026-09-24 19:39:57', '2026-09-24 11:39:57', '2026-09-24 11:39:57'),
(2, 1, 'Updated \"Kasambahay Report\" for Centro West — September 2026 (Submitted (On Time)).', '2026-09-25 04:54:26', '2026-09-24 20:54:26', '2026-09-24 20:54:26'),
(3, 1, 'Updated \"Kasambahay Report\" for Villasis — September 2026 (Submitted (On Time)).', '2026-09-25 04:59:11', '2026-09-24 20:59:11', '2026-09-24 20:59:11'),
(4, 1, 'Cleared \"Accomplishment\" for Villasis — September 2026.', '2026-09-25 05:09:33', '2026-09-24 21:09:33', '2026-09-24 21:09:33'),
(5, 1, 'Cleared \"Kasambahay Report\" for Calao East — September 2026.', '2026-09-25 05:10:54', '2026-09-24 21:10:54', '2026-09-24 21:10:54'),
(6, 1, 'Updated \"Kasambahay Report\" for Centro West — September 2026 (Submitted (On Time)).', '2026-09-25 05:10:57', '2026-09-24 21:10:57', '2026-09-24 21:10:57'),
(7, 1, 'Cleared \"Kasambahay Report\" for Centro West — September 2026.', '2026-09-25 05:11:06', '2026-09-24 21:11:06', '2026-09-24 21:11:06'),
(8, 1, 'Cleared \"Kasambahay Report\" for Villasis — September 2026.', '2026-09-25 05:11:13', '2026-09-24 21:11:13', '2026-09-24 21:11:13'),
(9, 1, 'Cleared \"Kasambahay Report\" for Centro West — August 2026.', '2026-09-25 05:11:37', '2026-09-24 21:11:37', '2026-09-24 21:11:37'),
(10, 1, 'Recorded \"Kasambahay Report\" for Villasis — August 2026 (not yet graded).', '2026-09-25 05:12:03', '2026-09-24 21:12:03', '2026-09-24 21:12:03'),
(11, 1, 'Cleared \"Kasambahay Report\" for Villasis — August 2026.', '2026-09-25 05:13:41', '2026-09-24 21:13:41', '2026-09-24 21:13:41'),
(12, 1, 'Recorded \"Kasambahay Report\" for Calao East — August 2026 (not yet graded).', '2026-09-25 05:14:17', '2026-09-24 21:14:17', '2026-09-24 21:14:17'),
(13, 1, 'Recorded \"Kasambahay Report\" for Villasis — August 2026 (not yet graded).', '2026-09-25 05:30:11', '2026-09-24 21:30:11', '2026-09-24 21:30:11'),
(14, 1, 'Recorded \"Accomplishment\" for Villasis — August 2026 (not yet graded).', '2026-09-25 05:32:21', '2026-09-24 21:32:21', '2026-09-24 21:32:21'),
(15, 1, 'Cleared \"Accomplishment\" for Villasis — August 2026.', '2026-09-25 05:55:23', '2026-09-24 21:55:23', '2026-09-24 21:55:23'),
(16, 1, 'Recorded \"Accomplishment\" for Centro West — August 2026 (not yet graded).', '2026-09-25 05:55:49', '2026-09-24 21:55:49', '2026-09-24 21:55:49'),
(17, 1, 'Cleared \"Accomplishment\" for Centro West — August 2026.', '2026-09-25 06:08:14', '2026-09-24 22:08:14', '2026-09-24 22:08:14'),
(18, 1, 'Recorded \"Accomplishment\" for Centro West — September 2026 (not yet graded).', '2026-09-25 06:09:23', '2026-09-24 22:09:23', '2026-09-24 22:09:23'),
(19, 1, 'Cleared \"Accomplishment\" for Centro West — September 2026.', '2026-09-25 06:09:39', '2026-09-24 22:09:39', '2026-09-24 22:09:39'),
(20, 1, 'Recorded \"Accomplishment\" for Centro West — September 2026 (not yet graded).', '2026-09-25 06:09:48', '2026-09-24 22:09:48', '2026-09-24 22:09:48'),
(21, 1, 'Cleared \"Accomplishment\" for Centro West — September 2026.', '2026-09-25 06:10:02', '2026-09-24 22:10:02', '2026-09-24 22:10:02'),
(22, 1, 'Recorded \"Accomplishment\" for Centro West — August 2026 (not yet graded).', '2026-09-25 06:10:18', '2026-09-24 22:10:18', '2026-09-24 22:10:18'),
(23, 1, 'Recorded \"Kasambahay Report\" for Calao East — September 2026 (not yet graded).', '2026-09-25 10:02:31', '2026-09-25 02:02:31', '2026-09-25 02:02:31'),
(24, 1, 'Updated \"Kasambahay Report\" for Calao East — September 2026 (not yet graded).', '2026-09-25 10:02:58', '2026-09-25 02:02:58', '2026-09-25 02:02:58'),
(25, 1, 'Updated \"Kasambahay Report\" for Calao East — August 2026 (not yet graded).', '2026-09-25 10:04:05', '2026-09-25 02:04:05', '2026-09-25 02:04:05'),
(26, 1, 'Recorded \"Katarungang Pambarangay Report\" for Calao East — August 2026 (not yet graded).', '2026-09-25 10:04:05', '2026-09-25 02:04:05', '2026-09-25 02:04:05'),
(27, 1, 'Updated \"Kasambahay Report\" for Calao East — August 2026 (not yet graded).', '2026-09-25 10:04:12', '2026-09-25 02:04:12', '2026-09-25 02:04:12'),
(28, 1, 'Updated \"Katarungang Pambarangay Report\" for Calao East — August 2026 (not yet graded).', '2026-09-25 10:04:12', '2026-09-25 02:04:12', '2026-09-25 02:04:12'),
(29, 1, 'Updated \"Kasambahay Report\" for Calao East — September 2026 (not yet graded).', '2026-09-25 16:56:01', '2026-09-25 08:56:01', '2026-09-25 08:56:01'),
(30, 1, 'Recorded \"SGBR\" for Calao East — 2026 (not yet graded).', '2026-09-25 16:56:01', '2026-09-25 08:56:01', '2026-09-25 08:56:01'),
(31, 1, 'Added barangay \"Patul\".', '2026-09-25 17:29:49', '2026-09-25 09:29:49', '2026-09-25 09:29:49'),
(32, 1, 'Recorded \"Kasambahay Report\" for Patul — September 2026 (not yet graded).', '2026-09-25 18:21:37', '2026-09-25 10:21:37', '2026-09-25 10:21:37'),
(33, 1, 'Updated \"Kasambahay Report\" for Patul — September 2026 (not yet graded).', '2026-09-25 18:21:38', '2026-09-25 10:21:38', '2026-09-25 10:21:38'),
(34, 1, 'Added barangay \"Malvar\".', '2026-09-25 18:22:48', '2026-09-25 10:22:48', '2026-09-25 10:22:48'),
(35, 1, 'Recorded \"Kasambahay Report\" for Malvar — August 2026 (not yet graded).', '2026-09-25 18:23:13', '2026-09-25 10:23:13', '2026-09-25 10:23:13'),
(36, 1, 'Recorded \"Katarungang Pambarangay Report\" for Malvar — September 2026 (Submitted (On Time)).', '2026-09-25 18:23:41', '2026-09-25 10:23:41', '2026-09-25 10:23:41'),
(37, 1, 'Updated \"Katarungang Pambarangay Report\" for Malvar — September 2026 (Submitted (On Time)).', '2026-09-25 18:25:34', '2026-09-25 10:25:34', '2026-09-25 10:25:34'),
(38, 1, 'Recorded \"Road Clearing Operation Report\" for Malvar — September 2026 (Late).', '2026-09-25 18:25:34', '2026-09-25 10:25:34', '2026-09-25 10:25:34'),
(39, 1, 'Added barangay log item \"Kasambahay Report\".', '2026-09-28 22:12:25', '2026-09-28 14:12:25', '2026-09-28 14:12:25'),
(40, 1, 'Updated \"Kasambahay Report\" for Calao East — September 2026 (not yet graded).', '2026-09-28 22:14:23', '2026-09-28 14:14:23', '2026-09-28 14:14:23'),
(41, 1, 'Recorded \"Katarungang Pambarangay Report\" for Calao East — September 2026 (not yet graded).', '2026-09-28 22:14:23', '2026-09-28 14:14:23', '2026-09-28 14:14:23'),
(42, 1, 'Updated \"SGBR\" for Calao East — 2026 (not yet graded).', '2026-09-28 22:14:23', '2026-09-28 14:14:23', '2026-09-28 14:14:23'),
(43, 1, 'Added barangay log entry \"Kasambahay Report\" for Calao East.', '2026-09-28 22:15:26', '2026-09-28 14:15:26', '2026-09-28 14:15:26'),
(44, 1, 'Added user log \"Gian Carlo  Ventura\".', '2026-09-28 22:18:25', '2026-09-28 14:18:25', '2026-09-28 14:18:25'),
(45, 1, 'Added barangay log entry \"Kasambahay Report\" for Malvar.', '2026-09-29 20:43:33', '2026-09-29 12:43:33', '2026-09-29 12:43:33'),
(46, 1, 'Recorded submission for \"Kasambahay Report\" (Malvar) - Submitted.', '2026-10-01 14:50:40', '2026-10-01 06:50:40', '2026-10-01 06:50:40'),
(47, 1, 'Recorded submission for \"Kasambahay Report\" (Malvar) - Submitted.', '2026-10-01 14:50:40', '2026-10-01 06:50:40', '2026-10-01 06:50:40'),
(48, 1, 'Filed report \"Kasambahay Report\" into Malvar\'s folder.', '2026-10-01 14:51:45', '2026-10-01 06:51:45', '2026-10-01 06:51:45'),
(49, 1, 'Deleted user log \"Gian Carlo  Ventura\".', '2026-10-01 15:24:05', '2026-10-01 07:24:05', '2026-10-01 07:24:05'),
(50, 1, 'Added user log \"Gian Carlo  Ventura\" with a login account.', '2026-10-01 15:25:45', '2026-10-01 07:25:45', '2026-10-01 07:25:45'),
(51, 3, 'Added barangay \"Rosario\".', '2026-10-01 15:34:16', '2026-10-01 07:34:16', '2026-10-01 07:34:16'),
(52, 3, 'Added barangay log entry \"Kasambahay Report\" for Malvar.', '2026-10-01 16:21:07', '2026-10-01 08:21:07', '2026-10-01 08:21:07'),
(53, 3, 'Added barangay log item \"Katarungang Pambarangay Report\".', '2026-10-01 16:36:33', '2026-10-01 08:36:33', '2026-10-01 08:36:33'),
(54, 3, 'Added barangay log entry \"Katarungang Pambarangay Report\" for Centro West.', '2026-10-01 16:36:52', '2026-10-01 08:36:52', '2026-10-01 08:36:52'),
(55, 3, 'Added barangay log entry \"Katarungang Pambarangay Report\" for Patul.', '2026-10-01 16:37:09', '2026-10-01 08:37:09', '2026-10-01 08:37:09'),
(56, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Centro West) - Submitted.', '2026-10-01 16:41:25', '2026-10-01 08:41:25', '2026-10-01 08:41:25'),
(57, 3, 'Filed report \"Katarungang Pambarangay Report\" into Centro West\'s folder.', '2026-10-01 16:41:30', '2026-10-01 08:41:30', '2026-10-01 08:41:30'),
(58, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Centro West) - Submitted.', '2026-10-01 16:41:33', '2026-10-01 08:41:33', '2026-10-01 08:41:33'),
(59, 1, 'Added user log \"Ed Jashley  Pasion\" with a login account.', '2026-10-01 16:47:40', '2026-10-01 08:47:40', '2026-10-01 08:47:40'),
(60, 4, 'Recorded submission for \"Katarungang Pambarangay Report\" (Patul) - Submitted.', '2026-10-01 16:48:55', '2026-10-01 08:48:55', '2026-10-01 08:48:55'),
(61, 2, 'Recorded submission for \"Katarungang Pambarangay Report\" (Centro West) - Submitted.', '2026-10-01 18:22:14', '2026-10-01 10:22:14', '2026-10-01 10:22:14'),
(62, 2, 'Recorded submission for \"Katarungang Pambarangay Report\" (Centro West) - Submitted.', '2026-10-01 18:22:17', '2026-10-01 10:22:17', '2026-10-01 10:22:17'),
(63, 2, 'Filed report \"Katarungang Pambarangay Report\" into Centro West\'s folder.', '2026-10-01 18:22:40', '2026-10-01 10:22:40', '2026-10-01 10:22:40'),
(64, 3, 'Added barangay \"Sinsayon\".', '2026-10-01 21:00:05', '2026-10-01 13:00:05', '2026-10-01 13:00:05'),
(65, 3, 'Updated user account \"Ed Pasion\" (DILG Monitoring Staff, username: EJ).', '2026-10-01 21:20:25', '2026-10-01 13:20:25', '2026-10-01 13:20:25'),
(66, 3, 'Added barangay log entry \"Katarungang Pambarangay Report\" for Sinsayon.', '2026-10-01 22:11:41', '2026-10-01 14:11:41', '2026-10-01 14:11:41'),
(67, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Sinsayon) - Submitted.', '2026-10-02 13:09:34', '2026-10-02 05:09:34', '2026-10-02 05:09:34'),
(68, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Sinsayon) - Submitted.', '2026-10-02 13:09:55', '2026-10-02 05:09:55', '2026-10-02 05:09:55'),
(69, 3, 'Added SK log entry \"Minutes of Meeting Report\" for Calao East.', '2026-10-02 13:38:12', '2026-10-02 05:38:12', '2026-10-02 05:38:12'),
(70, 3, 'Updated barangay \"Calao East\" (status: Active).', '2026-10-02 13:39:16', '2026-10-02 05:39:16', '2026-10-02 05:39:16'),
(71, 3, 'Updated barangay \"Centro West\" (status: Active).', '2026-10-02 13:39:19', '2026-10-02 05:39:19', '2026-10-02 05:39:19'),
(72, 3, 'Updated barangay \"Malvar\" (status: Active).', '2026-10-02 13:39:23', '2026-10-02 05:39:23', '2026-10-02 05:39:23'),
(73, 3, 'Updated barangay \"Patul\" (status: Active).', '2026-10-02 13:39:30', '2026-10-02 05:39:30', '2026-10-02 05:39:30'),
(74, 3, 'Updated barangay \"Rizal\" (status: Active).', '2026-10-02 13:39:35', '2026-10-02 05:39:35', '2026-10-02 05:39:35'),
(75, 3, 'Updated barangay \"Villasis\" (status: Active).', '2026-10-02 13:39:41', '2026-10-02 05:39:41', '2026-10-02 05:39:41'),
(76, 3, 'Recorded submission for \"Minutes of Meeting Report\" (Calao East) - Submitted.', '2026-10-02 13:40:30', '2026-10-02 05:40:30', '2026-10-02 05:40:30'),
(77, 3, 'Recorded submission for \"Minutes of Meeting Report\" (Calao East) - Submitted.', '2026-10-02 13:40:37', '2026-10-02 05:40:37', '2026-10-02 05:40:37'),
(78, 3, 'Recorded submission for \"Minutes of Meeting Report\" (Calao East) - Submitted.', '2026-10-02 13:41:01', '2026-10-02 05:41:01', '2026-10-02 05:41:01'),
(79, 3, 'Added barangay \"Victory Norte\".', '2026-10-02 21:10:26', '2026-10-02 13:10:26', '2026-10-02 13:10:26'),
(80, 3, 'Added Barangay log entry \"Kasambahay Report\" for Victory Norte.', '2026-10-02 21:11:50', '2026-10-02 13:11:50', '2026-10-02 13:11:50'),
(81, 3, 'Recorded submission for \"Kasambahay Report\" (Victory Norte) - Submitted.', '2026-10-02 21:12:38', '2026-10-02 13:12:38', '2026-10-02 13:12:38'),
(82, 3, 'Recorded submission for \"Kasambahay Report\" (Victory Norte) - Submitted.', '2026-10-02 21:13:01', '2026-10-02 13:13:01', '2026-10-02 13:13:01'),
(83, 3, 'Filed report \"Kasambahay Report\" into Victory Norte\'s folder.', '2026-10-02 21:13:20', '2026-10-02 13:13:20', '2026-10-02 13:13:20'),
(84, 3, 'Updated \"Kasambahay Report\" for Calao East — October 2026 (Submitted (On Time)).', '2026-10-02 21:18:18', '2026-10-02 13:18:18', '2026-10-02 13:18:18'),
(85, 3, 'Recorded \"Barangay Monthly Accomplishment Report\" for Calao East — October 2026 (Late).', '2026-10-02 21:18:18', '2026-10-02 13:18:18', '2026-10-02 13:18:18'),
(86, 3, 'Updated \"SGBR\" for Calao East — 2026 (not yet graded).', '2026-10-02 21:18:18', '2026-10-02 13:18:18', '2026-10-02 13:18:18'),
(87, 3, 'Updated \"Kasambahay Report\" for Calao East — August 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(88, 3, 'Updated \"Katarungang Pambarangay Report\" for Calao East — August 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(89, 3, 'Recorded \"Barangay Monthly Accomplishment Report\" for Calao East — August 2026 (Submitted (On Time)).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(90, 3, 'Updated \"SGBR\" for Calao East — 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(91, 3, 'Updated \"Kasambahay Report\" for Calao East — August 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(92, 3, 'Updated \"Katarungang Pambarangay Report\" for Calao East — August 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(93, 3, 'Updated \"Barangay Monthly Accomplishment Report\" for Calao East — August 2026 (Submitted (On Time)).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(94, 3, 'Updated \"SGBR\" for Calao East — 2026 (not yet graded).', '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42'),
(95, 3, 'Closed barangay log entry \"Kasambahay Report\" (Malvar).', '2026-10-02 21:20:34', '2026-10-02 13:20:34', '2026-10-02 13:20:34'),
(96, 3, 'Added barangay \"Mabini\".', '2026-10-02 21:33:35', '2026-10-02 13:33:35', '2026-10-02 13:33:35'),
(97, 3, 'Added barangay \"Victory Sur\".', '2026-10-02 21:33:53', '2026-10-02 13:33:53', '2026-10-02 13:33:53'),
(98, 3, 'Added Barangay log entry \"Katarungang Pambarangay Report\" for Victory Sur.', '2026-10-02 21:34:20', '2026-10-02 13:34:20', '2026-10-02 13:34:20'),
(99, 3, 'Added Barangay log entry \"Kasambahay Report\" for Victory Sur.', '2026-10-02 21:34:33', '2026-10-02 13:34:33', '2026-10-02 13:34:33'),
(100, 3, 'Added Barangay log entry \"Kasambahay Report\" for Victory Sur.', '2026-10-02 21:35:12', '2026-10-02 13:35:12', '2026-10-02 13:35:12'),
(101, 3, 'Recorded submission for \"Kasambahay Report\" (Victory Sur) - Submitted.', '2026-10-02 21:35:49', '2026-10-02 13:35:49', '2026-10-02 13:35:49'),
(102, 3, 'Filed report \"Kasambahay Report\" into Victory Sur\'s folder.', '2026-10-02 21:35:52', '2026-10-02 13:35:52', '2026-10-02 13:35:52'),
(103, 3, 'Recorded submission for \"Kasambahay Report\" (Victory Sur) - Submitted.', '2026-10-02 21:36:04', '2026-10-02 13:36:04', '2026-10-02 13:36:04'),
(104, 3, 'Filed report \"Kasambahay Report\" into Victory Sur\'s folder.', '2026-10-02 21:36:07', '2026-10-02 13:36:07', '2026-10-02 13:36:07'),
(105, 3, 'Added barangay \"Q\".', '2026-10-02 22:28:13', '2026-10-02 14:28:13', '2026-10-02 14:28:13'),
(106, 3, 'Deleted barangay \"Q\".', '2026-10-02 22:28:18', '2026-10-02 14:28:18', '2026-10-02 14:28:18'),
(107, 3, 'Added barangay \"Sagana\".', '2026-10-02 22:28:46', '2026-10-02 14:28:46', '2026-10-02 14:28:46'),
(108, 3, 'Added Barangay log entry \"Kasambahay Report\" for Sagana.', '2026-10-02 22:30:22', '2026-10-02 14:30:22', '2026-10-02 14:30:22'),
(109, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Victory Sur) - Submitted.', '2026-10-02 22:31:28', '2026-10-02 14:31:28', '2026-10-02 14:31:28'),
(110, 3, 'Recorded submission for \"Kasambahay Report\" (Calao East) - Submitted Late.', '2026-10-02 22:31:50', '2026-10-02 14:31:50', '2026-10-02 14:31:50'),
(111, 3, 'Recorded submission for \"Katarungang Pambarangay Report\" (Patul) - Submitted.', '2026-10-03 14:19:52', '2026-10-03 06:19:52', '2026-10-03 06:19:52'),
(113, 1, 'Signed in.', '2026-10-14 15:11:38', '2026-10-14 07:11:38', '2026-10-14 07:11:38'),
(114, 3, 'Signed in.', '2026-10-14 15:17:58', '2026-10-14 07:17:58', '2026-10-14 07:17:58'),
(115, 3, 'Created user account \"Junell Panag\" (Barangay User, username: Nel).', '2026-10-14 15:18:56', '2026-10-14 07:18:56', '2026-10-14 07:18:56'),
(117, 3, 'Signed in.', '2026-10-14 15:21:27', '2026-10-14 07:21:27', '2026-10-14 07:21:27'),
(118, 3, 'Added Barangay log entry \"Kasambahay Report\" for Malvar.', '2026-10-14 15:21:50', '2026-10-14 07:21:50', '2026-10-14 07:21:50'),
(119, 3, 'Added Barangay log entry \"Kasambahay Report\" for Malvar.', '2026-10-14 15:22:13', '2026-10-14 07:22:13', '2026-10-14 07:22:13'),
(122, 3, 'Signed in.', '2026-10-14 15:32:28', '2026-10-14 07:32:28', '2026-10-14 07:32:28'),
(123, 3, 'Deleted user account \"Junell Panag\" (username: Nel).', '2026-10-14 15:34:13', '2026-10-14 07:34:13', '2026-10-14 07:34:13'),
(124, 3, 'Added user log \"Junell Acop  Panag\" with a login account.', '2026-10-14 15:37:58', '2026-10-14 07:37:58', '2026-10-14 07:37:58'),
(125, 3, 'Updated user account \"Junell Panag\" (Barangay User, username: Nell, active).', '2026-10-14 15:38:41', '2026-10-14 07:38:41', '2026-10-14 07:38:41'),
(126, 7, 'Signed in (Malvar).', '2026-10-14 15:45:01', '2026-10-14 07:45:01', '2026-10-14 07:45:01'),
(127, 3, 'Signed in.', '2026-10-14 15:48:57', '2026-10-14 07:48:57', '2026-10-14 07:48:57'),
(128, 3, 'Signed in.', '2026-10-16 18:23:19', '2026-10-16 10:23:19', '2026-10-16 10:23:19'),
(129, 7, 'Signed in (Malvar).', '2026-10-16 18:23:49', '2026-10-16 10:23:49', '2026-10-16 10:23:49'),
(130, 7, 'Updated their profile.', '2026-10-16 18:25:14', '2026-10-16 10:25:14', '2026-10-16 10:25:14'),
(131, 7, 'Updated their profile.', '2026-10-16 18:25:15', '2026-10-16 10:25:15', '2026-10-16 10:25:15'),
(132, 3, 'Signed in.', '2026-10-16 18:29:46', '2026-10-16 10:29:46', '2026-10-16 10:29:46'),
(133, 3, 'Added Barangay log entry \"Katarungang Pambarangay Report\" for Malvar.', '2026-10-16 18:30:04', '2026-10-16 10:30:04', '2026-10-16 10:30:04'),
(134, 7, 'Signed in (Malvar).', '2026-10-16 18:30:17', '2026-10-16 10:30:17', '2026-10-16 10:30:17'),
(135, 7, 'Submitted report \"Katarungang Pambarangay Report\" (Malvar) - Submitted.', '2026-10-16 18:30:38', '2026-10-16 10:30:38', '2026-10-16 10:30:38'),
(136, 3, 'Signed in.', '2026-10-16 18:32:58', '2026-10-16 10:32:58', '2026-10-16 10:32:58'),
(137, 3, 'Signed in.', '2026-10-23 21:18:31', '2026-10-23 13:18:31', '2026-10-23 13:18:31'),
(138, 3, 'Signed in.', '2026-10-23 21:20:32', '2026-10-23 13:20:32', '2026-10-23 13:20:32'),
(139, 7, 'Signed in (Malvar).', '2026-10-23 21:34:30', '2026-10-23 13:34:30', '2026-10-23 13:34:30'),
(140, 3, 'Signed in.', '2026-10-23 22:11:28', '2026-10-23 14:11:28', '2026-10-23 14:11:28'),
(141, 3, 'Created user account \"VOVOV\" (Barangay User, username: VOVO).', '2026-10-23 22:15:37', '2026-10-23 14:15:37', '2026-10-23 14:15:37'),
(142, 3, 'Deleted user account \"VOVOV\" (username: VOVO).', '2026-10-23 22:15:43', '2026-10-23 14:15:43', '2026-10-23 14:15:43'),
(143, 3, 'Updated user account \"bubu\" (Barangay User, username: Nell, active).', '2026-10-23 22:16:02', '2026-10-23 14:16:02', '2026-10-23 14:16:02'),
(144, 3, 'Updated user account \"segs\" (Barangay User, username: Nell, active).', '2026-10-23 22:16:12', '2026-10-23 14:16:12', '2026-10-23 14:16:12'),
(145, 3, 'Updated user account \"Junell Panag\" (Barangay User, username: Nell, active).', '2026-10-23 22:16:26', '2026-10-23 14:16:26', '2026-10-23 14:16:26'),
(146, 3, 'Updated user account \"vuvu\" (Barangay User, username: Nell, active).', '2026-10-23 22:23:48', '2026-10-23 14:23:48', '2026-10-23 14:23:48'),
(147, 3, 'Updated user account \"Junell Panag\" (Barangay User, username: Nell, active).', '2026-10-23 22:23:59', '2026-10-23 14:23:59', '2026-10-23 14:23:59'),
(148, 3, 'Signed in.', '2026-10-24 12:57:00', '2026-10-24 04:57:00', '2026-10-24 04:57:00'),
(149, 3, 'Signed in.', '2026-10-25 14:35:18', '2026-10-25 06:35:18', '2026-10-25 06:35:18'),
(150, 3, 'Signed in.', '2026-10-25 14:35:32', '2026-10-25 06:35:32', '2026-10-25 06:35:32'),
(151, 3, 'Added Barangay report requirement \"VOWC\" (Monthly, mandatory).', '2026-10-25 14:48:47', '2026-10-25 06:48:47', '2026-10-25 06:48:47'),
(152, 7, 'Signed in (Malvar).', '2026-10-25 14:49:34', '2026-10-25 06:49:34', '2026-10-25 06:49:34'),
(153, 7, 'Submitted report \"VOWC\" (Malvar) - Submitted.', '2026-10-25 14:50:02', '2026-10-25 06:50:02', '2026-10-25 06:50:02'),
(154, 3, 'Signed in.', '2026-10-25 14:50:21', '2026-10-25 06:50:21', '2026-10-25 06:50:21'),
(155, 3, 'Stamped report RACTS-000021 \"VOWC\" (Malvar) - verification code F56WMOZCYR3Z, original sha256 fc87e67eb26136a1596f7195e0a2e97d72da419636007cc405b6cd786c68d43d, stamped sha256 49105d18f40cf9e501469965ccf0543afc2bacb4d72699d3e547064f2a8bd865.', '2026-10-25 14:50:36', '2026-10-25 06:50:36', '2026-10-25 06:50:36'),
(156, 3, 'Updated \"Kasambahay Report\" for Centro West — October 2026 (Submitted (On Time)).', '2026-10-25 15:00:27', '2026-10-25 07:00:27', '2026-10-25 07:00:27'),
(157, 7, 'Signed in (Malvar).', '2026-10-25 16:17:59', '2026-10-25 08:17:59', '2026-10-25 08:17:59');

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `barangay_name`, `status`, `date_process`, `processed_by`, `created_at`, `updated_at`) VALUES
(1, 'Calao East', 'active', '2026-10-02', 'Gian Ventura', '2026-09-05 00:08:37', '2026-10-02 05:39:16'),
(2, 'Centro West', 'active', '2026-10-02', 'Gian Ventura', '2026-09-05 00:08:54', '2026-10-02 05:39:19'),
(3, 'Villasis', 'active', '2026-10-02', 'Gian Ventura', '2026-09-05 00:09:05', '2026-10-02 05:39:41'),
(4, 'Rizal', 'active', '2026-10-02', 'Gian Ventura', '2026-09-25 02:12:20', '2026-10-02 05:39:35'),
(5, 'Patul', 'active', '2026-10-02', 'Gian Ventura', '2026-09-25 09:29:49', '2026-10-02 05:39:30'),
(6, 'Malvar', 'active', '2026-10-02', 'Gian Ventura', '2026-09-25 10:22:48', '2026-10-02 05:39:23'),
(7, 'Rosario', 'active', '2026-10-01', 'Gian Ventura', '2026-10-01 07:34:16', '2026-10-01 07:34:16'),
(8, 'Sinsayon', 'active', '2026-10-01', 'Gian Ventura', '2026-10-01 13:00:05', '2026-10-01 13:00:05'),
(9, 'Victory Norte', 'active', '2026-10-02', 'Gian Ventura', '2026-10-02 13:10:25', '2026-10-02 13:10:25'),
(10, 'Mabini', 'active', '2026-10-02', 'Gian Ventura', '2026-10-02 13:33:35', '2026-10-02 13:33:35'),
(11, 'Victory Sur', 'active', '2026-10-02', 'Gian Ventura', '2026-10-02 13:33:53', '2026-10-02 13:33:53'),
(13, 'Sagana', 'active', '2026-10-02', 'Gian Ventura', '2026-10-02 14:28:46', '2026-10-02 14:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_logs`
--

CREATE TABLE `barangay_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `council_type` enum('barangay','sk') NOT NULL DEFAULT 'barangay',
  `frequency` varchar(255) NOT NULL DEFAULT 'once',
  `deadline_date` date DEFAULT NULL,
  `deadline_day` tinyint(3) UNSIGNED DEFAULT NULL,
  `deadline_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `effective_from` date DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay_logs`
--

INSERT INTO `barangay_logs` (`id`, `item_description`, `council_type`, `frequency`, `deadline_date`, `deadline_day`, `deadline_month`, `effective_from`, `is_mandatory`, `status`, `date_process`, `processed_by`, `created_at`, `updated_at`) VALUES
(1, 'Kasambahay Report', 'barangay', 'once', '2026-09-28', NULL, NULL, NULL, 1, 'active', '2026-09-28', 'DILG Administrator', '2026-09-28 14:12:25', '2026-09-28 14:12:25'),
(2, 'Katarungang Pambarangay Report', 'barangay', 'once', '2026-12-31', NULL, NULL, NULL, 1, 'active', '2026-10-01', 'Gian Ventura', '2026-10-01 08:36:33', '2026-10-01 08:36:33'),
(3, 'Minutes of Meeting Report', 'sk', 'once', '2026-10-31', NULL, NULL, NULL, 1, 'active', NULL, NULL, '2026-10-02 05:38:12', '2026-10-02 05:38:12'),
(4, 'VOWC', 'barangay', 'monthly', NULL, 5, NULL, '2026-10-25', 1, 'active', '2026-10-25', 'Gian Ventura', '2026-10-25 06:48:47', '2026-10-25 06:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_log_details`
--

CREATE TABLE `barangay_log_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_log_id` bigint(20) UNSIGNED NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `period` varchar(7) DEFAULT NULL,
  `deadline_date` date NOT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `report_file_path` varchar(255) DEFAULT NULL,
  `report_original_name` varchar(255) DEFAULT NULL,
  `report_file_hash` varchar(64) DEFAULT NULL,
  `report_filed_at` datetime DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `submission_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `review_remarks` text DEFAULT NULL,
  `reviewed_by` varchar(255) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay_log_details`
--

INSERT INTO `barangay_log_details` (`id`, `barangay_log_id`, `item_description`, `period`, `deadline_date`, `date_submitted`, `report_file_path`, `report_original_name`, `report_file_hash`, `report_filed_at`, `status`, `submission_count`, `review_remarks`, `reviewed_by`, `reviewed_at`, `date_process`, `processed_by`, `created_at`, `updated_at`, `barangay_id`) VALUES
(1, 1, 'Kasambahay Report', NULL, '2026-09-28', '2026-10-02 22:31:00', NULL, NULL, NULL, NULL, 'late', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-09-28 14:15:26', '2026-10-02 14:31:50', 1),
(2, 1, 'Kasambahay Report', NULL, '2026-12-31', '2026-10-01 14:50:00', 'barangays/Malvar/Barangay/2-78606ce4-90de-4aa0-a0f8-ccc473d2bc60.jpg', '78606ce4-90de-4aa0-a0f8-ccc473d2bc60.jpg', NULL, '2026-10-01 14:51:45', 'submitted', 0, NULL, NULL, NULL, '2026-10-01', 'DILG Administrator', '2026-09-29 12:43:33', '2026-10-02 19:21:17', 6),
(3, 1, 'Kasambahay Report', NULL, '2026-11-30', NULL, NULL, NULL, NULL, NULL, 'closed', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-01 08:21:07', '2026-10-02 13:20:34', 6),
(4, 2, 'Katarungang Pambarangay Report', NULL, '2026-12-31', '2026-10-01 18:21:00', 'barangays/Centro West/Barangay/4-ISO EVALUATION RACTS.docx', 'ISO EVALUATION RACTS.docx', NULL, '2026-10-01 18:22:40', 'submitted', 0, NULL, NULL, NULL, '2026-10-01', 'DILG Monitoring Staff', '2026-10-01 08:36:52', '2026-10-02 19:21:17', 2),
(5, 2, 'Katarungang Pambarangay Report', NULL, '2026-12-31', '2026-10-03 14:19:00', 'barangays/Patul/Barangay/5-825371549_2281248519298114_4078544137879124967_n.jpg', '825371549_2281248519298114_4078544137879124967_n.jpg', NULL, '2026-10-03 14:19:52', 'submitted', 0, NULL, NULL, NULL, '2026-10-03', 'Gian Ventura', '2026-10-01 08:37:09', '2026-10-03 06:19:52', 5),
(6, 2, 'Katarungang Pambarangay Report', NULL, '2026-12-31', '2026-10-02 13:09:00', 'incoming-reports/Barangay/Sinsayon/OxM4WapkHjreYCRPx3woaktSyzu6qhtFdZX6zNgl.docx', 'ISO EVALUATION RACTS (1).docx', NULL, NULL, 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-01 14:11:41', '2026-10-02 19:21:17', 8),
(7, 3, 'Minutes of Meeting Report', NULL, '2026-10-31', '2026-10-02 13:40:00', 'incoming-reports/SK/Calao East/qOYMFpyEwqj4rIJkdKmUacfzese9fP7uAvjEtzCO.jpg', 'Picapica-photostrip (5).jpg', NULL, NULL, 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 05:38:12', '2026-10-02 19:21:17', 1),
(8, 1, 'Kasambahay Report', NULL, '2026-10-31', '2026-10-02 21:12:00', 'barangays/Victory Norte/Barangay/8-ISO EVALUATION RACTS (1).docx', 'ISO EVALUATION RACTS (1).docx', 'f0d3f6ee7c09aa0f34af0dbcbf2f73076420d26ecdbb476aaac0ef8a9402be27', '2026-10-02 21:13:20', 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 13:11:50', '2026-10-25 08:14:44', 9),
(9, 2, 'Katarungang Pambarangay Report', NULL, '2026-12-31', '2026-10-02 22:31:00', NULL, NULL, NULL, NULL, 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 13:34:20', '2026-10-02 14:31:28', 11),
(10, 1, 'Kasambahay Report', NULL, '2026-12-31', '2026-10-02 21:35:00', 'barangays/Victory Sur/Barangay/10-ISO EVALUATION RACTS (1) (4).docx', 'ISO EVALUATION RACTS (1) (4).docx', 'f0d3f6ee7c09aa0f34af0dbcbf2f73076420d26ecdbb476aaac0ef8a9402be27', '2026-10-02 21:35:52', 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 13:34:33', '2026-10-24 04:57:30', 11),
(11, 1, 'Kasambahay Report', NULL, '2026-12-29', '2026-10-02 21:35:00', 'barangays/Victory Sur/Barangay/11-829528822_122336472380020618_1826212030456877022_n.jpg', '829528822_122336472380020618_1826212030456877022_n.jpg', NULL, '2026-10-02 21:36:07', 'submitted', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 13:35:12', '2026-10-02 19:21:17', 11),
(12, 1, 'Kasambahay Report', NULL, '2026-10-31', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, '2026-10-02', 'Gian Ventura', '2026-10-02 14:30:22', '2026-10-02 14:30:22', 13),
(13, 1, 'Kasambahay Report', NULL, '2026-09-30', NULL, NULL, NULL, NULL, NULL, 'missed', 0, NULL, NULL, NULL, '2026-10-14', 'Gian Ventura', '2026-10-14 07:21:50', '2026-10-14 07:21:50', 6),
(14, 1, 'Kasambahay Report', NULL, '2026-11-30', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, '2026-10-14', 'Gian Ventura', '2026-10-14 07:22:13', '2026-10-14 07:22:13', 6),
(15, 2, 'Katarungang Pambarangay Report', NULL, '2026-12-31', '2026-10-16 18:30:38', 'barangays/Malvar/Barangay/15-RACTS-Finallyyy.docx', 'RACTS-Finallyyy.docx', 'c2a85e672177571aa72a31d43b43ddfa0bf062b990d6828a6edec1cbb7e83a51', '2026-10-16 18:30:38', 'submitted', 0, NULL, NULL, NULL, '2026-10-16', 'VOVO', '2026-10-16 10:30:04', '2026-10-23 13:24:29', 6),
(16, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 1),
(17, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 2),
(18, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 3),
(19, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 4),
(20, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 5),
(21, 4, 'VOWC', '2026-10', '2026-11-05', '2026-10-25 14:50:02', 'barangays/Malvar/Barangay/21-bryanFE3.docx', 'bryanFE3.docx', 'fc87e67eb26136a1596f7195e0a2e97d72da419636007cc405b6cd786c68d43d', '2026-10-25 14:50:02', 'submitted', 1, NULL, NULL, NULL, '2026-10-25', 'Junell Panag', '2026-10-25 06:48:47', '2026-10-25 06:50:02', 6),
(22, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 7),
(23, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 8),
(24, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 9),
(25, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 10),
(26, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 11),
(27, 4, 'VOWC', '2026-10', '2026-11-05', NULL, NULL, NULL, NULL, NULL, 'pending', 0, NULL, NULL, NULL, NULL, NULL, '2026-10-25 06:48:47', '2026-10-25 06:48:47', 13);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('racts1-cache-junell|127.0.0.1', 'i:2;', 1791964175),
('racts1-cache-junell|127.0.0.1:timer', 'i:1791964175;', 1791964175),
('racts1-cache-moderation.dictionary', 'a:2:{s:5:\"terms\";a:52:{i:0;a:6:{s:2:\"id\";i:1;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:14:{i:0;a:3:{s:4:\"text\";s:4:\"fuck\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:3:\"fak\";s:3:\"key\";s:3:\"fak\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:3:\"fuk\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:3;a:3:{s:4:\"text\";s:3:\"fck\";s:3:\"key\";s:2:\"fk\";s:11:\"phrase_only\";b:0;}i:4;a:3:{s:4:\"text\";s:4:\"fvck\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:5:\"phuck\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:6;a:3:{s:4:\"text\";s:4:\"phuk\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:7;a:3:{s:4:\"text\";s:3:\"fuq\";s:3:\"key\";s:3:\"fuk\";s:11:\"phrase_only\";b:0;}i:8;a:3:{s:4:\"text\";s:7:\"fucking\";s:3:\"key\";s:6:\"fuking\";s:11:\"phrase_only\";b:0;}i:9;a:3:{s:4:\"text\";s:6:\"fucker\";s:3:\"key\";s:5:\"fuker\";s:11:\"phrase_only\";b:0;}i:10;a:3:{s:4:\"text\";s:6:\"fucked\";s:3:\"key\";s:5:\"fuked\";s:11:\"phrase_only\";b:0;}i:11;a:3:{s:4:\"text\";s:5:\"fking\";s:3:\"key\";s:5:\"fking\";s:11:\"phrase_only\";b:0;}i:12;a:3:{s:4:\"text\";s:6:\"fuking\";s:3:\"key\";s:6:\"fuking\";s:11:\"phrase_only\";b:0;}i:13;a:3:{s:4:\"text\";s:7:\"fuckyou\";s:3:\"key\";s:6:\"fukyou\";s:11:\"phrase_only\";b:1;}}}i:1;a:6:{s:2:\"id\";i:2;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:12:\"motherfucker\";s:3:\"key\";s:11:\"motherfuker\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:13:\"motherfucking\";s:3:\"key\";s:12:\"motherfuking\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:4:\"mofo\";s:3:\"key\";s:4:\"mofo\";s:11:\"phrase_only\";b:0;}}}i:2;a:6:{s:2:\"id\";i:3;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:6:{i:0;a:3:{s:4:\"text\";s:4:\"shit\";s:3:\"key\";s:4:\"shit\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"shyt\";s:3:\"key\";s:4:\"shyt\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:3:\"sht\";s:3:\"key\";s:3:\"sht\";s:11:\"phrase_only\";b:0;}i:3;a:3:{s:4:\"text\";s:5:\"shity\";s:3:\"key\";s:5:\"shity\";s:11:\"phrase_only\";b:0;}i:4;a:3:{s:4:\"text\";s:7:\"bulshit\";s:3:\"key\";s:7:\"bulshit\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:8:\"shithead\";s:3:\"key\";s:8:\"shithead\";s:11:\"phrase_only\";b:0;}}}i:3;a:6:{s:2:\"id\";i:4;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:6:{i:0;a:3:{s:4:\"text\";s:5:\"bitch\";s:3:\"key\";s:5:\"bitkh\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"biatch\";s:3:\"key\";s:6:\"biatkh\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:5:\"bytch\";s:3:\"key\";s:5:\"bytkh\";s:11:\"phrase_only\";b:0;}i:3;a:3:{s:4:\"text\";s:7:\"bitches\";s:3:\"key\";s:7:\"bitkhes\";s:11:\"phrase_only\";b:0;}i:4;a:3:{s:4:\"text\";s:11:\"sonofabitch\";s:3:\"key\";s:11:\"sonofabitkh\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:11:\"sonofabitch\";s:3:\"key\";s:11:\"sonofabitkh\";s:11:\"phrase_only\";b:1;}}}i:4;a:6:{s:2:\"id\";i:5;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:7:\"bastard\";s:3:\"key\";s:7:\"bastard\";s:11:\"phrase_only\";b:0;}}}i:5;a:6:{s:2:\"id\";i:6;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:6:\"ashole\";s:3:\"key\";s:6:\"ashole\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"ashole\";s:3:\"key\";s:6:\"ashole\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:8:\"arsehole\";s:3:\"key\";s:8:\"arsehole\";s:11:\"phrase_only\";b:0;}}}i:6;a:6:{s:2:\"id\";i:7;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"cunt\";s:3:\"key\";s:4:\"kunt\";s:11:\"phrase_only\";b:0;}}}i:7;a:6:{s:2:\"id\";i:8;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"whore\";s:3:\"key\";s:5:\"whore\";s:11:\"phrase_only\";b:0;}}}i:8;a:6:{s:2:\"id\";i:9;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"slut\";s:3:\"key\";s:4:\"slut\";s:11:\"phrase_only\";b:0;}}}i:9;a:6:{s:2:\"id\";i:10;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:5:\"niger\";s:3:\"key\";s:5:\"niger\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"niga\";s:3:\"key\";s:4:\"niga\";s:11:\"phrase_only\";b:0;}}}i:10;a:6:{s:2:\"id\";i:11;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"fagot\";s:3:\"key\";s:5:\"fagot\";s:11:\"phrase_only\";b:0;}}}i:11;a:6:{s:2:\"id\";i:12;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:8:\"dickhead\";s:3:\"key\";s:7:\"dikhead\";s:11:\"phrase_only\";b:0;}}}i:12;a:6:{s:2:\"id\";i:13;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"pusy\";s:3:\"key\";s:4:\"pusy\";s:11:\"phrase_only\";b:0;}}}i:13;a:6:{s:2:\"id\";i:14;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:2:\"as\";s:3:\"key\";s:2:\"as\";s:11:\"phrase_only\";b:0;}}}i:14;a:6:{s:2:\"id\";i:15;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"dick\";s:3:\"key\";s:3:\"dik\";s:11:\"phrase_only\";b:0;}}}i:15;a:6:{s:2:\"id\";i:16;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"cock\";s:3:\"key\";s:3:\"kok\";s:11:\"phrase_only\";b:0;}}}i:16;a:6:{s:2:\"id\";i:17;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"porn\";s:3:\"key\";s:4:\"porn\";s:11:\"phrase_only\";b:0;}}}i:17;a:6:{s:2:\"id\";i:18;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:3:\"wtf\";s:3:\"key\";s:3:\"wtf\";s:11:\"phrase_only\";b:0;}}}i:18;a:6:{s:2:\"id\";i:19;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"stfu\";s:3:\"key\";s:4:\"stfu\";s:11:\"phrase_only\";b:0;}}}i:19;a:6:{s:2:\"id\";i:20;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:0;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:3:\"sex\";s:3:\"key\";s:4:\"seks\";s:11:\"phrase_only\";b:0;}}}i:20;a:6:{s:2:\"id\";i:21;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:8:{i:0;a:3:{s:4:\"text\";s:9:\"putangina\";s:3:\"key\";s:9:\"putangina\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:9:\"putangina\";s:3:\"key\";s:9:\"putangina\";s:11:\"phrase_only\";b:1;}i:2;a:3:{s:4:\"text\";s:7:\"putaina\";s:3:\"key\";s:7:\"putaina\";s:11:\"phrase_only\";b:1;}i:3;a:3:{s:4:\"text\";s:8:\"ptangina\";s:3:\"key\";s:8:\"ptangina\";s:11:\"phrase_only\";b:1;}i:4;a:3:{s:4:\"text\";s:8:\"ptangina\";s:3:\"key\";s:8:\"ptangina\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:11:\"putanginamo\";s:3:\"key\";s:11:\"putanginamu\";s:11:\"phrase_only\";b:0;}i:6;a:3:{s:4:\"text\";s:9:\"potangina\";s:3:\"key\";s:9:\"putangina\";s:11:\"phrase_only\";b:0;}i:7;a:3:{s:4:\"text\";s:11:\"putanginamo\";s:3:\"key\";s:11:\"putanginamu\";s:11:\"phrase_only\";b:1;}}}i:21;a:6:{s:2:\"id\";i:22;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:4:\"puta\";s:3:\"key\";s:4:\"puta\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"pota\";s:3:\"key\";s:4:\"puta\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:6:\"putaka\";s:3:\"key\";s:6:\"putaka\";s:11:\"phrase_only\";b:1;}}}i:22;a:6:{s:2:\"id\";i:23;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:7:{i:0;a:3:{s:4:\"text\";s:7:\"tangina\";s:3:\"key\";s:7:\"tangina\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"tngina\";s:3:\"key\";s:6:\"tngina\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:7:\"tangina\";s:3:\"key\";s:7:\"tangina\";s:11:\"phrase_only\";b:1;}i:3;a:3:{s:4:\"text\";s:6:\"tangna\";s:3:\"key\";s:6:\"tangna\";s:11:\"phrase_only\";b:0;}i:4;a:3:{s:4:\"text\";s:9:\"tanginamo\";s:3:\"key\";s:9:\"tanginamu\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:9:\"tanginamo\";s:3:\"key\";s:9:\"tanginamu\";s:11:\"phrase_only\";b:1;}i:6;a:3:{s:4:\"text\";s:9:\"tanginaka\";s:3:\"key\";s:9:\"tanginaka\";s:11:\"phrase_only\";b:0;}}}i:23;a:6:{s:2:\"id\";i:24;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:4:{i:0;a:3:{s:4:\"text\";s:4:\"gago\";s:3:\"key\";s:4:\"gagu\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"gaga\";s:3:\"key\";s:4:\"gaga\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:4:\"gagu\";s:3:\"key\";s:4:\"gagu\";s:11:\"phrase_only\";b:0;}i:3;a:3:{s:4:\"text\";s:6:\"gagoka\";s:3:\"key\";s:6:\"gaguka\";s:11:\"phrase_only\";b:1;}}}i:24;a:6:{s:2:\"id\";i:25;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:5:\"tanga\";s:3:\"key\";s:5:\"tanga\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"tnga\";s:3:\"key\";s:4:\"tnga\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:7:\"tangaka\";s:3:\"key\";s:7:\"tangaka\";s:11:\"phrase_only\";b:1;}}}i:25;a:6:{s:2:\"id\";i:26;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:4:\"bobo\";s:3:\"key\";s:4:\"bubu\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"buboka\";s:3:\"key\";s:6:\"bubuka\";s:11:\"phrase_only\";b:1;}i:2;a:3:{s:4:\"text\";s:6:\"boboka\";s:3:\"key\";s:6:\"bubuka\";s:11:\"phrase_only\";b:1;}}}i:26;a:6:{s:2:\"id\";i:27;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:4:\"ulol\";s:3:\"key\";s:4:\"ulul\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:4:\"ulul\";s:3:\"key\";s:4:\"ulul\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:4:\"olol\";s:3:\"key\";s:4:\"ulul\";s:11:\"phrase_only\";b:0;}}}i:27;a:6:{s:2:\"id\";i:28;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:6:{i:0;a:3:{s:4:\"text\";s:5:\"pakyu\";s:3:\"key\";s:5:\"pakyu\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:5:\"pakyu\";s:3:\"key\";s:5:\"pakyu\";s:11:\"phrase_only\";b:1;}i:2;a:3:{s:4:\"text\";s:5:\"pakyo\";s:3:\"key\";s:5:\"pakyu\";s:11:\"phrase_only\";b:0;}i:3;a:3:{s:4:\"text\";s:6:\"pakyou\";s:3:\"key\";s:5:\"pakyu\";s:11:\"phrase_only\";b:0;}i:4;a:3:{s:4:\"text\";s:5:\"fakyu\";s:3:\"key\";s:5:\"fakyu\";s:11:\"phrase_only\";b:0;}i:5;a:3:{s:4:\"text\";s:5:\"fakyu\";s:3:\"key\";s:5:\"fakyu\";s:11:\"phrase_only\";b:1;}}}i:28;a:6:{s:2:\"id\";i:29;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:6:\"kantot\";s:3:\"key\";s:6:\"kantut\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:8:\"kantutan\";s:3:\"key\";s:8:\"kantutan\";s:11:\"phrase_only\";b:0;}}}i:29;a:6:{s:2:\"id\";i:30;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:5:\"jakol\";s:3:\"key\";s:5:\"jakul\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:7:\"jakulan\";s:3:\"key\";s:7:\"jakulan\";s:11:\"phrase_only\";b:0;}}}i:30;a:6:{s:2:\"id\";i:31;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:6:\"pokpok\";s:3:\"key\";s:6:\"pukpuk\";s:11:\"phrase_only\";b:0;}}}i:31;a:6:{s:2:\"id\";i:32;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:9:\"tarantado\";s:3:\"key\";s:9:\"tarantadu\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:9:\"tarantada\";s:3:\"key\";s:9:\"tarantada\";s:11:\"phrase_only\";b:0;}}}i:32;a:6:{s:2:\"id\";i:33;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"fuzzy\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:9:\"hinayupak\";s:3:\"key\";s:9:\"hinayupak\";s:11:\"phrase_only\";b:0;}}}i:33;a:6:{s:2:\"id\";i:34;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:7:\"pakshet\";s:3:\"key\";s:7:\"pakshit\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:7:\"pakshit\";s:3:\"key\";s:7:\"pakshit\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:7:\"pakshet\";s:3:\"key\";s:7:\"pakshit\";s:11:\"phrase_only\";b:1;}}}i:34;a:6:{s:2:\"id\";i:35;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:7:\"siraulo\";s:3:\"key\";s:7:\"siraulu\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:7:\"siraulo\";s:3:\"key\";s:7:\"siraulu\";s:11:\"phrase_only\";b:1;}}}i:35;a:6:{s:2:\"id\";i:36;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:4:\"puki\";s:3:\"key\";s:4:\"puki\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"pekpek\";s:3:\"key\";s:6:\"pikpik\";s:11:\"phrase_only\";b:0;}}}i:36;a:6:{s:2:\"id\";i:37;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"burat\";s:3:\"key\";s:5:\"burat\";s:11:\"phrase_only\";b:0;}}}i:37;a:6:{s:2:\"id\";i:38;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"etits\";s:3:\"key\";s:5:\"itits\";s:11:\"phrase_only\";b:0;}}}i:38;a:6:{s:2:\"id\";i:39;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"iyot\";s:3:\"key\";s:4:\"iyut\";s:11:\"phrase_only\";b:0;}}}i:39;a:6:{s:2:\"id\";i:40;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"engot\";s:3:\"key\";s:5:\"ingut\";s:11:\"phrase_only\";b:0;}}}i:40;a:6:{s:2:\"id\";i:41;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"ungas\";s:3:\"key\";s:5:\"ungas\";s:11:\"phrase_only\";b:0;}}}i:41;a:6:{s:2:\"id\";i:42;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:6:\"lintik\";s:3:\"key\";s:6:\"lintik\";s:11:\"phrase_only\";b:0;}}}i:42;a:6:{s:2:\"id\";i:43;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"leche\";s:3:\"key\";s:5:\"likhi\";s:11:\"phrase_only\";b:0;}}}i:43;a:6:{s:2:\"id\";i:44;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:6:\"bwisit\";s:3:\"key\";s:6:\"bwisit\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:7:\"buwisit\";s:3:\"key\";s:7:\"buwisit\";s:11:\"phrase_only\";b:0;}}}i:44;a:6:{s:2:\"id\";i:45;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:5:\"hayop\";s:3:\"key\";s:5:\"hayup\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:7:\"hayopka\";s:3:\"key\";s:7:\"hayupka\";s:11:\"phrase_only\";b:1;}}}i:45;a:6:{s:2:\"id\";i:46;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:7:\"demonyo\";s:3:\"key\";s:7:\"dimunyu\";s:11:\"phrase_only\";b:0;}}}i:46;a:6:{s:2:\"id\";i:47;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:6:\"inutil\";s:3:\"key\";s:6:\"inutil\";s:11:\"phrase_only\";b:0;}}}i:47;a:6:{s:2:\"id\";i:48;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:5:\"bilat\";s:3:\"key\";s:5:\"bilat\";s:11:\"phrase_only\";b:0;}}}i:48;a:6:{s:2:\"id\";i:49;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:1:{i:0;a:3:{s:4:\"text\";s:4:\"oten\";s:3:\"key\";s:4:\"utin\";s:11:\"phrase_only\";b:0;}}}i:49;a:6:{s:2:\"id\";i:50;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:4:\"yawa\";s:3:\"key\";s:4:\"yawa\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"yawaka\";s:3:\"key\";s:6:\"yawaka\";s:11:\"phrase_only\";b:1;}}}i:50;a:6:{s:2:\"id\";i:51;s:6:\"action\";s:6:\"review\";s:4:\"mode\";s:5:\"exact\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:1;s:5:\"forms\";a:2:{i:0;a:3:{s:4:\"text\";s:5:\"pisti\";s:3:\"key\";s:5:\"pisti\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:11:\"pistengyawa\";s:3:\"key\";s:11:\"pistingyawa\";s:11:\"phrase_only\";b:1;}}}i:51;a:6:{s:2:\"id\";i:52;s:6:\"action\";s:5:\"block\";s:4:\"mode\";s:8:\"standard\";s:8:\"filipino\";b:1;s:10:\"has_phrase\";b:0;s:5:\"forms\";a:3:{i:0;a:3:{s:4:\"text\";s:6:\"ukinam\";s:3:\"key\";s:6:\"ukinam\";s:11:\"phrase_only\";b:0;}i:1;a:3:{s:4:\"text\";s:6:\"okinam\";s:3:\"key\";s:6:\"ukinam\";s:11:\"phrase_only\";b:0;}i:2;a:3:{s:4:\"text\";s:8:\"ukininam\";s:3:\"key\";s:8:\"ukininam\";s:11:\"phrase_only\";b:0;}}}}s:5:\"allow\";a:6:{s:7:\"shitake\";b:1;s:8:\"cocktail\";b:1;s:10:\"scunthorpe\";b:1;s:6:\"tangan\";b:1;s:5:\"tanda\";b:1;s:4:\"puto\";b:1;}}', 2108124908);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_05_000001_create_barangays_table', 2),
(5, '2026_09_05_000002_add_barangay_foreign_key_to_users_table', 2),
(6, '2026_09_05_000003_create_report_requirements_table', 3),
(7, '2026_09_05_000004_create_report_records_table', 4),
(8, '2026_09_05_000005_create_notifications_table', 5),
(9, '2026_09_05_000006_create_audit_logs_table', 6),
(10, '2026_09_24_000001_remove_barangay_code_from_barangays_table', 7),
(11, '2026_09_24_000002_redesign_report_tracking_schema', 7),
(12, '2026_09_24_000003_add_automation_fields_to_notifications_table', 7),
(13, '2026_09_24_000004_create_report_rankings_cache_table', 7),
(14, '2026_09_25_000001_add_soft_deletes_to_notifications_table', 8),
(15, '2026_09_25_000002_add_event_tracking_to_notifications_table', 9),
(16, '2026_09_28_000001_add_processing_fields_to_barangays_table', 10),
(17, '2026_09_28_000002_create_barangay_logs_table', 10),
(18, '2026_09_28_000003_create_barangay_log_details_table', 10),
(19, '2026_09_28_000004_create_user_logs_table', 10),
(20, '2026_09_29_000001_add_report_file_fields_to_barangay_log_details_table', 11),
(21, '2026_10_01_000001_add_user_id_to_user_logs_table', 12),
(23, '2026_10_02_000001_add_council_type_to_barangay_logs_table', 13),
(24, '2026_10_09_000001_add_profile_and_status_fields_to_users_table', 14),
(25, '2026_10_23_000001_create_report_stamps_table', 15),
(26, '2026_10_23_000002_add_recurring_requirements_and_review_to_report_filing', 16),
(27, '2026_10_23_000003_create_moderation_tables', 16),
(28, '2026_10_23_212658_change_action_to_text_on_audit_logs_table', 16),
(29, '2026_10_24_000001_drop_moderation_tables', 17);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `report_record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requirement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `barangay_log_detail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `notification_date` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `auto_generated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `report_record_id`, `type`, `barangay_id`, `requirement_id`, `barangay_log_detail_id`, `period`, `message`, `notification_date`, `is_read`, `auto_generated`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(4, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(6, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(8, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(10, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(12, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(14, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(16, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(18, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(20, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(22, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(24, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(26, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(28, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:14', 0, 0, '2026-09-14 16:07:14', '2026-09-14 16:07:14', NULL),
(30, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(32, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(34, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(36, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(38, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(40, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(42, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(44, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(46, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(48, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(50, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(52, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(54, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(56, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(58, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(60, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(62, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(64, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(66, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(68, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(70, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(72, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(74, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(76, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(78, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(80, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(82, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(84, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(86, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(88, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(90, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(92, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(94, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(96, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(98, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(100, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(102, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(104, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(106, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(108, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(110, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(159, 1, 22, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(160, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(161, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(162, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(163, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(164, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(165, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(166, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(167, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(168, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(169, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(170, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(171, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(172, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(173, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(174, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(175, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(176, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(177, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(178, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(179, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(180, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(181, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(182, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(183, 1, 22, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(184, 2, 22, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(185, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(186, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(187, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(188, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(189, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(190, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(191, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(192, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(193, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(194, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(195, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(196, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(197, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(198, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(199, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(200, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(201, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(202, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(203, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(204, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(205, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(206, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(207, 1, 23, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(208, 2, 23, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(209, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(210, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(211, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(212, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(213, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(214, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(215, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(216, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(217, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(218, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(219, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(220, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(221, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(222, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(223, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(224, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(225, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(226, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(227, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(228, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(229, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(230, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(231, 1, 22, 'late_submission', 1, 7, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(232, 2, 22, 'late_submission', 1, 7, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(233, 1, 30, 'late_submission', 1, 8, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 1, 1, '2026-09-25 02:04:05', '2026-09-29 12:46:21', NULL),
(234, 2, 30, 'late_submission', 1, 8, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(235, 1, 33, 'late_submission', 6, 7, NULL, '2026-08', 'Late Submission: Malvar (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 18:23:13', 0, 1, '2026-09-25 10:23:13', '2026-09-25 10:23:13', NULL),
(236, 2, 33, 'late_submission', 6, 7, NULL, '2026-08', 'Late Submission: Malvar (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 18:23:13', 0, 1, '2026-09-25 10:23:13', '2026-09-25 10:23:13', NULL),
(237, 3, 22, 'late_submission', 1, 7, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(238, 4, 22, 'late_submission', 1, 7, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(239, 3, 30, 'late_submission', 1, 8, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(240, 4, 30, 'late_submission', 1, 8, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(241, 1, 38, 'late_submission', 1, 9, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(242, 2, 38, 'late_submission', 1, 9, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(243, 3, 38, 'late_submission', 1, 9, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(244, 4, 38, 'late_submission', 1, 9, NULL, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(245, 7, NULL, 'report_assigned', 6, NULL, 21, '2026-10', 'New report required: VOWC (October 2026), due Nov 5, 2026.', '2026-10-25 14:48:47', 0, 1, '2026-10-25 06:48:47', '2026-10-25 06:48:47', NULL),
(246, 7, NULL, 'report_received', 6, NULL, 21, '2026-10', 'DILG received your report \"VOWC\" for October 2026 (Submitted).', '2026-10-25 14:50:02', 0, 1, '2026-10-25 06:50:02', '2026-10-25 06:50:02', NULL),
(247, 1, 9, 'late_submission', 2, 7, NULL, '2026-10', 'Late Submission: Centro West (Barangay Council) submitted \"Kasambahay Report\" for October 2026 on November 13, 2026, after the November 5, 2026 deadline.', '2026-10-25 15:00:27', 0, 1, '2026-10-25 07:00:27', '2026-10-25 07:00:27', NULL),
(248, 2, 9, 'late_submission', 2, 7, NULL, '2026-10', 'Late Submission: Centro West (Barangay Council) submitted \"Kasambahay Report\" for October 2026 on November 13, 2026, after the November 5, 2026 deadline.', '2026-10-25 15:00:27', 0, 1, '2026-10-25 07:00:27', '2026-10-25 07:00:27', NULL),
(249, 3, 9, 'late_submission', 2, 7, NULL, '2026-10', 'Late Submission: Centro West (Barangay Council) submitted \"Kasambahay Report\" for October 2026 on November 13, 2026, after the November 5, 2026 deadline.', '2026-10-25 15:00:27', 0, 1, '2026-10-25 07:00:27', '2026-10-25 07:00:27', NULL),
(250, 4, 9, 'late_submission', 2, 7, NULL, '2026-10', 'Late Submission: Centro West (Barangay Council) submitted \"Kasambahay Report\" for October 2026 on November 13, 2026, after the November 5, 2026 deadline.', '2026-10-25 15:00:27', 0, 1, '2026-10-25 07:00:27', '2026-10-25 07:00:27', NULL),
(251, 7, 9, 'late_submission', 2, 7, NULL, '2026-10', 'Late Submission: Centro West (Barangay Council) submitted \"Kasambahay Report\" for October 2026 on November 13, 2026, after the November 5, 2026 deadline.', '2026-10-25 15:00:27', 0, 1, '2026-10-25 07:00:27', '2026-10-25 07:00:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_rankings_cache`
--

CREATE TABLE `report_rankings_cache` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `council_type` enum('barangay','sk') NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `barangay_id` bigint(20) UNSIGNED NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `rank` int(10) UNSIGNED NOT NULL,
  `computed_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_records`
--

CREATE TABLE `report_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_id` bigint(20) UNSIGNED NOT NULL,
  `requirement_id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(255) NOT NULL,
  `submission_date` date NOT NULL,
  `status_color` enum('green','yellow','red') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_records`
--

INSERT INTO `report_records` (`id`, `barangay_id`, `requirement_id`, `period`, `submission_date`, `status_color`, `remarks`, `edited_by`, `edited_at`, `created_at`, `updated_at`) VALUES
(4, 1, 7, '2026-01', '2026-01-10', 'green', NULL, 1, '2026-09-24 17:30:03', '2026-09-24 09:30:03', '2026-09-24 09:30:03'),
(5, 1, 7, '2026-02', '2026-02-12', 'green', NULL, 1, '2026-09-24 17:31:28', '2026-09-24 09:31:28', '2026-09-24 09:31:28'),
(7, 3, 7, '2026-11', '2026-11-07', NULL, NULL, 1, '2026-09-24 18:40:00', '2026-09-24 09:36:01', '2026-09-24 10:40:00'),
(8, 3, 7, '2026-10', '2026-10-13', NULL, NULL, 1, '2026-09-24 17:37:51', '2026-09-24 09:37:51', '2026-09-24 09:37:51'),
(9, 2, 7, '2026-10', '2026-11-13', 'green', NULL, 3, '2026-10-25 15:00:27', '2026-09-24 09:47:42', '2026-10-25 07:00:27'),
(10, 3, 7, '2026-07', '2026-08-06', 'green', NULL, 1, '2026-09-24 17:51:13', '2026-09-24 09:51:13', '2026-09-24 09:51:13'),
(12, 2, 7, '2026-06', '2026-07-10', 'green', NULL, 1, '2026-09-24 18:18:56', '2026-09-24 10:18:56', '2026-09-24 10:18:56'),
(13, 1, 7, '2026-10', '2026-10-24', 'green', NULL, 3, '2026-10-02 21:18:18', '2026-09-24 10:49:35', '2026-10-02 13:18:18'),
(18, 1, 8, '2026-06', '2026-07-08', NULL, NULL, 1, '2026-09-24 19:13:52', '2026-09-24 11:13:52', '2026-09-24 11:13:52'),
(20, 2, 7, '2026-03', '2026-03-20', 'green', NULL, 1, '2026-09-24 19:39:56', '2026-09-24 11:39:57', '2026-09-24 11:39:57'),
(22, 1, 7, '2026-08', '2026-09-25', NULL, NULL, 3, '2026-10-02 21:18:42', '2026-09-24 21:14:17', '2026-10-02 13:18:42'),
(23, 3, 7, '2026-08', '2026-09-25', NULL, NULL, 1, '2026-09-25 05:30:11', '2026-09-24 21:30:11', '2026-09-24 21:30:11'),
(28, 2, 18, '2026-08', '2026-09-25', NULL, NULL, 1, '2026-09-25 06:10:18', '2026-09-24 22:10:18', '2026-09-24 22:10:18'),
(29, 1, 7, '2026-09', '2026-09-25', NULL, NULL, 1, '2026-09-28 22:14:23', '2026-09-25 02:02:31', '2026-09-28 14:14:23'),
(30, 1, 8, '2026-08', '2026-09-25', NULL, NULL, 3, '2026-10-02 21:18:42', '2026-09-25 02:04:05', '2026-10-02 13:18:42'),
(31, 1, 13, '2026', '2026-08-25', NULL, NULL, 3, '2026-10-02 21:18:42', '2026-09-25 08:56:01', '2026-10-02 13:18:42'),
(32, 5, 7, '2026-09', '2026-09-25', NULL, NULL, 1, '2026-09-25 18:21:38', '2026-09-25 10:21:37', '2026-09-25 10:21:38'),
(33, 6, 7, '2026-08', '2026-09-25', NULL, NULL, 1, '2026-09-25 18:23:13', '2026-09-25 10:23:13', '2026-09-25 10:23:13'),
(34, 6, 8, '2026-09', '2026-09-01', 'green', NULL, 1, '2026-09-25 18:25:34', '2026-09-25 10:23:41', '2026-09-25 10:25:34'),
(35, 6, 10, '2026-09', '2026-09-15', 'yellow', NULL, 1, '2026-09-25 18:25:34', '2026-09-25 10:25:34', '2026-09-25 10:25:34'),
(36, 1, 8, '2026-09', '2026-09-29', NULL, NULL, 1, '2026-09-28 22:14:23', '2026-09-28 14:14:23', '2026-09-28 14:14:23'),
(37, 1, 9, '2026-10', '2026-08-01', 'yellow', NULL, 3, '2026-10-02 21:18:18', '2026-10-02 13:18:18', '2026-10-02 13:18:18'),
(38, 1, 9, '2026-08', '2026-10-02', 'green', NULL, 3, '2026-10-02 21:18:42', '2026-10-02 13:18:42', '2026-10-02 13:18:42');

-- --------------------------------------------------------

--
-- Table structure for table `report_requirements`
--

CREATE TABLE `report_requirements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `council_type` enum('barangay','sk') NOT NULL,
  `frequency` enum('monthly','yearly') NOT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_requirements`
--

INSERT INTO `report_requirements` (`id`, `report_name`, `council_type`, `frequency`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'Kasambahay Report', 'barangay', 'monthly', 1, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(8, 'Katarungang Pambarangay Report', 'barangay', 'monthly', 2, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(9, 'Barangay Monthly Accomplishment Report', 'barangay', 'monthly', 3, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(10, 'Road Clearing Operation Report', 'barangay', 'monthly', 4, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(11, '1st Minutes of Meeting Report', 'barangay', 'monthly', 5, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(12, '2nd Minutes of Meeting Report', 'barangay', 'monthly', 6, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(13, 'SGBR', 'barangay', 'yearly', 1, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(14, '1st SBAD', 'barangay', 'yearly', 2, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(15, '2nd SBAD', 'barangay', 'yearly', 3, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(16, 'Annual Budget', 'barangay', 'yearly', 4, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(17, 'SGLGB', 'barangay', 'yearly', 5, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(18, 'Accomplishment', 'sk', 'monthly', 1, 1, '2026-09-24 07:15:37', '2026-09-24 09:13:08'),
(19, 'Minutes of Meeting Report', 'sk', 'monthly', 2, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(20, 'Annual Accomplishment Report', 'sk', 'yearly', 1, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(21, 'Annual Budget', 'sk', 'yearly', 2, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(22, 'IRP', 'sk', 'yearly', 3, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(23, '1st KK Assembly', 'sk', 'yearly', 4, 1, '2026-09-24 07:15:37', '2026-09-24 07:15:37'),
(24, '2nd KK Assembly', 'sk', 'yearly', 5, 1, '2026-09-24 07:15:37', '2026-09-24 11:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `report_stamps`
--

CREATE TABLE `report_stamps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_log_detail_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stamped_by_name` varchar(255) NOT NULL,
  `verification_code` varchar(32) NOT NULL,
  `original_file_hash` varchar(64) NOT NULL,
  `stamped_file_path` varchar(255) NOT NULL,
  `stamped_original_name` varchar(255) NOT NULL,
  `stamped_file_hash` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_stamps`
--

INSERT INTO `report_stamps` (`id`, `barangay_log_detail_id`, `user_id`, `stamped_by_name`, `verification_code`, `original_file_hash`, `stamped_file_path`, `stamped_original_name`, `stamped_file_hash`, `created_at`, `updated_at`) VALUES
(1, 15, 3, 'Gian Ventura', 'ATQRDAPNNSOU', 'c2a85e672177571aa72a31d43b43ddfa0bf062b990d6828a6edec1cbb7e83a51', 'stamped-reports/15/ATQRDAPNNSOU.docx', 'RACTS-Finallyyy (stamped).docx', '634f002df04af06c6f01177ff59494d197a74a87f39acb45e97f163086fdae31', '2026-10-23 13:24:29', '2026-10-23 13:24:29'),
(2, 10, 3, 'Gian Ventura', '3MNDXZVXZGIK', 'f0d3f6ee7c09aa0f34af0dbcbf2f73076420d26ecdbb476aaac0ef8a9402be27', 'stamped-reports/10/3MNDXZVXZGIK.docx', 'ISO EVALUATION RACTS (1) (4) (stamped).docx', '0dc25d62b8359947ed5dc7833ae95f7fa494e45e44e960b25b5905cf1f63243a', '2026-10-24 04:57:30', '2026-10-24 04:57:30'),
(3, 21, 3, 'Gian Ventura', 'F56WMOZCYR3Z', 'fc87e67eb26136a1596f7195e0a2e97d72da419636007cc405b6cd786c68d43d', 'stamped-reports/21/F56WMOZCYR3Z.docx', 'bryanFE3 (stamped).docx', '49105d18f40cf9e501469965ccf0543afc2bacb4d72699d3e547064f2a8bd865', '2026-10-25 06:50:36', '2026-10-25 06:50:36'),
(4, 8, 3, 'Gian Ventura', 'VDGLBOR1MWX5', 'f0d3f6ee7c09aa0f34af0dbcbf2f73076420d26ecdbb476aaac0ef8a9402be27', 'stamped-reports/8/VDGLBOR1MWX5.docx', 'ISO EVALUATION RACTS (1) (stamped).docx', 'fc92a3c4c7a3299bc2c622454c319a664e93b24341ce670db4ec00ad613d1fc1', '2026-10-25 08:14:44', '2026-10-25 08:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('deh7OWZmnRrYzAF6SuwmlrJATjh0nmwhVzwhJWnO', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFB5QVZWVnd5dmNxSVM5QTR1NTAzMVRnV2VuU3d5Y2FtT0dKblBnZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9teS1yZXBvcnRzIjtzOjU6InJvdXRlIjtzOjE0OiJwb3J0YWwucmVwb3J0cyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7fQ==', 1792916568),
('IdKZhHFWGqFZTNNcqDeu08z6XUGWO7HZ9xP2WrcC', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV290ZmZPSWl2QklrRlY1OUpma0FpQndjZ0c4Q25qTnAwSlFBZkJWVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1792910118),
('KFQnrQyT4MZXQzfVFzEzXCy8Q4TlfJkLffYnLJX5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.143.1 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVDRBQ0FUVElEbld5b1Y2RVJCS3hwUnVtYlVnMGVWNFNkNHFWRkFJVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1792911124),
('st0u8qbJt3C3I8ZqAv14Jac02BxJpgyzqljiiaXX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMGJZZ01mZzB6VmdGQ1RvaW9lWDVhMnI2cWl2VWZQZnJuMDBhN0xLUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792910160),
('tsHVPcrDjyXzy6nffqNHyWBjsqlfvvjD53qvVsCf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUDA5MEhONEsyV0pTVVY0bjRtcmJJSGJvNXBTdWlqekhJZTBqVGdBRSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792910174);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `contact_number` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `is_active`, `contact_number`, `position`, `avatar_path`, `last_login_at`, `barangay_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'DILG Administrator', 'admin', '$2y$12$lrystRxbY5DxDnTG7njm7eni/9Z4SOkl5F4M84S9LAgKjYoOYYrYK', 'admin', 1, NULL, NULL, NULL, '2026-10-14 07:11:38', NULL, 'Kuxzxk0zoWAYTUB3txKe9e2PMwfpL9a0pLGwA3YirEquGO8uLWH0M2xnRbZt', '2026-09-04 23:40:01', '2026-10-14 07:11:38'),
(2, 'DILG Monitoring Staff', 'staff', '$2y$12$V1p1qogvFyht2Fvln94W7uZX9X62JZff.iV87n4R1oBLH.x0KURXq', 'staff', 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-04 23:40:01', '2026-09-05 00:05:45'),
(3, 'Gian Ventura', 'Gian', '$2y$12$e1KLPOIvI.fu42viqEkKY.bEw.iqKDQPpfPHfIlnFgFlvMhfXGu82', 'admin', 1, NULL, NULL, NULL, '2026-10-25 06:50:21', NULL, 'jGUzLpHR6ZLVaZklNdmH9UdnGgy9tVKdqQnRc4IT55egerWOP1QON88y8rNA', '2026-10-01 07:25:45', '2026-10-25 06:50:21'),
(4, 'Ed Pasion', 'EJ', '$2y$12$Zh3dI4G0QwofJ1M1gD3KRegb6rnDULNiei1v1PLOlSs8Z4lGwACra', 'staff', 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-01 08:47:40', '2026-10-01 13:20:25'),
(7, 'Junell Panag', 'Nell', '$2y$12$YXjVSQBLHsImipdwkbgiqOCUQvhQ3QgvV7GyAg85rqaUcfXU0kq/S', 'barangay', 1, NULL, NULL, NULL, '2026-10-25 08:17:59', 6, NULL, '2026-10-14 07:37:58', '2026-10-25 08:17:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `first_name`, `middle_name`, `last_name`, `birthdate`, `gender`, `designation`, `roles`, `status`, `date_process`, `processed_by`, `user_id`, `created_at`, `updated_at`, `barangay_id`) VALUES
(2, 'Gian', 'Carlo', 'Ventura', '2005-08-04', 'Female', 'Barangay Councilor', 'Admin', 'active', '2026-10-01', 'DILG Administrator', 3, '2026-10-01 07:25:45', '2026-10-01 07:25:45', 5),
(3, 'Ed', 'Jashley', 'Pasion', '2005-09-23', 'Male', 'Kagawad', 'Staff', 'active', '2026-10-01', 'DILG Administrator', 4, '2026-10-01 08:47:40', '2026-10-01 08:47:40', 2),
(4, 'Junell', 'Acop', 'Panag', '2005-06-27', 'Male', 'Barangay Captain', 'Barangay', 'active', '2026-10-14', 'Gian Ventura', 7, '2026-10-14 07:37:58', '2026-10-14 07:37:58', 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangay_logs`
--
ALTER TABLE `barangay_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangay_log_details`
--
ALTER TABLE `barangay_log_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barangay_log_details_period_unique` (`barangay_log_id`,`barangay_id`,`period`),
  ADD KEY `barangay_log_details_barangay_id_foreign` (`barangay_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_report_record_id_foreign` (`report_record_id`),
  ADD KEY `notifications_barangay_id_foreign` (`barangay_id`),
  ADD KEY `notifications_requirement_id_foreign` (`requirement_id`),
  ADD KEY `notifications_barangay_log_detail_id_foreign` (`barangay_log_detail_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `report_rankings_cache`
--
ALTER TABLE `report_rankings_cache`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_rankings_cache_barangay_id_foreign` (`barangay_id`);

--
-- Indexes for table `report_records`
--
ALTER TABLE `report_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_records_unique_period` (`barangay_id`,`requirement_id`,`period`),
  ADD KEY `report_records_barangay_id_foreign` (`barangay_id`),
  ADD KEY `report_records_requirement_id_foreign` (`requirement_id`),
  ADD KEY `report_records_edited_by_foreign` (`edited_by`);

--
-- Indexes for table `report_requirements`
--
ALTER TABLE `report_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_stamps`
--
ALTER TABLE `report_stamps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_stamps_verification_code_unique` (`verification_code`),
  ADD KEY `report_stamps_barangay_log_detail_id_foreign` (`barangay_log_detail_id`),
  ADD KEY `report_stamps_user_id_foreign` (`user_id`),
  ADD KEY `report_stamps_original_file_hash_index` (`original_file_hash`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_barangay_id_foreign` (`barangay_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_logs_user_id_foreign` (`user_id`),
  ADD KEY `user_logs_barangay_id_foreign` (`barangay_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `barangay_logs`
--
ALTER TABLE `barangay_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `barangay_log_details`
--
ALTER TABLE `barangay_log_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT for table `report_rankings_cache`
--
ALTER TABLE `report_rankings_cache`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_records`
--
ALTER TABLE `report_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `report_requirements`
--
ALTER TABLE `report_requirements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `report_stamps`
--
ALTER TABLE `report_stamps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `barangay_log_details`
--
ALTER TABLE `barangay_log_details`
  ADD CONSTRAINT `barangay_log_details_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `barangay_log_details_barangay_log_id_foreign` FOREIGN KEY (`barangay_log_id`) REFERENCES `barangay_logs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_barangay_log_detail_id_foreign` FOREIGN KEY (`barangay_log_detail_id`) REFERENCES `barangay_log_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_report_record_id_foreign` FOREIGN KEY (`report_record_id`) REFERENCES `report_records` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `report_requirements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_rankings_cache`
--
ALTER TABLE `report_rankings_cache`
  ADD CONSTRAINT `report_rankings_cache_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_records`
--
ALTER TABLE `report_records`
  ADD CONSTRAINT `report_records_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`),
  ADD CONSTRAINT `report_records_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `report_records_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `report_requirements` (`id`);

--
-- Constraints for table `report_stamps`
--
ALTER TABLE `report_stamps`
  ADD CONSTRAINT `report_stamps_barangay_log_detail_id_foreign` FOREIGN KEY (`barangay_log_detail_id`) REFERENCES `barangay_log_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_stamps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
