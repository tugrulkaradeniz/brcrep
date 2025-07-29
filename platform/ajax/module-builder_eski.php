<?php
// ===== DÜZELTILMIŞ MODULE BUILDER =====
// platform/ajax/module-builder.php - Fixed Version

session_start();

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

// Method kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Database bağlantısı
try {
    require_once '../../dbConnect/dbkonfigur.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
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

// Input validation - Hem JSON hem Form destekle
$input_json = json_decode(file_get_contents('php://input'), true);
$input_post = $_POST;

// Debug: Gelen verileri logla
debugLog('POST Data', $_POST);
debugLog('JSON Data', $input_json);

// Input'u belirle
if (!empty($input_json)) {
    $input = $input_json;
    debugLog('Using JSON input');
} else if (!empty($input_post)) {
    $input = $input_post;
    debugLog('Using POST input');
} else {
    debugLog('No input found');
    echo json_encode(['error' => 'No input data found']);
    exit();
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
    $base_code = preg_replace('/_+/', '_', $base_code); // Çoklu underscore'ları tek yap
    $base_code = trim($base_code, '_'); // Başta/sonda underscore varsa sil
    
    // İlk deneme
    $module_code = $base_code;
    
    // Eğer var ise timestamp ekle
    $counter = 1;
    while (moduleCodeExists($pdo, $module_code)) {
        $module_code = $base_code . '_' . time() . '_' . $counter;
        $counter++;
        
        // Sonsuz döngüye girmesin diye
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
        return false; // Tablo yoksa veya hata varsa false döndür
    }
}

// Template fonksiyonları
function createDefaultModuleTemplate($pdo, $module_id, $module_name, $category) {
    switch ($category) {
        case 'quality_control':
            createQualityControlTemplate($pdo, $module_id);
            break;
        case 'traceability':
            createTraceabilityTemplate($pdo, $module_id);
            break;
        default:
            createBasicTemplate($pdo, $module_id);
            break;
    }
}

function createQualityControlTemplate($pdo, $module_id) {
    $components = [
        [
            'component_name' => 'Süreç Kontrol Formu',
            'component_type' => 'form',
            'component_config' => json_encode([
                'fields' => [
                    ['name' => 'process_step', 'type' => 'text', 'label' => 'Süreç Adımı', 'required' => true],
                    ['name' => 'control_point', 'type' => 'textarea', 'label' => 'Kontrol Noktası'],
                    ['name' => 'responsible_person', 'type' => 'text', 'label' => 'Sorumlu Kişi'],
                    ['name' => 'criteria', 'type' => 'textarea', 'label' => 'Kriterler']
                ]
            ]),
            'position_x' => 0,
            'position_y' => 0,
            'width' => 600,
            'height' => 400
        ],
        [
            'component_name' => 'Kalite Kontrol Dashboard',
            'component_type' => 'dashboard',
            'component_config' => json_encode([
                'widgets' => [
                    ['type' => 'counter', 'title' => 'Aktif Süreçler', 'value' => 0],
                    ['type' => 'counter', 'title' => 'Tamamlanan Kontroller', 'value' => 0],
                    ['type' => 'counter', 'title' => 'Bekleyen Düzeltmeler', 'value' => 0]
                ]
            ]),
            'position_x' => 0,
            'position_y' => 420,
            'width' => 600,
            'height' => 200
        ]
    ];
    
    foreach ($components as $component) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO module_components 
                (component_name, component_type, component_config, module_id, position_x, position_y, width, height, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $component['component_name'],
                $component['component_type'],
                $component['component_config'],
                $module_id,
                $component['position_x'],
                $component['position_y'],
                $component['width'],
                $component['height']
            ]);
        } catch (Exception $e) {
            debugLog('Component creation error', ['error' => $e->getMessage(), 'component' => $component['component_name']]);
        }
    }
}

function createTraceabilityTemplate($pdo, $module_id) {
    $components = [
        [
            'component_name' => 'Parti Takip Formu',
            'component_type' => 'form',
            'component_config' => json_encode([
                'fields' => [
                    ['name' => 'lot_number', 'type' => 'text', 'label' => 'Parti Numarası', 'required' => true],
                    ['name' => 'raw_material_weight', 'type' => 'number', 'label' => 'Hammadde Ağırlığı (kg)'],
                    ['name' => 'process_stage', 'type' => 'select', 'label' => 'Süreç Aşaması', 'options' => ['Hammadde Girişi', 'Fümigasyon', 'Yıkama', 'Kurutma', 'Paketleme']]
                ]
            ]),
            'position_x' => 0,
            'position_y' => 0,
            'width' => 600,
            'height' => 300
        ]
    ];
    
    foreach ($components as $component) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO module_components 
                (component_name, component_type, component_config, module_id, position_x, position_y, width, height, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $component['component_name'],
                $component['component_type'],
                $component['component_config'],
                $module_id,
                $component['position_x'],
                $component['position_y'],
                $component['width'],
                $component['height']
            ]);
        } catch (Exception $e) {
            debugLog('Component creation error', ['error' => $e->getMessage()]);
        }
    }
}

function createBasicTemplate($pdo, $module_id) {
    $components = [
        [
            'component_name' => 'Genel Form',
            'component_type' => 'form',
            'component_config' => json_encode([
                'fields' => [
                    ['name' => 'title', 'type' => 'text', 'label' => 'Başlık', 'required' => true],
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'Açıklama']
                ]
            ]),
            'position_x' => 0,
            'position_y' => 0,
            'width' => 500,
            'height' => 300
        ]
    ];
    
    foreach ($components as $component) {
        try {
            $stmt = $pdo->prepare('
                INSERT INTO module_components 
                (component_name, component_type, component_config, module_id, position_x, position_y, width, height, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $stmt->execute([
                $component['component_name'],
                $component['component_type'],
                $component['component_config'],
                $module_id,
                $component['position_x'],
                $component['position_y'],
                $component['width'],
                $component['height']
            ]);
        } catch (Exception $e) {
            debugLog('Component creation error', ['error' => $e->getMessage()]);
        }
    }
}

try {
    switch ($action) {
        case 'debug':
            echo json_encode([
                'success' => true,
                'debug_info' => [
                    'post_data' => $_POST,
                    'json_data' => $input_json,
                    'final_input' => $input,
                    'session' => $_SESSION,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
            break;
            
        case 'test_connection':
            $columns = checkTableStructure($pdo);
            echo json_encode([
                'success' => true,
                'message' => 'Module builder connection test successful',
                'timestamp' => date('Y-m-d H:i:s'),
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
        case 'create_module':
            debugLog('Creating module with action: ' . $action, $input);
            
            // Flexible field mapping
            $name = $input['name'] ?? $input['module_name'] ?? $input['title'] ?? '';
            $description = $input['description'] ?? '';
            $category = $input['category'] ?? 'general';
            $created_by = $_SESSION['admin_id'] ?? $_SESSION['platform_admin_id'] ?? $_SESSION['user_id'] ?? 1;
            
            debugLog('Module data extracted', [
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'created_by' => $created_by
            ]);
            
            if (empty($name)) {
                debugLog('Empty module name', $input);
                echo json_encode(['error' => 'Module name required', 'received_data' => $input]);
                exit();
            }
            
            // Tablo yapısını kontrol et
            $columns = checkTableStructure($pdo);
            debugLog('Table columns', $columns);
            
            if (in_array('module_name', $columns)) {
                // Yeni yapı: module_name kolonu var
                $module_code = generateUniqueModuleCode($pdo, $name);
                
                debugLog('Generated unique module code', ['original_name' => $name, 'module_code' => $module_code]);
                
                $stmt = $pdo->prepare('
                    INSERT INTO marketplace_modules 
                    (name, module_name, module_code, description, category, created_by, status, version, currency, price, is_base_module, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ');
                
                $params = [
                    $name,           // name
                    $name,           // module_name (aynı değer)
                    $module_code,    // module_code (UNIQUE)
                    $description,    // description
                    $category,       // category
                    $created_by,     // created_by
                    'published',     // status
                    '1.0',          // version
                    'USD',          // currency
                    0.00,           // price
                    0               // is_base_module
                ];
                
                debugLog('SQL Parameters (new structure)', $params);
                $result = $stmt->execute($params);
            } else {
                // Eski yapı: sadece name kolonu var
                $stmt = $pdo->prepare('
                    INSERT INTO marketplace_modules 
                    (name, description, category, created_by, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ');
                
                $params = [$name, $description, $category, $created_by, 'published'];
                debugLog('SQL Parameters (old structure)', $params);
                $result = $stmt->execute($params);
            }
            
            if ($result) {
                $module_id = $pdo->lastInsertId();
                
                // Modül için varsayılan template oluştur
                createDefaultModuleTemplate($pdo, $module_id, $name, $category);
                
                debugLog('Module created successfully', ['module_id' => $module_id, 'name' => $name]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Module created successfully',
                    'module_id' => $module_id,
                    'module_name' => $name,
                    'module_code' => $module_code ?? $name,
                    'redirect' => "module-builder.php?id={$module_id}"
                ]);
            } else {
                $error_info = $pdo->errorInfo();
                debugLog('Failed to create module', ['name' => $name, 'error' => $error_info]);
                echo json_encode(['error' => 'Failed to create module', 'sql_error' => $error_info]);
            }
            break;

        // YENİ: UPDATE_MODULE ACTION EKLENDI
        case 'update_module':
            debugLog('Updating module', $input);
            
            $module_id = $input['module_id'] ?? 0;
            if (!$module_id) {
                echo json_encode(['error' => 'Module ID required for update']);
                exit();
            }
            
            // Flexible field mapping for update
            $name = $input['name'] ?? $input['module_name'] ?? '';
            $description = $input['description'] ?? '';
            $category = $input['category'] ?? 'general';
            $updated_by = $_SESSION['admin_id'] ?? $_SESSION['platform_admin_id'] ?? $_SESSION['user_id'] ?? 1;
            
            if (empty($name)) {
                echo json_encode(['error' => 'Module name required for update']);
                exit();
            }
            
            debugLog('Update data extracted', [
                'module_id' => $module_id,
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'updated_by' => $updated_by
            ]);
            
            // Tablo yapısını kontrol et
            $columns = checkTableStructure($pdo);
            
            if (in_array('module_name', $columns)) {
                // Yeni yapı: hem name hem module_name güncelle
                $stmt = $pdo->prepare('
                    UPDATE marketplace_modules 
                    SET name = ?, module_name = ?, description = ?, category = ?, updated_at = NOW()
                    WHERE id = ?
                ');
                
                $params = [$name, $name, $description, $category, $module_id];
                debugLog('SQL Update Parameters (new structure)', $params);
                $result = $stmt->execute($params);
            } else {
                // Eski yapı: sadece name güncelle
                $stmt = $pdo->prepare('
                    UPDATE marketplace_modules 
                    SET name = ?, description = ?, category = ?, updated_at = NOW()
                    WHERE id = ?
                ');
                
                $params = [$name, $description, $category, $module_id];
                debugLog('SQL Update Parameters (old structure)', $params);
                $result = $stmt->execute($params);
            }
            
            if ($result) {
                debugLog('Module updated successfully', ['module_id' => $module_id, 'name' => $name]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Module updated successfully',
                    'module_id' => $module_id,
                    'module_name' => $name
                ]);
            } else {
                $error_info = $pdo->errorInfo();
                debugLog('Failed to update module', ['module_id' => $module_id, 'error' => $error_info]);
                echo json_encode(['error' => 'Failed to update module', 'sql_error' => $error_info]);
            }
            break;

        // YENİ: COMPONENT İŞLEMLERİ
        case 'add_component':
            $module_id = $input['module_id'] ?? 0;
            $component_name = $input['component_name'] ?? 'New Component';
            $component_type = $input['component_type'] ?? 'form';
            $component_config = $input['component_config'] ?? '{}';
            $position_x = $input['position_x'] ?? 0;
            $position_y = $input['position_y'] ?? 0;
            $width = $input['width'] ?? 300;
            $height = $input['height'] ?? 200;
            
            if (!$module_id) {
                echo json_encode(['error' => 'Module ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO module_components 
                (component_name, component_type, component_config, module_id, position_x, position_y, width, height, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $result = $stmt->execute([
                $component_name,
                $component_type,
                $component_config,
                $module_id,
                $position_x,
                $position_y,
                $width,
                $height
            ]);
            
            if ($result) {
                $component_id = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Component added successfully',
                    'component_id' => $component_id
                ]);
            } else {
                echo json_encode(['error' => 'Failed to add component']);
            }
            break;

        case 'update_component':
            $component_id = $input['component_id'] ?? 0;
            $component_name = $input['component_name'] ?? '';
            $component_config = $input['component_config'] ?? '{}';
            $position_x = $input['position_x'] ?? 0;
            $position_y = $input['position_y'] ?? 0;
            $width = $input['width'] ?? 300;
            $height = $input['height'] ?? 200;
            
            if (!$component_id) {
                echo json_encode(['error' => 'Component ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare('
                UPDATE module_components 
                SET component_name = ?, component_config = ?, position_x = ?, position_y = ?, width = ?, height = ?, updated_at = NOW()
                WHERE id = ?
            ');
            
            $result = $stmt->execute([
                $component_name,
                $component_config,
                $position_x,
                $position_y,
                $width,
                $height,
                $component_id
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Component updated successfully'
                ]);
            } else {
                echo json_encode(['error' => 'Failed to update component']);
            }
            break;

        case 'delete_component':
            $component_id = $input['component_id'] ?? 0;
            
            if (!$component_id) {
                echo json_encode(['error' => 'Component ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare('DELETE FROM module_components WHERE id = ?');
            $result = $stmt->execute([$component_id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            } else {
                echo json_encode(['error' => 'Failed to delete component']);
            }
            break;

        case 'update_component_positions':
            $positions = $input['positions'] ?? [];
            
            if (empty($positions)) {
                echo json_encode(['error' => 'No positions provided']);
                exit();
            }
            
            $updated_count = 0;
            foreach ($positions as $position) {
                $component_id = $position['component_id'] ?? 0;
                $x = $position['x'] ?? 0;
                $y = $position['y'] ?? 0;
                
                if ($component_id) {
                    $stmt = $pdo->prepare('
                        UPDATE module_components 
                        SET position_x = ?, position_y = ?, updated_at = NOW()
                        WHERE id = ?
                    ');
                    
                    if ($stmt->execute([$x, $y, $component_id])) {
                        $updated_count++;
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Updated {$updated_count} component positions"
            ]);
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
            
            // Module name'i düzelt
            foreach ($modules as &$module) {
                // Name priority: name > module_name > module_code
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
            
            debugLog('Getting module details', ['module_id' => $module_id]);
            
            // Module bilgisini al
            $stmt = $pdo->prepare('SELECT * FROM marketplace_modules WHERE id = ?');
            $stmt->execute([$module_id]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                echo json_encode(['error' => 'Module not found']);
                exit();
            }
            
            // Name düzeltmesi - priority: name > module_name > module_code
            if (empty($module['name'])) {
                if (!empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
                } elseif (!empty($module['module_code'])) {
                    $module['name'] = $module['module_code'];
                } else {
                    $module['name'] = 'Unnamed Module';
                }
            }
            
            // Components'ları al
            $stmt = $pdo->prepare('SELECT * FROM module_components WHERE module_id = ? ORDER BY created_at ASC');
            $stmt->execute([$module_id]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Workflows'ları al
            $stmt = $pdo->prepare('SELECT * FROM module_workflows WHERE module_id = ?');
            $stmt->execute([$module_id]);
            $workflows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            debugLog('Module details retrieved', [
                'module_id' => $module_id,
                'module_name' => $module['name'],
                'components_count' => count($components),
                'workflows_count' => count($workflows)
            ]);
            
            echo json_encode([
                'success' => true,
                'module' => $module,
                'components' => $components,
                'workflows' => $workflows
            ]);
            break;
            
        case 'preview':
            $module_id = $input['module_id'] ?? $input['id'] ?? 0;
            
            if (!$module_id) {
                echo json_encode(['error' => 'Module ID required for preview']);
                exit();
            }
            
            // Module bilgisini al
            $stmt = $pdo->prepare('SELECT * FROM marketplace_modules WHERE id = ?');
            $stmt->execute([$module_id]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                echo json_encode(['error' => 'Module not found']);
                exit();
            }
            
            // Name düzeltmesi
            if (empty($module['name'])) {
                if (!empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
                } elseif (!empty($module['module_code'])) {
                    $module['name'] = $module['module_code'];
                } else {
                    $module['name'] = 'Unnamed Module';
                }
            }
            
            // Components'ları al
            $stmt = $pdo->prepare('SELECT * FROM module_components WHERE module_id = ?');
            $stmt->execute([$module_id]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Preview HTML oluştur
            $preview_html = "
            <div class='module-preview'>
                <h3>{$module['name']}</h3>
                <p>{$module['description']}</p>
                <div class='components-preview'>
                    <h5>Components (" . count($components) . "):</h5>
            ";
            
            foreach ($components as $component) {
                $preview_html .= "<div class='component-item'>
                    <strong>{$component['component_name']}</strong> 
                    <span class='badge bg-info'>{$component['component_type']}</span>
                </div>";
            }
            
            $preview_html .= "
                </div>
                <div class='mt-3'>
                    <button class='btn btn-success' onclick='deployModule({$module['id']})'>Deploy Module</button>
                    <button class='btn btn-primary' onclick='editModule({$module['id']})'>Edit Module</button>
                </div>
            </div>
            ";
            
            echo json_encode([
                'success' => true,
                'preview_html' => $preview_html,
                'module' => $module,
                'components' => $components
            ]);
            break;
            
        default:
            debugLog('Unknown action', ['action' => $action, 'input' => $input]);
            echo json_encode([
                'error' => 'Unknown action: ' . $action, 
                'available_actions' => [
                    'save', 'create_module', 'update_module', 
                    'get_modules', 'get_module_details', 'preview', 
                    'add_component', 'update_component', 'delete_component', 'update_component_positions',
                    'test_connection', 'debug'
                ]
            ]);
            break;
    }
    
} catch (Exception $e) {
    debugLog('Exception occurred', [
        'action' => $action,
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
    
    logError('Exception in module-builder.php', [
        'action' => $action,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'sql_error' => isset($pdo) ? $pdo->errorInfo() : 'No PDO connection'
    ]);
}
?>

<script>

    // ===== DÜZELTİLMİŞ ADMIN PANEL JAVASCRIPT =====
// Bu kodu admin panel'in JavaScript bölümüne ekleyin
// Dosya: platform/pages/module-builder.php (JavaScript section)

// Module edit için gerekli fonksiyonlar
let currentModuleId = null;
let moduleComponents = [];
let moduleData = {};
let isDraggingComponent = false;
let componentPositionChanged = false;

// API base URL
const API_BASE = 'ajax/module-builder.php';

// Sayfa yüklendiğinde URL'den module ID'yi al
function initializeModuleEdit() {
    console.log('🚀 Module Builder initialized');
    
    // URL'den module ID'yi çıkar
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id') || urlParams.get('module_id');
    
    if (moduleId) {
        console.log('📖 Edit mode detected, Module ID:', moduleId);
        currentModuleId = parseInt(moduleId);
        loadModuleForEdit(moduleId);
    } else {
        console.log('➕ Create mode detected');
        setCreateMode();
    }

    // Form submit handler'ını ayarla
    setupFormHandler();
    
    // Auto-save component positions
    setupAutoSave();
}

// Form handler'ını ayarla
function setupFormHandler() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (currentModuleId) {
                updateModule();
            } else {
                createModule();
            }
        });
    }
}

// Auto-save setup
function setupAutoSave() {
    // Her 30 saniyede bir position'ları kaydet
    setInterval(() => {
        if (componentPositionChanged && currentModuleId) {
            console.log('💾 Auto-saving component positions...');
            saveComponentPositions();
            componentPositionChanged = false;
        }
    }, 30000);
}

// Modülü edit için yükle
async function loadModuleForEdit(moduleId) {
    console.log('📡 Loading module for edit:', moduleId);
    
    try {
        showLoadingState();
        
        const response = await apiCall('get_module_details', { module_id: moduleId });
        
        console.log('📥 Module data received:', response);
        
        if (response.success && response.module) {
            moduleData = response.module;
            moduleComponents = response.components || [];
            
            // Form field'larını doldur
            populateFormFields(response.module);
            
            // Components'ları canvas'a çiz
            renderComponentsOnCanvas(response.components || []);
            
            // UI'yi edit mode'a ayarla
            setEditMode(response.module);
            
            console.log('✅ Module loaded successfully');
        } else {
            console.error('❌ Failed to load module:', response.error);
            showError('Module yüklenemedi: ' + (response.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Exception loading module:', error);
        showError('Module yükleme hatası: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

// API çağrısı helper
async function apiCall(action, data = {}) {
    const payload = { action, ...data };
    
    console.log('📤 API Call:', action, payload);
    
    const response = await fetch(API_BASE, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const result = await response.json();
    console.log('📥 API Response:', action, result);
    
    return result;
}

// Form field'larını doldur
function populateFormFields(module) {
    console.log('📝 Populating form fields:', module);
    
    try {
        // Module name field'ları - çoklu field desteği
        const moduleNameSelectors = [
            '#module_name', '#moduleName', 
            'input[name="module_name"]', 'input[name="name"]',
            'input[name="title"]'
        ];
        
        const moduleNameField = findElement(moduleNameSelectors);
        if (moduleNameField) {
            // Name priority: name > module_name > module_code
            const moduleName = module.name || module.module_name || module.module_code || '';
            moduleNameField.value = moduleName;
            console.log('✅ Module name set:', moduleName);
        } else {
            console.warn('⚠️ Module name field not found');
        }
        
        // Description field
        const descSelectors = [
            '#description', '#moduleDescription', 
            'textarea[name="description"]', 'input[name="description"]'
        ];
        
        const descriptionField = findElement(descSelectors);
        if (descriptionField) {
            descriptionField.value = module.description || '';
            console.log('✅ Description set');
        } else {
            console.warn('⚠️ Description field not found');
        }
        
        // Category field
        const catSelectors = [
            '#category', '#moduleCategory', 
            'select[name="category"]', 'input[name="category"]'
        ];
        
        const categoryField = findElement(catSelectors);
        if (categoryField) {
            categoryField.value = module.category || 'general';
            console.log('✅ Category set:', module.category);
        } else {
            console.warn('⚠️ Category field not found');
        }
        
        // Page title güncelle
        updatePageTitle(module.name || module.module_name || 'Edit Module');
        
    } catch (error) {
        console.error('💥 Error populating form fields:', error);
    }
}

// Element bulma helper'ı
function findElement(selectors) {
    for (const selector of selectors) {
        const element = document.querySelector(selector);
        if (element) {
            return element;
        }
    }
    return null;
}

// Components'ları canvas'a çiz
function renderComponentsOnCanvas(components) {
    console.log('🎨 Rendering components on canvas:', components);
    
    try {
        // Canvas elementi bul
        const canvasSelectors = [
            '#module-canvas', '#designer-canvas', 
            '.designer-canvas', '.canvas', '.module-builder-canvas'
        ];
        
        const canvas = findElement(canvasSelectors);
        
        if (!canvas) {
            console.warn('⚠️ Canvas element not found');
            // Fallback: Liste halinde göster
            renderComponentsList(components);
            return;
        }
        
        // Canvas'ı temizle
        canvas.innerHTML = '';
        canvas.style.position = 'relative';
        canvas.style.minHeight = '500px';
        canvas.style.border = '2px dashed #ddd';
        canvas.style.borderRadius = '5px';
        canvas.style.padding = '20px';
        
        if (components.length === 0) {
            canvas.innerHTML = '<div class="empty-canvas" style="text-align: center; color: #999; padding: 50px;">No components yet. Drag components here or create new ones.</div>';
            return;
        }
        
        // Her component için DOM elementi oluştur
        components.forEach((component, index) => {
            const componentElement = createComponentElement(component, index);
            canvas.appendChild(componentElement);
        });
        
        console.log(`✅ ${components.length} components rendered on canvas`);
        
    } catch (error) {
        console.error('💥 Error rendering components:', error);
        // Fallback
        renderComponentsList(components);
    }
}

// Component DOM elementi oluştur
function createComponentElement(component, index) {
    const element = document.createElement('div');
    element.className = 'component-item draggable';
    element.setAttribute('data-component-id', component.id);
    element.setAttribute('data-index', index);
    
    // Position ve size ayarla
    element.style.position = 'absolute';
    element.style.left = (component.position_x || (index * 220)) + 'px';
    element.style.top = (component.position_y || (Math.floor(index / 3) * 250)) + 'px';
    element.style.width = (component.width || 200) + 'px';
    element.style.height = (component.height || 150) + 'px';
    element.style.border = '2px solid #007bff';
    element.style.borderRadius = '8px';
    element.style.backgroundColor = '#ffffff';
    element.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    element.style.cursor = 'move';
    element.style.overflow = 'hidden';
    element.style.display = 'flex';
    element.style.flexDirection = 'column';
    
    // Component içeriği
    element.innerHTML = `
        <div class="component-header" style="background: #007bff; color: white; padding: 8px; font-weight: bold; font-size: 12px; display: flex; justify-content: space-between; align-items: center;">
            <span>${component.component_name || 'Unnamed Component'}</span>
            <span style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; font-size: 10px;">${component.component_type || 'unknown'}</span>
        </div>
        <div class="component-content" style="flex: 1; padding: 10px; font-size: 11px; color: #666;">
            ${getComponentPreview(component)}
        </div>
        <div class="component-actions" style="padding: 5px; border-top: 1px solid #eee; display: flex; gap: 5px;">
            <button onclick="editComponent(${index})" class="btn btn-sm btn-primary" style="font-size: 10px; padding: 2px 6px;">Edit</button>
            <button onclick="deleteComponent(${index})" class="btn btn-sm btn-danger" style="font-size: 10px; padding: 2px 6px;">Delete</button>
        </div>
    `;
    
    // Drag functionality ekle
    makeDraggable(element);
    
    return element;
}

// Component preview içeriği
function getComponentPreview(component) {
    let config = {};
    try {
        config = component.component_config ? JSON.parse(component.component_config) : {};
    } catch (e) {
        console.warn('Invalid component config JSON:', component.component_config);
    }
    
    switch (component.component_type) {
        case 'form':
            const fieldCount = config.fields ? config.fields.length : 0;
            return `📝 Form<br><small>${fieldCount} fields</small>`;
        case 'checklist':
            const itemCount = config.items ? config.items.length : 0;
            return `✅ Checklist<br><small>${itemCount} items</small>`;
        case 'dashboard':
            const widgetCount = config.widgets ? config.widgets.length : 0;
            return `📊 Dashboard<br><small>${widgetCount} widgets</small>`;
        case 'table':
            return `📋 Data Table`;
        case 'chart':
            return `📈 Chart`;
        default:
            return `🔧 ${component.component_type}`;
    }
}

// Fallback: Liste halinde göster
function renderComponentsList(components) {
    console.log('📋 Rendering components as list (fallback)');
    
    let listContainer = document.getElementById('components-list');
    if (!listContainer) {
        // Liste container'ı oluştur
        listContainer = document.createElement('div');
        listContainer.id = 'components-list';
        listContainer.style.marginTop = '20px';
        listContainer.style.border = '1px solid #ddd';
        listContainer.style.borderRadius = '5px';
        listContainer.style.padding = '15px';
        
        // Form'un altına ekle
        const form = document.querySelector('form') || document.body;
        form.appendChild(listContainer);
    }
    
    if (components.length === 0) {
        listContainer.innerHTML = '<h4>Module Components</h4><p>No components found.</p>';
        return;
    }
    
    let html = '<h4>Module Components:</h4><div class="components-grid" style="display: grid; gap: 10px;">';
    components.forEach((component, index) => {
        html += `
        <div style="border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9;">
            <strong>${component.component_name}</strong> 
            <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px;">${component.component_type}</span>
            <br><small>Position: ${component.position_x}, ${component.position_y}</small>
            <div style="margin-top: 5px;">
                <button onclick="editComponent(${index})" class="btn btn-sm btn-primary">Edit</button>
                <button onclick="deleteComponent(${index})" class="btn btn-sm btn-danger">Delete</button>
            </div>
        </div>`;
    });
    html += '</div>';
    
    listContainer.innerHTML = html;
}

// Edit mode ayarları
function setEditMode(module) {
    console.log('⚙️ Setting edit mode UI');
    
    // Page title
    updatePageTitle(`Edit: ${module.name || module.module_name || 'Module'}`);
    
    // Save button'u güncelle
    const saveBtn = findElement(['#save-btn', 'button[type="submit"]', '.btn-save']);
    
    if (saveBtn) {
        saveBtn.textContent = 'Update Module';
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-success');
    }
    
    // Add component button ekle
    addComponentControls();
    
    // Cancel/Back button ekle
    addCancelButton();
    
    // Module info göster
    showModuleInfo(module);
}

// Component controls ekle
function addComponentControls() {
    if (document.getElementById('component-controls')) return;
    
    const controlsDiv = document.createElement('div');
    controlsDiv.id = 'component-controls';
    controlsDiv.style.marginBottom = '20px';
    controlsDiv.style.padding = '15px';
    controlsDiv.style.border = '1px solid #ddd';
    controlsDiv.style.borderRadius = '5px';
    controlsDiv.style.backgroundColor = '#f8f9fa';
    
    controlsDiv.innerHTML = `
        <h5>Component Controls</h5>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="addNewComponent('form')" class="btn btn-sm btn-outline-primary">📝 Add Form</button>
            <button onclick="addNewComponent('checklist')" class="btn btn-sm btn-outline-primary">✅ Add Checklist</button>
            <button onclick="addNewComponent('dashboard')" class="btn btn-sm btn-outline-primary">📊 Add Dashboard</button>
            <button onclick="addNewComponent('table')" class="btn btn-sm btn-outline-primary">📋 Add Table</button>
            <button onclick="saveComponentPositions()" class="btn btn-sm btn-warning">💾 Save Positions</button>
        </div>
    `;
    
    // Canvas'ın üstüne ekle
    const canvas = findElement(['#module-canvas', '#designer-canvas', '.designer-canvas', '.canvas']);
    if (canvas) {
        canvas.parentNode.insertBefore(controlsDiv, canvas);
    } else {
        const form = document.querySelector('form');
        if (form) {
            form.appendChild(controlsDiv);
        }
    }
}

// Module info göster
function showModuleInfo(module) {
    if (document.getElementById('module-info')) return;
    
    const infoDiv = document.createElement('div');
    infoDiv.id = 'module-info';
    infoDiv.style.marginBottom = '15px';
    infoDiv.style.padding = '10px';
    infoDiv.style.backgroundColor = '#d1ecf1';
    infoDiv.style.border = '1px solid #bee5eb';
    infoDiv.style.borderRadius = '5px';
    infoDiv.style.fontSize = '14px';
    
    infoDiv.innerHTML = `
        <strong>📝 Editing Module:</strong> ${module.name || 'Unnamed'} 
        <small>(ID: ${module.id}, Components: ${moduleComponents.length})</small>
    `;
    
    // Form'un başına ekle
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(infoDiv, form.firstChild);
    }
}

// Create mode ayarları
function setCreateMode() {
    console.log('➕ Setting create mode UI');
    updatePageTitle('Create New Module');
    
    // Form'u temizle
    const form = document.querySelector('form');
    if (form) {
        form.reset();
    }
}

// Page title güncelle
function updatePageTitle(title) {
    document.title = title;
    
    const pageHeader = findElement(['h1', '.page-title', 'h2']);
    if (pageHeader) {
        pageHeader.textContent = title;
    }
}

// Cancel button ekle
function addCancelButton() {
    if (document.getElementById('cancel-btn')) return;
    
    const saveBtn = findElement(['button[type="submit"]', '#save-btn']);
    if (saveBtn) {
        const cancelBtn = document.createElement('button');
        cancelBtn.id = 'cancel-btn';
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-secondary';
        cancelBtn.textContent = 'Cancel';
        cancelBtn.style.marginLeft = '10px';
        cancelBtn.onclick = () => {
            if (confirm('Unsaved changes will be lost. Continue?')) {
                window.location.href = 'modules.php';
            }
        };
        
        saveBtn.parentNode.insertBefore(cancelBtn, saveBtn.nextSibling);
    }
}

// Yeni modül oluştur
async function createModule() {
    console.log('➕ Creating new module...');
    
    try {
        showLoadingState();
        
        const formData = collectFormData();
        
        if (!formData.name) {
            showError('Module name is required');
            return;
        }
        
        const response = await apiCall('create_module', formData);
        
        if (response.success) {
            showSuccess('Module created successfully!');
            // Başarılı oluşturma sonrası edit sayfasına git
            setTimeout(() => {
                window.location.href = `module-builder.php?id=${response.module_id}`;
            }, 1500);
        } else {
            showError('Create failed: ' + (response.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Create exception:', error);
        showError('Create error: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

// Module güncelle
async function updateModule() {
    console.log('💾 Updating module...');
    
    try {
        showLoadingState();
        
        const formData = collectFormData();
        
        if (!formData.name) {
            showError('Module name is required');
            return;
        }
        
        const response = await apiCall('update_module', {
            module_id: currentModuleId,
            ...formData
        });
        
        if (response.success) {
            showSuccess('Module updated successfully!');
            
            // Module data'yı güncelle
            moduleData = { ...moduleData, ...formData };
            
            // Auto-save positions if changed
            if (componentPositionChanged) {
                await saveComponentPositions();
            }
            
        } else {
            showError('Update failed: ' + (response.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Update exception:', error);
        showError('Update error: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

// Form verilerini topla
function collectFormData() {
    const data = {};
    
    // Name - çoklu field desteği
    const nameField = findElement([
        '#module_name', '#moduleName', 
        'input[name="module_name"]', 'input[name="name"]', 'input[name="title"]'
    ]);
    if (nameField) data.name = nameField.value.trim();
    
    // Description
    const descField = findElement([
        '#description', '#moduleDescription', 
        'textarea[name="description"]', 'input[name="description"]'
    ]);
    if (descField) data.description = descField.value.trim();
    
    // Category
    const catField = findElement([
        '#category', '#moduleCategory', 
        'select[name="category"]', 'input[name="category"]'
    ]);
    if (catField) data.category = catField.value;
    
    console.log('📋 Collected form data:', data);
    return data;
}

// Component actions
async function addNewComponent(type) {
    console.log('➕ Adding new component:', type);
    
    if (!currentModuleId) {
        showError('Save module first before adding components');
        return;
    }
    
    try {
        const componentData = {
            module_id: currentModuleId,
            component_name: `New ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            component_type: type,
            component_config: JSON.stringify(getDefaultComponentConfig(type)),
            position_x: moduleComponents.length * 220,
            position_y: Math.floor(moduleComponents.length / 3) * 250,
            width: 200,
            height: 150
        };
        
        const response = await apiCall('add_component', componentData);
        
        if (response.success) {
            showSuccess('Component added successfully!');
            
            // Component'ı local array'e ekle
            const newComponent = {
                id: response.component_id,
                ...componentData
            };
            moduleComponents.push(newComponent);
            
            // Canvas'ı yeniden çiz
            renderComponentsOnCanvas(moduleComponents);
        } else {
            showError('Failed to add component: ' + response.error);
        }
        
    } catch (error) {
        console.error('💥 Add component error:', error);
        showError('Add component error: ' + error.message);
    }
}

// Default component config
function getDefaultComponentConfig(type) {
    switch (type) {
        case 'form':
            return {
                fields: [
                    { name: 'title', type: 'text', label: 'Title', required: true },
                    { name: 'description', type: 'textarea', label: 'Description' }
                ]
            };
        case 'checklist':
            return {
                items: [
                    { text: 'Item 1', completed: false },
                    { text: 'Item 2', completed: false }
                ]
            };
        case 'dashboard':
            return {
                widgets: [
                    { type: 'counter', title: 'Total Items', value: 0 }
                ]
            };
        case 'table':
            return {
                columns: ['Column 1', 'Column 2'],
                data: []
            };
        default:
            return {};
    }
}

async function editComponent(index) {
    const component = moduleComponents[index];
    if (!component) return;
    
    console.log('✏️ Editing component:', component);
    
    // Simple edit dialog
    const newName = prompt('Enter component name:', component.component_name);
    if (newName && newName !== component.component_name) {
        try {
            const response = await apiCall('update_component', {
                component_id: component.id,
                component_name: newName,
                component_config: component.component_config,
                position_x: component.position_x,
                position_y: component.position_y,
                width: component.width,
                height: component.height
            });
            
            if (response.success) {
                component.component_name = newName;
                moduleComponents[index] = component;
                renderComponentsOnCanvas(moduleComponents);
                showSuccess('Component updated!');
            } else {
                showError('Update failed: ' + response.error);
            }
        } catch (error) {
            showError('Update error: ' + error.message);
        }
    }
}

async function deleteComponent(index) {
    if (!confirm('Are you sure you want to delete this component?')) return;
    
    const component = moduleComponents[index];
    if (!component) return;
    
    console.log('🗑️ Deleting component:', component);
    
    try {
        const response = await apiCall('delete_component', {
            component_id: component.id
        });
        
        if (response.success) {
            // Array'den çıkar
            moduleComponents.splice(index, 1);
            
            // Canvas'ı yeniden çiz
            renderComponentsOnCanvas(moduleComponents);
            
            showSuccess('Component deleted!');
        } else {
            showError('Delete failed: ' + response.error);
        }
    } catch (error) {
        showError('Delete error: ' + error.message);
    }
}

// Component positions'ları kaydet
async function saveComponentPositions() {
    if (!currentModuleId) return;
    
    console.log('💾 Saving component positions...');
    
    try {
        const positions = moduleComponents.map(component => ({
            component_id: component.id,
            x: component.position_x,
            y: component.position_y
        }));
        
        const response = await apiCall('update_component_positions', { positions });
        
        if (response.success) {
            showSuccess('Positions saved!');
            componentPositionChanged = false;
        } else {
            showError('Save positions failed: ' + response.error);
        }
        
    } catch (error) {
        console.error('💥 Save positions error:', error);
        showError('Save positions error: ' + error.message);
    }
}

// Drag functionality
function makeDraggable(element) {
    let isDragging = false;
    let startX, startY, startLeft, startTop;
    
    element.addEventListener('mousedown', (e) => {
        if (e.target.tagName === 'BUTTON') return;
        
        isDragging = true;
        isDraggingComponent = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = parseInt(element.style.left);
        startTop = parseInt(element.style.top);
        
        element.style.zIndex = '1000';
        element.style.opacity = '0.8';
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        
        e.preventDefault();
    });
    
    function onMouseMove(e) {
        if (!isDragging) return;
        
        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;
        
        element.style.left = (startLeft + deltaX) + 'px';
        element.style.top = (startTop + deltaY) + 'px';
    }
    
    function onMouseUp() {
        if (!isDragging) return;
        
        isDragging = false;
        isDraggingComponent = false;
        
        element.style.zIndex = '';
        element.style.opacity = '';
        
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        
        // Position'ı kaydet
        saveComponentPosition(element);
    }
}

function saveComponentPosition(element) {
    const componentId = parseInt(element.getAttribute('data-component-id'));
    const index = parseInt(element.getAttribute('data-index'));
    const newX = parseInt(element.style.left);
    const newY = parseInt(element.style.top);
    
    console.log(`💾 Updating position for component ${componentId}: ${newX}, ${newY}`);
    
    // Component array'de güncelle
    if (moduleComponents[index] && moduleComponents[index].id == componentId) {
        moduleComponents[index].position_x = newX;
        moduleComponents[index].position_y = newY;
        componentPositionChanged = true;
    }
}

// UI Helper fonksiyonları
function showLoadingState() {
    let loadingDiv = document.getElementById('loading');
    if (!loadingDiv) {
        loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading';
        loadingDiv.style.position = 'fixed';
        loadingDiv.style.top = '50%';
        loadingDiv.style.left = '50%';
        loadingDiv.style.transform = 'translate(-50%, -50%)';
        loadingDiv.style.background = 'rgba(0,0,0,0.8)';
        loadingDiv.style.color = 'white';
        loadingDiv.style.padding = '20px';
        loadingDiv.style.borderRadius = '5px';
        loadingDiv.style.zIndex = '9999';
        loadingDiv.innerHTML = '🔄 Loading...';
        document.body.appendChild(loadingDiv);
    }
    loadingDiv.style.display = 'block';
}

function hideLoadingState() {
    const loadingDiv = document.getElementById('loading');
    if (loadingDiv) loadingDiv.style.display = 'none';
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '5px';
    notification.style.zIndex = '9999';
    notification.style.color = 'white';
    notification.style.fontSize = '14px';
    notification.style.fontWeight = 'bold';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
    notification.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
    
    // Click to remove
    notification.addEventListener('click', () => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    });
}

// Test connection
async function testConnection() {
    try {
        const response = await apiCall('test_connection');
        if (response.success) {
            showSuccess('Connection test successful!');
            console.log('✅ Connection test:', response);
        } else {
            showError('Connection test failed');
        }
    } catch (error) {
        showError('Connection error: ' + error.message);
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded, initializing module builder...');
    
    // Kısa bir gecikme ile başlat (DOM tam yüklensin diye)
    setTimeout(() => {
        initializeModuleEdit();
    }, 100);
});

console.log('🔧 Fixed Module Builder JavaScript loaded!');

</script>