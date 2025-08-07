<?php
// ===== BRC PROCESS MANAGEMENT API =====
// File: platform/ajax/process-management.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
session_start();

// Include required files
require_once '../../config/config.php';
require_once '../../config/functions.php';

// Database connection
try {
    require_once '../../dbConnect/dbkonfigur.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';
$company_id = $input['company_id'] ?? $_POST['company_id'] ?? $_GET['company_id'] ?? null;
$user_id = $input['user_id'] ?? $_POST['user_id'] ?? $_GET['user_id'] ?? null;

// Validate required parameters
if (empty($action)) {
    echo json_encode(['success' => false, 'error' => 'Action is required']);
    exit;
}

try {
    switch ($action) {
        
        case 'get_dashboard_data':
            // Get dashboard statistics
            $dashboardData = getDashboardData($pdo, $company_id);
            echo json_encode(['success' => true, 'dashboard_data' => $dashboardData]);
            break;
            
        case 'get_my_tasks':
            // Get tasks assigned to current user
            $tasks = getMyTasks($pdo, $company_id, $user_id);
            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;
            
        case 'get_active_executions':
            // Get active process executions
            $executions = getActiveExecutions($pdo, $company_id);
            echo json_encode(['success' => true, 'executions' => $executions]);
            break;
            
        case 'load_templates':
            // Load available process templates
            $templates = loadProcessTemplates($pdo);
            echo json_encode(['success' => true, 'templates' => $templates]);
            break;
            
        case 'create_from_template':
            // Create company process from template
            $result = createProcessFromTemplate($pdo, $input);
            echo json_encode($result);
            break;
            
        case 'start_process':
            // Start a process execution
            $result = startProcessExecution($pdo, $input);
            echo json_encode($result);
            break;
            
        case 'complete_step':
            // Complete a process step
            $result = completeProcessStep($pdo, $input);
            echo json_encode($result);
            break;
            
        case 'report_issue':
            // Report an issue during process execution
            $result = reportProcessIssue($pdo, $input);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Process Management API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()]);
}

// ===== HELPER FUNCTIONS =====

/**
 * Get dashboard statistics
 */
function getDashboardData($pdo, $company_id) {
    $sql = "
        SELECT 
            COUNT(CASE WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion < NOW() THEN 1 END) as overdue,
            COUNT(CASE WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 1 END) as due_soon,
            COUNT(CASE WHEN se.status = 'in_progress' THEN 1 END) as in_progress,
            COUNT(CASE WHEN se.status = 'completed' AND DATE(se.actual_completion) = CURDATE() THEN 1 END) as completed_today,
            COUNT(CASE WHEN pi.status IN ('open', 'in_progress') THEN 1 END) as open_issues
        FROM companies c
        LEFT JOIN company_processes cp ON c.id = cp.company_id
        LEFT JOIN process_executions pe ON cp.id = pe.company_process_id
        LEFT JOIN step_executions se ON pe.id = se.process_execution_id
        LEFT JOIN process_issues pi ON se.id = pi.step_execution_id
        WHERE c.id = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$company_id ?: 1]);
    return [$stmt->fetch(PDO::FETCH_ASSOC)];
}

/**
 * Get tasks assigned to user
 */
function getMyTasks($pdo, $company_id, $user_id) {
    $sql = "
        SELECT 
            se.id,
            se.step_name,
            se.step_description,
            se.status,
            se.assigned_role,
            se.scheduled_completion,
            se.estimated_duration,
            pe.execution_name,
            pe.batch_number,
            CASE 
                WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion < NOW() THEN 'overdue'
                WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 'due_soon'
                WHEN se.status = 'completed' AND DATE(se.actual_completion) = CURDATE() THEN 'completed_today'
                ELSE 'upcoming'
            END as task_status
        FROM step_executions se
        JOIN process_executions pe ON se.process_execution_id = pe.id
        JOIN company_processes cp ON pe.company_process_id = cp.id
        WHERE cp.company_id = ?
        AND se.status IN ('pending', 'in_progress', 'completed')
        AND (se.assigned_to = ? OR se.assigned_to IS NULL)
        ORDER BY 
            CASE WHEN se.scheduled_completion < NOW() THEN 1 ELSE 2 END,
            se.scheduled_completion ASC
        LIMIT 20
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$company_id ?: 1, $user_id ?: 1]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get active process executions
 */
function getActiveExecutions($pdo, $company_id) {
    $sql = "
        SELECT 
            pe.id,
            pe.execution_name,
            pe.batch_number,
            pe.current_step,
            pe.total_steps,
            pe.status,
            pe.priority,
            pe.scheduled_completion,
            cp.process_name
        FROM process_executions pe
        JOIN company_processes cp ON pe.company_process_id = cp.id
        WHERE cp.company_id = ?
        AND pe.status IN ('pending', 'in_progress')
        ORDER BY pe.priority DESC, pe.scheduled_completion ASC
        LIMIT 10
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$company_id ?: 1]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Load process templates
 */
function loadProcessTemplates($pdo) {
    $sql = "SELECT * FROM process_templates WHERE is_active = 1 ORDER BY template_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Create process from template
 */
function createProcessFromTemplate($pdo, $input) {
    $company_id = $input['company_id'] ?? null;
    $template_id = $input['template_id'] ?? null;
    $process_name = $input['process_name'] ?? null;
    
    if (!$company_id || !$template_id || !$process_name) {
        return ['success' => false, 'error' => 'Missing required parameters'];
    }
    
    // Get template
    $stmt = $pdo->prepare("SELECT * FROM process_templates WHERE id = ?");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$template) {
        return ['success' => false, 'error' => 'Template not found'];
    }
    
    // Create company process
    $process_code = strtolower(str_replace(' ', '_', $process_name)) . '_' . time();
    
    $stmt = $pdo->prepare("
        INSERT INTO company_processes 
        (company_id, template_id, process_name, process_code, process_data, status, created_by, created_at) 
        VALUES (?, ?, ?, ?, ?, 'active', ?, NOW())
    ");
    
    $stmt->execute([
        $company_id,
        $template_id,
        $process_name,
        $process_code,
        $template['template_data'],
        1
    ]);
    
    $process_id = $pdo->lastInsertId();
    
    return [
        'success' => true,
        'process_id' => $process_id,
        'process_code' => $process_code,
        'message' => 'Process created successfully'
    ];
}

/**
 * Start process execution
 */
function startProcessExecution($pdo, $input) {
    $process_id = $input['process_id'] ?? null;
    $execution_name = $input['execution_name'] ?? null;
    $batch_number = $input['batch_number'] ?? null;
    
    if (!$process_id || !$execution_name) {
        return ['success' => false, 'error' => 'Missing required parameters'];
    }
    
    // Get process data
    $stmt = $pdo->prepare("SELECT * FROM company_processes WHERE id = ?");
    $stmt->execute([$process_id]);
    $process = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$process) {
        return ['success' => false, 'error' => 'Process not found'];
    }
    
    $process_data = json_decode($process['process_data'], true);
    $total_steps = count($process_data['process_steps']);
    
    // Create execution
    $stmt = $pdo->prepare("
        INSERT INTO process_executions 
        (company_process_id, execution_name, batch_number, total_steps, status, started_by, started_at, scheduled_completion) 
        VALUES (?, ?, ?, ?, 'in_progress', ?, NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR))
    ");
    
    $stmt->execute([
        $process_id,
        $execution_name,
        $batch_number ?: 'BATCH-' . date('Ymd-His'),
        $total_steps,
        1
    ]);
    
    $execution_id = $pdo->lastInsertId();
    
    // Create step executions
    foreach ($process_data['process_steps'] as $index => $step) {
        $step_number = $index + 1;
        $scheduled_start = date('Y-m-d H:i:s', strtotime("+{$index} hours"));
        $scheduled_completion = date('Y-m-d H:i:s', strtotime("+{$index} hours +{$step['estimated_duration']} minutes"));
        
        $stmt = $pdo->prepare("
            INSERT INTO step_executions 
            (process_execution_id, step_number, step_name, step_description, assigned_role, status, 
             estimated_duration, scheduled_start, scheduled_completion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $execution_id,
            $step_number,
            $step['step_name'],
            $step['description'],
            $step['responsible_roles'][0] ?? 'Operator',
            $step_number === 1 ? 'pending' : 'waiting',
            $step['estimated_duration'],
            $scheduled_start,
            $scheduled_completion
        ]);
    }
    
    return [
        'success' => true,
        'execution_id' => $execution_id,
        'total_steps' => $total_steps,
        'message' => 'Process execution started successfully'
    ];
}

/**
 * Complete process step
 */
function completeProcessStep($pdo, $input) {
    $step_id = $input['step_id'] ?? null;
    $result = $input['result'] ?? 'ok';
    $notes = $input['notes'] ?? '';
    $user_id = $input['user_id'] ?? 1;
    
    if (!$step_id) {
        return ['success' => false, 'error' => 'Step ID is required'];
    }
    
    // Update step execution
    $stmt = $pdo->prepare("
        UPDATE step_executions 
        SET status = 'completed', result = ?, notes = ?, actual_completion = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$result, $notes, $step_id]);
    
    // Get process execution info
    $stmt = $pdo->prepare("
        SELECT pe.*, se.step_number, cp.company_id
        FROM step_executions se
        JOIN process_executions pe ON se.process_execution_id = pe.id
        JOIN company_processes cp ON pe.company_process_id = cp.id
        WHERE se.id = ?
    ");
    $stmt->execute([$step_id]);
    $execution_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($execution_info) {
        $company_id = $execution_info['company_id'];
        $execution_id = $execution_info['id'];
        $current_step = $execution_info['step_number'];
        $total_steps = $execution_info['total_steps'];
        
        // Update process execution current step
        $stmt = $pdo->prepare("
            UPDATE process_executions 
            SET current_step = ?
            WHERE id = ?
        ");
        $stmt->execute([$current_step, $execution_id]);
        
        // Activate next step if exists
        if ($current_step < $total_steps) {
            $stmt = $pdo->prepare("
                UPDATE step_executions 
                SET status = 'pending', scheduled_start = NOW()
                WHERE process_execution_id = ? AND step_number = ? AND status = 'waiting'
            ");
            $stmt->execute([$execution_id, $current_step + 1]);
            
            // Create notification for next step
            createStepNotification($pdo, $company_id, $execution_id, $current_step + 1, 'step_ready');
        } else {
            // Process completed
            $stmt = $pdo->prepare("
                UPDATE process_executions 
                SET status = 'completed', actual_completion = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$execution_id]);
            
            // Create process completion notification
            createProcessNotification($pdo, $company_id, $execution_id, 'process_complete');
        }
        
        // Create step completion notification
        createStepNotification($pdo, $company_id, $execution_id, $current_step, 'step_completed');
    }
    
    return [
        'success' => true,
        'message' => 'Step completed successfully'
    ];
}

function createStepNotification($pdo, $company_id, $execution_id, $step_number, $type) {
    // Get step and execution details
    $stmt = $pdo->prepare("
        SELECT se.step_name, pe.execution_name, pe.batch_number
        FROM step_executions se
        JOIN process_executions pe ON se.process_execution_id = pe.id
        WHERE pe.id = ? AND se.step_number = ?
    ");
    $stmt->execute([$execution_id, $step_number]);
    $stepInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$stepInfo) return;
    
    $titles = [
        'step_completed' => 'Step Completed',
        'step_ready' => 'Next Step Ready',
        'step_due' => 'Step Due Soon',
        'step_overdue' => 'Step Overdue'
    ];
    
    $messages = [
        'step_completed' => "Step {$step_number}: {$stepInfo['step_name']} completed for {$stepInfo['execution_name']}",
        'step_ready' => "Step {$step_number}: {$stepInfo['step_name']} is ready to start for {$stepInfo['execution_name']}",
        'step_due' => "Step {$step_number}: {$stepInfo['step_name']} is due soon for {$stepInfo['execution_name']}",
        'step_overdue' => "Step {$step_number}: {$stepInfo['step_name']} is overdue for {$stepInfo['execution_name']}"
    ];
    
    // Insert notification for all users in company (simplified)
    $stmt = $pdo->prepare("
        INSERT INTO process_notifications 
        (user_id, company_id, type, title, message, related_execution_id, created_at) 
        SELECT cu.id, ?, ?, ?, ?, ?, NOW()
        FROM company_users cu 
        WHERE cu.company_id = ? AND cu.status = 'active'
    ");
    
    $stmt->execute([
        $company_id,
        $type,
        $titles[$type],
        $messages[$type],
        $execution_id,
        $company_id
    ]);
}

// Helper function to create process notifications
function createProcessNotification($pdo, $company_id, $execution_id, $type) {
    $stmt = $pdo->prepare("
        SELECT execution_name, batch_number 
        FROM process_executions 
        WHERE id = ?
    ");
    $stmt->execute([$execution_id]);
    $processInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$processInfo) return;
    
    $title = "Process Completed";
    $message = "Process {$processInfo['execution_name']} (Batch: {$processInfo['batch_number']}) has been completed successfully";
    
    // Insert notification for all users in company
    $stmt = $pdo->prepare("
        INSERT INTO process_notifications 
        (user_id, company_id, type, title, message, related_execution_id, created_at) 
        SELECT cu.id, ?, ?, ?, ?, ?, NOW()
        FROM company_users cu 
        WHERE cu.company_id = ? AND cu.status = 'active'
    ");
    
    $stmt->execute([
        $company_id,
        $type,
        $title,
        $message,
        $execution_id,
        $company_id
    ]);
}

/**
 * Report process issue
 */
function reportProcessIssue($pdo, $input) {
    $step_id = $input['step_id'] ?? null;
    $issue_type = $input['issue_type'] ?? 'minor';
    $description = $input['description'] ?? '';
    $user_id = $input['user_id'] ?? 1;
    
    if (!$step_id || !$description) {
        return ['success' => false, 'error' => 'Step ID and description are required'];
    }
    
    // Create issue record
    $stmt = $pdo->prepare("
        INSERT INTO process_issues 
        (step_execution_id, issue_type, issue_description, status, created_by, created_at) 
        VALUES (?, ?, ?, 'open', ?, NOW())
    ");
    
    $stmt->execute([$step_id, $issue_type, $description, $user_id]);
    
    $issue_id = $pdo->lastInsertId();
    
    return [
        'success' => true,
        'issue_id' => $issue_id,
        'message' => 'Issue reported successfully'
    ];
}
?>