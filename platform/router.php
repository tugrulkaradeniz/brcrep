<?php
// Platform admin authentication check
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? '';

// Login page için authentication bypass
if (($page === 'admin' && $action === 'login') || $page === 'login') {
    require_once 'platform/auth/login.php';
    exit;
}

// Diğer sayfalar için authentication check
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: ?page=admin&action=login');
    exit;
}

switch ($page) {
    case 'admin':
        switch ($action) {
            case 'login':
                require_once 'platform/auth/login.php';
                break;
            case 'companies':
                require_once 'platform/pages/companies.php';
                break;
            case 'companies-add':
                require_once 'platform/pages/companies-add.php';
                break;
            case 'companies-edit':
                require_once 'platform/pages/companies-edit.php';
                break;
            case 'modules':
                require_once 'platform/pages/modules.php';
                break;
            case 'module-builder':
                require_once 'platform/pages/module-builder.php';
                break;
            case 'marketplace':
                require_once 'platform/pages/marketplace.php';
                break;
            case 'analytics':
                require_once 'platform/pages/analytics.php';
                break;
            case 'settings':
                require_once 'platform/pages/settings.php';
                break;
            case 'logout':
                require_once 'platform/auth/logout.php';
                break;
            default:
                // Default to dashboard
                require_once 'platform/pages/dashboard.php';
        }
        break;
        
    case 'dashboard':
        require_once 'platform/pages/dashboard.php';
        break;
        
    case 'companies':
        require_once 'platform/pages/companies.php';
        break;
        
    case 'companies-add':
        require_once 'platform/pages/companies-add.php';
        break;
        
    case 'companies-edit':
        require_once 'platform/pages/companies-edit.php';
        break;
        
    case 'modules':
        require_once 'platform/pages/modules.php';
        break;
        
    case 'module-builder':
        require_once 'platform/pages/module-builder.php';
        break;
        
    case 'marketplace':
        require_once 'platform/pages/marketplace.php';
        break;
        
    case 'analytics':
        require_once 'platform/pages/analytics.php';
        break;
        
    case 'settings':
        require_once 'platform/pages/settings.php';
        break;
        
    case 'logout':
        require_once 'platform/auth/logout.php';
        break;
        
    default:
        http_response_code(404);
        if (file_exists('platform/pages/404.php')) {
            require_once 'platform/pages/404.php';
        } else {
            echo "Page not found";
        }
}

?>