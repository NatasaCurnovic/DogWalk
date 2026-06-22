-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 11:05 PM
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
-- Database: `dog_walk`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED DEFAULT NULL,
  `receiver_id` int(10) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED NOT NULL,
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `walker_id` int(10) UNSIGNED DEFAULT NULL,
  `score` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `request_id`, `owner_id`, `walker_id`, `score`, `comment`, `created_at`) VALUES
(1, 2, 3, 4, 5, 'Odlicno iskustvo sve preporuke', '2026-06-03 14:11:19'),
(2, 3, 3, 5, 4, 'Okej je iskustvo, psa je bolela noga posle', '2026-06-03 14:18:18'),
(3, 5, 3, 10, 5, 'Preporuke, pas je bas uzivao, radujem se daljoj saradnji', '2026-06-03 22:46:21'),
(4, 6, NULL, 12, 5, 'Pas je baš uživao i lepo se izmorio, legao je odmah da spava', '2026-06-07 11:16:50'),
(5, 7, NULL, 4, 4, 'Preporuke', '2026-06-07 11:19:48'),
(6, 8, 3, 8, 5, 'Dobro iskustvo', '2026-06-11 13:34:26'),
(7, 10, 3, 5, 5, NULL, '2026-06-11 13:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` enum('owner','walker','admin') NOT NULL DEFAULT 'owner',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `activation_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `address`, `role`, `is_active`, `is_approved`, `is_banned`, `activation_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'DogWalk', 'admin@dogwalk.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', NULL, NULL, 'admin', 1, 1, 0, NULL, NULL, NULL, '2026-04-27 18:30:06', '2026-06-02 11:18:23'),
(3, 'Helena', 'Bergman', 'helenabergman9@gmail.com', '$2y$10$Eh/KGhdXJwdRYlm4kX4ScuhsbdmLW4zX5mBdqw092d5Gv9O3f9QdC', '0644410505', 'Gakovacki put', 'owner', 1, 1, 0, NULL, NULL, NULL, '2026-05-07 17:47:40', '2026-06-03 13:59:03'),
(4, 'Ana', 'Petrovic', 'ana.petrovic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060111111', 'Beograd', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-03 22:36:38'),
(5, 'Marko', 'Jovanovic', 'marko.jovanovic@gmail.com', '$2y$10$zJfL0xTiDJLLitbVQOIcdefH/28ufWbCqC6TmRqfOBkayGfx5PtL6', '060222222', 'Novi Sad', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-11 13:56:37'),
(6, 'Jelena', 'Nikolic', 'jelena.nikolic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060333333', 'Subotica', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-07 11:13:44'),
(7, 'Milos', 'Ilic', 'milos.ilic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060444444', 'Nis', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-07 11:13:50'),
(8, 'Sara', 'Stankovic', 'sara.stankovic@gmail.com', '$2y$10$HWW3ycoI76.QyuGTOHzzzuzWaFi8LLa4Z/5HFyzkvcepuHazWAGO6', '060555555', 'Kragujevac', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-11 13:49:52'),
(9, 'Nikola', 'Bozic', 'nikola.bozic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060666666', 'Beograd', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-07 11:14:06'),
(10, 'Tamara', 'Lukic', 'tamara.lukic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060777777', 'Novi Sad', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-03 22:40:39'),
(11, 'Aleksandar', 'Vasic', 'aleksandar.vasic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '060888888', 'Nis', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-07 11:14:15'),
(12, 'Ivana', 'Marinkovic', 'ivana.marinkovic@gmail.com', '$2y$10$67kYhv6Y5ZpF8vuO0ichdevQ9Ohu7g9nBQBMxmobD6zH67PM/sYXm', '060999999', 'Pancevo', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-11 13:35:59'),
(13, 'Milan', 'Stankovic', 'milan.stankovic@gmail.com', '$2y$10$fiAeW2dy69trWgB0eKGXoOlfsYAhHdZFKV3MkIo7UJj7FIgVklXIi', '061000000', 'Subotica', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-02 11:18:23', '2026-06-07 11:14:28'),
(14, 'Natasa', 'Curnovic', 'natasacurnovic2005@gmail.com', '$2y$10$9PbsQB.7LtUwV2MAjdVms.ye5WamvW/OYyxfWQe24j7s6UvBTkHqG', '064587995', 'Maksima Gorkog 8, Subotica', 'owner', 1, 0, 0, '37c9727a5ebdc993e6d0dc44ce77ea0bacfbdbadda747e2111d95fbd8c165b31', '641e88e66d53959e23a8a81968e9682ed22fd3d052ca46bf1701ac5183526bcb', '2026-06-11 13:50:21', '2026-06-03 23:22:10', '2026-06-11 13:20:21'),
(24, 'Konstantin', 'Pletikosic', 'pletikosick@gmail.com', '$2y$10$ytPv3DOfoUp.UVs9iIQZperF76qlfpRB0ZylZxJyIBKGOwzUTfE7W', '064525789', 'Segedinski put 5, Subotica', 'walker', 1, 1, 0, NULL, NULL, NULL, '2026-06-15 20:45:00', '2026-06-15 20:45:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_most_active_walkers`
-- (See below for the actual view)
--
CREATE TABLE `v_most_active_walkers` (
`id` int(10) unsigned
,`first_name` varchar(100)
,`last_name` varchar(100)
,`photo` varchar(255)
,`description` text
,`city` varchar(100)
,`total_walks` bigint(21)
,`avg_score` decimal(5,1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_top_rated_walkers`
-- (See below for the actual view)
--
CREATE TABLE `v_top_rated_walkers` (
`id` int(10) unsigned
,`first_name` varchar(100)
,`last_name` varchar(100)
,`photo` varchar(255)
,`description` text
,`city` varchar(100)
,`experience_years` int(10) unsigned
,`avg_score` decimal(5,1)
,`total_ratings` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_walker_search`
-- (See below for the actual view)
--
CREATE TABLE `v_walker_search` (
`id` int(10) unsigned
,`first_name` varchar(100)
,`last_name` varchar(100)
,`photo` varchar(255)
,`description` text
,`favorite_breed` varchar(100)
,`experience_years` int(10) unsigned
,`city` varchar(100)
,`price_per_hour` decimal(8,2)
,`is_available` tinyint(1)
,`avg_score` decimal(6,2)
,`total_ratings` bigint(21)
,`total_walks` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `walker_profiles`
--

CREATE TABLE `walker_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `favorite_breed` varchar(100) DEFAULT NULL,
  `experience_years` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `city` varchar(100) DEFAULT NULL,
  `price_per_hour` decimal(8,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `walker_profiles`
--

INSERT INTO `walker_profiles` (`id`, `user_id`, `photo`, `description`, `favorite_breed`, `experience_years`, `city`, `price_per_hour`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 4, 'Images/walker1.jpg', 'Pouzdana setacica sa iskustvom u radu sa mirnim i energicnim psima.', 'Labrador', 5, 'Beograd', 900.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(2, 5, 'Images/walker2.jpg', 'Volim duge setnje i rad sa psima svih velicina.', 'Zlatni retriver', 3, 'Novi Sad', 850.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(3, 6, 'Images/walker3.jpg', 'Strpljiva i pazljiva setacica, dostupna radnim danima.', 'Pudla', 4, 'Subotica', 800.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(4, 7, 'Images/walker4.jpg', 'Iskusan setac za aktivne pse i duze rute.', 'Haski', 6, 'Nis', 1000.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(5, 8, 'Images/walker5.jpg', 'Radim sa psima kojima treba dodatna paznja i miran pristup.', 'Nemacki ovcar', 4, 'Kragujevac', 900.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(6, 9, 'Images/walker6.jpg', 'Dostupan svaki dan za kratke i duge setnje.', 'Labrador', 7, 'Beograd', 950.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(7, 10, 'Images/walker11.jpg', 'Vikendom najvise radim sa mladim i razigranim psima.', 'Zlatni retriver', 5, 'Novi Sad', 850.00, 1, '2026-06-02 11:18:23', '2026-06-15 21:29:15'),
(8, 11, 'Images/walker10.jpg', 'Najvise iskustva imam sa vecim rasama.', 'Nemacki ovcar', 6, 'Nis', 1000.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(9, 12, 'Images/walker8.jpg', 'Popodnevne setnje i pazljiv rad sa psima koji se teze opustaju.', 'Buldog', 4, 'Pancevo', 800.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(10, 13, 'Images/walker7.jpg', 'Fleksibilni termini i iskustvo sa manjim rasama.', 'Pudla', 3, 'Subotica', 750.00, 1, '2026-06-02 11:18:23', '2026-06-02 11:18:23'),
(19, 24, 'Images/walker9.jpg', 'Zdravo, studiram veterinu i volim pse', 'Sibirski Haski', 0, NULL, 750.00, 1, '2026-06-15 20:45:00', '2026-06-15 21:29:46');

-- --------------------------------------------------------

--
-- Table structure for table `walk_logs`
--

CREATE TABLE `walk_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED NOT NULL,
  `walker_id` int(10) UNSIGNED DEFAULT NULL,
  `walk_description` text NOT NULL,
  `route` text DEFAULT NULL,
  `duration_minutes` smallint(5) UNSIGNED NOT NULL,
  `walked_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `walk_logs`
--

INSERT INTO `walk_logs` (`id`, `request_id`, `walker_id`, `walk_description`, `route`, `duration_minutes`, `walked_at`, `created_at`) VALUES
(1, 2, 4, 'Setnja je prosla odlicno', 'Centar i nazad', 30, '2026-06-03 14:03:49', '2026-06-03 14:03:49'),
(2, 3, 5, 'Pas je jako nestasan', 'Park Pionir-Centar', 60, '2026-06-03 14:16:18', '2026-06-03 14:16:18'),
(3, 5, 10, 'Setnja prosla odlicno Laki je super', 'Centar i nazad', 58, '2026-06-03 22:42:00', '2026-06-03 22:42:00'),
(4, 6, 12, 'Pas me je baš izmorio, lep provod smo imali', 'Park Heroja', 50, '2026-06-07 11:15:33', '2026-06-07 11:15:33'),
(5, 7, 4, 'Lepo smo sarađivali Koli i ja', 'Prvomajski bulevar i centar', 70, '2026-06-07 11:18:29', '2026-06-07 11:18:29'),
(6, 8, 8, 'Setnja je bila uspesna', 'Centar i nazad', 45, '2026-06-11 13:30:19', '2026-06-11 13:30:19'),
(7, 9, 8, 'Dobro je proslo', 'Centar i nazad', 50, '2026-06-11 13:50:23', '2026-06-11 13:50:23'),
(8, 10, 5, 'Dobro je proslo', 'Park Pionir-Centar', 50, '2026-06-11 13:57:03', '2026-06-11 13:57:03');

-- --------------------------------------------------------

--
-- Table structure for table `walk_requests`
--

CREATE TABLE `walk_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_id` int(10) UNSIGNED DEFAULT NULL,
  `walker_id` int(10) UNSIGNED DEFAULT NULL,
  `dog_name` varchar(100) NOT NULL,
  `dog_breed` varchar(100) NOT NULL,
  `dog_gender` enum('male','female') NOT NULL,
  `dog_age` tinyint(3) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `status` enum('pending','accepted','completed','cancelled') NOT NULL DEFAULT 'pending',
  `rating_code` varchar(64) DEFAULT NULL,
  `rating_code_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `walk_requests`
--

INSERT INTO `walk_requests` (`id`, `owner_id`, `walker_id`, `dog_name`, `dog_breed`, `dog_gender`, `dog_age`, `description`, `scheduled_at`, `status`, `rating_code`, `rating_code_used`, `created_at`, `updated_at`) VALUES
(1, 3, 4, 'Dragan', 'Haski', 'male', 5, 'Pas ujeda pazi', '2026-06-12 13:59:00', 'cancelled', NULL, 0, '2026-06-03 13:59:57', '2026-06-03 14:03:25'),
(2, 3, 4, 'Max', 'Labrador', 'male', 2, NULL, '2026-06-27 14:01:00', 'completed', '1f3d69bcd142eb08b6915f60aa141dc720200a7f076529fb70316c858251af47', 1, '2026-06-03 14:01:41', '2026-06-03 14:11:19'),
(3, 3, 5, 'Bleki', 'Civava', 'male', 1, 'Ima problema sa nogom', '2026-07-07 14:14:00', 'completed', 'd959e2de7a8c927c89c73ac1327fcdb83bb122c37e9081d3d1d7461c775dea37', 1, '2026-06-03 14:14:51', '2026-06-03 14:18:18'),
(4, 3, 8, 'Lana', 'Hrt', 'female', 6, NULL, '2026-06-06 22:39:00', 'cancelled', NULL, 0, '2026-06-03 22:39:49', '2026-06-11 13:29:59'),
(5, 3, 10, 'Laki', 'Nemacki ovcar', 'male', 2, 'Pas jako vuce potrebna snaga za setanje', '2026-06-19 14:40:00', 'completed', 'adfa9bd2dd8859a2faf97ac45832d88d9e3e62b011a0e51cfdc7fa8f9412aa9f', 1, '2026-06-03 22:41:10', '2026-06-03 22:46:21'),
(6, NULL, 12, 'Poli', 'Labrador', 'male', 1, 'Zivahan pas', '2026-07-02 04:12:00', 'completed', '3944c49b201b0032a6e2c8eae5b922bc420f28a39e38a32570533c6c602a05b2', 1, '2026-06-07 11:13:07', '2026-06-07 11:16:50'),
(7, NULL, 4, 'Koli', 'Border Koli', 'female', 5, 'Pas je stariji i treba polako sa njim', '2026-06-27 02:20:00', 'completed', '0156278bfd6704c936ec4ecf205982c7acdc8afd2a75987149695e5bc946cdef', 1, '2026-06-07 11:17:37', '2026-06-07 11:19:48'),
(8, 3, 8, 'Max', 'Labrador', 'male', 5, NULL, '2026-06-25 13:29:00', 'completed', '921a86bba93a6080874895f7a44916c782fff7f7e2df44021c1ce2ee6ccf4508', 1, '2026-06-11 13:29:27', '2026-06-11 13:34:26'),
(9, 3, 8, 'Dragan', 'Labrador', 'male', 2, NULL, '2026-06-26 13:49:00', 'completed', '54bdc5fe5e551c08309911d0439d9f3009b63bba1ee204b93c08d7765983454a', 0, '2026-06-11 13:49:13', '2026-06-11 13:50:23'),
(10, 3, 5, 'Bleki', 'Haski', 'male', 2, NULL, '2026-06-19 13:55:00', 'completed', '8d9413bb45cd48da6d1206e7571ccd21c4f5c66d3799f967679a60b668a1ef05', 1, '2026-06-11 13:56:01', '2026-06-11 13:57:33');

-- --------------------------------------------------------

--
-- Structure for view `v_most_active_walkers`
--
DROP TABLE IF EXISTS `v_most_active_walkers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_most_active_walkers`  AS SELECT `u`.`id` AS `id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `wp`.`photo` AS `photo`, `wp`.`description` AS `description`, `wp`.`city` AS `city`, count(distinct `wr`.`id`) AS `total_walks`, coalesce(round(avg(`r`.`score`),1),0) AS `avg_score` FROM (((`users` `u` join `walker_profiles` `wp` on(`wp`.`user_id` = `u`.`id`)) left join `walk_requests` `wr` on(`wr`.`walker_id` = `u`.`id` and `wr`.`status` = 'completed')) left join `ratings` `r` on(`r`.`walker_id` = `u`.`id`)) WHERE `u`.`role` = 'walker' AND `u`.`is_active` = 1 AND `u`.`is_approved` = 1 AND `u`.`is_banned` = 0 GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name`, `wp`.`photo`, `wp`.`description`, `wp`.`city` ORDER BY count(distinct `wr`.`id`) DESC, coalesce(round(avg(`r`.`score`),1),0) DESC LIMIT 0, 5 ;

-- --------------------------------------------------------

--
-- Structure for view `v_top_rated_walkers`
--
DROP TABLE IF EXISTS `v_top_rated_walkers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_rated_walkers`  AS SELECT `u`.`id` AS `id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `wp`.`photo` AS `photo`, `wp`.`description` AS `description`, `wp`.`city` AS `city`, `wp`.`experience_years` AS `experience_years`, coalesce(round(avg(`r`.`score`),1),0) AS `avg_score`, count(`r`.`id`) AS `total_ratings` FROM ((`users` `u` join `walker_profiles` `wp` on(`wp`.`user_id` = `u`.`id`)) left join `ratings` `r` on(`r`.`walker_id` = `u`.`id`)) WHERE `u`.`role` = 'walker' AND `u`.`is_active` = 1 AND `u`.`is_approved` = 1 AND `u`.`is_banned` = 0 GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name`, `wp`.`photo`, `wp`.`description`, `wp`.`city`, `wp`.`experience_years` ORDER BY coalesce(round(avg(`r`.`score`),1),0) DESC, count(`r`.`id`) DESC LIMIT 0, 5 ;

-- --------------------------------------------------------

--
-- Structure for view `v_walker_search`
--
DROP TABLE IF EXISTS `v_walker_search`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_walker_search`  AS SELECT `u`.`id` AS `id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `wp`.`photo` AS `photo`, `wp`.`description` AS `description`, `wp`.`favorite_breed` AS `favorite_breed`, `wp`.`experience_years` AS `experience_years`, `wp`.`city` AS `city`, `wp`.`price_per_hour` AS `price_per_hour`, `wp`.`is_available` AS `is_available`, coalesce(round(avg(`r`.`score`),2),0) AS `avg_score`, count(distinct `r`.`id`) AS `total_ratings`, count(distinct `wr`.`id`) AS `total_walks` FROM (((`users` `u` join `walker_profiles` `wp` on(`wp`.`user_id` = `u`.`id`)) left join `ratings` `r` on(`r`.`walker_id` = `u`.`id`)) left join `walk_requests` `wr` on(`wr`.`walker_id` = `u`.`id` and `wr`.`status` = 'completed')) WHERE `u`.`role` = 'walker' AND `u`.`is_active` = 1 AND `u`.`is_approved` = 1 AND `u`.`is_banned` = 0 GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name`, `wp`.`photo`, `wp`.`description`, `wp`.`favorite_breed`, `wp`.`experience_years`, `wp`.`city`, `wp`.`price_per_hour`, `wp`.`is_available` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg_request` (`request_id`),
  ADD KEY `idx_msg_sender` (`sender_id`),
  ADD KEY `idx_msg_receiver` (`receiver_id`),
  ADD KEY `idx_msg_read` (`receiver_id`,`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`,`is_read`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ratings_request` (`request_id`),
  ADD KEY `idx_ratings_walker` (`walker_id`),
  ADD KEY `idx_ratings_owner` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- Indexes for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_walker_profiles_user` (`user_id`),
  ADD KEY `idx_wp_city` (`city`),
  ADD KEY `idx_wp_available` (`is_available`);

--
-- Indexes for table `walk_logs`
--
ALTER TABLE `walk_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_walk_logs_request` (`request_id`),
  ADD KEY `idx_wl_walker` (`walker_id`);

--
-- Indexes for table `walk_requests`
--
ALTER TABLE `walk_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wr_owner` (`owner_id`),
  ADD KEY `idx_wr_walker` (`walker_id`),
  ADD KEY `idx_wr_status` (`status`),
  ADD KEY `idx_wr_scheduled` (`scheduled_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `walk_logs`
--
ALTER TABLE `walk_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `walk_requests`
--
ALTER TABLE `walk_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_msg_request` FOREIGN KEY (`request_id`) REFERENCES `walk_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_rat_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rat_request` FOREIGN KEY (`request_id`) REFERENCES `walk_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rat_walker` FOREIGN KEY (`walker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `walker_profiles`
--
ALTER TABLE `walker_profiles`
  ADD CONSTRAINT `fk_wp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `walk_logs`
--
ALTER TABLE `walk_logs`
  ADD CONSTRAINT `fk_wl_request` FOREIGN KEY (`request_id`) REFERENCES `walk_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wl_walker` FOREIGN KEY (`walker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `walk_requests`
--
ALTER TABLE `walk_requests`
  ADD CONSTRAINT `fk_wr_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_wr_walker` FOREIGN KEY (`walker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
