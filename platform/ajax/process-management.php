<?php
// ===== BRC PROCESS MANAGEMENT API =====
// File: platform/ajax/process-management.php
// Description: Core API for BRC process workflow management

// Buffer control
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Database connection
$db_paths = [
    '../../dbConnect/dbkonfigur.php',
    '../../../dbConnect/dbkonfigur.php',
    '../../../../dbConnect/dbkonfigur.php'
];

$pdo = null;
$db_connected = false;

foreach ($db_paths as $path) {
    if (file_exists($path)) {
        try {
            require_once $path;
            $db_connected = true;
            break;
        } catch (Exception $e) {
            continue;
        }
    }
}

if (!$db_connected) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// JSON input handling
$input_json = json_decode(file_get_contents('php://input'), true);
$input_post = $_POST;
$input_get = $_GET;

if (!empty($input_json)) {
    $input = $input_json;
} else if (!empty($input_post)) {
    $input = $input_post;
} else if (!empty($input_get)) {
    $input = $input_get;
} else {
    echo json_encode(['error' => 'No input data found']);
    exit();
}

$action = $input['action'] ?? '';

// Debug logging
function debugLog($message, $data = []) {
    $logDir = '../../logs/';
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $log = date('Y-m-d H:i:s') . ' - PROCESS: ' . $message . ' - ' . json_encode($data) . "\n";
    @file_put_contents($logDir . 'process.log', $log, FILE_APPEND);
}

// Load BRC template
function loadBRCTemplate($template_code) {
    $template_path = "../../templates/{$template_code}.json";
    if (!file_exists($template_path)) {
        return null;
    }
    
    $template_content = file_get_contents($template_path);
    return json_decode($template_content, true);
}

// Create process from template
function createProcessFromTemplate($pdo, $company_id, $template_id, $process_name, $customizations = []) {
    try {
        // Get template
        $stmt = $pdo->prepare("SELECT * FROM process_templates WHERE id = ?");
        $stmt->execute([$template_id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            return ['success' => false, 'error' => 'Template not found'];
        }
        
        // Load template data
        $template_data = loadBRCTemplate($template['template_code']);
        if (!$template_data) {
            return ['success' => false, 'error' => 'Template file not found'];
        }
        
        // Apply customizations
        if (!empty($customizations)) {
            $template_data = array_merge_recursive($template_data, $customizations);
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
            json_encode($template_data),
            $_SESSION['platform_admin_id'] ?? 1
        ]);
        
        $process_id = $pdo->lastInsertId();
        
        debugLog('Process created from template', [
            'process_id' => $process_id,
            'template_code' => $template['template_code'],
            'company_id' => $company_id
        ]);
        
        return [
            'success' => true,
            'process_id' => $process_id,
            'process_code' => $process_code,
            'message' => 'Process created successfully'
        ];
        
    } catch (Exception $e) {
        debugLog('Error creating process from template', ['error' => $e->getMessage()]);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Start process execution
function startProcessExecution($pdo, $process_id, $execution_name, $batch_number = null, $scheduled_completion = null) {
    try {
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
            VALUES (?, ?, ?, ?, 'in_progress', ?, NOW(), ?)
        ");
        
        $stmt->execute([
            $process_id,
            $execution_name,
            $batch_number,
            $total_steps,
            $_SESSION['user_id'] ?? $_SESSION['platform_admin_id'] ?? 1,
            $scheduled_completion
        ]);
        
        $execution_id = $pdo->lastInsertId();
        
        // Create step executions
        foreach ($process_data['process_steps'] as $step) {
            $step_scheduled_start = null;
            $step_scheduled_completion = null;
            
            if ($scheduled_completion) {
                $total_duration = array_sum(array_column($process_data['process_steps'], 'estimated_duration'));
                $step_start_offset = array_sum(array_slice(array_column($process_data['process_steps'], 'estimated_duration'), 0, $step['step_number'] - 1));
                
                $step_scheduled_start = date('Y-m-d H:i:s', strtotime($scheduled_completion) - ($total_duration - $step_start_offset) * 60);
                $step_scheduled_completion = date('Y-m-d H:i:s', strtotime($step_scheduled_start) + $step['estimated_duration'] * 60);
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO step_executions 
                (process_execution_id, step_number, step_name, step_description, assigned_role, status, scheduled_start, scheduled_completion, estimated_duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $status = ($step['step_number'] == 1) ? 'pending' : 'waiting';
            $assigned_role = $step['responsible_roles'][0] ?? 'Operator';
            
            $stmt->execute([
                $execution_id,
                $step['step_number'],
                $step['step_name'],
                $step['description'],
                $assigned_role,
                $status,
                $step_scheduled_start,
                $step_scheduled_completion,
                $step['estimated_duration']
            ]);
        }
        
        debugLog('Process execution started', [
            'execution_id' => $execution_id,
            'batch_number' => $batch_number,
            'total_steps' => $total_steps
        ]);
        
        return [
            'success' => true,
            'execution_id' => $execution_id,
            'total_steps' => $total_steps,
            'message' => 'Process execution started successfully'
        ];
        
    } catch (Exception $e) {
        debugLog('Error starting process execution', ['error' => $e->getMessage()]);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Complete process step
function completeProcessStep($pdo, $step_execution_id, $result, $notes = '', $corrective_action = '', $attachments = []) {
    try {
        // Get step info
        $stmt = $pdo->prepare("
            SELECT se.*, pe.id as execution_id, pe.current_step, pe.total_steps 
            FROM step_executions se 
            JOIN process_executions pe ON se.process_execution_id = pe.id 
            WHERE se.id = ?
        ");
        $stmt->execute([$step_execution_id]);
        $step = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$step) {
            return ['success' => false, 'error' => 'Step not found'];
        }
        
        // Update step
        $stmt = $pdo->prepare("
            UPDATE step_executions 
            SET status = 'completed', result = ?, notes = ?, corrective_action = ?, attachments = ?, actual_completion = NOW() 
            WHERE id = ?
        ");
        
        $stmt->execute([
            $result,
            $notes,
            $corrective_action,
            json_encode($attachments),
            $step_execution_id
        ]);
        
        // Update process current step
        $next_step = $step['current_step'] + 1;
        $process_status = ($next_step > $step['total_steps']) ? 'completed' : 'in_progress';
        
        $stmt = $pdo->prepare("
            UPDATE process_executions 
            SET current_step = ?, status = ?, actual_completion = CASE WHEN ? = 'completed' THEN NOW() ELSE actual_completion END 
            WHERE id = ?
        ");
        
        $stmt->execute([
            min($next_step, $step['total_steps']),
            $process_status,
            $process_status,
            $step['execution_id']
        ]);
        
        // Activate next step if exists
        if ($next_step <= $step['total_steps']) {
            $stmt = $pdo->prepare("
                UPDATE step_executions 
                SET status = 'pending' 
                WHERE process_execution_id = ? AND step_number = ?
            ");
            $stmt->execute([$step['execution_id'], $next_step]);
        }
        
        // Create issue if result is not OK
        if ($result !== 'ok') {
            $issue_type = ($result === 'critical_issue') ? 'critical' : 'major';
            
            $stmt = $pdo->prepare("
                INSERT INTO process_issues 
                (step_execution_id, issue_type, issue_description, corrective_action, status, created_by) 
                VALUES (?, ?, ?, ?, 'open', ?)
            ");
            
            $stmt->execute([
                $step_execution_id,
                $issue_type,
                $notes,
                $corrective_action,
                $_SESSION['user_id'] ?? 1
            ]);
        }
        
        debugLog('Step completed', [
            'step_id' => $step_execution_id,
            'result' => $result,
            'next_step' => $next_step
        ]);
        
        return [
            'success' => true,
            'next_step' => $next_step,
            'process_status' => $process_status,
            'message' => 'Step completed successfully'
        ];
        
    } catch (Exception $e) {
        debugLog('Error completing step', ['error' => $e->getMessage()]);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Main switch logic
try {
    switch ($action) {
        case 'load_templates':
            $stmt = $pdo->prepare("SELECT * FROM process_templates WHERE is_active = 1 ORDER BY template_name");
            $stmt->execute();
            $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'templates' => $templates
            ]);
            break;
            
        case 'create_from_template':
            $company_id = $input['company_id'] ?? null;
            $template_id = $input['template_id'] ?? null;
            $process_name = $input['process_name'] ?? null;
            $customizations = $input['customizations'] ?? [];
            
            if (!$company_id || !$template_id || !$process_name) {
                echo json_encode(['error' => 'Missing required parameters']);
                break;
            }
            
            $result = createProcessFromTemplate($pdo, $company_id, $template_id, $process_name, $customizations);
            echo json_encode($result);
            break;
            
        case 'start_process':
            $process_id = $input['process_id'] ?? null;
            $execution_name = $input['execution_name'] ?? null;
            $batch_number = $input['batch_number'] ?? null;
            $scheduled_completion = $input['scheduled_completion'] ?? null;
            
            if (!$process_id || !$execution_name) {
                echo json_encode(['error' => 'Missing required parameters']);
                break;
            }
            
            $result = startProcessExecution($pdo, $process_id, $execution_name, $batch_number, $scheduled_completion);
            echo json_encode($result);
            break;
            
        case 'complete_step':
            $step_execution_id = $input['step_execution_id'] ?? null;
            $result = $input['result'] ?? 'ok';
            $notes = $input['notes'] ?? '';
            $corrective_action = $input['corrective_action'] ?? '';
            $attachments = $input['attachments'] ?? [];
            
            if (!$step_execution_id) {
                echo json_encode(['error' => 'Missing step execution ID']);
                break;
            }
            
            $result = completeProcessStep($pdo, $step_execution_id, $result, $notes, $corrective_action, $attachments);
            echo json_encode($result);
            break;
            
        case 'get_company_processes':
            $company_id = $input['company_id'] ?? null;
            
            if (!$company_id) {
                echo json_encode(['error' => 'Company ID required']);
                break;
            }
            
            $stmt = $pdo->prepare("
                SELECT cp.*, pt.template_name, pt.brc_standard,
                       COUNT(pe.id) as execution_count,
                       MAX(pe.created_at) as last_execution
                FROM company_processes cp
                LEFT JOIN process_templates pt ON cp.template_id = pt.id
                LEFT JOIN process_executions pe ON cp.id = pe.company_process_id
                WHERE cp.company_id = ? AND cp.status = 'active'
                GROUP BY cp.id
                ORDER BY cp.created_at DESC
            ");
            $stmt->execute([$company_id]);
            $processes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'processes' => $processes
            ]);
            break;
            
        case 'get_active_executions':
            $company_id = $input['company_id'] ?? null;
            
            $where_clause = "";
            $params = [];
            
            if ($company_id) {
                $where_clause = "WHERE cp.company_id = ?";
                $params[] = $company_id;
            }
            
            $stmt = $pdo->prepare("
                SELECT pe.*, cp.process_name, cp.company_id, c.name as company_name,
                       pt.template_name, pt.brc_standard
                FROM process_executions pe
                JOIN company_processes cp ON pe.company_process_id = cp.id
                JOIN companies c ON cp.company_id = c.id
                JOIN process_templates pt ON cp.template_id = pt.id
                {$where_clause}
                AND pe.status IN ('pending', 'in_progress')
                ORDER BY pe.scheduled_completion ASC
            ");
            $stmt->execute($params);
            $executions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'executions' => $executions
            ]);
            break;
            
        case 'get_dashboard_data':
            $company_id = $input['company_id'] ?? null;
            
            // Use the view we created
            $where_clause = $company_id ? "WHERE company_id = ?" : "";
            $params = $company_id ? [$company_id] : [];
            
            $stmt = $pdo->prepare("SELECT * FROM v_daily_summary {$where_clause}");
            $stmt->execute($params);
            $dashboard_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'dashboard_data' => $dashboard_data
            ]);
            break;
            
        case 'get_my_tasks':
            $user_id = $input['user_id'] ?? $_SESSION['user_id'] ?? null;
            $company_id = $input['company_id'] ?? null;
            
            if (!$user_id) {
                echo json_encode(['error' => 'User ID required']);
                break;
            }
            
            $where_clause = "WHERE se.assigned_to = ?";
            $params = [$user_id];
            
            if ($company_id) {
                $where_clause .= " AND cp.company_id = ?";
                $params[] = $company_id;
            }
            
            $stmt = $pdo->prepare("
                SELECT se.*, pe.execution_name, pe.batch_number, cp.process_name,
                       CASE 
                           WHEN se.scheduled_completion < NOW() AND se.status IN ('pending', 'in_progress') THEN 'overdue'
                           WHEN se.scheduled_completion <= DATE_ADD(NOW(), INTERVAL 2 HOUR) AND se.status IN ('pending', 'in_progress') THEN 'due_soon'
                           WHEN se.status IN ('pending', 'in_progress') THEN 'upcoming'
                           WHEN se.status = 'completed' AND DATE(se.actual_completion) = CURDATE() THEN 'completed_today'
                           ELSE 'other'
                       END as task_status
                FROM step_executions se
                JOIN process_executions pe ON se.process_execution_id = pe.id
                JOIN company_processes cp ON pe.company_process_id = cp.id
                {$where_clause}
                ORDER BY 
                    CASE 
                        WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion < NOW() THEN 1
                        WHEN se.status IN ('pending', 'in_progress') AND se.scheduled_completion <= DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 2
                        WHEN se.status IN ('pending', 'in_progress') THEN 3
                        ELSE 4
                    END,
                    se.scheduled_completion ASC
            ");
            $stmt->execute($params);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'tasks' => $tasks
            ]);
            break;
            
        default:
            echo json_encode([
                'error' => 'Unknown action: ' . $action,
                'available_actions' => [
                    'load_templates', 'create_from_template', 'start_process', 'complete_step',
                    'get_company_processes', 'get_active_executions', 'get_dashboard_data', 'get_my_tasks'
                ]
            ]);
            break;
    }
    
} catch (Exception $e) {
    debugLog('Exception in process-management.php', [
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

ob_end_flush();
?>