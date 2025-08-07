-- =====================================================
-- BRC LOAD PLATFORM - COMPLETE DATABASE SCHEMA
-- File: install/database.sql
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS company_data;
DROP TABLE IF EXISTS company_module_subscriptions;
DROP TABLE IF EXISTS module_components;
DROP TABLE IF EXISTS marketplace_modules;
DROP TABLE IF EXISTS company_users;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS platform_admins;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- PLATFORM ADMINS (Creator/Platform yöneticileri)
-- =====================================================
CREATE TABLE platform_admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- =====================================================
-- COMPANIES (Müşteri şirketler)
-- =====================================================
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL,
    subdomain VARCHAR(100) UNIQUE NOT NULL,
    domain VARCHAR(255) NULL,
    plan_type ENUM('basic', 'premium', 'enterprise') DEFAULT 'basic',
    status ENUM('active', 'suspended', 'trial') DEFAULT 'trial',
    trial_end_date DATE NULL,
    subscription_end_date DATE NULL,
    max_users INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Contact & Billing Info
    contact_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(50) NULL,
    billing_address TEXT NULL,
    
    -- Configuration
    logo_url VARCHAR(500) NULL,
    theme_color VARCHAR(7) DEFAULT '#007bff',
    settings JSON NULL,
    
    INDEX idx_subdomain (subdomain),
    INDEX idx_status (status),
    INDEX idx_plan_type (plan_type)
);

-- =====================================================
-- COMPANY USERS (Her şirketin kendi kullanıcıları)
-- =====================================================
CREATE TABLE company_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin', 'owner') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_username (company_id, username),
    UNIQUE KEY unique_company_email (company_id, email),
    INDEX idx_company_id (company_id),
    INDEX idx_status (status),
    INDEX idx_role (role)
);

-- =====================================================
-- MARKETPLACE MODULES (Creator'ın oluşturduğu modüller)
-- =====================================================
CREATE TABLE marketplace_modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_name VARCHAR(255) NOT NULL,
    module_code VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    version VARCHAR(20) NOT NULL DEFAULT '1.0.0',
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_base_module BOOLEAN DEFAULT FALSE,
    requirements JSON NULL,
    status ENUM('draft', 'published', 'deprecated') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES platform_admins(id),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_module_code (module_code)
);

-- =====================================================
-- COMPANY MODULE SUBSCRIPTIONS (Şirketlerin abonelikleri)
-- =====================================================
CREATE TABLE company_module_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATE NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_module (company_id, module_id),
    INDEX idx_company_id (company_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_status (status)
);

-- =====================================================
-- MODULE COMPONENTS (Modül bileşenleri)
-- =====================================================
CREATE TABLE module_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    component_name VARCHAR(255) NOT NULL,
    component_type ENUM('display', 'input', 'action', 'layout') NOT NULL,
    component_code VARCHAR(100) NOT NULL,
    config JSON NOT NULL,
    order_index INT DEFAULT 0,
    is_locked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    INDEX idx_module_id (module_id),
    INDEX idx_component_type (component_type),
    INDEX idx_order_index (order_index)
);

-- =====================================================
-- COMPANY DATA (Her şirketin kendi verileri)
-- =====================================================
CREATE TABLE company_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    data_type VARCHAR(100) NOT NULL,
    data_id VARCHAR(100) NOT NULL,
    data_content JSON NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES company_users(id),
    UNIQUE KEY unique_company_data (company_id, module_id, data_type, data_id),
    INDEX idx_company_module (company_id, module_id),
    INDEX idx_data_type (data_type),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- WORKFLOWS (İş akışları)
-- =====================================================
CREATE TABLE workflows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    workflow_name VARCHAR(255) NOT NULL,
    workflow_code VARCHAR(100) NOT NULL,
    description TEXT NULL,
    steps JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE,
    INDEX idx_module_id (module_id),
    INDEX idx_workflow_code (workflow_code),
    INDEX idx_is_active (is_active)
);

-- =====================================================
-- WORKFLOW INSTANCES (İş akışı örnekleri)
-- =====================================================
CREATE TABLE workflow_instances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    workflow_id INT NOT NULL,
    data_id VARCHAR(100) NOT NULL,
    current_step INT DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'rejected', 'cancelled') DEFAULT 'pending',
    assigned_to INT NULL,
    step_data JSON NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES company_users(id) ON SET NULL,
    INDEX idx_company_workflow (company_id, workflow_id),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to)
);

-- =====================================================
-- AUDIT LOG (Denetim kayıtları)
-- =====================================================
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NULL,
    user_id INT NULL,
    user_type ENUM('platform_admin', 'company_user') NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100) NULL,
    record_id INT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_company_id (company_id),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- INITIAL PLATFORM ADMIN DATA
-- =====================================================
INSERT INTO platform_admins (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@brcload.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Platform', 'Admin', 'super_admin'),
('tugrulk', 'tugrul@brcload.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tuğrul', 'Kaan', 'admin');

-- =====================================================
-- DEMO COMPANIES
-- =====================================================
INSERT INTO companies (company_name, subdomain, contact_name, contact_email, contact_phone, plan_type, status, trial_end_date, max_users, theme_color) VALUES
('Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', '+1-555-0123', 'premium', 'active', DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 15, '#007bff'),
('Test Corporation', 'test', 'Jane Smith', 'jane@testcorp.com', '+1-555-0124', 'basic', 'trial', DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 5, '#28a745'),
('Quality Foods Inc.', 'qualityfoods', 'Mike Johnson', 'mike@qualityfoods.com', '+1-555-0125', 'enterprise', 'active', DATE_ADD(CURRENT_DATE, INTERVAL 365 DAY), 50, '#dc3545'),
('Safe Manufacturing', 'safemanuf', 'Sarah Wilson', 'sarah@safemanuf.com', '+1-555-0126', 'premium', 'active', DATE_ADD(CURRENT_DATE, INTERVAL 90 DAY), 25, '#ffc107');

-- =====================================================
-- DEMO COMPANY USERS
-- =====================================================
INSERT INTO company_users (company_id, username, email, password, first_name, last_name, role) VALUES
-- Demo Company Ltd. users
(1, 'admin', 'admin@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', 'owner'),
(1, 'manager', 'manager@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Manager', 'admin'),
(1, 'user1', 'user1@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Smith', 'user'),
(1, 'user2', 'user2@democompany.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carol', 'Jones', 'user'),

-- Test Corporation users
(2, 'admin', 'admin@testcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', 'owner'),
(2, 'testuser', 'test@testcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User', 'user'),

-- Quality Foods Inc. users
(3, 'admin', 'admin@qualityfoods.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Johnson', 'owner'),
(3, 'quality', 'quality@qualityfoods.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa', 'Quality', 'admin'),

-- Safe Manufacturing users
(4, 'admin', 'admin@safemanuf.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', 'owner'),
(4, 'safety', 'safety@safemanuf.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tom', 'Safety', 'admin');

-- =====================================================
-- MARKETPLACE MODULES
-- =====================================================
INSERT INTO marketplace_modules (module_name, module_code, description, category, version, price, is_base_module, status, created_by) VALUES
('BRC Risk Assessment', 'brc_risk_assessment', 'Complete BRC-compliant risk assessment module with automated workflows, compliance tracking, and detailed reporting capabilities.', 'BRC Compliance', '2.1.0', 99.99, TRUE, 'published', 1),
('Quality Management System', 'quality_mgmt', 'ISO 9001 compliant quality management system with document control, non-conformity management, and audit tracking.', 'Quality Management', '1.8.0', 79.99, TRUE, 'published', 1),
('Safety Management', 'safety_mgmt', 'OHSAS 18001 safety management system with incident tracking, safety training management, and compliance monitoring.', 'Safety & Health', '1.5.0', 89.99, TRUE, 'published', 1),
('Audit Management', 'audit_mgmt', 'Comprehensive audit management system for internal and external audits with finding tracking and corrective actions.', 'Audit Management', '1.3.0', 69.99, TRUE, 'published', 1),
('Document Control', 'document_control', 'Advanced document control system with version management, approval workflows, and distribution tracking.', 'Document Control', '2.0.0', 49.99, TRUE, 'published', 1),
('Training Management', 'training_mgmt', 'Employee training and competency management system with course creation, tracking, and certification management.', 'Training & Development', '1.7.0', 59.99, TRUE, 'published', 1),
('Supplier Management', 'supplier_mgmt', 'Supplier qualification, audit, and performance management system with risk assessment capabilities.', 'Supply Chain', '1.4.0', 89.99, FALSE, 'published', 1),
('HACCP Management', 'haccp_mgmt', 'HACCP system implementation and management with CCP monitoring, corrective actions, and verification procedures.', 'Food Safety', '1.6.0', 109.99, TRUE, 'published', 1);

-- =====================================================
-- DEMO MODULE SUBSCRIPTIONS
-- =====================================================
INSERT INTO company_module_subscriptions (company_id, module_id, expires_at, status) VALUES
-- Demo Company Ltd. (Premium plan - multiple modules)
(1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(1, 2, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(1, 3, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(1, 4, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),

-- Test Corporation (Basic plan - limited modules)
(2, 1, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 'active'),
(2, 5, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 'active'),

-- Quality Foods Inc. (Enterprise plan - all modules)
(3, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 2, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 3, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 4, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 5, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 6, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 7, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(3, 8, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),

-- Safe Manufacturing (Premium plan - safety focused)
(4, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(4, 3, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(4, 4, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active'),
(4, 6, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR), 'active');

-- =====================================================
-- MODULE COMPONENTS (BRC Risk Assessment Module Example)
-- =====================================================
INSERT INTO module_components (module_id, component_name, component_type, component_code, config, order_index, is_locked) VALUES
(1, 'Risk Assessment Form', 'input', 'risk_form', '{"fields": ["risk_area", "risk_category", "description", "likelihood", "impact", "existing_controls", "proposed_actions"], "validation": "required", "layout": "grid"}', 1, TRUE),
(1, 'Risk Matrix Display', 'display', 'risk_matrix', '{"matrix_size": "5x5", "color_scheme": "standard", "interactive": true}', 2, TRUE),
(1, 'Approval Workflow', 'action', 'approval_flow', '{"steps": ["Risk Assessor", "Supervisor", "Safety Manager", "Final Approval"], "auto_assign": true}', 3, TRUE),
(1, 'Compliance Reports', 'action', 'report_generator', '{"report_types": ["summary", "detailed", "compliance"], "formats": ["pdf", "excel"], "scheduling": true}', 4, TRUE),
(1, 'Dashboard Overview', 'layout', 'dashboard_grid', '{"widgets": ["risk_summary", "pending_reviews", "compliance_score"], "layout": "responsive"}', 0, TRUE);

-- =====================================================
-- WORKFLOWS (Risk Assessment Workflow Example)
-- =====================================================
INSERT INTO workflows (module_id, workflow_name, workflow_code, description, steps) VALUES
(1, 'BRC Risk Assessment Approval', 'brc_risk_approval', 'Standard BRC risk assessment approval workflow', 
'[
  {"id": 1, "name": "Initial Assessment", "role": "user", "description": "Complete risk assessment form", "required_fields": ["risk_description", "likelihood", "impact"]},
  {"id": 2, "name": "Supervisor Review", "role": "admin", "description": "Review and validate assessment", "actions": ["approve", "reject", "request_changes"]},
  {"id": 3, "name": "Safety Manager Approval", "role": "admin", "description": "Final safety review", "actions": ["approve", "reject"]},
  {"id": 4, "name": "Archive & Monitor", "role": "system", "description": "Archive assessment and set monitoring schedule", "auto_complete": true}
]');

-- =====================================================
-- DEMO COMPANY DATA (Sample Risk Assessments)
-- =====================================================
INSERT INTO company_data (company_id, module_id, data_type, data_id, data_content, created_by) VALUES
(1, 1, 'risk_assessment', 'RA-2024-001', 
'{"risk_area": "food_safety", "risk_category": "biological", "description": "Potential bacterial contamination in raw material storage", "location": "Cold Storage Room A", "likelihood": 3, "impact": 4, "risk_score": 12, "existing_controls": "Temperature monitoring, regular cleaning", "proposed_actions": "Install additional sensors, increase cleaning frequency", "status": "approved", "created_date": "2024-07-20"}', 1),

(1, 1, 'risk_assessment', 'RA-2024-002',
'{"risk_area": "security", "risk_category": "physical", "description": "Unauthorized access to production area", "location": "Production Line 1", "likelihood": 2, "impact": 3, "risk_score": 6, "existing_controls": "Access cards, security cameras", "proposed_actions": "Add biometric scanners", "status": "pending", "created_date": "2024-07-22"}', 3),

(3, 1, 'risk_assessment', 'RA-2024-003',
'{"risk_area": "quality", "risk_category": "operational", "description": "Equipment malfunction affecting product quality", "location": "Packaging Line", "likelihood": 4, "impact": 4, "risk_score": 16, "existing_controls": "Preventive maintenance, quality checks", "proposed_actions": "Upgrade equipment monitoring system", "status": "in_review", "created_date": "2024-07-21"}', 7);

-- =====================================================
-- WORKFLOW INSTANCES (Active Risk Assessment Workflows)
-- =====================================================
INSERT INTO workflow_instances (company_id, workflow_id, data_id, current_step, status, assigned_to, step_data) VALUES
(1, 1, 'RA-2024-002', 1, 'in_progress', 2, '{"submitted_at": "2024-07-22T10:30:00Z", "comments": "Needs urgent review"}'),
(3, 1, 'RA-2024-003', 2, 'in_progress', 8, '{"submitted_at": "2024-07-21T15:45:00Z", "supervisor_notes": "High priority due to impact score"}');

-- =====================================================
-- AUDIT LOG ENTRIES (Sample)
-- =====================================================
INSERT INTO audit_log (company_id, user_id, user_type, action, table_name, record_id, new_values, ip_address) VALUES
(1, 1, 'company_user', 'create_risk_assessment', 'company_data', 1, '{"data_id": "RA-2024-001", "status": "created"}', '192.168.1.100'),
(1, 2, 'company_user', 'approve_risk_assessment', 'company_data', 1, '{"status": "approved", "approved_by": 2}', '192.168.1.101'),
(3, 7, 'company_user', 'create_risk_assessment', 'company_data', 3, '{"data_id": "RA-2024-003", "status": "created"}', '192.168.1.200');

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================
ALTER TABLE companies ADD INDEX idx_created_at (created_at);
ALTER TABLE company_users ADD INDEX idx_last_login (last_login);
ALTER TABLE marketplace_modules ADD INDEX idx_price (price);
ALTER TABLE company_data ADD INDEX idx_data_content_status ((JSON_UNQUOTE(JSON_EXTRACT(data_content, '$.status'))));
ALTER TABLE workflow_instances ADD INDEX idx_started_at (started_at);

-- =====================================================
-- VIEWS FOR REPORTING
-- =====================================================
CREATE VIEW company_stats AS
SELECT 
    c.id,
    c.company_name,
    c.subdomain,
    c.status,
    c.plan_type,
    COUNT(DISTINCT cu.id) as total_users,
    COUNT(DISTINCT cms.id) as active_modules,
    COUNT(DISTINCT cd.id) as total_data_records,
    c.created_at
FROM companies c
LEFT JOIN company_users cu ON c.id = cu.company_id AND cu.status = 'active'
LEFT JOIN company_module_subscriptions cms ON c.id = cms.company_id AND cms.status = 'active'
LEFT JOIN company_data cd ON c.id = cd.company_id
GROUP BY c.id;

CREATE VIEW module_usage_stats AS
SELECT 
    mm.id,
    mm.module_name,
    mm.module_code,
    mm.category,
    mm.price,
    COUNT(DISTINCT cms.company_id) as subscriber_count,
    COUNT(DISTINCT cd.id) as data_records,
    AVG(CASE WHEN cms.status = 'active' THEN mm.price ELSE 0 END) as avg_revenue,
    mm.created_at
FROM marketplace_modules mm
LEFT JOIN company_module_subscriptions cms ON mm.id = cms.module_id
LEFT JOIN company_data cd ON mm.id = cd.module_id
GROUP BY mm.id;

-- =====================================================
-- TRIGGERS FOR AUDIT LOGGING
-- =====================================================
DELIMITER //

CREATE TRIGGER company_data_audit_insert
AFTER INSERT ON company_data
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (company_id, user_id, user_type, action, table_name, record_id, new_values, created_at)
    VALUES (NEW.company_id, NEW.created_by, 'company_user', 'create_data', 'company_data', NEW.id, 
            JSON_OBJECT('data_type', NEW.data_type, 'data_id', NEW.data_id), NOW());
END//

CREATE TRIGGER company_data_audit_update
AFTER UPDATE ON company_data
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (company_id, user_id, user_type, action, table_name, record_id, old_values, new_values, created_at)
    VALUES (NEW.company_id, NEW.created_by, 'company_user', 'update_data', 'company_data', NEW.id,
            JSON_OBJECT('data_content', OLD.data_content),
            JSON_OBJECT('data_content', NEW.data_content), NOW());
END//

DELIMITER ;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================
DELIMITER //

CREATE PROCEDURE GetCompanyRiskSummary(IN company_id INT)
BEGIN
    SELECT 
        JSON_UNQUOTE(JSON_EXTRACT(data_content, '$.risk_score')) as risk_score,
        JSON_UNQUOTE(JSON_EXTRACT(data_content, '$.status')) as status,
        COUNT(*) as count
    FROM company_data 
    WHERE company_id = company_id 
    AND module_id = (SELECT id FROM marketplace_modules WHERE module_code = 'brc_risk_assessment')
    AND data_type = 'risk_assessment'
    GROUP BY risk_score, status
    ORDER BY risk_score DESC;
END//

CREATE PROCEDURE GetModuleRevenue(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT 
        mm.module_name,
        mm.price,
        COUNT(cms.id) as subscriptions,
        SUM(mm.price) as total_revenue
    FROM marketplace_modules mm
    JOIN company_module_subscriptions cms ON mm.id = cms.module_id
    WHERE cms.subscribed_at BETWEEN start_date AND end_date
    AND cms.status = 'active'
    GROUP BY mm.id
    ORDER BY total_revenue DESC;
END//

DELIMITER ;

-- =====================================================
-- FINAL OPTIMIZATIONS
-- =====================================================
ANALYZE TABLE companies, company_users, marketplace_modules, company_module_subscriptions, company_data;

-- Set up full-text search for modules
ALTER TABLE marketplace_modules ADD FULLTEXT(module_name, description);

-- Create composite indexes for common queries
ALTER TABLE company_data ADD INDEX idx_company_module_type (company_id, module_id, data_type);
ALTER TABLE company_module_subscriptions ADD INDEX idx_active_subscriptions (company_id, status, expires_at);

-- =====================================================
-- SUCCESS MESSAGE
-- =====================================================
SELECT 'BRC Load Platform database installation completed successfully!' as message,
       (SELECT COUNT(*) FROM companies) as companies_created,
       (SELECT COUNT(*) FROM company_users) as users_created,
       (SELECT COUNT(*) FROM marketplace_modules) as modules_created,
       (SELECT COUNT(*) FROM company_module_subscriptions) as subscriptions_created;