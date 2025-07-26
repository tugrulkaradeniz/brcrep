<?php
// ===== DUPLICATE KEY SORUNU ÇÖZÜMLÜ VERSİYON =====
// platform/ajax/module-builder.php - Son versiyonu

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
                if (empty($module['name']) && !empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
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
            
            // Name düzeltmesi
            if (empty($module['name']) && !empty($module['module_name'])) {
                $module['name'] = $module['module_name'];
            }
            
            // Components'ları al
            $stmt = $pdo->prepare('SELECT * FROM module_components WHERE module_id = ?');
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
            echo json_encode(['error' => 'Unknown action: ' . $action, 'available_actions' => ['save', 'create_module', 'get_modules', 'preview', 'test_connection', 'debug']]);
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

    // ===== ADMIN PANEL JAVASCRIPT FIX =====
// Bu kodu admin panel'in JavaScript bölümüne ekleyin
// Dosya: platform/pages/module-builder.php (JavaScript section)

// Module edit için gerekli fonksiyonlar
let currentModuleId = null;
let moduleComponents = [];
let moduleData = {};

// Sayfa yüklendiğinde URL'den module ID'yi al
function initializeModuleEdit() {
    console.log('🚀 Module Builder initialized');
    
    // URL'den module ID'yi çıkar
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');
    
    if (moduleId) {
        console.log('📖 Edit mode detected, Module ID:', moduleId);
        currentModuleId = moduleId;
        loadModuleForEdit(moduleId);
    } else {
        console.log('➕ Create mode detected');
        setCreateMode();
    }
}

// Modülü edit için yükle
async function loadModuleForEdit(moduleId) {
    console.log('📡 Loading module for edit:', moduleId);
    
    try {
        showLoadingState();
        
        const response = await fetch('ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'get_module_details',
                module_id: moduleId
            })
        });
        
        const result = await response.json();
        console.log('📥 Module data received:', result);
        
        if (result.success && result.module) {
            moduleData = result.module;
            moduleComponents = result.components || [];
            
            // Form field'larını doldur
            populateFormFields(result.module);
            
            // Components'ları canvas'a çiz
            renderComponentsOnCanvas(result.components || []);
            
            // UI'yi edit mode'a ayarla
            setEditMode(result.module);
            
            console.log('✅ Module loaded successfully');
        } else {
            console.error('❌ Failed to load module:', result.error);
            showError('Module yüklenemedi: ' + (result.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Exception loading module:', error);
        showError('Module yükleme hatası: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

// Form field'larını doldur
function populateFormFields(module) {
    console.log('📝 Populating form fields:', module);
    
    try {
        // Module name field'ları
        const moduleNameField = document.getElementById('module_name') || 
                               document.getElementById('moduleName') || 
                               document.querySelector('input[name="module_name"]') ||
                               document.querySelector('input[name="name"]');
        
        if (moduleNameField) {
            const moduleName = module.name || module.module_name || '';
            moduleNameField.value = moduleName;
            console.log('✅ Module name set:', moduleName);
        } else {
            console.warn('⚠️ Module name field not found');
        }
        
        // Description field
        const descriptionField = document.getElementById('description') || 
                                document.getElementById('moduleDescription') ||
                                document.querySelector('textarea[name="description"]');
        
        if (descriptionField) {
            descriptionField.value = module.description || '';
            console.log('✅ Description set');
        } else {
            console.warn('⚠️ Description field not found');
        }
        
        // Category field
        const categoryField = document.getElementById('category') || 
                             document.getElementById('moduleCategory') ||
                             document.querySelector('select[name="category"]');
        
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

// Components'ları canvas'a çiz
function renderComponentsOnCanvas(components) {
    console.log('🎨 Rendering components on canvas:', components);
    
    try {
        // Canvas elementi bul
        const canvas = document.getElementById('module-canvas') || 
                      document.getElementById('designer-canvas') ||
                      document.querySelector('.designer-canvas') ||
                      document.querySelector('.canvas');
        
        if (!canvas) {
            console.warn('⚠️ Canvas element not found');
            // Fallback: Liste halinde göster
            renderComponentsList(components);
            return;
        }
        
        // Canvas'ı temizle
        canvas.innerHTML = '';
        
        if (components.length === 0) {
            canvas.innerHTML = '<div class="empty-canvas">No components yet. Drag components here.</div>';
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
    element.style.left = (component.position_x || 0) + 'px';
    element.style.top = (component.position_y || 0) + 'px';
    element.style.width = (component.width || 200) + 'px';
    element.style.height = (component.height || 150) + 'px';
    element.style.border = '2px solid #007bff';
    element.style.borderRadius = '5px';
    element.style.backgroundColor = '#f8f9fa';
    element.style.padding = '10px';
    element.style.cursor = 'move';
    
    // Component içeriği
    element.innerHTML = `
        <div class="component-header">
            <strong>${component.component_name || 'Unnamed Component'}</strong>
            <span class="component-type">${component.component_type || 'unknown'}</span>
        </div>
        <div class="component-content">
            ${getComponentPreview(component)}
        </div>
        <div class="component-actions">
            <button onclick="editComponent(${index})" class="btn-sm">Edit</button>
            <button onclick="deleteComponent(${index})" class="btn-sm btn-danger">Delete</button>
        </div>
    `;
    
    // Drag functionality ekle
    makeDraggable(element);
    
    return element;
}

// Component preview içeriği
function getComponentPreview(component) {
    const config = component.component_config ? JSON.parse(component.component_config) : {};
    
    switch (component.component_type) {
        case 'form':
            const fieldCount = config.fields ? config.fields.length : 0;
            return `📝 Form (${fieldCount} fields)`;
        case 'checklist':
            const itemCount = config.items ? config.items.length : 0;
            return `✅ Checklist (${itemCount} items)`;
        case 'dashboard':
            const widgetCount = config.widgets ? config.widgets.length : 0;
            return `📊 Dashboard (${widgetCount} widgets)`;
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
        
        // Canvas'tan sonra ekle
        const form = document.querySelector('form') || document.body;
        form.appendChild(listContainer);
    }
    
    if (components.length === 0) {
        listContainer.innerHTML = '<p>No components found.</p>';
        return;
    }
    
    let html = '<h4>Module Components:</h4><ul>';
    components.forEach((component, index) => {
        html += `
        <li>
            <strong>${component.component_name}</strong> 
            (${component.component_type}) 
            - Position: ${component.position_x}, ${component.position_y}
            <button onclick="editComponent(${index})">Edit</button>
            <button onclick="deleteComponent(${index})">Delete</button>
        </li>`;
    });
    html += '</ul>';
    
    listContainer.innerHTML = html;
}

// Edit mode ayarları
function setEditMode(module) {
    console.log('⚙️ Setting edit mode UI');
    
    // Page title
    updatePageTitle(`Edit: ${module.name || module.module_name || 'Module'}`);
    
    // Save button'u güncelle
    const saveBtn = document.getElementById('save-btn') || 
                   document.querySelector('button[type="submit"]') ||
                   document.querySelector('.btn-save');
    
    if (saveBtn) {
        saveBtn.textContent = 'Update Module';
        saveBtn.onclick = (e) => {
            e.preventDefault();
            updateModule();
        };
    }
    
    // Cancel/Back button ekle
    addCancelButton();
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
    
    const pageHeader = document.querySelector('h1') || 
                      document.querySelector('.page-title') ||
                      document.querySelector('h2');
    
    if (pageHeader) {
        pageHeader.textContent = title;
    }
}

// Cancel button ekle
function addCancelButton() {
    const saveBtn = document.querySelector('button[type="submit"]');
    if (saveBtn && !document.getElementById('cancel-btn')) {
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

// Module güncelle
async function updateModule() {
    console.log('💾 Updating module...');
    
    try {
        showLoadingState();
        
        const formData = collectFormData();
        
        const response = await fetch('ajax/module-builder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'update_module',
                module_id: currentModuleId,
                ...formData
            })
        });
        
        const result = await response.json();
        console.log('📥 Update response:', result);
        
        if (result.success) {
            showSuccess('Module updated successfully!');
            // Başarılı güncelleme sonrası
            setTimeout(() => {
                window.location.href = 'modules.php';
            }, 1500);
        } else {
            showError('Update failed: ' + (result.error || 'Unknown error'));
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
    
    // Name
    const nameField = document.getElementById('module_name') || 
                     document.querySelector('input[name="module_name"]') ||
                     document.querySelector('input[name="name"]');
    if (nameField) data.name = nameField.value;
    
    // Description
    const descField = document.getElementById('description') || 
                     document.querySelector('textarea[name="description"]');
    if (descField) data.description = descField.value;
    
    // Category
    const catField = document.getElementById('category') || 
                    document.querySelector('select[name="category"]');
    if (catField) data.category = catField.value;
    
    console.log('📋 Collected form data:', data);
    return data;
}

// UI Helper fonksiyonları
function showLoadingState() {
    const loadingDiv = document.getElementById('loading') || createLoadingDiv();
    loadingDiv.style.display = 'block';
}

function hideLoadingState() {
    const loadingDiv = document.getElementById('loading');
    if (loadingDiv) loadingDiv.style.display = 'none';
}

function createLoadingDiv() {
    const loading = document.createElement('div');
    loading.id = 'loading';
    loading.innerHTML = '<div style="text-align: center; padding: 20px;">🔄 Loading...</div>';
    loading.style.display = 'none';
    document.body.appendChild(loading);
    return loading;
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    // Simple notification
    const notification = document.createElement('div');
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px';
    notification.style.borderRadius = '5px';
    notification.style.zIndex = '9999';
    notification.style.color = 'white';
    notification.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 3000);
}

// Drag functionality
function makeDraggable(element) {
    let isDragging = false;
    let startX, startY, startLeft, startTop;
    
    element.addEventListener('mousedown', (e) => {
        if (e.target.tagName === 'BUTTON') return;
        
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = parseInt(element.style.left);
        startTop = parseInt(element.style.top);
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
    
    function onMouseMove(e) {
        if (!isDragging) return;
        
        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;
        
        element.style.left = (startLeft + deltaX) + 'px';
        element.style.top = (startTop + deltaY) + 'px';
    }
    
    function onMouseUp() {
        isDragging = false;
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        
        // Position'ı kaydet
        saveComponentPosition(element);
    }
}

function saveComponentPosition(element) {
    const componentId = element.getAttribute('data-component-id');
    const newX = parseInt(element.style.left);
    const newY = parseInt(element.style.top);
    
    console.log(`💾 Saving position for component ${componentId}: ${newX}, ${newY}`);
    
    // Component array'de güncelle
    const index = element.getAttribute('data-index');
    if (moduleComponents[index]) {
        moduleComponents[index].position_x = newX;
        moduleComponents[index].position_y = newY;
    }
}

// Component actions
function editComponent(index) {
    const component = moduleComponents[index];
    if (component) {
        console.log('✏️ Editing component:', component);
        // Component edit modal/form açılacak
        alert(`Editing component: ${component.component_name}\n(Edit functionality will be implemented)`);
    }
}

function deleteComponent(index) {
    if (confirm('Are you sure you want to delete this component?')) {
        const component = moduleComponents[index];
        console.log('🗑️ Deleting component:', component);
        
        // Array'den çıkar
        moduleComponents.splice(index, 1);
        
        // Canvas'ı yeniden çiz
        renderComponentsOnCanvas(moduleComponents);
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded, initializing module edit...');
    initializeModuleEdit();
});

// Form submit'i override et
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (currentModuleId) {
                updateModule();
            } else {
                // Create new module (existing functionality)
                form.submit();
            }
        });
    }
});

console.log('🔧 Admin Panel JavaScript Fix loaded!');

</script>