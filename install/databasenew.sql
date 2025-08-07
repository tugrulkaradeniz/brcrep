-- ====================================
-- BRC LOAD PLATFORM - DATABASE SETUP
-- ====================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS brcload_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE brcload_platform;

-- ====================================
-- 1. PLATFORM ADMINS TABLE
-- ====================================
CREATE TABLE platform_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ====================================
-- 2. COMPANIES TABLE (Multi-tenant)
-- ====================================
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(100) NOT NULL,
    subdomain VARCHAR(50) NOT NULL UNIQUE,
    contact_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    billing_address TEXT,
    logo_url VARCHAR(255),
    theme_color VARCHAR(7) DEFAULT '#007bff',
    plan_type ENUM('basic', 'premium', 'enterprise') DEFAULT 'basic',
    status ENUM('trial', 'active', 'suspended', 'cancelled') DEFAULT 'trial',
    trial_end_date DATE,
    subscription_end_date DATE,
    max_users INT DEFAULT 5,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_subdomain (subdomain),
    INDEX idx_status (status),
    INDEX idx_plan_type (plan_type)
);

-- ====================================
-- 3. COMPANY USERS TABLE
-- ====================================
CREATE TABLE company_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('owner', 'admin', 'manager', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_username (company_id, username),
    UNIQUE KEY unique_company_email (company_id, email),
    INDEX idx_company_id (company_id),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ====================================
-- 4. MARKETPLACE MODULES TABLE
-- ====================================
CREATE TABLE marketplace_modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_name VARCHAR(100) NOT NULL,
    module_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    category VARCHAR(50) DEFAULT 'Custom',
    price DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'USD',
    is_base_module BOOLEAN DEFAULT FALSE,
    version VARCHAR(10) DEFAULT '1.0',
    icon VARCHAR(50) DEFAULT 'puzzle-piece',
    cover_image VARCHAR(255),
    status ENUM('draft', 'published', 'deprecated') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES platform_admins(id),
    INDEX idx_module_code (module_code),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created_by (created_by)
);

-- ====================================
-- 5. MODULE COMPONENTS TABLE
-- ====================================
CREATE TABLE module_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    component_name VARCHAR(100) NOT NULL,
    component_type VARCHAR(50) NOT NULL,
    component_code VARCHAR(100) NOT NULL,
    config JSON,
    order_index INT DEFAULT 0,
    is_locked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    INDEX idx_module_id (module_id),
    INDEX idx_component_type (component_type),
    INDEX idx_order_index (order_index)
);

-- ====================================
-- 6. COMPANY MODULE SUBSCRIPTIONS TABLE
-- ====================================
CREATE TABLE company_module_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATE NULL,
    status ENUM('active', 'cancelled', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_module (company_id, module_id),
    INDEX idx_company_id (company_id),
    INDEX idx_module_id (module_id),
    INDEX idx_status (status)
);

-- ====================================
-- 7. COMPANY DATA TABLE (Multi-tenant Data Storage)
-- ====================================
CREATE TABLE company_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    data_type VARCHAR(50) NOT NULL,
    data_id VARCHAR(100),
    data_content JSON NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES company_users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_company_data (company_id, module_id, data_type, data_id),
    INDEX idx_company_id (company_id),
    INDEX idx_module_id (module_id),
    INDEX idx_data_type (data_type),
    INDEX idx_created_by (created_by)
);

-- ====================================
-- 8. WORKFLOW INSTANCES TABLE
-- ====================================
CREATE TABLE company_workflow_instances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    workflow_name VARCHAR(100) NOT NULL,
    data_id VARCHAR(100) NOT NULL,
    current_step VARCHAR(50) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    assigned_to INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES company_users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES company_users(id) ON DELETE SET NULL,
    INDEX idx_company_id (company_id),
    INDEX idx_module_id (module_id),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to)
);

-- ====================================
-- INSERT SAMPLE DATA
-- ====================================

-- Platform Admin User
INSERT INTO platform_admins (username, email, password, first_name, last_name, role, status) VALUES
('admin', 'admin@brcload.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Platform', 'Admin', 'super_admin', 'active'),
('moderator', 'moderator@brcload.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content', 'Moderator', 'moderator', 'active');

-- Demo Companies
INSERT INTO companies (company_name, subdomain, contact_name, contact_email, contact_phone, plan_type, status, max_users, theme_color) VALUES
('Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', '+1-555-0123', 'premium', 'active', 15, '#007bff'),
('Test Corporation', 'test', 'Jane Smith', 'jane@testcorp.com', '+1-555-0456', 'basic', 'trial', 5, '#28a745'),
('Enterprise Solutions', 'enterprise', 'Mike Johnson', 'mike@enterprise.com', '+1-555-0789', 'enterprise', 'active', 50, '#6f42c1');

-- Company Users
INSERT INTO company_users (company_id, username, email, password, first_name, last_name, role, status) VALUES
-- Demo Company Users
(1, 'admin', 'admin@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', 'owner', 'active'),
(1, 'manager', 'manager@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', 'manager', 'active'),
(1, 'user1', 'user1@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Brown', 'user', 'active'),
-- Test Corp Users  
(2, 'admin', 'admin@testcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', 'owner', 'active'),
(2, 'user1', 'user1@testcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tom', 'Davis', 'user', 'active'),
-- Enterprise Users
(3, 'admin', 'admin@enterprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Johnson', 'owner', 'active');

-- Base Modules
INSERT INTO marketplace_modules (module_name, module_code, description, category, price, is_base_module, version, icon, status, created_by) VALUES
('BRC Risk Assessment', 'brc_risk_assessment', 'Comprehensive BRC-compliant risk assessment module with automated workflows and compliance tracking.', 'BRC Compliance', 99.99, TRUE, '2.1', 'shield-alt', 'published', 1),
('Quality Management', 'quality_mgmt', 'Complete quality management system with document control, non-conformity tracking, and audit management.', 'Quality Management', 79.99, TRUE, '1.8', 'award', 'published', 1),
('Safety Management', 'safety_mgmt', 'Comprehensive safety management system with incident tracking, safety training, and compliance monitoring.', 'Safety & Health', 89.99, TRUE, '1.5', 'hard-hat', 'published', 1),
('Audit Management', 'audit_mgmt', 'Complete audit management system with planning, execution, and follow-up capabilities for all audit types.', 'Audit Management', 69.99, TRUE, '1.3', 'clipboard-check', 'published', 1),
('Document Control', 'document_control', 'Advanced document control system with version management, approval workflows, and distribution tracking.', 'Document Control', 49.99, TRUE, '2.0', 'file-alt', 'published', 1),
('Training Management', 'training_mgmt', 'Complete training management system with course creation, tracking, and competency assessment capabilities.', 'Training & Development', 59.99, TRUE, '1.7', 'graduation-cap', 'published', 1);

-- Module Components for BRC Risk Assessment
INSERT INTO module_components (module_id, component_name, component_type, component_code, config, order_index, is_locked) VALUES
(1, 'Risk Assessment Matrix', 'risk-matrix', 'risk_matrix_5x5', '{"size": "5x5", "colors": ["#22c55e", "#f59e0b", "#ef4444"]}', 1, TRUE),
(1, 'Risk Assessment Form', 'smart-form', 'risk_form', '{"fields": ["risk_description", "likelihood", "impact", "location"]}', 2, TRUE),
(1, 'Approval Workflow', 'approval-flow', 'risk_approval', '{"steps": ["assessor", "manager", "quality_manager"]}', 3, TRUE),
(1, 'Dashboard Overview', 'dashboard-grid', 'risk_dashboard', '{"cards": ["total_risks", "completed", "pending", "compliance_score"]}', 4, TRUE);

-- Sample Subscriptions
INSERT INTO company_module_subscriptions (company_id, module_id, status, expires_at) VALUES
(1, 1, 'active', '2025-01-24'), -- Demo Company → BRC Risk Assessment
(1, 2, 'active', '2025-01-24'), -- Demo Company → Quality Management
(2, 1, 'active', '2024-12-24'), -- Test Corp → BRC Risk Assessment
(3, 1, 'active', '2025-06-24'), -- Enterprise → BRC Risk Assessment
(3, 2, 'active', '2025-06-24'), -- Enterprise → Quality Management
(3, 3, 'active', '2025-06-24'); -- Enterprise → Safety Management

-- Sample Company Data
INSERT INTO company_data (company_id, module_id, data_type, data_id, data_content, created_by) VALUES
(1, 1, 'risk_assessment', 'RA-2024-001', '{"risk_description": "Bacterial contamination in raw material storage", "likelihood": 3, "impact": 4, "risk_score": 12, "status": "approved"}', 1),
(1, 1, 'risk_assessment', 'RA-2024-002', '{"risk_description": "Unauthorized access to production area", "likelihood": 2, "impact": 3, "risk_score": 6, "status": "pending"}', 1);

-- ====================================
-- CREATE INDEXES FOR PERFORMANCE
-- ====================================

-- Additional composite indexes for better query performance
CREATE INDEX idx_company_module_subscription ON company_module_subscriptions(company_id, module_id, status);
CREATE INDEX idx_company_data_lookup ON company_data(company_id, module_id, data_type);
CREATE INDEX idx_workflow_company_status ON company_workflow_instances(company_id, status);

-- ====================================
-- GRANT PERMISSIONS (Optional)
-- ====================================

-- If you need to create a specific database user:
-- CREATE USER 'brcload_user'@'localhost' IDENTIFIED BY 'secure_password';
-- GRANT ALL PRIVILEGES ON brcload_platform.* TO 'brcload_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ====================================
-- VERIFICATION QUERIES
-- ====================================

-- Check if all tables are created
SELECT TABLE_NAME, TABLE_ROWS 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'brcload_platform' 
ORDER BY TABLE_NAME;

-- Check sample data
SELECT 'Platform Admins' as table_name, COUNT(*) as count FROM platform_admins
UNION ALL
SELECT 'Companies', COUNT(*) FROM companies
UNION ALL  
SELECT 'Company Users', COUNT(*) FROM company_users
UNION ALL
SELECT 'Modules', COUNT(*) FROM marketplace_modules
UNION ALL
SELECT 'Subscriptions', COUNT(*) FROM company_module_subscriptions;

-- ====================================
-- SETUP COMPLETE! 🎉
-- ====================================

/*
TEST CREDENTIALS:

Platform Admin:
- Username: admin@brcload.com
- Password: password

Demo Company (demo.brcload.com):
- Username: admin
- Password: password

Test Company (test.brcload.com):  
- Username: admin
- Password: password

All passwords use the hash for "password"
*/