<?php
// platform/router.php - Platform Admin Routing

// Session'ı başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Admin authentication kontrolü
function requireAdminAuth() {
    if (!isset($_SESSION['platform_admin_id'])) {
        // Login sayfasına yönlendir
        $basePath = '/brcproject';
        header('Location: ' . $basePath . '/platform/auth/login.php');
        exit;
    }
}

// Admin login kontrolü (optional)
function isAdminLoggedIn() {
    return isset($_SESSION['platform_admin_id']);
}

// Request URI'yi temizle
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/brcproject';
$requestUri = str_replace($basePath, '', $requestUri);

// Query string'i temizle
if (strpos($requestUri, '?') !== false) {
    $requestUri = substr($requestUri, 0, strpos($requestUri, '?'));
}

// Debug için
error_log("Platform Router - URI: " . $requestUri);

// Layout helper function
function renderAdminPage($pagePath, $pageTitle = 'Admin Panel') {
    ob_start();
    include __DIR__ . '/layout/header.php';
    include $pagePath;
    include __DIR__ . '/layout/footer.php';
    return ob_get_clean();
}

// Routing logic
switch (true) {
    // Admin ana sayfası
    case $requestUri === '/admin' || $requestUri === '/platform':
        if (!isAdminLoggedIn()) {
            header('Location: ' . $basePath . '/platform/auth/login.php');
            exit;
        }
        include __DIR__ . '/pages/dashboard.php';
        break;
        
    // Login sayfası
    case $requestUri === '/admin/login' || $requestUri === '/platform/auth/login':
        include __DIR__ . '/auth/login.php';
        break;
        
    // Login process
    case $requestUri === '/admin/login-process' || $requestUri === '/platform/auth/login-process':
        include __DIR__ . '/auth/login-process.php';
        break;
        
    // Logout
    case $requestUri === '/admin/logout' || $requestUri === '/platform/auth/logout':
        include __DIR__ . '/auth/logout.php';
        break;
        
    // Dashboard
    case $requestUri === '/admin/dashboard' || $requestUri === '/platform/pages/dashboard':
        requireAdminAuth();
        include __DIR__ . '/pages/dashboard.php';
        break;
        
    // Companies management
    case $requestUri === '/admin/companies' || $requestUri === '/platform/pages/companies':
        requireAdminAuth();
        include __DIR__ . '/pages/companies.php';
        break;
        
    // Module builder
    case $requestUri === '/admin/module-builder' || $requestUri === '/platform/pages/module-builder':
        requireAdminAuth();
        include __DIR__ . '/pages/module-builder.php';
        break;
        
    // Modules
    case $requestUri === '/admin/modules' || $requestUri === '/platform/pages/modules':
        requireAdminAuth();
        include __DIR__ . '/pages/modules.php';
        break;
        
    // AJAX endpoints
    case strpos($requestUri, '/admin/ajax/') === 0 || strpos($requestUri, '/platform/ajax/') === 0:
        requireAdminAuth();
        
        if (strpos($requestUri, 'company-actions') !== false) {
            include __DIR__ . '/ajax/company-actions.php';
        } elseif (strpos($requestUri, 'module-builder') !== false) {
            include __DIR__ . '/ajax/module-builder.php';
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'AJAX endpoint not found']);
        }
        break;
        
    // 404 - Sayfa bulunamadı
    default:
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Sayfa Bulunamadı</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .error-container { max-width: 500px; margin: 0 auto; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>404 - Sayfa Bulunamadı</h1>
                <p>Aradığınız sayfa mevcut değil.</p>
                <p>URI: ' . htmlspecialchars($requestUri) . '</p>
                <a href="' . $basePath . '/admin" class="btn">Admin Paneline Dön</a>
                <a href="' . $basePath . '/website/home" class="btn">Ana Sayfaya Dön</a>
            </div>
        </body>
        </html>';
        break;
}
?>