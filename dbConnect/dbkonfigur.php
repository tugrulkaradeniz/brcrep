<?php
// dbConnect/dbkonfigur.php - Veritabanı bağlantısı

try {
    // Veritabanı ayarları
    $host = 'localhost';
    $dbname = 'brcload_platform'; // Veritabanı adınızı kontrol edin
    $username = 'root';
    $password = ''; // XAMPP'te genelde boş
    $charset = 'utf8mb4';
    
    // DSN (Data Source Name)
    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
    
    // PDO ayarları
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    // PDO bağlantısını kur
    $pdo = new PDO($dsn, $username, $password, $options);
    $db = $pdo; // Backward compatibility için
    
    // Global değişken olarak ayarla
    $GLOBALS['pdo'] = $pdo;
    $GLOBALS['db'] = $pdo;
    
    // Debug için (geliştirme sırasında)
    if (defined('DEBUG') && DEBUG) {
        error_log("Database connection successful");
    }
    
} catch (PDOException $e) {
    // Hata durumunda
    error_log("Database connection failed: " . $e->getMessage());
    
    // Geliştirme ortamında hatayı göster
    if (defined('DEBUG') && DEBUG) {
        die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
    } else {
        // Production'da genel hata mesajı
        die("Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.");
    }
}

// Connection test function
function testDbConnection() {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->query("SELECT 1");
        return $stmt !== false;
    } catch (PDOException $e) {
        error_log("DB connection test failed: " . $e->getMessage());
        return false;
    }
}
?>