-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2025 at 05:15 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_radar`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Đồ Nhật'),
(2, 'Đồ Hàn'),
(3, 'Đồ Trung'),
(4, 'Đồ Việt'),
(5, 'Pizza'),
(6, 'Đồ Âu'),
(7, 'Đồ ăn nhanh');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE `restaurant` (
  `id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `description` text,
  `open_time` varchar(50) DEFAULT NULL,
  `images` text,
  `status` enum('active','hidden') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`id`, `category_id`, `name`, `address`, `description`, `open_time`, `images`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kakuto-Nhà hàng nhật bản', 'Dịch vọng hậu- Cầu Giấy', 'Một số món ăn nổi bật: Thuyền gỗ rán, Cơm sườn heo Cà ri, Katsu rice', '17h-21h', 'Kakuto1.png,Kakuto2.png,Kakuto3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(2, 1, 'Marukame Udon', 'Ngọc Khánh- Ba Đình', 'Chuyên các món mì Udon theo phong cách Nhật Bản', '10h-22h', 'marukame1.png,marukame2.png,marukame3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(3, 1, 'Donchan- Cơm Nhật', 'Dương Khuê- Cầu Giấy', 'Nổi tiếng với món cơm thịt heo tẩm bột chiên giòn', '11h-22h', 'Donchan1.png,Donchan2.png,Donchan3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(4, 1, 'Donsaiya- Cơm mì Nhật Bản', 'Mai Dịch- Cầu Giấy', 'Chuyên các món mì ramen và cơm đặc trưng Nhật Bản', '10h-21h', 'donsaiya1.png,donsaiya2.png,donsaiya3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(5, 4, 'Nét Huế', 'Láng Hạ- Đống Đa', 'Phong cách ẩm thực Huế truyền thống', '7h-22h', 'nethue1.png,nethue2.png,nethue3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(6, 6, 'Pizza Hut', 'Ecohome 3- Bắc Từ Liêm', 'Chuỗi nhà hàng pizza nổi tiếng thế giới', '8h-22h', 'pizzahut1.png,pizzahut2.png,pizzahut3.png', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `restaurant_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `star` int NOT NULL,
  `images` text,
  `likes` int DEFAULT 0,
  `dislikes` int DEFAULT 0,
  `status` enum('pending','approved','hidden') DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `restaurant_id`, `user_id`, `content`, `star`, `images`, `likes`, `dislikes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Nhân viên phục vụ chu đáo, nhiệt tình', 5, '', 0, 0, 'approved', '2025-11-18 16:11:36', '2025-11-18 16:11:36'),
(2, 2, 1, 'Đồ ăn ở đây khá ngon', 5, '', 0, 0, 'approved', '2025-11-18 16:20:44', '2025-11-18 16:20:44'),
(3, 1, 1, 'Đợi bàn hơi lâu', 4, '', 0, 0, 'approved', '2025-11-18 16:21:14', '2025-11-18 16:21:14'),
(4, 2, 1, 'Nhà hàng này hợp khẩu vị', 4, 'assets/images/reviews/683ab665ef95d_udon.png', 0, 0, 'approved', '2025-11-19 07:57:25', '2025-11-19 07:57:25'),
(5, 3, 1, 'Tôi rất thích hà hàng này', 5, '', 0, 0, 'approved', '2025-11-19 07:58:12', '2025-11-19 07:58:12');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','locked') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fullname`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn A', 'user1@gmail.com', '$2y$10$zKwxHhlOd.8CLKkn0zHjg.EVGkmNkymsO2F/xfGW7ixLYSz2/a7bO', 'user', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58'),
(2, 'Nguyễn Văn B', 'user2@gmail.com', '$2y$10$OdhcWeim8vhptQPp8rjMnuNJqGHNb1eMaUzadnR72rEh.Cr3H.Rc2', 'user', 'active', '2025-11-18 06:38:58', '2025-11-18 06:38:58'),
(3, 'Nguyễn Văn C', 'user3@gmail.com', '$2y$10$/DP.vBkSfwsTi.X0iNd.SuAGlcELTOxtosepONob6kTR7KMTAbZIS', 'user', 'active', '2025-11-18 07:10:56', '2025-11-18 07:10:56'),
(4, 'Administrator', 'admin@gmail.com', '$2y$10$/DP.vBkSfwsTi.X0iNd.SuAGlcELTOxtosepONob6kTR7KMTAbZIS', 'admin', 'active', '2025-11-18 06:37:58', '2025-11-18 06:37:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD CONSTRAINT `restaurant_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
