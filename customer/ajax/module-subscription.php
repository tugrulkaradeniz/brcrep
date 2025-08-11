<?php
/**
 * Customer Module Subscription & Management API - FIXED VERSION
 * Path: customer/ajax/module-subscription.php
 */

// Buffer temizle
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Session kontrolü
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database bağlantısı - Multiple path support
$db_paths = [
    '../../dbConnect/dbkonfigur.php',
    '../../../dbConnect/dbkonfigur.php',
    '../dbConnect/dbkonfigur.php'
];

$pdo = null;
$db_connected = false;

foreach ($db_paths as $path) {
    if (file_exists($path)) {
        try {
            require_once $path;
            
            // PDO bağlantısı oluştur (dbkonfigur.php'den gelen değişkenlerle)
            if (isset($host, $dbname, $username, $password)) {
                $pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                    $username, 
                    $password
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db_connected = true;
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

if (!$db_connected) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Authentication check
if (!isset($_SESSION['company_user_id']) || !isset($_SESSION['company_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Please login first']);
    exit();
}

$userId = $_SESSION['company_user_id'];
$companyId = $_SESSION['company_id'];

// Input handling
$input_json = json_decode(file_get_contents('php://input'), true);
$input = !empty($input_json) ? $input_json : array_merge($_POST, $_GET);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        
        case 'get_marketplace':
            // Marketplace'teki modülleri getir
            $category = $input['category'] ?? '';
            $search = $input['search'] ?? '';
            
            $query = "
                SELECT m.*, 
                       cms.id as subscription_id,
                       cms.status as subscription_status,
                       cms.subscription_type,
                       cms.expires_at,
                       (SELECT COUNT(*) FROM company_module_subscriptions WHERE module_id = m.id) as total_users
                FROM marketplace_modules m
                LEFT JOIN company_module_subscriptions cms ON m.id = cms.module_id AND cms.company_id = ?
                WHERE m.status = 'published'
            ";
            
            $params = [$companyId];
            
            if ($category && $category !== 'all') {
                $query .= " AND m.category = ?";
                $params[] = $category;
            }
            
            if ($search) {
                $query .= " AND (m.name LIKE ? OR m.module_name LIKE ? OR m.description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            $query .= " ORDER BY m.is_featured DESC, m.created_at DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fix module names
            foreach ($modules as &$module) {
                if (empty($module['name']) && !empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
                }
                // Parse price if needed
                $module['price'] = floatval($module['price'] ?? 0);
                $module['is_subscribed'] = !empty($module['subscription_id']);
            }
            
            echo json_encode([
                'success' => true,
                'modules' => $modules,
                'total' => count($modules)
            ]);
            break;
            
        case 'subscribe':
            // Modüle abone ol
            $moduleId = intval($input['module_id'] ?? 0);
            $subscriptionType = $input['subscription_type'] ?? 'monthly';
            
            if (!$moduleId) {
                echo json_encode(['error' => 'Invalid module']);
                exit();
            }
            
            // Modül bilgilerini al
            $stmt = $pdo->prepare("
                SELECT * FROM marketplace_modules 
                WHERE id = ? AND status = 'published'
            ");
            $stmt->execute([$moduleId]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$module) {
                echo json_encode(['error' => 'Module not found']);
                exit();
            }
            
            // Zaten abone olunmuş mu kontrol et
            $stmt = $pdo->prepare("
                SELECT id FROM company_module_subscriptions 
                WHERE company_id = ? AND module_id = ?
            ");
            $stmt->execute([$companyId, $moduleId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['error' => 'Already subscribed to this module']);
                exit();
            }
            
            // Expiry date hesapla
            $expiresAt = null;
            $isFree = ($module['price'] == 0 || $module['is_base_module'] == 1);
            
            if (!$isFree) {
                if ($subscriptionType === 'monthly') {
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 month'));
                } elseif ($subscriptionType === 'yearly') {
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 year'));
                }
            }
            
            // Subscription oluştur
            $stmt = $pdo->prepare("
                INSERT INTO company_module_subscriptions 
                (company_id, module_id, subscription_type, status, expires_at) 
                VALUES (?, ?, ?, 'active', ?)
            ");
            $stmt->execute([
                $companyId, 
                $moduleId, 
                $isFree ? 'free' : $subscriptionType,
                $expiresAt
            ]);
            
            $moduleName = $module['name'] ?? $module['module_name'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully subscribed to ' . $moduleName,
                'module_id' => $moduleId,
                'redirect' => '/customer/modules/view/' . $moduleId
            ]);
            break;
            
        case 'unsubscribe':
            // Abonelikten çık
            $moduleId = intval($input['module_id'] ?? 0);
            
            if (!$moduleId) {
                echo json_encode(['error' => 'Invalid module']);
                exit();
            }
            
            // Abonelik kontrolü
            $stmt = $pdo->prepare("
                SELECT cms.*, m.name, m.module_name 
                FROM company_module_subscriptions cms
                JOIN marketplace_modules m ON cms.module_id = m.id
                WHERE cms.company_id = ? AND cms.module_id = ?
            ");
            $stmt->execute([$companyId, $moduleId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subscription) {
                echo json_encode(['error' => 'Not subscribed to this module']);
                exit();
            }
            
            // Status'u cancelled yap
            $stmt = $pdo->prepare("
                UPDATE company_module_subscriptions 
                SET status = 'cancelled', updated_at = NOW() 
                WHERE company_id = ? AND module_id = ?
            ");
            $stmt->execute([$companyId, $moduleId]);
            
            $moduleName = $subscription['name'] ?? $subscription['module_name'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Unsubscribed from ' . $moduleName
            ]);
            break;
            
        case 'get_subscribed':
            // Abone olunan modülleri getir
            $stmt = $pdo->prepare("
                SELECT m.*, cms.status as subscription_status, 
                       cms.subscription_type, cms.expires_at, cms.activated_at
                FROM company_module_subscriptions cms
                JOIN marketplace_modules m ON cms.module_id = m.id
                WHERE cms.company_id = ? AND cms.status = 'active'
                ORDER BY cms.activated_at DESC
            ");
            $stmt->execute([$companyId]);
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fix module names
            foreach ($modules as &$module) {
                if (empty($module['name']) && !empty($module['module_name'])) {
                    $module['name'] = $module['module_name'];
                }
            }
            
            echo json_encode([
                'success' => true,
                'modules' => $modules,
                'count' => count($modules)
            ]);
            break;
            
        case 'load_module':
            // Modülü kullanım için yükle
            $moduleId = intval($input['module_id'] ?? 0);
            
            if (!$moduleId) {
                echo json_encode(['error' => 'Invalid module']);
                exit();
            }
            
            // Abonelik kontrolü
            $stmt = $pdo->prepare("
                SELECT cms.*, m.name, m.module_name, m.module_code 
                FROM company_module_subscriptions cms
                JOIN marketplace_modules m ON cms.module_id = m.id
                WHERE cms.company_id = ? AND cms.module_id = ? AND cms.status = 'active'
            ");
            $stmt->execute([$companyId, $moduleId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subscription) {
                echo json_encode(['error' => 'Module not available']);
                exit();
            }
            
            // Module components'leri al
            $stmt = $pdo->prepare("
                SELECT * FROM module_components 
                WHERE module_id = ? 
                ORDER BY created_at ASC
            ");
            $stmt->execute([$moduleId]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($components as &$component) {
                $component['config'] = json_decode($component['component_config'] ?? '{}', true);
                $component['properties'] = json_decode($component['properties'] ?? '{}', true);
            }
            
            // Saved data'yı al
            $stmt = $pdo->prepare("
                SELECT * FROM module_data 
                WHERE company_id = ? AND module_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $stmt->execute([$companyId, $moduleId]);
            $savedData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $moduleName = $subscription['name'] ?? $subscription['module_name'];
            
            echo json_encode([
                'success' => true,
                'module' => [
                    'id' => $moduleId,
                    'name' => $moduleName,
                    'code' => $subscription['module_code']
                ],
                'components' => $components,
                'savedData' => $savedData,
                'subscription' => $subscription
            ]);
            break;
            
        case 'save_data':
            // Modül verisini kaydet
            $moduleId = intval($input['module_id'] ?? 0);
            $componentId = $input['component_id'] ?? '';
            $dataType = $input['data_type'] ?? 'form_data';
            $data = $input['data'] ?? [];
            
            if (!$moduleId) {
                echo json_encode(['error' => 'Invalid module']);
                exit();
            }
            
            // Abonelik kontrolü
            $stmt = $pdo->prepare("
                SELECT id FROM company_module_subscriptions 
                WHERE company_id = ? AND module_id = ? AND status = 'active'
            ");
            $stmt->execute([$companyId, $moduleId]);
            
            if (!$stmt->fetch()) {
                echo json_encode(['error' => 'Module not available']);
                exit();
            }
            
            // Veriyi kaydet
            $stmt = $pdo->prepare("
                INSERT INTO module_data 
                (company_id, module_id, component_id, data_type, data, status, created_by) 
                VALUES (?, ?, ?, ?, ?, 'draft', ?)
            ");
            $stmt->execute([
                $companyId,
                $moduleId,
                $componentId,
                $dataType,
                json_encode($data),
                $userId
            ]);
            
            $dataId = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Data saved successfully',
                'data_id' => $dataId
            ]);
            break;
            
        case 'get_stats':
            // Dashboard istatistikleri
            try {
                // Genel istatistikler
                $stmt = $pdo->prepare("
                    SELECT 
                        COUNT(DISTINCT module_id) as total_modules,
                        COUNT(DISTINCT CASE WHEN status = 'active' THEN module_id END) as active_modules,
                        COUNT(DISTINCT CASE WHEN subscription_type = 'free' THEN module_id END) as free_modules,
                        COUNT(DISTINCT CASE WHEN subscription_type != 'free' THEN module_id END) as paid_modules
                    FROM company_module_subscriptions
                    WHERE company_id = ?
                ");
                $stmt->execute([$companyId]);
                $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Recent activity
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as total_submissions,
                           COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                           COUNT(CASE WHEN status = 'submitted' THEN 1 END) as pending,
                           COUNT(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as this_week
                    FROM module_data
                    WHERE company_id = ?
                ");
                $stmt->execute([$companyId]);
                $activity = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stats = array_merge($stats, $activity);
                
                // Compliance score hesapla (örnek)
                $compliance_score = 94; // Bu gerçek verilerden hesaplanmalı
                $stats['compliance_score'] = $compliance_score;
                
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to get stats',
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'test_connection':
            echo json_encode([
                'success' => true,
                'message' => 'Module subscription API connected',
                'user_id' => $userId,
                'company_id' => $companyId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        default:
            echo json_encode([
                'error' => 'Invalid action',
                'available_actions' => [
                    'get_marketplace',
                    'subscribe',
                    'unsubscribe', 
                    'get_subscribed',
                    'load_module',
                    'save_data',
                    'get_stats',
                    'test_connection'
                ]
            ]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}

// Buffer flush
ob_end_flush();
?>