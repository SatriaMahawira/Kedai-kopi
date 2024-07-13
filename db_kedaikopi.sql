-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2024 at 12:10 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kedaikopi`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(7, 'admin1', '6c7ca345f63f835cb353ff15bd6c5e052ec08e7a'),
(8, 'admin2', '315f166c5aca63a157f7d41007675cb44a948b33'),
(9, 'admin4', 'ea053d11a8aad1ccf8c18f9241baeb9ec47e5d64');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(13, 3, 16, 'Coffe milk', 23000, 1, '1-Coffee-Milk_020712_0048.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(1, 1, 'Satria', '081294311076', 'cash on delivery', 'Griya, samping masjid - 13171', 'Coffe Latte ( 25000 x 4 ) - ', 100000, '2024-05-29', 'completed'),
(2, 1, 'Satria', '081294311076', 'Qris', 'Harapan Indah, Pagar warna hitam - 17131', 'Chocolate Coffee ( 30000 x 2 ) - Es teh Manis ( 12000 x 4 ) - ', 108000, '2024-05-29', 'completed'),
(4, 2, 'Asep', '80128387', 'Qris', 'Harapan indah, samping masjid - 213413', 'Chocolate Coffee ( 30000 x 1 ) - Chocolate Brownies ( 24000 x 1 ) - ', 54000, '2024-06-03', 'completed'),
(5, 3, 'Faran', '08812391239', 'Qris', 'Harapan indah, samping masjid - 17182', 'Coffe Latte ( 25000 x 1 ) - Chocolate Brownies ( 24000 x 2 ) - ', 73000, '2024-06-03', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`) VALUES
(1, 'Coffe Latte', 25000, 't_5d9ae209ae934.jpg'),
(2, 'Mochacino', 26000, 'imagasdes.jpeg'),
(3, 'Chocolate Coffee', 30000, '159068461-454155445833961-1801311753966360922-n-b9b4afce4d72eea6998a58b2f045b9a6_600x400.jpg'),
(7, 'Macchiato', 25000, 'medium_medium_Caramel_Macchiato__www.allfoodsrecipes.com_.jpg'),
(8, 'Cappucino', 27000, 'es-cappucino-cincau-foto-resep-utama.jpg'),
(9, 'Espresso', 20000, 'espresso-013-1024x681.jpg'),
(10, 'Es teh Manis', 12000, '083078300_1589462572-shutterstock_435468841.jpg'),
(11, 'Air Mineral', 6000, '000000666334_01_800.jpg.jpg'),
(12, 'Es Lemon Tea', 15000, 'es-lemon-tea.jpg'),
(13, 'Strawberry cake', 24000, '60564-StrawberryCakeFromScratch-ddfms-4X3-0291-1-cd8254e28ea14112b5fc49e25cd08ff6.jpg'),
(14, 'Chocolate Brownies', 24000, 'Resep-Brownies-Panggang-dan-Tips-Membuatnya-1024x683.jpg'),
(15, 'Kentang Goreng', 15000, 'cara-membuat-kentang-goreng-reny-20230204071836.jpg'),
(16, 'Coffe milk', 23000, '1-Coffee-Milk_020712_0048.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`) VALUES
(1, 'satria', 'satria@gmail.com', 'e7a73b1a1bb6328e80305273219a8e048e33de8b'),
(2, 'asep', 'asep@gmail.com', '549e6da6a3f49abd9369f06d222f1d323e127643'),
(3, 'Faran', 'faran@gmail.com', '0b05f0d97842d9544dfb811b7aa4fe761d668177');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
