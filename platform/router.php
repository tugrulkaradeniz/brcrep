<?php
// platform/router.php
require_once __DIR__ . '/../config/config.php';

// Get current page from URL parameter
$page = $_GET['page'] ?? 'dashboard';

// Remove any path traversal attempts
$page = basename($page);

// Auth sayfalarını kontrol et (login işlemleri için auth kontrolü yapma)
$auth_pages = ['login', 'login-process', 'logout'];
$is_auth_page = in_array($page, $auth_pages);

// Eğer auth sayfası değilse admin authentication kontrolü yap
if (!$is_auth_page) {
    if (!isset($_SESSION['platform_admin_id'])) {
        header('Location: /platform/auth/login');
        exit;
    }
}

// Auth sayfaları için özel routing
if ($page === 'login' || $page === 'auth') {
    include __DIR__ . '/auth/login.php';
    exit;
}

if ($page === 'login-process') {
    include __DIR__ . '/auth/login-process.php';
    exit;
}

if ($page === 'logout') {
    include __DIR__ . '/auth/logout.php';
    exit;
}

// Define valid pages
$validPages = [
    'dashboard',
    'companies', 
    'modules',
    'module-builder',
    'analytics',
    'reports',
    'revenue',
    'templates',
    'api',
    'settings',
    'logs',
    'backup',
    'notifications',
    'profile'
];

// Check if page is valid
if (!in_array($page, $validPages)) {
    $page = 'dashboard';
}

// Route to appropriate page
$pagePath = __DIR__ . "/pages/{$page}.php";

if (file_exists($pagePath)) {
    include $pagePath;
} else {
    // Show 404 or redirect to dashboard
    header('Location: /platform/dashboard');
    exit;
}
?>