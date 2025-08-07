<?php
// ===== BRC NOTIFICATION SYSTEM =====
// File: customer/ajax/notifications.php

header('Content-Type: application/json');
session_start();

require_once '../../config/config.php';
require_once '../../dbConnect/dbkonfigur.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';
$company_id = $_SESSION['company_id'] ?? $input['company_id'] ?? 1;
$user_id = $_SESSION['user_id'] ?? $input['user_id'] ?? 1;

try {
    switch ($action) {
        
        case 'get_notifications':
            $notifications = getNotifications($pdo, $company_id, $user_id);
            echo json_encode(['success' => true, 'notifications' => $notifications]);
            break;
            
        case 'mark_read':
            $notification_id = $input['notification_id'] ?? null;
            $result = markNotificationRead($pdo, $notification_id, $user_id);
            echo json_encode($result);
            break;
            
        case 'create_notification':
            $result = createNotification($pdo, $input);
            echo json_encode($result);
            break;
            
        case 'get_unread_count':
            $count = getUnreadCount($pdo, $company_id, $user_id);
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Notification API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

// ===== HELPER FUNCTIONS =====

/**
 * Get user notifications
 */
function getNotifications($pdo, $company_id, $user_id) {
    $sql = "
        SELECT 
            pn.*,
            pe.execution_name,
            se.step_name,
            pi.issue_description
        FROM process_notifications pn
        LEFT JOIN process_executions pe ON pn.related_execution_id = pe.id
        LEFT JOIN step_executions se ON pn.related_step_id = se.id
        LEFT JOIN process_issues pi ON pn.related_issue_id = pi.id
        WHERE pn.company_id = ? AND pn.user_id = ?
        ORDER BY pn.created_at DESC
        LIMIT 20
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$company_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Mark notification as read
 */
function markNotificationRead($pdo, $notification_id, $user_id) {
    if (!$notification_id) {
        return ['success' => false, 'error' => 'Notification ID required'];
    }
    
    $stmt = $pdo->prepare("
        UPDATE process_notifications 
        SET is_read = 1, read_at = NOW() 
        WHERE id = ? AND user_id = ?
    ");
    
    $stmt->execute([$notification_id, $user_id]);
    
    return ['success' => true, 'message' => 'Notification marked as read'];
}

/**
 * Create new notification
 */
function createNotification($pdo, $data) {
    $required = ['user_id', 'company_id', 'type', 'title', 'message'];
    
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            return ['success' => false, 'error' => "Missing field: {$field}"];
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO process_notifications 
        (user_id, company_id, type, title, message, related_execution_id, related_step_id, related_issue_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $data['user_id'],
        $data['company_id'],
        $data['type'],
        $data['title'],
        $data['message'],
        $data['related_execution_id'] ?? null,
        $data['related_step_id'] ?? null,
        $data['related_issue_id'] ?? null
    ]);
    
    return ['success' => true, 'notification_id' => $pdo->lastInsertId()];
}

/**
 * Get unread notification count
 */
function getUnreadCount($pdo, $company_id, $user_id) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM process_notifications 
        WHERE company_id = ? AND user_id = ? AND is_read = 0
    ");
    
    $stmt->execute([$company_id, $user_id]);
    return (int) $stmt->fetchColumn();
}
?>