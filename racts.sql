-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 02:32 PM
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
-- Database: `racts`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangay`
--

CREATE TABLE `barangay` (
  `ID` int(10) UNSIGNED NOT NULL,
  `BarangayName` varchar(150) NOT NULL DEFAULT '''',
  `Stats` varchar(50) NOT NULL DEFAULT '''',
  `DateProcess` date NOT NULL DEFAULT '1990-01-01',
  `ProcessBy` varchar(150) NOT NULL DEFAULT ''''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangaylogs`
--

CREATE TABLE `barangaylogs` (
  `ID` int(10) UNSIGNED NOT NULL,
  `ItemDescription` varchar(45) NOT NULL DEFAULT '''',
  `DeadlineDate` datetime NOT NULL DEFAULT '1990-01-01 00:00:00',
  `DateProcess` date NOT NULL DEFAULT '1990-01-01',
  `ProcessBy` varchar(150) NOT NULL DEFAULT '''',
  `Stats` varchar(150) NOT NULL DEFAULT ''''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barangaylogsdetails`
--

CREATE TABLE `barangaylogsdetails` (
  `ID` int(10) UNSIGNED NOT NULL,
  `ItemID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `BarangayName` varchar(150) NOT NULL DEFAULT '''',
  `ItemDescription` varchar(150) NOT NULL DEFAULT '''',
  `DeadlineDate` date NOT NULL DEFAULT '1990-01-01',
  `DateSubmitted` datetime NOT NULL DEFAULT '1990-01-01 00:00:00',
  `DateProcess` date NOT NULL DEFAULT '1990-01-01',
  `ProcessBy` varchar(150) NOT NULL DEFAULT '''',
  `Stats` varchar(150) NOT NULL DEFAULT ''''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userslogs`
--

CREATE TABLE `userslogs` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Firstname` varchar(150) NOT NULL DEFAULT '''',
  `Middlename` varchar(150) NOT NULL DEFAULT '''',
  `Lastname` varchar(150) NOT NULL DEFAULT '''',
  `Birthdate` date NOT NULL DEFAULT '1990-01-01',
  `Gender` varchar(150) NOT NULL DEFAULT '''',
  `Designation` varchar(150) NOT NULL DEFAULT '''',
  `Roles` varchar(150) NOT NULL DEFAULT '''',
  `Stats` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `DateProcess` date NOT NULL DEFAULT '1990-01-01',
  `ProcessBy` varchar(150) NOT NULL DEFAULT '''',
  `BarangayName` varchar(150) NOT NULL DEFAULT ''''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay`
--
ALTER TABLE `barangay`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `barangaylogs`
--
ALTER TABLE `barangaylogs`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `barangaylogsdetails`
--
ALTER TABLE `barangaylogsdetails`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `userslogs`
--
ALTER TABLE `userslogs`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangay`
--
ALTER TABLE `barangay`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangaylogs`
--
ALTER TABLE `barangaylogs`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangaylogsdetails`
--
ALTER TABLE `barangaylogsdetails`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userslogs`
--
ALTER TABLE `userslogs`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
