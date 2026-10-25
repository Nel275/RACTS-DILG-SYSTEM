SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `barangays`;
DROP TABLE IF EXISTS `barangay_logs`;
DROP TABLE IF EXISTS `barangay_log_details`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `report_rankings_cache`;
DROP TABLE IF EXISTS `report_records`;
DROP TABLE IF EXISTS `report_requirements`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `user_logs`;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2026 at 08:10 PM
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
(110, 3, 'Recorded submission for \"Kasambahay Report\" (Calao East) - Submitted Late.', '2026-10-02 22:31:50', '2026-10-02 14:31:50', '2026-10-02 14:31:50');

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
  `deadline_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay_logs`
--

INSERT INTO `barangay_logs` (`id`, `item_description`, `council_type`, `deadline_date`, `status`, `date_process`, `processed_by`, `created_at`, `updated_at`) VALUES
(1, 'Kasambahay Report', 'barangay', '2026-09-28', 'active', '2026-09-28', 'DILG Administrator', '2026-09-28 14:12:25', '2026-09-28 14:12:25'),
(2, 'Katarungang Pambarangay Report', 'barangay', '2026-12-31', 'active', '2026-10-01', 'Gian Ventura', '2026-10-01 08:36:33', '2026-10-01 08:36:33'),
(3, 'Minutes of Meeting Report', 'sk', '2026-10-31', 'active', NULL, NULL, '2026-10-02 05:38:12', '2026-10-02 05:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_log_details`
--

CREATE TABLE `barangay_log_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_log_id` bigint(20) UNSIGNED NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `deadline_date` date NOT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `report_file_path` varchar(255) DEFAULT NULL,
  `report_original_name` varchar(255) DEFAULT NULL,
  `report_filed_at` datetime DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_process` date DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangay_log_details`
--

INSERT INTO `barangay_log_details` (`id`, `barangay_log_id`, `item_description`, `deadline_date`, `date_submitted`, `report_file_path`, `report_original_name`, `report_filed_at`, `status`, `date_process`, `processed_by`, `created_at`, `updated_at`, `barangay_id`) VALUES
(1, 1, 'Kasambahay Report', '2026-09-28', '2026-10-02 22:31:00', NULL, NULL, NULL, 'late', '2026-10-02', 'Gian Ventura', '2026-09-28 14:15:26', '2026-10-02 14:31:50', 1),
(2, 1, 'Kasambahay Report', '2026-12-31', '2026-10-01 14:50:00', 'barangays/Malvar/2-78606ce4-90de-4aa0-a0f8-ccc473d2bc60.jpg', '78606ce4-90de-4aa0-a0f8-ccc473d2bc60.jpg', '2026-10-01 14:51:45', 'submitted', '2026-10-01', 'DILG Administrator', '2026-09-29 12:43:33', '2026-10-01 06:51:45', 6),
(3, 1, 'Kasambahay Report', '2026-11-30', NULL, NULL, NULL, NULL, 'closed', '2026-10-02', 'Gian Ventura', '2026-10-01 08:21:07', '2026-10-02 13:20:34', 6),
(4, 2, 'Katarungang Pambarangay Report', '2026-12-31', '2026-10-01 18:21:00', 'barangays/Centro West/4-ISO EVALUATION RACTS.docx', 'ISO EVALUATION RACTS.docx', '2026-10-01 18:22:40', 'submitted', '2026-10-01', 'DILG Monitoring Staff', '2026-10-01 08:36:52', '2026-10-01 10:22:40', 2),
(5, 2, 'Katarungang Pambarangay Report', '2026-12-31', '2026-10-01 16:48:00', NULL, NULL, NULL, 'submitted', '2026-10-01', 'Ed Pasion', '2026-10-01 08:37:09', '2026-10-01 08:48:55', 5),
(6, 2, 'Katarungang Pambarangay Report', '2026-12-31', '2026-10-02 13:09:00', 'incoming-reports/6/OxM4WapkHjreYCRPx3woaktSyzu6qhtFdZX6zNgl.docx', 'ISO EVALUATION RACTS (1).docx', NULL, 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-01 14:11:41', '2026-10-02 05:09:55', 8),
(7, 3, 'Minutes of Meeting Report', '2026-10-31', '2026-10-02 13:40:00', 'incoming-reports/7/qOYMFpyEwqj4rIJkdKmUacfzese9fP7uAvjEtzCO.jpg', 'Picapica-photostrip (5).jpg', NULL, 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-02 05:38:12', '2026-10-02 05:41:01', 1),
(8, 1, 'Kasambahay Report', '2026-10-31', '2026-10-02 21:12:00', 'barangays/Victory Norte/8-ISO EVALUATION RACTS (1).docx', 'ISO EVALUATION RACTS (1).docx', '2026-10-02 21:13:20', 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-02 13:11:50', '2026-10-02 13:13:20', 9),
(9, 2, 'Katarungang Pambarangay Report', '2026-12-31', '2026-10-02 22:31:00', NULL, NULL, NULL, 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-02 13:34:20', '2026-10-02 14:31:28', 11),
(10, 1, 'Kasambahay Report', '2026-12-31', '2026-10-02 21:35:00', 'barangays/Victory Sur/10-ISO EVALUATION RACTS (1) (4).docx', 'ISO EVALUATION RACTS (1) (4).docx', '2026-10-02 21:35:52', 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-02 13:34:33', '2026-10-02 13:35:52', 11),
(11, 1, 'Kasambahay Report', '2026-12-29', '2026-10-02 21:35:00', 'barangays/Victory Sur/11-829528822_122336472380020618_1826212030456877022_n.jpg', '829528822_122336472380020618_1826212030456877022_n.jpg', '2026-10-02 21:36:07', 'submitted', '2026-10-02', 'Gian Ventura', '2026-10-02 13:35:12', '2026-10-02 13:36:07', 11),
(12, 1, 'Kasambahay Report', '2026-10-31', NULL, NULL, NULL, NULL, 'pending', '2026-10-02', 'Gian Ventura', '2026-10-02 14:30:22', '2026-10-02 14:30:22', 13);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(23, '2026_10_02_000001_add_council_type_to_barangay_logs_table', 13);

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

INSERT INTO `notifications` (`id`, `user_id`, `report_record_id`, `type`, `barangay_id`, `requirement_id`, `period`, `message`, `notification_date`, `is_read`, `auto_generated`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(4, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(6, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-05 09:27:41', 1, 0, '2026-09-05 01:27:41', '2026-09-05 02:45:01', NULL),
(8, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(10, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(12, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: ADILI has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(14, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(16, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(18, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(20, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: PITIPIWPIW WIWIW has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(22, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(24, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(26, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:13', 0, 0, '2026-09-14 16:07:13', '2026-09-14 16:07:13', NULL),
(28, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: RENE has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 00:07:14', 0, 0, '2026-09-14 16:07:14', '2026-09-14 16:07:14', NULL),
(30, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(32, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(34, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(36, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(38, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Calao East has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(40, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(42, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Minutes of Meeting\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(44, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(46, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(48, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Centro West has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(50, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"Kalinisan Day Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(52, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"Monthly Accomplishment Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(54, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"SGB Report\" (deadline was January 1, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(56, 2, NULL, NULL, NULL, NULL, NULL, 'Overdue: Villasis has not submitted \"VAWC Report\" (deadline was September 15, 2026).', '2026-09-15 01:11:58', 0, 0, '2026-09-14 17:11:58', '2026-09-14 17:11:58', NULL),
(58, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(60, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(62, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(64, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(66, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(68, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(70, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(72, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(74, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(76, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(78, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(80, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(82, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Kasambahay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(84, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(86, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(88, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(90, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(92, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(94, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(96, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(98, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(100, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(102, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(104, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-24 15:54:43', 0, 1, '2026-09-24 07:54:43', '2026-09-24 07:54:43', NULL),
(106, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(108, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(110, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment\" for August 2026.', '2026-09-24 18:03:50', 0, 1, '2026-09-24 10:03:50', '2026-09-24 10:03:50', NULL),
(159, 1, 22, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(160, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(161, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(162, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(163, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(164, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(165, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(166, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(167, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(168, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(169, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(170, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(171, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(172, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(173, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(174, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(175, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(176, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(177, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(178, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(179, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(180, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(181, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(182, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:20:40', 0, 1, '2026-09-24 21:20:40', '2026-09-24 21:27:27', '2026-09-24 21:27:27'),
(183, 1, 22, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(184, 2, 22, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(185, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(186, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(187, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(188, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(189, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(190, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(191, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(192, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(193, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(194, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(195, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(196, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(197, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(198, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(199, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(200, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(201, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(202, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(203, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(204, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(205, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(206, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(207, 1, 23, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(208, 2, 23, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Kasambahay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(209, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(210, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Katarungang Pambarangay Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(211, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(212, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Barangay Monthly Accomplishment Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(213, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(214, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"Road Clearing Operation Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(215, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(216, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"1st Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(217, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(218, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (Barangay Council) did not submit \"2nd Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(219, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(220, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(221, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(222, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Calao East (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(223, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(224, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(225, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(226, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Centro West (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(227, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(228, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Accomplishment\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(229, 1, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:54:42', '2026-09-24 21:54:42'),
(230, 2, NULL, NULL, NULL, NULL, NULL, 'Late: Villasis (SK Council) did not submit \"Minutes of Meeting Report\" for August 2026.', '2026-09-25 05:53:55', 0, 1, '2026-09-24 21:53:55', '2026-09-24 21:53:55', NULL),
(231, 1, 22, 'late_submission', 1, 7, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(232, 2, 22, 'late_submission', 1, 7, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(233, 1, 30, 'late_submission', 1, 8, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 1, 1, '2026-09-25 02:04:05', '2026-09-29 12:46:21', NULL),
(234, 2, 30, 'late_submission', 1, 8, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 10:04:05', 0, 1, '2026-09-25 02:04:05', '2026-09-25 02:04:05', NULL),
(235, 1, 33, 'late_submission', 6, 7, '2026-08', 'Late Submission: Malvar (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 18:23:13', 0, 1, '2026-09-25 10:23:13', '2026-09-25 10:23:13', NULL),
(236, 2, 33, 'late_submission', 6, 7, '2026-08', 'Late Submission: Malvar (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-09-25 18:23:13', 0, 1, '2026-09-25 10:23:13', '2026-09-25 10:23:13', NULL),
(237, 3, 22, 'late_submission', 1, 7, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(238, 4, 22, 'late_submission', 1, 7, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Kasambahay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(239, 3, 30, 'late_submission', 1, 8, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(240, 4, 30, 'late_submission', 1, 8, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Katarungang Pambarangay Report\" for August 2026 on September 25, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(241, 1, 38, 'late_submission', 1, 9, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(242, 2, 38, 'late_submission', 1, 9, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(243, 3, 38, 'late_submission', 1, 9, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL),
(244, 4, 38, 'late_submission', 1, 9, '2026-08', 'Late Submission: Calao East (Barangay Council) submitted \"Barangay Monthly Accomplishment Report\" for August 2026 on October 2, 2026, after the September 5, 2026 deadline.', '2026-10-02 21:18:42', 0, 1, '2026-10-02 13:18:42', '2026-10-02 13:18:42', NULL);

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
(9, 2, 7, '2026-10', '2026-11-13', NULL, NULL, 1, '2026-09-24 19:12:30', '2026-09-24 09:47:42', '2026-09-24 11:12:30'),
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
('3XepumD5MRAZ8nLqzGnj6ak6I7WWTITwe3SZvbyr', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/2.19675.0 Chrome/152.0.7977.130 Safari/537.36 MSIX', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2J2a0E0QmRPZkR1aEtsbGlSMW04ZmhhM3ZrV2gyVVFObWQ3VTdDTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9sb2NhbGhvc3QvcmFjdHMvcHVibGljIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790956692),
('3Yas4JCUy4DnuOewGovC4ioxeR9RivEa9S0W363e', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/2.19675.0 Chrome/152.0.7977.130 Safari/537.36 MSIX', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia3BBUlFtQ2h4NkZLRzFkWXBESDlSd2U0b1lkdGtoN3ljNTVKd3NDUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC91c2VycyI7czo1OiJyb3V0ZSI7czoxMToidXNlcnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1790959752),
('9wpio0XDvky927OqtvQWjpHTaihvxg42QFwuKKe9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/2.19675.0 Chrome/152.0.7977.130 Safari/537.36 MSIX', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaGR3ZWE0YXhrNFI4ejdxZVhqNVB1SXhDYktxaTk2SWxTdUhzblJNdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790956728),
('hbWn3FOFtL3TLYLE0rCl4gFWm1e3HsSoYfCij7iW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSU1OYVR3aldpWnNWY3FjNWQ4RTY4aTdKYVlwSGZEQ2JEaU9wT1pkNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790956709),
('kRHyPkq1dni6qzinUgjOzcpH1xyFGJWiIMYrmnKh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0dhYkJJTEpJakEwclVjSUtJdGFDbldpU1lHTmpEWThZUTNvejV4bCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790946474),
('QwRplE31upWQXm2bkF5FN0xf9oMzJp3S7s118Eh0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic25DNDFGVzdOUFVjekNiTlZHSkJXNW5oRWhnbVBCNE1zQjdrRnNZVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790962996),
('UMNf2ahRvFfKy6Y3yVw2KkigrndluHr9Qz94Mqf8', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQlgxM2V4NVJHd1dISDhCYUlCNXVwUVk4Rlpja0FFU1l4Z01YZXpFeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1790956150);

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
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `barangay_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'DILG Administrator', 'admin', '$2y$12$lrystRxbY5DxDnTG7njm7eni/9Z4SOkl5F4M84S9LAgKjYoOYYrYK', 'admin', NULL, 'ItKxE3kUXMZuXdOryQ269YguE9ygWQ2AQnMH69G3lhSjO70HqF06VUmSDrwR', '2026-09-04 23:40:01', '2026-09-05 00:05:36'),
(2, 'DILG Monitoring Staff', 'staff', '$2y$12$V1p1qogvFyht2Fvln94W7uZX9X62JZff.iV87n4R1oBLH.x0KURXq', 'staff', NULL, NULL, '2026-09-04 23:40:01', '2026-09-05 00:05:45'),
(3, 'Gian Ventura', 'Gian', '$2y$12$e1KLPOIvI.fu42viqEkKY.bEw.iqKDQPpfPHfIlnFgFlvMhfXGu82', 'admin', NULL, '6iF0EullFZUNb6vb9Lq8t3U14Q9ttYLnYSx2YUMS1MRluQDVAUqRSR0t0Spa', '2026-10-01 07:25:45', '2026-10-01 07:25:45'),
(4, 'Ed Pasion', 'EJ', '$2y$12$Zh3dI4G0QwofJ1M1gD3KRegb6rnDULNiei1v1PLOlSs8Z4lGwACra', 'staff', NULL, NULL, '2026-10-01 08:47:40', '2026-10-01 13:20:25');

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
(3, 'Ed', 'Jashley', 'Pasion', '2005-09-23', 'Male', 'Kagawad', 'Staff', 'active', '2026-10-01', 'DILG Administrator', 4, '2026-10-01 08:47:40', '2026-10-01 08:47:40', 2);

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
  ADD KEY `barangay_log_details_barangay_log_id_foreign` (`barangay_log_id`),
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
  ADD KEY `notifications_requirement_id_foreign` (`requirement_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `barangay_logs`
--
ALTER TABLE `barangay_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `barangay_log_details`
--
ALTER TABLE `barangay_log_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
SET FOREIGN_KEY_CHECKS=1;
