-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 03, 2024 at 10:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS `hostel_listings`;
CREATE DATABASE IF NOT EXISTS `hostel_listings`;
USE `hostel_listings`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `contact_details` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `class_year` int(11) DEFAULT NULL,
  `user_type` enum('student','resident_assistant') NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Hostels`
--
CREATE TABLE `Hostels` (
  `hostel_id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_assistant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `number_of_rooms` int(11) NOT NULL,
  PRIMARY KEY (`hostel_id`),
  KEY `fk_resident_assistant_id` (`resident_assistant_id`),
  CONSTRAINT `Hostels_ibfk_1` FOREIGN KEY (`resident_assistant_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reviews`
--

CREATE TABLE `Reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `fk_hostel_id` (`hostel_id`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `Reviews_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `Hostels` (`hostel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

CREATE TABLE `Images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `hostel_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `fk_hostel_id_images` (`hostel_id`),
  CONSTRAINT `Images_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `Hostels` (`hostel_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `CommunityPosts`
--
CREATE TABLE `CommunityPosts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` ENUM('general', 'roommate', 'tips') NOT NULL,
  `post_content` text NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `fk_user_id_posts` (`user_id`),
  CONSTRAINT `CommunityPosts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
ALTER TABLE `CommunityPosts`
ADD COLUMN `hostel_id` int(11) DEFAULT NULL,
ADD CONSTRAINT `fk_hostel_id` FOREIGN KEY (`hostel_id`) REFERENCES `Hostels`(`hostel_id`) ON DELETE SET NULL ON UPDATE CASCADE;


-- Table structure for table `Replies`
--

CREATE TABLE `Replies` (
  reply_id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  user_id INT,
  reply_content TEXT,
  reply_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES CommunityPosts(post_id),
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Table structure for table `EmergencyContacts`
--

CREATE TABLE `EmergencyContacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
