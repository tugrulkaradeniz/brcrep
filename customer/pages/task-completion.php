<?php
// ===== BRC TASK COMPLETION INTERFACE =====
// File: customer/pages/task-completion.php

session_start();
if (!isset($_SESSION['company_id'])) {
    header('Location: /brcproject/customer/auth/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'] ?? null;
$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    header('Location: process-dashboard.php');
    exit;
}

// Get task details
require_once '../../dbConnect/dbkonfigur.php';

$stmt = $pdo->prepare("
    SELECT 
        se.*,
        pe.execution_name,
        pe.batch_number,
        pe.total_steps,
        cp.process_name,
        cp.process_data
    FROM step_executions se
    JOIN process_executions pe ON se.process_execution_id = pe.id
    JOIN company_processes cp ON pe.company_process_id = cp.id
    WHERE se.id = ? AND cp.company_id = ?
");
$stmt->execute([$task_id, $company_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: process-dashboard.php?error=task_not_found');
    exit;
}

// Get process data for control points
$processData = json_decode($task['process_data'], true);
$stepData = null;
foreach ($processData['process_steps'] ?? [] as $step) {
    if ($step['step_number'] == $task['step_number']) {
        $stepData = $step;
        break;
    }
}

// Default control points if no specific data
$controlPoints = [];
if ($stepData && isset($stepData['control_points'])) {
    $controlPoints = $stepData['control_points'];
} else {
    // Fallback default control points
    $controlPoints = [
        'Visual inspection completed',
        'Quality standards checked', 
        'Documentation reviewed',
        'Safety requirements verified'
    ];
}

$pageTitle = 'Complete Task: ' . $task['step_name'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - BRC Load</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .control-point {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .control-point.completed {
            background: #e8f5e8;
            border-left-color: var(--success-color);
        }
        
        .control-point.issue {
            background: #ffe6e6;
            border-left-color: var(--danger-color);
        }
        
        .check-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .btn-ok {
            background: var(--success-color);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-ok:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .btn-problem {
            background: var(--danger-color);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-problem:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .progress-indicator {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .issue-form {
            background: #fff3cd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }
        
        .final-actions {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            position: sticky;
            bottom: 2rem;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
        }
        
        .badge-status {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        .notes-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-1">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Task Completion
                    </h2>
                    <p class="mb-0 opacity-75"><?php echo htmlspecialchars($task['execution_name']); ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="process-dashboard.php" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="step-indicator">
                <div class="step-number"><?php echo $task['step_number']; ?></div>
                <div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($task['step_name']); ?></h5>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($task['step_description']); ?></p>
                </div>
                <div class="ms-auto">
                    <span class="badge badge-status bg-primary">
                        Step <?php echo $task['step_number']; ?> of <?php echo $task['total_steps'] ?? 5; ?>
                    </span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <small class="text-muted d-block">Batch Number</small>
                    <strong><?php echo htmlspecialchars($task['batch_number']); ?></strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Assigned Role</small>
                    <strong><?php echo htmlspecialchars($task['assigned_role']); ?></strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Estimated Duration</small>
                    <strong><?php echo $task['estimated_duration']; ?> minutes</strong>
                </div>
            </div>
        </div>

        <!-- Control Points Checklist -->
        <div class="task-card">
            <h4 class="mb-4">
                <i class="fas fa-list-check me-2"></i>
                Quality Control Points
            </h4>
            
            <?php foreach ($controlPoints as $index => $controlPoint): ?>
                <div class="control-point" data-point-id="<?php echo $index; ?>" id="control-point-<?php echo $index; ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-2">
                                <i class="fas fa-check-circle me-2 text-muted"></i>
                                <?php echo htmlspecialchars($controlPoint); ?>
                            </h6>
                            
                            <?php if ($index === 0): ?>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Acceptance Criteria: <?php echo htmlspecialchars($stepData['acceptance_criteria'] ?? 'Follow standard procedure'); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="check-buttons">
                        <button type="button" class="btn btn-ok" onclick="markControlPoint(<?php echo $index; ?>, 'ok')">
                            <i class="fas fa-check me-2"></i>OK
                        </button>
                        <button type="button" class="btn btn-problem" onclick="markControlPoint(<?php echo $index; ?>, 'problem')">
                            <i class="fas fa-exclamation-triangle me-2"></i>Problem
                        </button>
                    </div>
                    
                    <!-- Issue Form (Hidden by default) -->
                    <div class="issue-form" id="issue-form-<?php echo $index; ?>">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Report Issue</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Issue Type</label>
                                <select class="form-select" id="issue-type-<?php echo $index; ?>">
                                    <option value="minor">Minor Issue</option>
                                    <option value="major">Major Issue</option>
                                    <option value="critical">Critical Issue</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Corrective Action</label>
                                <input type="text" class="form-control" id="corrective-action-<?php echo $index; ?>" 
                                       placeholder="What action was taken?">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Issue Description</label>
                            <textarea class="form-control" id="issue-description-<?php echo $index; ?>" rows="3" 
                                      placeholder="Describe the issue..."></textarea>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm" onclick="saveIssue(<?php echo $index; ?>)">
                            <i class="fas fa-save me-2"></i>Save Issue
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <h5 class="mb-3">
                <i class="fas fa-sticky-note me-2"></i>
                Additional Notes
            </h5>
            <textarea class="form-control" id="step-notes" rows="4" 
                      placeholder="Add any additional observations, measurements, or comments..."></textarea>
        </div>
    </div>

    <!-- Final Actions (Sticky) -->
    <div class="final-actions">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start">
                    <div id="completion-status">
                        <small class="text-muted">Control Points: </small>
                        <span id="completed-count">0</span> / <span id="total-count"><?php echo count($controlPoints); ?></span> completed
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="saveDraft()">
                        <i class="fas fa-save me-2"></i>Save Draft
                    </button>
                    <button type="button" class="btn btn-success btn-lg" id="complete-step-btn" onclick="completeStep()" disabled>
                        <i class="fas fa-check-double me-2"></i>Complete Step
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== TASK COMPLETION JAVASCRIPT =====
        
        const TASK_ID = <?php echo $task_id; ?>;
        const COMPANY_ID = <?php echo $company_id; ?>;
        const USER_ID = <?php echo $user_id ?? 'null'; ?>;
        const API_BASE = '../../platform/ajax/process-management.php';
        
        let controlPointsStatus = {};
        let totalControlPoints = <?php echo count($controlPoints); ?>;
        let completedCount = 0;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCompletionStatus();
            console.log('✅ Task Completion Interface loaded');
        });
        
        // Mark control point as OK or Problem
        function markControlPoint(pointId, status) {
            console.log(`Marking point ${pointId} as ${status}`);
            
            const controlPoint = document.getElementById(`control-point-${pointId}`);
            const issueForm = document.getElementById(`issue-form-${pointId}`);
            
            console.log("Control point element:", controlPoint);
            
            if (!controlPoint) {
                console.error(`Control point ${pointId} not found!`);
                return;
            }
            
            if (status === 'ok') {
                controlPoint.classList.remove('issue');
                controlPoint.classList.add('completed');
                issueForm.style.display = 'none';
                
                controlPointsStatus[pointId] = 'ok';
                console.log(`✅ Point ${pointId} marked as OK`);
            } else if (status === 'problem') {
                controlPoint.classList.remove('completed');
                controlPoint.classList.add('issue');
                issueForm.style.display = 'block';
                
                controlPointsStatus[pointId] = 'problem';
                console.log(`❌ Point ${pointId} marked as Problem`);
            }
            
            updateCompletionStatus();
        }
        
        // Save issue details
        function saveIssue(pointId) {
            const issueType = document.getElementById(`issue-type-${pointId}`).value;
            const description = document.getElementById(`issue-description-${pointId}`).value;
            const correctiveAction = document.getElementById(`corrective-action-${pointId}`).value;
            
            if (!description.trim()) {
                alert('Please provide an issue description');
                return;
            }
            
            // Store issue data
            controlPointsStatus[pointId] = {
                status: 'problem',
                issue_type: issueType,
                description: description,
                corrective_action: correctiveAction
            };
            
            showNotification('Issue details saved', 'warning');
            updateCompletionStatus();
        }
        
        // Update completion status
        function updateCompletionStatus() {
            completedCount = Object.keys(controlPointsStatus).length;
            
            document.getElementById('completed-count').textContent = completedCount;
            
            console.log(`Progress: ${completedCount}/${totalControlPoints}`);
            
            const completeBtn = document.getElementById('complete-step-btn');
            if (completedCount >= totalControlPoints) {
                completeBtn.disabled = false;
                completeBtn.classList.add('btn-success');
                console.log('✅ Complete Step button enabled!');
            } else {
                completeBtn.disabled = true;
                completeBtn.classList.remove('btn-success');
            }
        }
        
        // Save draft
        function saveDraft() {
            const notes = document.getElementById('step-notes').value;
            
            // Auto-save functionality
            localStorage.setItem(`task_${TASK_ID}_draft`, JSON.stringify({
                controlPoints: controlPointsStatus,
                notes: notes,
                timestamp: Date.now()
            }));
            
            showNotification('Draft saved', 'info');
        }
        
        // Complete step
        async function completeStep() {
            const notes = document.getElementById('step-notes').value;
            
            // Determine overall result
            const hasProblems = Object.values(controlPointsStatus).some(status => 
                status === 'problem' || (typeof status === 'object' && status.status === 'problem')
            );
            
            const result = hasProblems ? 'issue' : 'ok';
            
            console.log('🚀 Completing step:', {
                task_id: TASK_ID,
                result: result,
                controlPoints: controlPointsStatus,
                notes: notes
            });
            
            try {
                const response = await fetch(API_BASE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'complete_step',
                        step_id: TASK_ID,
                        result: result,
                        notes: notes,
                        control_points: controlPointsStatus,
                        user_id: USER_ID
                    })
                });
                
                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.success) {
                    showNotification('Step completed successfully!', 'success');
                    
                    // Report any issues
                    for (const [pointId, status] of Object.entries(controlPointsStatus)) {
                        if (typeof status === 'object' && status.status === 'problem') {
                            await reportIssue(status);
                        }
                    }
                    
                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = 'process-dashboard.php';
                    }, 2000);
                } else {
                    showNotification('Error completing step: ' + data.error, 'danger');
                }
                
            } catch (error) {
                console.error('Complete step error:', error);
                showNotification('Failed to complete step', 'danger');
            }
        }
        
        // Report issue to system
        async function reportIssue(issueData) {
            try {
                await fetch(API_BASE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'report_issue',
                        step_id: TASK_ID,
                        issue_type: issueData.issue_type,
                        description: issueData.description,
                        corrective_action: issueData.corrective_action,
                        user_id: USER_ID
                    })
                });
            } catch (error) {
                console.error('Report issue error:', error);
            }
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>
</html>