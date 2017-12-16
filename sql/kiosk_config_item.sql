-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 06, 2017 at 02:50 PM
-- Server version: 5.6.35
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `powerpod_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `kiosk_config_item`
--

CREATE TABLE `kiosk_config_item` (
  `id` int(11) NOT NULL,
  `config_item_id` int(11) NOT NULL,
  `value` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL,
  `status_queued` int(11) NOT NULL,
  `value_queued` varchar(45) NOT NULL,
  `date_applied` datetime NOT NULL,
  `date_unapplied` datetime NOT NULL,
  `kiosk_id` int(11) NOT NULL,
  `date_queued` datetime DEFAULT NULL,
  `user_applied` varchar(45) DEFAULT NULL,
  `user_unapplied` varchar(45) DEFAULT NULL,
  `user_queued` varchar(45) DEFAULT NULL,
  `date_last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kiosk_config_item`
--
ALTER TABLE `kiosk_config_item`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kiosk_config_item`
--
ALTER TABLE `kiosk_config_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;