<?php
// ===== GEÇİCİ ADMİN SESSION OLUŞTURUCU =====
// Dosya: brcproject/test_session.php
// Bu dosyayı çalıştırarak admin session oluşturun

session_start();

echo "<h1>🔐 BRC Platform - Session Oluşturucu</h1>";

// Admin session oluştur
$_SESSION['admin_id'] = 1;
$_SESSION['user_id'] = 1; 
$_SESSION['company_id'] = 1;
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['company_name'] = 'Test Company';

echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>✅ Session Başarıyla Oluşturuldu!</h3>";
echo "<strong>Admin ID:</strong> " . $_SESSION['admin_id'] . "<br>";
echo "<strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
echo "<strong>Company ID:</strong> " . $_SESSION['company_id'] . "<br>";
echo "<strong>CSRF Token:</strong> " . substr($_SESSION['csrf_token'], 0, 20) . "...<br>";
echo "<strong>Admin Name:</strong> " . $_SESSION['admin_name'] . "<br>";
echo "</div>";

// Database bağlantısını test et
echo "<h3>📊 Database Test</h3>";
try {
    require_once 'dbConnect/dbkonfigur.php';
    
    // Test company oluştur (yoksa)
    $stmt = $pdo->prepare("INSERT IGNORE INTO companies (id, name, email, status) VALUES (1, 'Test Company', 'test@test.com', 'active')");
    $stmt->execute();
    
    // Test admin oluştur (yoksa)  
    $stmt = $pdo->prepare("INSERT IGNORE INTO platform_admins (id, username, email, password) VALUES (1, 'testadmin', 'admin@test.com', ?)");
    $stmt->execute([password_hash('123456', PASSWORD_DEFAULT)]);
    
    // Test user oluştur (yoksa)
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_users (id, company_id, name, email, password) VALUES (1, 1, 'Test User', 'user@test.com', ?)");
    $stmt->execute([password_hash('123456', PASSWORD_DEFAULT)]);
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "✅ Database bağlantısı başarılı!<br>";
    echo "✅ Test verileri oluşturuldu!<br>";
    echo "</div>";
    
} catch(Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "❌ Database hatası: " . $e->getMessage() . "<br>";
    echo "</div>";
}

// Session bilgilerini göster
echo "<h3>🔍 Mevcut Session Bilgileri</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test linkleri
echo "<h3>🔗 Test Linkleri</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='module_test.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Module Test Sayfası</a><br><br>";
echo "<a href='platform/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Platform Admin</a><br><br>";
echo "<a href='customer/' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Customer Panel</a><br><br>";
echo "</div>";

// AJAX Test
echo "<h3>📡 AJAX Test</h3>";
echo "<button onclick='testAjax()' style='background: #ffc107; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>AJAX Test Et</button>";
echo "<div id='ajax-result' style='margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;'></div>";

?>

<script>
async function testAjax() {
    const resultDiv = document.getElementById('ajax-result');
    resultDiv.innerHTML = '🔄 Test ediliyor...';
    
    try {
        // Module builder test
        const response = await fetch('platform/ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({
                action: 'test_connection',
                csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            resultDiv.innerHTML = `
                <div style="background: #d4edda; padding: 10px; border-radius: 5px;">
                    ✅ AJAX Test Başarılı!<br>
                    <strong>Status:</strong> ${response.status}<br>
                    <strong>Response:</strong> ${JSON.stringify(result, null, 2)}
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; padding: 10px; border-radius: 5px;">
                    ❌ AJAX Test Başarısız!<br>
                    <strong>Status:</strong> ${response.status}<br>
                    <strong>Error:</strong> ${JSON.stringify(result, null, 2)}
                </div>
            `;
        }
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 10px; border-radius: 5px;">
                💥 AJAX Exception: ${error.message}
            </div>
        `;
    }
}
</script>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 1000px; 
    margin: 0 auto; 
    padding: 20px; 
}
pre { 
    background: #f8f9fa; 
    padding: 15px; 
    border-radius: 5px; 
    overflow-x: auto; 
}
</style>