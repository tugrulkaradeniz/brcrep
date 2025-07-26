<?php
// ===== CUSTOMER DATA ACTIONS ENDPOINT - SESSION UYUMLU =====
// Dosya: customer/ajax/data-actions.php

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Database
try {
    require_once '../../dbConnect/dbkonfigur.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? '';

// Authentication check - esnek kontrol
$is_authenticated = (
    isset($_SESSION['user_id']) || 
    isset($_SESSION['admin_id']) || 
    isset($_SESSION['platform_admin_id']) ||
    $action === 'test_save'
);

if (!$is_authenticated) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required - Please login first',
        'session_info' => [
            'user_id' => $_SESSION['user_id'] ?? 'not_set',
            'admin_id' => $_SESSION['admin_id'] ?? 'not_set',
            'platform_admin_id' => $_SESSION['platform_admin_id'] ?? 'not_set'
        ]
    ]);
    exit();
}

// Error logging
function logDataError($message, $context = []) {
    $logDir = '../../logs/';
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $log = date('Y-m-d H:i:s') . ' - DATA-ACTIONS: ' . $message . ' - ' . json_encode($context) . "\n";
    @file_put_contents($logDir . 'data_actions.log', $log, FILE_APPEND);
}

try {
    switch ($action) {
        case 'test_save':
            echo json_encode([
                'success' => true,
                'message' => 'Data actions endpoint working perfectly',
                'timestamp' => date('Y-m-d H:i:s'),
                'received_data' => $input,
                'session_info' => [
                    'user_id' => $_SESSION['user_id'] ?? 'not_set',
                    'admin_id' => $_SESSION['admin_id'] ?? 'not_set',
                    'company_id' => $_SESSION['company_id'] ?? 'not_set',
                    'platform_admin_id' => $_SESSION['platform_admin_id'] ?? 'not_set'
                ]
            ]);
            break;
            
        case 'save_quality_control':
            $company_id = $_SESSION['company_id'] ?? 1;
            $process_step = $input['process_step'] ?? '';
            $control_input = $input['control_input'] ?? '';
            $frequency = $input['frequency'] ?? '';
            $criteria = $input['criteria'] ?? '';
            $responsible_person = $input['responsible_person'] ?? '';
            $corrective_action = $input['corrective_action'] ?? '';
            
            if (empty($process_step)) {
                echo json_encode(['error' => 'Process step required']);
                exit();
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO quality_control_plans 
                (company_id, process_step, control_input, frequency, criteria, responsible_person, corrective_action, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            
            $result = $stmt->execute([
                $company_id, $process_step, $control_input, $frequency, $criteria, $responsible_person, $corrective_action
            ]);
            
            if ($result) {
                logDataError('Quality control plan saved', ['company_id' => $company_id, 'process_step' => $process_step]);
                echo json_encode([
                    'success' => true,
                    'message' => 'Quality control plan saved successfully',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(['error' => 'Failed to save quality control plan']);
            }
            break;
            
        case 'save_traceability':
            $company_id = $_SESSION['company_id'] ?? 1;
            $lot_number = $input['lot_number'] ?? '';
            $raw_material_lot = $input['raw_material_lot'] ?? '';
            $raw_material_weight = $input['raw_material_weight'] ?? 0;
            
            if (empty($lot_number)) {
                echo json_encode(['error' => 'Lot number required']);
                exit();
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO lot_traceability 
                (company_id, lot_number, raw_material_lot, raw_material_weight, raw_material_date, created_at) 
                VALUES (?, ?, ?, ?, CURDATE(), NOW())
            ');
            
            $result = $stmt->execute([$company_id, $lot_number, $raw_material_lot, $raw_material_weight]);
            
            if ($result) {
                logDataError('Traceability record saved', ['company_id' => $company_id, 'lot_number' => $lot_number]);
                echo json_encode([
                    'success' => true,
                    'message' => 'Traceability record saved successfully',
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(['error' => 'Failed to save traceability record']);
            }
            break;
            
        case 'get_quality_controls':
            $company_id = $_SESSION['company_id'] ?? 1;
            
            $stmt = $pdo->prepare('
                SELECT * FROM quality_control_plans 
                WHERE company_id = ? 
                ORDER BY created_at DESC
            ');
            $stmt->execute([$company_id]);
            $controls = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'controls' => $controls,
                'count' => count($controls)
            ]);
            break;
            
        case 'get_traceability':
            $company_id = $_SESSION['company_id'] ?? 1;
            
            $stmt = $pdo->prepare('
                SELECT * FROM lot_traceability 
                WHERE company_id = ? 
                ORDER BY created_at DESC
            ');
            $stmt->execute([$company_id]);
            $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'lots' => $lots,
                'count' => count($lots)
            ]);
            break;
            
        case 'save_analysis_result':
            $company_id = $_SESSION['company_id'] ?? 1;
            $lot_number = $input['lot_number'] ?? '';
            $analysis_type = $input['analysis_type'] ?? '';
            $results = $input['results'] ?? [];
            $compliance_status = $input['compliance_status'] ?? 'PENDING';
            $notes = $input['notes'] ?? '';
            
            if (empty($lot_number) || empty($analysis_type)) {
                echo json_encode(['error' => 'Lot number and analysis type required']);
                exit();
            }
            
            // Eğer analysis_results tablosu varsa kaydet
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO analysis_results 
                    (company_id, lot_number, analysis_type, results, compliance_status, notes, test_date, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, CURDATE(), NOW())
                ');
                
                $result = $stmt->execute([
                    $company_id, 
                    $lot_number, 
                    $analysis_type, 
                    json_encode($results), 
                    $compliance_status, 
                    $notes
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Analysis result saved successfully',
                        'id' => $pdo->lastInsertId()
                    ]);
                } else {
                    echo json_encode(['error' => 'Failed to save analysis result']);
                }
                
            } catch (Exception $e) {
                // Tablo yoksa oluştur
                if (strpos($e->getMessage(), "doesn't exist") !== false) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Analysis results table not found. Please run database setup.'
                    ]);
                } else {
                    throw $e;
                }
            }
            break;
            
        case 'get_dashboard_data':
            $company_id = $_SESSION['company_id'] ?? 1;
            
            $dashboard_data = [];
            
            // Quality control plans count
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM quality_control_plans WHERE company_id = ?');
                $stmt->execute([$company_id]);
                $dashboard_data['quality_controls'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                $dashboard_data['quality_controls'] = 0;
            }
            
            // Traceability lots count
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM lot_traceability WHERE company_id = ?');
                $stmt->execute([$company_id]);
                $dashboard_data['lots'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                $dashboard_data['lots'] = 0;
            }
            
            // Active modules count
            try {
                $stmt = $pdo->prepare('
                    SELECT COUNT(*) as count 
                    FROM company_module_subscriptions s
                    INNER JOIN marketplace_modules m ON s.module_id = m.id
                    WHERE s.company_id = ? AND s.status = ?
                ');
                $stmt->execute([$company_id, 'active']);
                $dashboard_data['active_modules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                $dashboard_data['active_modules'] = 0;
            }
            
            echo json_encode([
                'success' => true,
                'dashboard_data' => $dashboard_data,
                'company_id' => $company_id
            ]);
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action: ' . $action]);
            break;
    }
    
} catch (Exception $e) {
    logDataError('Exception in data-actions.php', [
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
?>