<?php

// SESSION AYARLARI ÖNCELİKLE (session_start öncesinde)
ini_set('session.lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);

// Database Configuration (sadece tanımlı değilse tanımla)
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'brcload_platform');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Application Settings
if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/brcproject');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/../uploads/');
if (!defined('LOG_PATH')) define('LOG_PATH', __DIR__ . '/../logs/');
if (!defined('ASSETS_URL')) define('ASSETS_URL', BASE_URL . '/assets');

// Security Settings
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 3600); // 1 hour
if (!defined('CSRF_TOKEN_EXPIRY')) define('CSRF_TOKEN_EXPIRY', 1800); // 30 minutes
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 8);
if (!defined('LOGIN_ATTEMPTS_LIMIT')) define('LOGIN_ATTEMPTS_LIMIT', 5);
if (!defined('LOGIN_LOCKOUT_TIME')) define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
if (!defined('ALLOWED_FILE_TYPES')) define('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx');

// Email Configuration
if (!defined('SMTP_HOST')) define('SMTP_HOST', '');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', 'noreply@brcload.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'BRC Load Platform');

// Environment Settings
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development'); // development, staging, production
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', ENVIRONMENT === 'development');
if (!defined('LOG_LEVEL')) define('LOG_LEVEL', DEBUG_MODE ? 'DEBUG' : 'ERROR');

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

?>