<?php

session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../models/Module.php';
require_once '../../services/CompanyContext.php';

// Customer authentication check
if (!isset($_SESSION['company_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['company_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$moduleModel = new Module($baglanti);

switch ($action) {
    case 'subscribe':
        try {
            $moduleId = $_POST['module_id'];
            if (empty($moduleId)) {
                throw new Exception('Module ID is required');
            }
            
            // Check if already subscribed
            $stmt = $baglanti->prepare("
                SELECT id FROM company_module_subscriptions 
                WHERE company_id = ? AND module_id = ? AND status = 'active'
            ");
            $stmt->execute([$companyId, $moduleId]);
            
            if ($stmt->fetch()) {
                throw new Exception('Already subscribed to this module');
            }
            
            // Get module details
            $module = $moduleModel->getById($moduleId);
            if (!$module) {
                throw new Exception('Module not found');
            }
            
            // Create subscription
            $stmt = $baglanti->prepare("
                INSERT INTO company_module_subscriptions (company_id, module_id, expires_at, status)
                VALUES (?, ?, DATE_ADD(CURRENT_DATE, INTERVAL 1 MONTH), 'active')
            ");
            
            if ($stmt->execute([$companyId, $moduleId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully subscribed to module',
                    'module_name' => $module['module_name']
                ]);
            } else {
                throw new Exception('Failed to create subscription');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'unsubscribe':
        try {
            $moduleId = $_POST['module_id'];
            if (empty($moduleId)) {
                throw new Exception('Module ID is required');
            }
            
            $stmt = $baglanti->prepare("
                UPDATE company_module_subscriptions 
                SET status = 'cancelled', updated_at = NOW()
                WHERE company_id = ? AND module_id = ? AND status = 'active'
            ");
            
            if ($stmt->execute([$companyId, $moduleId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully unsubscribed from module'
                ]);
            } else {
                throw new Exception('Failed to unsubscribe');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'my_modules':
        try {
            $modules = $moduleModel->getByCompany($companyId);
            echo json_encode([
                'success' => true,
                'data' => $modules
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'marketplace':
        try {
            $modules = $moduleModel->getPublished();
            
            // Mark which modules are already subscribed
            foreach ($modules as &$module) {
                $stmt = $baglanti->prepare("
                    SELECT id FROM company_module_subscriptions 
                    WHERE company_id = ? AND module_id = ? AND status = 'active'
                ");
                $stmt->execute([$companyId, $module['id']]);
                $module['is_subscribed'] = $stmt->fetch() !== false;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $modules
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
}

?>