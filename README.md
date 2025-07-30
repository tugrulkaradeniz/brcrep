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