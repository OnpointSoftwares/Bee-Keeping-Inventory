-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 06, 2025 at 11:13 AM
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
-- Database: `beekeeping`
--

-- --------------------------------------------------------

--
-- Table structure for table `beehive`
--

CREATE TABLE `beehive` (
  `hiveID` int(11) NOT NULL,
  `hiveNumber` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `dateEstablished` date NOT NULL,
  `queenAge` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `beekeeperID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beehive`
--

INSERT INTO `beehive` (`hiveID`, `hiveNumber`, `location`, `dateEstablished`, `queenAge`, `notes`, `status`, `beekeeperID`) VALUES
(1, 'HIVE-001', 'Main Apiary', '2025-05-02', 1, 'Initial test hive', 'Active', 1),
(2, '1', 'Nakuru', '2025-05-05', 3, 'In good ', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `beekeeper`
--

CREATE TABLE `beekeeper` (
  `beekeeperID` int(11) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `createdOn` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beekeeper`
--

INSERT INTO `beekeeper` (`beekeeperID`, `fullName`, `email`, `mobile`, `address`, `username`, `password`, `status`, `createdOn`) VALUES
(1, 'Admin User', 'admin@beekeeping.com', '1234567890', 'Sample Address', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Active', '2025-05-02 15:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipmentID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `condition_status` varchar(50) NOT NULL,
  `purchaseDate` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipmentID`, `name`, `type`, `quantity`, `condition_status`, `purchaseDate`, `notes`, `status`) VALUES
(1, 'Electric Drill', 'Tool', 10, 'Good', '2023-03-10', 'Used in workshop A', 'Active'),
(2, 'Safety Helmet', 'Safety Gear', 20, 'New', '2024-01-15', 'Standard issue helmets', 'Active'),
(3, 'Welding Machine', 'Tool', 5, 'Fair', '2022-06-22', 'Needs maintenance', 'Active'),
(4, 'High-Vis Vest', 'Safety Gear', 50, 'New', '2024-03-01', '', 'Active'),
(5, 'Ladder', 'Tool', 7, 'Used', '2021-11-09', 'Stored in warehouse 2', 'Active'),
(6, 'Goggles', 'Safety Gear', 30, 'Good', '2023-12-05', '', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `hive_health`
--

CREATE TABLE `hive_health` (
  `checkID` int(11) NOT NULL,
  `hiveID` int(11) NOT NULL,
  `checkDate` date NOT NULL,
  `queenPresent` tinyint(1) NOT NULL DEFAULT 1,
  `colonyStrength` varchar(20) NOT NULL,
  `diseaseSymptoms` text DEFAULT NULL,
  `pestProblems` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hive_health`
--

INSERT INTO `hive_health` (`checkID`, `hiveID`, `checkDate`, `queenPresent`, `colonyStrength`, `diseaseSymptoms`, `pestProblems`, `notes`) VALUES
(1, 1, '2025-05-02', 1, 'Strong', NULL, NULL, 'Initial health check');

-- --------------------------------------------------------

--
-- Table structure for table `honey_production`
--

CREATE TABLE `honey_production` (
  `productionID` int(11) NOT NULL,
  `hiveID` int(11) NOT NULL,
  `harvestDate` date NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `type` varchar(50) NOT NULL,
  `quality` varchar(20) NOT NULL DEFAULT 'Standard',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `honey_production`
--

INSERT INTO `honey_production` (`productionID`, `hiveID`, `harvestDate`, `quantity`, `type`, `quality`, `notes`) VALUES
(1, 1, '2025-05-02', 10.50, 'Wildflower', 'Premium', 'First harvest');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `saleID` int(11) NOT NULL,
  `productionID` int(11) NOT NULL,
  `saleDate` date NOT NULL,
  `customerName` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unitPrice` decimal(10,2) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `paymentStatus` varchar(20) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$5sPWbgBvKWUW8YLOmDHVR.GjF6pEg4oF3V9.3oOIDgfItwrlQ/3wu', 'admin', '2025-05-02 16:13:55', '2025-05-05 14:51:10'),
(2, 'your_username', '$2y$10$0drZVtwu0JjYJkFcqIE11epB9Zjfl41HSZ0EruQFipj0/D0NV4vVu', 'Your Full Name', '2025-05-05 15:10:30', '2025-05-05 15:10:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beehive`
--
ALTER TABLE `beehive`
  ADD PRIMARY KEY (`hiveID`),
  ADD UNIQUE KEY `unique_hive_number` (`hiveNumber`),
  ADD KEY `beekeeperID` (`beekeeperID`);

--
-- Indexes for table `beekeeper`
--
ALTER TABLE `beekeeper`
  ADD PRIMARY KEY (`beekeeperID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipmentID`);

--
-- Indexes for table `hive_health`
--
ALTER TABLE `hive_health`
  ADD PRIMARY KEY (`checkID`),
  ADD KEY `hiveID` (`hiveID`);

--
-- Indexes for table `honey_production`
--
ALTER TABLE `honey_production`
  ADD PRIMARY KEY (`productionID`),
  ADD KEY `hiveID` (`hiveID`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`saleID`),
  ADD KEY `productionID` (`productionID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beehive`
--
ALTER TABLE `beehive`
  MODIFY `hiveID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `beekeeper`
--
ALTER TABLE `beekeeper`
  MODIFY `beekeeperID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hive_health`
--
ALTER TABLE `hive_health`
  MODIFY `checkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `honey_production`
--
ALTER TABLE `honey_production`
  MODIFY `productionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `saleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beehive`
--
ALTER TABLE `beehive`
  ADD CONSTRAINT `beehive_ibfk_1` FOREIGN KEY (`beekeeperID`) REFERENCES `beekeeper` (`beekeeperID`) ON DELETE SET NULL;

--
-- Constraints for table `hive_health`
--
ALTER TABLE `hive_health`
  ADD CONSTRAINT `hive_health_ibfk_1` FOREIGN KEY (`hiveID`) REFERENCES `beehive` (`hiveID`) ON DELETE CASCADE;

--
-- Constraints for table `honey_production`
--
ALTER TABLE `honey_production`
  ADD CONSTRAINT `honey_production_ibfk_1` FOREIGN KEY (`hiveID`) REFERENCES `beehive` (`hiveID`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`productionID`) REFERENCES `honey_production` (`productionID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
