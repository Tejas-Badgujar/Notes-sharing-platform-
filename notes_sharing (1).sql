-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 03:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `notes_sharing`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `field` varchar(60) NOT NULL DEFAULT 'General',
  `description` text DEFAULT NULL,
  `semester` tinyint(4) NOT NULL DEFAULT 1,
  `downloads` int(11) NOT NULL DEFAULT 0,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `tags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `subject`, `file_path`, `status`, `created_at`, `field`, `description`, `semester`, `downloads`, `avg_rating`, `tags`) VALUES
(1, 2, 'Data Structures Unit 1', 'Data Structures', 'uploads/sample1.pdf', 'pending', '2026-04-26 19:35:47', 'General', NULL, 1, 0, 0.00, NULL),
(2, 3, 'Algorithm Design Ch 2', 'Algorithms', 'uploads/sample2.pdf', 'approved', '2026-04-26 19:35:47', 'General', NULL, 1, 0, 0.00, NULL),
(3, 4, 'Microprocessors Notes', 'EC', 'uploads/sample3.pdf', 'pending', '2026-04-26 19:35:47', 'General', NULL, 1, 0, 0.00, NULL),
(4, 6, 'AOAA', 'Analysis of Algorithm 2', 'uploads/1777291694_6_AOA_p6.pdf', 'pending', '2026-04-27 12:08:14', 'Information Technology', 'NAAA', 4, 0, 0.00, 'data analyst'),
(5, 6, 'wwafs', 'Analysis of Algorithm', 'uploads/1777291737_6_AOA_p7.pdf', 'pending', '2026-04-27 12:08:57', 'Computer Science', 'nvnvnvn', 1, 0, 0.00, 'data analyst  4');

-- --------------------------------------------------------

--
-- Table structure for table `note_ratings`
--

CREATE TABLE `note_ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `rated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `note_ratings`
--
DELIMITER $$
CREATE TRIGGER `trg_rating_after_delete` AFTER DELETE ON `note_ratings` FOR EACH ROW UPDATE notes SET avg_rating = (SELECT COALESCE(AVG(rating),0) FROM note_ratings WHERE note_id = OLD.note_id)
  WHERE id = OLD.note_id
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_rating_after_insert` AFTER INSERT ON `note_ratings` FOR EACH ROW UPDATE notes SET avg_rating = (SELECT AVG(rating) FROM note_ratings WHERE note_id = NEW.note_id)
  WHERE id = NEW.note_id
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_rating_after_update` AFTER UPDATE ON `note_ratings` FOR EACH ROW UPDATE notes SET avg_rating = (SELECT AVG(rating) FROM note_ratings WHERE note_id = NEW.note_id)
  WHERE id = NEW.note_id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `saved_notes`
--

CREATE TABLE `saved_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `note_id` int(10) UNSIGNED NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) DEFAULT NULL,
  `user_type` enum('average','premium') NOT NULL DEFAULT 'average',
  `whatsapp_ok` tinyint(1) NOT NULL DEFAULT 0,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `theme` varchar(30) NOT NULL DEFAULT '',
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `max_streak` int(11) NOT NULL DEFAULT 0,
  `last_upload_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `branch`, `role`, `last_activity`, `created_at`, `username`, `user_type`, `whatsapp_ok`, `first_name`, `last_name`, `theme`, `current_streak`, `max_streak`, `last_upload_date`) VALUES
(1, 'System', 'Admin', 'admin@notes.com', '$2y$10$0uD6Qrh0cOJOseh8z79IR.CNjyNj3niX7ultDgsu1svqCFPygLlma', NULL, 'admin', '2026-04-26 19:35:46', '2026-04-26 19:35:46', NULL, 'average', 0, NULL, NULL, '', 0, 0, NULL),
(2, 'John', 'Doe', 'john@gmail.com', '$2y$10$OVhvj8uCoMhjKlUwyWkKVeNpVK3kkbXo/ZYNhLzQ1pCqbGDda8Gwm', 'CS', 'user', '2026-04-26 19:35:47', '2026-04-26 19:35:47', NULL, 'average', 0, NULL, NULL, '', 0, 0, NULL),
(3, 'Jane', 'Smith', 'jane@yahoo.com', '$2y$10$V/c1fvvvpqNW70hzD5xg8uLysDsJTPVDG8NaN0VJrrNg4yal1mTtq', 'IT', 'user', '2026-04-26 19:35:47', '2026-04-26 19:35:47', NULL, 'average', 0, NULL, NULL, '', 0, 0, NULL),
(4, 'Alice', 'Johnson', 'alice@gmail.com', '$2y$10$Z.DVEl2o5GNoN2kdFEPB.esDvRLFUt90ZtTnDRKf4jVnNUo5L/nkm', 'EC', 'user', '2026-04-26 19:35:47', '2026-04-26 19:35:47', NULL, 'average', 0, NULL, NULL, '', 0, 0, NULL),
(5, NULL, NULL, 'giantcorsair999@gmail.com', '$2y$10$e/aTFHz0xHVG1.eEkd3P1e8.5pLaI9SBsQlnLMjgS2X/Xgw5h7zuK', 'Information Technology', 'user', '2026-04-27 13:58:03', '2026-04-27 11:56:19', 'Rmy4143', 'average', 0, 'Bhagyesh', 'Patil', 'theme-demon', 0, 0, NULL),
(6, NULL, NULL, 'alitaazib1@gmail.com', '$2y$10$dkce5wUMoZkONUOixU2GUubGuHB0WzfcZn/tg5NtqpzgmFH4dJ8v.', 'Other', 'user', '2026-04-28 11:55:33', '2026-04-27 12:02:26', 'Rmy414', 'average', 0, 'Bhagyesh', 'Patil', 'theme-demon', 1, 1, '2026-04-27'),
(7, NULL, NULL, 'beingbokhya123@gmail.com', '$2y$10$gMMBSUZI.Bb/sk8gZAdp2.esbnp2Os5CS3SoMlHFf/v.2eQQZtgjW', 'MPSC', 'user', '2026-04-27 13:59:16', '2026-04-27 13:58:16', 'Rmy4', 'average', 0, 'Bhagyesh', 'Patil', '', 0, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note_ratings`
--
ALTER TABLE `note_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rating` (`note_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saved_notes`
--
ALTER TABLE `saved_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_saved` (`user_id`,`note_id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `note_ratings`
--
ALTER TABLE `note_ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_notes`
--
ALTER TABLE `saved_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_ratings`
--
ALTER TABLE `note_ratings`
  ADD CONSTRAINT `note_ratings_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_notes`
--
ALTER TABLE `saved_notes`
  ADD CONSTRAINT `saved_notes_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
