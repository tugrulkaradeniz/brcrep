<?php
// ===== DEBUG LOG CHECKER =====
// Dosya: debug/debug_checker.php
// Admin panel null değer sorununu bulur

echo "<h1>🔍 BRC Debug Log Checker</h1>";

// Debug log dosyasını oku
$log_files = [
    '../logs/debug.log',
    '../logs/error.log',
    '../../logs/debug.log',
    '../../logs/error.log'
];

$found_logs = false;

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        $found_logs = true;
        echo "<h2>📄 Log Dosyası: $log_file</h2>";
        
        $logs = file_get_contents($log_file);
        $lines = explode("\n", $logs);
        $recent_lines = array_slice($lines, -20); // Son 20 satır
        
        echo "<div style='background: #000; color: #00ff00; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px;'>";
        
        foreach ($recent_lines as $line) {
            if (!empty(trim($line))) {
                // Null değerleri vurgula
                if (strpos($line, 'null') !== false || strpos($line, 'NULL') !== false) {
                    echo "<span style='color: #ff0000; font-weight: bold;'>$line</span><br>";
                } else if (strpos($line, 'ERROR') !== false || strpos($line, 'error') !== false) {
                    echo "<span style='color: #ff6666;'>$line</span><br>";
                } else if (strpos($line, 'SUCCESS') !== false || strpos($line, 'success') !== false) {
                    echo "<span style='color: #66ff66;'>$line</span><br>";
                } else {
                    echo "$line<br>";
                }
            }
        }
        
        echo "</div><br>";
    }
}

if (!$found_logs) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "❌ Debug log dosyaları bulunamadı!<br>";
    echo "Lütfen önce admin panelden bir modül oluşturmayı deneyin.<br>";
    echo "</div>";
}

// Real-time debug testi
echo "<h2>🔄 Real-time Debug Test</h2>";
echo "<button onclick='testAdminPanel()' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Admin Panel Test</button>";
echo "<div id='test-result' style='margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px;'></div>";

// Console error checker
echo "<h2>🌐 Browser Console Hatları</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<strong>⚠️ Console'da hangi hata var?</strong><br>";
echo "1. F12 tuşuna basın<br>";
echo "2. Console sekmesini açın<br>";
echo "3. Admin panelde modül oluşturmayı deneyin<br>";
echo "4. Console'daki KIRMIZI hataları buraya yazın<br>";
echo "</div>";

// Admin panel form debug
echo "<h2>📝 Admin Panel Form Debug</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<form id='debug-form'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Modül Adı:</label><br>";
echo "<input type='text' id='module-name' value='Debug Test Module' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Açıklama:</label><br>";
echo "<textarea id='module-description' style='width: 300px; height: 60px; padding: 5px;'>Debug test modülü</textarea>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>Kategori:</label><br>";
echo "<select id='module-category' style='width: 300px; padding: 5px;'>";
echo "<option value='quality_control'>Quality Control</option>";
echo "<option value='general'>General</option>";
echo "</select>";
echo "</div>";
echo "<button type='button' onclick='debugSave()' style='background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 5px;'>Debug Save</button>";
echo "</form>";
echo "</div>";

// JavaScript debug fonksiyonları
?>

<script>
async function testAdminPanel() {
    const resultDiv = document.getElementById('test-result');
    resultDiv.innerHTML = '🔄 Admin panel test ediliyor...';
    
    try {
        // Debug endpoint'i test et
        const response = await fetch('../platform/ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'debug'
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            resultDiv.innerHTML = `
                <div style="background: #d4edda; padding: 10px; border-radius: 5px;">
                    ✅ Debug endpoint çalışıyor!<br>
                    <strong>Response:</strong>
                    <pre style="font-size: 11px; margin-top: 10px;">${JSON.stringify(result, null, 2)}</pre>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div style="background: #f8d7da; padding: 10px; border-radius: 5px;">
                    ❌ Debug endpoint hatası!<br>
                    <strong>Status:</strong> ${response.status}<br>
                    <strong>Error:</strong> <pre>${JSON.stringify(result, null, 2)}</pre>
                </div>
            `;
        }
        
    } catch (error) {
        resultDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 10px; border-radius: 5px;">
                💥 Connection Error: ${error.message}
            </div>
        `;
    }
}

async function debugSave() {
    const name = document.getElementById('module-name').value;
    const description = document.getElementById('module-description').value;
    const category = document.getElementById('module-category').value;
    
    console.log('📤 Gönderilen veri:', {
        action: 'save',
        name: name,
        description: description,
        category: category
    });
    
    try {
        const response = await fetch('../platform/ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'save',
                name: name,
                description: description,
                category: category
            })
        });
        
        console.log('📥 Response status:', response.status);
        
        const result = await response.json();
        console.log('📥 Response data:', result);
        
        if (response.ok && result.success) {
            alert('✅ Debug save başarılı! Module ID: ' + result.module_id);
            // Sayfayı yenile ki log'ları görelim
            window.location.reload();
        } else {
            alert('❌ Debug save hatası: ' + (result.error || 'Unknown error'));
            console.error('Debug save error:', result);
        }
        
    } catch (error) {
        console.error('💥 Debug save exception:', error);
        alert('💥 Debug save exception: ' + error.message);
    }
}

// Sayfa yüklendiğinde otomatik test
window.addEventListener('load', function() {
    console.log('🔍 Debug checker yüklendi');
    
    // Console error listener
    window.addEventListener('error', function(e) {
        console.error('🚨 JavaScript Error detected:', e.error);
    });
    
    // 2 saniye sonra otomatik test
    setTimeout(testAdminPanel, 2000);
});
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
    padding: 10px; 
    border-radius: 5px; 
    overflow-x: auto; 
    font-size: 11px;
}
</style>

<?php
echo "<br><strong>🎯 Sonraki Adımlar:</strong><br>";
echo "<ol>";
echo "<li><strong>Console hatasını paylaş</strong> - F12 > Console'daki kırmızı hatalar</li>";
echo "<li><strong>Debug Save butonunu dene</strong> - Üstteki formu doldur ve test et</li>";
echo "<li><strong>Log sonuçlarını kontrol et</strong> - Bu sayfa null değerleri kırmızı gösterir</li>";
echo "</ol>";

echo "<br><strong>📞 Yardım:</strong> Console'da göster hangi exact hatayı alıyorsun, çözümü bulalım!";
?>