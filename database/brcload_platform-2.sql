-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 08:33 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brcload_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `analysis_plans`
--

CREATE TABLE `analysis_plans` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `analysis_type` varchar(100) NOT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `test_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_parameters`)),
  `laboratory` varchar(255) DEFAULT NULL,
  `target_completion` date DEFAULT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analysis_results`
--

CREATE TABLE `analysis_results` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `lot_number` varchar(50) DEFAULT NULL,
  `sample_date` date DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`results`)),
  `compliance_status` enum('COMPLIANT','NON_COMPLIANT','PENDING') DEFAULT 'PENDING',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `theme_color` varchar(7) DEFAULT '#007bff',
  `plan_type` enum('basic','premium','enterprise') DEFAULT 'basic',
  `status` enum('trial','active','suspended','cancelled') DEFAULT 'trial',
  `trial_end_date` date DEFAULT NULL,
  `subscription_end_date` date DEFAULT NULL,
  `max_users` int(11) DEFAULT 5,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_code` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `domain`, `name`, `company_name`, `subdomain`, `contact_name`, `contact_email`, `contact_phone`, `billing_address`, `logo_url`, `theme_color`, `plan_type`, `status`, `trial_end_date`, `subscription_end_date`, `max_users`, `settings`, `created_at`, `updated_at`, `company_code`, `logo`) VALUES
(1, 'demo', 'Demo Company', 'Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', '+1-555-0123', NULL, NULL, '#007bff', 'premium', 'active', NULL, NULL, 15, NULL, '2025-07-24 11:39:15', '2025-07-30 11:52:47', 'DEMO001', NULL),
(2, 'test', 'Test Manufacturing', 'Test Corporation', 'test', 'Jane Smith', 'jane@testcorp.com', '+1-555-0456', NULL, NULL, '#28a745', 'basic', 'trial', NULL, NULL, 5, NULL, '2025-07-24 11:39:15', '2025-07-30 11:52:47', 'TEST001', NULL),
(3, 'company1', 'Company 1', 'Enterprise Solutions', 'enterprise', 'Mike Johnson', 'mike@enterprise.com', '+1-555-0789', NULL, NULL, '#6f42c1', 'enterprise', 'active', NULL, NULL, 50, NULL, '2025-07-24 11:39:15', '2025-07-30 11:52:47', 'COMP001', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_data`
--

CREATE TABLE `company_data` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `data_type` varchar(50) NOT NULL,
  `data_key` varchar(255) NOT NULL,
  `data_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_value`)),
  `data_id` varchar(100) DEFAULT NULL,
  `data_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data_content`)),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_data`
--

INSERT INTO `company_data` (`id`, `company_id`, `module_id`, `data_type`, `data_key`, `data_value`, `data_id`, `data_content`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'risk_assessment', '', NULL, 'RA-2024-001', '{\"risk_description\": \"Bacterial contamination in raw material storage\", \"likelihood\": 3, \"impact\": 4, \"risk_score\": 12, \"status\": \"approved\"}', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(2, 1, 1, 'risk_assessment', '', NULL, 'RA-2024-002', '{\"risk_description\": \"Unauthorized access to production area\", \"likelihood\": 2, \"impact\": 3, \"risk_score\": 6, \"status\": \"pending\"}', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_module_subscriptions`
--

CREATE TABLE `company_module_subscriptions` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` date DEFAULT NULL,
  `status` enum('active','cancelled','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_module_subscriptions`
--

INSERT INTO `company_module_subscriptions` (`id`, `company_id`, `module_id`, `subscribed_at`, `expires_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-07-24 11:39:15', '2025-01-24', 'active', '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(3, 2, 1, '2025-07-24 11:39:15', '2024-12-24', 'active', '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(4, 3, 1, '2025-07-24 11:39:15', '2025-06-24', 'active', '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(6, 3, 3, '2025-07-24 11:39:15', '2025-06-24', 'active', '2025-07-24 11:39:15', '2025-07-24 11:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_processes`
--

CREATE TABLE `company_processes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `process_name` varchar(255) NOT NULL,
  `process_code` varchar(100) DEFAULT NULL,
  `process_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`process_data`)),
  `status` enum('draft','active','paused','completed','archived') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `assigned_manager` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_users`
--

CREATE TABLE `company_users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` enum('owner','admin','manager','user') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_users`
--

INSERT INTO `company_users` (`id`, `company_id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin@democompany.com', '$2y$10$Y9ddWO6lX2TeHgvxx27K4.19oSSe603PteQhxB.FXYondCXgkan3.', 'John', 'Doe', 'owner', 'active', '2025-07-30 13:15:31', '2025-07-24 11:39:15', '2025-07-30 13:15:31'),
(2, 1, 'manager', 'manager@democompany.com', '123456', 'Sarah', 'Wilson', 'manager', 'active', NULL, '2025-07-24 11:39:15', '2025-07-30 13:14:59'),
(3, 1, 'user1', 'user1@democompany.com', '123456', 'Robert', 'Brown', 'user', 'active', NULL, '2025-07-24 11:39:15', '2025-07-30 13:14:59'),
(4, 2, 'admin', 'admin@testcorp.com', '$2y$10$1xVOd9BN3y3NpF6/D4F0/umISu4jzsorDDEANgYFKkNA/VxwnQ2vS', 'Jane', 'Smith', 'owner', 'active', NULL, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(5, 2, 'user1', 'user1@testcorp.com', '$2y$10$1xVOd9BN3y3NpF6/D4F0/umISu4jzsorDDEANgYFKkNA/VxwnQ2vS', 'Tom', 'Davis', 'user', 'active', NULL, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(6, 3, 'admin', 'admin@enterprise.com', '$2y$10$1xVOd9BN3y3NpF6/D4F0/umISu4jzsorDDEANgYFKkNA/VxwnQ2vS', 'Mike', 'Johnson', 'owner', 'active', NULL, '2025-07-24 11:39:15', '2025-07-24 11:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `culture_surveys`
--

CREATE TABLE `culture_surveys` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `survey_name` varchar(255) NOT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`questions`)),
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `survey_period` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `culture_survey_responses`
--

CREATE TABLE `culture_survey_responses` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `employee_demographics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_demographics`)),
  `responses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`responses`)),
  `total_score` int(11) DEFAULT NULL,
  `completion_time` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lot_traceability`
--

CREATE TABLE `lot_traceability` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `lot_number` varchar(50) NOT NULL,
  `raw_material_lot` varchar(50) DEFAULT NULL,
  `raw_material_weight` decimal(10,2) DEFAULT NULL,
  `raw_material_date` date DEFAULT NULL,
  `fumigation_start` datetime DEFAULT NULL,
  `fumigation_end` datetime DEFAULT NULL,
  `sizing_date` date DEFAULT NULL,
  `dark_room_check` date DEFAULT NULL,
  `washing_weight` decimal(10,2) DEFAULT NULL,
  `production_dates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`production_dates`)),
  `labeling_date` date DEFAULT NULL,
  `packaging_date` date DEFAULT NULL,
  `loading_date` date DEFAULT NULL,
  `health_certificate` varchar(100) DEFAULT NULL,
  `plant_certificate` varchar(100) DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `status` enum('active','completed','shipped') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketplace_modules`
--

CREATE TABLE `marketplace_modules` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `module_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'Custom',
  `price` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `is_base_module` tinyint(1) DEFAULT 0,
  `version` varchar(10) DEFAULT '1.0',
  `icon` varchar(50) DEFAULT 'puzzle-piece',
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','deprecated') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marketplace_modules`
--

INSERT INTO `marketplace_modules` (`id`, `name`, `module_name`, `module_code`, `description`, `category`, `price`, `currency`, `is_base_module`, `version`, `icon`, `cover_image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '', 'BRC Risk Assessment', 'brc_risk_assessment', 'Comprehensive BRC-compliant risk assessment module with automated workflows and compliance tracking.', 'BRC Compliance', 99.99, 'USD', 1, '2.1', 'shield-alt', NULL, 'published', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(3, '', 'Safety Management', 'safety_mgmt', 'Comprehensive safety management system with incident tracking, safety training, and compliance monitoring.', 'Safety & Health', 89.99, 'USD', 1, '1.5', 'hard-hat', NULL, 'published', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(4, '', 'Audit Management', 'audit_mgmt', 'Complete audit management system with planning, execution, and follow-up capabilities for all audit types.', 'Audit Management', 69.99, 'USD', 1, '1.3', 'clipboard-check', NULL, 'published', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(5, '', 'Document Control', 'document_control', 'Advanced document control system with version management, approval workflows, and distribution tracking.', 'Document Control', 49.99, 'USD', 1, '2.0', 'file-alt', NULL, 'published', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(6, '', 'Training Management', 'training_mgmt', 'Complete training management system with course creation, tracking, and competency assessment capabilities.', 'Training & Development', 59.99, 'USD', 1, '1.7', 'graduation-cap', NULL, 'published', 1, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(12, 'deneme222', 'deneme222', 'deneme222', 'denemeye yarıyor 2 defa', 'quality_control', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 11:45:13', '2025-07-26 11:45:13'),
(13, 'BRC Kalite Kontrol Planı v2.0', 'BRC Kalite Kontrol Planı v2.0', 'brc_kalite_kontrol_plan___v2_0', 'Fümigasyon, yıkama, seçim, aflatoksin kontrolü ve son kontrol süreçlerini kapsayan kapsamlı kalite kontrol sistemi', 'quality_control', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 11:45:21', '2025-07-26 11:45:21'),
(14, 'Ürün Spesifikasyon Yönetimi v2.0', 'Ürün Spesifikasyon Yönetimi v2.0', '__r__n_spesifikasyon_y__netimi_v2_0', 'TS 541 Kuru İncir standardı, fiziksel-kimyasal-mikrobiyolojik özellikler ve beslenme değerleri yönetimi', 'specifications', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 11:45:25', '2025-07-26 11:45:25'),
(15, 'Test Modülü 1753530335029', 'Test Modülü 1753530335029', 'test_mod__l___1753530335029', 'Otomatik test modülü', 'general', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 11:45:35', '2025-07-26 11:45:35'),
(16, 'Existing Module 2', 'Existing Module 2', 'existing_module_2', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 12:28:20', '2025-07-26 12:28:20'),
(17, 'deneme', 'deneme', 'deneme', '', 'Quality Management', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:13:41', '2025-07-26 16:13:41'),
(19, 'Debug Test Module', 'Debug Test Module', 'debug_test_module', 'Debug test modülü', 'quality_control', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:16:24', '2025-07-26 16:16:24'),
(21, 'Debug Test Module', 'Debug Test Module', 'debug_test_module_1753546774_1', 'Debug test modülü', 'quality_control', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:19:34', '2025-07-26 16:19:34'),
(22, 'deneme', 'deneme', 'deneme_1753546799_1', '', 'Quality Management', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:19:59', '2025-07-26 16:19:59'),
(23, 'deneme', 'deneme', 'deneme_1753546811_1', '', 'Quality Management', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:20:11', '2025-07-26 16:20:11'),
(24, 'Existing Module22', 'Existing Module22', 'existing_module22', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:21:11', '2025-07-26 16:21:11'),
(25, 'Existing Module23', 'Existing Module23', 'existing_module23', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:31:00', '2025-07-26 16:31:00'),
(26, 'Existing Module22', 'Existing Module22', 'existing_module22_1753548605_1', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 16:50:05', '2025-07-26 16:50:05'),
(27, 'Existing Module31', 'Existing Module31', 'existing_module31', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 17:02:18', '2025-07-26 17:02:18'),
(28, 'Existing Module222', 'Existing Module222', 'existing_module222', '', '', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 17:03:25', '2025-07-26 17:03:25'),
(29, 'Existing Module222', 'Existing Module222', 'existing_module222_1753549410_1', NULL, NULL, 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-26 17:03:30', '2025-07-29 14:01:18'),
(30, 'denemdeneme211', 'denemdeneme222', 'denemdeneme', 'tanimmm', 'Quality Management', 50.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-29 11:13:40', '2025-07-29 14:01:34'),
(31, 'denemetrial', 'denemetrial', 'denemetrial', '10', 'Safety & Health', 0.00, 'USD', 0, '1.0', 'puzzle-piece', NULL, 'published', 1, '2025-07-30 07:38:51', '2025-07-30 12:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `module_components`
--

CREATE TABLE `module_components` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `position_x` int(11) DEFAULT 0,
  `position_y` int(11) DEFAULT 0,
  `width` int(11) DEFAULT 300,
  `height` int(11) DEFAULT 200,
  `component_name` varchar(100) NOT NULL,
  `component_type` varchar(50) NOT NULL,
  `component_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`component_config`)),
  `component_props` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`component_props`)),
  `component_code` varchar(255) DEFAULT 'auto-generated',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `order_index` int(11) DEFAULT 0,
  `is_locked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_components`
--

INSERT INTO `module_components` (`id`, `module_id`, `position_x`, `position_y`, `width`, `height`, `component_name`, `component_type`, `component_config`, `component_props`, `component_code`, `config`, `order_index`, `is_locked`, `created_at`) VALUES
(1, 1, 0, 0, 300, 200, 'Risk Assessment Matrix', 'risk-matrix', NULL, NULL, 'risk_matrix_5x5', '{\"size\": \"5x5\", \"colors\": [\"#22c55e\", \"#f59e0b\", \"#ef4444\"]}', 1, 1, '2025-07-24 11:39:15'),
(2, 1, 0, 0, 300, 200, 'Risk Assessment Form', 'smart-form', NULL, NULL, 'risk_form', '{\"fields\": [\"risk_description\", \"likelihood\", \"impact\", \"location\"]}', 2, 1, '2025-07-24 11:39:15'),
(3, 1, 0, 0, 300, 200, 'Approval Workflow', 'approval-flow', NULL, NULL, 'risk_approval', '{\"steps\": [\"assessor\", \"manager\", \"quality_manager\"]}', 3, 1, '2025-07-24 11:39:15'),
(4, 1, 0, 0, 300, 200, 'Dashboard Overview', 'dashboard-grid', NULL, NULL, 'risk_dashboard', '{\"cards\": [\"total_risks\", \"completed\", \"pending\", \"compliance_score\"]}', 4, 1, '2025-07-24 11:39:15'),
(10, 29, 707, 86, 300, 200, 'Data Chart', 'chart', '{\"type\":\"line\",\"data\":{\"labels\":[],\"datasets\":[]}}', NULL, 'data_chart_1753797678', NULL, 0, 0, '2025-07-29 14:01:18'),
(11, 30, 50, 50, 280, 180, 'Quality Control Form', 'form', '{\"fields\":[{\"name\":\"process_step\",\"type\":\"text\",\"label\":\"Süreç Adımı\"}]}', NULL, 'quality_control_form_1753797694', NULL, 0, 0, '2025-07-29 14:01:34'),
(12, 30, 707, 10, 300, 200, 'Risk Assessment Matrix', 'risk-matrix', '{\"matrix_size\":\"5x5\",\"categories\":[\"Likelihood\",\"Impact\"]}', NULL, 'risk_assessment_matrix_1753797694', NULL, 0, 0, '2025-07-29 14:01:34'),
(13, 30, 50, 280, 250, 160, 'Approval Workflow', 'approval-flow', '{\"steps\":[\"Review\",\"Approve\",\"Publish\"]}', NULL, 'approval_workflow_1753797694', NULL, 0, 0, '2025-07-29 14:01:34'),
(15, 31, 593, 42, 350, 200, 'Quality Control Table', 'quality-control-table', '{\"headers\":[\"Process Step\",\"Control Point\",\"Criteria\",\"Responsible\"],\"rows\":[]}', NULL, 'quality_control_table_1753878661', NULL, 0, 0, '2025-07-30 12:31:01'),
(16, 31, 198, 235, 200, 120, 'KPI Card', 'kpi-card', '{\"title\":\"Key Metric\",\"value\":0,\"target\":100,\"unit\":\"%\"}', NULL, 'kpi_card_1753878661', NULL, 0, 0, '2025-07-30 12:31:01'),
(17, 31, 16, 328, 250, 160, 'Tab Panel', 'tab-panel', '{}', NULL, 'tab_panel_1753878661', NULL, 0, 0, '2025-07-30 12:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `module_workflows`
--

CREATE TABLE `module_workflows` (
  `id` int(11) NOT NULL,
  `workflow_name` varchar(255) NOT NULL,
  `module_id` int(11) NOT NULL,
  `workflow_steps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`workflow_steps`)),
  `approval_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`approval_levels`)),
  `current_step` int(11) DEFAULT 1,
  `status` enum('pending','in_progress','completed','rejected') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `workflow_data` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_admins`
--

CREATE TABLE `platform_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `platform_admins`
--

INSERT INTO `platform_admins` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@brcload.com', '123456', 'Platform', 'Admin', 'super_admin', 'active', '2025-07-30 12:39:07', '2025-07-24 11:39:15', '2025-07-30 12:39:07'),
(2, 'moderator', 'moderator@brcload.com', '$2y$10$1xVOd9BN3y3NpF6/D4F0/umISu4jzsorDDEANgYFKkNA/VxwnQ2vS', 'Content', 'Moderator', 'moderator', 'active', NULL, '2025-07-24 11:39:15', '2025-07-24 11:39:15'),
(3, 'testadmin', 'test@brcload.com', '123456', 'Test', 'Administrator', 'super_admin', 'active', NULL, '2025-07-26 07:58:52', '2025-07-26 07:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `process_audit_log`
--

CREATE TABLE `process_audit_log` (
  `id` int(11) NOT NULL,
  `process_execution_id` int(11) NOT NULL,
  `step_execution_id` int(11) DEFAULT NULL,
  `action_type` enum('process_started','step_completed','issue_reported','corrective_action','process_completed') NOT NULL,
  `performed_by` int(11) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `process_executions`
--

CREATE TABLE `process_executions` (
  `id` int(11) NOT NULL,
  `company_process_id` int(11) NOT NULL,
  `execution_name` varchar(255) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `current_step` int(11) DEFAULT 1,
  `total_steps` int(11) DEFAULT 0,
  `status` enum('pending','in_progress','completed','failed','on_hold','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `started_by` int(11) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `scheduled_completion` timestamp NULL DEFAULT NULL,
  `actual_completion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `process_issues`
--

CREATE TABLE `process_issues` (
  `id` int(11) NOT NULL,
  `step_execution_id` int(11) NOT NULL,
  `issue_type` enum('minor','major','critical') NOT NULL,
  `issue_category` varchar(100) DEFAULT NULL,
  `issue_description` text NOT NULL,
  `root_cause` text DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `preventive_action` text DEFAULT NULL,
  `responsible_person` int(11) DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `resolution_date` timestamp NULL DEFAULT NULL,
  `status` enum('open','in_progress','resolved','verified','closed') DEFAULT 'open',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `process_notifications`
--

CREATE TABLE `process_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `type` enum('step_due','step_overdue','process_complete','issue_created','issue_resolved','process_started') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `related_execution_id` int(11) DEFAULT NULL,
  `related_step_id` int(11) DEFAULT NULL,
  `related_issue_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `process_templates`
--

CREATE TABLE `process_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_code` varchar(100) NOT NULL,
  `brc_standard` varchar(100) DEFAULT NULL,
  `template_version` varchar(20) DEFAULT '1.0',
  `template_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`template_data`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `process_templates`
--

INSERT INTO `process_templates` (`id`, `template_name`, `template_code`, `brc_standard`, `template_version`, `template_data`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'BRC Food Safety Quality Control v9', 'brc_food_safety_v9', 'BRC Food Safety v9', '1.0', '{}', 1, 1, '2025-07-30 11:41:52', '2025-07-30 11:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

CREATE TABLE `product_specifications` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `crop_year` year(4) DEFAULT NULL,
  `physical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`physical_specs`)),
  `chemical_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chemical_specs`)),
  `microbiological_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`microbiological_specs`)),
  `nutritional_specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nutritional_specs`)),
  `status` enum('active','inactive','draft') DEFAULT 'draft',
  `version` varchar(20) DEFAULT '1.0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quality_control_plans`
--

CREATE TABLE `quality_control_plans` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `process_step` varchar(255) NOT NULL,
  `control_input` varchar(255) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `criteria` text DEFAULT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quality_control_plans`
--

INSERT INTO `quality_control_plans` (`id`, `company_id`, `process_step`, `control_input`, `frequency`, `criteria`, `responsible_person`, `corrective_action`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tedarikçiden seçilmiş ürün satın alma', 'Girdi Kontrolü', 'Her parti girişinde', 'Hammadde Giriş Kontrol Formundaki değerler', 'KALİTE-LABORATUVAR SORUMLUSU', 'Ürün İadesi', 'active', '2025-07-26 10:34:23', '2025-07-26 10:34:23'),
(2, 1, 'Fümigasyon', 'Kullanılan Fümigant Miktarı, Süresi, Basınç, Sıcaklık, Canlı Böcek Varlığı', 'Her Fümigasyonda', 'Talimatta istenen özelliklere uygunluk', 'TEDARİKÇİ FİRMA YETKİLİSİ', 'Yeniden fümigasyon işlemi', 'active', '2025-07-26 10:34:23', '2025-07-26 10:34:23'),
(3, 1, 'Yıkama', 'Tuz oranı', 'Her Yıkamada', 'Min 6, Max 8 bome derecesi', 'KALİTE-LABORATUVAR SORUMLUSU', 'Tuz ilavesi veya seyreltilmesi', 'active', '2025-07-26 10:34:23', '2025-07-26 10:34:23'),
(4, 1, 'Aflatoksinli İncir Seçimi', 'Aflatoksin', 'Her 5 telerde bir', 'UV lamba altında aflatoksin tespit edilmemesi', 'ÜRETİM SORUMLUSU, KARANLIK ODA SORUMLUSU', 'Yeniden aflatoksin seçimi', 'active', '2025-07-26 10:34:23', '2025-07-26 10:34:23'),
(5, 1, 'Son Kontrol', 'Bitmiş ürün Analiz formundaki kriterler (fiziksel)', 'Her partide', 'Limitlere uygunluk', 'GENEL KOORDİNATÖR, KALİTE-LAB. SORUMLUSU', 'Uygun olmayan ürünün pakete konulmaması', 'active', '2025-07-26 10:34:23', '2025-07-26 10:34:23'),
(6, 1, 'Tedarikçiden seçilmiş ürün satın alma', 'Girdi Kontrolü', 'Her parti girişinde', 'Hammadde Giriş Kontrol Formundaki değerler', 'KALİTE-LABORATUVAR SORUMLUSU', 'Ürün İadesi', 'active', '2025-07-26 11:09:12', '2025-07-26 11:09:12'),
(7, 1, 'Fümigasyon', 'Kullanılan Fümigant Miktarı, Süresi, Basınç, Sıcaklık, Canlı Böcek Varlığı', 'Her Fümigasyonda', 'Talimatta istenen özelliklere uygunluk', 'TEDARİKÇİ FİRMA YETKİLİSİ', 'Yeniden fümigasyon işlemi', 'active', '2025-07-26 11:09:12', '2025-07-26 11:09:12'),
(8, 1, 'Yıkama', 'Tuz oranı', 'Her Yıkamada', 'Min 6, Max 8 bome derecesi', 'KALİTE-LABORATUVAR SORUMLUSU', 'Tuz ilavesi veya seyreltilmesi', 'active', '2025-07-26 11:09:12', '2025-07-26 11:09:12'),
(9, 1, 'Aflatoksinli İncir Seçimi', 'Aflatoksin', 'Her 5 telerde bir', 'UV lamba altında aflatoksin tespit edilmemesi', 'ÜRETİM SORUMLUSU', 'Yeniden aflatoksin seçimi', 'active', '2025-07-26 11:09:12', '2025-07-26 11:09:12'),
(10, 1, 'Son Kontrol', 'Bitmiş ürün fiziksel kontrol', 'Her partide', 'Limitlere uygunluk', 'KALİTE-LABORATUVAR SORUMLUSU', 'Uygun olmayan ürünün pakete konulmaması', 'active', '2025-07-26 11:09:12', '2025-07-26 11:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `step_executions`
--

CREATE TABLE `step_executions` (
  `id` int(11) NOT NULL,
  `process_execution_id` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `step_name` varchar(255) NOT NULL,
  `step_description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_role` varchar(100) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','failed','skipped','waiting') DEFAULT 'pending',
  `result` enum('ok','issue','critical_issue') DEFAULT NULL,
  `scheduled_start` timestamp NULL DEFAULT NULL,
  `actual_start` timestamp NULL DEFAULT NULL,
  `scheduled_completion` timestamp NULL DEFAULT NULL,
  `actual_completion` timestamp NULL DEFAULT NULL,
  `estimated_duration` int(11) DEFAULT 30,
  `notes` text DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_processes`
-- (See below for the actual view)
--
CREATE TABLE `v_active_processes` (
`execution_id` int(11)
,`execution_name` varchar(255)
,`batch_number` varchar(100)
,`status` enum('pending','in_progress','completed','failed','on_hold','cancelled')
,`priority` enum('low','medium','high','critical')
,`current_step` int(11)
,`total_steps` int(11)
,`scheduled_completion` timestamp
,`process_name` varchar(255)
,`company_id` int(11)
,`company_name` varchar(255)
,`completion_percentage` decimal(15,1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_daily_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_daily_summary` (
`company_id` int(11)
,`company_name` varchar(255)
,`company_domain` varchar(100)
,`company_status` varchar(9)
,`due_soon` bigint(21)
,`overdue` bigint(21)
,`completed_today` bigint(21)
,`open_issues` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `v_active_processes`
--
DROP TABLE IF EXISTS `v_active_processes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_processes`  AS SELECT `pe`.`id` AS `execution_id`, `pe`.`execution_name` AS `execution_name`, `pe`.`batch_number` AS `batch_number`, `pe`.`status` AS `status`, `pe`.`priority` AS `priority`, `pe`.`current_step` AS `current_step`, `pe`.`total_steps` AS `total_steps`, `pe`.`scheduled_completion` AS `scheduled_completion`, `cp`.`process_name` AS `process_name`, `cp`.`company_id` AS `company_id`, `c`.`name` AS `company_name`, CASE WHEN `pe`.`total_steps` > 0 THEN round(`pe`.`current_step` / `pe`.`total_steps` * 100,1) ELSE 0 END AS `completion_percentage` FROM ((`process_executions` `pe` join `company_processes` `cp` on(`pe`.`company_process_id` = `cp`.`id`)) join `companies` `c` on(`cp`.`company_id` = `c`.`id`)) WHERE `pe`.`status` in ('pending','in_progress') ;

-- --------------------------------------------------------

--
-- Structure for view `v_daily_summary`
--
DROP TABLE IF EXISTS `v_daily_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_daily_summary`  AS SELECT `c`.`id` AS `company_id`, `c`.`name` AS `company_name`, coalesce(`c`.`domain`,concat('company',`c`.`id`)) AS `company_domain`, coalesce(`c`.`status`,'active') AS `company_status`, count(case when `se`.`status` in ('pending','in_progress') and `se`.`scheduled_completion` <= current_timestamp() + interval 2 hour then 1 end) AS `due_soon`, count(case when `se`.`status` in ('pending','in_progress') and `se`.`scheduled_completion` < current_timestamp() then 1 end) AS `overdue`, count(case when `se`.`status` = 'completed' and cast(`se`.`actual_completion` as date) = curdate() then 1 end) AS `completed_today`, count(case when `pi`.`status` in ('open','in_progress') then 1 end) AS `open_issues` FROM ((((`companies` `c` left join `company_processes` `cp` on(`c`.`id` = `cp`.`company_id`)) left join `process_executions` `pe` on(`cp`.`id` = `pe`.`company_process_id`)) left join `step_executions` `se` on(`pe`.`id` = `se`.`process_execution_id`)) left join `process_issues` `pi` on(`se`.`id` = `pi`.`step_execution_id`)) WHERE coalesce(`c`.`status`,'active') = 'active' GROUP BY `c`.`id`, `c`.`name`, `c`.`domain`, `c`.`status` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analysis_plans`
--
ALTER TABLE `analysis_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_analysis` (`company_id`,`analysis_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `analysis_results`
--
ALTER TABLE `analysis_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lot_compliance` (`lot_number`,`compliance_status`),
  ADD KEY `idx_plan_id` (`plan_id`),
  ADD KEY `idx_company_id` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subdomain` (`subdomain`),
  ADD KEY `idx_subdomain` (`subdomain`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_plan_type` (`plan_type`);

--
-- Indexes for table `company_data`
--
ALTER TABLE `company_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_company_data` (`company_id`,`module_id`,`data_type`,`data_id`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_data_type` (`data_type`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_company_data_type` (`company_id`,`data_type`),
  ADD KEY `idx_data_key` (`data_key`);

--
-- Indexes for table `company_module_subscriptions`
--
ALTER TABLE `company_module_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_company_module` (`company_id`,`module_id`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `company_processes`
--
ALTER TABLE `company_processes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_template_id` (`template_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_process_code` (`process_code`);

--
-- Indexes for table `company_users`
--
ALTER TABLE `company_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_company_username` (`company_id`,`username`),
  ADD UNIQUE KEY `unique_company_email` (`company_id`,`email`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `culture_surveys`
--
ALTER TABLE `culture_surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_survey` (`company_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `culture_survey_responses`
--
ALTER TABLE `culture_survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_survey_score` (`survey_id`,`total_score`),
  ADD KEY `idx_company_id` (`company_id`);

--
-- Indexes for table `lot_traceability`
--
ALTER TABLE `lot_traceability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lot` (`company_id`,`lot_number`),
  ADD KEY `idx_lot_number` (`lot_number`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `marketplace_modules`
--
ALTER TABLE `marketplace_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `module_code` (`module_code`),
  ADD KEY `idx_module_code` (`module_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `module_components`
--
ALTER TABLE `module_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_component_type` (`component_type`),
  ADD KEY `idx_order_index` (`order_index`);

--
-- Indexes for table `module_workflows`
--
ALTER TABLE `module_workflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_module_id` (`module_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `platform_admins`
--
ALTER TABLE `platform_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `process_audit_log`
--
ALTER TABLE `process_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `step_execution_id` (`step_execution_id`),
  ADD KEY `idx_process_execution_id` (`process_execution_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_performed_by` (`performed_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `process_executions`
--
ALTER TABLE `process_executions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_process_id` (`company_process_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_batch_number` (`batch_number`),
  ADD KEY `idx_scheduled_completion` (`scheduled_completion`),
  ADD KEY `idx_executions_time_range` (`scheduled_completion`,`status`);

--
-- Indexes for table `process_issues`
--
ALTER TABLE `process_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_step_execution_id` (`step_execution_id`),
  ADD KEY `idx_issue_type` (`issue_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_responsible_person` (`responsible_person`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_issues_open_critical` (`status`,`issue_type`,`due_date`);

--
-- Indexes for table `process_notifications`
--
ALTER TABLE `process_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `related_execution_id` (`related_execution_id`),
  ADD KEY `related_step_id` (`related_step_id`),
  ADD KEY `related_issue_id` (`related_issue_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `process_templates`
--
ALTER TABLE `process_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_code` (`template_code`),
  ADD KEY `idx_template_code` (`template_code`),
  ADD KEY `idx_brc_standard` (`brc_standard`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_product` (`company_id`,`product_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `quality_control_plans`
--
ALTER TABLE `quality_control_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_process` (`company_id`,`process_step`);

--
-- Indexes for table `step_executions`
--
ALTER TABLE `step_executions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_process_execution_id` (`process_execution_id`),
  ADD KEY `idx_step_number` (`step_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `idx_scheduled_completion` (`scheduled_completion`),
  ADD KEY `idx_steps_assigned_pending` (`assigned_to`,`status`,`scheduled_completion`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analysis_plans`
--
ALTER TABLE `analysis_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analysis_results`
--
ALTER TABLE `analysis_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_data`
--
ALTER TABLE `company_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_module_subscriptions`
--
ALTER TABLE `company_module_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company_processes`
--
ALTER TABLE `company_processes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_users`
--
ALTER TABLE `company_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `culture_surveys`
--
ALTER TABLE `culture_surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `culture_survey_responses`
--
ALTER TABLE `culture_survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lot_traceability`
--
ALTER TABLE `lot_traceability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketplace_modules`
--
ALTER TABLE `marketplace_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `module_components`
--
ALTER TABLE `module_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `module_workflows`
--
ALTER TABLE `module_workflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `platform_admins`
--
ALTER TABLE `platform_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `process_audit_log`
--
ALTER TABLE `process_audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `process_executions`
--
ALTER TABLE `process_executions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `process_issues`
--
ALTER TABLE `process_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `process_notifications`
--
ALTER TABLE `process_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `process_templates`
--
ALTER TABLE `process_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_specifications`
--
ALTER TABLE `product_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quality_control_plans`
--
ALTER TABLE `quality_control_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `step_executions`
--
ALTER TABLE `step_executions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analysis_plans`
--
ALTER TABLE `analysis_plans`
  ADD CONSTRAINT `analysis_plans_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `analysis_results`
--
ALTER TABLE `analysis_results`
  ADD CONSTRAINT `analysis_results_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `analysis_plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analysis_results_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_data`
--
ALTER TABLE `company_data`
  ADD CONSTRAINT `company_data_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_data_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_data_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `company_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_module_subscriptions`
--
ALTER TABLE `company_module_subscriptions`
  ADD CONSTRAINT `company_module_subscriptions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_module_subscriptions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_processes`
--
ALTER TABLE `company_processes`
  ADD CONSTRAINT `company_processes_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_processes_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `process_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_users`
--
ALTER TABLE `company_users`
  ADD CONSTRAINT `company_users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `culture_surveys`
--
ALTER TABLE `culture_surveys`
  ADD CONSTRAINT `culture_surveys_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `culture_survey_responses`
--
ALTER TABLE `culture_survey_responses`
  ADD CONSTRAINT `culture_survey_responses_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `culture_surveys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `culture_survey_responses_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lot_traceability`
--
ALTER TABLE `lot_traceability`
  ADD CONSTRAINT `lot_traceability_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketplace_modules`
--
ALTER TABLE `marketplace_modules`
  ADD CONSTRAINT `marketplace_modules_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `platform_admins` (`id`);

--
-- Constraints for table `module_components`
--
ALTER TABLE `module_components`
  ADD CONSTRAINT `module_components_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `module_workflows`
--
ALTER TABLE `module_workflows`
  ADD CONSTRAINT `module_workflows_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `process_audit_log`
--
ALTER TABLE `process_audit_log`
  ADD CONSTRAINT `process_audit_log_ibfk_1` FOREIGN KEY (`process_execution_id`) REFERENCES `process_executions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `process_audit_log_ibfk_2` FOREIGN KEY (`step_execution_id`) REFERENCES `step_executions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `process_executions`
--
ALTER TABLE `process_executions`
  ADD CONSTRAINT `process_executions_ibfk_1` FOREIGN KEY (`company_process_id`) REFERENCES `company_processes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `process_issues`
--
ALTER TABLE `process_issues`
  ADD CONSTRAINT `process_issues_ibfk_1` FOREIGN KEY (`step_execution_id`) REFERENCES `step_executions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `process_notifications`
--
ALTER TABLE `process_notifications`
  ADD CONSTRAINT `process_notifications_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `process_notifications_ibfk_2` FOREIGN KEY (`related_execution_id`) REFERENCES `process_executions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `process_notifications_ibfk_3` FOREIGN KEY (`related_step_id`) REFERENCES `step_executions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `process_notifications_ibfk_4` FOREIGN KEY (`related_issue_id`) REFERENCES `process_issues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_specifications`
--
ALTER TABLE `product_specifications`
  ADD CONSTRAINT `product_specifications_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quality_control_plans`
--
ALTER TABLE `quality_control_plans`
  ADD CONSTRAINT `quality_control_plans_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `step_executions`
--
ALTER TABLE `step_executions`
  ADD CONSTRAINT `step_executions_ibfk_1` FOREIGN KEY (`process_execution_id`) REFERENCES `process_executions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
