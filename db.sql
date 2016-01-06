-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2016 at 04:57 PM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--
-- Database: `a5106316_amazing`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `pwd` blob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `pwd`) VALUES
(1, 'amazinguser1', 0x5d5756486dc1b0532dbe5b8250c8ed37),
(2, 'amazinguser2', 0xad28c1a3b4b28ac50db01ab0f0133959),
(3, 'amazinguser3', 0x6bdbc21ebce5ef209910a0fa6b170716),
(4, 'amazinguser4', 0xa28ed0ad0b2df7429cd828fddfb49d4c);

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `reserve_ID` int(10) UNSIGNED NOT NULL,
  `message_ID` int(10) UNSIGNED NOT NULL,
  `file_ID` int(10) UNSIGNED NOT NULL,
  `date_uploaded` datetime NOT NULL,
  `filename` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `reserve_ID` int(10) UNSIGNED NOT NULL,
  `message_ID` int(10) UNSIGNED NOT NULL,
  `date_added` datetime NOT NULL,
  `message` text NOT NULL,
  `sender` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`reserve_ID`, `message_ID`, `date_added`, `message`, `sender`) VALUES
(48, 48, '2012-10-15 17:01:23', '"huhu"', 'josemanalo'),
(48, 49, '2012-10-15 17:11:56', '&#039;huhu&quot;huHuhdsa&#039;dasdas&quot;&quot;dasdas&#039;asdas', 'josemanalo'),
(48, 47, '2012-10-15 17:00:55', '&quot;hahaha&quot;', 'josemanalo'),
(47, 46, '2012-10-15 16:53:47', 'dasdsadasdsa', 'josemanalo');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reserve_ID` int(10) UNSIGNED NOT NULL,
  `user_ID` int(10) UNSIGNED NOT NULL,
  `event` varchar(20) NOT NULL,
  `location` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `date_reserved` date NOT NULL,
  `numGuests` int(10) UNSIGNED NOT NULL,
  `guestPrice` int(10) UNSIGNED NOT NULL,
  `coverage` text NOT NULL,
  `extras` text NOT NULL,
  `cakes` text NOT NULL,
  `treats` text NOT NULL,
  `personnel` text NOT NULL,
  `message` text NOT NULL,
  `totalPrice` int(10) UNSIGNED NOT NULL,
  `paid` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reserve_ID`, `user_ID`, `event`, `location`, `date`, `time`, `date_reserved`, `numGuests`, `guestPrice`, `coverage`, `extras`, `cakes`, `treats`, `personnel`, `message`, `totalPrice`, `paid`) VALUES
(46, 2, 'Anniversary', '678 Hulugan, Cavite', '2012-10-26', '15:00:00', '2012-10-15', 200, 60000, 'Free Video Coverage,', '', '2 Layer Cake,3 Layer Cake,', 'Ice Crumble Cart,Cotton Candy,', 'Mascot,Mascot Character: MickeyMouse,', 'Hey i want mickey mouse', 73000, 0),
(47, 2, 'Wedding', '456 Holiwod', '2012-10-28', '13:01:00', '2012-10-15', 300, 90000, '', '', '', '', '', 'dasdsadasdsa', 90000, 0),
(48, 2, 'Debut', '123 fsdfas', '2012-10-30', '13:01:00', '2012-10-15', 300, 90000, '', '', '', '', '', '&quot;hahaha&quot;', 90000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `pwd` blob NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `contactno` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(30) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `pwd`, `name`, `contactno`, `email`) VALUES
(1, 'juandelacruz', 0xc706dcc9d30daef55dab8edea458df0d, 'Juan Dela Cruz', '09101001010', 'juandelacruz@hotmail.com'),
(2, 'josemanalo', 0x1b582272b62ff1bda384b9f9595ea179, 'Jose Manalo', '437-2234', 'josemanalo@yahoo.com'),
(3, 'pepesmith', 0x718a6c4f625e134b64d1820eb3e6af4f, 'Pepe Smith', '434-1235', 'pepesmith@gmail.com'),
(4, 'joanamejico', 0x34ac38ff0e1b018fea95844f6df7300c, 'joana mejico', '09463922449', 'joanamejico@ymail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`file_ID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_ID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reserve_ID`),
  ADD UNIQUE KEY `date` (`date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `file_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reserve_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
