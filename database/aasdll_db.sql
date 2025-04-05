-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 06:02 AM
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
-- Database: `artisell_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(2, 1, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'
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
  `category` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `city` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`, `created_at`, `city`) VALUES
(1, 'Handmade Basket', 'A beautiful handmade basket.', 25.00, 'minglanilla', 'https://api.deepai.org/job-view-file/f11db409-4cc7-4278-acb5-97edacd0aa80/outputs/output.jpg', '2025-03-19 13:07:46', NULL),
(2, 'Traditional Mat', 'A traditional mat made from local materials.', 15.00, 'minglanilla', 'https://api.deepai.org/job-view-file/db9f0724-79a5-4c98-acce-904da158a331/outputs/output.jpg', '2025-03-19 13:07:46', NULL),
(3, 'Artisan Jewelry', 'Unique artisan jewelry.', 30.00, 'minglanilla', 'https://api.deepai.org/job-view-file/a825647d-4b21-4336-80a4-a0d9adbb5c0b/outputs/output.jpg', '2025-03-19 13:07:46', NULL),
(4, 'Minglanilla Handwoven Bag', 'A stylish handwoven bag made from local materials.', 35.00, 'minglanilla', 'https://api.deepai.org/job-view-file/af4bb4c7-35dc-446f-acdc-88ba9182e038/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(5, 'Minglanilla Rattan Chair', 'A comfortable rattan chair perfect for your home.', 120.00, 'minglanilla', 'https://api.deepai.org/job-view-file/1af94d29-f7c2-4787-9922-866dd9bdc260/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(6, 'Minglanilla Decorative Plate', 'Beautifully crafted decorative plate for your dining table.', 20.00, 'minglanilla', 'https://api.deepai.org/job-view-file/8048bc00-2b12-43bf-ac86-510b4a348444/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(7, 'Aloquinsan Bamboo Utensils', 'Eco-friendly bamboo utensils for your kitchen.', 15.00, 'aloquinsan', 'https://api.deepai.org/job-view-file/94e91ab0-7317-4dac-91be-366774572018/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(8, 'Aloquinsan Handcrafted Soap', 'Natural handcrafted soap made from local ingredients.', 8.00, 'aloquinsan', 'https://api.deepai.org/job-view-file/a271d89e-68dc-4acd-9008-8ccff17928ca/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(9, 'Aloquinsan Woven Basket', 'A traditional woven basket for storage or decoration.', 25.00, 'aloquinsan', 'https://api.deepai.org/job-view-file/d55f162f-3d70-4499-b659-dabf322b517c/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(10, 'Catmon Clay Pot', 'Handmade clay pot for cooking and serving.', 30.00, 'catmon', 'path/to/catmon_pot.jpg', '2025-03-19 13:12:32', NULL),
(11, 'Catmon Coconut Candies', 'Delicious coconut candies made from fresh coconuts.', 5.00, 'catmon', 'path/to/catmon_candies.jpg', '2025-03-19 13:12:32', NULL),
(12, 'Catmon Traditional Hat', 'A traditional hat made from local materials.', 12.00, 'catmon', 'path/to/catmon_hat.jpg', '2025-03-19 13:12:32', NULL),
(13, 'Dumanjug Torta', 'Famous Dumanjug Torta, a local delicacy.', 10.00, 'dumanjug', 'path/to/dumanjug_torta.jpg', '2025-03-19 13:12:32', NULL),
(14, 'Dumanjug Handwoven Mat', 'A beautiful handwoven mat for your home.', 40.00, 'dumanjug', 'https://api.deepai.org/job-view-file/c2da71fd-498a-481f-98a1-88fea58fd44f/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(15, 'Dumanjug Native Basket', 'A native basket perfect for carrying goods.', 18.00, 'dumanjug', 'https://api.deepai.org/job-view-file/da1ec28f-434b-4666-b28c-37114a15b4af/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(16, 'Santander Fresh Seafood', 'Freshly caught seafood from Santander.', 50.00, 'santander', 'https://api.deepai.org/job-view-file/323db2be-eb6a-417f-aa74-82f7c744fb10/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(17, 'Santander Handcrafted Souvenirs', 'Unique handcrafted souvenirs from Santander.', 15.00, 'santander', 'https://api.deepai.org/job-view-file/3e713f33-e3fa-4b2a-9f66-ef838f8bd9f3/outputs/output.jpg', '2025-03-19 13:12:32', NULL),
(18, 'Santander Local Spices', 'A selection of local spices for your cooking.', 7.00, 'santander', 'path/to/santander_spices.jpg', '2025-03-19 13:12:32', NULL),
(19, 'Alcoy Coconut Jam', 'Delicious coconut jam made from fresh coconuts.', 6.00, 'alcoy', 'path/to/alcoy_jam.jpg', '2025-03-19 13:12:32', NULL),
(20, 'Alcoy Handwoven Bags', 'Stylish handwoven bags made by local artisans.', 30.00, 'alcoy', 'path/to/alcoy_bags.jpg', '2025-03-19 13:12:32', NULL),
(21, 'Alcoy Beach Towels', 'Soft and absorbent beach towels for your trips.', 20.00, 'alcoy', 'path/to/alcoy_towels.jpg', '2025-03-19 13:12:32', NULL),
(22, 'Moalboal Shell Crafts', 'Beautiful shell crafts made by local artisans.', 25.00, 'moalboal', 'path/to/moalboal_shells.jpg', '2025-03-19 13:12:32', NULL),
(23, 'Moalboal Dried Fish', 'Traditional dried fish, a local delicacy.', 12.00, 'moalboal', 'path/to/moalboal_fish.jpg', '2025-03-19 13:12:32', NULL),
(24, 'Moalboal Beach Mats', 'Comfortable mats for your beach outings.', 15.00, 'moalboal', 'path/to/moalboal_mats.jpg', '2025-03-19 13:12:32', NULL),
(25, 'Borbon Takyong', 'Delicious Takyong (land snails) delicacy from Borbon.', 8.00, 'borbon', 'path/to/borbon_takyong.jpg', '2025-03-19 13:12:32', NULL),
(26, 'Borbon Handcrafted Baskets', 'Unique handcrafted baskets from Borbon.', 20.00, 'borbon', 'path/to/borbon_baskets.jpg', '2025-03-19 13:12:32', NULL),
(27, 'Borbon Local Produce', 'Fresh local produce from Borbon farmers.', 10.00, 'borbon', 'path/to/borbon_produce.jpg', '2025-03-19 13:12:32', NULL),
(28, 'Handwoven Basket', 'Beautifully crafted basket made from natural fibers', 0.00, 'crafts', 'images/basket.jpg', '2025-04-02 15:05:27', 'minglanilla'),
(29, 'Honey Cake', 'Delicious homemade cake with natural honey', 0.00, 'delicacies', 'images/honeycake.jpg', '2025-04-02 15:05:27', 'catmon'),
(30, 'Wooden Sculpture', 'Intricate hand-carved wooden sculpture', 0.00, 'crafts', 'images/sculpture.jpg', '2025-04-02 15:05:27', 'moalboal'),
(31, 'Spiced Nuts', 'Roasted nuts with traditional spices', 0.00, 'delicacies', 'images/spicednuts.jpg', '2025-04-02 15:05:27', 'borbon'),
(32, 'Test Product', 'A sample product', 100.00, 'crafts', 'uploads/test.jpg', '2025-04-02 16:25:25', 'cebu'),
(33, 'LAPOK', 'SADAS', 1.03, 'FOODS', 'uploads/67ed65fb89471.png', '2025-04-02 16:29:47', 'BISAGASA');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer','vendor') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'customer', 'customer@gmail.com', '$2y$10$cQOZKMk1zJGmU81.l/roaOaxzjnbjhcey44CewVnJ2jUbBIfS9z5a', 'customer', '2025-03-15 11:41:44'),
(2, 'vendor', 'vendor@gmail.com', '$2y$10$75IfGrYHRTwmWXCBTyQyNe0zfe228OrHtsT2a8EJvbGpzch6h25t2', 'vendor', '2025-03-19 12:42:01'),
(3, 'Admin', 'admin@gmail.com', '$2y$10$d08bs5N.mbettz82Z5FbCeVstV98oxZA6oyN0un7qPhpZiDavqRZ.', 'customer', '2025-04-02 13:52:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
