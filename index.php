<?php

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
                            <!-- FIX: Platform admin login için doğru URL -->
                            <a href="?page=admin&action=login" class="btn btn-primary btn-lg">
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
                        
                        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
                            <div class="mt-4 pt-3 border-top text-start">
                                <h6>Debug Info:</h6>
                                <small class="text-muted">
                                    <strong>Tenant Type:</strong> <?= $tenantContext->getType() ?><br>
                                    <strong>Query String:</strong> <?= $_SERVER['QUERY_STRING'] ?? 'empty' ?><br>
                                    <strong>Request URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'unknown' ?>
                                </small>
                            </div>
                        <?php endif; ?>
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
?>
