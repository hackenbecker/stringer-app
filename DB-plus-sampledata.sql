-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2024 at 11:14 AM
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
-- Database: `sdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int(3) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `level`, `email`, `active`) VALUES
(2, 'admin', '$2y$10$XxU4Xgr8uFhL23Iw3ghKo.wRZiRAKmkvlHiawXGNsqm79weV6DLL6', 1, 'hackenbecker@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `all_string`
--

CREATE TABLE `all_string` (
  `string_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `type` varchar(50) NOT NULL,
  `sportid` int(5) NOT NULL,
  `length` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `all_string`
--

INSERT INTO `all_string` (`string_id`, `brand`, `notes`, `type`, `sportid`, `length`) VALUES
(1, 'Yonex', '', 'BG80', 1, 200),
(2, 'Yonex', '', 'Exbolt 65', 1, 200),
(3, 'Yonex', '', 'BG66 Ultimax', 1, 200),
(4, 'Ashaway', '', 'Rally 21 Fire', 1, 200),
(5, 'Yonex', '', 'Aerobite Boost', 1, 200),
(6, 'Owner', '', 'Supplied', 1, 200),
(7, 'Tecnifibre', ' ', 'Razor Soft 18/1.20', 2, 0),
(8, 'Tecnifibre ', ' ', 'Razor Soft 17/1.25', 2, 0),
(9, 'Tecnifibre', ' ', 'X-One Biphase 17/1.24', 2, 0),
(10, 'Head', ' ', 'Rip Control', 2, 200),
(11, 'Head', '1.2m guague', 'Hawk', 2, 200),
(12, 'Yonex', '0.66mm Gauge', 'Nanogy 98', 1, 200),
(13, 'Ashaway', '0.69mm Gauge', 'Zymax 69 Fire', 1, 200),
(14, 'Yonex', '0.70mm Gauge', 'BG65 Ti', 1, 200),
(15, 'Prince', 'All round playability and value', 'Synthetic Gut w/Duraflex', 2, 200),
(16, 'Tecnifibre', 'Comfort, power, feel', 'NRG2', 2, 200),
(17, 'Babolat', 'Maximum comfort, power, feel, tension maintenance', 'Touch VS', 2, 200),
(18, 'Wilson', 'Maximum possibilities, fewer trade-offs', 'Champion\'s Choice', 2, 200),
(19, 'Solinco', 'Control', 'Tour Bite', 2, 200),
(20, 'WeissCannon', 'Spin', 'Ultra Cable', 2, 200),
(21, 'Wilson', 'Control', 'NXT', 2, 200);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_ID` int(5) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Notes` varchar(500) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mobile` varchar(20) NOT NULL,
  `pref_string` int(6) NOT NULL,
  `pref_stringc` int(6) NOT NULL,
  `tension` varchar(5) NOT NULL,
  `tensionc` varchar(5) NOT NULL,
  `prestretch` varchar(5) NOT NULL,
  `racketid` int(10) NOT NULL,
  `discount` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_ID`, `Name`, `Notes`, `Email`, `Mobile`, `pref_string`, `pref_stringc`, `tension`, `tensionc`, `prestretch`, `racketid`, `discount`) VALUES
(1, 'Chris Jones', '', '', '', 2, 0, '25', '0', '0', 1, 0),
(2, 'James O\'Connor', '', '', '', 5, 0, '27', '0', '0', 2, 0),
(3, 'Ashley Smith', '', '', '', 1, 0, '30', '0', '0', 3, 0),
(4, 'Barry Marshall', '', '', '', 3, 0, '28', '0', '0', 0, 0),
(5, 'Ben Smith', '', '', '', 1, 0, '28', '0', '0', 0, 0),
(6, 'Bradley O\'connor', '', '', '', 6, 0, '29', '0', '0', 1, 0),
(7, 'Chris ', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(8, 'Clive', 'Next restring $currency2 off', '', '', 0, 0, '25', '0', '0', 0, 0),
(9, 'Craig', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(10, 'Damien', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(11, 'Damon', '', '', '', 0, 0, '27', '0', '0', 0, 0),
(12, 'Dan', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(13, 'Darren Jones', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(14, 'Dave Webb', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(15, 'Nick', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(16, 'Fay', '', '', '', 0, 0, '51', '0', '0', 0, 0),
(17, 'Gary', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(18, 'Gerald', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(19, 'Hannah', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(20, 'James Hannley', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(21, 'James Thompson', '', '', '', 0, 0, '55', '0', '0', 0, 0),
(22, 'Jeanette Long', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(23, 'John Baker', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(24, 'Jon Smith', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(25, 'Josh Stevens', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(26, 'Kevin ', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(27, 'Lara', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(28, 'Martin', '', '', '', 1, 0, '24', '0', '0', 0, 0),
(29, 'Martin Thompson ', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(30, 'Michael ', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(31, 'N.Brooks', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(32, 'Olly', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(33, 'Richard Harrison', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(34, 'Rob', '', '', '', 0, 0, '23', '0', '0', 0, 0),
(35, 'Robin Smith', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(36, 'Rohit', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(37, 'Sarah Jones', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(38, 'Sarwar', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(39, 'Simon', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(40, 'Stephen', '', '', '', 0, 0, '22', '0', '0', 0, 0),
(41, 'Steve Page', '', '', '', 0, 0, '24', '0', '0', 0, 0),
(42, 'Stuart', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(43, 'Tom', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(44, 'Tom Marshall', '', '', '', 0, 0, '28', '0', '0', 0, 0),
(45, 'Tony', '', '', '', 0, 0, '27', '0', '0', 0, 0),
(46, 'Vinay', '', '', '', 0, 0, '29', '0', '0', 0, 0),
(47, 'Matt Thomas', '', '', '', 0, 0, '25', '0', '0', 0, 0),
(48, 'Dan', '', '', '', 0, 0, '26', '0', '0', 0, 0),
(49, 'Hugh', '', '', '', 0, 0, '55', '35', '0', 34, 20),
(50, 'Kingsley', '', '', '', 4, 0, '26', '35', '0', 39, 0),
(52, 'Mark ', '', '', '', 11, 0, '55', '55', '0', 40, 0);

-- --------------------------------------------------------

--
-- Table structure for table `grip`
--

CREATE TABLE `grip` (
  `gripid` int(11) NOT NULL,
  `Price` varchar(6) NOT NULL,
  `type` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grip`
--

INSERT INTO `grip` (`gripid`, `Price`, `type`) VALUES
(2, '5.00', 'Yonex Grap');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rackets`
--

CREATE TABLE `rackets` (
  `racketid` int(7) NOT NULL,
  `manuf` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `sport` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rackets`
--

INSERT INTO `rackets` (`racketid`, `manuf`, `model`, `pattern`, `sport`) VALUES
(1, 'Victor', 'Thruster F', '', 1),
(2, 'Victor', 'Jetspeed S12', '', 1),
(3, 'Victor', 'Bravesword 1', '', 1),
(4, 'Yonex', '88D Pro', '', 1),
(5, 'Yonex', '88S Pro', '', 1),
(6, 'Yonex', 'Arcsabre', '', 1),
(7, 'Yonex', 'Astrox 77', '', 1),
(8, 'Yonex', '88S', '', 1),
(9, 'Yonex', 'Duora', '', 1),
(10, 'Apacs', 'Ziggler', '', 1),
(11, 'Ashaway', 'Quantum Q5', '', 1),
(12, 'Yonex', 'Arcsabre FD', '', 1),
(13, 'Ashaway', 'Vtc force', '', 1),
(14, 'Yonex', 'Voltric Z Force', '', 1),
(15, 'Carlton', 'Airstream', '', 1),
(16, 'Apacs', 'Wave 10', '', 1),
(17, 'Carlton', 'Attack 200', '', 1),
(18, 'Carlton', 'Fury', '', 1),
(19, 'Carlton', 'Kenesis XT', '', 1),
(20, 'Victor', 'Bravesword LYD', '', 1),
(21, 'Yonex', 'Astrox force', '', 1),
(22, 'Yonex', 'Cab 20', '', 1),
(23, 'Yonex', 'Duora Strike', '', 1),
(24, 'Yonex', 'Muscle power', '', 1),
(25, 'Yonex', 'Nano Flare', '', 1),
(26, 'Yonex', 'Nanoray 900', '', 1),
(27, 'Yonex', 'Nano Speed', '', 1),
(28, 'Yonex', 'Voltric 50 Neo', '', 1),
(29, 'Yonex', 'Z Force 2', '', 1),
(30, 'Yonex', 'Arcsabre 001', '', 1),
(31, 'Gosen', 'Gungnir 07R', '', 1),
(32, 'Victor', 'Bravesword 1800', 'https://dancewithbirdie.files.wordpress.com/2018/06/stringpattern.pdf', 1),
(33, 'Head', 'Heat 1G', '', 2),
(34, 'Wilson', 'Pro-Staff 61-90', '', 2),
(35, 'Head', 'Prestige pro 200', '', 2),
(36, 'Yonex', 'Ezone98', '', 2),
(37, 'Yonex', 'Astrox Smash', '', 1),
(38, 'Wilson ', 'Pro-Staff 90', '', 2),
(39, 'Apacs', 'Woven power', '', 1),
(40, 'Head', 'Extreme MPL 600', '', 2),
(41, 'Yonex', 'Astrox 100 ZZ', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sport`
--

CREATE TABLE `sport` (
  `sportid` int(4) NOT NULL,
  `sportname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sport`
--

INSERT INTO `sport` (`sportid`, `sportname`) VALUES
(1, 'Badminton'),
(2, 'Tennis'),
(3, 'Squash');

-- --------------------------------------------------------

--
-- Table structure for table `string`
--

CREATE TABLE `string` (
  `stringid` int(5) NOT NULL,
  `stock_id` varchar(50) NOT NULL,
  `string_number` varchar(4) NOT NULL,
  `Owner_supplied` varchar(3) NOT NULL,
  `purchase_date` varchar(10) NOT NULL,
  `note` varchar(100) NOT NULL,
  `reel_no` int(6) NOT NULL,
  `reel_price` varchar(6) NOT NULL,
  `racket_price` varchar(5) NOT NULL,
  `empty` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `string`
--

INSERT INTO `string` (`stringid`, `stock_id`, `string_number`, `Owner_supplied`, `purchase_date`, `note`, `reel_no`, `reel_price`, `racket_price`, `empty`) VALUES
(1, '1', '23', 'no', '05/05/2024', '', 1, '98', '17', 1),
(2, '2', '20', 'no', '03/04/2023', '', 1, '114', '18', 1),
(3, '3', '7', 'no', '10/04/2024', 'Yonex Ultimax Yellow', 1, '88', '17', 0),
(4, '4', '12', 'no', '05/04/2024', 'Ashaway Rally 21 Fire White', 1, '47', '15', 0),
(5, '5', '14', 'yes', '10/03/2023', 'James reel', 1, '0', '12', 0),
(6, '5', '17', 'yes', '10/04/2023', 'Brad\'s reel', 1, '0', '12', 0),
(7, '6', '6', 'yes', '01/01/2023', 'Owner supplied string', 1, '0', '12', 0),
(8, '7', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1),
(9, '8', '2', 'yes', '12/06/2024', '', 1, '0', '12', 1),
(10, '9', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1),
(11, '10', '1', 'yes', '25/06/2024', '', 1, '0', '12', 0),
(12, '11', '0', 'yes', '15/07/2024', '', 1, '0', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `stringjobs`
--

CREATE TABLE `stringjobs` (
  `job_id` int(10) NOT NULL,
  `customerid` int(10) NOT NULL,
  `stringid` int(10) NOT NULL,
  `stringidc` int(10) NOT NULL,
  `racketid` int(10) DEFAULT NULL,
  `collection_date` varchar(11) NOT NULL,
  `delivery_date` varchar(11) NOT NULL,
  `pre_tension` varchar(6) NOT NULL,
  `tension` varchar(2) NOT NULL,
  `tensionc` varchar(2) NOT NULL,
  `price` varchar(8) NOT NULL,
  `grip_required` varchar(3) DEFAULT NULL,
  `paid` varchar(7) NOT NULL,
  `delivered` varchar(3) NOT NULL,
  `comments` varchar(500) NOT NULL,
  `free_job` varchar(3) NOT NULL,
  `imageid` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stringjobs`
--

INSERT INTO `stringjobs` (`job_id`, `customerid`, `stringid`, `stringidc`, `racketid`, `collection_date`, `delivery_date`, `pre_tension`, `tension`, `tensionc`, `price`, `grip_required`, `paid`, `delivered`, `comments`, `free_job`, `imageid`) VALUES
(1, 42, 4, 0, 0, '06/05/2023', '27/04/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(2, 4, 3, 0, 0, '06/05/2023', '26/04/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(3, 29, 4, 0, 0, '07/05/2023', '08/04/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(4, 38, 3, 0, 0, '10/05/2023', '17/05/2023', '0', '27', '0', '17', '0', '1', '1', '', '0', 0),
(5, 38, 3, 0, 0, '10/05/2023', '17/05/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(6, 10, 2, 0, 0, '11/05/2023', '09/05/2023', '0', '26', '0', '18', '0', '1', '1', '', '0', 0),
(7, 10, 3, 0, 0, '11/05/2023', '09/05/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(8, 10, 3, 0, 0, '11/05/2023', '09/05/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(9, 1, 2, 0, 32, '19/05/2023', '19/05/2023', '0', '27', '0', '0', '0', '1', '1', '', '1', 0),
(10, 1, 2, 0, 32, '27/05/2023', '27/05/2023', '0', '27', '0', '0', '0', '1', '1', '', '1', 0),
(11, 22, 4, 0, 0, '30/05/2023', '01/06/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(12, 48, 4, 0, 0, '30/05/2023', '01/06/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(13, 17, 4, 0, 0, '01/06/2023', '08/06/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(14, 17, 4, 0, 0, '01/06/2023', '08/06/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(15, 6, 7, 0, 0, '04/06/2023', '11/06/2023', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(16, 1, 2, 0, 32, '08/06/2023', '08/06/2023', '0', '27', '0', '0', '0', '1', '1', '', '1', 0),
(17, 5, 7, 0, 0, '08/06/2023', '15/06/2023', '0', '25', '0', '12', '0', '1', '1', '', '0', 0),
(18, 4, 3, 0, 0, '13/06/2023', '20/06/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(19, 21, 4, 0, 0, '22/06/2023', '29/06/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(20, 29, 4, 0, 0, '22/06/2023', '29/06/2023', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(21, 29, 4, 0, 0, '22/06/2023', '29/06/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(22, 21, 7, 0, 0, '22/06/2023', '29/06/2023', '0', '56', '0', '12', '0', '1', '1', '', '0', 0),
(23, 21, 7, 0, 0, '22/06/2023', '29/06/2023', '0', '56', '0', '12', '0', '1', '1', '', '0', 0),
(24, 1, 2, 0, 32, '23/06/2023', '26/06/2023', '0', '27', '0', '0', '0', '1', '1', '', '1', 0),
(25, 32, 4, 0, 0, '23/06/2023', '30/06/2023', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(26, 42, 3, 0, 0, '24/06/2023', '29/06/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(27, 41, 1, 0, 0, '26/06/2023', '26/06/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(28, 7, 3, 0, 0, '29/06/2023', '29/06/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(29, 45, 7, 0, 0, '29/06/2023', '29/06/2023', '0', '56', '0', '12', '0', '1', '1', '', '0', 0),
(30, 21, 7, 0, 0, '06/07/2023', '07/07/2023', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(31, 21, 7, 0, 0, '06/07/2023', '07/07/2023', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(32, 21, 7, 0, 0, '06/07/2023', '07/07/2023', '0', '26', '0', '12', '0', '1', '1', '', '0', 0),
(33, 41, 1, 0, 0, '10/07/2023', '12/07/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(34, 39, 3, 0, 0, '10/07/2023', '12/07/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(35, 29, 4, 0, 0, '20/07/2023', '21/07/2023', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(36, 25, 3, 0, 0, '20/07/2023', '27/07/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(37, 29, 3, 0, 0, '20/07/2023', '21/07/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(38, 27, 4, 0, 0, '20/07/2023', '27/07/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(39, 29, 1, 0, 0, '20/07/2023', '21/07/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(40, 24, 4, 0, 0, '20/07/2023', '27/07/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(41, 24, 4, 0, 0, '20/07/2023', '27/07/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(42, 11, 3, 0, 0, '24/07/2023', '28/07/2023', '0', '27', '0', '17', '0', '1', '1', '', '0', 0),
(43, 21, 7, 0, 0, '25/07/2023', '26/07/2023', '0', '53', '0', '12', '0', '1', '1', '', '0', 0),
(44, 21, 7, 0, 0, '25/07/2023', '26/07/2023', '0', '53', '0', '12', '0', '1', '1', '', '0', 0),
(45, 4, 3, 0, 0, '27/07/2023', '01/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(46, 33, 3, 0, 0, '01/08/2023', '02/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(47, 33, 3, 0, 0, '01/08/2023', '02/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(48, 38, 1, 0, 0, '02/08/2023', '09/08/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(49, 1, 2, 0, 32, '08/08/2023', '09/08/2023', '0', '27', '0', '0', '0', '1', '1', '', '1', 0),
(50, 43, 3, 0, 0, '08/08/2023', '09/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(51, 8, 3, 0, 0, '21/08/2023', '24/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(52, 2, 7, 0, 0, '21/08/2023', '24/08/2023', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(53, 23, 1, 0, 0, '21/08/2023', '24/08/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(54, 30, 7, 0, 0, '22/08/2023', '23/08/2023', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(55, 12, 7, 0, 0, '22/08/2023', '25/08/2023', '0', '52', '0', '12', '0', '1', '1', '', '0', 0),
(56, 46, 2, 0, 0, '22/08/2023', '23/08/2023', '0', '29', '0', '18', '0', '1', '1', '', '0', 0),
(57, 46, 2, 0, 0, '22/08/2023', '23/08/2023', '0', '29', '0', '18', '0', '1', '1', '', '0', 0),
(58, 46, 2, 0, 0, '22/08/2023', '23/08/2023', '0', '29', '0', '18', '0', '1', '1', '', '0', 0),
(59, 46, 2, 0, 0, '30/08/2023', '31/08/2023', '0', '29', '0', '18', '0', '1', '1', '', '0', 0),
(60, 11, 3, 0, 0, '01/09/2023', '04/09/2023', '0', '27', '0', '17', '0', '1', '1', '', '0', 0),
(61, 38, 1, 0, 0, '01/09/2023', '04/09/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(62, 6, 6, 0, 0, '03/09/2023', '05/09/2023', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(63, 6, 6, 0, 0, '03/09/2023', '05/09/2023', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(64, 3, 1, 0, 0, '03/09/2023', '05/09/2023', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(65, 23, 1, 0, 0, '03/09/2023', '05/09/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(66, 23, 1, 0, 0, '03/09/2023', '05/09/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(67, 40, 4, 0, 0, '15/09/2023', '15/09/2023', '0', '22', '0', '15', '0', '1', '1', '', '0', 0),
(68, 40, 4, 0, 0, '15/09/2023', '15/09/2023', '0', '22', '0', '15', '0', '1', '1', '', '0', 0),
(69, 14, 4, 0, 0, '21/09/2023', '28/09/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(70, 31, 4, 0, 0, '21/09/2023', '28/09/2023', '0', '26', '0', '15', '0', '1', '1', '', '0', 0),
(71, 8, 1, 0, 0, '26/09/2023', '28/09/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(72, 4, 3, 0, 0, '27/09/2023', '04/10/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(73, 9, 3, 0, 0, '27/09/2023', '04/10/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(74, 1, 2, 0, 32, '29/09/2023', '29/09/2023', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(75, 1, 2, 0, 32, '29/09/2023', '29/09/2023', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(76, 1, 2, 0, 32, '29/09/2023', '29/09/2023', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(77, 16, 7, 0, 0, '02/10/2023', '03/10/2023', '0', '51', '0', '12', '0', '1', '1', '', '0', 0),
(78, 13, 1, 0, 0, '17/10/2023', '21/10/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(79, 6, 6, 0, 0, '17/10/2023', '21/10/2023', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(80, 2, 7, 0, 0, '17/10/2023', '21/10/2023', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(81, 42, 3, 0, 0, '26/10/2023', '02/11/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(82, 1, 2, 0, 32, '26/10/2023', '30/09/2023', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(83, 4, 3, 0, 0, '31/10/2023', '02/11/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(84, 45, 7, 0, 0, '03/11/2023', '07/11/2023', '0', '52', '0', '12', '0', '1', '1', '', '0', 0),
(85, 45, 7, 0, 0, '03/11/2023', '07/11/2023', '0', '52', '0', '12', '0', '1', '1', '', '0', 0),
(86, 37, 4, 0, 0, '05/11/2023', '07/11/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(87, 37, 4, 0, 0, '05/11/2023', '07/11/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(88, 3, 1, 0, 0, '05/11/2023', '07/11/2023', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(89, 41, 1, 0, 0, '09/11/2023', '16/11/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(90, 29, 3, 0, 0, '09/11/2023', '16/11/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(91, 29, 3, 0, 0, '09/11/2023', '16/11/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(92, 12, 7, 0, 0, '09/11/2023', '12/11/2023', '0', '24', '0', '12', '0', '1', '1', '', '0', 0),
(93, 24, 4, 0, 0, '09/11/2023', '16/11/2023', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(94, 6, 6, 0, 0, '10/11/2023', '11/11/2023', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(95, 44, 3, 0, 0, '13/11/2023', '16/11/2023', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(96, 2, 5, 0, 0, '14/11/2023', '17/11/2023', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(97, 3, 1, 0, 0, '14/11/2023', '17/11/2023', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(98, 3, 1, 0, 0, '14/11/2023', '17/11/2023', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(99, 21, 7, 0, 0, '17/11/2023', '18/11/2023', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(100, 21, 7, 0, 0, '17/11/2023', '18/11/2023', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(101, 39, 3, 0, 0, '20/11/2023', '27/11/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(102, 2, 5, 0, 0, '26/11/2023', '27/11/2023', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(103, 41, 1, 0, 0, '27/11/2023', '29/11/2023', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(104, 30, 7, 0, 0, '27/11/2023', '27/11/2023', '0', '25', '0', '12', '0', '1', '1', '', '0', 0),
(105, 29, 4, 0, 0, '01/12/2023', '07/12/2023', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(106, 24, 4, 0, 0, '07/12/2023', '08/12/2023', '0', '25', '0', '15', '0', '1', '1', '', '1', 0),
(107, 38, 1, 0, 0, '11/12/2023', '18/12/2023', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(108, 29, 4, 0, 0, '15/12/2023', '17/12/2023', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(109, 21, 7, 0, 0, '15/12/2023', '22/11/2023', '0', '53', '0', '12', '0', '1', '1', '', '0', 0),
(110, 21, 7, 0, 0, '15/12/2023', '22/11/2023', '0', '53', '0', '12', '0', '1', '1', '', '0', 0),
(111, 1, 4, 0, 32, '15/12/2023', '16/12/2023', '0', '22', '0', '0', '0', '1', '1', 'Junior racket', '1', 0),
(112, 29, 4, 0, 0, '04/01/2024', '11/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(113, 34, 4, 0, 0, '04/01/2024', '11/01/2024', '0', '23', '0', '15', '0', '1', '1', '', '0', 0),
(114, 32, 4, 0, 0, '05/01/2024', '12/01/2024', '0', '23', '0', '15', '0', '1', '1', '', '0', 0),
(115, 44, 3, 0, 0, '05/01/2024', '08/01/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(116, 1, 2, 0, 32, '10/01/2024', '11/01/2024', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(117, 44, 3, 0, 0, '12/01/2024', '18/01/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(118, 42, 3, 0, 0, '14/01/2024', '18/01/2024', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(119, 42, 3, 0, 0, '14/01/2024', '18/01/2024', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(120, 18, 7, 0, 0, '16/01/2024', '18/01/2024', '0', '26', '0', '12', '0', '1', '1', '', '0', 0),
(121, 6, 6, 0, 0, '16/01/2024', '20/01/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(122, 2, 5, 0, 0, '16/01/2024', '18/01/2024', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(123, 29, 4, 0, 0, '18/01/2024', '25/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(124, 29, 4, 0, 0, '18/01/2024', '25/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(125, 29, 4, 0, 0, '18/01/2024', '25/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(126, 35, 4, 0, 0, '18/01/2024', '25/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(127, 15, 1, 0, 0, '23/01/2024', '26/01/2023', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(128, 6, 6, 0, 0, '23/01/2024', '25/01/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(129, 6, 6, 0, 0, '23/01/2024', '25/01/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(130, 41, 3, 0, 0, '24/01/2024', '26/01/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(131, 13, 1, 0, 0, '30/01/2024', '31/01/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(132, 26, 4, 0, 0, '30/01/2024', '31/01/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(133, 6, 6, 0, 0, '30/01/2024', '31/01/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(134, 23, 1, 0, 0, '30/01/2024', '31/01/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(135, 45, 7, 0, 0, '02/02/2024', '07/02/2024', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(136, 44, 3, 0, 0, '02/02/2024', '07/02/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(137, 44, 3, 0, 0, '02/02/2024', '07/02/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(138, 19, 4, 0, 0, '07/02/2024', '08/02/2024', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(139, 4, 3, 0, 0, '07/02/2024', '08/02/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(140, 1, 2, 0, 32, '14/02/2024', '15/02/2024', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(141, 47, 3, 0, 0, '15/02/2024', '22/02/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(142, 29, 3, 0, 0, '15/02/2024', '22/02/2024', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(143, 21, 7, 0, 0, '21/02/2024', '21/02/2024', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(144, 21, 7, 0, 0, '21/02/2024', '21/02/2024', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(145, 38, 1, 0, 0, '26/02/2024', '04/03/2024', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(146, 28, 1, 0, 0, '27/02/2024', '28/02/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(147, 13, 1, 0, 0, '03/03/2024', '09/03/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(148, 6, 6, 0, 0, '03/03/2024', '09/03/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(149, 2, 5, 0, 0, '03/03/2024', '09/03/2024', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(150, 3, 1, 0, 0, '03/03/2024', '09/03/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(151, 3, 1, 0, 0, '03/03/2024', '09/03/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(152, 3, 1, 0, 0, '03/03/2024', '09/03/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(153, 22, 4, 0, 0, '03/03/2024', '09/03/2024', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(154, 1, 2, 0, 32, '09/03/2024', '09/03/2024', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(155, 44, 3, 0, 0, '11/03/2024', '14/03/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(156, 17, 4, 0, 0, '14/03/2024', '21/03/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(157, 24, 4, 0, 0, '14/03/2024', '21/03/2024', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(158, 29, 4, 0, 0, '18/03/2024', '21/03/2024', '0', '22', '0', '15', '0', '1', '1', '', '0', 0),
(159, 29, 3, 0, 0, '18/03/2024', '21/03/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(160, 12, 7, 0, 0, '21/03/2024', '24/11/2023', '0', '24', '0', '12', '0', '1', '1', '', '0', 0),
(161, 21, 7, 0, 0, '23/03/2024', '28/03/2024', '0', '55', '0', '12', '0', '1', '1', '', '0', 0),
(162, 21, 7, 0, 0, '23/03/2024', '28/03/2024', '0', '55', '0', '12', '0', '1', '1', '', '0', 0),
(163, 21, 7, 0, 0, '23/03/2024', '28/03/2024', '0', '55', '0', '12', '0', '1', '1', '', '0', 0),
(164, 21, 3, 0, 0, '28/03/2024', '06/04/2024', '0', '26', '0', '17', '0', '1', '1', '', '0', 0),
(165, 6, 6, 0, 0, '28/03/2024', '28/03/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(166, 6, 6, 0, 1, '28/03/2024', '28/03/2024', '0', '28', '0', '12', '0', '1', '1', '', '0', 0),
(167, 41, 3, 0, 0, '03/04/2024', '05/04/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(168, 5, 4, 0, 0, '05/04/2024', '06/04/2024', '0', '28', '0', '15', '0', '1', '1', '', '0', 0),
(169, 36, 4, 0, 0, '08/04/2024', '15/04/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(170, 4, 3, 0, 0, '09/04/2024', '10/04/2024', '0', '28', '0', '17', '0', '1', '1', '', '0', 0),
(171, 20, 4, 0, 0, '11/04/2024', '18/04/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(172, 29, 3, 0, 0, '11/04/2024', '18/04/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(173, 24, 4, 0, 0, '11/04/2024', '18/04/2024', '0', '25', '0', '15', '0', '1', '1', '', '0', 0),
(174, 20, 4, 0, 0, '28/04/2024', '18/04/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(175, 6, 6, 0, 1, '28/04/2024', '21/04/2024', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(176, 6, 6, 0, 1, '28/04/2024', '21/04/2024', '0', '69', '0', '12', '0', '1', '1', '', '0', 0),
(177, 6, 6, 0, 1, '28/04/2024', '21/04/2024', '0', '29', '0', '12', '0', '1', '1', '', '0', 0),
(178, 2, 5, 0, 4, '28/04/2024', '21/04/2024', '0', '27', '0', '12', '0', '1', '1', '', '0', 0),
(179, 13, 1, 0, 13, '28/04/2024', '21/04/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(180, 3, 1, 0, 5, '28/04/2024', '21/04/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(181, 3, 1, 0, 5, '28/04/2024', '21/04/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(182, 3, 1, 0, 5, '28/04/2024', '21/04/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(183, 28, 1, 0, 10, '28/04/2024', '18/04/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(184, 28, 1, 0, 31, '28/04/2024', '18/04/2024', '0', '24', '0', '17', '0', '1', '1', '', '0', 0),
(185, 3, 1, 0, 5, '28/04/2024', '26/04/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(186, 3, 1, 0, 5, '28/04/2024', '26/04/2024', '0', '30', '0', '17', '0', '1', '1', '', '0', 0),
(187, 32, 4, 0, 10, '28/04/2024', '03/05/2024', '0', '22', '0', '15', '0', '1', '1', '', '0', 0),
(188, 21, 7, 0, 10, '28/04/2024', '29/04/2024', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(189, 21, 7, 0, 10, '28/04/2024', '29/04/2024', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(190, 29, 4, 0, 30, '03/05/2024', '09/05/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 0),
(191, 8, 1, 0, 10, '09/05/2024', '16/05/2024', '0', '25', '60', '17', '0', '1', '1', '', '0', 0),
(192, 1, 2, 0, 32, '16/05/2024', '16/05/2024', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(193, 45, 7, 0, 33, '30/05/2024', '03/06/2024', '0', '54', '0', '12', '0', '1', '1', '', '0', 0),
(194, 49, 7, 0, 34, '07/06/2024', '10/06/2024', '0', '55', '0', '12', '0', '1', '1', '', '0', 0),
(195, 8, 3, 0, 0, '12/06/2024', '15/06/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 0),
(196, 7, 4, 0, 0, '12/06/2024', '15/06/2024', '0', '25', '0', '15', '0', '1', '1', '15 Grommets changed', '0', 17),
(197, 21, 10, 9, 36, '12/06/2024', '14/06/2024', '0', '56', '53', '12', '0', '0', '1', '', '0', 14),
(198, 21, 10, 9, 35, '12/06/2024', '14/06/2024', '0', '56', '53', '12', '0', '0', '1', '', '0', 15),
(199, 21, 8, 0, 35, '12/06/2024', '14/06/2024', '0', '53', '53', '12', '0', '0', '1', 'Grommet bank broken. Added new grommet to cross hole 16. ', '0', 0),
(200, 29, 4, 0, 37, '14/06/2024', '20/06/2024', '0', '25', '70', '15', '0', '1', '1', '', '0', 10),
(201, 1, 3, 0, 32, '15/06/2024', '17/06/2024', '0', '25', '0', '0', '0', '1', '1', '', '1', 0),
(202, 29, 3, 0, 0, '20/06/2024', '27/06/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 20),
(203, 29, 4, 0, 15, '20/06/2024', '27/06/2024', '0', '24', '0', '15', '0', '1', '1', '', '0', 19),
(204, 49, 11, 0, 38, '25/06/2024', '27/06/2024', '0', '55', '0', '12', '0', '1', '1', '', '0', 21),
(205, 29, 3, 0, 18, '27/06/2024', '28/06/2024', '0', '25', '0', '17', '0', '1', '1', '', '0', 22),
(206, 50, 4, 0, 39, '27/06/2024', '29/06/2024', '0', '26', '35', '12', '0', '1', '0', '', '0', 24),
(207, 50, 4, 0, 39, '27/06/2024', '28/06/2024', '0', '26', '0', '12', '0', '1', '0', '', '0', 23),
(208, 4, 3, 0, 41, '16/07/2024', '23/07/2024', '0', '28', '70', '17', '0', '1', '0', '', '0', 0),
(209, 6, 6, 0, 1, '18/07/2024', '20/07/2024', '0', '29', '0', '12', '0', '0', '0', '', '0', 0),
(210, 2, 5, 0, 5, '18/07/2024', '20/07/2024', '0', '28', '0', '12', '0', '0', '0', '', '0', 0),
(211, 52, 7, 0, 40, '15/07/2024', '16/07/2024', '0', '55', '55', '12', '0', '1', '1', '', '0', 25),
(212, 52, 7, 0, 40, '15/07/2024', '16/07/2024', '0', '55', '55', '12', '0', '1', '1', '', '0', 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `all_string`
--
ALTER TABLE `all_string`
  ADD PRIMARY KEY (`string_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_ID`);

--
-- Indexes for table `grip`
--
ALTER TABLE `grip`
  ADD PRIMARY KEY (`gripid`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rackets`
--
ALTER TABLE `rackets`
  ADD PRIMARY KEY (`racketid`);

--
-- Indexes for table `sport`
--
ALTER TABLE `sport`
  ADD PRIMARY KEY (`sportid`);

--
-- Indexes for table `string`
--
ALTER TABLE `string`
  ADD PRIMARY KEY (`stringid`),
  ADD UNIQUE KEY `stringid` (`stringid`);

--
-- Indexes for table `stringjobs`
--
ALTER TABLE `stringjobs`
  ADD PRIMARY KEY (`job_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `all_string`
--
ALTER TABLE `all_string`
  MODIFY `string_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `grip`
--
ALTER TABLE `grip`
  MODIFY `gripid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `rackets`
--
ALTER TABLE `rackets`
  MODIFY `racketid` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `sport`
--
ALTER TABLE `sport`
  MODIFY `sportid` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `string`
--
ALTER TABLE `string`
  MODIFY `stringid` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stringjobs`
--
ALTER TABLE `stringjobs`
  MODIFY `job_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
