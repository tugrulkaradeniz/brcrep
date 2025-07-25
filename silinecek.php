<?php
/**
 * =====================================================
 * DÜZELTILMIŞ CONFIG.PHP (Session Sırası Düzeltildi)
 * File: config/config.php
 * =====================================================
 */

// SESSION AYARLARI ÖNCELİKLE (session_start öncesinde)
ini_set('session.lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);

// Database Configuration (sadece tanımlı değilse tanımla)
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'brcload_platform');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Application Settings
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/brcproject');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/../uploads/');
if (!defined('LOG_PATH')) define('LOG_PATH', __DIR__ . '/../logs/');
if (!defined('ASSETS_URL')) define('ASSETS_URL', BASE_URL . '/assets');

// Security Settings
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 3600); // 1 hour
if (!defined('CSRF_TOKEN_EXPIRY')) define('CSRF_TOKEN_EXPIRY', 1800); // 30 minutes
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 8);
if (!defined('LOGIN_ATTEMPTS_LIMIT')) define('LOGIN_ATTEMPTS_LIMIT', 5);
if (!defined('LOGIN_LOCKOUT_TIME')) define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
if (!defined('ALLOWED_FILE_TYPES')) define('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx');

// Email Configuration
if (!defined('SMTP_HOST')) define('SMTP_HOST', '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', 'noreply@brcload.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'BRC Load Platform');

// Environment Settings
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development'); // development, staging, production
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', ENVIRONMENT === 'development');
if (!defined('LOG_LEVEL')) define('LOG_LEVEL', DEBUG_MODE ? 'DEBUG' : 'ERROR');

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

/**
 * =====================================================
 * DÜZELTILMIŞ DBKONFIGUR.PHP (Constant Kontrolü ile)
 * File: dbConnect/dbkonfigur.php
 * =====================================================
 */

// Config dosyası yüklenmemişse yükle
if (!defined('DB_HOST')) {
    if (file_exists(__DIR__ . '/../config/config.php')) {
        require_once __DIR__ . '/../config/config.php';
    } else {
        // Fallback values
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'brcload_platform');
        define('DB_USER', 'root');
        define('DB_PASS', '');
    }
}

try {
    $baglanti = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
} catch (PDOException $e) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die("Database Connection Error: " . $e->getMessage());
    } else {
        die("Database connection failed. Please check your configuration.");
    }
}

/**
 * =====================================================
 * DÜZELTILMIŞ INDEX.PHP (Session Sırası Düzeltildi)
 * File: index.php
 * =====================================================
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Config'i ÖNCELİKLE yükle (session ayarları için)
require_once 'config/config.php';

// ŞİMDİ session başlat (ayarlar yapıldıktan sonra)
session_start();

// Auto-loading
require_once 'config/autoload.php';

// Database connection check
try {
    require_once 'dbConnect/dbkonfigur.php';
} catch (Exception $e) {
    die("Database connection failed. Please check your configuration. Error: " . $e->getMessage());
}

// Multi-tenant context detector
try {
    $tenantContext = TenantContext::detect();
} catch (Exception $e) {
    die("Tenant detection failed: " . $e->getMessage());
}

// Log the request
log_event('DEBUG', 'Request received', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'tenant_type' => $tenantContext->getType(),
    'company_id' => $tenantContext->getCompanyId(),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
]);

// Routing based on tenant type
try {
    switch ($tenantContext->getType()) {
        case 'platform':
            if (file_exists('platform/router.php')) {
                require_once 'platform/router.php';
            } else {
                die('Platform router not found');
            }
            break;
            
        case 'customer':
            if (file_exists('customer/router.php')) {
                require_once 'customer/router.php';
            } else {
                die('Customer router not found');
            }
            break;
            
        case 'main':
            if (file_exists('website/router.php')) {
                require_once 'website/router.php';
            } else {
                // Ana site HTML'i
                ?>
                <!DOCTYPE html>
                <html lang="tr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>BRC Load Platform</title>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                    <style>
                        body {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-family: 'Inter', sans-serif;
                        }
                        .welcome-card {
                            background: white;
                            border-radius: 20px;
                            padding: 3rem;
                            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                            text-align: center;
                            max-width: 500px;
                        }
                        .logo {
                            width: 80px;
                            height: 80px;
                            background: linear-gradient(135deg, #2563eb, #1d4ed8);
                            border-radius: 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 2rem;
                            font-size: 2rem;
                            color: white;
                        }
                    </style>
                </head>
                <body>
                    <div class="welcome-card">
                        <div class="logo">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <h1>BRC Load Platform</h1>
                        <p class="text-muted mb-4">Multi-Tenant SaaS for BRC Compliance Management</p>
                        
                        <div class="d-grid gap-3">
                            <a href="?page=admin" class="btn btn-primary btn-lg">
                                <i class="fas fa-cog me-2"></i>Platform Admin Panel
                            </a>
                            <a href="/brcproject/demo" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-building me-2"></i>Demo Company Panel
                            </a>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <strong>Test Credentials:</strong><br>
                                Admin: admin@brcload.com / admin123<br>
                                Demo Company: admin / password123
                            </small>
                        </div>
                    </div>
                </body>
                </html>
                <?php
            }
            break;
            
        default:
            http_response_code(404);
            echo "Tenant not found. Type: " . $tenantContext->getType();
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                echo "<br>Debug Info:";
                echo "<br>Host: " . ($_SERVER['HTTP_HOST'] ?? 'unknown');
                echo "<br>URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown');
                echo "<br>Query: " . ($_SERVER['QUERY_STRING'] ?? 'unknown');
            }
    }
} catch (Exception $e) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "Router Error: " . $e->getMessage();
        echo "<br>File: " . $e->getFile();
        echo "<br>Line: " . $e->getLine();
        echo "<br>Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "An error occurred. Please try again later.";
    }
}

/**
 * =====================================================
 * DÜZELTILMIŞ CUSTOMER ROUTER (Hata Kontrolü ile)
 * File: customer/router.php
 * =====================================================
 */

global $tenantContext;

// Company existence check
if (!$tenantContext->getCompanyId()) {
    // Demo company yoksa hata göster
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Company Not Found - BRC Load</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Inter', sans-serif;
            }
            .error-card {
                background: white;
                border-radius: 20px;
                padding: 3rem;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 500px;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #dc3545, #c82333);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 2rem;
                font-size: 2rem;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="text-danger">Company Not Found</h1>
            <p class="text-muted mb-4">The requested company is not found or inactive.</p>
            
            <div class="alert alert-info text-start">
                <strong>Possible reasons:</strong>
                <ul class="mb-0 mt-2">
                    <li>Company subdomain does not exist in database</li>
                    <li>Company status is not 'active'</li>
                    <li>Database connection issue</li>
                </ul>
            </div>
            
            <div class="d-grid gap-2">
                <a href="<?= BASE_URL ?>" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <a href="<?= BASE_URL ?>?page=admin" class="btn btn-outline-secondary">
                    <i class="fas fa-cog me-2"></i>Platform Admin
                </a>
            </div>
            
            <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
                <div class="mt-4 pt-3 border-top text-start">
                    <h6>Debug Info:</h6>
                    <small class="text-muted">
                        <strong>Subdomain:</strong> <?= $tenantContext->getSubdomain() ?? 'null' ?><br>
                        <strong>Company ID:</strong> <?= $tenantContext->getCompanyId() ?? 'null' ?><br>
                        <strong>URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'unknown' ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Set company context
CompanyContext::set($tenantContext->getCompanyId());

// Customer authentication check
if (!isset($_SESSION['company_user_id']) || $_SESSION['company_id'] !== $tenantContext->getCompanyId()) {
    if (($_GET['page'] ?? '') !== 'login') {
        header('Location: ?page=login');
        exit;
    }
}

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'login':
        require_once 'customer/auth/login.php';
        break;
        
    case 'dashboard':
        require_once 'customer/pages/dashboard.php';
        break;
        
    case 'marketplace':
        require_once 'customer/pages/marketplace.php';
        break;
        
    case 'modules':
        require_once 'customer/pages/modules.php';
        break;
        
    case 'logout':
        session_destroy();
        header('Location: ?page=login');
        break;
        
    default:
        // Module routing
        $module = $_GET['module'] ?? '';
        if ($module && file_exists("customer/modules/{$module}.php")) {
            require_once "customer/modules/{$module}.php";
        } else {
            require_once 'customer/pages/dashboard.php';
        }
}

/**
 * =====================================================
 * HIZLI DATABASE TEST & FIX
 * File: fix_demo_company.php (Geçici)
 * =====================================================
 */

// Bu dosyayı çalıştırarak demo company'yi ekleyebilirsiniz
// http://localhost/brcproject/fix_demo_company.php

require_once 'config/config.php';
require_once 'dbConnect/dbkonfigur.php';

try {
    // Demo company'nin var olup olmadığını kontrol et
    $stmt = $baglanti->prepare("SELECT COUNT(*) FROM companies WHERE subdomain = 'demo'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "<h3>Demo company bulunamadı. Ekleniyor...</h3>";
        
        // Demo company ekle
        $stmt = $baglanti->prepare("
            INSERT INTO companies (company_name, subdomain, contact_name, contact_email, plan_type, status, theme_color) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Demo Company Ltd.',
            'demo',
            'John Doe',
            'john@democompany.com',
            'premium',
            'active',
            '#007bff'
        ]);
        
        $companyId = $baglanti->lastInsertId();
        echo "<p>✅ Demo company eklendi (ID: $companyId)</p>";
        
        // Demo user ekle
        $stmt = $baglanti->prepare("
            INSERT INTO company_users (company_id, username, email, password, first_name, last_name, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $companyId,
            'admin',
            'admin@democompany.com',
            password_hash('password123', PASSWORD_DEFAULT),
            'Demo',
            'Admin',
            'owner'
        ]);
        
        echo "<p>✅ Demo user eklendi</p>";
        echo "<p><strong>Artık demo company'ye giriş yapabilirsiniz:</strong></p>";
        echo "<p>URL: <a href='/brcproject/demo'>/brcproject/demo</a></p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: password123</p>";
        
    } else {
        echo "<h3>✅ Demo company zaten mevcut!</h3>";
        
        // Demo company bilgilerini göster
        $stmt = $baglanti->prepare("SELECT * FROM companies WHERE subdomain = 'demo'");
        $stmt->execute();
        $company = $stmt->fetch();
        
        echo "<p><strong>Company:</strong> " . $company['company_name'] . "</p>";
        echo "<p><strong>Status:</strong> " . $company['status'] . "</p>";
        echo "<p><strong>URL:</strong> <a href='/brcproject/demo'>/brcproject/demo</a></p>";
        
        // Demo user bilgilerini göster
        $stmt = $baglanti->prepare("SELECT * FROM company_users WHERE company_id = ?");
        $stmt->execute([$company['id']]);
        $users = $stmt->fetchAll();
        
        echo "<p><strong>Users:</strong></p>";
        foreach ($users as $user) {
            echo "<p>- {$user['username']} ({$user['email']}) - {$user['role']}</p>";
        }
    }
    
    echo "<hr>";
    echo '<p><a href="/brcproject/">← Ana Sayfaya Dön</a></p>';
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}