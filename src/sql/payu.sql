-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 03 Kwi 2020, 08:44
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
-- Baza danych: `payu`
--
CREATE DATABASE IF NOT EXISTS `payu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `payu`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `payment_gateway` varchar(30) NOT NULL DEFAULT 'none',
  `payment_status` varchar(250) NOT NULL DEFAULT '',
  `payment_orderId` varchar(250) NOT NULL DEFAULT '',
  `payment_error` text NOT NULL DEFAULT '',
  `payment_refresh` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_url` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabela Truncate przed wstawieniem `orders`
--

TRUNCATE TABLE `orders`;
-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_order_notify`
--

DROP TABLE IF EXISTS `payment_order_notify`;
CREATE TABLE IF NOT EXISTS `payment_order_notify` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tabela Truncate przed wstawieniem `payment_order_notify`
--

TRUNCATE TABLE `payment_order_notify`;
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;

--
-- Tabela Truncate przed wstawieniem `payment_order_refund`
--

TRUNCATE TABLE `payment_order_refund`;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
