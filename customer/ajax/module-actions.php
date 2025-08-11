<?php
// customer/ajax/module-actions.php - Customer Module Subscription Management

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Customer authentication check
if (!isset($_SESSION['company_user_id']) || !isset($_SESSION['company_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=brcload_platform;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        throw new Exception('Invalid request data');
    }

    $companyId = $_SESSION['company_id'];
    $userId = $_SESSION['company_user_id'];
    $action = $input['action'];

    switch ($action) {
        case 'subscribe':
            handleSubscription($pdo, $companyId, $input);
            break;
            
        case 'unsubscribe':
            handleUnsubscription($pdo, $companyId, $input);
            break;
            
        case 'get_subscriptions':
            getSubscriptions($pdo, $companyId);
            break;
            
        default:
            throw new Exception('Unknown action: ' . $action);
    }

} catch (Exception $e) {
    error_log("Customer Module Action Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function handleSubscription($pdo, $companyId, $input) {
    if (!isset($input['module_id'])) {
        throw new Exception('Module ID is required');
    }
    
    $moduleId = $input['module_id'];
    
    // Check if module exists and is published
    $moduleStmt = $pdo->prepare("
        SELECT * FROM marketplace_modules 
        WHERE id = ? AND status = 'published'
    ");
    $moduleStmt->execute([$moduleId]);
    $module = $moduleStmt->fetch();
    
    if (!$module) {
        throw new Exception('Module not found or not available');
    }
    
    // Check if already subscribed
    $existingStmt = $pdo->prepare("
        SELECT id FROM company_module_subscriptions 
        WHERE company_id = ? AND module_id = ?
    ");
    $existingStmt->execute([$companyId, $moduleId]);
    $existing = $existingStmt->fetch();
    
    if ($existing) {
        // Update existing subscription
        $updateStmt = $pdo->prepare("
            UPDATE company_module_subscriptions 
            SET status = 'active', subscribed_at = NOW()
            WHERE company_id = ? AND module_id = ?
        ");
        $updateStmt->execute([$companyId, $moduleId]);
    } else {
        // Create new subscription
        $subscribeStmt = $pdo->prepare("
            INSERT INTO company_module_subscriptions (
                company_id, module_id, status, subscribed_at
            ) VALUES (?, ?, 'active', NOW())
        ");
        $subscribeStmt->execute([$companyId, $moduleId]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully subscribed to ' . $module['name'],
        'module_name' => $module['name'],
        'module_code' => $module['module_code']
    ]);
}

function handleUnsubscription($pdo, $companyId, $input) {
    if (!isset($input['module_id'])) {
        throw new Exception('Module ID is required');
    }
    
    $moduleId = $input['module_id'];
    
    // Update subscription status
    $unsubscribeStmt = $pdo->prepare("
        UPDATE company_module_subscriptions 
        SET status = 'cancelled', cancelled_at = NOW()
        WHERE company_id = ? AND module_id = ?
    ");
    $unsubscribeStmt->execute([$companyId, $moduleId]);
    
    if ($unsubscribeStmt->rowCount() === 0) {
        throw new Exception('Subscription not found');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully unsubscribed from module'
    ]);
}

function getSubscriptions($pdo, $companyId) {
    $subscriptionsStmt = $pdo->prepare("
        SELECT 
            cms.*,
            mm.name as module_name,
            mm.module_code,
            mm.description,
            mm.price,
            mm.category,
            mm.icon
        FROM company_module_subscriptions cms
        JOIN marketplace_modules mm ON cms.module_id = mm.id
        WHERE cms.company_id = ? AND cms.status = 'active'
        ORDER BY cms.subscribed_at DESC
    ");
    $subscriptionsStmt->execute([$companyId]);
    $subscriptions = $subscriptionsStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'subscriptions' => $subscriptions
    ]);
}
?>