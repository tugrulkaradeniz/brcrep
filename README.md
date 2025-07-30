# 🏭 BRC Load Platform

> **Enterprise-Grade Multi-Tenant SaaS Platform for BRC Compliance & Quality Management**

A comprehensive, production-ready platform that enables companies to build, deploy, and manage BRC compliance modules with a visual drag-and-drop interface.

[![PHP](https://img.shields.io/badge/PHP-8%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow.svg)](https://javascript.info)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🚀 **Key Features**

### 🎨 **Visual Module Builder**
- **Drag & Drop Interface** - Professional-grade component designer
- **Component Library** - 15+ pre-built components (Risk Matrix, Smart Forms, KPI Cards, etc.)
- **Live Preview** - Real-time module preview and testing
- **Canvas-based Design** - Visual positioning with grid system
- **Properties Panel** - Dynamic component configuration

### 🏭 **BRC Process Management System** ⭐ **NEW**
- **Template-Based Workflows** - Pre-built BRC Food Safety v9 process templates
- **Real-Time Process Tracking** - Monitor quality control steps with live status updates
- **Role-Based Task Management** - Admin defines, operators execute, managers monitor
- **Time-Critical Alerts** - Overdue tasks, upcoming deadlines, critical issues
- **Interactive Checklists** - Step-by-step guidance with OK/Issue/Critical buttons
- **Smart Analytics** - Process performance, compliance rates, efficiency metrics
- **Issue & CAPA Tracking** - Non-conformity management with corrective actions
- **Document Integration** - Upload certificates, photos, inspection reports
- **Dual Dashboard System** - General company dashboard + specialized process dashboard

### 🏢 **Multi-Tenant Architecture**
- **Company Isolation** - Complete data separation per tenant
- **Path-based Routing** - XAMPP compatible (`/demo`, `/test`, `/company1`)
- **Custom Branding** - Per-company logos, colors, and themes
- **Scalable Infrastructure** - Enterprise-ready architecture

### 📊 **BRC Compliance Suite**
- **Risk Assessment Matrix** - Interactive 5x5 risk calculator
- **Quality Control Tables** - Process monitoring and tracking
- **Approval Workflows** - Multi-step approval processes
- **Compliance Reporting** - Automated BRC standard reports
- **Audit Management** - Comprehensive audit trails

### 🛠 **Advanced Components**
- **Smart Forms** - Dynamic validation and conditional logic
- **File Upload** - Drag & drop with validation
- **Signature Pad** - Digital signature capture
- **Chart Visualizations** - Real-time data charts
- **Status Tracking** - Progress monitoring
- **Notification System** - Email, SMS, in-app alerts

---

## 📁 **Project Structure**

```
brcproject/
├── 🎛️ platform/              # Admin Platform
│   ├── auth/                  # Authentication system
│   ├── pages/                 # Admin pages
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── companies.php      # Company management
│   │   ├── module-builder.php # Visual module builder ⭐
│   │   ├── process-templates.php # BRC process templates ⭐ NEW
│   │   └── modules.php        # Module management
│   ├── ajax/                  # API endpoints
│   │   ├── company-actions.php
│   │   ├── module-builder.php # Module builder API ⭐
│   │   └── process-management.php # Process workflow API ⭐ NEW
│   └── layout/                # UI components
│
├── 👥 customer/               # Customer Panels
│   ├── auth/                  # Customer authentication
│   ├── pages/                 # Customer pages
│   │   ├── dashboard.php      # General company dashboard
│   │   ├── process-dashboard.php # BRC process monitoring ⭐ NEW
│   │   ├── quality-control.php   # Quality control interface ⭐ NEW
│   │   ├── marketplace.php    # Module marketplace
│   │   └── modules.php        # Active modules
│   ├── modules/               # Module implementations
│   │   ├── brc_risk_assessment.php
│   │   ├── process_workflows.php # ⭐ NEW
│   │   └── dynamic-router.php
│   └── ajax/                  # Customer APIs
│       ├── module-actions.php
│       └── process-execution.php # ⭐ NEW
│
├── 🌐 website/                # Main Website
│   ├── pages/                 # Public pages
│   └── router.php             # Website routing
│
├── ⚙️ config/                 # Configuration
│   ├── config.php             # Main config
│   ├── autoload.php           # Class autoloader
│   └── functions.php          # Utility functions
│
├── 📦 models/                 # Data Models
│   ├── Company.php            # Company management
│   ├── Module.php             # Module marketplace
│   ├── ProcessTemplate.php    # BRC process templates ⭐ NEW
│   ├── ProcessExecution.php   # Workflow instances ⭐ NEW
│   └── PlatformAdmin.php      # Admin authentication
│
├── 🔧 services/               # Business Logic
│   ├── TenantContext.php      # Tenant detection
│   ├── CompanyContext.php     # Company context
│   ├── WorkflowEngine.php     # Process execution engine ⭐ NEW
│   └── NotificationService.php # Time-based alerts ⭐ NEW
│
├── 🎨 assets/                 # Frontend Assets
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
│       ├── process-dashboard.js # ⭐ NEW
│       └── workflow-tracker.js  # ⭐ NEW
│
├── 📋 templates/              # BRC Process Templates ⭐ NEW
│   ├── brc_food_safety_v9.json
│   ├── quality_control_plan.json
│   └── environmental_monitoring.json
│
└── 🗄️ dbConnect/              # Database
    └── dbkonfigur.php         # DB configuration
```

---

## 🛠 **Installation & Setup**

### **Prerequisites**
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- XAMPP/WAMP (for local development)

### **Quick Start**

1. **Clone the Repository**
```bash
git clone https://github.com/tugrulkaradeniz/brcrep.git
cd brcrep
```

2. **Database Setup**
```sql
-- Create database
CREATE DATABASE brcproject CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema
mysql -u root -p brcproject < database/schema.sql
```

3. **Configure Database**
```php
// dbConnect/dbkonfigur.php
$host = 'localhost';
$dbname = 'brcproject';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
```

4. **Set Permissions**
```bash
chmod 755 -R brcproject/
chmod 777 -R brcproject/logs/
chmod 777 -R brcproject/uploads/
```

5. **Access the Platform**
- **Main Website**: `http://localhost/brcproject/`
- **Admin Platform**: `http://localhost/brcproject/admin/`
- **Demo Company**: `http://localhost/brcproject/demo/`

### **Multi-Tenant Setup**

For production with real subdomains:
```apache
# .htaccess configuration
RewriteEngine On
RewriteCond %{HTTP_HOST} ^([^.]+)\.yourdomain\.com$
RewriteRule ^(.*)$ /brcproject/customer/?company=%1&path=$1 [QSA,L]
```

---

## 🎯 **Usage Guide**

### **1. Admin Platform**

#### **Company Management**
```php
// Create new company
POST /platform/ajax/company-actions.php
{
    "action": "create_company",
    "name": "ABC Manufacturing",
    "domain": "abc-manufacturing",
    "admin_email": "admin@abc.com"
}
```

#### **Module Builder** ⭐
```javascript
// Access the visual module builder
http://localhost/brcproject/admin/module-builder

// Create new module
http://localhost/brcproject/admin/module-builder?action=create

// Edit existing module
http://localhost/brcproject/admin/module-builder?edit=30
```

#### **BRC Process Management** ⭐ **NEW**
```javascript
// Access process template manager
http://localhost/brcproject/admin/process-templates

// Create quality control process from template
POST /platform/ajax/process-management.php
{
    "action": "create_from_template",
    "template_id": "brc_food_safety_v9",
    "company_id": 5,
    "process_name": "Daily Quality Control - Dried Fruits"
}
```

### **2. Module Builder API**

#### **Save Module with Components**
```javascript
// Frontend JavaScript usage
const moduleData = {
    name: "Quality Control Process",
    description: "Complete quality management workflow",
    category: "Quality Management", 
    version: "1.0",
    price: 299
};

const components = [
    {
        name: "Risk Assessment Matrix",
        type: "risk-matrix",
        config: { size: "5x5", axes: ["Likelihood", "Impact"] },
        position_x: 100,
        position_y: 50,
        width: 300,
        height: 300
    }
];

// Save via API
const response = await fetch('/platform/ajax/module-builder.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        action: 'save',
        module_id: 30,
        ...moduleData,
        components: JSON.stringify(components)
    })
});
```

### **3. Customer Panel**

#### **Module Marketplace**
```php
// Subscribe to module
POST /customer/ajax/module-actions.php
{
    "action": "subscribe",
    "module_id": 30,
    "company_id": 5
}
```

#### **BRC Process Execution** ⭐ **NEW**
```javascript
// Start quality control process for batch
POST /customer/ajax/process-execution.php
{
    "action": "start_process",
    "process_id": 15,
    "batch_name": "Lot #12345 - Dried Apricots",
    "scheduled_completion": "2025-02-01 17:00:00"
}

// Complete process step
POST /customer/ajax/process-execution.php
{
    "action": "complete_step",
    "step_execution_id": 234,
    "result": "ok", // or "issue" or "critical_issue"
    "notes": "Moisture content within acceptable limits",
    "attachments": ["moisture_test_result.pdf"]
}

// Report issue
POST /customer/ajax/process-execution.php
{
    "action": "report_issue",
    "step_execution_id": 234,
    "issue_type": "minor",
    "description": "Slight discoloration in 5% of batch",
    "corrective_action": "Additional sorting required"
}
```

#### **Access Process Dashboard**
```
// Real-time process monitoring
http://localhost/brcproject/demo/process-dashboard

// Quality control interface  
http://localhost/brcproject/demo/quality-control

// Individual process tracking
http://localhost/brcproject/demo/quality-control?execution_id=123
```

---

## 🧩 **Component Library**

### **Display Components**
| Component | Description | Use Case |
|-----------|-------------|----------|
| 🛡️ **Risk Matrix** | 5x5 Interactive risk assessment | BRC risk evaluation |
| 📊 **Charts** | Data visualization (Line, Bar, Pie) | KPI tracking |
| 📈 **KPI Cards** | Key performance indicators | Dashboard metrics |
| 🎯 **Status Tracker** | Progress monitoring | Workflow status |
| 🏭 **Quality Control Table** | Process monitoring | Quality management |

### **Input Components**
| Component | Description | Use Case |
|-----------|-------------|----------|
| 📝 **Smart Forms** | Dynamic forms with validation | Data collection |
| 📁 **File Upload** | Drag & drop file uploads | Document management |
| 📅 **Date Picker** | Advanced date selection | Scheduling |
| ✍️ **Signature Pad** | Digital signature capture | Approvals |

### **Action Components**
| Component | Description | Use Case |
|-----------|-------------|----------|
| ✅ **Approval Flow** | Multi-step approval process | Workflow management |
| 🔔 **Notifications** | Email, SMS, in-app alerts | Communication |
| 📋 **Report Generator** | Automated report creation | Compliance reporting |

### **Layout Components**
| Component | Description | Use Case |
|-----------|-------------|----------|
| ▦ **Dashboard Grid** | Responsive grid layout | Dashboard design |
| 🗃️ **Card Container** | Flexible card layouts | Content organization |
| 📑 **Tab Panel** | Tabbed content | Information grouping |

---

## 🔧 **API Reference**

### **Module Builder Endpoints**

```php
POST /platform/ajax/module-builder.php
```

#### **Actions:**

| Action | Description | Parameters |
|--------|-------------|------------|
| `create_module` | Create new module | `name`, `description`, `category` |
| `update_module` | Update existing module | `module_id`, `name`, `description`, `category`, `version`, `price` |
| `get_modules` | List all modules | - |
| `get_module_details` | Get module details | `module_id` |
| `add_component` | Add component to module | `module_id`, `component_name`, `component_type`, `component_config`, `position_x`, `position_y`, `width`, `height` |
| `delete_all_components` | Remove all components | `module_id` |
| `save` | Save module + components | `module_id`, module fields, `components` (JSON array) |

#### **Example Responses:**

```json
// Success Response
{
    "success": true,
    "message": "Module created successfully",
    "module_id": 30,
    "module_name": "Quality Control Process"
}

// Error Response  
{
    "success": false,
    "error": "Module name is required"
}
```

---

## 🔐 **Security Features**

- **CSRF Protection** - All forms protected against CSRF attacks
- **Input Sanitization** - All user inputs sanitized and validated
- **SQL Injection Prevention** - Prepared statements used throughout
- **Session Security** - Secure session management
- **Password Hashing** - BCrypt password hashing
- **Rate Limiting** - API rate limiting implemented
- **Data Isolation** - Complete tenant data separation
- **Access Control** - Role-based permissions

---

## 🗄️ **Database Schema**

### **Core Tables**

```sql
-- Companies (Enhanced Multi-Tenant)
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(100) UNIQUE,
    company_code VARCHAR(50),
    logo VARCHAR(255),
    theme_color VARCHAR(7) DEFAULT '#667eea',
    contact_email VARCHAR(255),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- BRC Process Templates ⭐ NEW
CREATE TABLE process_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(255) NOT NULL,
    template_code VARCHAR(100) UNIQUE NOT NULL,
    brc_standard VARCHAR(100),
    template_version VARCHAR(20) DEFAULT '1.0',
    template_data JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Company Process Instances ⭐ NEW
CREATE TABLE company_processes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    template_id INT,
    process_name VARCHAR(255) NOT NULL,
    process_code VARCHAR(100),
    process_data JSON NOT NULL,
    status ENUM('draft', 'active', 'paused', 'completed', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Process Executions (Workflow Instances) ⭐ NEW  
CREATE TABLE process_executions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_process_id INT NOT NULL,
    execution_name VARCHAR(255) NOT NULL,
    batch_number VARCHAR(100),
    current_step INT DEFAULT 1,
    total_steps INT DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'failed', 'on_hold', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    started_at TIMESTAMP,
    scheduled_completion TIMESTAMP,
    actual_completion TIMESTAMP,
    FOREIGN KEY (company_process_id) REFERENCES company_processes(id) ON DELETE CASCADE
);

-- Step Executions (Individual Tasks) ⭐ NEW
CREATE TABLE step_executions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    process_execution_id INT NOT NULL,
    step_number INT NOT NULL,
    step_name VARCHAR(255) NOT NULL,
    assigned_to INT,
    assigned_role VARCHAR(100),
    status ENUM('pending', 'in_progress', 'completed', 'failed', 'skipped', 'waiting') DEFAULT 'pending',
    result ENUM('ok', 'issue', 'critical_issue') NULL,
    scheduled_completion TIMESTAMP,
    actual_completion TIMESTAMP,
    estimated_duration INT DEFAULT 30,
    notes TEXT,
    corrective_action TEXT,
    attachments JSON,
    FOREIGN KEY (process_execution_id) REFERENCES process_executions(id) ON DELETE CASCADE
);

-- Process Issues & CAPA ⭐ NEW
CREATE TABLE process_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    step_execution_id INT NOT NULL,
    issue_type ENUM('minor', 'major', 'critical') NOT NULL,
    issue_description TEXT NOT NULL,
    root_cause TEXT,
    corrective_action TEXT,
    preventive_action TEXT,
    responsible_person INT,
    due_date TIMESTAMP,
    resolution_date TIMESTAMP,
    status ENUM('open', 'in_progress', 'resolved', 'verified', 'closed') DEFAULT 'open',
    FOREIGN KEY (step_execution_id) REFERENCES step_executions(id) ON DELETE CASCADE
);

-- Analytics Views ⭐ NEW
CREATE VIEW v_active_processes AS
SELECT pe.*, cp.process_name, c.name as company_name,
       ROUND((pe.current_step / pe.total_steps) * 100, 1) as completion_percentage
FROM process_executions pe
JOIN company_processes cp ON pe.company_process_id = cp.id
JOIN companies c ON cp.company_id = c.id
WHERE pe.status IN ('pending', 'in_progress');

CREATE VIEW v_daily_summary AS
SELECT c.id as company_id, c.name as company_name,
       COUNT(CASE WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion <= DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 1 END) as due_soon,
       COUNT(CASE WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion < NOW() THEN 1 END) as overdue,
       COUNT(CASE WHEN se.status = 'completed' AND DATE(se.actual_completion) = CURDATE() THEN 1 END) as completed_today,
       COUNT(CASE WHEN pi.status IN ('open', 'in_progress') THEN 1 END) as open_issues
FROM companies c
LEFT JOIN company_processes cp ON c.id = cp.company_id
LEFT JOIN process_executions pe ON cp.id = pe.company_process_id
LEFT JOIN step_executions se ON pe.id = se.process_execution_id
LEFT JOIN process_issues pi ON se.id = pi.step_execution_id
WHERE c.status = 'active'
GROUP BY c.id;
```

---

## 🚀 **Recent Updates**

### **v3.0.0** - BRC Process Management System ⭐ **NEW**
- ✅ **Template-Based Process Management** - Master BRC Food Safety v9 templates
- ✅ **Real-Time Workflow Tracking** - Live status monitoring with time-critical alerts
- ✅ **Role-Based Task Management** - Admin defines, operators execute, managers monitor
- ✅ **Interactive Quality Control** - Step-by-step checklists with OK/Issue/Critical buttons
- ✅ **Smart Time Management** - Overdue alerts, upcoming deadlines, priority queuing
- ✅ **Issue & CAPA Tracking** - Non-conformity management with corrective actions
- ✅ **Process Analytics** - Performance metrics, compliance rates, efficiency reports
- ✅ **Document Integration** - Upload inspection reports, certificates, photos
- ✅ **Dual Dashboard System** - General company dashboard + specialized BRC process dashboard
- ✅ **Database Views & Optimization** - v_active_processes, v_daily_summary, v_my_tasks

### **v2.1.0** - Module Builder Enhancement
- ✅ **Fixed JSON/FormData compatibility** in module-builder API
- ✅ **Component positioning system** - Drag & drop with real-time updates
- ✅ **Enhanced component library** - 15+ professional components
- ✅ **Auto-save functionality** - Prevent data loss
- ✅ **Price field support** - Module pricing in marketplace
- ✅ **Improved error handling** - Better debugging and logging

### **Recent Bug Fixes**
- 🔧 Fixed `module_id` NULL issue in component saving
- 🔧 Resolved `component_code` field requirement
- 🔧 JSON decode compatibility for all API endpoints
- 🔧 Component drag & drop positioning accuracy
- 🔧 Price field persistence in module updates

---

## 🧪 **Testing**

### **Module Builder Test**
```javascript
// Test module creation and component addition
async function testModuleBuilder() {
    // 1. Create module
    const createResponse = await apiCall('create_module', {
        name: 'Test Quality Module',
        description: 'Testing module creation',
        category: 'Quality Management'
    });
    
    console.log('Module created:', createResponse.module_id);
    
    // 2. Add components
    const addComponentResponse = await apiCall('add_component', {
        module_id: createResponse.module_id,
        component_name: 'Test Risk Matrix',
        component_type: 'risk-matrix',
        component_config: '{"size":"5x5"}',
        position_x: 100,
        position_y: 100,
        width: 300,
        height: 300
    });
    
    console.log('Component added:', addComponentResponse.component_id);
}
```

### **BRC Process Management Test** ⭐ **NEW**
```javascript
// Test process execution workflow
async function testProcessWorkflow() {
    // 1. Create process from template
    const processResponse = await apiCall('create_from_template', {
        template_id: 'brc_food_safety_v9',
        company_id: 5,
        process_name: 'Test Quality Control - Batch #001'
    });
    
    // 2. Start process execution
    const executionResponse = await apiCall('start_process', {
        process_id: processResponse.process_id,
        batch_name: 'Test Batch #001',
        scheduled_completion: '2025-02-01 17:00:00'
    });
    
    // 3. Complete first step
    const stepResponse = await apiCall('complete_step', {
        step_execution_id: executionResponse.first_step_id,
        result: 'ok',
        notes: 'All parameters within acceptable range'
    });
    
    console.log('Process workflow test completed successfully');
}
```

### **Multi-Tenant Test**
```bash
# Test different company access
curl "http://localhost/brcproject/demo/dashboard"
curl "http://localhost/brcproject/test/dashboard"
curl "http://localhost/brcproject/company1/dashboard"

# Test process isolation
curl "http://localhost/brcproject/demo/process-dashboard"
curl "http://localhost/brcproject/test/quality-control"
```

---

## 📈 **Performance**

- **Database Optimization** - Indexed queries, efficient joins, analytics views
- **AJAX-Driven UI** - Minimal page reloads, real-time updates
- **Component Caching** - Reduced load times
- **Compressed Assets** - Optimized CSS/JS delivery
- **Lazy Loading** - Components loaded on demand
- **Process Views** - Pre-calculated v_active_processes, v_daily_summary for fast dashboards
- **JSON Template System** - Efficient process data storage and retrieval
- **Multi-Level Caching** - Template caching, dashboard data caching, step execution optimization

---

## 🤝 **Contributing**

1. Fork the repository
2. Create feature branch (`git checkout -b feature/new-component`)
3. Commit changes (`git commit -am 'Add new component'`)
4. Push to branch (`git push origin feature/new-component`)
5. Create Pull Request

### **Development Guidelines**
- Follow PSR-4 autoloading standards
- Use prepared statements for all database queries
- Implement proper error handling and logging
- Add JSDoc comments for JavaScript functions
- Test multi-tenant functionality

---

## 📜 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🛟 **Support**

### **Documentation**
- [API Documentation](docs/api.md)
- [Component Development Guide](docs/components.md)
- [Multi-Tenant Setup Guide](docs/multi-tenant.md)

### **Community**
- [GitHub Issues](https://github.com/tugrulkaradeniz/brcrep/issues)
- [Discussions](https://github.com/tugrulkaradeniz/brcrep/discussions)

### **Commercial Support**
For enterprise support, custom development, or deployment assistance:
- **Email**: support@brcload.com
- **Website**: [brcload.com](https://brcload.com)

---

## ⭐ **Acknowledgments**

- **BRC Global Standards** - Compliance framework inspiration
- **Bootstrap Team** - UI framework
- **PHP Community** - Open source ecosystem
- **Contributors** - Everyone who helped build this platform

---

<div align="center">

**[⬆ Back to Top](#-brc-load-platform)**

Made with ❤️ for the **BRC Compliance Community**

[![Stars](https://img.shields.io/github/stars/tugrulkaradeniz/brcrep?style=social)](https://github.com/tugrulkaradeniz/brcrep/stargazers)
[![Forks](https://img.shields.io/github/forks/tugrulkaradeniz/brcrep?style=social)](https://github.com/tugrulkaradeniz/brcrep/network/members)

</div>