<?php
// platform/auth/login-process.php - TAMAMEN TEMİZ VERSION
// Debug mesajları YOK!

session_start();

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /brcproject/platform/auth/login.php');
    exit;
}

// Variable conflict'i önlemek için unique isimler
$adminLoginName = $_POST['username'] ?? '';
$adminLoginPass = $_POST['password'] ?? '';

// Validation
if (empty($adminLoginName) || empty($adminLoginPass)) {
    $_SESSION['login_error'] = 'Kullanıcı adı ve şifre gereklidir.';
    header('Location: /brcproject/platform/auth/login.php');
    exit;
}

// Database connection
require_once __DIR__ . '/../../dbConnect/dbkonfigur.php';

try {
    if (isset($pdo)) {
        // Database query
        $queryStmt = $pdo->prepare("
            SELECT id, username, first_name, last_name, role, password 
            FROM platform_admins 
            WHERE username = ? AND status = 'active'
        ");
        $queryStmt->execute([$adminLoginName]);
        $adminData = $queryStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($adminData) {
            // Password check - sadece plain text (hash kaldırdım)
            if ($adminData['password'] === $adminLoginPass) {
                // SUCCESS!
                $_SESSION['platform_admin_id'] = $adminData['id'];
                $_SESSION['platform_admin_username'] = $adminData['username'];
                $_SESSION['platform_admin_name'] = trim($adminData['first_name'] . ' ' . $adminData['last_name']);
                $_SESSION['platform_admin_role'] = $adminData['role'];
                $_SESSION['login_time'] = time();
                
                session_regenerate_id(true);
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE platform_admins SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$adminData['id']]);
                
                // Redirect to dashboard
                header('Location: /brcproject/admin');
                exit;
            }
        }
    }
    
    // Demo fallback
    $demoAccounts = [
        'admin' => '123456',
        'testadmin' => '123456'
    ];
    
    if (isset($demoAccounts[$adminLoginName]) && $demoAccounts[$adminLoginName] === $adminLoginPass) {
        $_SESSION['platform_admin_id'] = 1;
        $_SESSION['platform_admin_username'] = $adminLoginName;
        $_SESSION['platform_admin_name'] = 'Demo Administrator';
        $_SESSION['platform_admin_role'] = 'super_admin';
        $_SESSION['login_time'] = time();
        
        session_regenerate_id(true);
        header('Location: /brcproject/admin');
        exit;
    }
    
    // Failed
    $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
    
} catch (PDOException $e) {
    $_SESSION['login_error'] = 'Sistem hatası.';
}

header('Location: /brcproject/platform/auth/login.php');
exit;
?>