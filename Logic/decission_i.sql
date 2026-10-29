-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 08:47 AM
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
-- Database: `decission_i`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `activity` varchar(255) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `activity`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'logout', 'User logged out', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-13 07:53:55'),
(2, 2, 'article_created', 'Created article: NISAB', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-13 08:31:31'),
(3, 1, 'test', 'Testing log activity', NULL, 1, '127.0.0.1', 'Test', '2026-10-13 08:46:31'),
(4, 2, 'article_created', 'Created article: Gold and Silver', NULL, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-13 08:52:18'),
(5, 2, 'article_created', 'Created article: Business', NULL, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-13 09:04:25'),
(6, 2, 'article_created', 'Created article: Agriculture', NULL, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 11:31:34'),
(7, 2, 'article_updated', 'Updated article: NISAB', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:22:08'),
(8, 2, 'article_updated', 'Updated article: Business', NULL, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:26:07'),
(9, 2, 'article_updated', 'Updated article: Agriculture', NULL, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:43:28'),
(10, 2, 'article_updated', 'Updated article: NISAB', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:47:25'),
(11, 2, 'fatwa_created', 'Created fatwa: shin akwai zakkar akan motocin hawa', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:42:57'),
(12, 2, 'fatwa_updated', 'Updated fatwa #1', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 12:40:05'),
(13, 2, 'fatwa_updated', 'Updated fatwa #1', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 16:26:50'),
(14, 2, 'fatwa_published_from_question', 'Published fatwa #3 from question #3', NULL, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 16:52:21'),
(15, 2, 'fatwa_deleted', 'Deleted fatwa #3', NULL, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 16:53:02');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(10) UNSIGNED NOT NULL,
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `beneficiary_code` varchar(80) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Nigeria',
  `beneficiary_category` varchar(150) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `calculation_id` int(10) UNSIGNED NOT NULL,
  `certificate_no` varchar(80) NOT NULL,
  `verification_code` varchar(150) NOT NULL,
  `issued_to` varchar(255) NOT NULL,
  `category_summary` varchar(255) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `payment_date` datetime DEFAULT NULL,
  `status` enum('pending','valid','revoked','expired') NOT NULL DEFAULT 'pending',
  `issued_by` int(10) UNSIGNED DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `revoked_reason` text DEFAULT NULL,
  `qr_payload` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_v2` varchar(30) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_verifications`
--

CREATE TABLE `certificate_verifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `certificate_id` int(10) UNSIGNED NOT NULL,
  `verification_code` varchar(150) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `verification_result` enum('valid','invalid','revoked','expired') NOT NULL,
  `verified_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` char(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `decimal_places` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `decimal_places`, `is_default`, `status`, `created_at`) VALUES
(1, 'NGN', 'Nigerian Naira', '₦', 2, 1, 'active', '2026-10-12 14:55:33'),
(2, 'USD', 'United States Dollar', '$', 2, 0, 'active', '2026-10-12 14:55:33'),
(3, 'GBP', 'British Pound', '£', 2, 0, 'active', '2026-10-12 14:55:33'),
(4, 'EUR', 'Euro', '€', 2, 0, 'active', '2026-10-12 14:55:33'),
(5, 'SAR', 'Saudi Riyal', '﷼', 2, 0, 'active', '2026-10-12 14:55:33'),
(6, 'AED', 'United Arab Emirates Dirham', 'د.إ', 2, 0, 'active', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `distributions`
--

CREATE TABLE `distributions` (
  `id` int(10) UNSIGNED NOT NULL,
  `fund_id` int(10) UNSIGNED NOT NULL,
  `distribution_reference` varchar(80) NOT NULL,
  `total_amount` decimal(18,2) NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `distribution_date` datetime NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('draft','pending','approved','completed','cancelled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distribution_items`
--

CREATE TABLE `distribution_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `distribution_id` int(10) UNSIGNED NOT NULL,
  `beneficiary_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(10) UNSIGNED NOT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `question` varchar(500) NOT NULL,
  `answer` longtext NOT NULL,
  `category` varchar(150) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fatwas`
--

CREATE TABLE `fatwas` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` longtext NOT NULL,
  `source` varchar(500) DEFAULT NULL,
  `scholar` varchar(255) DEFAULT NULL,
  `madhhab` varchar(100) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `status` enum('draft','pending_review','under_review','approved','published','rejected','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_v2` varchar(30) NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fatwas`
--

INSERT INTO `fatwas` (`id`, `category_id`, `language`, `title`, `slug`, `question`, `answer`, `source`, `scholar`, `madhhab`, `excerpt`, `status`, `is_featured`, `created_by`, `reviewed_by`, `published_at`, `created_at`, `updated_at`, `status_v2`) VALUES
(1, 7, 'en', NULL, 'shin-akwai-zakkar-akan-motocin-hawa', 'shin akwai zakkar akan motocin hawa', 'aa babu zakka akan motocin hawa kamar motoci da kuma babura wanda ba na neman kudi bane ba.', 'minhajul muslim', 'sheik abubakar aljazairi', 'malikiyya', 'babu zakkan akan abin amfani', 'published', 1, 2, NULL, '2026-10-14 10:42:57', '2026-10-14 09:42:57', '2026-10-14 12:40:05', 'draft'),
(2, NULL, 'en', NULL, 'test-1791995239', 'ina iya bawa yaron shagona zakka?', 'ehh kana iya bawa yaron shagon zakka saboda baya daga mutanen da allah ya dora maka cidasu kamar uwa da uba da sauran su', NULL, NULL, NULL, NULL, 'published', 0, 2, NULL, '2026-10-14 17:27:19', '2026-10-14 16:27:19', '2026-10-14 16:27:19', 'draft');

-- --------------------------------------------------------

--
-- Table structure for table `fatwa_categories`
--

CREATE TABLE `fatwa_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fatwa_categories`
--

INSERT INTO `fatwa_categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`) VALUES
(1, 'General Zakat', 'general-zakat', 'General questions concerning Zakat', 'active', '2026-10-12 14:55:33'),
(2, 'Gold & Jewellery', 'gold-jewellery', 'Questions concerning gold and jewellery', 'active', '2026-10-12 14:55:33'),
(3, 'Cash & Savings', 'cash-savings', 'Questions concerning cash and savings', 'active', '2026-10-12 14:55:33'),
(4, 'Business', 'business', 'Questions concerning business Zakat', 'active', '2026-10-12 14:55:33'),
(5, 'Agriculture', 'agriculture', 'Questions concerning agricultural Zakat', 'active', '2026-10-12 14:55:33'),
(6, 'Livestock', 'livestock', 'Questions concerning livestock', 'active', '2026-10-12 14:55:33'),
(7, 'Property', 'property', 'Questions concerning property', 'active', '2026-10-12 14:55:33'),
(8, 'Madhhab & Methodology', 'madhhab-methodology', 'Questions concerning scholarly methodology', 'active', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `fatwa_reviews`
--

CREATE TABLE `fatwa_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `fatwa_id` int(10) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `action` enum('submitted','reviewed','approved','rejected','revision_requested') NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_articles`
--

CREATE TABLE `knowledge_articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `reading_time_minutes` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `status` enum('draft','review','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `featured_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `view_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(320) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_articles`
--

INSERT INTO `knowledge_articles` (`id`, `category_id`, `language`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `reading_time_minutes`, `status`, `is_featured`, `featured_order`, `view_count`, `created_by`, `reviewed_by`, `published_at`, `seo_title`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 3, 'en', 'NISAB', 'Zakat is not only supposed to help certain categories of individuals according to the Quran (Surah At-Tawbah 9:60) where Allah says:', NULL, 'Methodology and scholar note\r\n\r\nThis tool estimates Zakat using a 2.5% rate on net zakatable wealth held for one lunar year. It shows both the gold nisab (85g) and silver nisab (595g) in your selected currency using live metal rates derived from ounce prices. Opinions differ on what assets are zakatable, how business stock is valued, and which debts may be deducted. Consult a qualified scholar or ZSMI before relying on this estimate for a formal ruling.', 'assets/uploads/knowledge/article-1791967645-6f4724b5.webp', 5, 'published', 1, 0, 0, 2, NULL, NULL, NULL, NULL, '2026-10-13 08:31:31', '2026-10-14 08:47:25'),
(2, 4, 'en', 'Gold and Silver', 'Zakat is not only supposed to help certain categories of individuals according to the Quran (Surah At-Tawbah 9:60) where Allah says:-1791881538', NULL, 'Methodology and scholar note\r\n\r\nThis tool estimates Zakat using a 2.5% rate on net zakatable wealth held for one lunar year. It shows both the gold nisab (85g) and silver nisab (595g) in your selected currency using live metal rates derived from ounce prices. Opinions differ on what assets are zakatable, how business stock is valued, and which debts may be deducted. Consult a qualified scholar or ZSMI before relying on this estimate for a formal ruling.', 'assets/uploads/knowledge/article-1791881538-05d1f136.png', 5, 'published', 0, 0, 0, 2, NULL, NULL, NULL, NULL, '2026-10-13 08:52:18', '2026-10-13 08:52:18'),
(3, 5, 'en', 'Business', 'understanding business in zakkat', NULL, 'Included sections\r\n\r\nCash in hand, savings, and converted foreign currency balances\r\nBusiness stock, business cash, and qualifying investments\r\nGold and silver entered in grams with live per-gram conversion\r\nRecoverable receivables and immediate debts you may deduct', 'assets/uploads/knowledge/article-1791966367-1d21da02.png', 3, 'published', 1, 0, 0, 2, NULL, NULL, NULL, NULL, '2026-10-13 09:04:25', '2026-10-14 08:26:07'),
(4, 6, 'en', 'Agriculture', 'understand Agriculture obliganition', NULL, 'What is Agricultural Zakat? (Ushr Explained Simply)\r\nAgricultural Zakat is called Ushr in Arabic. The word \"Ushr\" means \"one-tenth.\" It is the Zakat you pay on crops, grains, fruits, and other agricultural produce that you grow on your land.\r\n\r\nThink of it as a small share of your harvest that goes to the poor and needy in your community. When your fields produce food, a portion of that blessing is set aside for those who are less fortunate.\r\n\r\nUnlike the regular 2.5% Zakat on savings and gold, agricultural Zakat is either 5% or 10% depending on how you water your crops. And unlike regular Zakat, you do not need to wait for a full year - it is due at each harvest.', 'assets/uploads/knowledge/article-1791967408-1fb9aa01.webp', 5, 'published', 1, 0, 0, 2, NULL, NULL, NULL, NULL, '2026-10-13 11:31:34', '2026-10-14 08:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_sources`
--

CREATE TABLE `knowledge_article_sources` (
  `id` int(10) UNSIGNED NOT NULL,
  `article_id` int(10) UNSIGNED NOT NULL,
  `source_type` enum('quran','hadith','fiqh_book','scholar','institution','website','other') NOT NULL DEFAULT 'other',
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `reference_text` varchar(500) DEFAULT NULL,
  `source_url` varchar(1000) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_article_translations`
--

CREATE TABLE `knowledge_article_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `article_id` int(10) UNSIGNED NOT NULL,
  `language_code` enum('en','ha','ar') NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(320) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_categories`
--

CREATE TABLE `knowledge_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_categories`
--

INSERT INTO `knowledge_categories` (`id`, `name`, `slug`, `description`, `icon`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Zakat Basics', 'zakat-basics', 'Fundamental concepts, conditions and principles of Zakat.', 'book-open', 1, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(2, 'Zakat on Wealth', 'zakat-on-wealth', 'General guidance concerning qualifying wealth.', 'wallet', 2, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(3, 'Cash & Savings', 'cash-and-savings', 'Educational resources about cash and savings.', 'banknote', 3, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(4, 'Gold & Silver', 'gold-and-silver', 'Resources concerning gold, silver and Nisab.', 'coins', 4, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(5, 'Business Zakat', 'business-zakat', 'Educational resources about business and commercial wealth.', 'briefcase', 5, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(6, 'Agriculture', 'agriculture', 'Resources concerning agricultural produce.', 'wheat', 6, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(7, 'Livestock', 'livestock', 'Resources concerning livestock Zakat.', 'paw-print', 7, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(8, 'Fiqh & Methodology', 'fiqh-methodology', 'Scholarly methodology and source-based Zakat guidance.', 'scale', 8, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `metal_prices`
--

CREATE TABLE `metal_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `metal` enum('gold','silver') NOT NULL,
  `price_per_gram` decimal(18,4) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `source_name` varchar(255) DEFAULT NULL,
  `source_reference` varchar(500) DEFAULT NULL,
  `effective_from` datetime NOT NULL,
  `effective_to` datetime DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `methodologies`
--

CREATE TABLE `methodologies` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `methodologies`
--

INSERT INTO `methodologies` (`id`, `code`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'maliki', 'Mālikī', 'Mālikī methodology', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(2, 'hanafi', 'Ḥanafī', 'Ḥanafī methodology', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(3, 'shafii', 'Shāfiʿī', 'Shāfiʿī methodology', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(4, 'hanbali', 'Ḥanbalī', 'Ḥanbalī methodology', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `motivations`
--

CREATE TABLE `motivations` (
  `id` int(10) UNSIGNED NOT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `type` enum('Quran','Hadith') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `reference_text` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nisab_settings`
--

CREATE TABLE `nisab_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `methodology_id` int(10) UNSIGNED DEFAULT NULL,
  `metal` enum('gold','silver') NOT NULL,
  `threshold_grams` decimal(12,4) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `valuation_price` decimal(18,4) DEFAULT NULL,
  `effective_from` datetime NOT NULL,
  `effective_to` datetime DEFAULT NULL,
  `source_reference` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nisab_settings`
--

INSERT INTO `nisab_settings` (`id`, `methodology_id`, `metal`, `threshold_grams`, `currency_code`, `valuation_price`, `effective_from`, `effective_to`, `source_reference`, `status`, `created_at`) VALUES
(1, 1, 'gold', 85.0000, 'NGN', NULL, '2026-10-12 15:55:33', NULL, 'Default system structure; valuation requires approved current source.', 'active', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `organization_type` enum('zakat_institution','ngo','foundation','company','government','other') NOT NULL DEFAULT 'other',
  `registration_number` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Nigeria',
  `logo` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive','pending','suspended') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organization_users`
--

CREATE TABLE `organization_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `organization_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `organization_role` enum('owner','admin','reviewer','finance_officer','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_providers`
--

CREATE TABLE `payment_providers` (
  `id` int(10) UNSIGNED NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `provider_code` varchar(50) NOT NULL,
  `provider_type` enum('online','bank','offline') NOT NULL,
  `public_key` varchar(500) DEFAULT NULL,
  `secret_key` varchar(500) DEFAULT NULL,
  `webhook_secret` varchar(500) DEFAULT NULL,
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_providers`
--

INSERT INTO `payment_providers` (`id`, `provider_name`, `provider_code`, `provider_type`, `public_key`, `secret_key`, `webhook_secret`, `configuration`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bank Transfer', 'bank_transfer', 'bank', NULL, NULL, NULL, NULL, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(2, 'Offline Payment', 'offline', 'offline', NULL, NULL, NULL, NULL, 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(3, 'Paystack', 'paystack', 'online', NULL, NULL, NULL, NULL, 'inactive', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(4, 'Flutterwave', 'flutterwave', 'online', NULL, NULL, NULL, NULL, 'inactive', '2026-10-12 14:55:33', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `payment_reviews`
--

CREATE TABLE `payment_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `action` enum('submitted','reviewed','approved','rejected','requested_information') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `payment_id` int(10) UNSIGNED NOT NULL,
  `provider_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_reference` varchar(150) DEFAULT NULL,
  `gateway_reference` varchar(150) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `status` enum('initiated','pending','successful','failed','reversed') NOT NULL DEFAULT 'initiated',
  `request_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_payload`)),
  `response_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_payload`)),
  `initiated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `permission_key`, `description`, `created_at`) VALUES
(1, 'Manage Users', 'users.manage', 'Create, update and manage users', '2026-10-12 14:55:33'),
(2, 'View Users', 'users.view', 'View user records', '2026-10-12 14:55:33'),
(3, 'Manage Knowledge', 'knowledge.manage', 'Create, update and manage knowledge articles', '2026-10-12 14:55:33'),
(4, 'Publish Knowledge', 'knowledge.publish', 'Publish reviewed knowledge articles', '2026-10-12 14:55:33'),
(5, 'Manage Fatwa', 'fatwa.manage', 'Manage Fatwa records', '2026-10-12 14:55:33'),
(6, 'Review Fatwa', 'fatwa.review', 'Review Fatwa content', '2026-10-12 14:55:33'),
(7, 'Manage Calculations', 'calculations.manage', 'View and manage calculations', '2026-10-12 14:55:33'),
(8, 'Manage Payments', 'payments.manage', 'Manage Zakat payments', '2026-10-12 14:55:33'),
(9, 'Review Payments', 'payments.review', 'Review and approve payments', '2026-10-12 14:55:33'),
(10, 'Manage Certificates', 'certificates.manage', 'Generate and manage certificates', '2026-10-12 14:55:33'),
(11, 'View Reports', 'reports.view', 'View system reports', '2026-10-12 14:55:33'),
(12, 'Manage Organizations', 'organizations.manage', 'Manage institutional organizations', '2026-10-12 14:55:33'),
(13, 'Manage Beneficiaries', 'beneficiaries.manage', 'Manage beneficiaries', '2026-10-12 14:55:33'),
(14, 'Manage Distribution', 'distribution.manage', 'Manage Zakat distribution', '2026-10-12 14:55:33'),
(15, 'View Audit Logs', 'audit.view', 'View system audit records', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashed_validator` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `calculation_id` int(10) UNSIGNED NOT NULL,
  `report_no` varchar(60) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `report_format` enum('html','pdf') NOT NULL DEFAULT 'html',
  `status` enum('generated','archived') NOT NULL DEFAULT 'generated',
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `role_key` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `role_key`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'System administrator', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(2, 'User', 'user', 'Normal Zakat platform user', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(3, 'Reviewer', 'reviewer', 'Reviews platform content and records', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(4, 'Scholar', 'scholar', 'Qualified reviewer for Islamic knowledge and Fatwa content', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(5, 'Finance Officer', 'finance_officer', 'Handles payment and financial review', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(6, 'Organization Admin', 'organization_admin', 'Manages an institutional organization account', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(7, 'Super Admin', 'super_admin', 'Highest level system administration', 'active', '2026-10-12 14:55:33', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'ZAKKATPRO', 'string', 'Application name', NULL, '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(2, 'default_language', 'en', 'string', 'Default platform language', NULL, '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(3, 'default_currency', 'NGN', 'string', 'Default platform currency', NULL, '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(4, 'zakat_rate', '2.5', 'number', 'Default rate used by applicable Zakat calculations', NULL, '2026-10-12 14:55:33', '2026-10-12 14:55:33'),
(5, 'default_methodology', 'maliki', 'string', 'Default methodology', NULL, '2026-10-12 14:55:33', '2026-10-12 14:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `user_type` enum('individual','organization','institution') NOT NULL DEFAULT 'individual',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `full_name`, `email`, `password`, `phone`, `address`, `language`, `user_type`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'Abdulganiyu Tanimu Hassan', 'abdulganiyutanimu3321@gmail.com', '$2y$10$NG0.bhYMbBCQcHKEoTuqWuvzpSJlLIaXifHVAQCWz61ciKwX911CK', NULL, NULL, 'en', 'individual', 'active', NULL, '2026-10-13 07:38:53', '2026-10-13 07:38:53'),
(2, 1, 'System Administrator', 'admin@zakatpro.com', '$2y$10$cDk.GOiBVzAgM.LsTo6zUuoI.U.a8cW3lcNm9e3hPi4MruTVzTWh.', NULL, NULL, 'en', 'individual', 'active', NULL, '2026-10-13 08:08:35', '2026-10-13 08:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `user_questions`
--

CREATE TABLE `user_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `language` enum('en','ha','ar') NOT NULL DEFAULT 'en',
  `question` longtext NOT NULL,
  `privacy_mode` enum('public','anonymous','private') DEFAULT 'private',
  `reply` longtext DEFAULT NULL,
  `workflow_status` enum('pending','under_review','answered','published','rejected') NOT NULL DEFAULT 'pending',
  `answered_by` int(10) UNSIGNED DEFAULT NULL,
  `answered_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_questions`
--

INSERT INTO `user_questions` (`id`, `user_id`, `full_name`, `email`, `phone`, `country`, `category_id`, `category`, `language`, `question`, `privacy_mode`, `reply`, `workflow_status`, `answered_by`, `answered_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Abdulganiyu Tanimu Hassan', 'abdulganiyutanimu3312@gmail.com', '+2348100641888', 'Nigeria', NULL, 'Business & Trade', 'en', 'ina iya bawa yaron shagona zakka?', 'public', 'ehh kana iya bawa yaron shagon zakka saboda baya daga mutanen da allah ya dora maka cidasu kamar uwa da uba da sauran su', 'answered', 2, '2026-10-14 15:11:28', '2026-10-14 13:47:14', '2026-10-14 14:11:28'),
(2, NULL, 'Ashafa abubakar Aliyu', 'abdulganiyutanimu3321@gmail.com', '+2348036525210', 'Nigeria', NULL, 'Zakat Recipients', 'en', 'shin ina iya bawa maihaifana zakka ?', 'private', NULL, 'pending', NULL, NULL, '2026-10-14 13:47:31', '2026-10-14 13:47:31'),
(3, NULL, 'nazeem', 'nazee@gmail.com', '+2348100641888', 'Nigeria', 6, 'Livestock', 'ha', 'ana fidda dawaki zakka?', 'private', 'babu zakka akan dawaki', 'published', 2, '2026-10-14 17:52:21', '2026-10-14 16:31:07', '2026-10-14 16:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zakat_calculations`
--

CREATE TABLE `zakat_calculations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reference_no` varchar(60) NOT NULL,
  `methodology_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `calculation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `cash` decimal(18,2) NOT NULL DEFAULT 0.00,
  `gold_grams` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `silver_grams` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `business_assets` decimal(18,2) NOT NULL DEFAULT 0.00,
  `property_assets` decimal(18,2) NOT NULL DEFAULT 0.00,
  `agriculture_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `livestock_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `investment_assets` decimal(18,2) NOT NULL DEFAULT 0.00,
  `other_assets` decimal(18,2) NOT NULL DEFAULT 0.00,
  `debts` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_wealth` decimal(18,2) NOT NULL DEFAULT 0.00,
  `nisab_basis` enum('gold','silver','custom') NOT NULL DEFAULT 'gold',
  `nisab_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `gold_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `silver_price` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `zakat_rate` decimal(6,3) NOT NULL DEFAULT 2.500,
  `eligible` tinyint(1) NOT NULL DEFAULT 0,
  `zakat_due` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','calculated','saved','cancelled') NOT NULL DEFAULT 'calculated',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_v2` varchar(30) NOT NULL DEFAULT 'calculated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zakat_category_breakdown`
--

CREATE TABLE `zakat_category_breakdown` (
  `id` int(10) UNSIGNED NOT NULL,
  `calculation_id` int(10) UNSIGNED NOT NULL,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `gross_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(18,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `zakat_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zakat_funds`
--

CREATE TABLE `zakat_funds` (
  `id` int(10) UNSIGNED NOT NULL,
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `fund_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `target_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `current_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zakat_payments`
--

CREATE TABLE `zakat_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `calculation_id` int(10) UNSIGNED NOT NULL,
  `report_id` int(10) UNSIGNED DEFAULT NULL,
  `payment_reference` varchar(80) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'NGN',
  `payment_method` enum('online','bank_transfer','offline') NOT NULL,
  `payment_provider_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_reference` varchar(150) DEFAULT NULL,
  `offline_receipt` varchar(500) DEFAULT NULL,
  `status` enum('initiated','pending','processing','under_review','confirmed','approved','rejected','failed','cancelled','refunded') NOT NULL DEFAULT 'initiated',
  `payment_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `idx_activity_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_date` (`created_at`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beneficiary_code` (`beneficiary_code`),
  ADD KEY `fk_beneficiary_org` (`organization_id`),
  ADD KEY `idx_beneficiary_status` (`verification_status`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_no` (`certificate_no`),
  ADD UNIQUE KEY `verification_code` (`verification_code`),
  ADD KEY `fk_certificate_user` (`user_id`),
  ADD KEY `fk_certificate_payment` (`payment_id`),
  ADD KEY `fk_certificate_calculation` (`calculation_id`),
  ADD KEY `fk_certificate_issuer` (`issued_by`),
  ADD KEY `idx_certificate_status` (`status`),
  ADD KEY `idx_certificate_verification` (`verification_code`);

--
-- Indexes for table `certificate_verifications`
--
ALTER TABLE `certificate_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_certificate_verification` (`certificate_id`),
  ADD KEY `idx_certificate_verification_code` (`verification_code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `distributions`
--
ALTER TABLE `distributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distribution_reference` (`distribution_reference`),
  ADD KEY `fk_distribution_fund` (`fund_id`),
  ADD KEY `fk_distribution_approver` (`approved_by`),
  ADD KEY `fk_distribution_currency` (`currency_code`);

--
-- Indexes for table `distribution_items`
--
ALTER TABLE `distribution_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_distribution_item_distribution` (`distribution_id`),
  ADD KEY `fk_distribution_item_beneficiary` (`beneficiary_id`),
  ADD KEY `fk_distribution_item_currency` (`currency_code`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fatwas`
--
ALTER TABLE `fatwas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_fatwa_creator` (`created_by`),
  ADD KEY `fk_fatwa_reviewer` (`reviewed_by`),
  ADD KEY `idx_fatwa_category` (`category_id`),
  ADD KEY `idx_fatwa_status` (`status`);
ALTER TABLE `fatwas` ADD FULLTEXT KEY `ft_fatwa_search` (`question`,`answer`);

--
-- Indexes for table `fatwa_categories`
--
ALTER TABLE `fatwa_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `fatwa_reviews`
--
ALTER TABLE `fatwa_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fatwa_review_user` (`reviewer_id`),
  ADD KEY `idx_fatwa_reviews` (`fatwa_id`);

--
-- Indexes for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_article_creator` (`created_by`),
  ADD KEY `fk_article_reviewer` (`reviewed_by`),
  ADD KEY `idx_article_category` (`category_id`),
  ADD KEY `idx_article_status` (`status`),
  ADD KEY `idx_article_featured` (`is_featured`,`featured_order`);
ALTER TABLE `knowledge_articles` ADD FULLTEXT KEY `ft_knowledge_article_search` (`title`,`excerpt`,`content`);

--
-- Indexes for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_article_sources` (`article_id`);

--
-- Indexes for table `knowledge_article_translations`
--
ALTER TABLE `knowledge_article_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_article_translation` (`article_id`,`language_code`),
  ADD UNIQUE KEY `uq_article_translation_slug` (`slug`);

--
-- Indexes for table `knowledge_categories`
--
ALTER TABLE `knowledge_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_knowledge_category_status` (`status`);

--
-- Indexes for table `metal_prices`
--
ALTER TABLE `metal_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_metal_price` (`metal`,`currency_code`,`effective_from`),
  ADD KEY `fk_metal_price_currency` (`currency_code`);

--
-- Indexes for table `methodologies`
--
ALTER TABLE `methodologies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `motivations`
--
ALTER TABLE `motivations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nisab_settings`
--
ALTER TABLE `nisab_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nisab_methodology` (`methodology_id`),
  ADD KEY `fk_nisab_currency` (`currency_code`),
  ADD KEY `idx_nisab_active` (`metal`,`currency_code`,`effective_from`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_organizations_type` (`organization_type`),
  ADD KEY `idx_organizations_status` (`status`);

--
-- Indexes for table `organization_users`
--
ALTER TABLE `organization_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_organization_user` (`organization_id`,`user_id`),
  ADD KEY `fk_org_users_user` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_password_reset_user` (`user_id`),
  ADD KEY `idx_password_reset_expiry` (`expires_at`);

--
-- Indexes for table `payment_providers`
--
ALTER TABLE `payment_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_code` (`provider_code`);

--
-- Indexes for table `payment_reviews`
--
ALTER TABLE `payment_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_review_reviewer` (`reviewer_id`),
  ADD KEY `idx_payment_reviews_payment` (`payment_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaction_provider` (`provider_id`),
  ADD KEY `idx_transaction_payment` (`payment_id`),
  ADD KEY `idx_gateway_reference` (`gateway_reference`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `idx_remember_user` (`user_id`),
  ADD KEY `idx_remember_selector` (`selector`),
  ADD KEY `idx_remember_expires` (`expires_at`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_no` (`report_no`),
  ADD KEY `idx_reports_user` (`user_id`),
  ADD KEY `idx_reports_calculation` (`calculation_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `role_key` (`role_key`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_permission` (`role_id`,`permission_id`),
  ADD KEY `fk_role_permissions_permission` (`permission_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `fk_setting_user` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role_id`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `idx_users_type` (`user_type`);

--
-- Indexes for table `user_questions`
--
ALTER TABLE `user_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_question_user` (`user_id`),
  ADD KEY `fk_user_question_category` (`category_id`),
  ADD KEY `fk_user_question_answerer` (`answered_by`),
  ADD KEY `idx_user_questions_status` (`workflow_status`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_session_user` (`user_id`),
  ADD KEY `idx_session_expiry` (`expires_at`);

--
-- Indexes for table `zakat_calculations`
--
ALTER TABLE `zakat_calculations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`),
  ADD KEY `fk_calculation_methodology` (`methodology_id`),
  ADD KEY `fk_calculation_currency` (`currency_code`),
  ADD KEY `idx_calculation_user` (`user_id`),
  ADD KEY `idx_calculation_date` (`calculation_date`),
  ADD KEY `idx_calculation_status` (`status`);

--
-- Indexes for table `zakat_category_breakdown`
--
ALTER TABLE `zakat_category_breakdown`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_breakdown_calculation` (`calculation_id`),
  ADD KEY `idx_breakdown_category` (`category_code`);

--
-- Indexes for table `zakat_funds`
--
ALTER TABLE `zakat_funds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fund_organization` (`organization_id`),
  ADD KEY `fk_fund_currency` (`currency_code`);

--
-- Indexes for table `zakat_payments`
--
ALTER TABLE `zakat_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD KEY `fk_payment_calculation` (`calculation_id`),
  ADD KEY `fk_payment_report` (`report_id`),
  ADD KEY `fk_payment_provider` (`payment_provider_id`),
  ADD KEY `fk_payment_reviewer` (`reviewed_by`),
  ADD KEY `idx_payment_user` (`user_id`),
  ADD KEY `idx_payment_status` (`status`),
  ADD KEY `idx_payment_reference` (`payment_reference`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_verifications`
--
ALTER TABLE `certificate_verifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `distributions`
--
ALTER TABLE `distributions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distribution_items`
--
ALTER TABLE `distribution_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fatwas`
--
ALTER TABLE `fatwas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fatwa_categories`
--
ALTER TABLE `fatwa_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fatwa_reviews`
--
ALTER TABLE `fatwa_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_article_translations`
--
ALTER TABLE `knowledge_article_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_categories`
--
ALTER TABLE `knowledge_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `metal_prices`
--
ALTER TABLE `metal_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `methodologies`
--
ALTER TABLE `methodologies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `motivations`
--
ALTER TABLE `motivations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nisab_settings`
--
ALTER TABLE `nisab_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organization_users`
--
ALTER TABLE `organization_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_providers`
--
ALTER TABLE `payment_providers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_reviews`
--
ALTER TABLE `payment_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_questions`
--
ALTER TABLE `user_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zakat_calculations`
--
ALTER TABLE `zakat_calculations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zakat_category_breakdown`
--
ALTER TABLE `zakat_category_breakdown`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zakat_funds`
--
ALTER TABLE `zakat_funds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zakat_payments`
--
ALTER TABLE `zakat_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `fk_beneficiary_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_certificate_calculation` FOREIGN KEY (`calculation_id`) REFERENCES `zakat_calculations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_certificate_issuer` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_certificate_payment` FOREIGN KEY (`payment_id`) REFERENCES `zakat_payments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_certificate_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `certificate_verifications`
--
ALTER TABLE `certificate_verifications`
  ADD CONSTRAINT `fk_certificate_verification` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `distributions`
--
ALTER TABLE `distributions`
  ADD CONSTRAINT `fk_distribution_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_distribution_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_distribution_fund` FOREIGN KEY (`fund_id`) REFERENCES `zakat_funds` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `distribution_items`
--
ALTER TABLE `distribution_items`
  ADD CONSTRAINT `fk_distribution_item_beneficiary` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_distribution_item_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_distribution_item_distribution` FOREIGN KEY (`distribution_id`) REFERENCES `distributions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fatwas`
--
ALTER TABLE `fatwas`
  ADD CONSTRAINT `fk_fatwa_category` FOREIGN KEY (`category_id`) REFERENCES `fatwa_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fatwa_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fatwa_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fatwa_reviews`
--
ALTER TABLE `fatwa_reviews`
  ADD CONSTRAINT `fk_fatwa_review_fatwa` FOREIGN KEY (`fatwa_id`) REFERENCES `fatwas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fatwa_review_user` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD CONSTRAINT `fk_article_category` FOREIGN KEY (`category_id`) REFERENCES `knowledge_categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_article_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_article_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `knowledge_article_sources`
--
ALTER TABLE `knowledge_article_sources`
  ADD CONSTRAINT `fk_article_source` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `knowledge_article_translations`
--
ALTER TABLE `knowledge_article_translations`
  ADD CONSTRAINT `fk_article_translation` FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `metal_prices`
--
ALTER TABLE `metal_prices`
  ADD CONSTRAINT `fk_metal_price_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE;

--
-- Constraints for table `nisab_settings`
--
ALTER TABLE `nisab_settings`
  ADD CONSTRAINT `fk_nisab_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nisab_methodology` FOREIGN KEY (`methodology_id`) REFERENCES `methodologies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `organization_users`
--
ALTER TABLE `organization_users`
  ADD CONSTRAINT `fk_org_users_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_org_users_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_reviews`
--
ALTER TABLE `payment_reviews`
  ADD CONSTRAINT `fk_payment_review_payment` FOREIGN KEY (`payment_id`) REFERENCES `zakat_payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_review_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_transaction_payment` FOREIGN KEY (`payment_id`) REFERENCES `zakat_payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_provider` FOREIGN KEY (`provider_id`) REFERENCES `payment_providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `fk_remember_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_calculation` FOREIGN KEY (`calculation_id`) REFERENCES `zakat_calculations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `fk_setting_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_questions`
--
ALTER TABLE `user_questions`
  ADD CONSTRAINT `fk_user_question_answerer` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_question_category` FOREIGN KEY (`category_id`) REFERENCES `fatwa_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_question_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zakat_calculations`
--
ALTER TABLE `zakat_calculations`
  ADD CONSTRAINT `fk_calculation_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calculation_methodology` FOREIGN KEY (`methodology_id`) REFERENCES `methodologies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calculation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `zakat_category_breakdown`
--
ALTER TABLE `zakat_category_breakdown`
  ADD CONSTRAINT `fk_breakdown_calculation` FOREIGN KEY (`calculation_id`) REFERENCES `zakat_calculations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zakat_funds`
--
ALTER TABLE `zakat_funds`
  ADD CONSTRAINT `fk_fund_currency` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fund_organization` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `zakat_payments`
--
ALTER TABLE `zakat_payments`
  ADD CONSTRAINT `fk_payment_calculation` FOREIGN KEY (`calculation_id`) REFERENCES `zakat_calculations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_provider` FOREIGN KEY (`payment_provider_id`) REFERENCES `payment_providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
