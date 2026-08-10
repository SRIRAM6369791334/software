-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2026 at 06:55 AM
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
-- Database: `poultry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `timestamp`) VALUES
(1, 1, 'Login', 'Auth', 1, '2026-08-07 23:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_code` varchar(255) NOT NULL,
  `placement_date` date NOT NULL,
  `initial_count` int(11) NOT NULL,
  `current_count` int(11) NOT NULL,
  `breed` varchar(255) DEFAULT NULL,
  `avg_placement_weight` decimal(8,3) DEFAULT NULL,
  `status` enum('Active','Closed') NOT NULL DEFAULT 'Active',
  `closed_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bird_batches`
--

CREATE TABLE `bird_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_name` varchar(255) NOT NULL,
  `date_received` date NOT NULL,
  `initial_count` int(11) NOT NULL,
  `current_count` int(11) NOT NULL,
  `avg_weight` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_bank_ledgers`
--

CREATE TABLE `cash_bank_ledgers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ledger_date` date NOT NULL,
  `opening_cash_balance` decimal(12,2) NOT NULL,
  `opening_bank_balance` decimal(12,2) NOT NULL,
  `cash_income` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_income` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_expense` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_expense` decimal(12,2) NOT NULL DEFAULT 0.00,
  `closing_cash_balance` decimal(12,2) NOT NULL,
  `closing_bank_balance` decimal(12,2) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_amount` decimal(12,2) DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('Retail','Wholesale') NOT NULL DEFAULT 'Retail',
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `address`, `gst_number`, `route`, `route_id`, `type`, `balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'mathina chicken', '99444011145', 'panruti', NULL, 'panruti', NULL, 'Retail', 7820.00, '2026-07-18 06:46:45', '2026-08-05 01:10:35', '2026-08-05 01:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `cod_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_transfer_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) GENERATED ALWAYS AS (`cod_amount` + `bank_transfer_amount`) STORED,
  `payment_mode` enum('Cash','UPI','NEFT','Cheque','Bank Transfer') NOT NULL,
  `payment_type` enum('Full','Part','Advance','Regular','Adjustment','Opening') NOT NULL,
  `balance_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_bills`
--

CREATE TABLE `daily_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dealer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `gst_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(255) NOT NULL DEFAULT 'cash',
  `bank_method` enum('UPI','Cheque','NEFT') DEFAULT NULL,
  `previous_outstanding` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payments_during_day` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('COD','Pending','Bank','Paid') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_bill_items`
--

CREATE TABLE `daily_bill_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `daily_bill_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `rate_per_kg` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `day_load_batches`
--

CREATE TABLE `day_load_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billing_date` date NOT NULL,
  `status` enum('Open','Invoiced','Locked') NOT NULL DEFAULT 'Open',
  `total_boxes` int(11) NOT NULL DEFAULT 0,
  `total_box_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_empty_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_bird_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_farm_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_dealer_income` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_vendor_cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_dealer_collected` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_vendor_paid` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_loss_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_weight_loss_approved` tinyint(1) NOT NULL DEFAULT 0,
  `weight_loss_approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `weight_loss_approved_at` timestamp NULL DEFAULT NULL,
  `weight_loss_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `day_load_entries`
--

CREATE TABLE `day_load_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `dealer_id` bigint(20) UNSIGNED NOT NULL,
  `weekly_bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `daily_bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paper_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `customer_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `no_of_boxes` int(11) NOT NULL,
  `box_weight` decimal(10,2) NOT NULL,
  `empty_weight` decimal(10,2) NOT NULL,
  `bird_weight` decimal(10,2) NOT NULL,
  `farm_weight` decimal(10,2) DEFAULT NULL,
  `total_weight` decimal(10,2) DEFAULT NULL,
  `loss_weight` decimal(10,2) DEFAULT NULL,
  `status` enum('Active','Adjusted','Split','Cancelled') NOT NULL DEFAULT 'Active',
  `parent_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `remarks` varchar(255) DEFAULT NULL,
  `dealer_collected` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vendor_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `dealer_payment_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `vendor_payment_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `day_load_invoices`
--

CREATE TABLE `day_load_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `total_boxes` int(11) NOT NULL DEFAULT 0,
  `total_box_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_empty_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_bird_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_farm_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_loss_weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) GENERATED ALWAYS AS (`total_amount` - `amount_paid`) STORED,
  `payment_status` enum('Pending','Partial','Paid') NOT NULL DEFAULT 'Pending',
  `status` enum('Draft','Final') NOT NULL DEFAULT 'Draft',
  `version` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealers`
--

CREATE TABLE `dealers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `route` varchar(255) DEFAULT NULL,
  `route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pending_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dealers`
--

INSERT INTO `dealers` (`id`, `firm_name`, `gst_number`, `location`, `contact_person`, `phone`, `route`, `route_id`, `pending_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'mathina chicken', NULL, 'panruti', '9944011145', '99444011145', 'panruti', NULL, 0.00, '2026-07-18 06:49:05', '2026-07-18 06:49:05', NULL),
(2, 'kaithan chicken', NULL, 'karayambuthur', '9944011145', '9944011141', 'panruti', NULL, 0.00, '2026-07-18 06:52:16', '2026-08-05 01:34:43', NULL),
(3, 'NSK sakathi', NULL, 'panruti', '9944011145', '9944011142', 'panruti', NULL, 0.00, '2026-07-18 06:55:24', '2026-07-18 06:55:24', NULL),
(4, 'basheer chicken', NULL, 'pattampakkam', '9944011145', '9944011143', 'pattampakkam', NULL, 0.00, '2026-07-18 06:57:04', '2026-08-05 01:37:19', NULL),
(5, 'kalima  chicken', NULL, 'pattampakkam', '9944011145', '9944011144', 'pattampakkam', NULL, 0.00, '2026-07-18 07:00:04', '2026-08-05 01:17:54', NULL),
(6, 'boys chicken', NULL, 'pattampakkam', '9944011145', '9944011145', 'pattampakkam', NULL, 0.00, '2026-07-18 07:02:13', '2026-07-24 05:28:14', NULL),
(7, 'R R chicken', NULL, 'pattampakkam', '9944011145', '9944011146', 'pattampakkam', NULL, 0.00, '2026-07-18 07:03:24', '2026-08-05 02:05:12', NULL),
(8, 'M N chicken', NULL, 'pattampakkam', 'owner', '9944011147', 'pattampakkam', NULL, 0.00, '2026-07-18 07:05:05', '2026-07-18 07:05:05', NULL),
(9, 'haji chicken', NULL, 'nellikuppam', 'haji (owner)', '9843978188', 'nellikuppam', NULL, 0.00, '2026-07-18 07:06:49', '2026-08-05 01:51:30', NULL),
(10, 'kuthus chicken', NULL, 'nellikuppam', 'kuthus (owner)', '9944011148', 'nellikuppam', NULL, 0.00, '2026-07-18 07:07:50', '2026-08-05 02:02:18', NULL),
(11, 'ghouse chicken', NULL, 'nellikuppam', 'ghouse (owner)', '9944011149', 'nellikuppam', NULL, 0.00, '2026-07-18 07:09:16', '2026-08-05 01:49:05', NULL),
(12, 'durai broilers kpm', NULL, 'nellikuppam', 'durai kpm(owner)', '9944011100', 'nellikuppam', NULL, 0.00, '2026-07-18 07:10:57', '2026-08-05 01:52:26', NULL),
(13, 'sun chicken', NULL, 'vellagate', 'sathish', '99444011101', 'nellikuppam', NULL, 0.00, '2026-07-18 07:13:12', '2026-07-18 07:13:12', NULL),
(14, 'kavin chicken', NULL, 'vellagate', 'kavin', '99444011102', 'nellikuppam', NULL, 0.00, '2026-07-18 07:14:09', '2026-08-06 23:22:13', NULL),
(15, 'zyan chicken', NULL, 'kondru', 'ajmal (owner)', '7010441328', 'kondur', NULL, 0.00, '2026-07-18 07:15:54', '2026-07-18 07:15:54', NULL),
(16, 'hotel harun', NULL, 'cuddalore', 'naina', '99444011134', 'cuddalore', NULL, 0.00, '2026-07-18 07:19:15', '2026-07-18 07:19:56', NULL),
(17, 'MSR CUDDALORE', NULL, 'cuddalore', 'NASAR', '9384920211', 'cuddalore', NULL, 0.00, '2026-07-18 07:21:10', '2026-08-05 01:55:42', NULL),
(18, 'MSR NOOR', NULL, 'cuddalore', 'NASAR', '99444011190', 'cuddalore', NULL, 0.00, '2026-07-18 07:22:00', '2026-08-05 01:54:59', NULL),
(19, 'FAKUR CHICKEN', NULL, 'cuddalore', 'FAKUR', '99444011123', 'cuddalore', NULL, 0.00, '2026-07-18 07:22:52', '2026-07-18 07:22:52', NULL),
(20, 'MSR OWN SHOP', NULL, 'nellikuppam', 'HASSAN ALI', '99444011140', 'nellikuppam', NULL, 0.00, '2026-07-18 07:23:52', '2026-07-18 07:23:52', NULL),
(21, 'LAKSHMI CHICKEN', NULL, 'pattampakkam', 'LAKKSHMI', '99444011177', 'pattampakkam', NULL, 0.00, '2026-07-18 07:24:42', '2026-08-05 01:47:31', NULL),
(22, 'SRK', NULL, 'panruti', 'SRK', '9944011190', 'panruti', NULL, 0.00, '2026-07-18 07:25:28', '2026-07-18 07:25:28', NULL),
(23, 'SARAVAN', NULL, 'nellikuppam', 'ILLAGO', '99444011167', 'NELLIKUPPAM', NULL, 0.00, '2026-07-18 07:26:26', '2026-07-18 07:26:26', NULL),
(24, 'MSR VPM', NULL, 'VPM', 'MSR', '99444011154', 'VPM', NULL, 0.00, '2026-07-18 07:27:06', '2026-07-18 07:27:06', NULL),
(25, 'aluva chicken', NULL, 'cuddalore', 'aludeen', '6383952296', 'cuddalore', NULL, 0.00, '2026-07-22 03:06:07', '2026-07-22 03:06:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dealer_payments`
--

CREATE TABLE `dealer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dealer_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `day_load_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_group_id` varchar(36) DEFAULT NULL,
  `date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) GENERATED ALWAYS AS (`cash_amount` + `bank_amount`) STORED,
  `pending_balance_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'Cash',
  `bank_transfer_type` enum('UPI','Bank Transfer','NEFT','RTGS','IMPS','Cheque','Other') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_purchases`
--

CREATE TABLE `dealer_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dealer_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `gst_percentage` decimal(5,2) NOT NULL DEFAULT 18.00,
  `gst_amount` decimal(12,2) NOT NULL,
  `net_amount` decimal(12,2) NOT NULL,
  `weekly_bill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_purchase_items`
--

CREATE TABLE `dealer_purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dealer_purchase_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `rate_per_kg` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emis`
--

CREATE TABLE `emis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emi_type` enum('Bank','Finance Company') NOT NULL DEFAULT 'Bank',
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loan_name` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entry_adjustment_logs`
--

CREATE TABLE `entry_adjustment_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entry_id` bigint(20) UNSIGNED NOT NULL,
  `action_type` enum('Create','Edit','Split','Cancel') NOT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `resulting_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `adjusted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Bank Transfer') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#6B7280',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Fuel', '#94fc56', '2026-07-07 07:33:35', '2026-07-07 07:33:35'),
(2, 'Salary', '#b083a4', '2026-07-07 07:33:35', '2026-07-07 07:33:35'),
(3, 'Transport', '#f5f0c4', '2026-07-07 07:33:35', '2026-07-07 07:33:35'),
(4, 'Utility', '#94df2a', '2026-07-07 07:33:35', '2026-07-07 07:33:35'),
(5, 'Misc', '#74248c', '2026-07-07 07:33:35', '2026-07-07 07:33:35'),
(6, 'Rent', '#7f4118', '2026-07-07 07:36:13', '2026-07-07 07:36:13'),
(7, 'Electricity', '#656e43', '2026-07-07 07:36:13', '2026-07-07 07:36:13'),
(8, 'Labour', '#f98a29', '2026-07-07 07:36:13', '2026-07-07 07:36:13'),
(9, 'Feed', '#a80425', '2026-07-07 07:36:13', '2026-07-07 07:36:13'),
(10, 'Medicine', '#4ff278', '2026-07-07 07:36:13', '2026-07-07 07:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('Feed','Chick','Medicine','Vaccine','Equipment','Other') NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `base_unit` varchar(255) NOT NULL DEFAULT 'kg',
  `conversion_rate` decimal(10,2) NOT NULL DEFAULT 1.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--
-- Error reading structure for table poultry_db.migrations: #1932 - Table &#039;poultry_db.migrations&#039; doesn&#039;t exist in engine
-- Error reading data for table poultry_db.migrations: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `poultry_db`.`migrations`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_adjustment_logs`
--

CREATE TABLE `payment_adjustment_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `action_type` varchar(255) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `reason` varchar(255) DEFAULT NULL,
  `adjusted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `permission_group_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `permission_group_id`) VALUES
(1, 'view customers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(2, 'create customers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(3, 'edit customers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(4, 'delete customers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(5, 'view customer bills', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(6, 'view customer payments', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(7, 'view customer emis', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 1),
(8, 'view dealers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(9, 'create dealers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(10, 'edit dealers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(11, 'delete dealers', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(12, 'view dealer purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(13, 'view dealer ledger', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 2),
(14, 'view vendors', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 3),
(15, 'create vendors', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 3),
(16, 'edit vendors', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 3),
(17, 'delete vendors', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 3),
(18, 'view vendor purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 3),
(19, 'view bills', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 4),
(20, 'create bills', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 4),
(21, 'edit bills', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 4),
(22, 'delete bills', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 4),
(23, 'view purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 5),
(24, 'create purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 5),
(25, 'edit purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 5),
(26, 'delete purchases', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 5),
(27, 'view payments', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 6),
(28, 'create payments', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 6),
(29, 'edit payments', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 6),
(30, 'delete payments', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 6),
(31, 'view expenses', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 7),
(32, 'create expenses', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 7),
(33, 'edit expenses', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 7),
(34, 'delete expenses', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 7),
(35, 'view emis', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 8),
(36, 'create emis', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 8),
(37, 'edit emis', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 8),
(38, 'delete emis', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 8),
(39, 'view reports', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 9),
(40, 'view profit dashboard', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 9),
(41, 'view users', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(42, 'create users', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(43, 'edit users', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(44, 'delete users', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(45, 'view roles', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(46, 'create roles', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(47, 'edit roles', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(48, 'delete roles', 'web', '2026-08-07 23:00:43', '2026-08-07 23:00:43', 10),
(49, 'view permissions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(50, 'create permissions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(51, 'edit permissions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(52, 'delete permissions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(53, 'manage users', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(54, 'manage roles', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(55, 'manage permissions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(56, 'view activity logs', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 10),
(57, 'view stock', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(58, 'create stock', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(59, 'edit stock', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(60, 'delete stock', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(61, 'view batches', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(62, 'create batches', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(63, 'edit batches', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(64, 'delete batches', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(65, 'view warehouses', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(66, 'create warehouses', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(67, 'edit warehouses', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(68, 'delete warehouses', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(69, 'view items', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(70, 'create items', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(71, 'edit items', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(72, 'delete items', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(73, 'view consumptions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(74, 'create consumptions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(75, 'delete consumptions', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(76, 'view mortalities', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(77, 'create mortalities', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(78, 'delete mortalities', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(79, 'view analytics', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 11),
(80, 'view routes', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(81, 'create routes', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(82, 'edit routes', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(83, 'delete routes', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(84, 'mark delivery status', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(85, 'view vehicles', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(86, 'create vehicles', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(87, 'edit vehicles', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(88, 'delete vehicles', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(89, 'view drivers', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(90, 'create drivers', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(91, 'edit drivers', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12),
(92, 'delete drivers', 'web', '2026-08-07 23:00:44', '2026-08-07 23:00:44', 12);

-- --------------------------------------------------------

--
-- Table structure for table `permission_groups`
--

CREATE TABLE `permission_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_groups`
--

INSERT INTO `permission_groups` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Customers', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(2, 'Dealers', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(3, 'Vendors', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(4, 'Bills', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(5, 'Purchases', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(6, 'Payments', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(7, 'Expenses', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(8, 'EMIs', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(9, 'Reports & Dashboards', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(10, 'User Management', '2026-08-07 23:00:43', '2026-08-07 23:00:43'),
(11, 'Inventory & Stock', '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(12, 'Routes & Delivery', '2026-08-07 23:00:44', '2026-08-07 23:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poultry_consumptions`
--

CREATE TABLE `poultry_consumptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poultry_mortalities`
--

CREATE TABLE `poultry_mortalities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `count` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_name` varchar(255) NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `gst_percentage` decimal(5,2) NOT NULL DEFAULT 18.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'kg',
  `rate` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `description`, `is_system`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(2, 'accountant', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(3, 'delivery_staff', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(4, 'data_entry', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(5, 'manager', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(6, 'sales', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44'),
(7, 'store_keeper', 'web', NULL, 0, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 4),
(1, 5),
(1, 6),
(2, 1),
(2, 4),
(2, 5),
(2, 6),
(3, 1),
(3, 4),
(3, 5),
(3, 6),
(4, 1),
(4, 5),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(8, 2),
(8, 4),
(8, 5),
(8, 6),
(9, 1),
(9, 4),
(9, 5),
(9, 6),
(10, 1),
(10, 4),
(10, 5),
(10, 6),
(11, 1),
(11, 5),
(12, 1),
(13, 1),
(14, 1),
(14, 2),
(14, 4),
(14, 5),
(15, 1),
(15, 4),
(15, 5),
(16, 1),
(16, 4),
(16, 5),
(17, 1),
(17, 5),
(18, 1),
(19, 1),
(19, 2),
(19, 3),
(19, 5),
(19, 6),
(20, 1),
(20, 2),
(20, 5),
(20, 6),
(21, 1),
(21, 2),
(21, 5),
(21, 6),
(22, 1),
(22, 5),
(23, 1),
(23, 4),
(23, 5),
(24, 1),
(24, 4),
(24, 5),
(25, 1),
(25, 4),
(25, 5),
(26, 1),
(26, 5),
(27, 1),
(27, 2),
(27, 5),
(27, 6),
(28, 1),
(28, 2),
(28, 5),
(28, 6),
(29, 1),
(29, 2),
(29, 5),
(29, 6),
(30, 1),
(30, 5),
(31, 1),
(31, 2),
(31, 5),
(32, 1),
(32, 2),
(32, 5),
(33, 1),
(33, 2),
(33, 5),
(34, 1),
(34, 5),
(35, 1),
(35, 5),
(36, 1),
(36, 5),
(37, 1),
(37, 5),
(38, 1),
(38, 5),
(39, 1),
(39, 2),
(39, 5),
(40, 1),
(40, 2),
(40, 5),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(57, 2),
(57, 4),
(57, 5),
(57, 7),
(58, 1),
(58, 4),
(58, 5),
(58, 7),
(59, 1),
(59, 4),
(59, 5),
(59, 7),
(60, 1),
(60, 5),
(61, 1),
(61, 4),
(61, 5),
(61, 7),
(62, 1),
(62, 4),
(62, 5),
(62, 7),
(63, 1),
(63, 4),
(63, 5),
(63, 7),
(64, 1),
(64, 5),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(80, 3),
(80, 5),
(80, 6),
(80, 7),
(81, 1),
(81, 5),
(82, 1),
(82, 5),
(83, 1),
(83, 5),
(84, 1),
(84, 3),
(84, 5),
(84, 6),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_items`
--

CREATE TABLE `stock_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Feed',
  `unit` varchar(255) NOT NULL DEFAULT 'kg',
  `current_stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `reorder_level` decimal(10,3) NOT NULL DEFAULT 0.000,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_ledgers`
--

CREATE TABLE `stock_ledgers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `type` enum('IN','OUT') NOT NULL,
  `source_type` varchar(255) NOT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `txn_type` enum('IN','OUT','ADJUST') DEFAULT NULL,
  `type` enum('purchase_in','sale_out','adjustment') NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'kg',
  `rate` decimal(10,2) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin', 'admin@poultry.com', NULL, '$2y$12$R2whQ8fxkfQGj9g1uLg4C.Np/zZ8c215YowXB.1v76miwAlb68f.m', 1, NULL, '2026-08-07 23:00:44', '2026-08-07 23:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_number` varchar(255) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `is_shop` tinyint(1) NOT NULL DEFAULT 0,
  `gst_number` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `route` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pending_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `firm_name`, `is_shop`, `gst_number`, `location`, `contact_person`, `phone`, `route`, `notes`, `pending_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'mani foods private limted', 0, NULL, 'plani', '9944011145', '9944011345', 'villupuram suronding', 'nothing', 0.00, '2026-07-20 01:13:08', '2026-07-20 01:13:08', NULL),
(2, 'royal agro farm private limited', 0, NULL, 'coimbatore', '9944011100', '99444011109', 'villupuram suronding', 'no', 0.00, '2026-07-20 01:16:30', '2026-07-20 01:16:30', NULL),
(3, 'swami feeds private limited', 0, NULL, 'coimbatore', 'jayaram (zonal)', '99444011111', 'villupuram suronding', 'no', 0.00, '2026-07-20 01:18:12', '2026-07-20 01:18:12', NULL),
(4, 'skm', 0, NULL, 'coimbatore', 'Shakeen Msr', '99444011145', 'villupuram suronding', 'no', 0.00, '2026-07-22 03:25:23', '2026-07-22 03:25:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payments`
--

CREATE TABLE `vendor_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `day_load_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `cash_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) GENERATED ALWAYS AS (`cash_amount` + `bank_amount`) STORED,
  `pending_balance_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'Cash',
  `bank_transfer_type` enum('UPI','Bank Transfer','NEFT','RTGS','IMPS','Cheque','Other') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weekly_bills`
--

CREATE TABLE `weekly_bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `dealer_id` bigint(20) UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `gst_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(255) NOT NULL DEFAULT 'cash',
  `bank_method` enum('UPI','Cheque','NEFT') DEFAULT NULL,
  `status` enum('COD','Pending','Bank','Paid') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `monday_payment_amount` decimal(12,2) DEFAULT NULL,
  `monday_payment_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `friday_payment_amount` decimal(12,2) DEFAULT NULL,
  `friday_payment_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `previous_outstanding` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payments_during_week` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weekly_bill_items`
--

CREATE TABLE `weekly_bill_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `weekly_bill_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `quantity_kg` decimal(10,2) NOT NULL,
  `rate_per_kg` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batches_batch_code_unique` (`batch_code`),
  ADD KEY `idx_batches_status` (`status`);

--
-- Indexes for table `bird_batches`
--
ALTER TABLE `bird_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cash_bank_ledgers`
--
ALTER TABLE `cash_bank_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_bank_ledgers_ledger_date_unique` (`ledger_date`),
  ADD KEY `cash_bank_ledgers_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_name_index` (`name`),
  ADD KEY `customers_route_index` (`route`),
  ADD KEY `customers_type_index` (`type`),
  ADD KEY `customers_route_id_foreign` (`route_id`),
  ADD KEY `idx_customers_balance` (`balance`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_payments_customer_id_index` (`customer_id`),
  ADD KEY `customer_payments_date_index` (`date`),
  ADD KEY `customer_payments_date_customer_id_index` (`date`,`customer_id`);

--
-- Indexes for table `daily_bills`
--
ALTER TABLE `daily_bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `daily_bills_invoice_no_unique` (`invoice_no`),
  ADD KEY `daily_bills_customer_id_foreign` (`customer_id`),
  ADD KEY `daily_bills_date_index` (`date`),
  ADD KEY `daily_bills_status_index` (`status`),
  ADD KEY `daily_bills_date_customer_id_index` (`date`,`customer_id`),
  ADD KEY `daily_bills_dealer_id_foreign` (`dealer_id`);

--
-- Indexes for table `daily_bill_items`
--
ALTER TABLE `daily_bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `daily_bill_items_daily_bill_id_foreign` (`daily_bill_id`),
  ADD KEY `daily_bill_items_item_name_index` (`item_name`);

--
-- Indexes for table `day_load_batches`
--
ALTER TABLE `day_load_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `day_load_batches_billing_date_unique` (`billing_date`),
  ADD KEY `day_load_batches_invoice_id_foreign` (`invoice_id`),
  ADD KEY `day_load_batches_weight_loss_approved_by_foreign` (`weight_loss_approved_by`);

--
-- Indexes for table `day_load_entries`
--
ALTER TABLE `day_load_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `day_load_entries_batch_id_foreign` (`batch_id`),
  ADD KEY `day_load_entries_vendor_id_foreign` (`vendor_id`),
  ADD KEY `day_load_entries_dealer_id_foreign` (`dealer_id`),
  ADD KEY `day_load_entries_parent_entry_id_foreign` (`parent_entry_id`),
  ADD KEY `day_load_entries_weekly_bill_id_foreign` (`weekly_bill_id`),
  ADD KEY `day_load_entries_daily_bill_id_foreign` (`daily_bill_id`);

--
-- Indexes for table `day_load_invoices`
--
ALTER TABLE `day_load_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `day_load_invoices_invoice_no_unique` (`invoice_no`),
  ADD KEY `day_load_invoices_batch_id_foreign` (`batch_id`);

--
-- Indexes for table `dealers`
--
ALTER TABLE `dealers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dealers_firm_name_index` (`firm_name`),
  ADD KEY `dealers_route_id_foreign` (`route_id`),
  ADD KEY `idx_dealers_pending_amount` (`pending_amount`);

--
-- Indexes for table `dealer_payments`
--
ALTER TABLE `dealer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dealer_payments_dealer_id_index` (`dealer_id`),
  ADD KEY `dealer_payments_date_index` (`date`),
  ADD KEY `dealer_payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `dealer_payments_day_load_entry_id_foreign` (`day_load_entry_id`),
  ADD KEY `dealer_payments_payment_group_id_index` (`payment_group_id`);

--
-- Indexes for table `dealer_purchases`
--
ALTER TABLE `dealer_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dealer_purchases_invoice_no_unique` (`invoice_no`),
  ADD KEY `dealer_purchases_dealer_id_foreign` (`dealer_id`),
  ADD KEY `dealer_purchases_weekly_bill_id_foreign` (`weekly_bill_id`);

--
-- Indexes for table `dealer_purchase_items`
--
ALTER TABLE `dealer_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dealer_purchase_items_dealer_purchase_id_foreign` (`dealer_purchase_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emis`
--
ALTER TABLE `emis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emis_due_date_index` (`due_date`),
  ADD KEY `emis_status_index` (`status`);

--
-- Indexes for table `entry_adjustment_logs`
--
ALTER TABLE `entry_adjustment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_adjustment_logs_entry_id_foreign` (`entry_id`),
  ADD KEY `entry_adjustment_logs_resulting_entry_id_foreign` (`resulting_entry_id`),
  ADD KEY `entry_adjustment_logs_adjusted_by_foreign` (`adjusted_by`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_date_index` (`date`),
  ADD KEY `expenses_category_index` (`category`),
  ADD KEY `expenses_category_id_foreign` (`category_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_categories_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_code_unique` (`code`),
  ADD KEY `items_name_index` (`name`),
  ADD KEY `items_type_index` (`type`),
  ADD KEY `items_category_index` (`category`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_adjustment_logs`
--
ALTER TABLE `payment_adjustment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_adjustment_logs_adjusted_by_foreign` (`adjusted_by`),
  ADD KEY `payment_adjustment_logs_payment_id_index` (`payment_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  ADD KEY `permissions_permission_group_id_foreign` (`permission_group_id`);

--
-- Indexes for table `permission_groups`
--
ALTER TABLE `permission_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_groups_name_unique` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `poultry_consumptions`
--
ALTER TABLE `poultry_consumptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poultry_consumptions_batch_id_foreign` (`batch_id`),
  ADD KEY `poultry_consumptions_item_id_foreign` (`item_id`),
  ADD KEY `poultry_consumptions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `poultry_consumptions_created_by_foreign` (`created_by`),
  ADD KEY `poultry_consumptions_date_batch_id_index` (`date`,`batch_id`);

--
-- Indexes for table `poultry_mortalities`
--
ALTER TABLE `poultry_mortalities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poultry_mortalities_batch_id_foreign` (`batch_id`),
  ADD KEY `poultry_mortalities_created_by_foreign` (`created_by`),
  ADD KEY `poultry_mortalities_date_batch_id_index` (`date`,`batch_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_date_index` (`date`),
  ADD KEY `purchases_vendor_name_index` (`vendor_name`),
  ADD KEY `purchases_vendor_id_foreign` (`vendor_id`),
  ADD KEY `purchases_date_vendor_id_index` (`date`,`vendor_id`),
  ADD KEY `purchases_invoice_no_index` (`invoice_no`),
  ADD KEY `purchases_payment_mode_index` (`payment_mode`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_item_name_index` (`item_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  ADD KEY `roles_created_by_foreign` (`created_by`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routes_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `routes_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_summary_item_name_unique` (`item_name`);

--
-- Indexes for table `stock_ledgers`
--
ALTER TABLE `stock_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_ledgers_batch_id_foreign` (`batch_id`),
  ADD KEY `stock_ledgers_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_ledgers_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `idx_ledgers_item_type_qty` (`item_id`,`type`,`quantity`),
  ADD KEY `idx_ledgers_txn_date` (`transaction_date`),
  ADD KEY `idx_ledgers_created_at` (`created_at`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_vehicle_number_unique` (`vehicle_number`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendors_firm_name_index` (`firm_name`);

--
-- Indexes for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_payments_vendor_id_index` (`vendor_id`),
  ADD KEY `vendor_payments_date_index` (`date`),
  ADD KEY `vendor_payments_day_load_entry_id_foreign` (`day_load_entry_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weekly_bills`
--
ALTER TABLE `weekly_bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `weekly_bills_invoice_no_unique` (`invoice_no`),
  ADD KEY `weekly_bills_status_index` (`status`),
  ADD KEY `weekly_bills_period_start_index` (`period_start`),
  ADD KEY `weekly_bills_period_start_customer_id_index` (`period_start`,`dealer_id`),
  ADD KEY `idx_weekly_bills_period_customer` (`period_end`,`dealer_id`),
  ADD KEY `weekly_bills_dealer_id_foreign` (`dealer_id`);

--
-- Indexes for table `weekly_bill_items`
--
ALTER TABLE `weekly_bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `weekly_bill_items_weekly_bill_id_foreign` (`weekly_bill_id`),
  ADD KEY `weekly_bill_items_item_name_index` (`item_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bird_batches`
--
ALTER TABLE `bird_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_bank_ledgers`
--
ALTER TABLE `cash_bank_ledgers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_bills`
--
ALTER TABLE `daily_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_bill_items`
--
ALTER TABLE `daily_bill_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `day_load_batches`
--
ALTER TABLE `day_load_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `day_load_entries`
--
ALTER TABLE `day_load_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `day_load_invoices`
--
ALTER TABLE `day_load_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealers`
--
ALTER TABLE `dealers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `dealer_payments`
--
ALTER TABLE `dealer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_purchases`
--
ALTER TABLE `dealer_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_purchase_items`
--
ALTER TABLE `dealer_purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emis`
--
ALTER TABLE `emis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entry_adjustment_logs`
--
ALTER TABLE `entry_adjustment_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_adjustment_logs`
--
ALTER TABLE `payment_adjustment_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `permission_groups`
--
ALTER TABLE `permission_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poultry_consumptions`
--
ALTER TABLE `poultry_consumptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poultry_mortalities`
--
ALTER TABLE `poultry_mortalities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_items`
--
ALTER TABLE `stock_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_ledgers`
--
ALTER TABLE `stock_ledgers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_bills`
--
ALTER TABLE `weekly_bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_bill_items`
--
ALTER TABLE `weekly_bill_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cash_bank_ledgers`
--
ALTER TABLE `cash_bank_ledgers`
  ADD CONSTRAINT `cash_bank_ledgers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_bills`
--
ALTER TABLE `daily_bills`
  ADD CONSTRAINT `daily_bills_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_bills_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_bill_items`
--
ALTER TABLE `daily_bill_items`
  ADD CONSTRAINT `daily_bill_items_daily_bill_id_foreign` FOREIGN KEY (`daily_bill_id`) REFERENCES `daily_bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `day_load_batches`
--
ALTER TABLE `day_load_batches`
  ADD CONSTRAINT `day_load_batches_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `day_load_invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `day_load_batches_weight_loss_approved_by_foreign` FOREIGN KEY (`weight_loss_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `day_load_entries`
--
ALTER TABLE `day_load_entries`
  ADD CONSTRAINT `day_load_entries_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `day_load_batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `day_load_entries_daily_bill_id_foreign` FOREIGN KEY (`daily_bill_id`) REFERENCES `daily_bills` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `day_load_entries_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`),
  ADD CONSTRAINT `day_load_entries_parent_entry_id_foreign` FOREIGN KEY (`parent_entry_id`) REFERENCES `day_load_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `day_load_entries_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`),
  ADD CONSTRAINT `day_load_entries_weekly_bill_id_foreign` FOREIGN KEY (`weekly_bill_id`) REFERENCES `weekly_bills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `day_load_invoices`
--
ALTER TABLE `day_load_invoices`
  ADD CONSTRAINT `day_load_invoices_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `day_load_batches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dealers`
--
ALTER TABLE `dealers`
  ADD CONSTRAINT `dealers_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dealer_payments`
--
ALTER TABLE `dealer_payments`
  ADD CONSTRAINT `dealer_payments_day_load_entry_id_foreign` FOREIGN KEY (`day_load_entry_id`) REFERENCES `day_load_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dealer_payments_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dealer_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `day_load_invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dealer_purchases`
--
ALTER TABLE `dealer_purchases`
  ADD CONSTRAINT `dealer_purchases_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dealer_purchases_weekly_bill_id_foreign` FOREIGN KEY (`weekly_bill_id`) REFERENCES `weekly_bills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dealer_purchase_items`
--
ALTER TABLE `dealer_purchase_items`
  ADD CONSTRAINT `dealer_purchase_items_dealer_purchase_id_foreign` FOREIGN KEY (`dealer_purchase_id`) REFERENCES `dealer_purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `entry_adjustment_logs`
--
ALTER TABLE `entry_adjustment_logs`
  ADD CONSTRAINT `entry_adjustment_logs_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `entry_adjustment_logs_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `day_load_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entry_adjustment_logs_resulting_entry_id_foreign` FOREIGN KEY (`resulting_entry_id`) REFERENCES `day_load_entries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_adjustment_logs`
--
ALTER TABLE `payment_adjustment_logs`
  ADD CONSTRAINT `payment_adjustment_logs_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_adjustment_logs_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `dealer_payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_permission_group_id_foreign` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_groups` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `poultry_consumptions`
--
ALTER TABLE `poultry_consumptions`
  ADD CONSTRAINT `poultry_consumptions_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poultry_consumptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `poultry_consumptions_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poultry_consumptions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `poultry_mortalities`
--
ALTER TABLE `poultry_mortalities`
  ADD CONSTRAINT `poultry_mortalities_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poultry_mortalities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `routes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_ledgers`
--
ALTER TABLE `stock_ledgers`
  ADD CONSTRAINT `stock_ledgers_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
  ADD CONSTRAINT `stock_ledgers_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_ledgers_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD CONSTRAINT `vendor_payments_day_load_entry_id_foreign` FOREIGN KEY (`day_load_entry_id`) REFERENCES `day_load_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_bills`
--
ALTER TABLE `weekly_bills`
  ADD CONSTRAINT `weekly_bills_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `dealers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_bill_items`
--
ALTER TABLE `weekly_bill_items`
  ADD CONSTRAINT `weekly_bill_items_weekly_bill_id_foreign` FOREIGN KEY (`weekly_bill_id`) REFERENCES `weekly_bills` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
