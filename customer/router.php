<?php

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

    case 'module':
        $moduleCode = $_GET['module_code'] ?? '';
        if ($moduleCode) {
            // Check subscription first
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Database connection for subscription check
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=brcload_platform;charset=utf8mb4", "root", "", [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                
                $moduleCheckStmt = $pdo->prepare("
                    SELECT cms.*, mm.module_name 
                    FROM company_module_subscriptions cms
                    JOIN marketplace_modules mm ON cms.module_id = mm.id
                    WHERE cms.company_id = ? AND mm.module_code = ? AND cms.status = 'active'
                ");
                $moduleCheckStmt->execute([$_SESSION['company_id'] ?? 1, $moduleCode]);
                $subscription = $moduleCheckStmt->fetch();
                
                if ($subscription) {
                    // Check if module file exists
                    $moduleFile = "modules/{$moduleCode}.php";
                    if (file_exists($moduleFile)) {
                        require_once $moduleFile;
                    } else {
                        // Module not implemented yet
                        echo "<div class='container mt-5'>
                                <div class='alert alert-warning'>
                                    <h4><i class='fas fa-tools'></i> Module Under Development</h4>
                                    <p>The <strong>{$subscription['module_name']}</strong> module is currently being developed.</p>
                                    <p>You are subscribed to this module and will have access as soon as it's ready.</p>
                                    <a href='?page=marketplace' class='btn btn-primary'>Return to Marketplace</a>
                                </div>
                            </div>";
                    }
                } else {
                    // Not subscribed
                    echo "<div class='container mt-5'>
                            <div class='alert alert-danger'>
                                <h4><i class='fas fa-lock'></i> Access Denied</h4>
                                <p>You need to subscribe to this module to access it.</p>
                                <a href='?page=marketplace' class='btn btn-primary'>Go to Marketplace</a>
                            </div>
                        </div>";
                }
            } catch (PDOException $e) {
                echo "<div class='container mt-5'>
                        <div class='alert alert-danger'>
                            <h4>Database Error</h4>
                            <p>Unable to verify module access.</p>
                        </div>
                    </div>";
            }
        } else {
            // No module code provided
            header('Location: ?page=marketplace');
            exit;
        }
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

?>