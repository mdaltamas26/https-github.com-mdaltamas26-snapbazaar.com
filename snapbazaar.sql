-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 09:54 AM
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
-- Database: `snapbazaar`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'India',
  `pincode` varchar(10) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_name` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `name`, `phone`, `address`, `city`, `state`, `country`, `pincode`, `is_default`, `created_at`, `updated_at`, `full_name`, `mobile`, `type`) VALUES
(1, 1, 'Md Altamas', '9534427814', 'Metro Lay Out Nayanda Halli', 'bengalore', 'karnataka', 'India', '560039', 0, '2025-03-25 10:28:31', '2025-05-30 16:14:58', 'Md Altamas', NULL, NULL),
(3, 7, 'Md Altamas', '9102228481', 'Benglore 560039 balaji medical store ', 'Benglore ', 'Karnataka ', 'India', '560039', 0, '2025-04-01 16:38:47', '2025-04-06 23:19:17', 'Md Altamas ', NULL, NULL),
(4, 11, '', '09102228481', 'Metro Lay Out Nayanda Halli', 'bengalore', 'karnataka', 'India', '560039', 0, '2025-05-28 08:30:39', '2025-05-28 08:30:39', 'Md Altamas', NULL, NULL),
(5, 12, '', '9334295859', 'Metro Lay Out Nayanda Halli', 'bengalore', 'karnataka', 'India', '851131', 0, '2025-05-29 18:37:37', '2025-05-30 16:19:37', 'Md Altamas', NULL, NULL),
(6, 17, '', '09102228481', 'Metro Lay Out Nayanda Halli', 'bengalore', 'karnataka', 'India', '560039', 0, '2025-05-30 05:49:05', '2025-05-30 05:49:05', 'Md Altamas', NULL, NULL),
(8, 22, '', '9334295859', 'Karichak begusarai 851131', 'begusarai', 'bihar', 'India', '851131', 0, '2025-06-03 13:16:37', '2025-06-03 13:16:37', 'Ataullah', NULL, NULL),
(9, 23, '', '09334295859', 'Karichak begusarai 851131', 'begusarai', 'bihar', 'India', '851131', 0, '2025-06-05 03:42:08', '2025-06-05 03:42:08', 'Ataullah', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `name`, `email`, `mobile`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'angryffgaming99@gmail.com', 'Md Altamas', '', '9102228481', '$2y$10$KxQow7r8eIMS2uSZAZekp.3rwEev5Pdo4zrEO8kjzLsZoBb0bYuna', 'admin', 'active', '2025-03-25 10:12:59', '2025-04-08 10:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(93, 7, 31, 1, '2025-05-28 18:04:07'),
(98, 17, 33, 1, '2025-05-30 06:05:07'),
(99, 18, 33, 1, '2025-05-30 06:06:02'),
(106, 12, 33, 1, '2025-05-30 17:43:00'),
(143, 1, 30, 1, '2025-06-05 03:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `min_purchase` decimal(10,2) NOT NULL,
  `max_discount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_percentage`, `valid_from`, `valid_to`, `min_purchase`, `max_discount`, `created_at`, `discount`, `expiry_date`) VALUES
(9, 'AN', 0.00, '0000-00-00', '0000-00-00', 0.00, 0.00, '2025-05-06 09:49:40', 100.00, '2025-06-27');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_messages`
--

CREATE TABLE `deleted_messages` (
  `id` int(11) NOT NULL,
  `original_sender_id` varchar(255) DEFAULT NULL,
  `original_receiver_id` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `original_timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(100) DEFAULT NULL,
  `receiver_id` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `file_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `is_read`, `file_url`) VALUES
(137, '1', 'admin', 'Hi', '2025-04-10 21:20:58', 0, NULL),
(138, 'admin', '1', '<a href=\'livechat/1744303107_Screenshot 2025-03-06 112315.png\' target=\'_blank\'>📎 Screenshot 2025-03-06 112315.png</a>', '2025-04-10 22:08:27', 0, NULL),
(139, '1', 'admin', '<a href=\'livechat/1745143103_HII AJMAL HUSSAIN MINOR_PROJECT_PERSNAL_REPORT_[1][1].doc\' target=\'_blank\'>📎 HII AJMAL HUSSAIN MINOR_PROJECT_PERSNAL_REPORT_[1][1].doc</a>', '2025-04-20 15:28:23', 0, NULL),
(140, '1', 'admin', '<a href=\'livechat/1745143144_VID20250420152843.mp4\' target=\'_blank\'>📎 VID20250420152843.mp4</a>', '2025-04-20 15:29:04', 0, NULL),
(141, '1', 'admin', 'h99', '2025-05-14 20:11:08', 0, NULL),
(143, '17', 'admin', 'hii', '2025-05-30 11:23:02', 0, NULL),
(144, 'admin', '17', 'ginga', '2025-05-30 11:23:26', 0, NULL),
(145, '20', 'admin', 'hello', '2025-06-01 22:58:16', 0, NULL),
(146, '22', 'admin', 'hiiiiiiiiiiiiiiiiiiii', '2025-06-03 18:44:58', 0, NULL),
(147, 'admin', '22', 'jk5', '2025-06-03 18:54:25', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `my_orders`
--

CREATE TABLE `my_orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','processing','shipped','delivered','canceled','refund_requested') NOT NULL,
  `tracking_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `transaction_id` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'UPI',
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `refund_status` varchar(20) DEFAULT 'none',
  `address_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `product_name` varchar(255) DEFAULT NULL,
  `delivery_lat` varchar(50) DEFAULT NULL,
  `delivery_lng` varchar(50) DEFAULT NULL,
  `latitude` varchar(20) DEFAULT NULL,
  `longitude` varchar(20) DEFAULT NULL,
  `wallet_deduction` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `my_orders`
--

INSERT INTO `my_orders` (`id`, `order_id`, `user_id`, `product_id`, `quantity`, `total_price`, `payment_status`, `order_status`, `tracking_id`, `created_at`, `updated_at`, `status`, `transaction_id`, `payment_method`, `coupon_code`, `discount_amount`, `address`, `phone`, `refund_status`, `address_id`, `total_amount`, `order_date`, `product_name`, `delivery_lat`, `delivery_lng`, `latitude`, `longitude`, `wallet_deduction`) VALUES
(37, '', 1, 31, 1, 699.00, 'failed', '', NULL, '2025-04-10 16:28:38', '2025-06-05 03:35:33', '', '195664563356', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-04-10 21:58:38', NULL, NULL, NULL, NULL, NULL, 0.00),
(38, '', 1, 28, 1, 199.00, 'paid', 'delivered', NULL, '2025-04-10 16:30:32', '2025-05-16 15:51:47', '', '419565566262', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-04-10 22:00:32', NULL, NULL, NULL, NULL, NULL, 0.00),
(40, '', 1, 28, 1, 1149.00, 'paid', 'delivered', NULL, '2025-05-14 11:55:47', '2025-05-14 14:03:30', 'active', 'TXN200497', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 17:25:47', NULL, NULL, NULL, NULL, NULL, 0.00),
(43, '', 1, 33, 1, 1149.00, 'paid', 'delivered', NULL, '2025-05-14 11:55:47', '2025-05-14 14:03:45', 'active', 'TXN200497', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 17:25:47', NULL, NULL, NULL, NULL, NULL, 0.00),
(44, '', 1, 33, 1, 1502.00, 'paid', 'delivered', NULL, '2025-05-14 14:24:23', '2025-06-04 16:44:46', 'active', 'TXN943650', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 19:54:23', NULL, NULL, NULL, NULL, NULL, 0.00),
(45, '', 1, 32, 1, 1502.00, 'paid', 'delivered', NULL, '2025-05-14 14:24:23', '2025-06-04 16:52:24', 'active', 'TXN943650', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 19:54:23', NULL, NULL, NULL, NULL, NULL, 0.00),
(46, '', 1, 33, 1, 2.00, 'paid', 'delivered', NULL, '2025-05-14 14:29:43', '2025-06-04 16:56:57', 'active', 'TXN317860', '', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 19:59:43', NULL, NULL, NULL, NULL, NULL, 0.00),
(47, '', 1, 33, 1, 2.00, 'paid', 'delivered', NULL, '2025-05-14 14:31:51', '2025-05-20 09:16:42', 'active', 'TXN269668', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 20:01:51', NULL, NULL, NULL, NULL, NULL, 0.00),
(48, '', 1, 32, 1, 1500.00, 'paid', 'delivered', NULL, '2025-05-14 14:31:51', '2025-05-20 09:16:39', 'active', 'TXN269668', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli', '9102228481', 'none', NULL, 0.00, '2025-05-14 20:01:51', NULL, NULL, NULL, NULL, NULL, 0.00),
(50, '', 1, 33, 1, 2.00, 'paid', 'delivered', NULL, '2025-05-14 14:39:05', '2025-05-20 09:16:35', '', '195664563356', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-14 20:09:05', NULL, NULL, NULL, NULL, NULL, 0.00),
(51, '', 1, 31, 1, 699.00, 'pending', 'pending', NULL, '2025-05-19 11:31:29', '2025-05-19 11:31:29', '', '111111111145', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-19 17:01:29', NULL, NULL, NULL, NULL, NULL, 0.00),
(52, '', 1, 28, 1, 199.00, 'pending', 'pending', NULL, '2025-05-19 11:31:29', '2025-05-19 11:31:29', '', '111111111145', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-19 17:01:29', NULL, NULL, NULL, NULL, NULL, 0.00),
(53, '', 1, 28, 1, 199.00, 'pending', 'pending', NULL, '2025-05-19 11:36:27', '2025-05-19 11:36:27', '', '195664563356', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-19 17:06:27', NULL, NULL, NULL, NULL, NULL, 0.00),
(54, '', 1, 33, 1, 2.00, 'pending', 'delivered', NULL, '2025-05-19 11:37:23', '2025-06-04 17:37:12', 'active', 'TXN55859915', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-19 17:07:23', NULL, NULL, NULL, NULL, NULL, 0.00),
(55, '', 1, 31, 1, 699.00, 'paid', 'delivered', NULL, '2025-05-19 11:37:23', '2025-05-20 09:15:16', 'active', 'TXN55859915', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-19 17:07:23', NULL, NULL, NULL, NULL, NULL, 0.00),
(56, '', 1, 32, 1, 1500.00, 'paid', 'pending', NULL, '2025-05-19 11:37:57', '2025-06-04 17:35:22', 'active', 'TXN95640846', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-19 17:07:57', NULL, NULL, NULL, NULL, NULL, 0.00),
(57, '', 1, 33, 1, 2.00, 'paid', 'delivered', NULL, '2025-05-19 11:37:57', '2025-05-20 09:16:24', 'active', 'TXN95640846', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-19 17:07:57', NULL, NULL, NULL, NULL, NULL, 0.00),
(58, '', 1, 32, 1, 1400.00, 'pending', 'shipped', NULL, '2025-05-19 11:44:36', '2025-06-04 17:06:28', 'active', 'TXN51294003', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-19 17:14:36', NULL, NULL, NULL, NULL, NULL, 0.00),
(59, '', 1, 33, 3, 6.00, 'pending', 'pending', NULL, '2025-05-28 08:04:07', '2025-05-28 08:04:07', 'active', 'TXN82485202', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 13:34:07', NULL, NULL, NULL, NULL, NULL, 0.00),
(60, '', 1, 28, 2, 398.00, 'pending', 'shipped', NULL, '2025-05-28 08:04:07', '2025-06-04 17:07:50', 'active', 'TXN82485202', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 13:34:07', NULL, NULL, NULL, NULL, NULL, 0.00),
(61, '', 1, 29, 1, 35000.00, 'pending', 'pending', NULL, '2025-05-28 08:10:04', '2025-05-28 08:10:04', '', '195664563356', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-28 13:40:04', NULL, NULL, NULL, NULL, NULL, 0.00),
(62, '', 1, 30, 1, 21999.00, 'pending', 'pending', NULL, '2025-05-28 08:10:23', '2025-05-28 08:10:23', 'active', 'TXN41385754', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 13:40:23', NULL, NULL, NULL, NULL, NULL, 0.00),
(63, '', 1, 33, 1, 2.00, 'pending', 'pending', NULL, '2025-05-28 08:25:05', '2025-05-28 08:25:05', 'active', 'TXN55073622', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 13:55:05', NULL, NULL, NULL, NULL, NULL, 0.00),
(64, '', 7, 31, 1, 699.00, 'pending', 'pending', NULL, '2025-05-28 08:27:33', '2025-05-28 08:27:33', 'active', 'TXN12946720', 'COD', '', 0.00, 'Benglore 560039 balaji medical store , Benglore , Karnataka  - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 13:57:33', NULL, NULL, NULL, NULL, NULL, 0.00),
(65, '', 11, 28, 1, 199.00, 'pending', '', NULL, '2025-05-28 08:30:46', '2025-05-28 08:36:38', 'active', 'TXN13242742', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 14:00:46', NULL, NULL, NULL, NULL, NULL, 0.00),
(66, '', 11, 31, 1, 699.00, 'pending', '', NULL, '2025-05-28 08:32:07', '2025-05-28 08:36:01', 'active', 'TXN38784477', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 14:02:07', NULL, NULL, NULL, NULL, NULL, 0.00),
(67, '', 11, 30, 1, 21999.00, 'paid', 'delivered', NULL, '2025-05-28 08:36:53', '2025-05-30 06:14:25', 'active', 'TXN41536403', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 14:06:53', NULL, NULL, NULL, NULL, NULL, 0.00),
(68, '', 11, 28, 1, 199.00, 'pending', 'pending', NULL, '2025-05-28 16:45:32', '2025-05-28 16:45:32', 'active', 'TXN85622098', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 22:15:32', NULL, NULL, NULL, NULL, NULL, 0.00),
(69, '', 11, 33, 1, 1.87, 'pending', 'pending', NULL, '2025-05-28 16:50:20', '2025-05-28 16:50:20', 'active', 'TXN57527990', 'COD', 'an', 0.13, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 22:20:20', NULL, NULL, NULL, NULL, NULL, 0.00),
(70, '', 11, 32, 1, 1400.13, 'pending', 'pending', NULL, '2025-05-28 16:50:20', '2025-05-28 16:50:20', 'active', 'TXN57527990', 'COD', 'an', 99.87, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-28 22:20:20', NULL, NULL, NULL, NULL, NULL, 0.00),
(71, '', 7, 31, 1, 667.21, 'pending', 'pending', NULL, '2025-05-28 16:56:30', '2025-05-28 16:56:30', 'active', 'TXN74037412', 'COD', 'an', 31.79, 'Benglore 560039 balaji medical store , Benglore , Karnataka  - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 22:26:30', NULL, NULL, NULL, NULL, NULL, 0.00),
(72, '', 7, 32, 1, 1431.79, 'pending', 'pending', NULL, '2025-05-28 16:56:30', '2025-05-28 16:56:30', 'active', 'TXN74037412', 'COD', 'an', 68.21, 'Benglore 560039 balaji medical store , Benglore , Karnataka  - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 22:26:30', NULL, NULL, NULL, NULL, NULL, 0.00),
(73, '', 7, 28, 1, 199.00, 'paid', 'pending', NULL, '2025-05-28 17:48:45', '2025-05-28 17:48:45', 'active', 'TXN78638615', 'UPI', '', 0.00, 'Benglore 560039 balaji medical store , Benglore , Karnataka  - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 23:18:45', NULL, NULL, NULL, NULL, NULL, 0.00),
(74, '', 7, 31, 1, 699.00, 'paid', 'pending', NULL, '2025-05-28 17:48:45', '2025-05-28 17:48:45', 'active', 'TXN78638615', 'UPI', '', 0.00, 'Benglore 560039 balaji medical store , Benglore , Karnataka  - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-28 23:18:45', NULL, NULL, NULL, NULL, NULL, 0.00),
(75, '', 7, 30, 1, 21999.00, 'paid', 'pending', NULL, '2025-05-28 17:49:51', '2025-06-04 17:35:12', '', '195664563356', 'UPI', NULL, 0.00, NULL, NULL, 'none', NULL, 0.00, '2025-05-28 23:19:51', NULL, NULL, NULL, NULL, NULL, 0.00),
(76, '', 1, 33, 1, 1.87, 'pending', 'pending', NULL, '2025-05-29 18:12:08', '2025-05-29 18:12:08', 'active', 'TXN25531564', 'COD', 'an', 0.13, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-29 23:42:08', NULL, NULL, NULL, NULL, NULL, 0.00),
(77, '', 1, 32, 1, 1400.13, 'pending', 'shipped', NULL, '2025-05-29 18:12:08', '2025-06-04 17:05:30', 'active', 'TXN25531564', 'COD', 'an', 99.87, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9102228481', 'none', NULL, 0.00, '2025-05-29 23:42:08', NULL, NULL, NULL, NULL, NULL, 0.00),
(78, '', 12, 29, 1, 35000.00, 'paid', 'delivered', NULL, '2025-05-29 18:37:42', '2025-05-30 06:14:03', 'active', 'TXN38266063', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 851131', '09102228481', 'none', NULL, 0.00, '2025-05-30 00:07:42', NULL, NULL, NULL, NULL, NULL, 0.00),
(79, '', 17, 30, 1, 21899.00, 'paid', 'delivered', NULL, '2025-05-30 05:49:27', '2025-05-30 06:14:11', 'active', 'TXN15506061', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '09102228481', 'none', NULL, 0.00, '2025-05-30 11:19:27', NULL, NULL, NULL, NULL, NULL, 0.00),
(80, '', 12, 28, 2, 298.00, 'pending', 'pending', NULL, '2025-05-30 16:48:43', '2025-05-30 16:48:43', 'active', 'TXN35412030', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 851131', '9334295859', 'none', NULL, 0.00, '2025-05-30 22:18:43', NULL, NULL, NULL, NULL, NULL, 0.00),
(81, '', 12, 32, 2, 3000.00, 'paid', 'pending', NULL, '2025-05-30 17:31:54', '2025-05-30 17:31:54', 'active', 'TXN42445281', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 851131', '9334295859', 'none', NULL, 0.00, '2025-05-30 23:01:54', NULL, NULL, NULL, NULL, NULL, 0.00),
(82, '', 1, 33, 1, 1.87, 'pending', 'pending', NULL, '2025-05-30 17:50:42', '2025-05-30 17:50:42', 'active', 'TXN67349642', 'COD', 'an', 0.13, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-05-30 23:20:42', NULL, NULL, NULL, NULL, NULL, 0.00),
(83, '', 1, 32, 1, 1400.13, 'paid', 'delivered', NULL, '2025-05-30 17:50:42', '2025-06-04 17:00:10', 'active', 'TXN67349642', 'COD', 'an', 99.87, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-05-30 23:20:42', NULL, NULL, NULL, NULL, NULL, 0.00),
(84, '', 1, 28, 1, 199.00, 'paid', 'pending', NULL, '2025-05-30 17:55:44', '2025-05-30 17:55:44', 'active', 'TXN90787066', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-05-30 23:25:44', NULL, NULL, NULL, NULL, NULL, 0.00),
(85, '', 1, 33, 3, 6.00, 'paid', 'pending', NULL, '2025-05-31 06:27:17', '2025-05-31 06:27:17', 'active', 'TXN46638799', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-05-31 11:57:17', NULL, NULL, NULL, NULL, NULL, 0.00),
(86, '', 1, 33, 1, 2.00, 'paid', 'pending', NULL, '2025-06-01 13:16:53', '2025-06-01 13:16:53', 'active', 'TXN87147220', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-01 18:46:53', NULL, NULL, NULL, NULL, NULL, 0.00),
(87, '', 1, 32, 1, 1500.00, 'paid', 'pending', NULL, '2025-06-01 13:16:53', '2025-06-01 13:16:53', 'active', 'TXN87147220', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-01 18:46:53', NULL, NULL, NULL, NULL, NULL, 0.00),
(89, '', 1, 28, 1, 199.00, 'paid', 'pending', NULL, '2025-06-02 11:13:25', '2025-06-02 11:13:25', 'active', 'TXN78832286', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-02 16:43:25', NULL, NULL, NULL, NULL, NULL, 0.00),
(90, '', 22, 33, 2, 4.00, 'pending', 'shipped', NULL, '2025-06-03 13:19:29', '2025-06-03 13:20:40', 'active', 'TXN68296772', 'UPI', '', 0.00, 'Karichak begusarai 851131, begusarai, bihar - 851131', '9334295859', 'none', NULL, 0.00, '2025-06-03 18:49:29', NULL, NULL, NULL, NULL, NULL, 0.00),
(91, '', 1, 32, 1, 1500.00, 'pending', 'pending', NULL, '2025-06-04 07:30:52', '2025-06-04 07:30:52', 'active', 'TXN40221357', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 13:00:52', NULL, NULL, NULL, NULL, NULL, 0.00),
(92, '', 1, 32, 1, 1500.00, 'pending', 'pending', NULL, '2025-06-04 07:44:56', '2025-06-04 07:44:56', 'active', 'TXN43749987', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 13:14:56', NULL, NULL, NULL, NULL, NULL, 0.00),
(93, '', 1, 32, 2, 2900.00, 'pending', 'pending', NULL, '2025-06-04 08:15:18', '2025-06-04 08:15:18', 'active', 'TXN66549448', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 13:45:18', NULL, NULL, NULL, NULL, NULL, 0.00),
(94, '', 1, 29, 1, 34900.00, 'pending', 'pending', NULL, '2025-06-04 08:21:19', '2025-06-04 08:21:19', 'active', 'TXN73956191', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 13:51:19', NULL, NULL, NULL, NULL, NULL, 1000.00),
(95, '', 1, 32, 1, 1500.00, 'pending', 'pending', NULL, '2025-06-04 08:22:48', '2025-06-04 08:22:48', 'active', 'TXN63379907', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 13:52:48', NULL, NULL, NULL, NULL, NULL, 1000.00),
(96, '', 1, 30, 1, 21999.00, 'pending', 'pending', NULL, '2025-06-04 08:40:10', '2025-06-04 08:40:10', 'active', 'TXN63155298', 'COD', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:10:10', NULL, NULL, NULL, NULL, NULL, 0.00),
(97, '', 1, 29, 1, 32900.00, 'paid', 'delivered', NULL, '2025-06-04 08:41:27', '2025-06-04 17:12:25', 'active', 'TXN94349326', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:11:27', NULL, NULL, NULL, NULL, NULL, 2000.00),
(98, '', 1, 31, 1, 0.00, 'paid', '', NULL, '2025-06-04 08:50:07', '2025-06-04 10:22:03', 'active', 'TXN14187764', 'Wallet', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:20:07', NULL, NULL, NULL, NULL, NULL, 699.00),
(99, '', 1, 31, 3, 696.00, 'pending', 'pending', NULL, '2025-06-04 08:52:44', '2025-06-04 08:52:44', 'active', 'TXN56872163', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:22:44', NULL, NULL, NULL, NULL, NULL, 1301.00),
(100, '', 1, 30, 1, 0.00, 'paid', '', NULL, '2025-06-04 09:10:22', '2025-06-04 09:28:46', 'active', 'TXN53156775', 'Wallet', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:40:22', NULL, NULL, NULL, NULL, NULL, 21999.00),
(101, '', 1, 31, 1, 0.00, 'paid', '', NULL, '2025-06-04 09:14:46', '2025-06-04 09:34:51', 'active', 'TXN91070501', 'Wallet', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:44:46', NULL, NULL, NULL, NULL, NULL, 699.00),
(102, '', 1, 28, 1, 0.00, 'paid', '', NULL, '2025-06-04 09:21:19', '2025-06-04 09:23:07', 'active', 'TXN57247750', 'Wallet', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 14:51:19', NULL, NULL, NULL, NULL, NULL, 199.00),
(103, '', 1, 29, 1, 4400.00, 'paid', 'delivered', NULL, '2025-06-04 10:23:43', '2025-06-04 11:49:08', 'active', 'TXN37331689', 'COD', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 15:53:43', NULL, NULL, NULL, NULL, NULL, 30500.00),
(104, '', 1, 30, 2, 14503.54, 'pending', 'pending', NULL, '2025-06-04 10:58:36', '2025-06-04 10:58:36', 'active', 'TXN29568318', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 16:28:36', NULL, NULL, NULL, NULL, NULL, 29494.46),
(105, '', 1, 32, 1, 494.46, 'pending', 'pending', NULL, '2025-06-04 10:58:36', '2025-06-04 10:58:36', 'active', 'TXN29568318', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 16:28:36', NULL, NULL, NULL, NULL, NULL, 1005.54),
(106, '', 1, 29, 1, 4500.00, 'pending', 'pending', NULL, '2025-06-04 11:09:02', '2025-06-04 11:09:02', 'active', 'TXN74465310', 'UPI', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 16:39:02', NULL, NULL, NULL, NULL, NULL, 30500.00),
(107, '', 1, 29, 1, 4400.00, 'pending', '', NULL, '2025-06-04 11:16:10', '2025-06-04 11:47:25', 'active', 'TXN32398985', 'UPI', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 16:46:10', NULL, NULL, NULL, NULL, NULL, 30500.00),
(108, '', 1, 32, 22, 2400.00, 'pending', '', NULL, '2025-06-04 11:42:26', '2025-06-04 11:46:27', 'active', 'TXN55130385', 'UPI', 'an', 100.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 17:12:26', NULL, NULL, NULL, NULL, NULL, 30500.00),
(109, '', 1, 28, 1, 0.00, 'paid', '', NULL, '2025-06-04 17:14:02', '2025-06-04 18:06:52', 'active', 'TXN69155651', 'Wallet', '', 0.00, 'Metro Lay Out Nayanda Halli, bengalore, karnataka - 560039', '9534427814', 'none', NULL, 0.00, '2025-06-04 22:44:02', NULL, NULL, NULL, NULL, NULL, 199.00),
(110, '', 23, 31, 1, 599.00, 'pending', 'pending', NULL, '2025-06-05 03:42:52', '2025-06-05 03:42:52', 'active', 'TXN61406028', 'COD', 'an', 100.00, 'Karichak begusarai 851131, begusarai, bihar - 851131', '09334295859', 'none', NULL, 0.00, '2025-06-05 09:12:52', NULL, NULL, NULL, NULL, NULL, 0.00),
(111, '', 23, 32, 1, 1400.00, 'failed', '', NULL, '2025-06-05 03:46:17', '2025-06-05 03:47:16', 'active', 'TXN10111255', 'UPI', 'an', 100.00, 'Karichak begusarai 851131, begusarai, bihar - 851131', '09334295859', 'none', NULL, 0.00, '2025-06-05 09:16:17', NULL, NULL, NULL, NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `order_status` enum('processing','shipped','delivered','cancelled') DEFAULT 'processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `extra_fields` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_name` varchar(255) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT 0,
  `is_sale` tinyint(1) DEFAULT 0,
  `brand` varchar(100) DEFAULT NULL,
  `warranty` varchar(100) DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `return_policy` text DEFAULT NULL,
  `rating` float DEFAULT 0,
  `total_reviews` int(11) DEFAULT 0,
  `video_url` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `battery` varchar(50) DEFAULT NULL,
  `screen_size` varchar(50) DEFAULT NULL,
  `processor` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `extra_details` text DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `on_sale` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `extra_fields`, `images`, `stock`, `category`, `image`, `created_at`, `product_name`, `is_new`, `is_sale`, `brand`, `warranty`, `specifications`, `highlights`, `return_policy`, `rating`, `total_reviews`, `video_url`, `tags`, `model_number`, `color`, `ram`, `storage`, `battery`, `screen_size`, `processor`, `material`, `size`, `gender`, `video`, `extra_details`, `gallery_images`, `on_sale`) VALUES
(28, 'One Piece, Vol. 1: Volume 1 IEnglish, Paperback, Oda Elichiro)', 'One Piece, Vol. 1: Volume 1 IEnglish, Paperback, Oda Elichiro)', 199.00, '{\"author\":\"Rafi\",\"publisher\":\"Rafi\"}', '[\"IMG_20250410_212507.jpg\"]', 190, 'Books', 'IMG_20250410_212507.jpg', '2025-04-10 16:14:45', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(29, 'ASUS Vivobook 15, with Backlit Keyboard, Intel Core 15 12th Gen', 'ASUS Vivobook 15, with Backlit Keyboard, Intel Core 15 12th Gen', 35000.00, '{\"ram\":\"8 GB RAM\",\"rom\":\"256 GB ROM\",\"processor\":\"Inte code \",\"scareen size\":\"16.9 \",\"weight\":\"1.7 KG\"}', '[\"IMG_20250410_212523.jpg\"]', 999, 'Laptop', 'IMG_20250410_212523.jpg', '2025-04-10 16:17:14', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(30, 'realme 12 Pro+ ', 'realme 12 Pro+', 21999.00, '{\"ram\":\"8 GB RAM\",\"rom\":\"256 GB ROM\",\"battery\":\"5000 mah\",\"processor\":\"Inte core 7s jen 2\"}', '[\"IMG_20250410_212548.jpg\"]', 150, 'Mobile', 'IMG_20250410_212548.jpg', '2025-04-10 16:22:05', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(31, 'Jeremy Lin Series Basketball Shoes For Men (Bluc, Purple)', 'Jeremy Lin Series Basketball Shoes For Men (Bluc, Purple)', 699.00, 'null', '[\"IMG_20250410_212646.jpg\"]', 999, 'Faishon', 'IMG_20250410_212646.jpg', '2025-04-10 16:23:39', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(32, 'Earbuds JBL ', 'Earbuds JBL ', 1500.00, 'null', '[\"Screenshot 2025-04-10 215456.png\"]', 1400, 'Home Appliances', 'Screenshot 2025-04-10 215456.png', '2025-04-10 16:27:23', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(33, 'Yawer', 'rafi', 2.00, '{\"ram\":\"\",\"rom\":\"\",\"battery\":\"\",\"processor\":\"\"}', '[]', 150, 'Mobile', 'Screenshot 2025-04-01 233206.png', '2025-05-14 11:52:33', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"Brand\":\"\",\"RAM\":\"\",\"Storage\":\"\",\"AutoDescription\":\"\"}', '[]', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referrer_id` int(11) DEFAULT NULL,
  `referred_user_id` int(11) DEFAULT NULL,
  `reward` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','credited') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refund_requests`
--

CREATE TABLE `refund_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `type` varchar(20) DEFAULT 'refund'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refund_requests`
--

INSERT INTO `refund_requests` (`id`, `user_id`, `order_id`, `product_id`, `amount`, `reason`, `status`, `created_at`, `updated_at`, `requested_at`, `type`) VALUES
(18, 1, 40, 0, 0.00, 'please', 'approved', '2025-05-18 21:18:42', '2025-05-18 21:19:03', '2025-05-18 21:18:42', 'refund'),
(19, 11, 66, 0, 0.00, 'a', '', '2025-05-28 14:06:01', NULL, '2025-05-28 14:06:01', 'cancel'),
(20, 11, 65, 0, 0.00, 'a', '', '2025-05-28 14:06:38', NULL, '2025-05-28 14:06:38', 'cancel'),
(21, 1, 102, 0, 0.00, 'g', '', '2025-06-04 14:53:07', NULL, '2025-06-04 14:53:07', 'cancel'),
(22, 1, 100, 0, 0.00, 'please return my money', '', '2025-06-04 14:58:46', NULL, '2025-06-04 14:58:46', 'cancel'),
(23, 1, 101, 0, 0.00, 'hhh', '', '2025-06-04 15:04:51', NULL, '2025-06-04 15:04:51', 'cancel'),
(24, 1, 98, 0, 699.00, 'bb', '', '2025-06-04 15:52:03', NULL, '2025-06-04 15:52:03', 'cancel'),
(25, 1, 103, 0, 30500.00, 'cancle kar', '', '2025-06-04 15:55:47', NULL, '2025-06-04 15:55:47', 'cancel'),
(26, 1, 108, 0, 30500.00, 'hiii', '', '2025-06-04 17:16:27', NULL, '2025-06-04 17:16:27', 'cancel'),
(27, 1, 107, 0, 30500.00, 'h', '', '2025-06-04 17:17:25', NULL, '2025-06-04 17:17:25', 'cancel'),
(28, 1, 109, 0, 199.00, 'd', '', '2025-06-04 23:36:52', NULL, '2025-06-04 23:36:52', 'cancel');

-- --------------------------------------------------------

--
-- Table structure for table `support_replies`
--

CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply_message` text NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_replies`
--

INSERT INTO `support_replies` (`id`, `ticket_id`, `user_id`, `message`, `created_at`, `reply_message`, `admin_id`) VALUES
(34, 5, NULL, '', '2025-04-10 16:43:35', 'Your order is still in process ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','closed','pending') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `file_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_reply` text DEFAULT NULL,
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `subject`, `message`, `status`, `created_at`, `priority`, `file_path`, `updated_at`, `admin_reply`, `reply`) VALUES
(5, 1, 'Order Issue', 'Order ID: #39', 'pending', '2025-04-10 16:41:17', 'medium', 'uploads/1744303277_Screenshot 2025-04-10 221051.png', '2025-04-10 16:41:17', NULL, NULL),
(6, 1, 'Product Not Delivered', 'hhhhhhhhhhhhh', 'pending', '2025-05-02 17:06:24', 'high', 'uploads/1746205584_google_pay.png', '2025-05-02 17:06:24', NULL, NULL),
(7, 17, 'Product Not Delivered', 'kya maja aa raha hai bhai', 'pending', '2025-05-30 06:44:20', 'high', 'uploads/1748587460_google_pay.png', '2025-05-30 06:44:20', NULL, NULL),
(8, 17, 'Product Not Delivered', 'kya maja aa raha hai bhai', 'pending', '2025-05-30 06:49:15', 'high', 'uploads/1748587755_google_pay.png', '2025-05-30 06:49:15', NULL, NULL),
(9, 17, 'Order Issue', 'dwah', 'pending', '2025-05-30 06:50:15', 'medium', 'uploads/1748587815_Screenshot 2024-08-03 203538.png', '2025-05-30 06:50:15', NULL, NULL),
(10, 1, 'Order Issue', 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'pending', '2025-05-30 16:03:28', 'medium', '', '2025-05-30 16:03:28', NULL, NULL),
(11, 1, 'Order Issue', 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'pending', '2025-05-30 16:07:02', 'medium', '', '2025-05-30 16:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `reply` text NOT NULL,
  `replied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mobile` varchar(15) NOT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `is_banned` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `reset_otp_expiry` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_active` datetime DEFAULT current_timestamp(),
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` varchar(20) DEFAULT NULL,
  `referral_earned` tinyint(1) DEFAULT 0,
  `wallet_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `profile_picture`, `created_at`, `mobile`, `status`, `is_banned`, `reset_token`, `reset_token_expiry`, `reset_otp`, `reset_otp_expiry`, `last_seen`, `last_active`, `referral_code`, `referred_by`, `referral_earned`, `wallet_balance`) VALUES
(1, 'Md Altamas', 'angryffgaming99@gmail.com', '910222848', '$2y$10$/v3rPG6HUIVsBWECMd4LwOLSYNCke7fRHJzkWGMDESKcUEPbh5nE6', NULL, '2025-03-25 10:00:50', '9102228481', 'active', 0, NULL, NULL, NULL, NULL, '2025-06-05 09:11:28', '2025-06-04 15:52:39', 'ALTAMAS5678', NULL, 100, 61020.00),
(6, 'altamas', 'angryffgaming8@gmail.com', '', '$2y$10$jq6wvZbvAEv73ZHu4e1qg.p1lK/PaVdND9nQzyLoxSZfRM9sQsv2u', NULL, '2025-03-29 10:25:53', '9534427415', 'active', 0, 'eda2a6d609aadd5e241ee2ef12ea820244282e7c4044cb1bbebf226aff5a115adb20d676c484f8d9999213b8fe0549fbccf0', '2025-03-29 13:07:42', NULL, NULL, '2025-04-07 19:44:37', '2025-04-07 19:49:57', NULL, NULL, 0, 0.00),
(7, 'Md Altamas', 'angryffgaming888@gmail.com', '9534427814', '$2y$10$1KvdYN4g9bjf2yiwa9ZhLub8E3VSfCrTVABa28rrDF1NEi25iC9Mu', NULL, '2025-03-29 10:47:36', '9334295859', 'active', 0, '81a816c910ec01ab17467a155c356aec5d41a5e0117fe46f2a52885ffd8c66f864d656dc2f3337fba86be84e1b940aa38157', '2025-03-29 13:06:43', NULL, NULL, '2025-05-23 19:36:54', '2025-04-08 02:33:07', NULL, NULL, 0, 0.00),
(9, 'Shaif', 'angryk199@gmail.com', '7676164123', '$2y$10$tAJQAxztYvyK9y.gBkgFneNn20xSHYrunwJY5JtZ8GXfgx0vy2AhK', NULL, '2025-04-04 18:33:45', '7676164123', 'active', 0, NULL, NULL, NULL, NULL, '2025-04-07 19:44:37', '2025-04-07 19:49:57', NULL, NULL, 0, 0.00),
(11, 'Shaif Ali', 'angryk033@gmail.com', '09102228481', '$2y$10$umXChToYmtgdR7fXF.mBb.L2DIfwYZ1wytpOmSj4tlB7cUYPBW30.', NULL, '2025-04-10 20:55:49', '8088284843', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-28 14:08:14', '2025-04-11 02:25:49', NULL, NULL, 0, 0.00),
(12, 'Abdul kaish', 'abdulkaishmd1@gmail.com', NULL, '$2y$10$qzHz7hqUxXa.qs3gPWSGW.chuzccFTsxSQAak7x6O5TbmSDL5b3C6', NULL, '2025-05-29 18:32:34', '9162078678', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 00:02:34', '2025-05-30 00:02:34', NULL, NULL, 0, 0.00),
(13, 'Abdul kaish', 'abdulkaishmd71@gmail.com', NULL, '$2y$10$crlvQb9mSq9qGW9a16a1f.BhofhY/2V9eulQV6tM9nyhR5KasVaLS', NULL, '2025-05-30 05:17:37', '9534427818', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 10:47:37', '2025-05-30 10:47:37', NULL, NULL, 0, 0.00),
(15, 'king', 'angry910223@gmail.com', NULL, '$2y$10$EpkAedPAwUkiDacJ0txpJu7ajjaslQd9TXmJI2/OUL9k8YT.Q0nty', NULL, '2025-05-30 05:40:02', '5858969625', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 11:10:02', '2025-05-30 11:10:02', NULL, NULL, 0, 0.00),
(16, 'Abdul Kaish', 'abdulkaishmd9@gmail.com', NULL, '$2y$10$ldXRvKqmU4Xzl0GeckKXROJg9UPQLE2rSvpay69EmyLb8rhbF98ey', NULL, '2025-05-30 05:45:34', '1231231230', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 11:15:34', '2025-05-30 11:15:34', NULL, NULL, 0, 0.00),
(17, 'Abdul Kaish', 'abdulkaishmd8@gmail.com', NULL, '$2y$10$s3rbMR.w1f.YbAGdoVTg2uRUb14/2N4blU8lORh2h6bIYOOTwkOFm', NULL, '2025-05-30 05:47:11', '7569459865', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 12:20:00', '2025-05-30 12:20:00', NULL, NULL, 0, 0.00),
(18, 'Md Abdulkaish', 'angry910225@gmail.com', NULL, '$2y$10$jnLvA/UY/DxPA/vdhCL1h.Wg./Ueor6rXCroh8Pamjc9HTZRZ.JvS', NULL, '2025-05-30 06:01:40', '1643526890', 'active', 0, NULL, NULL, NULL, NULL, '2025-05-30 11:31:40', '2025-05-30 11:31:40', NULL, NULL, 0, 0.00),
(21, 'Md Altamas', 'angry910222@gmail.com', NULL, '$2y$10$EJ5fVySaRBlJI09UR8aXm.k/Bp2tYjfMrNTezP9Pb0R5Th4OVmeXm', NULL, '2025-06-01 17:32:00', '3333333333', 'active', 0, NULL, NULL, NULL, NULL, '2025-06-01 23:16:18', '2025-06-01 23:02:00', '780a6f72', '1', 0, 0.00),
(22, 'Ataullah', 'ataullah295859@gmail.com', NULL, '$2y$10$G/iGM42DpW7Z1NmBD0FE..Nbidt2CfNg5kupMpKnffTE6iAdT0LK.', NULL, '2025-06-03 13:12:23', '7848965896', 'active', 0, NULL, NULL, NULL, NULL, '2025-06-03 18:53:10', '2025-06-03 18:44:58', '0b327e2a', '1', 0, 0.00),
(23, 'Md Asjad', 'sibgatullah306@gmail.com', NULL, '$2y$10$IEnDu9rTDKVQDTH3cCkQkeXunN8PbKcnzvqp1yxvtLT4IRsQaTo5e', NULL, '2025-06-05 03:41:28', '7258955435', 'active', 0, NULL, NULL, NULL, NULL, '2025-06-05 09:11:28', '2025-06-05 09:11:28', '8ee880f7', '1', 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `source` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `type`, `amount`, `description`, `created_at`, `source`, `order_id`) VALUES
(1, 1, 'credit', 100.00, NULL, '2025-06-04 09:13:07', 'admin', NULL),
(2, 1, 'credit', 50.00, NULL, '2025-06-04 09:13:07', 'referral', NULL),
(3, 1, 'debit', 70.00, NULL, '2025-06-04 09:13:07', 'order', NULL),
(4, 1, 'credit', 30.00, NULL, '2025-06-04 09:13:07', 'refund', NULL),
(5, 1, 'debit', 20.00, NULL, '2025-06-04 09:13:07', 'order', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(27, 1, 32, '2025-04-10 16:48:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `my_orders`
--
ALTER TABLE `my_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referred_user_id` (`referred_user_id`);

--
-- Indexes for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `mobile_2` (`mobile`),
  ADD UNIQUE KEY `mobile_3` (`mobile`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `deleted_messages`
--
ALTER TABLE `deleted_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `my_orders`
--
ALTER TABLE `my_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refund_requests`
--
ALTER TABLE `refund_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `my_orders`
--
ALTER TABLE `my_orders`
  ADD CONSTRAINT `my_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `my_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `my_orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referrals_ibfk_2` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refund_requests`
--
ALTER TABLE `refund_requests`
  ADD CONSTRAINT `refund_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `refund_requests_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `my_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD CONSTRAINT `support_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD CONSTRAINT `ticket_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_replies_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
