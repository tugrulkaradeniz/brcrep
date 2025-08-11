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
            // Debug output - remove after testing
            echo "<!-- DEBUG: Module code: {$moduleCode} -->\n";
            
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
                    SELECT cms.*, mm.module_name, mm.module_code 
                    FROM company_module_subscriptions cms
                    JOIN marketplace_modules mm ON cms.module_id = mm.id
                    WHERE cms.company_id = ? AND mm.module_code = ? AND cms.status = 'active'
                ");
                $moduleCheckStmt->execute([$_SESSION['company_id'] ?? 1, $moduleCode]);
                $subscription = $moduleCheckStmt->fetch();
                
                // Debug output
                echo "<!-- DEBUG: Subscription found: " . ($subscription ? 'YES' : 'NO') . " -->\n";
                if ($subscription) {
                    echo "<!-- DEBUG: Module name: {$subscription['module_name']} -->\n";
                }
                
                if ($subscription) {
                    // FIX: Correct file path from project root
                    $moduleFile = "customer/modules/{$moduleCode}.php";
                    
                    echo "<!-- DEBUG: Looking for file: {$moduleFile} -->\n";
                    echo "<!-- DEBUG: File exists: " . (file_exists($moduleFile) ? 'YES' : 'NO') . " -->\n";
                    
                    if (file_exists($moduleFile)) {
                        echo "<!-- DEBUG: Loading module file -->\n";
                        require_once $moduleFile;
                    } else {
                        // Module file doesn't exist - create it or show development message
                        ?>
                        <!DOCTYPE html>
                        <html lang="tr">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title><?= htmlspecialchars($subscription['module_name']) ?> - BRC Load</title>
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                            <link href="<?= BASE_URL ?>assets/css/customer.css" rel="stylesheet">
                        </head>
                        <body>
                            <?php include 'customer/layout/header.php'; ?>
                            
                            <div class="container mt-4">
                                <div class="alert alert-warning">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fas fa-tools fa-3x text-warning"></i>
                                        </div>
                                        <div class="col">
                                            <h4 class="alert-heading">
                                                <i class="fas fa-tools"></i> Module Under Development
                                            </h4>
                                            <p class="mb-2">The <strong><?= htmlspecialchars($subscription['module_name']) ?></strong> module is currently being developed.</p>
                                            <p class="mb-2">
                                                <strong>Module Code:</strong> <code><?= htmlspecialchars($moduleCode) ?></code><br>
                                                <strong>Expected File:</strong> <code><?= htmlspecialchars($moduleFile) ?></code>
                                            </p>
                                            <hr>
                                            <p class="mb-0">You are subscribed to this module and will have access as soon as it's ready.</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="?page=marketplace" class="btn btn-primary">
                                            <i class="fas fa-store me-2"></i>Return to Marketplace
                                        </a>
                                        <a href="?page=modules" class="btn btn-outline-secondary">
                                            <i class="fas fa-th-large me-2"></i>My Modules
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <?php include 'customer/layout/footer.php'; ?>
                        </body>
                        </html>
                        <?php
                    }
                } else {
                    // Not subscribed or subscription not found
                    ?>
                    <!DOCTYPE html>
                    <html lang="tr">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Access Denied - BRC Load</title>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                        <link href="<?= BASE_URL ?>assets/css/customer.css" rel="stylesheet">
                    </head>
                    <body>
                        <?php include 'customer/layout/header.php'; ?>
                        
                        <div class="container mt-4">
                            <div class="alert alert-danger">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <i class="fas fa-lock fa-3x text-danger"></i>
                                    </div>
                                    <div class="col">
                                        <h4 class="alert-heading">
                                            <i class="fas fa-lock"></i> Access Denied
                                        </h4>
                                        <p class="mb-2">You need to subscribe to this module to access it.</p>
                                        <p class="mb-0">
                                            <strong>Module Code:</strong> <code><?= htmlspecialchars($moduleCode) ?></code>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="?page=marketplace" class="btn btn-primary">
                                        <i class="fas fa-store me-2"></i>Go to Marketplace
                                    </a>
                                    <a href="?page=dashboard" class="btn btn-outline-secondary">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <?php include 'customer/layout/footer.php'; ?>
                    </body>
                    </html>
                    <?php
                }
            } catch (PDOException $e) {
                ?>
                <!DOCTYPE html>
                <html lang="tr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Database Error - BRC Load</title>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                    <link href="<?= BASE_URL ?>assets/css/customer.css" rel="stylesheet">
                </head>
                <body>
                    <?php include 'customer/layout/header.php'; ?>
                    
                    <div class="container mt-4">
                        <div class="alert alert-danger">
                            <h4><i class="fas fa-database"></i> Database Error</h4>
                            <p>Unable to verify module access.</p>
                            <p><strong>Error:</strong> <?= htmlspecialchars($e->getMessage()) ?></p>
                            <a href="?page=dashboard" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Return to Dashboard
                            </a>
                        </div>
                    </div>
                    
                    <?php include 'customer/layout/footer.php'; ?>
                </body>
                </html>
                <?php
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