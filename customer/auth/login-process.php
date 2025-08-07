<?php
// ===== CUSTOMER LOGIN PROCESS =====
// File: customer/auth/login-process.php

session_start();

// Include required files
require_once '../../config/config.php';
require_once '../../config/functions.php';
require_once '../../services/TenantContext.php';
require_once '../../services/CompanyContext.php';

try {
    // Initialize tenant context
    $tenantContext = new TenantContext();
    $tenant = $tenantContext->detect();
    
    if (!$tenant) {
        // Default to demo company for testing
        $companyId = 1;
    } else {
        CompanyContext::set($tenantContext->getCompanyId());
        $companyId = $tenantContext->getCompanyId();
    }
    
    // Get form data
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        exit;
    }
    
    // Database connection
    require_once '../../dbConnect/dbkonfigur.php';
    
    // Find user in company_users table
    $stmt = $pdo->prepare("
        SELECT cu.*, c.name as company_name 
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
    
    // Redirect to dashboard
    if ($tenant) {
        // Use tenant-based routing
        $baseUrl = str_replace('/customer/auth/login-process.php', '', $_SERVER['REQUEST_URI']);
        header('Location: ' . $baseUrl . '?page=dashboard');
    } else {
        // Default dashboard
        header('Location: ../pages/dashboard.php');
    }
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: login.php?error=system_error');
    exit;
}
?>