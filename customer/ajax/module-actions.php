<?php
// Customer Module Subscription AJAX Handler
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['company_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$moduleId = intval($input['module_id'] ?? 0);
$companyId = $_SESSION['company_id'];
$userId = $_SESSION['user_id'];

if (!$action || !$moduleId) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=brcload_platform;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    if ($action === 'subscribe') {
        // Check if already subscribed
        $checkStmt = $pdo->prepare("
            SELECT id FROM company_module_subscriptions 
            WHERE company_id = ? AND module_id = ?
        ");
        $checkStmt->execute([$companyId, $moduleId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Already subscribed to this module']);
            exit;
        }
        
        // Get module info
        $moduleStmt = $pdo->prepare("SELECT module_name, price FROM marketplace_modules WHERE id = ?");
        $moduleStmt->execute([$moduleId]);
        $module = $moduleStmt->fetch();
        
        if (!$module) {
            echo json_encode(['success' => false, 'message' => 'Module not found']);
            exit;
        }
        
        // Insert subscription
        $insertStmt = $pdo->prepare("
            INSERT INTO company_module_subscriptions 
            (company_id, module_id, subscribed_at, expires_at, status, created_at, updated_at)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'active', NOW(), NOW())
        ");
        
        $result = $insertStmt->execute([$companyId, $moduleId]);
        
        if ($result) {
            // Log activity
            error_log("User {$userId} subscribed company {$companyId} to module {$moduleId}: {$module['module_name']}");
            
            echo json_encode([
                'success' => true, 
                'message' => "Successfully subscribed to {$module['module_name']}",
                'module_name' => $module['module_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to subscribe']);
        }
        
    } elseif ($action === 'unsubscribe') {
        // Remove subscription
        $deleteStmt = $pdo->prepare("
            DELETE FROM company_module_subscriptions 
            WHERE company_id = ? AND module_id = ?
        ");
        
        $result = $deleteStmt->execute([$companyId, $moduleId]);
        
        if ($result && $deleteStmt->rowCount() > 0) {
            // Get module name for logging
            $moduleStmt = $pdo->prepare("SELECT module_name FROM marketplace_modules WHERE id = ?");
            $moduleStmt->execute([$moduleId]);
            $module = $moduleStmt->fetch();
            
            error_log("User {$userId} unsubscribed company {$companyId} from module {$moduleId}: {$module['module_name']}");
            
            echo json_encode([
                'success' => true, 
                'message' => "Successfully unsubscribed from {$module['module_name']}",
                'module_name' => $module['module_name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Subscription not found or already removed']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in module-actions.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>