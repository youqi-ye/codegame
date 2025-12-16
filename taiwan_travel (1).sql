-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-04-17 03:26:53
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `taiwan_travel`
--

-- --------------------------------------------------------

--
-- 資料表結構 `ads`
--

CREATE TABLE `ads` (
  `id` int(10) UNSIGNED NOT NULL,
  `img_url` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `ads`
--

INSERT INTO `ads` (`id`, `img_url`, `target_url`, `duration`, `start_date`, `status`) VALUES
(1, 'https://image.kkday.com/v2/image/get/s1.kkday.com/product_195548/20250121025718_6LZMV/jpg', '', 10, '2025-04-13 15:38:19', 1),
(2, 'https://img.ltn.com.tw/Upload/house/page/2025/02/17/250217-23333-1-TL9GE.jpg', '', 10, '2025-04-13 15:39:19', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  `video_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `favorites`
--

INSERT INTO `favorites` (`id`, `username`, `video_id`, `created_at`) VALUES
(30, 7, 'Jfu0KVX_0fk', '2025-04-13 09:57:44'),
(31, 7, 'TG0KHmIIfME', '2025-04-13 10:06:32');

-- --------------------------------------------------------

--
-- 資料表結構 `member_logs`
--

CREATE TABLE `member_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `member_logs`
--

INSERT INTO `member_logs` (`id`, `username`, `login_time`, `logout_time`) VALUES
(1, '7', '2025-04-13 17:16:25', NULL),
(2, '41141110', '2025-04-13 17:26:35', '2025-04-13 17:26:46'),
(3, '7', '2025-04-13 17:27:32', '2025-04-13 17:27:39'),
(4, '41141110', '2025-04-13 17:40:54', '2025-04-13 17:41:10'),
(5, '7', '2025-04-13 17:41:14', '2025-04-13 17:50:13'),
(6, '7', '2025-04-13 17:57:38', '2025-04-13 17:58:09'),
(7, '7', '2025-04-13 18:06:26', '2025-04-13 18:06:58'),
(8, '7', '2025-04-15 14:53:38', '2025-04-15 14:54:50'),
(9, '7', '2025-04-15 14:53:38', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Yuki', 'a0933554787@gmail.com', '旅遊攻略很棒！！真的！！', '2025-04-01 16:11:56'),
(2, 'Yuki', 'a0933554787@gmail.com', '旅遊攻略很棒！！真的！！', '2025-04-01 17:42:19');

-- --------------------------------------------------------

--
-- 資料表結構 `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `rating` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `message`, `created_at`, `rating`) VALUES
(1, '匿名', '真的很棒欸！！', '2025-04-02 12:29:55', 5),
(2, 'Yuki', '旅遊攻略不錯喔！', '2025-04-02 12:52:58', 5),
(3, '匿名', '還行吧！', '2025-04-02 13:21:38', 1),
(5, '匿名', '還行吧！', '2025-04-02 13:23:00', 1),
(6, '匿名', '棒！！', '2025-04-02 13:32:23', 5);

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `phone`) VALUES
(2, '1', '123@gmail.com', '$2y$10$.yWPEPj3rlRFCmRD8UmztuI.ITsyAH0pqSXEaR7Eg8ODeq6G6jUzS', '2025-04-02 15:15:35', NULL),
(3, '2', '2@gmail.com', '$2y$10$04C6nTi0wX3LDmYlGkU0Z.VHWjJhcHu8jOAeCWPDPBYIIr4NMQDtO', '2025-04-02 15:16:40', NULL),
(4, '3', '3@gmail.com', '$2y$10$mQ9P6CML3k8VMAqeclEcqu78p38.9EfRJKSHBhB/Nwm6JMMcsKx9e', '2025-04-02 15:18:38', NULL),
(7, '4', '4@gmail.com', '$2y$10$UBf8iqbrCDcjERQEWQw7n.baYnOOhK3cVR9Q1I.TLcUycW69IRqrq', '2025-04-02 15:23:08', NULL),
(8, '7', '7@gmail.com', '$2y$10$3nXfTz6Nx5d9jMArnZ50WOlMUaUmP2qfERvPBOHVOnnC1gUPPzuwK', '2025-04-03 08:24:09', NULL),
(9, '41141110', 'a@gmail.com', '$2y$10$2tvVMooD9WBfgbPEW/WNKuLLSVQJSItk/5WkwmsDifbM8Gn0DJiPu', '2025-04-03 08:34:29', NULL),
(10, '9', '9@gmail.com', '$2y$10$gxJ9Cm15P3.p8dF3s.hWWuyR539rXw8IgDtAwdnQh0USsXfmLOEQ.', '2025-04-04 06:45:17', '091234567'),
(11, '22', 'lintingjin@gmail.com', '$2y$10$xQ4YQl9nO1ptSdYbrTW.quiMyBLPYpGasUCd0.3qk4f1hJTBI0MYK', '2025-04-13 08:55:23', '095555555');

-- --------------------------------------------------------

--
-- 資料表結構 `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `video_id` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `videos`
--

INSERT INTO `videos` (`id`, `title`, `video_id`, `status`) VALUES
(5, '台灣年年推觀光為何大崩盤？外國客抱怨這致命缺點慘輸鄰國！高雄逆勢爆紅成國旅最熱門城市？」', 'Jfu0KVX_0fk', 1),
(6, '【台灣旅遊】淡水美食深度遊，本地人才知道的10間隱藏小吃', 'TG0KHmIIfME', 1),
(7, '關於這次事件，我家人想說的是....【Andy老師】', 'CjxJWGpynX0', 1),
(8, '【台灣旅遊】5天4夜台中旅行Vlog｜溫體牛、屋馬燒肉好好吃🤤 // 台中好玩好吃全紀錄💕', '4X19VAH7b3g', 1);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`username`,`video_id`),
  ADD KEY `video_id` (`video_id`);

--
-- 資料表索引 `member_logs`
--
ALTER TABLE `member_logs`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member_logs`
--
ALTER TABLE `member_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
