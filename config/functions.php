<?php
function safe_redirect($url) {
    // Validate URL to prevent open redirect vulnerabilities
    $parsed = parse_url($url);
    
    if (isset($parsed['host']) && $parsed['host'] !== $_SERVER['HTTP_HOST']) {
        $url = defined('BASE_URL') ? BASE_URL : 'http://localhost'; // Redirect to home if external URL
    }
    
    header('Location: ' . $url);
    exit;
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        (defined('CSRF_TOKEN_EXPIRY') && time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRY)) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token) &&
           isset($_SESSION['csrf_token_time']) &&
           (defined('CSRF_TOKEN_EXPIRY') && time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_EXPIRY);
}

/**
 * Sanitize input
 */
function sanitize_input($input) {
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Log system events - GÜVENLİ HALE GETİRİLDİ
 */
function log_event($level, $message, $context = []) {
    // Eğer LOG_LEVEL tanımlı değilse, sadece ERROR ve yukarısını logla
    if (!defined('LOG_LEVEL')) {
        $logLevel = 'ERROR';
    } else {
        $logLevel = LOG_LEVEL;
    }
    
    $log_levels = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
    
    if (!in_array($level, $log_levels)) {
        $level = 'INFO';
    }
    
    // Check if we should log this level
    $current_level_index = array_search($logLevel, $log_levels);
    $message_level_index = array_search($level, $log_levels);
    
    if ($message_level_index < $current_level_index) {
        return;
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'message' => $message,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $log_line = json_encode($log_entry) . PHP_EOL;
    
    // LOG_PATH kontrolü
    $logPath = defined('LOG_PATH') ? LOG_PATH : __DIR__ . '/../logs/';
    if (!is_dir($logPath)) {
        @mkdir($logPath, 0755, true);
    }
    
    $log_file = $logPath . 'system-' . date('Y-m-d') . '.log';
    @file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Check if user is rate limited
 */
function check_rate_limit($identifier, $limit = 60, $window = 3600) {
    $logPath = defined('LOG_PATH') ? LOG_PATH : __DIR__ . '/../logs/';
    if (!is_dir($logPath)) {
        @mkdir($logPath, 0755, true);
    }
    
    $cache_file = $logPath . 'rate_limit_' . md5($identifier) . '.json';
    
    $data = [];
    if (file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true) ?: [];
    }
    
    $now = time();
    $window_start = $now - $window;
    
    // Clean old entries
    $data = array_filter($data, function($timestamp) use ($window_start) {
        return $timestamp > $window_start;
    });
    
    // Check if limit exceeded
    if (count($data) >= $limit) {
        return false;
    }
    
    // Add current request
    $data[] = $now;
    @file_put_contents($cache_file, json_encode($data), LOCK_EX);
    
    return true;
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure random password
 */
function generate_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}

/**
 * Format file size
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Check if file type is allowed
 */
function is_allowed_file_type($filename) {
    $allowedTypes = defined('ALLOWED_FILE_TYPES') ? ALLOWED_FILE_TYPES : 'jpg,jpeg,png,pdf,doc,docx';
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = explode(',', $allowedTypes);
    
    return in_array($extension, $allowed);
}

/**
 * Get company theme color
 */
function get_company_theme_color($company_id) {
    try {
        global $baglanti;
        if (!$baglanti) return '#007bff';
        
        $stmt = $baglanti->prepare("SELECT theme_color FROM companies WHERE id = ?");
        $stmt->execute([$company_id]);
        $result = $stmt->fetch();
        
        return $result ? $result['theme_color'] : '#007bff';
    } catch (Exception $e) {
        return '#007bff';
    }
}

/**
 * Format currency
 */
function format_currency($amount, $currency = 'USD') {
    return $currency . ' ' . number_format($amount, 2);
}

/**
 * Generate unique ID
 */
function generate_unique_id($prefix = '') {
    return $prefix . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

/**
 * Send email notification
 */
function send_notification($to, $subject, $message, $html = false) {
    // This would integrate with your email service provider
    // For now, we'll just log the notification
    
    log_event('INFO', 'Email notification sent', [
        'to' => $to,
        'subject' => $subject,
        'html' => $html
    ]);
    
    return true; // Simulate successful sending
}

?>