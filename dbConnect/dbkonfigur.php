<?php

// Config dosyası yüklenmemişse yükle
if (!defined('DB_HOST')) {
    if (file_exists(__DIR__ . '/../config/config.php')) {
        require_once __DIR__ . '/../config/config.php';
    } else {
        // Fallback values
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'brcload_platform');
        define('DB_USER', 'root');
        define('DB_PASS', '');
    }
}

try {
    $baglanti = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
} catch (PDOException $e) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die("Database Connection Error: " . $e->getMessage());
    } else {
        die("Database connection failed. Please check your configuration.");
    }
}

?>