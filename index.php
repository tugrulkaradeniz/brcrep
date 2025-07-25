<?php
// index.php (Ana dosya - XAMPP Uyumlu)
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoload.php';

// URL'yi parse et
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// XAMPP için proje klasör yolunu tespit et
$project_folder = '';
if (strpos($script_name, '/') !== false) {
    $path_parts = explode('/', $script_name);
    if (count($path_parts) > 2) {
        $project_folder = '/' . $path_parts[1];
    }
}

// Request path'i al
$path = str_replace($project_folder, '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Debug için (geliştirme sırasında açabilirsiniz)
/*
echo "<!-- DEBUG INFO -->";
echo "<!-- Request URI: " . $request_uri . " -->";
echo "<!-- Script Name: " . $script_name . " -->";
echo "<!-- Project Folder: " . $project_folder . " -->";
echo "<!-- Path: " . $path . " -->";
*/

// Path'i parçala
$segments = explode('/', $path);
$section = $segments[0] ?? '';

// Eğer path boşsa veya sadece index.php ise
if (empty($path) || $path == 'index.php') {
    // Admin paneline mi yoksa website'e mi gitmek istediğini kontrol et
    if (isset($_GET['page']) && $_GET['page'] == 'admin') {
        $section = 'platform';
        $_GET['page'] = 'dashboard';
    } else {
        // Varsayılan olarak website ana sayfasına git
        header('Location: ' . $project_folder . '/website/home');
        exit;
    }
}

switch ($section) {
    case 'platform':
        // Platform admin panel
        if (!isset($_SESSION['platform_admin_id'])) {
            // Auth sayfaları kontrolü
            $page = $_GET['page'] ?? $segments[1] ?? '';
            if (!in_array($page, ['login', 'login-process', 'logout', 'auth'])) {
                header('Location: ' . $project_folder . '/platform/auth/login');
                exit;
            }
        }
        
        // Page parametresini ayarla
        if (!isset($_GET['page'])) {
            $_GET['page'] = $segments[1] ?? 'dashboard';
        }
        
        include __DIR__ . '/platform/router.php';
        break;
        
    case 'customer':
        // Customer panel routing
        require_once __DIR__ . '/services/TenantContext.php';
        require_once __DIR__ . '/services/CompanyContext.php';
        
        // Tenant detection (demo, test gibi)
        $tenant = $segments[1] ?? 'demo'; // Varsayılan tenant
        
        // Company context set et
        $companyContext = CompanyContext::getInstance();
        try {
            $company = $companyContext->setCompanyBySubdomain($tenant);
            if (!$company) {
                die('Company not found for tenant: ' . htmlspecialchars($tenant));
            }
        } catch (Exception $e) {
            die('Tenant error: ' . htmlspecialchars($e->getMessage()));
        }
        
        // Customer authentication kontrolü
        if (!isset($_SESSION['company_user_id'])) {
            $page = $_GET['page'] ?? $segments[2] ?? '';
            if (!in_array($page, ['login', 'login-process', 'logout', 'auth'])) {
                header('Location: ' . $project_folder . '/customer/' . $tenant . '/auth/login');
                exit;
            }
        }
        
        // Page parametresini ayarla
        if (!isset($_GET['page'])) {
            $_GET['page'] = $segments[2] ?? 'dashboard';
        }
        
        include __DIR__ . '/customer/router.php';
        break;
        
    case 'website':
        // Ana website
        if (!isset($_GET['page'])) {
            $_GET['page'] = $segments[1] ?? 'home';
        }
        include __DIR__ . '/website/router.php';
        break;
        
    case 'api':
        // API endpoints
        include __DIR__ . '/api/router.php';
        break;
        
    default:
        // Bilinmeyen section - website ana sayfasına yönlendir
        header('Location: ' . $project_folder . '/website/home');
        exit;
}
?>