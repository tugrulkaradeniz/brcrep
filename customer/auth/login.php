<?php
// ===== CUSTOMER LOGIN PAGE =====
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session first
session_start();

// Include required files
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../services/TenantContext.php';
require_once '../../services/CompanyContext.php';

// Initialize tenant context
$tenantContext = new TenantContext();
$tenant = $tenantContext->detect();

if (!$tenant) {
    // Fallback to demo company for testing
    $companyData = [
        'company_name' => 'Demo Company',
        'theme_color' => '#007bff'
    ];
} else {
    // Set company context
    try {
        CompanyContext::set($tenantContext->getCompanyId());
        $companyData = CompanyContext::getCompanyData();
    } catch (Exception $e) {
        // Fallback if CompanyContext fails
        $companyData = [
            'company_name' => 'Demo Company', 
            'theme_color' => '#007bff'
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= $companyData['company_name'] ?? 'Company' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --company-color: <?= $companyData['theme_color'] ?? '#007bff' ?>;
        }
        
        body {
            background: linear-gradient(135deg, var(--company-color) 0%, #6c757d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .company-header {
            margin-bottom: 2rem;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            background: var(--company-color);
        }
        
        .company-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .company-header p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .login-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--company-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .form-check {
            margin-bottom: 1.5rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: var(--company-color);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.2s;
        }
        
        .btn-login:hover {
            background: #0056b3;
            color: white;
        }
        
        .login-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .alert {
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            border-radius: 10px;
        }
    </style>
</head>
<body class="customer-login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="company-header">
                <div class="company-logo">
                    <?= strtoupper(substr($companyData['company_name'], 0, 2)) ?>
                </div>
                <h2><?= htmlspecialchars($companyData['company_name']) ?></h2>
                <p>Welcome back! Please sign in to your account.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Invalid credentials. Please try again.
                </div>
            <?php endif; ?>

            <form method="POST" action="login-process.php" class="login-form">
                <div class="form-group">
                    <label><i class="fas fa-user me-2"></i>Username or Email</label>
                    <input type="text" name="login" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock me-2"></i>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="login-footer">
                <small class="text-muted">
                    Powered by <strong>BRC Load</strong> Platform
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>