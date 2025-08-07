-- BRC Load Platform - Database Setup
-- Run this SQL in phpMyAdmin after creating the database

-- Create the database (if not exists)
CREATE DATABASE IF NOT EXISTS `brcload_platform` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `brcload_platform`;

-- 1. Companies Table
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `settings` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subdomain` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Platform Admins Table
CREATE TABLE IF NOT EXISTS `platform_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Company Users Table
CREATE TABLE IF NOT EXISTS `company_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('admin','manager','user','viewer') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_username` (`company_id`, `username`),
  UNIQUE KEY `company_email` (`company_id`, `email`),
  KEY `company_id` (`company_id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Marketplace Modules Table
CREATE TABLE IF NOT EXISTS `marketplace_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_code` (`module_code`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Module Components Table
CREATE TABLE IF NOT EXISTS `module_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `component_name` varchar(100) NOT NULL,
  `component_type` varchar(50) NOT NULL,
  `component_code` varchar(100) NOT NULL,
  `config` longtext DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `is_locked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `component_type` (`component_type`),
  KEY `order_index` (`order_index`),
  FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Module Workflows Table
CREATE TABLE IF NOT EXISTS `module_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `workflow_data` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Company Module Subscriptions Table
CREATE TABLE IF NOT EXISTS `company_module_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `status` enum('active','inactive','trial','expired') DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `settings` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_module` (`company_id`, `module_id`),
  KEY `company_id` (`company_id`),
  KEY `module_id` (`module_id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Company Data Table
CREATE TABLE IF NOT EXISTS `company_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `data_type` varchar(100) NOT NULL,
  `data_key` varchar(255) NOT NULL,
  `data_value` longtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_module_key` (`company_id`, `module_id`, `data_key`),
  KEY `company_id` (`company_id`),
  KEY `module_id` (`module_id`),
  KEY `data_type` (`data_type`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`module_id`) REFERENCES `marketplace_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Data

-- Sample Companies
INSERT INTO `companies` (`company_name`, `subdomain`, `contact_name`, `contact_email`, `plan_type`, `status`) VALUES
('Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', 'premium', 'active'),
('Test Corporation', 'test', 'Jane Smith', 'jane@testcorp.com', 'basic', 'trial'),
('Enterprise Solutions', 'enterprise', 'Mike Johnson', 'mike@enterprise.com', 'enterprise', 'active');

-- Sample Platform Admin
INSERT INTO `platform_admins` (`username`, `email`, `password`, `first_name`, `last_name`, `role`) VALUES
('admin', 'admin@brcload.com', '123456', 'Platform', 'Admin', 'super_admin');

-- Sample Modules
INSERT INTO `marketplace_modules` (`module_name`, `module_code`, `description`, `category`, `price`, `status`, `created_by`) VALUES
('BRC Risk Assessment', 'brc_risk_assessment', 'Comprehensive risk assessment module with 5x5 matrix and BRC compliance features.', 'BRC Compliance', 299.00, 'published', 1),
('Quality Management', 'quality_mgmt', 'Complete quality control process management with checklists and reporting.', 'Quality Management', 399.00, 'published', 1),
('Safety Management', 'safety_mgmt', 'Safety management system with incident tracking and prevention.', 'Safety & Health', 199.00, 'published', 1),
('Audit Management', 'audit_mgmt', 'Audit planning, execution and follow-up management system.', 'Audit Management', 299.00, 'published', 1),
('Document Control', 'document_control', 'Document version control and approval workflow management.', 'Document Control', 149.00, 'published', 1),
('Training Management', 'training_mgmt', 'Employee training tracking and skill development management.', 'Training & Development', 199.00, 'published', 1);

-- Quality Control Component for Quality Management Module
INSERT INTO `module_components` (`module_id`, `component_name`, `component_type`, `component_code`, `config`, `order_index`) VALUES
(2, 'Kalite Kontrol Süreci Tablosu', 'quality-control-table', 'quality_control_table_main', '{
  "title": "Kalite Kontrol Süreci",
  "showHeader": true,
  "allowEdit": true,
  "allowAdd": true,
  "allowDelete": true,
  "exportable": true,
  "columns": [
    {"id": "asama", "label": "AŞAMA", "width": "15%", "required": true},
    {"id": "kriter", "label": "KRİTER", "width": "15%", "required": true},
    {"id": "siklik", "label": "SIKLIK", "width": "10%", "required": true},
    {"id": "kabul_kriteri", "label": "KABUL KRİTERİ", "width": "20%", "required": true},
    {"id": "kontrol_sorumlusu", "label": "KONTROL SORUMLUSU", "width": "15%", "required": true},
    {"id": "sapma_durumu", "label": "SAPMA DURUMUNDA YAPILACAK", "width": "25%", "required": true}
  ],
  "sampleData": [
    {
      "asama": "Hammadde Kontrolü",
      "kriter": "Görsel Kontrol",
      "siklik": "Her parti",
      "kabul_kriteri": "Spesifikasyona uygun",
      "kontrol_sorumlusu": "QC Uzmanı",
      "sapma_durumu": "Red et ve tedarikçiye iade"
    },
    {
      "asama": "Üretim Süreci",
      "kriter": "Sıcaklık Kontrolü", 
      "siklik": "2 saatte bir",
      "kabul_kriteri": "±2°C tolerans",
      "kontrol_sorumlusu": "Üretim Operatörü",
      "sapma_durumu": "Ekipman ayarını yap"
    }
  ]
}', 1);

-- Sample Subscriptions
INSERT INTO `company_module_subscriptions` (`company_id`, `module_id`, `status`) VALUES
(1, 1, 'active'),
(1, 2, 'active'),
(1, 3, 'active'),
(2, 1, 'active'),
(2, 2, 'active'),
(3, 2, 'active'),
(3, 4, 'active'),
(3, 5, 'active');

-- Create Performance Indexes
CREATE INDEX IF NOT EXISTS `idx_company_data_lookup` ON `company_data` (`company_id`, `module_id`, `data_type`);
CREATE INDEX IF NOT EXISTS `idx_modules_search` ON `marketplace_modules` (`category`, `status`, `module_name`);
CREATE INDEX IF NOT EXISTS `idx_subscriptions_active` ON `company_module_subscriptions` (`status`, `expires_at`);