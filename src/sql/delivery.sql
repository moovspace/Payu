-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 22 Mar 2020, 19:10
-- Wersja serwera: 10.3.22-MariaDB-0+deb10u1
-- Wersja PHP: 7.3.14-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `delivery`
--
CREATE DATABASE IF NOT EXISTS `delivery` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `delivery`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','failed','completed','refunded','canceled','onhold','processing','delivery') NOT NULL DEFAULT 'pending',
  `name` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `coupon` varchar(50) NOT NULL DEFAULT '',
  `delivery_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pick_up_time` varchar(10) NOT NULL DEFAULT '',
  `mobile` varchar(50) NOT NULL,
  `info` varchar(250) NOT NULL DEFAULT '',
  `payment` int(11) NOT NULL DEFAULT 1,
  `worker` bigint(22) NOT NULL DEFAULT 0,
  `rf_user` bigint(22) NOT NULL DEFAULT 0,
  `ip` varchar(100) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `visible_user` tinyint(1) NOT NULL DEFAULT 1,
  `paid` enum('not-paid','paid','refunded') NOT NULL DEFAULT 'not-paid',
  `paid_with` enum('money','card','payu') NOT NULL DEFAULT 'money',
  `email` varchar(190) NOT NULL DEFAULT '',
  `payment_gateway` varchar(30) NOT NULL DEFAULT '',
  `payment_status` varchar(100) NOT NULL DEFAULT '',
  `payment_refresh` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_orderId` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_product`
--

DROP TABLE IF EXISTS `order_product`;
CREATE TABLE IF NOT EXISTS `order_product` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `rf_orders` bigint(22) NOT NULL,
  `product` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sale` tinyint(1) NOT NULL DEFAULT 0,
  `attr` bigint(22) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_product_addon`
--

DROP TABLE IF EXISTS `order_product_addon`;
CREATE TABLE IF NOT EXISTS `order_product_addon` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `rf_orders` bigint(22) NOT NULL,
  `rf_order_product` bigint(22) NOT NULL,
  `product` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sale` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_order`
--

DROP TABLE IF EXISTS `payment_order`;
CREATE TABLE IF NOT EXISTS `payment_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(250) NOT NULL,
  `extOrderId` varchar(250) NOT NULL DEFAULT '',
  `currencyCode` varchar(3) NOT NULL DEFAULT 'PLN',
  `totalAmount` int(11) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_order_notify`
--

DROP TABLE IF EXISTS `payment_order_notify`;
CREATE TABLE IF NOT EXISTS `payment_order_notify` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(250) NOT NULL,
  `extOrderId` varchar(250) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `orderCreateDate` varchar(50) NOT NULL,
  `customerIp` varchar(250) NOT NULL DEFAULT '',
  `currencyCode` varchar(10) NOT NULL DEFAULT 'PLN',
  `totalAmount` bigint(22) NOT NULL,
  `description` varchar(250) NOT NULL DEFAULT '',
  `merchantPosId` varchar(250) NOT NULL DEFAULT '',
  `payMethod` text NOT NULL DEFAULT '',
  `notifyUrl` varchar(500) NOT NULL DEFAULT '',
  `properties` text NOT NULL DEFAULT '',
  `buyer` text NOT NULL DEFAULT '',
  `products` text NOT NULL DEFAULT '',
  `localReceiptDateTime` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_order_refund`
--

DROP TABLE IF EXISTS `payment_order_refund`;
CREATE TABLE IF NOT EXISTS `payment_order_refund` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `orderId` varchar(250) NOT NULL,
  `extOrderId` varchar(250) NOT NULL DEFAULT '',
  `currencyCode` varchar(3) NOT NULL DEFAULT 'PLN',
  `totalAmount` int(11) NOT NULL DEFAULT 0,
  `refund` text NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
