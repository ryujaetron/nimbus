-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2026 at 08:21 AM
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
-- Database: `nimbusdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(3, 5, 3, 1, '2026-03-26 13:30:46'),
(4, 5, 4, 1, '2026-03-26 13:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(120) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `url`, `created_at`) VALUES
(1, 'Motorcycle Parts', 'motorcycle-parts', '2026-03-24 07:53:58'),
(2, 'Riding Gear', 'riding-gear', '2026-03-24 07:53:58'),
(3, 'Accessories', 'accessories', '2026-03-24 07:53:58'),
(4, 'Maintenance', 'maintenance', '2026-03-25 01:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_uses` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order`, `max_uses`, `used_count`, `expires_at`, `is_active`, `created_at`) VALUES
(1, 'RIDE10', 'percent', 10.00, 1000.00, 100, 0, NULL, 1, '2026-03-24 07:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `parent_order_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'cod',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `vendor_id`, `name`, `url`, `description`, `price`, `sale_price`, `stock`, `image`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Full Face Helmet', 'full-face-helmet', 'DOT approved full face motorcycle helmet with visor.', 3500.00, 2999.00, 40, NULL, 1, 1, '2026-03-24 07:53:58', '2026-03-27 08:27:18'),
(2, 3, 1, 'Motorcycle Phone Mount', 'motorcycle-phone-mount', 'Handlebar phone holder with anti-vibration design.', 799.00, NULL, 60, NULL, 0, 1, '2026-03-24 07:53:58', '2026-03-27 08:27:18'),
(3, 2, 1, 'Riding Gloves', 'riding-gloves', 'Breathable anti-slip motorcycle riding gloves.', 499.00, NULL, 80, NULL, 1, 1, '2026-03-24 07:53:58', '2026-03-27 08:27:18'),
(4, 1, 1, 'LED Headlight Bulb', 'led-headlight-bulb', 'High brightness LED bulb for motorcycles.', 899.00, NULL, 50, NULL, 0, 1, '2026-03-24 07:53:58', '2026-03-27 08:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','vendor') DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `phone`, `created_at`) VALUES
(1, 'Admin', 'User', 'admin@nimbus.com', '$2y$12$hash', 'admin', NULL, '2026-03-24 07:53:58'),
(2, 'Juan', 'dela Cruz', 'juan@email.com', '$2y$12$hash', 'customer', NULL, '2026-03-24 07:53:58'),
(3, 'Renci', 'Silot', 'silotjannrenci14@gmail.com', '$2y$10$kIExInD3hhkWf4vimst09OcLxJ.VB/cO9njX5HQB/33MwVKONt8m2', 'customer', NULL, '2026-03-24 16:19:18'),
(4, 'Deivan', 'Sanborn', 'deivan@gmail.com', '$2y$10$TEBNZqR9mnVhi9xtOo0l5eyaaBgMmBhz7Kq7GlfP2mZmBYAwXGAaW', 'customer', NULL, '2026-03-24 16:25:01'),
(5, 'user', 'user', 'user@user.com', '$2y$10$xr/xzjCPhbjaCQbXJRjqee8NBg2uPVkkos32Vqh5s0HzPtrEzcOm6', 'customer', NULL, '2026-03-25 10:03:55'),
(6, 'Deivan', 'Sanborn', 'deivansanborn@gmail.com', '$2y$10$K5/O0ZG2tzQLaBOhGrAwteHeyTBspXaq3c4DAseAeTzH7u6iPcKnW', 'customer', NULL, '2026-03-27 14:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(150) NOT NULL,
  `store_url` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `store_address` varchar(255) DEFAULT NULL,
  `valid_id_image` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 5, 2, '2026-03-26 13:30:51'),
(3, 3, 2, '2026-03-26 13:36:54'),
(4, 3, 3, '2026-03-26 13:37:02'),
(5, 3, 4, '2026-03-26 13:37:04'),
(6, 3, 1, '2026-03-26 13:37:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_user` (`user_id`),
  ADD KEY `order_coupon` (`coupon_id`),
  ADD KEY `parent_order_id` (`parent_order_id`),
  ADD KEY `order_vendor` (`vendor_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oi_order` (`order_id`),
  ADD KEY `oi_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_user` (`user_id`),
  ADD KEY `review_product` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `store_url` (`store_url`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_wish` (`user_id`,`product_id`),
  ADD KEY `wishlist_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `order_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_parent` FOREIGN KEY (`parent_order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `prod_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prod_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
