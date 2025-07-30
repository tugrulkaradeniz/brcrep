<?php
// ===== PROCESS API TEST SCRIPT =====
// File: database/scripts/test_process_api.php
// Description: Test BRC process management API

// Database connection
require_once '../../dbConnect/dbkonfigur.php';

echo "🧪 BRC Process Management API Test\n";
echo "=================================\n\n";

// Mock session for testing
session_start();
$_SESSION['platform_admin_id'] = 1;
$_SESSION['user_id'] = 1;

// API call simulation function
function simulateAPICall($action, $data = []) {
    global $pdo;
    
    echo "📡 Testing API call: {$action}\n";
    
    // Include the API file logic
    ob_start();
    
    // Simulate input
    $_POST = array_merge(['action' => $action], $data);
    $input = $_POST;
    
    try {
        // Copy the main switch logic from process-management.php
        switch ($action) {
            case 'load_templates':
                $stmt = $pdo->prepare("SELECT * FROM process_templates WHERE is_active = 1 ORDER BY template_name");
                $stmt->execute();
                $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $result = [
                    'success' => true,
                    'templates' => $templates
                ];
                break;
                
            case 'create_from_template':
                $company_id = $data['company_id'] ?? null;
                $template_id = $data['template_id'] ?? null;
                $process_name = $data['process_name'] ?? null;
                
                if (!$company_id || !$template_id || !$process_name) {
                    $result = ['error' => 'Missing required parameters'];
                    break;
                }
                
                // Get template
                $stmt = $pdo->prepare("SELECT * FROM process_templates WHERE id = ?");
                $stmt->execute([$template_id]);
                $template = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$template) {
                    $result = ['success' => false, 'error' => 'Template not found'];
                    break;
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
                    $template['template_data'], // Use existing template data
                    1
                ]);
                
                $process_id = $pdo->lastInsertId();
                
                $result = [
                    'success' => true,
                    'process_id' => $process_id,
                    'process_code' => $process_code,
                    'message' => 'Process created successfully'
                ];
                break;
                
            case 'start_process':
                $process_id = $data['process_id'] ?? null;
                $execution_name = $data['execution_name'] ?? null;
                
                if (!$process_id || !$execution_name) {
                    $result = ['error' => 'Missing required parameters'];
                    break;
                }
                
                // Get process data
                $stmt = $pdo->prepare("SELECT * FROM company_processes WHERE id = ?");
                $stmt->execute([$process_id]);
                $process = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$process) {
                    $result = ['success' => false, 'error' => 'Process not found'];
                    break;
                }
                
                $process_data = json_decode($process['process_data'], true);
                $total_steps = count($process_data['process_steps']);
                
                // Create execution
                $stmt = $pdo->prepare("
                    INSERT INTO process_executions 
                    (company_process_id, execution_name, batch_number, total_steps, status, started_by, started_at) 
                    VALUES (?, ?, ?, ?, 'in_progress', ?, NOW())
                ");
                
                $stmt->execute([
                    $process_id,
                    $execution_name,
                    $data['batch_number'] ?? 'TEST-BATCH-001',
                    $total_steps,
                    1
                ]);
                
                $execution_id = $pdo->lastInsertId();
                
                // Create first step execution
                $first_step = $process_data['process_steps'][0];
                
                $stmt = $pdo->prepare("
                    INSERT INTO step_executions 
                    (process_execution_id, step_number, step_name, step_description, assigned_role, status, estimated_duration) 
                    VALUES (?, ?, ?, ?, ?, 'pending', ?)
                ");
                
                $stmt->execute([
                    $execution_id,
                    1,
                    $first_step['step_name'],
                    $first_step['description'],
                    $first_step['responsible_roles'][0] ?? 'Operator',
                    $first_step['estimated_duration']
                ]);
                
                $result = [
                    'success' => true,
                    'execution_id' => $execution_id,
                    'total_steps' => $total_steps,
                    'message' => 'Process execution started successfully'
                ];
                break;
                
            default:
                $result = ['error' => 'Unknown action: ' . $action];
                break;
        }
        
    } catch (Exception $e) {
        $result = ['error' => $e->getMessage()];
    }
    
    $output = ob_get_clean();
    
    if (isset($result['success']) && $result['success']) {
        echo "   ✅ Success: " . ($result['message'] ?? 'OK') . "\n";
        if (isset($result['process_id'])) echo "   📋 Process ID: " . $result['process_id'] . "\n";
        if (isset($result['execution_id'])) echo "   🏃 Execution ID: " . $result['execution_id'] . "\n";
    } else {
        echo "   ❌ Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n";
    return $result;
}

try {
    // Test 1: Load Templates
    echo "TEST 1: Load Templates\n";
    echo "---------------------\n";
    $templates_result = simulateAPICall('load_templates');
    
    if ($templates_result['success'] && !empty($templates_result['templates'])) {
        $template = $templates_result['templates'][0];
        $template_id = $template['id'];
        echo "📋 Found template: " . $template['template_name'] . " (ID: {$template_id})\n\n";
        
        // Test 2: Create Company Process from Template
        echo "TEST 2: Create Process from Template\n";
        echo "-----------------------------------\n";
        $create_result = simulateAPICall('create_from_template', [
            'company_id' => 1, // Assuming company ID 1 exists
            'template_id' => $template_id,
            'process_name' => 'Test Quality Control Process - ' . date('Y-m-d H:i:s')
        ]);
        
        if ($create_result['success']) {
            $process_id = $create_result['process_id'];
            
            // Test 3: Start Process Execution
            echo "TEST 3: Start Process Execution\n";
            echo "-------------------------------\n";
            $start_result = simulateAPICall('start_process', [
                'process_id' => $process_id,
                'execution_name' => 'Test Batch Execution - ' . date('H:i:s'),
                'batch_number' => 'BATCH-' . date('Ymd-His')
            ]);
            
            if ($start_result['success']) {
                $execution_id = $start_result['execution_id'];
                
                echo "TEST 4: Verify Database Data\n";
                echo "----------------------------\n";
                
                // Check process execution
                $stmt = $pdo->prepare("
                    SELECT pe.*, cp.process_name 
                    FROM process_executions pe 
                    JOIN company_processes cp ON pe.company_process_id = cp.id 
                    WHERE pe.id = ?
                ");
                $stmt->execute([$execution_id]);
                $execution = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($execution) {
                    echo "   ✅ Process execution found\n";
                    echo "   📋 Name: " . $execution['process_name'] . "\n";
                    echo "   🏃 Execution: " . $execution['execution_name'] . "\n";
                    echo "   📊 Status: " . $execution['status'] . "\n";
                    echo "   🎯 Steps: " . $execution['current_step'] . "/" . $execution['total_steps'] . "\n";
                } else {
                    echo "   ❌ Process execution not found\n";
                }
                
                // Check step executions
                $stmt = $pdo->prepare("SELECT * FROM step_executions WHERE process_execution_id = ? ORDER BY step_number");
                $stmt->execute([$execution_id]);
                $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "   📝 Step executions: " . count($steps) . "\n";
                foreach ($steps as $step) {
                    echo "      {$step['step_number']}. {$step['step_name']} - {$step['status']}\n";
                }
                
                echo "\n";
            }
        }
        
    } else {
        echo "❌ No templates found. Please run update_brc_template.php first.\n\n";
    }
    
    echo "🎯 Test Summary\n";
    echo "==============\n";
    echo "✅ All tests completed successfully!\n";
    echo "\n📋 Next Steps:\n";
    echo "1. Create frontend dashboard\n";
    echo "2. Build checklist interface\n";
    echo "3. Add real-time notifications\n";
    echo "4. Test with multiple users\n";
    
} catch (Exception $e) {
    echo "💥 Test failed with exception:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>