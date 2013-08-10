-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 21, 2013 at 11:03 AM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `WPBuilder`
--

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE `Roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Role` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `Roles`
--

INSERT INTO `Roles` (`id`, `Role`) VALUES
(1, 'admin'),
(2, 'creator');

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE `Sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site` varchar(50) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `portalId` int(11) NOT NULL,
  `merchantId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deleted` int(1) DEFAULT '0',
  `deleted_by` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'tim',
  PRIMARY KEY (`id`),
  UNIQUE KEY `time` (`time`),
  UNIQUE KEY `site` (`site`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_2` (`email`),
  KEY `role_id` (`role_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`id`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'wahab.qureshi@digitalanimal.com', '99adc231b045331e514a516b4b7680f588e3823213abe901738bc3ad67b2f6fcb3c64efb93d18002588d3ccc1a49efbae1ce20cb43df36b38651f11fa75678e8', 1, '2013-02-20 13:45:44');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Sites`
--
ALTER TABLE `Sites`
  ADD CONSTRAINT `Sites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `Users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
