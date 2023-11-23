-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2023 at 12:48 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secprog`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `attempts` int(11) NOT NULL,
  `last_attempt_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `attempts`, `last_attempt_time`) VALUES
(14, 'test2', 0, '2023-11-23 11:47:32'),
(15, 'test2', 1, '2023-11-23 11:47:38'),
(16, 'test2', 1, '2023-11-23 11:47:40'),
(17, 'test2', 1, '2023-11-23 11:47:43'),
(18, 'test2', 1, '2023-11-23 11:47:44'),
(19, 'test2', 1, '2023-11-23 11:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `owner_id` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `filename`, `comment`, `owner_id`) VALUES
(11, '1700558503_test2.jpg', 'test', 'us5891'),
(12, '1700558674_test2.jpg', 'kiwkiw', 'us5891'),
(14, '1700576590_test2.jpg', 'kentang', 'us5891'),
(16, '1700577345_test2.jpg', 'kiwkiwkiw', 'us5891'),
(17, '1700577377_test2.jpg', 'hihihihehehe', 'us5891'),
(18, '1700636459_test2.jpg', 'mememomo', 'us5891'),
(19, '1700636500_test2.jpg', 'kentang123', 'us5891'),
(21, '1700641580_test2.jpg', 'kentang123123', 'us5891'),
(24, '1700740009_test2.jpg', 'hihihi', 'us5891');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(6) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `phone_num` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `phone_num`, `email`, `role`) VALUES
('us3622', 'test3', 'd8a928b2043db77e340b523547bf16cb4aa483f0645fe0a290ed1f20aab76257', '0808080808', 'dasu@testing.com', 'guest'),
('us5891', 'test2', 'd8a928b2043db77e340b523547bf16cb4aa483f0645fe0a290ed1f20aab76257', '1234567890', 'dasu@testing2.com', 'guest'),
('us7043', 'test4', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '0808080808', 'dasu@testing3.com', 'guest'),
('us9915', 'test1', 'd8a928b2043db77e340b523547bf16cb4aa483f0645fe0a290ed1f20aab76257', '09090909090909', 'john@testing.com', 'guest');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;