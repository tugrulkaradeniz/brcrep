<?php
/**
 * BRC Load Platform - Database Setup Script
 * Run this file once to create all required tables and sample data
 */

// Include config
require_once 'config/config.php';

// Generate proper password hashes
$passwordHash = password_hash('password', PASSWORD_DEFAULT);
$admin123Hash = password_hash('admin123', PASSWORD_DEFAULT);

try {
    // Connect to MySQL server (without specific database)
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "<h2>🚀 BRC Load Platform - Database Setup</h2>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>";
    
    // Create database
    echo "📁 Creating database: " . DB_NAME . "...<br>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    echo "✅ Database created successfully!<br><br>";
    
    // 1. Platform Admins Table
    echo "👨‍💼 Creating platform_admins table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS platform_admins (
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
        )
    ");
    echo "✅ platform_admins table created!<br>";
    
    // 2. Companies Table
    echo "🏢 Creating companies table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS companies (
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
        )
    ");
    echo "✅ companies table created!<br>";
    
    // 3. Company Users Table
    echo "👥 Creating company_users table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS company_users (
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
        )
    ");
    echo "✅ company_users table created!<br>";
    
    // 4. Marketplace Modules Table
    echo "🧩 Creating marketplace_modules table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS marketplace_modules (
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
        )
    ");
    echo "✅ marketplace_modules table created!<br>";
    
    // 5. Module Components Table
    echo "🧱 Creating module_components table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS module_components (
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
        )
    ");
    echo "✅ module_components table created!<br>";
    
    // 6. Company Module Subscriptions Table
    echo "💳 Creating company_module_subscriptions table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS company_module_subscriptions (
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
        )
    ");
    echo "✅ company_module_subscriptions table created!<br>";
    
    // 7. Company Data Table
    echo "💾 Creating company_data table...<br>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS company_data (
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
        )
    ");
    echo "✅ company_data table created!<br><br>";
    
    // INSERT SAMPLE DATA
    echo "<strong>📊 Inserting sample data...</strong><br><br>";
    
    // Platform Admin Users
    echo "👨‍💼 Adding platform admin users...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO platform_admins (username, email, password, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@brcload.com', $admin123Hash, 'Platform', 'Admin', 'super_admin', 'active']);
    $stmt->execute(['moderator', 'moderator@brcload.com', $passwordHash, 'Content', 'Moderator', 'moderator', 'active']);
    echo "✅ Platform admin users added!<br>";
    
    // Demo Companies
    echo "🏢 Adding demo companies...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO companies (company_name, subdomain, contact_name, contact_email, contact_phone, plan_type, status, max_users, theme_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', '+1-555-0123', 'premium', 'active', 15, '#007bff']);
    $stmt->execute(['Test Corporation', 'test', 'Jane Smith', 'jane@testcorp.com', '+1-555-0456', 'basic', 'trial', 5, '#28a745']);
    $stmt->execute(['Enterprise Solutions', 'enterprise', 'Mike Johnson', 'mike@enterprise.com', '+1-555-0789', 'enterprise', 'active', 50, '#6f42c1']);
    echo "✅ Demo companies added!<br>";
    
    // Company Users
    echo "👥 Adding company users...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_users (company_id, username, email, password, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    // Demo Company Users
    $stmt->execute([1, 'admin', 'admin@democompany.com', $passwordHash, 'John', 'Doe', 'owner', 'active']);
    $stmt->execute([1, 'manager', 'manager@democompany.com', $passwordHash, 'Sarah', 'Wilson', 'manager', 'active']);
    $stmt->execute([1, 'user1', 'user1@democompany.com', $passwordHash, 'Robert', 'Brown', 'user', 'active']);
    // Test Corp Users
    $stmt->execute([2, 'admin', 'admin@testcorp.com', $passwordHash, 'Jane', 'Smith', 'owner', 'active']);
    $stmt->execute([2, 'user1', 'user1@testcorp.com', $passwordHash, 'Tom', 'Davis', 'user', 'active']);
    // Enterprise Users
    $stmt->execute([3, 'admin', 'admin@enterprise.com', $passwordHash, 'Mike', 'Johnson', 'owner', 'active']);
    echo "✅ Company users added!<br>";
    
    // Base Modules
    echo "🧩 Adding base modules...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO marketplace_modules (module_name, module_code, description, category, price, is_base_module, version, icon, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['BRC Risk Assessment', 'brc_risk_assessment', 'Comprehensive BRC-compliant risk assessment module with automated workflows and compliance tracking.', 'BRC Compliance', 99.99, 1, '2.1', 'shield-alt', 'published', 1]);
    $stmt->execute(['Quality Management', 'quality_mgmt', 'Complete quality management system with document control, non-conformity tracking, and audit management.', 'Quality Management', 79.99, 1, '1.8', 'award', 'published', 1]);
    $stmt->execute(['Safety Management', 'safety_mgmt', 'Comprehensive safety management system with incident tracking, safety training, and compliance monitoring.', 'Safety & Health', 89.99, 1, '1.5', 'hard-hat', 'published', 1]);
    $stmt->execute(['Audit Management', 'audit_mgmt', 'Complete audit management system with planning, execution, and follow-up capabilities for all audit types.', 'Audit Management', 69.99, 1, '1.3', 'clipboard-check', 'published', 1]);
    $stmt->execute(['Document Control', 'document_control', 'Advanced document control system with version management, approval workflows, and distribution tracking.', 'Document Control', 49.99, 1, '2.0', 'file-alt', 'published', 1]);
    $stmt->execute(['Training Management', 'training_mgmt', 'Complete training management system with course creation, tracking, and competency assessment capabilities.', 'Training & Development', 59.99, 1, '1.7', 'graduation-cap', 'published', 1]);
    echo "✅ Base modules added!<br>";
    
    // Module Components
    echo "🧱 Adding module components...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO module_components (module_id, component_name, component_type, component_code, config, order_index, is_locked) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'Risk Assessment Matrix', 'risk-matrix', 'risk_matrix_5x5', '{"size": "5x5", "colors": ["#22c55e", "#f59e0b", "#ef4444"]}', 1, 1]);
    $stmt->execute([1, 'Risk Assessment Form', 'smart-form', 'risk_form', '{"fields": ["risk_description", "likelihood", "impact", "location"]}', 2, 1]);
    $stmt->execute([1, 'Approval Workflow', 'approval-flow', 'risk_approval', '{"steps": ["assessor", "manager", "quality_manager"]}', 3, 1]);
    $stmt->execute([1, 'Dashboard Overview', 'dashboard-grid', 'risk_dashboard', '{"cards": ["total_risks", "completed", "pending", "compliance_score"]}', 4, 1]);
    echo "✅ Module components added!<br>";
    
    // Sample Subscriptions
    echo "💳 Adding sample subscriptions...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_module_subscriptions (company_id, module_id, status, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 1, 'active', '2025-01-24']); // Demo Company → BRC Risk Assessment
    $stmt->execute([1, 2, 'active', '2025-01-24']); // Demo Company → Quality Management
    $stmt->execute([2, 1, 'active', '2024-12-24']); // Test Corp → BRC Risk Assessment
    $stmt->execute([3, 1, 'active', '2025-06-24']); // Enterprise → BRC Risk Assessment
    $stmt->execute([3, 2, 'active', '2025-06-24']); // Enterprise → Quality Management
    $stmt->execute([3, 3, 'active', '2025-06-24']); // Enterprise → Safety Management
    echo "✅ Sample subscriptions added!<br>";
    
    // Sample Company Data
    echo "💾 Adding sample company data...<br>";
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_data (company_id, module_id, data_type, data_id, data_content, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 1, 'risk_assessment', 'RA-2024-001', '{"risk_description": "Bacterial contamination in raw material storage", "likelihood": 3, "impact": 4, "risk_score": 12, "status": "approved"}', 1]);
    $stmt->execute([1, 1, 'risk_assessment', 'RA-2024-002', '{"risk_description": "Unauthorized access to production area", "likelihood": 2, "impact": 3, "risk_score": 6, "status": "pending"}', 1]);
    echo "✅ Sample company data added!<br><br>";
    
    // Verification
    echo "<strong>🔍 Verification:</strong><br>";
    $tables = ['platform_admins', 'companies', 'company_users', 'marketplace_modules', 'company_module_subscriptions'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "✅ $table: $count records<br>";
    }
    
    echo "<br><div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<strong>🎉 DATABASE SETUP COMPLETE!</strong><br><br>";
    echo "<strong>📋 Test Credentials:</strong><br>";
    echo "<strong>Platform Admin:</strong><br>";
    echo "• Email: admin@brcload.com<br>";
    echo "• Password: admin123<br><br>";
    echo "<strong>Demo Company (/demo):</strong><br>";
    echo "• Username: admin<br>";
    echo "• Password: password<br><br>";
    echo "<strong>Test Company (/test):</strong><br>";
    echo "• Username: admin<br>";
    echo "• Password: password<br>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<br><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Go to Platform</a>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<strong>❌ Database Setup Failed:</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config/config.php";
    echo "</div>";
}
?>