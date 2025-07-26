<?php
// BRC PLATFORM DEBUG CHECKLIST
// Her bir adımı sırayla test edin

echo "<h1>BRC Platform Debug Test</h1>";

// ===== 1. DATABASE CONNECTION TEST =====
echo "<h2>1. Database Bağlantı Testi</h2>";
try {
    require_once 'dbConnect/dbkonfigur.php';
    echo "✅ Database bağlantısı başarılı!<br>";
    
    // Tabloları kontrol et
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Mevcut tablolar: " . count($tables) . " adet<br>";
    foreach($tables as $table) {
        echo "- " . $table . "<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Database hatası: " . $e->getMessage() . "<br>";
}

// ===== 2. REQUIRED TABLES CHECK =====
echo "<h2>2. Gerekli Tablo Kontrolleri</h2>";
$required_tables = [
    'companies',
    'company_users', 
    'marketplace_modules',
    'company_module_subscriptions',
    'platform_admins',
    'module_components',
    'module_workflows',
    'company_data'
];

foreach($required_tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "✅ $table tablosu mevcut<br>";
    } catch(Exception $e) {
        echo "❌ $table tablosu eksik - Oluşturulmalı!<br>";
    }
}

// ===== 3. SESSION TEST =====
echo "<h2>3. Session Testi</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session aktif<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "❌ Session problemi<br>";
}

// ===== 4. WRITE PERMISSION TEST =====
echo "<h2>4. Dosya Yazma İzinleri</h2>";
$test_dirs = ['logs/', 'uploads/', 'tmp/'];
foreach($test_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (is_writable($dir)) {
        echo "✅ $dir yazılabilir<br>";
    } else {
        echo "❌ $dir yazma izni yok!<br>";
    }
}

// ===== 5. AJAX ENDPOINT TEST =====
echo "<h2>5. AJAX Endpoint Testi</h2>";
$ajax_files = [
    'platform/ajax/module-builder.php',
    'customer/ajax/module-actions.php',
    'customer/ajax/data-actions.php',
    'platform/ajax/company-actions.php'
];

foreach($ajax_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file mevcut<br>";
    } else {
        echo "❌ $file eksik!<br>";
    }
}

// ===== 6. PHP CONFIGURATION CHECK =====
echo "<h2>6. PHP Konfigürasyon</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Upload Max Size: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";

// PDO Extension check
if (extension_loaded('pdo')) {
    echo "✅ PDO extension yüklü<br>";
} else {
    echo "❌ PDO extension eksik!<br>";
}

// ===== 7. CSRF TOKEN TEST =====
echo "<h2>7. CSRF Token Testi</h2>";
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$csrf_token = generateCSRFToken();
echo "✅ CSRF Token oluşturuldu: " . substr($csrf_token, 0, 10) . "...<br>";

// ===== 8. ERROR LOGGING SETUP =====
echo "<h2>8. Error Logging Kurulumu</h2>";
function setupErrorLogging() {
    // Error log dosyası oluştur
    if (!file_exists('logs/error.log')) {
        touch('logs/error.log');
        chmod('logs/error.log', 0644);
    }
    
    // Custom error handler
    set_error_handler(function($severity, $message, $file, $line) {
        $log = date('Y-m-d H:i:s') . " - ERROR: $message in $file:$line\n";
        file_put_contents('logs/error.log', $log, FILE_APPEND);
    });
    
    echo "✅ Error logging kuruldu<br>";
}

setupErrorLogging();

// ===== 9. MODULE BUILDER TEST =====
echo "<h2>9. Module Builder Component Testi</h2>";

// Test için basit bir modül oluşturma
function testModuleCreation() {
    global $pdo;
    
    try {
        // Test modülü oluştur
        $stmt = $pdo->prepare("
            INSERT INTO marketplace_modules 
            (name, description, category, price, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            'Test Kalite Kontrol',
            'Test modülü - gıda güvenliği',
            'quality_control',
            0.00,
            1
        ]);
        
        if ($result) {
            echo "✅ Test modülü oluşturuldu<br>";
            
            // Oluşturulan modülü sil
            $module_id = $pdo->lastInsertId();
            $pdo->prepare("DELETE FROM marketplace_modules WHERE id = ?")->execute([$module_id]);
            echo "✅ Test modülü temizlendi<br>";
            
            return true;
        }
        
    } catch(Exception $e) {
        echo "❌ Modül oluşturma hatası: " . $e->getMessage() . "<br>";
        return false;
    }
}

$module_test = testModuleCreation();

// ===== 10. JAVASCRIPT CONSOLE CHECK =====
echo "<h2>10. Frontend JavaScript Testi</h2>";
?>

<script>
console.log("🔍 BRC Platform Frontend Debug Test başladı");

// AJAX test function
async function testAJAX() {
    try {
        console.log("📡 AJAX test başlıyor...");
        
        // Test endpoint'e basit bir request
        const response = await fetch('platform/ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo $csrf_token; ?>'
            },
            body: JSON.stringify({
                action: 'test_connection',
                data: 'debug_test'
            })
        });
        
        console.log("📊 Response status:", response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log("✅ AJAX test başarılı:", data);
            document.getElementById('ajax-result').innerHTML = "✅ AJAX çalışıyor";
        } else {
            console.error("❌ AJAX response error:", response.statusText);
            document.getElementById('ajax-result').innerHTML = "❌ AJAX hatası: " + response.statusText;
        }
        
    } catch (error) {
        console.error("❌ AJAX test hatası:", error);
        document.getElementById('ajax-result').innerHTML = "❌ AJAX bağlantı hatası: " + error.message;
    }
}

// DOM yüklendikten sonra test et
document.addEventListener('DOMContentLoaded', function() {
    console.log("🚀 DOM yüklendi, testler başlıyor");
    
    // Formsavetest
    const testForm = document.createElement('form');
    testForm.innerHTML = `
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="text" name="test_field" value="test_value">
        <button type="button" onclick="testFormSubmit()">Form Test</button>
    `;
    document.body.appendChild(testForm);
    
    // AJAX test başlat
    setTimeout(testAJAX, 1000);
});

function testFormSubmit() {
    console.log("📝 Form submit test başlıyor...");
    
    const formData = new FormData();
    formData.append('action', 'test_save');
    formData.append('test_field', 'test_value');
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    
    fetch('customer/ajax/data-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("✅ Form submit test sonucu:", data);
        document.getElementById('form-result').innerHTML = "✅ Form submit çalışıyor: " + data;
    })
    .catch(error => {
        console.error("❌ Form submit hatası:", error);
        document.getElementById('form-result').innerHTML = "❌ Form submit hatası: " + error.message;
    });
}
</script>

<div id="ajax-result">🔄 AJAX test bekliyor...</div>
<div id="form-result">🔄 Form test bekliyor...</div>

<?php
// ===== SONUÇ ÖZETİ =====
echo "<h2>🎯 Debug Özeti</h2>";
echo "<div style='background:#f8f9fa; padding:20px; border-radius:8px;'>";
echo "<h3>Problemli Alanlar İçin Çözümler:</h3>";

echo "<h4>1. Database Sorunları:</h4>";
echo "<code>
-- Eksik tabloları oluşturmak için:
CREATE DATABASE IF NOT EXISTS brc_platform;
USE brc_platform;

-- Temel tablo yapısı
CREATE TABLE marketplace_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    price DECIMAL(10,2) DEFAULT 0.00,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
</code>";

echo "<h4>2. AJAX Endpoint Düzeltmeleri:</h4>";
echo "<code>
// platform/ajax/module-builder.php başına ekle:
header('Content-Type: application/json');
header('X-Requested-With: XMLHttpRequest');

// Error handling ekle:
if (\$_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
</code>";

echo "<h4>3. Gıda Güvenliği Modülleri İçin Tablolar:</h4>";
echo "<code>
-- Kalite kontrol planı
CREATE TABLE quality_control_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    process_step VARCHAR(255),
    control_point TEXT,
    frequency VARCHAR(100),
    criteria TEXT,
    responsible_person VARCHAR(255),
    corrective_action TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ürün spesifikasyonları  
CREATE TABLE product_specifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    product_name VARCHAR(255),
    product_type VARCHAR(100),
    crop_year YEAR,
    physical_specs JSON,
    chemical_specs JSON,
    microbiological_specs JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
</code>";

echo "</div>";

echo "<br><strong>🔍 Bir sonraki adım:</strong> Hangi alanda sorun yaşandığını tespit edelim ve o alana odaklanalım.";
?>