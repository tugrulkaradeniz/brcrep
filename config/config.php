<?php
// config/config.php - Platform konfigürasyonu

// Error reporting (geliştirme için)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug mode
define('DEBUG', true); // Production'da false yapın

// Session ayarları
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Platform ayarları
define('PLATFORM_NAME', 'BRC Load Platform');
define('PLATFORM_VERSION', '2.1');
define('BASE_PATH', '/brcproject');

// URL ayarları
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . '://' . $host . BASE_PATH);

// Path ayarları
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Veritabanı ayarları (dbkonfigur.php'de kullanılacak)
define('DB_HOST', 'localhost');
define('DB_NAME', 'brcload_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security ayarları
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 saat

// Upload ayarları
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Multi-tenant ayarları
define('DEFAULT_COMPANY', 'default');
define('DEMO_COMPANIES', ['demo', 'test', 'company1', 'company2']);

// Email ayarları (ileriye yönelik)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Log ayarları
define('LOG_PATH', ROOT_PATH . '/logs');
define('LOG_LEVEL', DEBUG ? 'DEBUG' : 'INFO');

// Log dizinini oluştur - güvenli şekilde
function ensureLogDirectory() {
    try {
        if (!is_dir(LOG_PATH)) {
            // Permission kontrolü
            $parentDir = dirname(LOG_PATH);
            if (!is_writable($parentDir)) {
                error_log("Parent directory not writable: " . $parentDir);
                return false;
            }
            
            if (!mkdir(LOG_PATH, 0755, true)) {
                error_log("Failed to create log directory: " . LOG_PATH);
                return false;
            }
        }
        return true;
    } catch (Exception $e) {
        error_log("Error creating log directory: " . $e->getMessage());
        return false;
    }
}

// Utility functions
function logMessage($message, $level = 'INFO') {
    // Log'u devre dışı bırak (permission sorunu için)
    if (!DEBUG) {
        return true;
    }
    
    // Log dizinini kontrol et
    if (!ensureLogDirectory()) {
        // Log yazamazsa sessizce devam et
        return false;
    }
    
    try {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = LOG_PATH . '/app_' . date('Y-m-d') . '.log';
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        // Log dosyasına yazabilir miyiz kontrol et
        if (is_dir(LOG_PATH) && is_writable(LOG_PATH)) {
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            return true;
        } else {
            // Sessizce devam et
            return false;
        }
    } catch (Exception $e) {
        // Sessizce devam et
        return false;
    }
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Autoload sınıfları
spl_autoload_register(function($className) {
    $directories = [
        ROOT_PATH . '/models/',
        ROOT_PATH . '/services/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/helpers/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Platform başlatıldığını logla
if (DEBUG) {
    logMessage("Platform initialized - " . getCurrentUrl());
}
?>