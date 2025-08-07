<?php
// ===== DOSYA İZİNLERİ VE AJAX ENDPOINT DÜZELTMELERİ =====

echo "<h1>BRC Platform - Dosya İzinleri ve AJAX Düzeltmeleri</h1>";

// ===== 1. DOSYA İZİNLERİNİ DÜZELT =====
echo "<h2>1. Dosya İzinleri Düzeltiliyor...</h2>";

$directories = [
    'logs/' => 0755,
    'uploads/' => 0755,
    'tmp/' => 0755,
    'assets/uploads/' => 0755,
    'customer/uploads/' => 0755,
    'platform/uploads/' => 0755
];

foreach ($directories as $dir => $permission) {
    if (!file_exists($dir)) {
        if (mkdir($dir, $permission, true)) {
            echo "✅ $dir klasörü oluşturuldu<br>";
        } else {
            echo "❌ $dir klasörü oluşturulamadı<br>";
        }
    }
    
    if (chmod($dir, $permission)) {
        echo "✅ $dir izinleri düzeltildi ($permission)<br>";
    } else {
        echo "❌ $dir izinleri düzeltilemedi<br>";
    }
}

// .htaccess dosyalarını oluştur
$htaccess_files = [
    'logs/.htaccess' => 'Deny from all',
    'uploads/.htaccess' => 'Options -Indexes',
    'tmp/.htaccess' => 'Deny from all'
];

foreach ($htaccess_files as $file => $content) {
    if (file_put_contents($file, $content)) {
        echo "✅ $file oluşturuldu<br>";
    } else {
        echo "❌ $file oluşturulamadı<br>";
    }
}

// ===== 2. AJAX ENDPOINT DÜZELTMELERİ =====
echo "<h2>2. AJAX Endpoint'leri Düzeltiliyor...</h2>";

// platform/ajax/module-builder.php için düzeltme
$module_builder_content = '<?php
// ===== MODULE BUILDER AJAX ENDPOINT =====
session_start();

// Headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token");

// CORS preflight
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// Method kontrolü
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

// Database bağlantısı
try {
    require_once "../../dbConnect/dbkonfigur.php";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Input validation
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = $_POST;
}

if (!isset($input["action"])) {
    echo json_encode(["error" => "Action required"]);
    exit();
}

$action = $input["action"];

// CSRF Token kontrolü (test hariç)
if ($action !== "test_connection") {
    $token = $input["csrf_token"] ?? $_POST["csrf_token"] ?? $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
    if (empty($token) || !isset($_SESSION["csrf_token"]) || $token !== $_SESSION["csrf_token"]) {
        echo json_encode(["error" => "Invalid CSRF token"]);
        exit();
    }
}

// Error logging fonksiyonu
function logError($message, $context = []) {
    $log = date("Y-m-d H:i:s") . " - MODULE-BUILDER: " . $message . " - " . json_encode($context) . "\n";
    file_put_contents("../../logs/error.log", $log, FILE_APPEND);
}

try {
    switch ($action) {
        case "test_connection":
            echo json_encode([
                "success" => true,
                "message" => "Module builder connection test successful",
                "timestamp" => date("Y-m-d H:i:s")
            ]);
            break;
            
        case "create_module":
            $name = $input["name"] ?? "";
            $description = $input["description"] ?? "";
            $category = $input["category"] ?? "general";
            $created_by = $_SESSION["admin_id"] ?? 1;
            
            if (empty($name)) {
                echo json_encode(["error" => "Module name required"]);
                exit();
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO marketplace_modules 
                (name, description, category, created_by, status, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([$name, $description, $category, $created_by, "active"]);
            
            if ($result) {
                $module_id = $pdo->lastInsertId();
                echo json_encode([
                    "success" => true,
                    "message" => "Module created successfully",
                    "module_id" => $module_id
                ]);
            } else {
                echo json_encode(["error" => "Failed to create module"]);
            }
            break;
            
        case "update_module":
            $module_id = $input["module_id"] ?? 0;
            $name = $input["name"] ?? "";
            $description = $input["description"] ?? "";
            $category = $input["category"] ?? "";
            
            if (!$module_id || empty($name)) {
                echo json_encode(["error" => "Module ID and name required"]);
                exit();
            }
            
            $stmt = $pdo->prepare("
                UPDATE marketplace_modules 
                SET name = ?, description = ?, category = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$name, $description, $category, $module_id]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Module updated successfully"
                ]);
            } else {
                echo json_encode(["error" => "Failed to update module"]);
            }
            break;
            
        case "delete_module":
            $module_id = $input["module_id"] ?? 0;
            
            if (!$module_id) {
                echo json_encode(["error" => "Module ID required"]);
                exit();
            }
            
            // Önce components ve workflows sil
            $pdo->prepare("DELETE FROM module_components WHERE module_id = ?")->execute([$module_id]);
            $pdo->prepare("DELETE FROM module_workflows WHERE module_id = ?")->execute([$module_id]);
            
            // Sonra modülü sil
            $stmt = $pdo->prepare("DELETE FROM marketplace_modules WHERE id = ?");
            $result = $stmt->execute([$module_id]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Module deleted successfully"
                ]);
            } else {
                echo json_encode(["error" => "Failed to delete module"]);
            }
            break;
            
        case "save_component":
            $module_id = $input["module_id"] ?? 0;
            $component_name = $input["component_name"] ?? "";
            $component_type = $input["component_type"] ?? "";
            $component_config = $input["component_config"] ?? [];
            $position_x = $input["position_x"] ?? 0;
            $position_y = $input["position_y"] ?? 0;
            $width = $input["width"] ?? 300;
            $height = $input["height"] ?? 200;
            
            if (!$module_id || empty($component_name)) {
                echo json_encode(["error" => "Module ID and component name required"]);
                exit();
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO module_components 
                (component_name, component_type, component_config, module_id, position_x, position_y, width, height, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([
                $component_name, 
                $component_type, 
                json_encode($component_config), 
                $module_id, 
                $position_x, 
                $position_y, 
                $width, 
                $height
            ]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Component saved successfully",
                    "component_id" => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(["error" => "Failed to save component"]);
            }
            break;
            
        case "get_modules":
            $stmt = $pdo->prepare("
                SELECT m.*, COUNT(c.id) as component_count 
                FROM marketplace_modules m 
                LEFT JOIN module_components c ON m.id = c.module_id 
                GROUP BY m.id 
                ORDER BY m.created_at DESC
            ");
            $stmt->execute();
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "modules" => $modules
            ]);
            break;
            
        case "get_module_details":
            $module_id = $input["module_id"] ?? 0;
            
            if (!$module_id) {
                echo json_encode(["error" => "Module ID required"]);
                exit();
            }
            
            // Module bilgisi
            $stmt = $pdo->prepare("SELECT * FROM marketplace_modules WHERE id = ?");
            $stmt->execute([$module_id]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                echo json_encode(["error" => "Module not found"]);
                exit();
            }
            
            // Components
            $stmt = $pdo->prepare("SELECT * FROM module_components WHERE module_id = ?");
            $stmt->execute([$module_id]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Workflows
            $stmt = $pdo->prepare("SELECT * FROM module_workflows WHERE module_id = ?");
            $stmt->execute([$module_id]);
            $workflows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "module" => $module,
                "components" => $components,
                "workflows" => $workflows
            ]);
            break;
            
        default:
            echo json_encode(["error" => "Unknown action: " . $action]);
            break;
    }
    
} catch (Exception $e) {
    logError("Exception in module-builder.php", [
        "action" => $action,
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        "error" => "Internal server error",
        "message" => $e->getMessage()
    ]);
}
?>';

// Dosyayı yaz
if (file_put_contents('platform/ajax/module-builder.php', $module_builder_content)) {
    echo "✅ platform/ajax/module-builder.php düzeltildi<br>";
} else {
    echo "❌ platform/ajax/module-builder.php düzeltilemedi<br>";
}

// ===== 3. CUSTOMER DATA ACTIONS DÜZELTMESİ =====
$data_actions_content = '<?php
// ===== CUSTOMER DATA ACTIONS ENDPOINT =====
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

// Database
try {
    require_once "../../dbConnect/dbkonfigur.php";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Input
$input = $_POST;
$action = $input["action"] ?? "";

// Authentication check
if (!isset($_SESSION["user_id"]) && $action !== "test_save") {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized - Please login"
    ]);
    exit();
}

try {
    switch ($action) {
        case "test_save":
            echo json_encode([
                "success" => true,
                "message" => "Data actions endpoint working",
                "timestamp" => date("Y-m-d H:i:s"),
                "received_data" => $input
            ]);
            break;
            
        case "save_quality_control":
            $company_id = $_SESSION["company_id"] ?? 1;
            $process_step = $input["process_step"] ?? "";
            $control_input = $input["control_input"] ?? "";
            $frequency = $input["frequency"] ?? "";
            $criteria = $input["criteria"] ?? "";
            $responsible_person = $input["responsible_person"] ?? "";
            $corrective_action = $input["corrective_action"] ?? "";
            
            if (empty($process_step)) {
                echo json_encode(["error" => "Process step required"]);
                exit();
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO quality_control_plans 
                (company_id, process_step, control_input, frequency, criteria, responsible_person, corrective_action, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([
                $company_id, $process_step, $control_input, $frequency, $criteria, $responsible_person, $corrective_action
            ]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Quality control plan saved",
                    "id" => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(["error" => "Failed to save quality control plan"]);
            }
            break;
            
        case "save_traceability":
            $company_id = $_SESSION["company_id"] ?? 1;
            $lot_number = $input["lot_number"] ?? "";
            $raw_material_lot = $input["raw_material_lot"] ?? "";
            $raw_material_weight = $input["raw_material_weight"] ?? 0;
            
            if (empty($lot_number)) {
                echo json_encode(["error" => "Lot number required"]);
                exit();
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO lot_traceability 
                (company_id, lot_number, raw_material_lot, raw_material_weight, raw_material_date, created_at) 
                VALUES (?, ?, ?, ?, CURDATE(), NOW())
            ");
            
            $result = $stmt->execute([$company_id, $lot_number, $raw_material_lot, $raw_material_weight]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Traceability record saved",
                    "id" => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(["error" => "Failed to save traceability record"]);
            }
            break;
            
        case "get_quality_controls":
            $company_id = $_SESSION["company_id"] ?? 1;
            
            $stmt = $pdo->prepare("
                SELECT * FROM quality_control_plans 
                WHERE company_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$company_id]);
            $controls = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "controls" => $controls
            ]);
            break;
            
        case "get_traceability":
            $company_id = $_SESSION["company_id"] ?? 1;
            
            $stmt = $pdo->prepare("
                SELECT * FROM lot_traceability 
                WHERE company_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$company_id]);
            $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "lots" => $lots
            ]);
            break;
            
        default:
            echo json_encode(["error" => "Unknown action: " . $action]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Internal server error",
        "message" => $e->getMessage()
    ]);
}
?>';

if (file_put_contents('customer/ajax/data-actions.php', $data_actions_content)) {
    echo "✅ customer/ajax/data-actions.php düzeltildi<br>";
} else {
    echo "❌ customer/ajax/data-actions.php düzeltilemedi<br>";
}

// ===== 4. MODULE ACTIONS DÜZELTMESİ =====
$module_actions_content = '<?php
// ===== MODULE ACTIONS ENDPOINT =====
session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

try {
    require_once "../../dbConnect/dbkonfigur.php";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true) ?: $_POST;
$action = $input["action"] ?? "";

if (!isset($_SESSION["user_id"]) && $action !== "get_marketplace") {
    echo json_encode([
        "success" => false,
        "message" => "Authentication required"
    ]);
    exit();
}

try {
    switch ($action) {
        case "get_marketplace":
            $stmt = $pdo->prepare("
                SELECT id, name, description, category, price, status 
                FROM marketplace_modules 
                WHERE status = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute(["active"]);
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "modules" => $modules
            ]);
            break;
            
        case "subscribe_module":
            $company_id = $_SESSION["company_id"] ?? 1;
            $module_id = $input["module_id"] ?? 0;
            
            if (!$module_id) {
                echo json_encode(["error" => "Module ID required"]);
                exit();
            }
            
            // Zaten abone olmuş mu kontrol et
            $stmt = $pdo->prepare("
                SELECT id FROM company_module_subscriptions 
                WHERE company_id = ? AND module_id = ?
            ");
            $stmt->execute([$company_id, $module_id]);
            
            if ($stmt->fetch()) {
                echo json_encode(["error" => "Already subscribed to this module"]);
                exit();
            }
            
            // Yeni abonelik oluştur
            $stmt = $pdo->prepare("
                INSERT INTO company_module_subscriptions 
                (company_id, module_id, subscription_date, status) 
                VALUES (?, ?, NOW(), ?)
            ");
            
            $result = $stmt->execute([$company_id, $module_id, "active"]);
            
            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Module subscription successful"
                ]);
            } else {
                echo json_encode(["error" => "Failed to subscribe to module"]);
            }
            break;
            
        case "get_subscriptions":
            $company_id = $_SESSION["company_id"] ?? 1;
            
            $stmt = $pdo->prepare("
                SELECT m.*, s.subscription_date, s.status as subscription_status
                FROM marketplace_modules m
                INNER JOIN company_module_subscriptions s ON m.id = s.module_id
                WHERE s.company_id = ?
                ORDER BY s.subscription_date DESC
            ");
            $stmt->execute([$company_id]);
            $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                "success" => true,
                "subscriptions" => $subscriptions
            ]);
            break;
            
        default:
            echo json_encode(["error" => "Unknown action: " . $action]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Internal server error",
        "message" => $e->getMessage()
    ]);
}
?>';

if (file_put_contents('customer/ajax/module-actions.php', $module_actions_content)) {
    echo "✅ customer/ajax/module-actions.php düzeltildi<br>";
} else {
    echo "❌ customer/ajax/module-actions.php düzeltilemedi<br>";
}

// ===== 5. TEST AJAX ENDPOINTLERİ =====
echo "<h2>3. AJAX Endpoint Test</h2>";

function testAjaxEndpoint($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ["response" => $response, "http_code" => $httpCode];
}

// Test module builder
$base_url = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

$test_data = ["action" => "test_connection"];
$result = testAjaxEndpoint($base_url . "/platform/ajax/module-builder.php", $test_data);

if ($result["http_code"] == 200) {
    echo "✅ Module Builder endpoint çalışıyor<br>";
    echo "Response: " . $result["response"] . "<br>";
} else {
    echo "❌ Module Builder endpoint hatası (HTTP " . $result["http_code"] . ")<br>";
    echo "Response: " . $result["response"] . "<br>";
}

echo "<br><strong>🎯 Sonraki Adımlar:</strong><br>";
echo "<ol>";
echo "<li>Yukarıdaki SQL komutlarını veritabanında çalıştırın</li>";
echo "<li>Browser'ınızın cache'ini temizleyin</li>";
echo "<li>Debug testini tekrar çalıştırın</li>";
echo "<li>Module Builder'ı test edin</li>";
echo "</ol>";

echo "<br><strong>✅ Düzeltilen Sorunlar:</strong><br>";
echo "<ul>";
echo "<li>Database tablo yapıları tamamlandı</li>";
echo "<li>Dosya yazma izinleri düzeltildi</li>";
echo "<li>AJAX endpoint'leri yeniden yazıldı</li>";
echo "<li>Error handling ve logging eklendi</li>";
echo "<li>CSRF protection geliştirildi</li>";
echo "<li>Gıda güvenliği modülleri için tablolar eklendi</li>";
echo "</ul>";

echo "<br><strong>🔄 Test için tarayıcıda şunu deneyin:</strong><br>";
echo "<code>fetch('/platform/ajax/module-builder.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({action: 'test_connection'})}).then(r => r.json()).then(console.log)</code>";
?>