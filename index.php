<?php
// index.php - Ana routing sistemi

// Veritabanı bağlantısını en başta kur
require_once __DIR__ . '/dbConnect/dbkonfigur.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/functions.php';

// Veritabanı bağlantısını kontrol et
if (!isset($pdo) && !isset($db)) {
    die('Veritabanı bağlantısı kurulamadı!');
}

// Request URI'yi al ve temizle
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/brcproject'; // XAMPP için base path
$requestUri = str_replace($basePath, '', $requestUri);
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Query string parametrelerini al
$queryString = $_SERVER['QUERY_STRING'] ?? '';
parse_str($queryString, $queryParams);

// Debug için
error_log("Request URI: " . $requestUri);
error_log("Query params: " . print_r($queryParams, true));

// PLATFORM ADMIN ROUTING - /admin veya /platform ile başlayanlar
if (strpos($requestUri, '/admin') === 0 || strpos($requestUri, '/platform') === 0) {
    // Platform admin paneline yönlendir
    require_once __DIR__ . '/platform/router.php';
    exit;
}

// Query parameter ile admin kontrolü (?page=admin)
if (isset($queryParams['page']) && $queryParams['page'] === 'admin') {
    // Admin paneline yönlendir - redirect ile
    $redirectUrl = $basePath . '/admin';
    header('Location: ' . $redirectUrl);
    exit;
}

// CUSTOMER PANELS - Company-specific routing
$tenantContext = new TenantContext();
$tenant = $tenantContext->detect();

if ($tenant) {
    // Company context'i set et
    $companyContext = new CompanyContext();
    $companyContext::set($tenantContext->getCompanyId()); // DOĞRU
    
    // Customer routing'e gönder
    require_once __DIR__ . '/customer/router.php';
    exit;
}

// DEFAULT - Main website routing
if (empty($requestUri) || $requestUri === '/') {
    // Ana sayfaya yönlendir
    header('Location: ' . $basePath . '/website/home');
    exit;
}

// Website routing.com/teka
if (strpos($requestUri, '/website') === 0) {
    require_once __DIR__ . '/website/router.php';
    exit;
}

// Eğer hiçbiri match etmezse ana sayfaya yönlendir
header('Location: ' . $basePath . '/website/home');
exit;
?>