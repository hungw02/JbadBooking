-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2025 at 05:08 PM
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
-- Database: `jbad_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `maintenance_start_date` datetime DEFAULT NULL,
  `maintenance_end_date` datetime DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `name`, `status`, `maintenance_start_date`, `maintenance_end_date`, `image`, `created_at`, `updated_at`) VALUES
(1, '1', 'available', NULL, NULL, 'image/court/1743501009.png', '2025-03-30 22:47:17', '2025-05-09 09:50:09'),
(2, '2', 'available', NULL, NULL, 'image/court/1743501074.png', '2025-03-30 22:51:14', '2025-03-30 22:51:14'),
(3, '3', 'available', NULL, NULL, 'image/court/1743501084.png', '2025-03-30 22:51:25', '2025-03-30 22:51:25'),
(4, '4', 'available', NULL, NULL, 'image/court/1743501096.png', '2025-03-30 22:51:36', '2025-03-30 22:51:36'),
(5, '5', 'available', NULL, NULL, 'image/court/1743501122.png', '2025-03-30 22:52:02', '2025-03-30 22:52:02'),
(6, '6', 'available', NULL, NULL, 'image/court/1743501141.png', '2025-03-30 22:52:21', '2025-03-30 22:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `court_rates`
--

CREATE TABLE `court_rates` (
  `id` bigint UNSIGNED NOT NULL,
  `day_of_week` tinyint NOT NULL COMMENT '2-8: Thứ 2-Chủ nhật',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price_per_hour` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `court_rates`
--

INSERT INTO `court_rates` (`id`, `day_of_week`, `start_time`, `end_time`, `price_per_hour`, `created_at`, `updated_at`) VALUES
(1, 2, '05:00:00', '09:59:00', 60000, '2025-03-09 06:42:28', '2025-03-09 06:42:28'),
(2, 3, '05:00:00', '09:59:00', 60000, '2025-03-09 06:42:28', '2025-03-09 06:42:28'),
(3, 4, '05:00:00', '09:59:00', 60000, '2025-03-09 06:42:28', '2025-03-09 06:42:28'),
(4, 5, '05:00:00', '09:59:00', 60000, '2025-03-09 06:42:28', '2025-03-09 06:42:28'),
(5, 6, '05:00:00', '09:59:00', 60000, '2025-03-09 06:42:28', '2025-03-09 06:42:28'),
(6, 2, '10:00:00', '13:59:00', 50000, '2025-03-09 06:43:10', '2025-03-09 06:43:10'),
(7, 3, '10:00:00', '13:59:00', 50000, '2025-03-09 06:43:10', '2025-03-09 06:43:10'),
(8, 4, '10:00:00', '13:59:00', 50000, '2025-03-09 06:43:10', '2025-03-09 06:43:10'),
(9, 5, '10:00:00', '13:59:00', 50000, '2025-03-09 06:43:10', '2025-03-09 06:43:10'),
(10, 6, '10:00:00', '13:59:00', 50000, '2025-03-09 06:43:10', '2025-03-09 06:43:10'),
(11, 2, '14:00:00', '17:59:00', 70000, '2025-03-09 06:44:15', '2025-03-09 06:44:15'),
(12, 3, '14:00:00', '17:59:00', 70000, '2025-03-09 06:44:15', '2025-03-09 06:44:15'),
(13, 4, '14:00:00', '17:59:00', 70000, '2025-03-09 06:44:15', '2025-03-09 06:44:15'),
(14, 5, '14:00:00', '17:59:00', 70000, '2025-03-09 06:44:15', '2025-03-09 06:44:15'),
(15, 6, '14:00:00', '17:59:00', 70000, '2025-03-09 06:44:15', '2025-03-09 06:44:15'),
(16, 2, '18:00:00', '23:59:00', 80000, '2025-03-09 06:45:33', '2025-03-09 06:45:33'),
(17, 3, '18:00:00', '23:59:00', 80000, '2025-03-09 06:45:33', '2025-03-09 06:45:33'),
(18, 4, '18:00:00', '23:59:00', 80000, '2025-03-09 06:45:33', '2025-03-09 06:45:33'),
(19, 5, '18:00:00', '23:59:00', 80000, '2025-03-09 06:45:33', '2025-03-09 06:45:33'),
(20, 6, '18:00:00', '23:59:00', 80000, '2025-03-09 06:45:33', '2025-03-09 06:45:33'),
(21, 7, '05:00:00', '09:59:00', 70000, '2025-03-09 06:46:46', '2025-03-09 06:46:46'),
(22, 8, '05:00:00', '09:59:00', 70000, '2025-03-09 06:46:46', '2025-03-09 06:46:46'),
(23, 7, '10:00:00', '13:59:00', 60000, '2025-03-09 06:47:11', '2025-03-09 06:47:11'),
(24, 8, '10:00:00', '13:59:00', 60000, '2025-03-09 06:47:11', '2025-03-09 06:47:11'),
(25, 7, '14:00:00', '17:59:00', 80000, '2025-03-09 06:47:57', '2025-03-09 06:47:57'),
(26, 8, '14:00:00', '17:59:00', 80000, '2025-03-09 06:47:57', '2025-03-09 06:47:57'),
(27, 7, '18:00:00', '23:59:00', 90000, '2025-03-09 06:48:44', '2025-03-09 06:48:44'),
(28, 8, '18:00:00', '23:59:00', 90000, '2025-03-10 02:57:28', '2025-03-10 03:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `imports`
--

CREATE TABLE `imports` (
  `id` bigint UNSIGNED NOT NULL,
  `owner_id` bigint UNSIGNED NOT NULL,
  `workshop_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_items`
--

CREATE TABLE `import_items` (
  `id` bigint UNSIGNED NOT NULL,
  `import_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `import_price` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_01_01_000001_create_users_table', 1),
(2, '2025_01_01_000002_create_courts_table', 1),
(3, '2025_01_01_000003_create_court_rates_table', 1),
(4, '2025_01_01_000004_create_products_table', 1),
(5, '2025_01_01_000005_create_imports_table', 1),
(6, '2025_01_01_000006_create_import_items_table', 1),
(7, '2025_01_01_000007_create_storages_table', 1),
(8, '2025_01_01_000008_create_promotions_table', 1),
(9, '2025_01_01_000009_create_single_bookings_table', 1),
(10, '2025_01_01_000010_create_subscription_bookings_table', 1),
(11, '2025_01_01_000011_create_refunds_table', 1),
(12, '2025_01_01_000012_create_teammate_finder_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_price` bigint UNSIGNED NOT NULL,
  `selling_price` bigint UNSIGNED NOT NULL,
  `sale` int NOT NULL DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `status` enum('available','out_of_stock') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `type` enum('sale','rent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sale',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `import_price`, `selling_price`, `sale`, `image`, `quantity`, `status`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Vợt cầu lông Yonex Astrox 100ZZ Kurenai', 20000, 20000, 0, 'image/product/1741629063.png', 4, 'available', 'rent', '2025-03-08 06:22:56', '2025-05-22 17:08:14'),
(2, 'Dây cước căng vợt Yonex BG 65 TITANIUM', 150000, 150000, 0, 'image/product/1741629466.png', 29, 'available', 'sale', '2025-03-08 06:24:00', '2025-05-22 17:05:54'),
(3, 'Nước bù khoáng Revive muối khoáng chai 500ml', 5000, 10000, 0, 'image/product/1741629409.png', 37, 'available', 'sale', '2025-03-08 06:26:13', '2025-05-22 17:06:11'),
(4, 'Quấn cán Yonex AC 149EX', 5000, 10000, 0, 'image/product/1741629643.png', 45, 'available', 'sale', '2025-03-08 06:26:56', '2025-05-22 17:06:56'),
(5, 'Balo cầu lông chống nước', 300000, 350000, 0, 'image/product/1741692574.png', 49, 'available', 'sale', '2025-03-08 06:28:55', '2025-05-22 17:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percent` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `booking_type` enum('all','single','subscription') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `description`, `image`, `discount_percent`, `start_date`, `end_date`, `status`, `booking_type`, `created_at`, `updated_at`) VALUES
(1, 'Khai trương sân cầu', 'Tưng bừng khai trương, ưu đãi ngập tràn', 'image/promotion/1743760130.jpeg', 10, '2025-04-04', NULL, 'active', 'single', '2025-04-02 22:48:50', '2025-04-02 23:57:10'),
(2, 'Đặt sân định kỳ', 'Đặt sân định kỳ để nhận ưu đãi giảm giá lên đến 10%. Đặc biệt ưu đãi cho khách hàng thân thiết.', 'image/promotion/1743760231.jpeg', 20, '2025-04-04', NULL, 'active', 'subscription', '2025-04-02 22:50:31', '2025-04-17 18:47:10'),
(3, 'Giảm giá giờ chơi hàng tuần', 'Giá thuê khung giờ 10h-14h hàng ngày chỉ với 50k/h', 'image/promotion/1743760351.jpeg', 5, '2025-04-04', NULL, 'active', 'all', '2025-04-02 22:52:31', '2025-04-24 02:30:45'),
(4, 'Đặt sân trực tuyến dễ dàng', 'Chỉ với vài thao tác đơn giản, bạn có thể đặt sân cầu lông mọi lúc mọi nơi qua hệ thống.', 'image/promotion/1743760412.jpeg', 0, '2025-04-04', NULL, 'active', 'all', '2025-04-02 22:53:32', '2025-04-02 22:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint UNSIGNED NOT NULL,
  `bookable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookable_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `refund_amount` bigint UNSIGNED NOT NULL,
  `refund_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `single_bookings`
--

CREATE TABLE `single_bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `court_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `payment_type` enum('deposit','full') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` enum('vnpay','wallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED DEFAULT NULL,
  `discount_percent` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancel_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `single_bookings`
--

INSERT INTO `single_bookings` (`id`, `court_id`, `customer_id`, `start_time`, `end_time`, `payment_type`, `payment_method`, `total_price`, `promotion_id`, `discount_percent`, `status`, `cancel_time`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2025-04-22 06:00:00', '2025-04-22 08:00:00', 'full', 'wallet', 108000, 1, 10, 'completed', NULL, '2025-04-20 16:21:49', '2025-05-22 17:00:48'),
(2, 2, 2, '2025-04-23 19:00:00', '2025-04-23 21:00:00', 'full', 'wallet', 162000, 1, 10, 'completed', NULL, '2025-04-20 16:23:10', '2025-05-22 17:00:45'),
(3, 2, 2, '2025-04-24 19:00:00', '2025-04-24 21:00:00', 'full', 'wallet', 162000, 1, 10, 'completed', NULL, '2025-04-20 16:24:14', '2025-05-22 17:00:42'),
(4, 1, 2, '2025-04-25 06:00:00', '2025-04-25 08:00:00', 'full', 'wallet', 108000, 1, 10, 'completed', NULL, '2025-04-20 16:21:49', '2025-05-22 17:00:26'),
(5, 3, 3, '2025-04-26 19:00:00', '2025-04-26 22:00:00', 'full', 'vnpay', 243000, 1, 10, 'completed', NULL, '2025-04-22 16:41:22', '2025-05-22 17:00:39'),
(6, 3, 3, '2025-04-28 19:30:00', '2025-04-28 22:00:00', 'full', 'vnpay', 180000, 1, 10, 'completed', NULL, '2025-04-22 16:46:22', '2025-05-22 17:00:36'),
(7, 3, 3, '2025-04-29 06:30:00', '2025-04-29 08:30:00', 'full', 'vnpay', 108000, 1, 10, 'completed', NULL, '2025-04-22 16:48:37', '2025-05-22 17:00:32'),
(8, 6, 3, '2025-04-30 18:00:00', '2025-04-30 20:00:00', 'deposit', 'wallet', 144000, 1, 10, 'completed', NULL, '2025-04-22 16:51:40', '2025-05-22 17:00:29'),
(9, 3, 4, '2025-05-23 05:00:00', '2025-05-23 07:00:00', 'full', 'vnpay', 108000, 1, 10, 'confirmed', NULL, '2025-05-22 16:53:53', '2025-05-22 16:54:13'),
(10, 4, 4, '2025-05-24 08:00:00', '2025-05-24 10:00:00', 'full', 'vnpay', 126000, 1, 10, 'confirmed', NULL, '2025-05-22 16:55:05', '2025-05-22 16:55:24');

-- --------------------------------------------------------

--
-- Table structure for table `storages`
--

CREATE TABLE `storages` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `transaction_type` enum('sale','rent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rent',
  `status` enum('returned','not_returned','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storages`
--

INSERT INTO `storages` (`id`, `product_id`, `product_name`, `quantity`, `total_price`, `transaction_type`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, '2', 'Dây cước căng vợt Yonex BG 65 TITANIUM', '1', 150000.00, 'sale', 'completed', '11kg, 2 nút, màu trắng', '2025-05-22 17:05:54', '2025-05-22 17:06:42'),
(2, '3', 'Nước bù khoáng Revive muối khoáng chai 500ml', '6', 60000.00, 'sale', 'completed', 'Sân 2', '2025-05-22 17:06:11', '2025-05-22 17:06:21'),
(3, '4', 'Quấn cán Yonex AC 149EX', '5', 50000.00, 'sale', 'completed', NULL, '2025-05-22 17:06:56', '2025-05-22 17:06:56'),
(4, '5', 'Balo cầu lông chống nước', '1', 350000.00, 'sale', 'completed', NULL, '2025-05-22 17:07:06', '2025-05-22 17:07:06'),
(5, '1', 'Vợt cầu lông Yonex Astrox 100ZZ Kurenai', '1', 20000.00, 'rent', 'returned', NULL, '2025-05-22 17:07:15', '2025-05-22 17:07:22'),
(6, '1', 'Vợt cầu lông Yonex Astrox 100ZZ Kurenai', '2', 40000.00, 'rent', 'returned', NULL, '2025-05-22 17:07:51', '2025-05-22 17:07:56'),
(7, '1', 'Vợt cầu lông Yonex Astrox 100ZZ Kurenai', '2', 40000.00, 'rent', 'returned', NULL, '2025-05-22 17:08:10', '2025-05-22 17:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_bookings`
--

CREATE TABLE `subscription_bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `court_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `day_of_week` tinyint NOT NULL COMMENT '2-8: Thứ 2-Chủ nhật',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `payment_type` enum('deposit','full') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` enum('vnpay','wallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED DEFAULT NULL,
  `discount_percent` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancel_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_bookings`
--

INSERT INTO `subscription_bookings` (`id`, `court_id`, `customer_id`, `start_date`, `end_date`, `day_of_week`, `start_time`, `end_time`, `payment_type`, `payment_method`, `total_price`, `promotion_id`, `discount_percent`, `status`, `cancel_time`, `created_at`, `updated_at`) VALUES
(1, 5, 4, '2025-05-22', '2025-06-19', 2, '20:00:00', '22:00:00', 'full', 'vnpay', 512000, 2, 20, 'confirmed', NULL, '2025-05-22 16:56:17', '2025-05-22 16:56:40'),
(2, 4, 5, '2025-05-22', '2025-06-19', 2, '19:00:00', '21:00:00', 'full', 'vnpay', 512000, 2, 20, 'confirmed', NULL, '2025-05-22 16:58:50', '2025-05-22 16:59:11');

-- --------------------------------------------------------

--
-- Table structure for table `teammate_finder`
--

CREATE TABLE `teammate_finder` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `skill_level` enum('yếu','trung bình yếu','trung bình','trung bình khá','khá') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expectations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `play_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teammate_finder`
--

INSERT INTO `teammate_finder` (`id`, `user_id`, `full_name`, `skill_level`, `contact_info`, `expectations`, `play_time`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'Nguyễn Văn A', 'yếu', 'https://www.facebook.com/nguyenvana', 'Nam trung bình yếu', '7r-9r tối 4, 5, 6', 1, '2025-04-24 10:30:02', '2025-04-24 10:30:02'),
(2, 3, 'Nguyễn Văn B', 'trung bình yếu', 'https://www.facebook.com/nguyenvanb', 'Cần tìm đôi nam nữ trung bình', '7r-9r tối 4', 1, '2025-04-24 10:31:09', '2025-04-24 10:31:09'),
(3, 4, 'Nguyễn Văn C', 'trung bình', 'https://www.facebook.com/nguyenvanc', 'Tìm 2 nam trung bình', '6h-8h tối 3, 5, 7', 1, '2025-04-24 10:32:41', '2025-04-24 10:33:06'),
(4, 5, 'Nguyễn Văn D', 'trung bình khá', 'https://www.facebook.com/nguyenvand', 'Cần 2 nữ trung bình, trung bình yếu', '8h-10h tối 4, 5, 6', 1, '2025-04-24 10:34:27', '2025-04-24 10:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('customer','owner') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `wallets` bigint UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `point` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `email`, `phone`, `role`, `wallets`, `status`, `point`, `created_at`, `updated_at`) VALUES
(1, 'Hoàng Gia Bảo', 'admin', '$2y$12$mYdkt7D28WOxuDwdBOGYzeIK7kJ/DpoWEpItJaKdF1pnaPrcyo9a6', 'admin@gmail.com', '0123456789', 'owner', 0, 'active', 0, '2025-03-08 06:14:40', '2025-05-10 07:59:38'),
(2, 'Nguyễn Văn A', 'user1', '$2y$12$yQ5YSRlD.whxN9ohT7ij5eC0FjVoWiYcHI.VOSXof9XKqo9Mee4Py', 'user1@gmail.com', '0123456788', 'customer', 999999, 'active', 4, '2025-03-08 06:16:17', '2025-05-22 16:24:14'),
(3, 'Nguyễn Văn B', 'user2', '$2y$12$T5sEoeUhwQJfVJj3jpwrLOdruU3c9qPt6NhhcDwADBhj3CZfCPezi', 'user2@gmail.com', '0123456787', 'customer', 28000, 'active', 4, '2025-04-22 19:12:24', '2025-05-22 16:51:40'),
(4, 'Nguyễn Văn C', 'user3', '$2y$12$CgqxYRh8VguYTX4Qb7lXAueu2H59hCAIvNdmJONxNXeOl82r6afni', 'user3@gmail.com', '0123456786', 'customer', 100000, 'active', 7, '2025-04-22 22:36:57', '2025-05-22 16:56:40'),
(5, 'Nguyễn Văn D', 'user4', '$2y$12$Wog79Q6HSzaYb/iB0a5vEeBlQFiS6XutukzRhjSBD7q6XgcVOlOv2', 'user4@gmail.com', '0123456785', 'customer', 100000, 'active', 5, '2025-04-22 22:43:49', '2025-05-22 16:59:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_rates`
--
ALTER TABLE `court_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imports_owner_id_foreign` (`owner_id`);

--
-- Indexes for table `import_items`
--
ALTER TABLE `import_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_items_import_id_foreign` (`import_id`),
  ADD KEY `import_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refunds_bookable_type_bookable_id_index` (`bookable_type`,`bookable_id`),
  ADD KEY `refunds_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `single_bookings`
--
ALTER TABLE `single_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `single_bookings_court_id_foreign` (`court_id`),
  ADD KEY `single_bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `single_bookings_promotion_id_foreign` (`promotion_id`);

--
-- Indexes for table `storages`
--
ALTER TABLE `storages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_bookings`
--
ALTER TABLE `subscription_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_bookings_court_id_foreign` (`court_id`),
  ADD KEY `subscription_bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `subscription_bookings_promotion_id_foreign` (`promotion_id`);

--
-- Indexes for table `teammate_finder`
--
ALTER TABLE `teammate_finder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teammate_finder_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `court_rates`
--
ALTER TABLE `court_rates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `imports`
--
ALTER TABLE `imports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_items`
--
ALTER TABLE `import_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `single_bookings`
--
ALTER TABLE `single_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `storages`
--
ALTER TABLE `storages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscription_bookings`
--
ALTER TABLE `subscription_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teammate_finder`
--
ALTER TABLE `teammate_finder`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `imports`
--
ALTER TABLE `imports`
  ADD CONSTRAINT `imports_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `import_items`
--
ALTER TABLE `import_items`
  ADD CONSTRAINT `import_items_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `single_bookings`
--
ALTER TABLE `single_bookings`
  ADD CONSTRAINT `single_bookings_court_id_foreign` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `single_bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `single_bookings_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscription_bookings`
--
ALTER TABLE `subscription_bookings`
  ADD CONSTRAINT `subscription_bookings_court_id_foreign` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_bookings_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teammate_finder`
--
ALTER TABLE `teammate_finder`
  ADD CONSTRAINT `teammate_finder_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
