<?php

session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../services/CompanyContext.php';

// Customer authentication check
if (!isset($_SESSION['company_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['company_id'];
$userId = $_SESSION['company_user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'save_data':
        try {
            $moduleId = $_POST['module_id'];
            $dataType = $_POST['data_type'];
            $dataId = $_POST['data_id'];
            $dataContent = $_POST['data_content'];
            
            if (empty($moduleId) || empty($dataType) || empty($dataContent)) {
                throw new Exception('Missing required fields');
            }
            
            // Check if company has access to this module
            if (!CompanyContext::checkSubscription($moduleId)) {
                throw new Exception('No subscription to this module');
            }
            
            $stmt = $baglanti->prepare("
                INSERT INTO company_data (
                    company_id, module_id, data_type, data_id, data_content, created_by
                ) VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                data_content = VALUES(data_content),
                updated_at = NOW()
            ");
            
            if ($stmt->execute([$companyId, $moduleId, $dataType, $dataId, $dataContent, $userId])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Data saved successfully'
                ]);
            } else {
                throw new Exception('Failed to save data');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'get_data':
        try {
            $moduleId = $_GET['module_id'];
            $dataType = $_GET['data_type'];
            $dataId = $_GET['data_id'] ?? null;
            
            if (empty($moduleId) || empty($dataType)) {
                throw new Exception('Missing required parameters');
            }
            
            $sql = "
                SELECT cd.*, cu.username as created_by_name
                FROM company_data cd
                LEFT JOIN company_users cu ON cd.created_by = cu.id
                WHERE cd.company_id = ? AND cd.module_id = ? AND cd.data_type = ?
            ";
            $params = [$companyId, $moduleId, $dataType];
            
            if ($dataId) {
                $sql .= " AND cd.data_id = ?";
                $params[] = $dataId;
            }
            
            $sql .= " ORDER BY cd.created_at DESC";
            
            $stmt = $baglanti->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $data
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