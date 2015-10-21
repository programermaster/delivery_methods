-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 21, 2015 at 02:10 AM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `delivery_methods`
--
CREATE DATABASE IF NOT EXISTS `delivery_methods` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `delivery_methods`;

-- --------------------------------------------------------

--
-- Table structure for table `method`
--

CREATE TABLE IF NOT EXISTS `method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` decimal(5,2) DEFAULT NULL,
  `delivery_url` varchar(255) NOT NULL,
  `from_weight` decimal(5,2) NOT NULL,
  `to_weight` decimal(5,2) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `method`
--

INSERT INTO `method` (`id`, `name`, `value`, `delivery_url`, `from_weight`, `to_weight`, `notes`) VALUES
(1, 'Delivery method0', NULL, '', 0.00, 0.00, ''),
(2, 'Delivery method1', 0.00, '', 0.00, 0.00, ''),
(3, 'Delivery method2', 10.00, 'http://www.google.com', 15.00, 25.00, 'This is super delivery'),
(4, 'Delivery method2', 20.00, 'http://www.yahoo.com', 10.00, 40.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `ranges`
--

CREATE TABLE IF NOT EXISTS `ranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` decimal(5,2) NOT NULL DEFAULT '0.00',
  `to` decimal(5,2) NOT NULL DEFAULT '0.00',
  `price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `delivery_method_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ranges`
--

INSERT INTO `ranges` (`id`, `from`, `to`, `price`, `delivery_method_id`, `order`) VALUES
(1, 10.00, 20.00, 15.00, 3, 0),
(2, 30.00, 40.00, 35.00, 3, 2),
(4, 50.00, 60.00, 55.00, 2, 0),
(5, 40.00, 50.00, 45.00, 3, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
