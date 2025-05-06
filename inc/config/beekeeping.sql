-- Database: `beekeeping`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `honey_production`;
DROP TABLE IF EXISTS `hive_health`;
DROP TABLE IF EXISTS `equipment`;
DROP TABLE IF EXISTS `beehive`;
DROP TABLE IF EXISTS `beekeeper`;

-- Create tables
CREATE TABLE `beekeeper` (
  `beekeeperID` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`beekeeperID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `beehive` (
  `hiveID` int(11) NOT NULL AUTO_INCREMENT,
  `hiveNumber` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `dateEstablished` date NOT NULL,
  `queenAge` int(11) DEFAULT NULL,
  `notes` text,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `beekeeperID` int(11),
  PRIMARY KEY (`hiveID`),
  UNIQUE KEY `unique_hive_number` (`hiveNumber`),
  FOREIGN KEY (`beekeeperID`) REFERENCES `beekeeper`(`beekeeperID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `equipment` (
  `equipmentID` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(100) NOT NULL,
  `itemNumber` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unitPrice` decimal(10,2) NOT NULL,
  `purchaseDate` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `notes` text,
  `beekeeperID` int(11),
  PRIMARY KEY (`equipmentID`),
  UNIQUE KEY `unique_item_number` (`itemNumber`),
  FOREIGN KEY (`beekeeperID`) REFERENCES `beekeeper`(`beekeeperID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `honey_production` (
  `productionID` int(11) NOT NULL AUTO_INCREMENT,
  `hiveID` int(11) NOT NULL,
  `harvestDate` date NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `type` varchar(50) NOT NULL,
  `quality` varchar(20) NOT NULL DEFAULT 'Standard',
  `notes` text,
  PRIMARY KEY (`productionID`),
  FOREIGN KEY (`hiveID`) REFERENCES `beehive`(`hiveID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `hive_health` (
  `checkID` int(11) NOT NULL AUTO_INCREMENT,
  `hiveID` int(11) NOT NULL,
  `checkDate` date NOT NULL,
  `queenPresent` tinyint(1) NOT NULL DEFAULT 1,
  `colonyStrength` varchar(20) NOT NULL,
  `diseaseSymptoms` text,
  `pestProblems` text,
  `notes` text,
  PRIMARY KEY (`checkID`),
  FOREIGN KEY (`hiveID`) REFERENCES `beehive`(`hiveID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sales` (
  `saleID` int(11) NOT NULL AUTO_INCREMENT,
  `productionID` int(11) NOT NULL,
  `saleDate` date NOT NULL,
  `customerName` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unitPrice` decimal(10,2) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `paymentStatus` varchar(20) NOT NULL DEFAULT 'Pending',
  `notes` text,
  PRIMARY KEY (`saleID`),
  FOREIGN KEY (`productionID`) REFERENCES `honey_production`(`productionID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO `beekeeper` (`fullName`, `email`, `mobile`, `address`, `username`, `password`, `status`) 
VALUES ('Admin User', 'admin@beekeeping.com', '1234567890', 'Sample Address', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Active');

-- Insert sample hive
INSERT INTO `beehive` (`hiveNumber`, `location`, `dateEstablished`, `queenAge`, `notes`, `beekeeperID`) 
SELECT 'HIVE-001', 'Main Apiary', CURDATE(), 1, 'Initial test hive', beekeeperID 
FROM `beekeeper` WHERE username = 'admin';

-- Insert sample health check
INSERT INTO `hive_health` (`hiveID`, `checkDate`, `queenPresent`, `colonyStrength`, `notes`)
SELECT hiveID, CURDATE(), 1, 'Strong', 'Initial health check' 
FROM `beehive` WHERE hiveNumber = 'HIVE-001';

-- Insert sample honey production
INSERT INTO `honey_production` (`hiveID`, `harvestDate`, `quantity`, `type`, `quality`, `notes`)
SELECT hiveID, CURDATE(), 10.5, 'Wildflower', 'Premium', 'First harvest' 
FROM `beehive` WHERE hiveNumber = 'HIVE-001';

COMMIT;
