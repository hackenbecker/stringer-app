-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2024 at 05:51 PM
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
  `racketid` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_ID`, `Name`, `Notes`, `Email`, `Mobile`, `pref_string`, `pref_stringc`, `tension`, `tensionc`, `prestretch`, `racketid`) VALUES
(1, 'Chris Leah', '', '', '', 2, 0, '25', '0', '0', 1),
(2, 'Jimmy', '', '', '', 5, 0, '27', '0', '0', 2),
(3, 'Ashley', '', '', '', 1, 0, '30', '0', '0', 3),
(4, 'Barry', '', '', '', 3, 0, '28', '0', '0', 0),
(5, 'Ben', '', '', '', 1, 0, '28', '0', '0', 0),
(6, 'Bradley', '', '', '', 6, 0, '29', '0', '0', 1),
(7, 'Chrissy', '', '', '', 0, 0, '24', '0', '0', 0),
(8, 'Clive', 'Next restring £2 off', '', '', 0, 0, '25', '0', '0', 0),
(9, 'Craig', '', '', '', 0, 0, '24', '0', '0', 0),
(10, 'Damien', '', '', '', 0, 0, '26', '0', '0', 0),
(11, 'Damon', '', '', '', 0, 0, '27', '0', '0', 0),
(12, 'Dan', '', '', '', 0, 0, '24', '0', '0', 0),
(13, 'Darren', '', '', '', 0, 0, '25', '0', '0', 0),
(14, 'Dave', '', '', '', 0, 0, '26', '0', '0', 0),
(15, 'Nick', '', '', '', 0, 0, '25', '0', '0', 0),
(16, 'Fay', '', '', '', 0, 0, '51', '0', '0', 0),
(17, 'Gary', '', '', '', 0, 0, '25', '0', '0', 0),
(18, 'Gerald', '', '', '', 0, 0, '26', '0', '0', 0),
(19, 'Hannah', '', '', '', 0, 0, '25', '0', '0', 0),
(20, 'Harry', '', '', '', 0, 0, '24', '0', '0', 0),
(21, 'Jim', '', '', '', 0, 0, '55', '0', '0', 0),
(22, 'Jeanette', '', '', '', 0, 0, '25', '0', '0', 0),
(23, 'John', '', '', '', 0, 0, '25', '0', '0', 0),
(24, 'Jon', '', '', '', 0, 0, '25', '0', '0', 0),
(25, 'Josh', '', '', '', 0, 0, '24', '0', '0', 0),
(26, 'Kevin', '', '', '', 0, 0, '24', '0', '0', 0),
(27, 'Lara', '', '', '', 0, 0, '25', '0', '0', 0),
(28, 'Milley', '', '', '', 1, 0, '24', '0', '0', 0),
(29, 'Martin', '', '', '', 0, 0, '26', '0', '0', 0),
(30, 'Michael', '', '', '', 0, 0, '25', '0', '0', 0),
(31, 'Neil', '', '', '', 0, 0, '26', '0', '0', 0),
(32, 'Olly', '', '', '', 0, 0, '24', '0', '0', 0),
(33, 'Richard', '', '', '', 0, 0, '25', '0', '0', 0),
(34, 'Rob', '', '', '', 0, 0, '23', '0', '0', 0),
(35, 'Robin', '', '', '', 0, 0, '24', '0', '0', 0),
(36, 'Rohit', '', '', '', 0, 0, '24', '0', '0', 0),
(37, 'Sarah', '', '', '', 0, 0, '25', '0', '0', 0),
(38, 'Sarwar', '', '', '', 0, 0, '26', '0', '0', 0),
(39, 'Simon ', '', '', '', 0, 0, '24', '0', '0', 0),
(40, 'Stephen', '', '', '', 0, 0, '22', '0', '0', 0),
(41, 'Steve ', '', '', '', 0, 0, '24', '0', '0', 0),
(42, 'Stuart ', '', '', '', 0, 0, '26', '0', '0', 0),
(43, 'Peter', '', '', '', 0, 0, '25', '0', '0', 0),
(44, 'Tom', '', '', '', 0, 0, '28', '0', '0', 0),
(45, 'Tony', '', '', '', 0, 0, '27', '0', '0', 0),
(46, 'Vinay', '', '', '', 0, 0, '29', '0', '0', 0),
(47, 'Matt', '', '', '', 0, 0, '25', '0', '0', 0),
(48, 'Dan', '', '', '', 0, 0, '26', '0', '0', 0),
(49, 'Hugh', '', '', '', 0, 0, '55', '', '0', 34);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
