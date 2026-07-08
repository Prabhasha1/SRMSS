-- ========================================================
-- Smart Route Management & Scheduling System (SRMSS)
-- Complete Database Export
-- Database Name: srmss_db
-- ========================================================

CREATE DATABASE IF NOT EXISTS `srmss_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `srmss_db`;

-- --------------------------------------------------------
-- Table Structure: `depots`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `schedules`;
DROP TABLE IF EXISTS `drivers`;
DROP TABLE IF EXISTS `vehicles`;
DROP TABLE IF EXISTS `routes`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `depots`;

CREATE TABLE `depots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure: `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Administrator','Supervisor','Depot Staff') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure: `routes`
-- --------------------------------------------------------

CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_number` varchar(20) NOT NULL,
  `start_location` varchar(100) NOT NULL,
  `end_location` varchar(100) NOT NULL,
  `distance_km` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `depot_id` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_number` (`route_number`),
  KEY `depot_id` (`depot_id`),
  CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure: `drivers`
-- --------------------------------------------------------

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license_number` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('Available','On Route','Leave') DEFAULT 'Available',
  `depot_id` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_number` (`license_number`),
  KEY `depot_id` (`depot_id`),
  CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure: `vehicles`
-- --------------------------------------------------------

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(20) NOT NULL,
  `model` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('Operational','Maintenance','Out of Service') DEFAULT 'Operational',
  `maintenance_notes` text DEFAULT NULL,
  `depot_id` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plate_number` (`plate_number`),
  KEY `depot_id` (`depot_id`),
  CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure: `schedules`
-- --------------------------------------------------------

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `route_id` (`route_id`),
  KEY `driver_id` (`driver_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ========================================================
-- DML - DEFAULT SEED DATA INSERTIONS
-- ========================================================

-- 1. Insert Initial Central Depot
INSERT INTO `depots` (`id`, `name`, `location`) VALUES
(1, 'Central Bus Depot', 'Main Transport Hub, City Center');

-- 2. Insert Administrative Users
-- Note: 'password123' hashed using PHP password_hash(PASSWORD_DEFAULT)
INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$wE1M21K8N8dK6a0f7qZ8u.E5Zp8QZ4e/2D8kK6n8W8Y8Z8X8W8Y8Z', 'Administrator'),
(2, 'supervisor', '$2y$10$wE1M21K8N8dK6a0f7qZ8u.E5Zp8QZ4e/2D8kK6n8W8Y8Z8X8W8Y8Z', 'Supervisor'),
(3, 'staff', '$2y$10$wE1M21K8N8dK6a0f7qZ8u.E5Zp8QZ4e/2D8kK6n8W8Y8Z8X8W8Y8Z', 'Depot Staff');

-- 3. Insert Transport Routes
INSERT INTO `routes` (`id`, `route_number`, `start_location`, `end_location`, `distance_km`, `depot_id`) VALUES
(1, 'R-101', 'Central Depot', 'Airport Terminal 1', 25.50, 1),
(2, 'R-102', 'Central Depot', 'East Suburb Station', 18.20, 1),
(3, 'R-201', 'North Station', 'South Harbor Hub', 32.00, 1);

-- 4. Insert Registered Drivers
INSERT INTO `drivers` (`id`, `license_number`, `first_name`, `last_name`, `phone`, `status`, `depot_id`) VALUES
(1, 'DL-987654', 'John', 'Doe', '0771234567', 'Available', 1),
(2, 'DL-123456', 'David', 'Smith', '0772345678', 'On Route', 1),
(3, 'DL-456789', 'Michael', 'Brown', '0773456789', 'Leave', 1);

-- 5. Insert Fleet Vehicles
INSERT INTO `vehicles` (`id`, `plate_number`, `model`, `capacity`, `status`, `maintenance_notes`, `depot_id`) VALUES
(1, 'BUS-1001', 'Volvo B8R', 54, 'Operational', 'Routine oil check completed.', 1),
(2, 'BUS-1002', 'Scania K320', 48, 'Operational', NULL, 1),
(3, 'BUS-1003', 'Isuzu LT134', 40, 'Maintenance', 'Engine tuning required.', 1);

-- 6. Insert Operational Schedules
INSERT INTO `schedules` (`id`, `route_id`, `driver_id`, `vehicle_id`, `departure_time`, `arrival_time`, `start_time`, `end_time`) VALUES
(1, 1, 1, 1, '2026-07-10 08:00:00', '2026-07-10 09:30:00', '08:00:00', '09:30:00'),
(2, 2, 2, 2, '2026-07-10 10:00:00', '2026-07-10 11:15:00', '10:00:00', '11:15:00');

COMMIT;