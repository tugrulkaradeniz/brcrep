<?php
// ===== FIXED MODULE BUILDER PHP =====
// platform/ajax/module-builder.php

// Buffer'ı temizle (JSON response için önemli)
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Session kontrolü - sadece başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Method kontrolü - GET ve POST'u destekle
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Only GET and POST supported.']);
    exit();
}

// Database bağlantısı - MULTIPLE PATH SUPPORT
// Database bağlantısı - CORRECT PATHS for your structure
$db_paths = [
    '../../../dbConnect/dbkonfigur.php',  // platform/ajax/ to brcproject/dbConnect/
    '../../dbConnect/dbkonfigur.php',  
    '../../../../dbConnect/dbkonfigur.php', // just in case
    '../dbConnect/dbkonfigur.php',        // short path
    './dbConnect/dbkonfigur.php'          // current dir
];

$pdo = null;
$db_connected = false;

foreach ($db_paths as $path) {
    if (file_exists($path)) {
        try {
            require_once $path;
            $db_connected = true;
            //echo json_encode(['debug' => 'Database file found at: ' . $path]);
            break;
        } catch (Exception $e) {
            continue;
        }
    }
}

if (!$db_connected) {
    // Create manual PDO connection if no config file found
    try {
        // Common database configurations
        $configs = [
            ['localhost', 'brcproject', 'root', ''],
            ['localhost', 'brc_database', 'root', ''],
            ['127.0.0.1', 'brcproject', 'root', ''],
            ['localhost', 'brcproject', 'root', 'root']
        ];
        
        foreach ($configs as $config) {
            try {
                $pdo = new PDO(
                    "mysql:host={$config[0]};dbname={$config[1]};charset=utf8", 
                    $config[2], 
                    $config[3]
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db_connected = true;
                break;
            } catch (Exception $e) {
                continue;
            }
        }
    } catch (Exception $e) {
        // Still no connection
    }
}

if (!$db_connected) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed',
        'message' => 'Could not find database configuration file',
        'searched_paths' => $db_paths,
        'current_dir' => __DIR__,
        'suggestion' => 'Please check database configuration file path'
    ]);
    exit();
}

// DEBUG: Gelen veriyi logla
function debugLog($message, $data = []) {
    $logDir = '../../logs/';
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $log = date('Y-m-d H:i:s') . ' - DEBUG: ' . $message . ' - ' . json_encode($data) . "\n";
    @file_put_contents($logDir . 'debug.log', $log, FILE_APPEND);
}

// Input validation - JSON, POST ve GET destekle
$input_json = json_decode(file_get_contents('php://input'), true);
$input_post = $_POST;
$input_get = $_GET;

// Debug: Gelen verileri logla
debugLog('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
debugLog('POST Data', $_POST);
debugLog('GET Data', $_GET);
debugLog('JSON Data', $input_json);

// Input'u belirle - Priority: JSON > POST > GET
if (!empty($input_json)) {
    $input = $input_json;
    debugLog('Using JSON input');
} else if (!empty($input_post)) {
    $input = $input_post;
    debugLog('Using POST input');
} else if (!empty($input_get)) {
    $input = $input_get;
    debugLog('Using GET input');
} else {
    debugLog('No input found');
    echo json_encode(['error' => 'No input data found']);
    exit();
}

// GET request için default action
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($input['action'])) {
    // URL'den action'ı belirle
    if (isset($input['edit']) || isset($input['id'])) {
        $input['action'] = 'get_module_details';
        $input['module_id'] = $input['edit'] ?? $input['id'];
        debugLog('GET request detected, setting action to get_module_details', $input);
    } else {
        $input['action'] = 'get_modules';
        debugLog('GET request detected, setting action to get_modules');
    }
}

if (!isset($input['action'])) {
    debugLog('No action found', $input);
    echo json_encode(['error' => 'Action required', 'received_data' => $input]);
    exit();
}

$action = $input['action'];
debugLog('Action detected', ['action' => $action]);

// Error logging fonksiyonu
function logError($message, $context = []) {
    $logDir = '../../logs/';
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $log = date('Y-m-d H:i:s') . ' - MODULE-BUILDER: ' . $message . ' - ' . json_encode($context) . "\n";
    @file_put_contents($logDir . 'error.log', $log, FILE_APPEND);
}

// Database tablo yapısını kontrol et
function checkTableStructure($pdo) {
    try {
        $stmt = $pdo->query("DESCRIBE marketplace_modules");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    } catch (Exception $e) {
        return [];
    }
}

// UNIQUE MODULE CODE OLUŞTUR
function generateUniqueModuleCode($pdo, $name) {
    $base_code = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));
    $base_code = preg_replace('/_+/', '_', $base_code);
    $base_code = trim($base_code, '_');
    
    $module_code = $base_code;
    $counter = 1;
    while (moduleCodeExists($pdo, $module_code)) {
        $module_code = $base_code . '_' . time() . '_' . $counter;
        $counter++;
        if ($counter > 10) {
            $module_code = $base_code . '_' . uniqid();
            break;
        }
    }
    return $module_code;
}

function moduleCodeExists($pdo, $module_code) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM marketplace_modules WHERE module_code = ?");
        $stmt->execute([$module_code]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

try {
    switch ($action) {
        case 'debug':
            echo json_encode([
                'success' => true,
                'debug_info' => [
                    'post_data' => $_POST,
                    'get_data' => $_GET,
                    'json_data' => $input_json,
                    'final_input' => $input,
                    'session' => $_SESSION,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'db_connected' => $db_connected
                ]
            ]);
            break;
            
        case 'test_connection':
            $columns = checkTableStructure($pdo);
            echo json_encode([
                'success' => true,
                'message' => 'Module builder connection test successful',
                'timestamp' => date('Y-m-d H:i:s'),
                'request_method' => $_SERVER['REQUEST_METHOD'],
                'db_connected' => $db_connected,
                'session_info' => [
                    'admin_id' => $_SESSION['admin_id'] ?? 'not_set',
                    'user_id' => $_SESSION['user_id'] ?? 'not_set',
                    'company_id' => $_SESSION['company_id'] ?? 'not_set',
                    'platform_admin_id' => $_SESSION['platform_admin_id'] ?? 'not_set'
                ],
                'table_columns' => $columns
            ]);
            break;
            
        case 'save':
            $module_id = $input['module_id'] ?? null;
            $components = json_decode($input['components'] ?? '[]', true);
            
            try {
                if (isset($input['name'])) {
                    $sql = "UPDATE marketplace_modules SET 
                        name = :name,
                        description = :description,
                        category = :category,
                        version = :version,
                        price = :price,
                        updated_at = NOW()
                        WHERE id = :module_id";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':name' => $input['name'],
                        ':description' => $input['description'],
                        ':category' => $input['category'],
                        ':version' => $input['version'],
                        ':price' => $input['price'],
                        ':module_id' => $module_id
                    ]);
                }
                
                // Eski komponentleri sil
                $sql = "DELETE FROM module_components WHERE module_id = :module_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':module_id' => $module_id]);
                
                // Yeni komponentleri kaydet
                foreach ($components as $component) {
                    $sql = "INSERT INTO module_components (
                        module_id, component_name, component_type, component_code,
                        component_config, position_x, position_y, 
                        width, height, created_at
                    ) VALUES (
                        :module_id, :component_name, :component_type, :component_code,
                        :component_config, :position_x, :position_y,
                        :width, :height, NOW()
                    )";
                    
                    $component_code = strtolower(str_replace([' ', '-'], '_', $component['name'])) . '_' . time();
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':module_id' => $module_id,
                        ':component_name' => $component['name'],
                        ':component_type' => $component['type'],
                        ':component_code' => $component_code,
                        ':component_config' => json_encode($component['config']),
                        ':position_x' => $component['position_x'],
                        ':position_y' => $component['position_y'],
                        ':width' => $component['width'],
                        ':height' => $component['height']
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Module and components saved successfully',
                    'module_id' => $module_id,
                    'components_count' => count($components)
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Save failed: ' . $e->getMessage()
                ]);
            }
            break;

        case 'add_component':
            try {
                // $input kullan, $_POST değil
                debugLog('add_component called', $input);
                
                $component_code = strtolower(str_replace([' ', '-'], '_', $input['component_name'])) . '_' . time();
                
                $sql = "INSERT INTO module_components (
                    module_id, component_name, component_type, component_code,
                    component_config, position_x, position_y,
                    width, height, created_at
                ) VALUES (
                    :module_id, :component_name, :component_type, :component_code,
                    :component_config, :position_x, :position_y,
                    :width, :height, NOW()
                )";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':module_id' => (int)$input['module_id'],  // $input kullan!
                    ':component_name' => $input['component_name'],
                    ':component_type' => $input['component_type'],
                    ':component_code' => $component_code,
                    ':component_config' => $input['component_config'],
                    ':position_x' => (float)$input['position_x'],
                    ':position_y' => (float)$input['position_y'],
                    ':width' => (int)$input['width'],
                    ':height' => (int)$input['height']
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Component added successfully',
                        'component_id' => $pdo->lastInsertId(),
                        'component_code' => $component_code
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to insert component'
                    ]);
                }
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to add component: ' . $e->getMessage(),
                    'debug_input' => $input  // Debug için
                ]);
            }
            break;

        case 'create_module':
            debugLog('Creating module with action: ' . $action, $input);
            
            $name = $input['name'] ?? $input['module_name'] ?? $input['title'] ?? '';
            $description = $input['description'] ?? '';
            $category = $input['category'] ?? 'general';
            $created_by = $_SESSION['admin_id'] ?? $_SESSION['platform_admin_id'] ?? $_SESSION['user_id'] ?? 1;
            
            if (empty($name)) {
                echo json_encode(['error' => 'Module name required', 'received_data' => $input]);
                exit();
            }
            
            $columns = checkTableStructure($pdo);
            
            if (in_array('module_name', $columns)) {
                $module_code = generateUniqueModuleCode($pdo, $name);
                $stmt = $pdo->prepare('
                    INSERT INTO marketplace_modules 
                    (name, module_name, module_code, description, category, created_by, status, version, currency, price, is_base_module, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ');
                $params = [$name, $name, $module_code, $description, $category, $created_by, 'published', '1.0', 'USD', 0.00, 0];
            } else {
                $stmt = $pdo->prepare('
                    INSERT INTO marketplace_modules 
                    (name, description, category, created_by, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ');
                $params = [$name, $description, $category, $created_by, 'published'];
            }
            
            if ($stmt->execute($params)) {
                $module_id = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Module created successfully',
                    'module_id' => $module_id,
                    'module_name' => $name,
                    'redirect' => "module-builder.php?id={$module_id}"
                ]);
            } else {
                echo json_encode(['error' => 'Failed to create module']);
            }
            break;

        case 'update_module':
            try {
                // $input kullan, $_POST değil
                $sql = "UPDATE marketplace_modules SET 
                    name = :name,
                    description = :description,
                    category = :category,
                    version = :version,
                    price = :price,
                    updated_at = NOW()
                    WHERE id = :module_id";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':name' => $input['name'],
                    ':description' => $input['description'],
                    ':category' => $input['category'],
                    ':version' => $input['version'],
                    ':price' => (float)($input['price'] ?? 0),
                    ':module_id' => (int)$input['module_id']
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Module updated successfully',
                        'module_id' => $input['module_id'],
                        'module_name' => $input['name']
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to update module'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Update failed: ' . $e->getMessage()
                ]);
            }
            break;
            
        case 'get_modules':
            $stmt = $pdo->prepare('
                SELECT m.*, COUNT(c.id) as component_count 
                FROM marketplace_modules m 
                LEFT JOIN module_components c ON m.id = c.module_id 
                GROUP BY m.id 
                ORDER BY m.created_at DESC
            ');
            $stmt->execute();
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($modules as &$module) {
                if (empty($module['name'])) {
                    if (!empty($module['module_name'])) {
                        $module['name'] = $module['module_name'];
                    } elseif (!empty($module['module_code'])) {
                        $module['name'] = $module['module_code'];
                    } else {
                        $module['name'] = 'Unnamed Module';
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'modules' => $modules
            ]);
            break;
            
        case 'get_module_details':
            $module_id = $input['module_id'] ?? $input['id'] ?? 0;
            
            if (!$module_id) {
                echo json_encode(['error' => 'Module ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare('SELECT * FROM marketplace_modules WHERE id = ?');
            $stmt->execute([$module_id]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                echo json_encode(['error' => 'Module not found']);
                exit();
            }
            
            if (empty($module['name'])) {
                if (!empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
                } elseif (!empty($module['module_code'])) {
                    $module['name'] = $module['module_code'];
                } else {
                    $module['name'] = 'Unnamed Module';
                }
            }
            
            $stmt = $pdo->prepare('SELECT * FROM module_components WHERE module_id = ? ORDER BY created_at ASC');
            $stmt->execute([$module_id]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'module' => $module,
                'components' => $components
            ]);
            break;

        case 'delete_all_components':
            try {
                // $input kullan, $_POST değil
                $sql = "DELETE FROM module_components WHERE module_id = :module_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':module_id' => (int)$input['module_id']]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'All components deleted successfully'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to delete components: ' . $e->getMessage()
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'error' => 'Unknown action: ' . $action, 
                'available_actions' => ['save', 'create_module', 'update_module', 'get_modules', 'get_module_details', 'test_connection', 'debug']
            ]);
            break;
    }
    
} catch (Exception $e) {
    logError('Exception in module-builder.php', [
        'action' => $action,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}

// Buffer'ı flush et
ob_end_flush();
?>