<?php

session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../models/Module.php';

// Platform admin authentication check
if (!isset($_SESSION['platform_admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$moduleModel = new Module($baglanti);

switch ($action) {
    case 'save':
        try {
            $moduleData = json_decode($_POST['module_data'], true);
            if (!$moduleData) {
                throw new Exception('Invalid module data');
            }
            
            $moduleId = $_POST['module_id'] ?? null;
            
            if ($moduleId) {
                // Update existing module
                $updateData = [
                    'module_name' => $moduleData['name'],
                    'description' => $moduleData['description'] ?? '',
                    'category' => $moduleData['category'] ?? 'Custom',
                    'price' => floatval($moduleData['price'] ?? 0),
                    'status' => 'draft'
                ];
                
                $stmt = $baglanti->prepare("
                    UPDATE marketplace_modules 
                    SET module_name = ?, description = ?, category = ?, price = ?, status = ?, updated_at = NOW()
                    WHERE id = ? AND created_by = ?
                ");
                
                $stmt->execute([
                    $updateData['module_name'],
                    $updateData['description'],
                    $updateData['category'],
                    $updateData['price'],
                    $updateData['status'],
                    $moduleId,
                    $_SESSION['platform_admin_id']
                ]);
                
                // Update components
                updateModuleComponents($baglanti, $moduleId, $moduleData['components'] ?? []);
                
            } else {
                // Create new module
                $stmt = $baglanti->prepare("
                    INSERT INTO marketplace_modules (
                        module_name, module_code, description, category, price, 
                        is_base_module, status, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $moduleCode = generateModuleCode($moduleData['name']);
                
                $stmt->execute([
                    $moduleData['name'],
                    $moduleCode,
                    $moduleData['description'] ?? '',
                    $moduleData['category'] ?? 'Custom',
                    floatval($moduleData['price'] ?? 0),
                    $moduleData['is_base_module'] ?? false,
                    'draft',
                    $_SESSION['platform_admin_id']
                ]);
                
                $moduleId = $baglanti->lastInsertId();
                
                // Add components
                updateModuleComponents($baglanti, $moduleId, $moduleData['components'] ?? []);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Module saved successfully',
                'module_id' => $moduleId
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'publish':
        try {
            $moduleId = $_POST['module_id'];
            if (!$moduleId) {
                throw new Exception('Module ID is required');
            }
            
            // Validate module before publishing
            $module = $moduleModel->getById($moduleId);
            if (!$module) {
                throw new Exception('Module not found');
            }
            
            // Check if module has components
            $stmt = $baglanti->prepare("
                SELECT COUNT(*) as component_count 
                FROM module_components 
                WHERE module_id = ?
            ");
            $stmt->execute([$moduleId]);
            $componentCount = $stmt->fetch()['component_count'];
            
            if ($componentCount == 0) {
                throw new Exception('Module must have at least one component before publishing');
            }
            
            // Update status to published
            $stmt = $baglanti->prepare("
                UPDATE marketplace_modules 
                SET status = 'published', updated_at = NOW()
                WHERE id = ? AND created_by = ?
            ");
            
            if ($stmt->execute([$moduleId, $_SESSION['platform_admin_id']])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Module published successfully'
                ]);
            } else {
                throw new Exception('Failed to publish module');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'load':
        try {
            $moduleId = $_GET['module_id'];
            if (!$moduleId) {
                throw new Exception('Module ID is required');
            }
            
            $module = $moduleModel->getById($moduleId);
            if (!$module) {
                throw new Exception('Module not found');
            }
            
            // Get components
            $stmt = $baglanti->prepare("
                SELECT * FROM module_components 
                WHERE module_id = ? 
                ORDER BY order_index
            ");
            $stmt->execute([$moduleId]);
            $components = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'module' => $module,
                    'components' => $components
                ]
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

// Helper functions
function updateModuleComponents($db, $moduleId, $components) {
    // Delete existing components
    $stmt = $db->prepare("DELETE FROM module_components WHERE module_id = ?");
    $stmt->execute([$moduleId]);
    
    // Add new components
    if (!empty($components)) {
        $stmt = $db->prepare("
            INSERT INTO module_components (
                module_id, component_name, component_type, component_code, 
                config, order_index, is_locked
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($components as $index => $component) {
            $stmt->execute([
                $moduleId,
                $component['name'] ?? $component['type'],
                $component['type'],
                $component['id'],
                json_encode($component['properties'] ?? []),
                $index,
                $component['is_locked'] ?? false
            ]);
        }
    }
}

function generateModuleCode($name) {
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));
}

?>