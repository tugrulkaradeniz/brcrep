<?php
// ===== CUSTOMER LOGIN PROCESS =====
// File: customer/auth/login-process.php

session_start();

// DEBUG: Form POST olup olmadığını kontrol et
echo "DEBUG: POST data received<br>";
var_dump($_POST);
echo "<br>LOGIN: " . ($_POST['login'] ?? 'empty') . "<br>";
echo "PASSWORD: " . ($_POST['password'] ?? 'empty') . "<br>";
die(); // Burada dur ve kontrol et

// Include required files
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../services/TenantContext.php';
require_once '../../services/CompanyContext.php';



try {
    // Get form data
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        exit;
    }
    
    // Initialize tenant context
    $tenantContext = new TenantContext();
    $tenant = $tenantContext->detect();
    
    // Determine company ID and domain
    if (!$tenant) {
        // Default to demo company for testing
        $companyId = 1;
        $companyDomain = 'demo';
    } else {
        CompanyContext::set($tenantContext->getCompanyId());
        $companyId = $tenantContext->getCompanyId();
        $companyDomain = $tenant['domain'] ?? 'demo';
    }
    
    // Database connection
    require_once '../../dbConnect/dbkonfigur.php';
    
    // Find user in company_users table
    $stmt = $pdo->prepare("
        SELECT cu.*, c.name as company_name, c.domain, c.theme_color 
        FROM company_users cu 
        JOIN companies c ON cu.company_id = c.id 
        WHERE cu.company_id = ? 
        AND (cu.username = ? OR cu.email = ?) 
        AND cu.status = 'active'
        LIMIT 1
    ");
    
    $stmt->execute([$companyId, $login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: login.php?error=user_not_found');
        exit;
    }
    
    // Verify password
    $isValidPassword = false;
    
    // Try hashed password first
    if (password_verify($password, $user['password'])) {
        $isValidPassword = true;
    } 
    // Fallback: try plain text password (for demo)
    elseif ($user['password'] === $password || $user['password'] === '123456') {
        $isValidPassword = true;
        
        // Update to hashed password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE company_users SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashedPassword, $user['id']]);
    }
    
    if (!$isValidPassword) {
        header('Location: login.php?error=invalid_password');
        exit;
    }
    
    // Update last login
    $updateLoginStmt = $pdo->prepare("UPDATE company_users SET last_login = NOW() WHERE id = ?");
    $updateLoginStmt->execute([$user['id']]);
    
    // Set session variables
    $_SESSION['company_user_id'] = $user['id'];
    $_SESSION['company_id'] = $user['company_id'];
    $_SESSION['user_id'] = $user['id']; // For dashboard compatibility
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['company_name'] = $user['company_name'];
    $_SESSION['company_domain'] = $user['domain'];
    $_SESSION['theme_color'] = $user['theme_color'];
    
    // Log successful login
    log_event('INFO', 'User login successful', [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'company_id' => $user['company_id'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    
    // Determine redirect URL
    $dashboardUrl = '';
    
    // Try to get company domain from HTTP_REFERER first (most reliable)
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        // Extract company domain from referer like: http://localhost/brcproject/demo?page=login
        if (preg_match('/\/brcproject\/([^\/\?]+)/', $referer, $matches)) {
            $companyDomain = $matches[1];
        }
    }
    
    // Fallback to user's company domain
    if (empty($companyDomain) && !empty($user['domain'])) {
        $companyDomain = $user['domain'];
    }
    
    // Final fallback
    if (empty($companyDomain)) {
        $companyDomain = 'demo';
    }
    
    // Construct dashboard URL
    if (defined('BASE_URL')) {
        $dashboardUrl = BASE_URL . '/' . $companyDomain . '?page=dashboard';
    } else {
        $dashboardUrl = '../../' . $companyDomain . '?page=dashboard';
    }
    
    // Redirect to dashboard
    header('Location: ' . $dashboardUrl);
    exit;
    
} catch (Exception $e) {
    // Log error
    log_event('ERROR', 'Login process error', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'login_attempt' => $_POST['login'] ?? 'unknown'
    ]);
    
    header('Location: login.php?error=system_error');
    exit;
}
?>