<?php
// platform/ajax/module-builder.php - Modül kaydetme/yükleme AJAX endpoint

session_start();

// Admin kontrolü
if (!isset($_SESSION['platform_admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../../dbConnect/dbkonfigur.php';
require_once __DIR__ . '/../../config/config.php';

// Request method kontrolü
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'save':
            saveModule();
            break;
            
        case 'load':
            loadModule();
            break;
            
        case 'publish':
            publishModule();
            break;
            
        case 'delete':
            deleteModule();
            break;
            
        case 'duplicate':
            duplicateModule();
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}

/**
 * Modül kaydetme işlemi
 */
function saveModule() {
    global $pdo;
    
    // POST verilerini al
    $moduleData = json_decode(file_get_contents('php://input'), true);
    
    if (!$moduleData) {
        throw new Exception('Invalid module data');
    }
    
    // Validasyon
    validateModuleData($moduleData);
    
    // Mevcut modül var mı kontrol et
    $moduleId = $moduleData['id'] ?? null;
    
    if ($moduleId) {
        // Güncelleme
        updateExistingModule($moduleId, $moduleData);
    } else {
        // Yeni modül oluştur
        $moduleId = createNewModule($moduleData);
    }
    
    echo json_encode([
        'success' => true,
        'module_id' => $moduleId,
        'message' => 'Module saved successfully'
    ]);
}

/**
 * Modül yükleme işlemi
 */
function loadModule() {
    global $pdo;
    
    $moduleId = $_GET['module_id'] ?? null;
    
    if (!$moduleId) {
        throw new Exception('Module ID required');
    }
    
    // Modül verilerini getir (sisteminizin yapısına uygun)
    $stmt = $pdo->prepare("
        SELECT m.*
        FROM marketplace_modules m
        WHERE m.id = ?
    ");
    
    $stmt->execute([$moduleId]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$module) {
        throw new Exception('Module not found');
    }
    
    // Component verilerini getir
    $compStmt = $pdo->prepare("
        SELECT component_name, component_type, component_code, config, order_index
        FROM module_components 
        WHERE module_id = ? 
        ORDER BY order_index
    ");
    $compStmt->execute([$moduleId]);
    $components = $compStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Component verilerini uyumlu formata çevir
    $moduleComponents = [];
    foreach ($components as $comp) {
        $moduleComponents[] = [
            'id' => $comp['component_code'],
            'type' => $comp['component_type'],
            'name' => $comp['component_name'],
            'icon' => '🧩', // Default icon
            'properties' => json_decode($comp['config'], true) ?? []
        ];
    }
    
    // Modül verilerini uyumlu formata çevir
    $responseModule = [
        'id' => $module['id'],
        'name' => $module['module_name'], // module_name -> name
        'description' => $module['description'],
        'category' => $module['category'],
        'version' => $module['version'],
        'price' => $module['price'],
        'components' => $moduleComponents
    ];
    
    echo json_encode([
        'success' => true,
        'module' => $responseModule
    ]);
}

/**
 * Modül yayınlama işlemi
 */
function publishModule() {
    global $pdo;
    
    $moduleId = $_POST['module_id'] ?? null;
    
    if (!$moduleId) {
        throw new Exception('Module ID required');
    }
    
    // Modülü yayınla
    $stmt = $pdo->prepare("
        UPDATE marketplace_modules 
        SET status = 'active', published_at = NOW() 
        WHERE id = ?
    ");
    
    $stmt->execute([$moduleId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Module not found or already published');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Module published successfully'
    ]);
}

/**
 * Modül silme işlemi
 */
function deleteModule() {
    global $pdo;
    
    $moduleId = $_POST['module_id'] ?? null;
    
    if (!$moduleId) {
        throw new Exception('Module ID required');
    }
    
    // Transaction başlat
    $pdo->beginTransaction();
    
    try {
        // İlişkili verileri sil
        $pdo->prepare("DELETE FROM module_components WHERE module_id = ?")->execute([$moduleId]);
        $pdo->prepare("DELETE FROM module_workflows WHERE module_id = ?")->execute([$moduleId]);
        $pdo->prepare("DELETE FROM company_module_subscriptions WHERE module_id = ?")->execute([$moduleId]);
        
        // Modülü sil
        $stmt = $pdo->prepare("DELETE FROM marketplace_modules WHERE id = ?");
        $stmt->execute([$moduleId]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Module not found');
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Module deleted successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Modül kopyalama işlemi
 */
function duplicateModule() {
    global $pdo;
    
    $moduleId = $_POST['module_id'] ?? null;
    
    if (!$moduleId) {
        throw new Exception('Module ID required');
    }
    
    // Orijinal modülü yükle
    $stmt = $pdo->prepare("SELECT * FROM marketplace_modules WHERE id = ?");
    $stmt->execute([$moduleId]);
    $originalModule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$originalModule) {
        throw new Exception('Original module not found');
    }
    
    // Yeni modül verilerini hazırla
    $newModuleData = [
        'name' => $originalModule['name'] . ' (Copy)',
        'description' => $originalModule['description'],
        'category' => $originalModule['category'],
        'version' => '1.0.0',
        'price' => $originalModule['price'],
        'components' => [], // Bu daha sonra kopyalanacak
        'workflow' => []
    ];
    
    // Yeni modül oluştur
    $newModuleId = createNewModule($newModuleData);
    
    // Component ve workflow verilerini kopyala
    copyModuleComponents($moduleId, $newModuleId);
    copyModuleWorkflows($moduleId, $newModuleId);
    
    echo json_encode([
        'success' => true,
        'module_id' => $newModuleId,
        'message' => 'Module duplicated successfully'
    ]);
}

/**
 * Modül verilerini doğrula
 */
function validateModuleData($data) {
    $required = ['name', 'description', 'category', 'version'];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Field '{$field}' is required");
        }
    }
    
    if (!is_array($data['components'])) {
        throw new Exception('Components must be an array');
    }
    
    // Version format kontrolü
    if (!preg_match('/^\d+\.\d+\.\d+$/', $data['version'])) {
        throw new Exception('Invalid version format (expected: x.y.z)');
    }
}

/**
 * Yeni modül oluştur
 */
function createNewModule($data) {
    global $pdo;
    
    $adminId = $_SESSION['platform_admin_id'] ?? 1; // Default admin ID
    
    // Transaction başlat
    $pdo->beginTransaction();
    
    try {
        // Ana modül kaydını oluştur (sisteminizin yapısına uygun)
        $stmt = $pdo->prepare("
            INSERT INTO marketplace_modules (
                module_name, module_code, description, category, version, price, 
                status, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'draft', ?, NOW(), NOW())
        ");
        
        // Module code oluştur
        $moduleCode = strtolower(str_replace(' ', '_', $data['name']));
        
        $stmt->execute([
            $data['name'],
            $moduleCode,
            $data['description'],
            $data['category'],
            $data['version'],
            $data['price'] ?? 0,
            $adminId
        ]);
        
        $moduleId = $pdo->lastInsertId();
        
        // Component verilerini kaydet (sisteminizin yapısına uygun)
        if (!empty($data['components'])) {
            saveModuleComponentsNew($moduleId, $data['components']);
        }
        
        $pdo->commit();
        return $moduleId;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Modül component'larını kaydet (yeni yapı)
 */
function saveModuleComponentsNew($moduleId, $components) {
    global $pdo;
    
    foreach ($components as $index => $component) {
        $stmt = $pdo->prepare("
            INSERT INTO module_components (
                module_id, component_name, component_type, component_code, 
                config, order_index, is_locked, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
        ");
        
        // Component code oluştur
        $componentCode = strtolower(str_replace(' ', '_', $component['name'])) . '_' . ($index + 1);
        
        $stmt->execute([
            $moduleId,
            $component['name'],
            $component['type'],
            $componentCode,
            json_encode($component['properties'] ?? []),
            $index
        ]);
    }
}

/**
 * Mevcut modülü güncelle
 */
function updateExistingModule($moduleId, $data) {
    global $pdo;
    
    // Transaction başlat
    $pdo->beginTransaction();
    
    try {
        // Ana modül kaydını güncelle
        $stmt = $pdo->prepare("
            UPDATE marketplace_modules 
            SET name = ?, description = ?, category = ?, 
                version = ?, price = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['category'],
            $data['version'],
            $data['price'] ?? 0,
            $moduleId
        ]);
        
        // Mevcut component verilerini sil ve yeniden oluştur
        $pdo->prepare("DELETE FROM module_components WHERE module_id = ?")->execute([$moduleId]);
        if (!empty($data['components'])) {
            saveModuleComponents($moduleId, $data['components']);
        }
        
        // Mevcut workflow verilerini sil ve yeniden oluştur
        $pdo->prepare("DELETE FROM module_workflows WHERE module_id = ?")->execute([$moduleId]);
        if (!empty($data['workflow'])) {
            saveModuleWorkflows($moduleId, $data['workflow']);
        }
        
        $pdo->commit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Modül component'larını kaydet
 */
function saveModuleComponents($moduleId, $components) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO module_components (
            module_id, components_data, created_at
        ) VALUES (?, ?, NOW())
    ");
    
    $stmt->execute([$moduleId, json_encode($components)]);
}

/**
 * Modül workflow'unu kaydet
 */
function saveModuleWorkflows($moduleId, $workflow) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO module_workflows (
            module_id, workflow_data, created_at
        ) VALUES (?, ?, NOW())
    ");
    
    $stmt->execute([$moduleId, json_encode($workflow)]);
}

/**
 * Component'ları kopyala
 */
function copyModuleComponents($sourceModuleId, $targetModuleId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT components_data FROM module_components 
        WHERE module_id = ?
    ");
    
    $stmt->execute([$sourceModuleId]);
    $components = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($components && $components['components_data']) {
        saveModuleComponents($targetModuleId, json_decode($components['components_data'], true));
    }
}

/**
 * Workflow'ları kopyala
 */
function copyModuleWorkflows($sourceModuleId, $targetModuleId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT workflow_data FROM module_workflows 
        WHERE module_id = ?
    ");
    
    $stmt->execute([$sourceModuleId]);
    $workflow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($workflow && $workflow['workflow_data']) {
        saveModuleWorkflows($targetModuleId, json_decode($workflow['workflow_data'], true));
    }
}
?>