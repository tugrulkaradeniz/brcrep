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
│   │   └── modules.php        # Module management
│   ├── ajax/                  # API endpoints
│   │   ├── company-actions.php
│   │   └── module-builder.php # Module builder API ⭐
│   └── layout/                # UI components
│
├── 👥 customer/               # Customer Panels
│   ├── auth/                  # Customer authentication
│   ├── pages/                 # Customer pages
│   │   ├── dashboard.php      # Customer dashboard
│   │   ├── marketplace.php    # Module marketplace
│   │   └── modules.php        # Active modules
│   ├── modules/               # Module implementations
│   │   ├── brc_risk_assessment.php
│   │   └── dynamic-router.php
│   └── ajax/                  # Customer APIs
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
│   └── PlatformAdmin.php      # Admin authentication
│
├── 🔧 services/               # Business Logic
│   ├── TenantContext.php      # Tenant detection
│   └── CompanyContext.php     # Company context
│
├── 🎨 assets/                 # Frontend Assets
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
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

#### **Access Subscribed Modules**
```
http://localhost/brcproject/demo/modules/risk-assessment
http://localhost/brcproject/demo/modules/quality-control
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
-- Companies (Tenants)
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(100) UNIQUE,
    logo VARCHAR(255),
    theme_color VARCHAR(7) DEFAULT '#667eea',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Marketplace Modules
CREATE TABLE marketplace_modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    version VARCHAR(20) DEFAULT '1.0',
    price DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Module Components
CREATE TABLE module_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    component_name VARCHAR(255) NOT NULL,
    component_type VARCHAR(100) NOT NULL,
    component_code VARCHAR(255),
    component_config JSON,
    position_x FLOAT DEFAULT 0,
    position_y FLOAT DEFAULT 0,
    width INT DEFAULT 300,
    height INT DEFAULT 200,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE
);

-- Company Subscriptions
CREATE TABLE company_module_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    module_id INT NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES marketplace_modules(id) ON DELETE CASCADE
);
```

---

## 🚀 **Recent Updates**

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

### **Multi-Tenant Test**
```bash
# Test different company access
curl "http://localhost/brcproject/demo/dashboard"
curl "http://localhost/brcproject/test/dashboard"
curl "http://localhost/brcproject/company1/dashboard"
```

---

## 📈 **Performance**

- **Database Optimization** - Indexed queries, efficient joins
- **AJAX-Driven UI** - Minimal page reloads
- **Component Caching** - Reduced load times
- **Compressed Assets** - Optimized CSS/JS delivery
- **Lazy Loading** - Components loaded on demand

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


# 🏭 BRC Process Management System
## **Complete Architecture & Implementation Plan**

---

## 🎯 **System Overview**

### **Core Concept**
**Template-Based Process Management System** where:
- **Platform** provides master BRC templates
- **Companies** clone and customize templates for their needs  
- **Operators** execute daily workflows with real-time tracking
- **Managers** monitor progress and analyze performance

### **User Roles & Permissions**

| Role | Permissions | Capabilities |
|------|-------------|-------------|
| **Platform Admin** | Full system access | Create master templates, manage global settings |
| **Company Admin** | Company-wide access | Clone templates, customize processes, assign roles |
| **Process Manager** | Process oversight | Monitor workflows, view reports, manage schedules |
| **Quality Controller** | Quality operations | Execute inspections, report issues, update checklists |
| **Operator** | Task execution | Complete assigned steps, report status, upload documents |

---

## 🏗️ **System Architecture**

### **Module Structure**
```
brcproject/
├── 📋 platform/templates/          # Master BRC Templates
│   ├── food_safety_v9.json        # BRC Food Safety v9 template
│   ├── packaging_materials.json   # BRC Packaging template  
│   └── storage_distribution.json  # BRC Storage template
│
├── 🏢 customer/processes/          # Company-specific processes
│   ├── {company_id}/              # Isolated per company
│   │   ├── active_processes/      # Running workflows
│   │   ├── completed_processes/   # Historical data
│   │   └── custom_templates/      # Company customizations
│
├── 🔄 modules/process_management/  # Core system
│   ├── workflow_engine.php        # Process execution engine
│   ├── template_manager.php       # Template operations
│   ├── scheduler.php             # Time-based triggers
│   └── reporting.php             # Analytics & reports
│
└── 📊 components/process_components/ # UI Components
    ├── process_designer.js        # Visual flow builder
    ├── checklist_generator.js     # Dynamic checklist creation
    ├── workflow_tracker.js        # Real-time progress tracking
    └── dashboard_analytics.js     # Performance analytics
```

### **Database Schema**

```sql
-- Master Templates (Platform level)
CREATE TABLE process_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(255) NOT NULL,
    brc_standard VARCHAR(100),
    template_version VARCHAR(20),
    template_data JSON, -- Complete process definition
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Company Process Instances  
CREATE TABLE company_processes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    template_id INT,
    process_name VARCHAR(255),
    process_data JSON, -- Customized process definition
    status ENUM('draft', 'active', 'paused', 'completed', 'archived'),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (template_id) REFERENCES process_templates(id)
);

-- Process Executions (Workflow instances)
CREATE TABLE process_executions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_process_id INT NOT NULL,
    execution_name VARCHAR(255), -- "Lot #12345 - Quality Control"
    current_step INT DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'failed', 'on_hold'),
    started_by INT,
    started_at TIMESTAMP,
    scheduled_completion TIMESTAMP,
    actual_completion TIMESTAMP,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    FOREIGN KEY (company_process_id) REFERENCES company_processes(id)
);

-- Individual Step Executions
CREATE TABLE step_executions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    process_execution_id INT NOT NULL,
    step_number INT NOT NULL,
    step_name VARCHAR(255),
    assigned_to INT, -- User ID
    status ENUM('pending', 'in_progress', 'completed', 'failed', 'skipped'),
    scheduled_start TIMESTAMP,
    actual_start TIMESTAMP,
    scheduled_completion TIMESTAMP,
    actual_completion TIMESTAMP,
    result ENUM('ok', 'issue', 'critical_issue'),
    notes TEXT,
    corrective_action TEXT,
    attachments JSON, -- File uploads
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (process_execution_id) REFERENCES process_executions(id)
);

-- Issues and Non-conformities
CREATE TABLE process_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    step_execution_id INT NOT NULL,
    issue_type ENUM('minor', 'major', 'critical'),
    issue_description TEXT,
    root_cause TEXT,
    corrective_action TEXT,
    preventive_action TEXT,
    responsible_person INT,
    due_date TIMESTAMP,
    resolution_date TIMESTAMP,
    status ENUM('open', 'in_progress', 'resolved', 'verified'),
    FOREIGN KEY (step_execution_id) REFERENCES step_executions(id)
);

-- Time Tracking & Notifications
CREATE TABLE process_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('step_due', 'step_overdue', 'process_complete', 'issue_created'),
    title VARCHAR(255),
    message TEXT,
    related_execution_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🎨 **Frontend Components**

### **1. Process Template Designer**
```javascript
// Master template creation/editing (Platform Admin only)
const ProcessTemplateDesigner = {
    features: [
        'Drag & drop workflow steps',
        'Define control criteria per step', 
        'Set timing requirements',
        'Assign responsibility roles',
        'Configure corrective actions',
        'BRC clause mapping'
    ]
};
```

### **2. Process Customization Panel**
```javascript
// Company-specific customization (Company Admin)
const ProcessCustomizer = {
    features: [
        'Clone master templates',
        'Modify step descriptions', 
        'Adjust timing requirements',
        'Assign specific personnel',
        'Add custom steps',
        'Configure notifications'
    ]
};
```

### **3. Workflow Execution Dashboard**
```javascript
// Real-time process monitoring (All users)
const WorkflowDashboard = {
    views: [
        'Today\'s Tasks - What needs to be done now',
        'Overdue Items - What\'s behind schedule', 
        'Upcoming Tasks - What\'s coming up',
        'Completed Today - What was finished',
        'Issue Tracking - Problems that need attention',
        'Team Performance - Who\'s doing what'
    ]
};
```

### **4. Interactive Checklist Interface**
```javascript
// Step-by-step execution (Operators)
const ChecklistInterface = {
    features: [
        'Step-by-step guidance',
        'OK/Issue/Critical buttons',
        'Photo/document upload',
        'Comments and notes',
        'Corrective action triggers',
        'Next step navigation'
    ]
};
```

---

## 📋 **BRC Quality Control Template**

### **Based on your document - structured data:**

```json
{
    "template_name": "BRC Food Safety Quality Control v9",
    "brc_standard": "Food Safety v9",
    "template_version": "1.0",
    "process_steps": [
        {
            "step_number": 1,
            "step_name": "Hammadde Girişi Kontrolü",
            "category": "Incoming Materials",
            "description": "Tedarikçiden seçilmiş ürün (kuru incir, kuru kayısı, dut, vişne vb.) satın alma",
            "control_criteria": "Hammadde Giriş Kontrol Formundaki değerlere uygunluk",
            "frequency": "Her parti girişinde",
            "responsible_roles": ["Kalite-Lab Sorumlusu"],
            "estimated_duration": 30, // minutes
            "mandatory": true,
            "brc_clauses": ["5.1", "5.2"],
            "corrective_actions": [
                "Ürün İadesi",
                "Tedarikçinin uyarılması"
            ],
            "required_documents": ["Giriş Kontrol Formu", "Tedarikçi Sertifikası"],
            "measurement_parameters": [
                {
                    "parameter": "Nem oranı",
                    "target_value": "Max %20",
                    "measurement_method": "Nem ölçer"
                },
                {
                    "parameter": "Yabancı madde",
                    "target_value": "0%",
                    "measurement_method": "Görsel kontrol"
                }
            ]
        },
        {
            "step_number": 2,
            "step_name": "Ambalaj Girişi",
            "category": "Packaging Control",
            "description": "Ambalaj malzemesi alımında uyulması gereken kriterler",
            "control_criteria": "Talimat değerlere uygunluk", 
            "frequency": "Her ambalaj girişinde",
            "responsible_roles": ["Depo Sorumlusu"],
            "estimated_duration": 15,
            "mandatory": true,
            "corrective_actions": [
                "Ürün İadesi",
                "Tedarikçinin uyarılması"
            ]
        },
        {
            "step_number": 3,
            "step_name": "Yardımcı Malzeme Kontrolü",
            "category": "Supporting Materials",
            "description": "Tuz, Pirinç unu, doğal rafya, selefon, etiket",
            "control_criteria": "İlgili alım talimatına uygunluk",
            "frequency": "Her satın almada",
            "responsible_roles": ["Kalite-Lab Sorumlusu", "Depo Sorumlusu", "Etiket Sorumlusu"],
            "estimated_duration": 20,
            "mandatory": true,
            "corrective_actions": ["Ürün İadesi", "Tedarikçinin uyarılması"]
        },
        // ... Continue with all 27 steps from your document
        {
            "step_number": 27,
            "step_name": "Son Kontrol - Pestisit Analizi",
            "category": "Final Control",
            "description": "Bitmiş organik üründe pestisit analizi",
            "control_criteria": "Üründe pestiste rastlanmaması",
            "frequency": "Yılda bir",
            "responsible_roles": ["Genel Koordinatör", "Kalite-Lab Sorumlusu", "Üretim Sorumlusu"],
            "estimated_duration": 60,
            "mandatory": true,
            "corrective_actions": [
                "Ürünlerin analiz edilmesi/karantinaya alma/imha"
            ],
            "external_lab_required": true
        }
    ],
    "workflow_connections": [
        {"from": 1, "to": 2, "condition": "approved"},
        {"from": 2, "to": 3, "condition": "approved"},
        // ... Define the complete flow
    ],
    "timing_rules": {
        "total_process_time": "3-5 days per batch",
        "critical_path_steps": [4, 8, 12, 15, 20, 25], // Fümigasyon, Seçim, etc.
        "parallel_steps": [[6, 7], [13, 14]], // Steps that can run simultaneously
        "waiting_periods": {
            "dinlendirme": 480, // 8 hours
            "isitma": 120 // 2 hours
        }
    },
    "notification_rules": {
        "upcoming_task": 60, // 1 hour before
        "overdue_warning": 30, // 30 minutes after due
        "critical_delay": 120 // 2 hours overdue
    }
}
```

---

## ⏰ **Time Management System**

### **Status Categories**
```javascript
const TaskStatus = {
    UPCOMING: 'Zamanı gelen işler (Sonraki 2 saat)',
    DUE_NOW: 'Şu anda yapılması gerekenler',
    OVERDUE: 'Geciken işler (Kırmızı alarm)',
    IN_PROGRESS: 'Devam eden işler',
    COMPLETED_TODAY: 'Bugün tamamlanan işler',
    WAITING: 'Bekleyen işler (Önceki adım tamamlanmamış)'
};
```

### **Dashboard Views**
```javascript
const DashboardViews = {
    // Operatör için
    MY_TASKS: {
        title: 'Benim Görevlerim',
        sections: [
            'Acil (Gecikenler)',
            'Bugün yapılacaklar', 
            'Yarın için hazırlık',
            'Tamamladıklarım'
        ]
    },
    
    // Yönetici için  
    TEAM_OVERVIEW: {
        title: 'Takım Durumu',
        sections: [
            'Kritik gecikmeler',
            'Departman performansı',
            'Kaynak kullanımı',
            'Trend analizi'
        ]
    },
    
    // Kalite Kontrolör için
    QUALITY_FOCUS: {
        title: 'Kalite Odaklı Görünüm',
        sections: [
            'Bekleyen onaylar',
            'Tespit edilen sorunlar',
            'Düzeltici faaliyetler',
            'Uygunluk oranları'
        ]
    }
};
```

---

## 📊 **Reporting & Analytics**

### **Report Types**
1. **Process Performance Report**
   - Süreç tamamlanma süreleri
   - Gecikme analizi
   - Sorun kategorileri
   - Düzeltici faaliyet etkinliği

2. **Personnel Performance Report**
   - Kişi bazında tamamlama oranları
   - Kalite skorları
   - Sorumluluk alanı performansı
   - Eğitim ihtiyaçları

3. **Compliance Report**
   - BRC clause uygunluk oranları
   - Audit hazırlık durumu
   - Risk kategorisi dağılımları
   - İyileştirme önerileri

4. **Operational Efficiency Report**
   - Süreç optimizasyon önerileri
   - Kaynak kullanım analizi
   - Maliyet etki analizi
   - Zaman kullanım verimliliği

---

## 🔄 **Implementation Phases**

### **Phase 1: Core Foundation (2-3 weeks)**
- [ ] Database schema implementation
- [ ] Basic template system
- [ ] User role management
- [ ] Simple checklist interface
- [ ] Master BRC template creation

### **Phase 2: Workflow Engine (2-3 weeks)**
- [ ] Process execution engine
- [ ] Time tracking system
- [ ] Status management
- [ ] Notification system
- [ ] Basic reporting

### **Phase 3: Advanced Features (2-3 weeks)**
- [ ] Visual process designer
- [ ] Advanced analytics
- [ ] Document management
- [ ] Issue tracking
- [ ] Performance optimization

### **Phase 4: Polish & Testing (1-2 weeks)**
- [ ] UI/UX improvements
- [ ] Performance optimization
- [ ] User testing
- [ ] Documentation
- [ ] Deployment preparation

---

## 🎯 **Success Criteria**

### **Technical KPIs**
- [ ] Process execution time reduced by 30%
- [ ] Human error rate decreased by 50%
- [ ] Documentation completeness 95%+
- [ ] System availability 99.5%+

### **Business KPIs**
- [ ] BRC audit preparation time reduced by 60%
- [ ] Non-conformity detection rate increased by 40%
- [ ] Training time for new operators reduced by 70%
- [ ] Compliance score improvement 20%+

### **User Experience KPIs**
- [ ] User adoption rate 90%+
- [ ] Daily active usage 80%+
- [ ] User satisfaction score 4.5/5+
- [ ] Support ticket reduction 60%+

---

## 🚀 **Next Steps**

1. **Database Design** - Finalize schema and create migration scripts
2. **Template Creation** - Convert your quality control document to structured template
3. **Core Engine** - Build process execution and time tracking
4. **UI Components** - Create responsive dashboard and checklist interfaces
5. **Integration** - Connect with existing BRC module system
6. **Testing** - Pilot with sample quality control process
7. **Deployment** - Roll out to production environment

This architecture provides a solid foundation for a comprehensive process management system that scales with business needs while maintaining BRC compliance standards.