-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 03:13 AM
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
-- Database: `messageboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` varchar(255) NOT NULL,
  `comment_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment_edited` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `comment_text`, `comment_created`, `comment_edited`) VALUES
(1, 1, 3, 'Looks like an awesome spot!', '2025-09-12 01:36:19', NULL),
(2, 2, 4, 'This is so pretty!! 🌼✨', '2025-09-12 01:48:03', NULL),
(3, 3, 5, 'Wow, incredible! 🙌', '2025-09-12 02:11:56', NULL),
(4, 2, 6, 'Obsessed 😍', '2025-09-12 02:33:36', NULL),
(5, 5, 6, 'Hope you guys had an awesome weekend away!!', '2025-09-12 02:34:20', '2025-09-11 18:34:44'),
(6, 6, 2, 'Living the dream ❤️🪻', '2025-09-12 03:16:18', NULL),
(7, 6, 8, 'You + me + flowers = dream team 💐🙌', '2025-09-18 00:29:29', NULL),
(8, 8, 8, 'You guys are crazy! 😂', '2025-09-18 00:30:02', NULL),
(9, 8, 9, 'Craziest thing we\'ve ever done 🤙🍻', '2025-09-18 00:34:45', NULL),
(10, 12, 10, 'Congrats to the bride & groom! Your styling came out incredible ✨😍', '2025-09-18 00:47:51', NULL),
(11, 7, 10, 'Love this 🤗', '2025-09-18 00:48:31', NULL),
(12, 5, 10, 'Looks like you all had a great time 😊', '2025-09-18 00:49:15', NULL),
(13, 13, 3, 'Awesome shot mate, looks like heaps of fun', '2025-09-18 00:54:21', NULL),
(14, 8, 3, 'This is awesome', '2025-09-18 00:54:51', NULL),
(15, 8, 1, 'Nuts 🥜', '2025-09-18 01:40:29', NULL),
(16, 7, 1, 'My favourite pic of yours!', '2025-09-18 01:41:07', NULL),
(17, 13, 1, 'That\'s some crazy pow! ⛄', '2025-09-18 01:43:13', '2025-09-17 17:43:26'),
(18, 14, 8, 'Looks like a good coffee, where\'d you guys go? 🤔', '2025-09-18 01:47:19', NULL),
(19, 4, 8, 'Splendourful 🙌🌟', '2025-09-18 01:48:49', NULL),
(20, 8, 7, 'Definitely won\'t be our last time jumping out of a plane ✈️😂', '2025-09-18 02:14:58', NULL),
(21, 15, 11, 'Jealous as, this looks awesome!', '2025-09-18 02:38:21', NULL),
(22, 8, 11, 'Wow, I would never do this 😂', '2025-09-18 02:38:53', NULL),
(23, 18, 1, 'Cool spot, how\'d the camp go? ', '2025-09-18 03:32:13', '2025-09-19 16:24:21'),
(24, 18, 3, 'Great shot love it 📸👍', '2025-09-19 01:00:49', NULL),
(25, 54, 3, '💯💯 😂', '2025-09-19 01:01:13', NULL),
(26, 55, 3, 'Wow, that\'s awesome, where are you headed??', '2025-09-19 01:01:42', NULL),
(27, 56, 1, 'This is incredible!', '2025-09-19 01:26:53', NULL),
(28, 56, 9, 'Unreal 😮', '2025-09-19 01:38:43', NULL),
(29, 16, 15, 'Awesome, where was this??', '2025-09-19 02:42:50', NULL),
(30, 61, 3, 'Awesome pic 💯', '2025-09-19 02:57:08', NULL),
(31, 62, 1, 'Fun little wave 👌', '2025-09-20 00:59:14', NULL),
(32, 58, 1, 'Looks like you guys had a heap of fun!!', '2025-09-20 00:59:53', NULL),
(33, 63, 1, 'Awesome 💯😍', '2025-09-20 01:00:48', NULL),
(34, 63, 2, 'This makes me want to take a tropical holiday ASAP 😂', '2025-09-20 01:02:45', NULL),
(35, 59, 2, '🥳🥳🎈', '2025-09-20 01:03:11', NULL),
(36, 63, 4, 'OMG where was this??!', '2025-09-20 01:05:22', NULL),
(37, 59, 4, 'Had a great time with you all 🍷🥳', '2025-09-20 01:07:04', NULL),
(38, 63, 8, 'Looks incredible 🙌🙌', '2025-09-20 01:08:31', NULL),
(39, 63, 10, '🍹😎☀️', '2025-09-20 01:10:39', NULL),
(40, 63, 11, 'What a great view, nice one Ethan!', '2025-09-20 01:12:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `comment_like_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `comment_likes`
--

INSERT INTO `comment_likes` (`comment_like_id`, `comment_id`, `user_id`, `created_at`) VALUES
(1, 1, 4, '2025-09-12 01:47:40'),
(2, 1, 5, '2025-09-12 02:12:13'),
(3, 2, 6, '2025-09-12 02:33:39'),
(4, 3, 6, '2025-09-12 02:33:52'),
(5, 6, 8, '2025-09-18 00:27:46'),
(6, 8, 9, '2025-09-18 00:34:18'),
(7, 14, 1, '2025-09-18 01:40:37'),
(8, 8, 7, '2025-09-18 02:14:17'),
(9, 9, 7, '2025-09-18 02:14:19'),
(10, 15, 7, '2025-09-18 02:14:20'),
(11, 14, 7, '2025-09-18 02:14:21'),
(12, 21, 1, '2025-09-18 03:30:20'),
(13, 21, 3, '2025-09-19 00:59:33'),
(14, 27, 9, '2025-09-19 01:38:21'),
(15, 34, 4, '2025-09-20 01:06:33'),
(16, 38, 10, '2025-09-20 01:10:42');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_picture` varchar(255) DEFAULT NULL,
  `post_text` varchar(255) NOT NULL,
  `post_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `post_edited` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `post_picture`, `post_text`, `post_created`, `post_edited`) VALUES
(1, 1, '/uploads/profiles/1/posts/1/Messenger_creation_5410702A-194B-4210-B33B-E9F9870842E9.jpeg', 'Upstream wander at Minyon Falls, NSW 💦🌿', '2025-09-12 01:21:23', NULL),
(2, 2, '/uploads/profiles/2/posts/2/pexels-lucasfonseca-2216350.jpg', 'Chasing flowers, catching smiles🌻😎', '2025-09-12 01:31:22', NULL),
(3, 3, '/uploads/profiles/3/posts/3/pexels-bri-schneiter-28802-346529.jpg', 'Chilly morning at Aoraki/Mount Cook National Park, New Zealand ❄️🏔️', '2025-09-12 01:34:17', NULL),
(4, 4, '/uploads/profiles/4/posts/4/pexels-wendywei-1540338.jpg', 'Good friends, great music, best memories at Splendour in the Grass 🎶❤️', '2025-09-12 01:49:03', NULL),
(5, 5, '/uploads/profiles/5/posts/5/pexels-josh-willink-11499-1157399.jpg', 'Nothing better than a long weekend away camping with the fam 👍⛰️🏕️', '2025-09-12 02:15:48', NULL),
(6, 6, '/uploads/profiles/6/posts/6/pexels-shkrabaanthony-4612197.jpg', 'In my element 💐', '2025-09-12 02:41:54', NULL),
(7, 3, '/uploads/profiles/3/posts/7/casey-horner-4rDCa5hBlCs-unsplash.jpg', 'Forest Therapy 🌿', '2025-09-12 02:53:54', NULL),
(8, 7, '/uploads/profiles/7/posts/8/pexels-pixabay-70361.jpg', 'The craziest and best thing I\'ve ever done 🪂✈️', '2025-09-12 03:14:08', NULL),
(9, 2, '/uploads/profiles/2/posts/9/pexels-blackeditors-33133005.jpg', 'Beach vibes and great company 🏖️🌊', '2025-09-12 03:18:32', NULL),
(12, 8, '/uploads/profiles/8/posts/12/pexels-asadphoto-169198.jpg', 'From the first mood board to the last petal on the aisle… pulled off the dreamiest ‘I do’ for my bestie in the Maldives 💍🌸🏝️', '2025-09-18 00:27:22', NULL),
(13, 9, '/uploads/profiles/9/posts/13/pexels-agustin-villalba-589020055-17206877.jpg', 'Japan served up the goods… we just showed up 🏂❄️', '2025-09-18 00:39:43', NULL),
(14, 10, '/uploads/profiles/10/posts/14/istockphoto-1464525180-612x612.jpg', 'Double shot of us ☕😉', '2025-09-18 00:52:51', NULL),
(15, 3, '/uploads/profiles/3/posts/15/pexels-pixabay-206359.jpg', 'Peaks in the clouds, fire in the sky 🔥🏔️', '2025-09-18 00:59:34', NULL),
(16, 1, '/uploads/profiles/1/posts/16/Messenger_creation_AF805715-F619-4F51-AE74-3184472EBDE1.jpeg', 'Sunshine Coast Australia day 2025 ☀️😎', '2025-09-18 01:42:41', NULL),
(17, 8, '/uploads/profiles/8/posts/17/pexels-elletakesphotos-1549280.jpg', 'Great vibes with great people ❤️🍷📸', '2025-09-18 01:46:45', NULL),
(18, 7, '/uploads/profiles/7/posts/18/pexels-jacob-riesel-82256039-33138747.jpg', 'Rocky paths, smooth vibes with a great crew🏝️', '2025-09-18 02:17:16', NULL),
(19, 6, '/uploads/profiles/6/posts/19/pexels-roman-odintsov-8180510.jpg', 'Your daily reminder that flowers make everything better 🌼💛', '2025-09-18 02:27:19', NULL),
(54, 11, '/uploads/profiles/11/posts/54/aditya-sethia-T0TyVwTLlMA-unsplash.jpg', 'Coding by day, concerts by night TGIF 🎶😅', '2025-09-18 02:58:25', NULL),
(55, 12, '/uploads/profiles/12/posts/55/pexels-photo-13899806.jpeg', 'Views like this never get old ✈️💙', '2025-09-19 00:49:40', NULL),
(56, 3, '/uploads/profiles/3/posts/56/pexels-simonmigaj-1009136.jpg', 'Aurora Borealis over frozen falls, Iceland 💫❄️', '2025-09-19 00:59:12', NULL),
(57, 13, '/uploads/profiles/13/posts/57/juliette-contin-SwZpnBK48Yo-unsplash.jpg', 'This is my kind of weekend commute 😎🌲', '2025-09-19 01:23:37', NULL),
(58, 9, '/uploads/profiles/9/posts/58/pexels-ben-spadinger-2151746515-32458992.jpg', 'Views worth the climb 🏔️❄️☀️', '2025-09-19 01:29:22', NULL),
(59, 14, '/uploads/profiles/14/posts/59/pexels-rdne-6224633.jpg', 'Flashback to a wild 2024 NYE party with this bunch ❤️😂🍹', '2025-09-19 01:44:16', NULL),
(60, 15, '/uploads/profiles/15/posts/60/photo-1562278574-b4e1c6049ed6.jpg', 'Under the branches, beside the water 🌳🌊☀️', '2025-09-19 02:41:26', NULL),
(61, 7, '/uploads/profiles/7/posts/61/bryan-xandrix-espiritu-VDQ1ir4bMdU-unsplash.jpg', 'Good friends, good fire, good night 🍻🔥🌌', '2025-09-19 02:50:37', NULL),
(62, 5, '/uploads/profiles/5/posts/62/pexels-madbyte-67386.jpg', 'Quick little wave at Snapper 🌊', '2025-09-19 03:22:02', NULL),
(63, 3, '/uploads/profiles/3/posts/63/izdhaan-nizar--N0XrDDwpnc-unsplash.jpg', 'Paradise from above 📸👌', '2025-09-19 03:40:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `post_like_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`post_like_id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 2, '2025-09-12 01:29:53'),
(2, 1, 3, '2025-09-12 01:36:25'),
(3, 2, 3, '2025-09-12 01:36:28'),
(4, 3, 4, '2025-09-12 01:47:27'),
(5, 2, 4, '2025-09-12 01:47:29'),
(6, 1, 4, '2025-09-12 01:47:41'),
(7, 3, 5, '2025-09-12 02:10:26'),
(8, 1, 5, '2025-09-12 02:12:14'),
(9, 5, 6, '2025-09-12 02:31:52'),
(10, 4, 6, '2025-09-12 02:31:56'),
(11, 2, 6, '2025-09-12 02:33:41'),
(12, 3, 6, '2025-09-12 02:33:49'),
(13, 6, 3, '2025-09-12 02:45:08'),
(14, 5, 3, '2025-09-12 02:45:09'),
(15, 4, 3, '2025-09-12 02:45:10'),
(16, 7, 7, '2025-09-12 03:10:44'),
(17, 4, 7, '2025-09-12 03:10:51'),
(18, 3, 7, '2025-09-12 03:10:53'),
(19, 8, 2, '2025-09-12 03:14:46'),
(20, 5, 2, '2025-09-12 03:15:10'),
(21, 6, 2, '2025-09-12 03:15:17'),
(22, 7, 2, '2025-09-12 03:15:43'),
(23, 8, 1, '2025-09-17 23:47:18'),
(24, 9, 8, '2025-09-18 00:27:27'),
(25, 7, 8, '2025-09-18 00:27:40'),
(26, 6, 8, '2025-09-18 00:27:43'),
(27, 8, 8, '2025-09-18 00:29:41'),
(28, 8, 9, '2025-09-18 00:34:15'),
(29, 4, 9, '2025-09-18 00:34:57'),
(30, 2, 9, '2025-09-18 00:35:03'),
(31, 9, 9, '2025-09-18 00:35:18'),
(32, 12, 9, '2025-09-18 00:35:21'),
(33, 12, 10, '2025-09-18 00:46:59'),
(34, 9, 10, '2025-09-18 00:47:55'),
(35, 7, 10, '2025-09-18 00:48:00'),
(36, 6, 10, '2025-09-18 00:48:34'),
(37, 5, 10, '2025-09-18 00:49:18'),
(38, 3, 10, '2025-09-18 00:49:22'),
(39, 13, 10, '2025-09-18 00:49:34'),
(40, 14, 3, '2025-09-18 00:54:00'),
(41, 13, 3, '2025-09-18 00:54:02'),
(42, 9, 3, '2025-09-18 00:54:27'),
(43, 15, 1, '2025-09-18 01:40:04'),
(44, 14, 1, '2025-09-18 01:40:07'),
(45, 13, 1, '2025-09-18 01:40:10'),
(46, 3, 1, '2025-09-18 01:41:15'),
(47, 16, 8, '2025-09-18 01:46:48'),
(48, 15, 8, '2025-09-18 01:46:50'),
(49, 2, 8, '2025-09-18 01:47:37'),
(51, 4, 8, '2025-09-18 01:47:46'),
(52, 14, 8, '2025-09-18 01:49:07'),
(53, 17, 7, '2025-09-18 02:13:44'),
(54, 16, 7, '2025-09-18 02:13:46'),
(55, 13, 7, '2025-09-18 02:13:52'),
(56, 18, 6, '2025-09-18 02:22:16'),
(57, 17, 6, '2025-09-18 02:22:17'),
(58, 15, 6, '2025-09-18 02:22:24'),
(59, 14, 6, '2025-09-18 02:22:25'),
(60, 12, 6, '2025-09-18 02:22:29'),
(61, 8, 6, '2025-09-18 02:27:35'),
(62, 7, 6, '2025-09-18 02:27:36'),
(63, 19, 11, '2025-09-18 02:37:42'),
(64, 18, 11, '2025-09-18 02:37:46'),
(65, 15, 11, '2025-09-18 02:37:55'),
(66, 13, 11, '2025-09-18 02:38:27'),
(67, 8, 11, '2025-09-18 02:38:32'),
(68, 7, 11, '2025-09-18 02:39:27'),
(69, 3, 11, '2025-09-18 02:39:31'),
(70, 1, 11, '2025-09-18 02:46:18'),
(71, 17, 11, '2025-09-18 02:46:31'),
(72, 54, 1, '2025-09-18 03:30:02'),
(73, 18, 1, '2025-09-18 03:30:06'),
(75, 55, 3, '2025-09-19 00:59:16'),
(76, 19, 3, '2025-09-19 00:59:20'),
(77, 17, 3, '2025-09-19 00:59:25'),
(78, 54, 3, '2025-09-19 01:00:56'),
(79, 55, 13, '2025-09-19 01:23:42'),
(80, 54, 13, '2025-09-19 01:23:49'),
(81, 17, 13, '2025-09-19 01:23:57'),
(82, 16, 13, '2025-09-19 01:23:59'),
(83, 13, 13, '2025-09-19 01:24:04'),
(84, 8, 13, '2025-09-19 01:24:08'),
(85, 7, 13, '2025-09-19 01:24:10'),
(86, 3, 13, '2025-09-19 01:24:15'),
(87, 57, 1, '2025-09-19 01:26:38'),
(88, 56, 1, '2025-09-19 01:26:39'),
(90, 57, 9, '2025-09-19 01:38:16'),
(91, 56, 9, '2025-09-19 01:38:19'),
(92, 18, 9, '2025-09-19 01:39:06'),
(93, 15, 9, '2025-09-19 01:39:12'),
(94, 1, 15, '2025-09-19 02:41:46'),
(95, 2, 15, '2025-09-19 02:41:48'),
(96, 5, 15, '2025-09-19 02:41:52'),
(97, 6, 15, '2025-09-19 02:41:56'),
(98, 12, 15, '2025-09-19 02:42:01'),
(99, 15, 15, '2025-09-19 02:42:05'),
(100, 60, 7, '2025-09-19 02:50:42'),
(101, 59, 7, '2025-09-19 02:50:44'),
(102, 58, 7, '2025-09-19 02:50:46'),
(103, 61, 3, '2025-09-19 02:55:51'),
(104, 60, 3, '2025-09-19 02:57:12'),
(105, 59, 3, '2025-09-19 02:57:15'),
(106, 61, 5, '2025-09-19 03:22:06'),
(107, 60, 5, '2025-09-19 03:22:11'),
(108, 56, 5, '2025-09-19 03:27:02'),
(109, 62, 3, '2025-09-19 03:37:47'),
(110, 55, 1, '2025-09-20 00:13:44'),
(111, 63, 1, '2025-09-20 00:58:35'),
(112, 62, 1, '2025-09-20 00:58:37'),
(113, 60, 1, '2025-09-20 00:59:26'),
(114, 61, 1, '2025-09-20 00:59:29'),
(115, 58, 1, '2025-09-20 00:59:35'),
(116, 63, 2, '2025-09-20 01:02:25'),
(117, 59, 2, '2025-09-20 01:02:58'),
(118, 56, 2, '2025-09-20 01:03:19'),
(119, 19, 2, '2025-09-20 01:03:24'),
(120, 54, 2, '2025-09-20 01:04:26'),
(121, 12, 2, '2025-09-20 01:04:37'),
(122, 63, 4, '2025-09-20 01:04:56'),
(123, 60, 4, '2025-09-20 01:06:37'),
(124, 61, 4, '2025-09-20 01:06:39'),
(125, 59, 4, '2025-09-20 01:06:43'),
(126, 57, 4, '2025-09-20 01:07:09'),
(127, 19, 4, '2025-09-20 01:07:43'),
(128, 56, 4, '2025-09-20 01:07:50'),
(129, 63, 8, '2025-09-20 01:08:09'),
(130, 62, 8, '2025-09-20 01:08:45'),
(131, 58, 8, '2025-09-20 01:08:50'),
(132, 57, 8, '2025-09-20 01:08:52'),
(133, 56, 8, '2025-09-20 01:08:55'),
(134, 55, 8, '2025-09-20 01:08:56'),
(135, 19, 8, '2025-09-20 01:09:01'),
(136, 18, 8, '2025-09-20 01:09:03'),
(137, 17, 8, '2025-09-20 01:09:05'),
(138, 63, 9, '2025-09-20 01:09:23'),
(139, 54, 9, '2025-09-20 01:09:36'),
(140, 63, 10, '2025-09-20 01:10:18'),
(141, 56, 10, '2025-09-20 01:10:52'),
(142, 19, 10, '2025-09-20 01:10:55'),
(143, 63, 11, '2025-09-20 01:11:26'),
(144, 56, 11, '2025-09-20 01:12:23'),
(145, 57, 11, '2025-09-20 01:12:31');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `occupation` varchar(50) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`profile_id`, `user_id`, `location`, `occupation`, `bio`, `profile_picture`) VALUES
(1, 1, 'Ballarat, VIC, Australia', 'Web Developer / IT Systems Manager', 'Creator of the Messageboard application 💻🌐', '/uploads/profiles/1/profile_picture/68c37518209ff_Messenger_creation_080B7D02-387F-44D0-95A6-D5D6EB3009FF.jpeg'),
(2, 2, 'Brisbane, QLD, Australia', 'Graphic Designer', 'Sunshine chaser 🌞 | Coffee lover ☕️ | Capturing life in colour 🎨', '/uploads/profiles/2/profile_picture/68c3778a03a30_pexels-alipazani-2613260.jpg'),
(3, 3, 'Melbourne, VIC, Australia', 'Photographer', 'Turning everyday scenes into timeless stories 📸✨', '/uploads/profiles/3/profile_picture/68c3787fbf6af_pexels-phalgunnmaharishi-33539378.jpg'),
(4, 4, 'Sydney, NSW, Australia', 'Nursing Student', 'Beach days, road trips, and chasing every adventure 🌴✈️', '/uploads/profiles/4/profile_picture/68c37baa77c2c_istockphoto-899161162-612x612.jpg'),
(5, 5, 'Perth, WA, Australia', 'Digital Marketer', 'Music on repeat 🎧 | Big fan of good vibes only ✌️', '/uploads/profiles/5/profile_picture/68c381091d7bb_istockphoto-1388253782-612x612.jpg'),
(6, 6, 'Gold Coast, QLD, Australia', 'Florist', 'Sunshine, sea breeze & fresh flowers 🌊🌸', '/uploads/profiles/6/profile_picture/68c386144442c_istockphoto-477151294-612x612.jpg'),
(7, 7, 'Adelaide, SA, Australia', 'Musician & Thrill seeker', 'Late‑night gigs 🎸🎶 | early‑morning hikes 🚶‍♂️🏔️', '/uploads/profiles/7/profile_picture/68c38f2e10a44_istockphoto-638651064-612x612.jpg'),
(8, 8, 'Sydney, NSW, Australia', 'Event Stylist & Planner', 'Making moments look as good as they feel 💛💐📷', '/uploads/profiles/8/profile_picture/68cb512b17490_istockphoto-638756792-612x612.jpg'),
(9, 9, 'Torquay, VIC, Australia', 'Carpenter & Renovator', 'Good tools, good tunes, good mates 🔨🍻', '/uploads/profiles/9/profile_picture/68cb53826f511_pexels-heftiba-1194412.jpg'),
(10, 10, 'Melbourne, VIC, Australia', 'Primary School Teacher', 'Patience, coffee, and a good sense of humour 📚✏️', '/uploads/profiles/10/profile_picture/68cb5653e2dae_istockphoto-1471845315-612x612.jpg'),
(11, 11, 'Geelong, VIC, Australia', 'Lead Software Engineer', 'Debugging life one line at a time 🪲💻', '/uploads/profiles/11/profile_picture/68cb6f7395be0_istockphoto-1338134336-612x612.jpg'),
(12, 12, 'Canberra, ACT, Australia', 'Travel Consultant', 'Professional over‑packer, collecting passport stamps, and good stories ✈️💼', '/uploads/profiles/12/profile_picture/68cca3984d05b_pexels-photo-3768892.jpeg'),
(13, 13, 'Melbourne, VIC, Australia', 'Business Analyst Student', 'City life with a soft spot for the outdoors 🏕️🏙️', '/uploads/profiles/13/profile_picture/68ccaf439ddd8_istockphoto-1406197730-612x612.jpg'),
(14, 14, 'Perth, WA, Australia', 'Social Media Manager', 'Making memories and content on the daily 📸🥂🌅', '/uploads/profiles/14/profile_picture/68ccb5244043b_pexels-rdne-6224743.jpg'),
(15, 15, 'Brisbane, QLD, Australia', 'Digital Marketing Assistant', 'Fluent in memes and iced lattes 😂', '/uploads/profiles/15/profile_picture/68ccb82174801_pexels-joennguyen-2599510.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `date_of_birth`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Jack', 'Marshall', '1997-01-01', 'admin@messageboard.com', '$2y$10$xbBA3OFFZkzMANdFKqRer.c70HCMIvB3AZ9Z0JKYtnEkexXyEgSPy', 'admin', '2025-09-12 01:18:52'),
(2, 'Ava', 'Sterling', '1995-10-09', 'avasterling95@gmail.com', '$2y$10$AJ1I8fSv0nCeh1yJxfOwSOzj2ucZUFIV/eKF7uSEBY.s04vFG.szq', 'user', '2025-09-12 01:24:09'),
(3, 'Ethan', 'Caldwell', '1990-04-05', 'ethancaldwell90@yahoo.com', '$2y$10$nxAas/KJaPePogdXDZxL3Og9h5UwZJr5V14jvZyMXY465yPk1dz9W', 'user', '2025-09-12 01:32:49'),
(4, 'Ruby', 'McKenzie', '2005-04-11', 'rubymckenzie05@hotmail.com', '$2y$10$Qx0XtJSpKtvPknrmXZcORuhcgWfHy7H92UAWlmfyRjI4j4JmK0k9i', 'user', '2025-09-12 01:45:58'),
(5, 'Lucas', 'Navarro', '1982-01-23', 'lucasnavarro82@gmail.com', '$2y$10$uD143oqby0nR6C9pUrc09uENslacxMV9eX8eGH8iJrEq20qAS0Kym', 'user', '2025-09-12 02:06:19'),
(6, 'Chloe', 'Armstrong', '1983-03-07', 'chloearmstrong83@live.com', '$2y$10$JUnHGktTl1RdpwORAqflKOiXiuGBG/hZ1Oj0.4q8H4Vvvva95GB3e', 'user', '2025-09-12 02:28:34'),
(7, 'Nathan', 'Brooks', '1992-02-27', 'nathanbrooks92@gmail.com', '$2y$10$rksJQxRtgsOmXYXMi6C80OTZOGJoIYSWrNqyGGYjjRjheFkWeFAii', 'user', '2025-09-12 02:55:10'),
(8, 'Ella', 'Parker', '1998-12-01', 'ellaparker98@gmail.com', '$2y$10$0TPzKZMUbwdGfTk26DAg2e9qLoI87ARryYltSOxZrlMVOfeN.NKFa', 'user', '2025-09-18 00:20:30'),
(9, 'Damien', 'Foster', '1985-12-19', 'damienfoster85@yahoo.com', '$2y$10$obbWIkl49v1mybv19YOGb.ajp1zBdKJAl94.sQ0kA1jARiZmUxmi6', 'user', '2025-09-18 00:31:42'),
(10, 'Mia', 'Jensen', '1979-01-14', 'miajensen79@outlook.com', '$2y$10$gbOrQmnWnpy0.Y2cojHeFu7Z7xWEcqNxRVKLHqHwwfvKhY755LNbq', 'user', '2025-09-18 00:43:50'),
(11, 'Xavier', 'Stone ', '1988-03-08', 'xavierstone88@gmail.com', '$2y$10$QSLScP3Cr7P5u5eLJpXks.IJqhKPdrpIquU2IbEjAPEALL23mR1X.', 'user', '2025-09-18 02:29:55'),
(12, 'Zoe', 'Choi', '1993-02-22', 'zoechoi93@gmail.com', '$2y$10$tnejD75TU61R3Z/xMiUTEOksmw602T9okA.o6FDIzo4bD4IqSTBbW', 'user', '2025-09-18 03:47:52'),
(13, 'Asher', 'Patel', '1998-05-04', 'asherpatel98@live.com', '$2y$10$v9PjWxuPUGpq.in1zxOi0ut6X7mfkFJFHwJ0a/V9ID5/li98l/ZB.', 'user', '2025-09-19 01:15:08'),
(14, 'Grace', 'Bennett', '2000-05-17', 'gracebennett25@yahoo.com', '$2y$10$ycUpJQTgbH3y29OsFlNQ2.N9yiVB02mkR.qGRgcMyUX3IjqZAHAq6', 'user', '2025-09-19 01:40:08'),
(15, 'Jordan', 'Kim', '2001-09-16', 'jordankim01@aol.com', '$2y$10$NxYABHkziGjd0R6Hu/sWMeLxnhw.067wjGF7qwUrsnF9xvYb6xFnW', 'user', '2025-09-19 01:46:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`comment_like_id`),
  ADD UNIQUE KEY `unique_like` (`comment_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`post_like_id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `comment_like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `post_like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
