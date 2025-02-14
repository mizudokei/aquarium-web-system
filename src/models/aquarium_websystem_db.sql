-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-01-31 06:05:41
-- サーバのバージョン： 10.4.25-MariaDB
-- PHP のバージョン: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `aquarium_websystem_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admins`
--

CREATE TABLE `admins` (
	`id` int(11) NOT NULL,
	`email` varchar(100) NOT NULL,
	`password` varchar(255) NOT NULL,
	`role` enum('superadmin','manager') DEFAULT 'manager',
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin@example.com', '$2y$10$1sVhWZrXqLSiLpchnE1G2OBoaf/oMiSR3XYMsesNYlpDKS4xWKQYe', 'superadmin', '2025-01-15 11:46:43');

-- --------------------------------------------------------

--
-- テーブルの構造 `admission_fee_types`
--

CREATE TABLE `admission_fee_types` (
	`id` int(11) NOT NULL,
	`type` enum('大人','高校生','小・中学生') NOT NULL,
	`price` int(11) NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `admission_fee_types`
--

INSERT INTO `admission_fee_types` (`id`, `type`, `price`, `created_at`) VALUES
(1, '大人', 2700, '2025-01-28 08:33:24'),
(2, '高校生', 1400, '2025-01-28 08:33:24'),
(3, '小・中学生', 700, '2025-01-28 08:33:24');

-- --------------------------------------------------------

--
-- テーブルの構造 `entry_logs`
--

CREATE TABLE `entry_logs` (
	`id` int(11) NOT NULL,
	`reservation_id` varchar(16) NOT NULL,
	`user_id` int(11) NOT NULL,
	`entry_time` timestamp NOT NULL DEFAULT current_timestamp(),
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_types`
--

CREATE TABLE `event_types` (
	`id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`price` int(11) NOT NULL,
	`capacity` int(11) NOT NULL,
	`start_time` time NOT NULL,
	`end_time` time NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `reservations`
--

CREATE TABLE `reservations` (
	`id` varchar(16) NOT NULL,
	`user_id` int(11) NOT NULL,
	`reservation_date` date NOT NULL,
	`reservation_timeslot` varchar(20) NOT NULL,
	`total_price` int(11) NOT NULL,
	`status` enum('reserved','cancelled') NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `reservation_date`, `reservation_timeslot`, `total_price`, `status`, `created_at`) VALUES
('f3e73c0410661e0f', 1, '2025-02-01', '13:30～14:00', 4800, 'reserved', '2025-01-28 15:52:04');

-- --------------------------------------------------------

--
-- テーブルの構造 `reservation_tickets`
--

CREATE TABLE `reservation_tickets` (
	`id` varchar(16) NOT NULL,
	`reservation_id` varchar(16) NOT NULL,
	`ticket_type` enum('admission','event') NOT NULL,
	`admission_fee_type_id` int(11) NOT NULL,
	`event_id` int(11) DEFAULT NULL,
	`seat_number` varchar(10) DEFAULT NULL,
	`recipient_lastname` varchar(255) NOT NULL,
	`recipient_firstname` varchar(255) NOT NULL,
	`recipient_email` varchar(255) DEFAULT NULL,
	`qr_code` varchar(255) NOT NULL,
	`used_at` timestamp NULL DEFAULT NULL,
	`share_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `reservation_tickets`
--

INSERT INTO `reservation_tickets` (`id`, `reservation_id`, `ticket_type`, `admission_fee_type_id`, `event_id`, `seat_number`, `recipient_lastname`, `recipient_firstname`, `recipient_email`, `qr_code`, `used_at`, `share_token`) VALUES
('2704cf029caba60f', 'f3e73c0410661e0f', '', 3, NULL, NULL, '', '', NULL, '/../../public/assets/qr_codes/qrcode_6798fd24891308.59441614.png', NULL, NULL),
('3b09baea628e1699', 'f3e73c0410661e0f', '', 1, NULL, NULL, '水野', '次郎', 'mizunotoki.halstudent@gmail.com', '/../../public/assets/qr_codes/qrcode_6798fd248523b3.96283465.png', NULL, '56b4e3a63b6cde2ff83678587bdf8860'),
('92cf423ec11322fe', 'f3e73c0410661e0f', '', 2, NULL, NULL, '', '', NULL, '/../../public/assets/qr_codes/qrcode_6798fd24875323.98309227.png', NULL, NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `sales_days`
--

CREATE TABLE `sales_days` (
	`id` int(11) NOT NULL,
	`date` date NOT NULL,
	`is_operational` tinyint(1) DEFAULT 1,
	`working_hour_id` int(11) NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `sales_days`
--

INSERT INTO `sales_days` (`id`, `date`, `is_operational`, `working_hour_id`, `created_at`) VALUES
(1, '2025-01-16', 1, 1, '2025-01-15 19:43:45'),
(2, '2025-01-17', 1, 1, '2025-01-15 19:47:23'),
(3, '2025-01-18', 1, 1, '2025-01-15 19:55:42'),
(6, '2025-01-19', 1, 1, '2025-01-15 20:05:49'),
(7, '2025-01-20', 1, 1, '2025-01-15 20:06:13'),
(10, '2025-01-27', 1, 2, '2025-01-15 21:38:46'),
(11, '2025-02-01', 1, 1, '2025-01-15 21:38:46');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
	`id` int(11) NOT NULL,
	`last_name` varchar(10) NOT NULL,
	`first_name` varchar(10) NOT NULL,
	`birth` date NOT NULL DEFAULT '1970-01-01',
	`email` varchar(100) NOT NULL,
	`password` varchar(255) NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `last_name`, `first_name`, `birth`, `email`, `password`, `created_at`) VALUES
(1, '水野', '斗稀', '2004-03-25', 'mizunotoki.halstudent@gmail.com', '$2y$10$zJCwWvdnvkUDDRw4T74XV.ofJoFvtWxH2IeoAWgA4BECYbRNQ4QoK', '2025-01-16 03:15:25');

-- --------------------------------------------------------

--
-- テーブルの構造 `working_hours`
--

CREATE TABLE `working_hours` (
	`id` int(11) NOT NULL,
	`name` varchar(100) NOT NULL,
	`start_time` time NOT NULL,
	`end_time` time NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `working_hours`
--

INSERT INTO `working_hours` (`id`, `name`, `start_time`, `end_time`, `created_at`) VALUES
(1, 'Standard Hours', '09:00:00', '17:00:00', '2025-01-15 11:46:43'),
(2, 'Sub Hours', '10:00:00', '21:00:00', '2025-01-16 02:19:36');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admins`
--
ALTER TABLE `admins`
	ADD PRIMARY KEY (`id`),
	ADD UNIQUE KEY `email` (`email`);

--
-- テーブルのインデックス `admission_fee_types`
--
ALTER TABLE `admission_fee_types`
	ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `entry_logs`
--
ALTER TABLE `entry_logs`
	ADD PRIMARY KEY (`id`),
	ADD KEY `reservation_id` (`reservation_id`),
	ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `event_types`
--
ALTER TABLE `event_types`
	ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `reservations`
--
ALTER TABLE `reservations`
	ADD PRIMARY KEY (`id`),
	ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
	ADD PRIMARY KEY (`id`),
	ADD KEY `reservation_id` (`reservation_id`),
	ADD KEY `admission_fee_type_id` (`admission_fee_type_id`),
	ADD KEY `event_id` (`event_id`);

--
-- テーブルのインデックス `sales_days`
--
ALTER TABLE `sales_days`
	ADD PRIMARY KEY (`id`),
	ADD UNIQUE KEY `date` (`date`),
	ADD KEY `working_hour_id` (`working_hour_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
	ADD PRIMARY KEY (`id`),
	ADD UNIQUE KEY `email` (`email`);

--
-- テーブルのインデックス `working_hours`
--
ALTER TABLE `working_hours`
	ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `admins`
--
ALTER TABLE `admins`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `admission_fee_types`
--
ALTER TABLE `admission_fee_types`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `entry_logs`
--
ALTER TABLE `entry_logs`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `event_types`
--
ALTER TABLE `event_types`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `sales_days`
--
ALTER TABLE `sales_days`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `working_hours`
--
ALTER TABLE `working_hours`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `entry_logs`
--
ALTER TABLE `entry_logs`
	ADD CONSTRAINT `entry_logs_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
	ADD CONSTRAINT `entry_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `reservations`
--
ALTER TABLE `reservations`
	ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `reservation_tickets`
--
ALTER TABLE `reservation_tickets`
	ADD CONSTRAINT `reservation_tickets_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
	ADD CONSTRAINT `reservation_tickets_ibfk_2` FOREIGN KEY (`admission_fee_type_id`) REFERENCES `admission_fee_types` (`id`) ON DELETE CASCADE,
	ADD CONSTRAINT `reservation_tickets_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `event_types` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `sales_days`
--
ALTER TABLE `sales_days`
	ADD CONSTRAINT `sales_days_ibfk_1` FOREIGN KEY (`working_hour_id`) REFERENCES `working_hours` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
