s
CREATE DATABASE IF NOT EXISTS `meinprojekt` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `meinprojekt`;
CREATE TABLE IF NOT EXISTS `personen` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vorname` VARCHAR(100) NOT NULL,
  `nachname` VARCHAR(100) NOT NULL,
  `geburtstag` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
