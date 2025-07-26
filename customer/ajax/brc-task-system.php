<?php
// ===== GERÇEK BRC TASK MANAGEMENT SİSTEMİ =====
// Dosya: customer/ajax/brc-task-system.php

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    require_once '../../dbConnect/dbkonfigur.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? '';

// BRC Task tabloları oluştur (ilk çalıştırmada)
function createBRCTables($pdo) {
    $tables = [
        "CREATE TABLE IF NOT EXISTS brc_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL DEFAULT 1,
            lot_number VARCHAR(50),
            task_title VARCHAR(255) NOT NULL,
            process_step VARCHAR(100),
            assigned_to VARCHAR(100),
            assigned_to_name VARCHAR(100),
            priority ENUM('low','medium','high','critical') DEFAULT 'medium',
            status ENUM('pending','in_progress','completed','overdue','blocked') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            started_at DATETIME NULL,
            deadline DATETIME NULL,
            completed_at DATETIME NULL,
            checklist JSON,
            actual_values JSON,
            sla_met BOOLEAN DEFAULT NULL,
            parent_task_id INT NULL,
            next_task_trigger VARCHAR(100) NULL,
            created_by INT,
            notes TEXT,
            INDEX idx_lot_number (lot_number),
            INDEX idx_status (status),
            INDEX idx_assigned_to (assigned_to)
        )",
        
        "CREATE TABLE IF NOT EXISTS brc_process_workflows (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL DEFAULT 1,
            lot_number VARCHAR(50) NOT NULL,
            workflow_name VARCHAR(255),
            current_step VARCHAR(100),
            current_step_order INT DEFAULT 1,
            total_steps INT DEFAULT 5,
            step_data JSON,
            started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expected_completion DATETIME,
            actual_completion DATETIME NULL,
            status ENUM('active','completed','blocked','cancelled') DEFAULT 'active',
            progress_percentage DECIMAL(5,2) DEFAULT 0.00,
            sla_status ENUM('on_time','at_risk','overdue') DEFAULT 'on_time',
            quality_alerts JSON,
            UNIQUE KEY unique_lot_workflow (lot_number, workflow_name),
            INDEX idx_status (status)
        )",
        
        "CREATE TABLE IF NOT EXISTS brc_quality_checks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL DEFAULT 1,
            task_id INT,
            lot_number VARCHAR(50),
            check_type VARCHAR(100),
            parameter_name VARCHAR(100),
            expected_value VARCHAR(100),
            actual_value VARCHAR(100),
            unit VARCHAR(20),
            min_limit DECIMAL(10,2),
            max_limit DECIMAL(10,2),
            is_compliant BOOLEAN,
            checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            checked_by VARCHAR(100),
            notes TEXT,
            corrective_action TEXT,
            FOREIGN KEY (task_id) REFERENCES brc_tasks(id) ON DELETE CASCADE,
            INDEX idx_lot_compliance (lot_number, is_compliant)
        )",
        
        "CREATE TABLE IF NOT EXISTS brc_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL DEFAULT 1,
            user_email VARCHAR(100),
            task_id INT,
            lot_number VARCHAR(50),
            notification_type ENUM('task_assigned','deadline_approaching','sla_breach','quality_alert','approval_needed'),
            title VARCHAR(255),
            message TEXT,
            is_read BOOLEAN DEFAULT FALSE,
            sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_unread (user_email, is_read)
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
}

// Task oluşturma fonksiyonu
function createBRCTask($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO brc_tasks 
        (company_id, lot_number, task_title, process_step, assigned_to, assigned_to_name, priority, deadline, checklist, parent_task_id, next_task_trigger, created_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $data['company_id'] ?? 1,
        $data['lot_number'],
        $data['task_title'],
        $data['process_step'],
        $data['assigned_to'],
        $data['assigned_to_name'] ?? '',
        $data['priority'] ?? 'medium',
        $data['deadline'],
        json_encode($data['checklist'] ?? []),
        $data['parent_task_id'] ?? null,
        $data['next_task_trigger'] ?? null,
        $data['created_by'] ?? 1,
        $data['notes'] ?? ''
    ]);
    
    if ($result) {
        $task_id = $pdo->lastInsertId();
        
        // Bildirim gönder
        sendNotification($pdo, [
            'user_email' => $data['assigned_to'],
            'task_id' => $task_id,
            'lot_number' => $data['lot_number'],
            'notification_type' => 'task_assigned',
            'title' => 'Yeni Görev Atandı',
            'message' => "'{$data['task_title']}' görevi size atandı. Parti: {$data['lot_number']}"
        ]);
        
        return $task_id;
    }
    
    return false;
}

// Workflow başlatma
function startWorkflow($pdo, $lot_number, $workflow_type = 'dried_fig_production') {
    $workflows = [
        'dried_fig_production' => [
            'name' => 'Kuru İncir Üretim Süreci',
            'steps' => [
                ['step' => 'raw_material_entry', 'name' => 'Hammadde Girişi', 'duration_hours' => 2],
                ['step' => 'fumigation', 'name' => 'Fümigasyon', 'duration_hours' => 24],
                ['step' => 'sizing', 'name' => 'Boylandırma', 'duration_hours' => 4],
                ['step' => 'aflatoxin_check', 'name' => 'Aflatoksin Kontrolü', 'duration_hours' => 2],
                ['step' => 'washing', 'name' => 'Yıkama', 'duration_hours' => 3],
                ['step' => 'drying', 'name' => 'Kurutma', 'duration_hours' => 8],
                ['step' => 'final_inspection', 'name' => 'Son Kontrol', 'duration_hours' => 2],
                ['step' => 'packaging', 'name' => 'Paketleme', 'duration_hours' => 4]
            ]
        ]
    ];
    
    $workflow = $workflows[$workflow_type];
    $expected_completion = date('Y-m-d H:i:s', strtotime('+' . array_sum(array_column($workflow['steps'], 'duration_hours')) . ' hours'));
    
    // Workflow kaydı oluştur
    $stmt = $pdo->prepare("
        INSERT INTO brc_process_workflows 
        (lot_number, workflow_name, current_step, total_steps, step_data, expected_completion)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $lot_number,
        $workflow['name'],
        $workflow['steps'][0]['step'],
        count($workflow['steps']),
        json_encode($workflow['steps']),
        $expected_completion
    ]);
    
    // İlk task'ı oluştur
    createBRCTask($pdo, [
        'lot_number' => $lot_number,
        'task_title' => $workflow['steps'][0]['name'],
        'process_step' => $workflow['steps'][0]['step'],
        'assigned_to' => 'kalite@company.com',
        'assigned_to_name' => 'Kalite Sorumlusu',
        'priority' => 'high',
        'deadline' => date('Y-m-d H:i:s', strtotime('+' . $workflow['steps'][0]['duration_hours'] . ' hours')),
        'checklist' => [
            'Parti numarası kaydet',
            'Ağırlık ölç',
            'Kalite kontrolü yap',
            'Belgeleri kontrol et'
        ],
        'next_task_trigger' => $workflow['steps'][1]['step'] ?? null
    ]);
    
    return $pdo->lastInsertId();
}

// Task tamamlama ve workflow ilerletme
function completeTask($pdo, $task_id, $completion_data) {
    // Task'ı tamamla
    $stmt = $pdo->prepare("
        UPDATE brc_tasks 
        SET status = 'completed', completed_at = NOW(), actual_values = ?
        WHERE id = ?
    ");
    $stmt->execute([json_encode($completion_data), $task_id]);
    
    // Task bilgisini al
    $stmt = $pdo->prepare("SELECT * FROM brc_tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task) return false;
    
    // Kalite kontrollerini kaydet
    if (isset($completion_data['quality_checks'])) {
        foreach ($completion_data['quality_checks'] as $check) {
            saveQualityCheck($pdo, $task_id, $task['lot_number'], $check);
        }
    }
    
    // Workflow'u ilerlet
    if ($task['next_task_trigger']) {
        triggerNextTask($pdo, $task['lot_number'], $task['next_task_trigger']);
    }
    
    return true;
}

// Kalite kontrolü kaydetme
function saveQualityCheck($pdo, $task_id, $lot_number, $check_data) {
    $is_compliant = true;
    
    // Limit kontrolü
    if (isset($check_data['min_limit']) && $check_data['actual_value'] < $check_data['min_limit']) {
        $is_compliant = false;
    }
    if (isset($check_data['max_limit']) && $check_data['actual_value'] > $check_data['max_limit']) {
        $is_compliant = false;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO brc_quality_checks 
        (task_id, lot_number, check_type, parameter_name, expected_value, actual_value, unit, min_limit, max_limit, is_compliant, checked_by, notes, corrective_action)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $task_id,
        $lot_number,
        $check_data['check_type'],
        $check_data['parameter_name'],
        $check_data['expected_value'],
        $check_data['actual_value'],
        $check_data['unit'] ?? '',
        $check_data['min_limit'] ?? null,
        $check_data['max_limit'] ?? null,
        $is_compliant,
        $check_data['checked_by'],
        $check_data['notes'] ?? '',
        $is_compliant ? '' : $check_data['corrective_action'] ?? 'Düzeltici faaliyet gerekli'
    ]);
    
    // Uygunsuzluk varsa uyarı
    if (!$is_compliant) {
        sendNotification($pdo, [
            'user_email' => 'manager@company.com',
            'task_id' => $task_id,
            'lot_number' => $lot_number,
            'notification_type' => 'quality_alert',
            'title' => 'Kalite Uygunsuzluğu',
            'message' => "Parti {$lot_number} - {$check_data['parameter_name']}: {$check_data['actual_value']} (beklenen: {$check_data['expected_value']})"
        ]);
    }
}

// Bildirim gönderme
function sendNotification($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO brc_notifications 
        (user_email, task_id, lot_number, notification_type, title, message)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['user_email'],
        $data['task_id'] ?? null,
        $data['lot_number'],
        $data['notification_type'],
        $data['title'],
        $data['message']
    ]);
}

try {
    // Tabloları oluştur
    createBRCTables($pdo);
    
    switch ($action) {
        case 'start_workflow':
            $lot_number = $input['lot_number'] ?? '';
            $workflow_type = $input['workflow_type'] ?? 'dried_fig_production';
            
            if (empty($lot_number)) {
                echo json_encode(['error' => 'Lot number required']);
                exit();
            }
            
            $workflow_id = startWorkflow($pdo, $lot_number, $workflow_type);
            
            echo json_encode([
                'success' => true,
                'message' => "Workflow başlatıldı: {$lot_number}",
                'workflow_id' => $workflow_id,
                'lot_number' => $lot_number
            ]);
            break;
            
        case 'get_active_tasks':
            $assigned_to = $input['assigned_to'] ?? $_SESSION['user_email'] ?? 'kalite@company.com';
            
            $stmt = $pdo->prepare("
                SELECT t.*, w.progress_percentage, w.sla_status 
                FROM brc_tasks t
                LEFT JOIN brc_process_workflows w ON t.lot_number = w.lot_number
                WHERE t.assigned_to = ? AND t.status IN ('pending', 'in_progress')
                ORDER BY t.priority DESC, t.deadline ASC
            ");
            $stmt->execute([$assigned_to]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($tasks as &$task) {
                $task['checklist'] = json_decode($task['checklist'], true);
                $task['actual_values'] = json_decode($task['actual_values'], true);
            }
            
            echo json_encode([
                'success' => true,
                'tasks' => $tasks
            ]);
            break;
            
        case 'start_task':
            $task_id = $input['task_id'] ?? 0;
            
            $stmt = $pdo->prepare("
                UPDATE brc_tasks 
                SET status = 'in_progress', started_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([$task_id]);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Task başlatıldı' : 'Task başlatılamadı'
            ]);
            break;
            
        case 'complete_task':
            $task_id = $input['task_id'] ?? 0;
            $completion_data = $input['completion_data'] ?? [];
            
            $result = completeTask($pdo, $task_id, $completion_data);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Task tamamlandı' : 'Task tamamlanamadı'
            ]);
            break;
            
        case 'get_lot_status':
            $lot_number = $input['lot_number'] ?? '';
            
            // Workflow durumu
            $stmt = $pdo->prepare("SELECT * FROM brc_process_workflows WHERE lot_number = ?");
            $stmt->execute([$lot_number]);
            $workflow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Tasks
            $stmt = $pdo->prepare("SELECT * FROM brc_tasks WHERE lot_number = ? ORDER BY created_at");
            $stmt->execute([$lot_number]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Quality checks
            $stmt = $pdo->prepare("SELECT * FROM brc_quality_checks WHERE lot_number = ? ORDER BY checked_at");
            $stmt->execute([$lot_number]);
            $quality_checks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'lot_number' => $lot_number,
                'workflow' => $workflow,
                'tasks' => $tasks,
                'quality_checks' => $quality_checks
            ]);
            break;
            
        case 'get_dashboard':
            $company_id = $_SESSION['company_id'] ?? 1;
            
            // KPI'lar
            $stats = [];
            
            // Aktif workflow'lar
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM brc_process_workflows WHERE status = 'active'");
            $stmt->execute();
            $stats['active_workflows'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Bekleyen task'lar
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM brc_tasks WHERE status = 'pending'");
            $stmt->execute();
            $stats['pending_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // SLA uygunluk
            $stmt = $pdo->prepare("SELECT AVG(CASE WHEN sla_met = 1 THEN 1 ELSE 0 END) * 100 as percentage FROM brc_tasks WHERE completed_at IS NOT NULL");
            $stmt->execute();
            $stats['sla_compliance'] = round($stmt->fetch(PDO::FETCH_ASSOC)['percentage'], 1);
            
            // Kalite uygunsuzluklar
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM brc_quality_checks WHERE is_compliant = 0 AND checked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $stmt->execute();
            $stats['quality_issues'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo json_encode([
                'success' => true,
                'dashboard_stats' => $stats
            ]);
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action: ' . $action]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
?>