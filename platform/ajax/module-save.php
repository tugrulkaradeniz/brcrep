<?php
// platform/ajax/module-save.php - Real Module Saving API

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Admin authentication check
if (!isset($_SESSION['platform_admin_id'])) {
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
    
    if (!$input) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = ['module_name', 'description', 'category', 'price', 'components'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Generate module code
    $moduleCode = strtolower(str_replace([' ', '-', '_'], '_', $input['module_name'])) . '_v' . ($input['version'] ?? '1.0');
    $moduleCode = preg_replace('/[^a-z0-9_]/', '', $moduleCode);

    $pdo->beginTransaction();

    // 1. Save main module
    $moduleStmt = $pdo->prepare("
        INSERT INTO modules (
            module_name, module_code, description, category, price, version, 
            status, created_by, features, tags, icon_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $features = json_encode($input['features'] ?? []);
    $tags = json_encode($input['tags'] ?? []);
    $version = $input['version'] ?? '1.0';
    $status = $input['publish_immediately'] ?? false ? 'published' : 'draft';

    $moduleStmt->execute([
        $input['module_name'],
        $moduleCode,
        $input['description'],
        $input['category'],
        floatval($input['price']),
        $version,
        $status,
        $_SESSION['platform_admin_id'],
        $features,
        $tags,
        $input['icon_url'] ?? null
    ]);

    $moduleId = $pdo->lastInsertId();

    // 2. Save module components
    $componentStmt = $pdo->prepare("
        INSERT INTO module_components (
            module_id, component_type, component_data, component_html, 
            position_order, tab_name
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($input['components'] as $index => $component) {
        $componentData = json_encode($component['properties'] ?? []);
        $componentHtml = generateComponentHtml($component['type'], $component['properties'] ?? []);
        
        $componentStmt->execute([
            $moduleId,
            $component['type'],
            $componentData,
            $componentHtml,
            $component['order'] ?? $index,
            $component['tab'] ?? 'main'
        ]);
    }

    // 3. Create version snapshot
    $versionStmt = $pdo->prepare("
        INSERT INTO module_versions (module_id, version, changelog, component_data)
        VALUES (?, ?, ?, ?)
    ");

    $versionStmt->execute([
        $moduleId,
        $version,
        $input['changelog'] ?? 'Initial version',
        json_encode($input['components'])
    ]);

    // 4. Add to marketplace if published
    $marketplaceId = null;
    if ($status === 'published') {
        $marketplaceStmt = $pdo->prepare("
            INSERT INTO marketplace_modules (
                name, module_name, module_code, description, category, price, 
                currency, version, icon, status, created_by, source_module_id, 
                auto_generated, is_base_module, is_featured
            ) VALUES (?, ?, ?, ?, ?, ?, 'USD', ?, 'puzzle-piece', 'published', ?, ?, TRUE, 0, 0)
        ");

        $marketplaceStmt->execute([
            $input['module_name'], // name
            $input['module_name'], // module_name  
            $moduleCode,          // module_code
            $input['description'], // description
            $input['category'],   // category
            floatval($input['price']), // price
            $version,             // version
            $_SESSION['platform_admin_id'], // created_by
            $moduleId             // source_module_id
        ]);

        $marketplaceId = $pdo->lastInsertId();
    }

    $pdo->commit();

    // Generate module file
    $moduleFilePath = generateModuleFile($moduleId, $moduleCode, $input);

    // Response
    echo json_encode([
        'success' => true,
        'module_id' => $moduleId,
        'module_code' => $moduleCode,
        'module_file' => $moduleFilePath,
        'marketplace_id' => $marketplaceId,
        'status' => $status,
        'message' => $status === 'published' 
            ? 'Module published successfully to marketplace!' 
            : 'Module saved as draft'
    ]);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollback();
    }
    
    error_log("Module Save Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Helper function to generate component HTML
function generateComponentHtml($componentType, $properties) {
    $templates = [
        'label' => '<span class="brc-label {{style}}">{{text}}</span>',
        'smart-form' => '<div class="brc-smart-form"><h5>{{title}}</h5></div>',
        'timeline' => '<div class="timeline-container">{{items}}</div>',
        'kpi-cards' => '<div class="kpi-grid">{{cards}}</div>',
        'risk-matrix' => '<div class="risk-matrix-container">5x5 Risk Matrix</div>'
    ];

    $template = $templates[$componentType] ?? '<div class="component-placeholder">Component</div>';
    
    // Replace placeholders with actual properties
    foreach ($properties as $key => $value) {
        if (is_string($value)) {
            $placeholder = '{{' . $key . '}}';
            $template = str_replace($placeholder, htmlspecialchars($value), $template);
        }
    }

    return $template;
}

// Helper function to generate module file
function generateModuleFile($moduleId, $moduleCode, $moduleData) {
    $moduleDir = __DIR__ . '/../../customer/modules/';
    
    // Create modules directory if it doesn't exist
    if (!is_dir($moduleDir)) {
        mkdir($moduleDir, 0755, true);
    }

    $moduleFile = $moduleDir . $moduleCode . '.php';
    
    $moduleTemplate = generateModuleTemplate($moduleData, $moduleCode);
    
    if (file_put_contents($moduleFile, $moduleTemplate) === false) {
        throw new Exception('Failed to create module file');
    }
    
    return $moduleFile;
}

// Generate complete module PHP file
function generateModuleTemplate($moduleData, $moduleCode) {
    $moduleName = htmlspecialchars($moduleData['module_name']);
    $description = htmlspecialchars($moduleData['description']);
    $components = $moduleData['components'] ?? [];
    
    $componentHtml = '';
    foreach ($components as $component) {
        $componentHtml .= generateComponentSection($component);
    }

    $currentDate = date('Y-m-d H:i:s');

    return <<<PHP
<?php
// Auto-generated module: {$moduleName}
// Generated at: {$currentDate}
// Module code: {$moduleCode}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset(\$_SESSION['company_user_id'])) {
    header('Location: ?page=login');
    exit;
}

\$companyId = \$_SESSION['company_id'];
\$userId = \$_SESSION['company_user_id'];

try {
    \$pdo = new PDO("mysql:host=localhost;dbname=brcload_platform;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    \$companyStmt = \$pdo->prepare("SELECT name, theme_color FROM companies WHERE id = ?");
    \$companyStmt->execute([\$companyId]);
    \$company = \$companyStmt->fetch() ?: ['name' => 'Company', 'theme_color' => '#007bff'];
} catch (PDOException \$e) {
    \$company = ['name' => 'Company', 'theme_color' => '#007bff'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$moduleName} - <?= htmlspecialchars(\$company['name']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/brcproject/assets/css/customer.css" rel="stylesheet">
    <style>
        :root {
            --company-color: <?= \$company['theme_color'] ?>;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .module-container {
            background: white;
            border-radius: 20px;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .module-header {
            background: linear-gradient(135deg, var(--company-color), #0056b3);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
        }
        .brc-label {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 0.5rem 0;
        }
        .brc-label.warning { background: linear-gradient(135deg, #ffc107, #fd7e14); color: black; }
        .brc-label.danger { background: linear-gradient(135deg, #dc3545, #c82333); }
        .brc-smart-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid #e9ecef;
            margin: 1rem 0;
        }
        .timeline-container {
            position: relative;
            padding: 2rem 0;
        }
        .timeline-line {
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #007bff, #28a745);
            border-radius: 2px;
        }
        .timeline-item {
            position: relative;
            padding-left: 80px;
            margin: 1.5rem 0;
        }
        .timeline-marker {
            position: absolute;
            left: 20px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
        }
        .timeline-content {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <?php include 'customer/layout/header.php'; ?>
    
    <div class="container">
        <div class="module-container">
            <div class="module-header">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="fas fa-cube me-3"></i>{$moduleName}
                </h1>
                <p class="lead mb-0">{$description}</p>
                <div class="mt-3">
                    <a href="?page=marketplace" class="btn btn-light">
                        <i class="fas fa-store me-2"></i>Marketplace
                    </a>
                    <a href="?page=modules" class="btn btn-outline-light">
                        <i class="fas fa-th-large me-2"></i>My Modules
                    </a>
                </div>
            </div>
            
            <div class="p-4">
                {$componentHtml}
                
                <div class="mt-4 text-center">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Module Information</h6>
                        <p class="mb-0">This module was created with the BRC Module Builder and is ready for use.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'customer/layout/footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('{$moduleName} module loaded successfully');
        });
    </script>
</body>
</html>
PHP;
}

function generateComponentSection($component) {
    $type = $component['type'] ?? 'unknown';
    $props = $component['properties'] ?? [];
    
    switch ($type) {
        case 'kpi-cards':
            return generateKpiCards($props);
        case 'smart-form':
            return generateSmartForm($props);
        case 'timeline':
            return generateTimeline($props);
        case 'label':
            return generateLabel($props);
        default:
            return '<div class="alert alert-secondary mb-3">Component: ' . htmlspecialchars($type) . '</div>';
    }
}

function generateKpiCards($props) {
    $cards = $props['cards'] ?? [];
    if (empty($cards)) {
        $cards = [
            ['title' => 'Total Items', 'value' => '150', 'color' => 'primary'],
            ['title' => 'Completed', 'value' => '89%', 'color' => 'success'],
            ['title' => 'Pending', 'value' => '12', 'color' => 'warning']
        ];
    }
    
    $html = '<div class="row mb-4">';
    foreach ($cards as $card) {
        $color = htmlspecialchars($card['color'] ?? 'primary');
        $title = htmlspecialchars($card['title'] ?? 'KPI');
        $value = htmlspecialchars($card['value'] ?? '0');
        
        $html .= <<<HTML
<div class="col-md-3 mb-3">
    <div class="card text-center border-0 bg-{$color} text-white">
        <div class="card-body">
            <h3>{$value}</h3>
            <small>{$title}</small>
        </div>
    </div>
</div>
HTML;
    }
    $html .= '</div>';
    
    return $html;
}

function generateSmartForm($props) {
    $title = htmlspecialchars($props['title'] ?? 'Form');
    $fields = $props['fields'] ?? [];
    
    $fieldsHtml = '';
    if (empty($fields)) {
        $fieldsHtml = '
            <div class="mb-3">
                <label class="form-label">Sample Field</label>
                <input type="text" class="form-control" placeholder="Enter value">
            </div>
            <button class="btn btn-primary">Submit</button>
        ';
    } else {
        foreach ($fields as $field) {
            $fieldsHtml .= generateFormField($field);
        }
        $fieldsHtml .= '<button class="btn btn-primary">Submit Form</button>';
    }
    
    return <<<HTML
<div class="brc-smart-form">
    <h5><i class="fas fa-wpforms text-primary me-2"></i>{$title}</h5>
    <form>{$fieldsHtml}</form>
</div>
HTML;
}

function generateFormField($field) {
    $type = $field['type'] ?? 'text';
    $label = htmlspecialchars($field['label'] ?? 'Field');
    
    switch ($type) {
        case 'text':
            return '<div class="mb-3"><label class="form-label">' . $label . '</label><input type="text" class="form-control"></div>';
        case 'select':
            $options = '';
            if (isset($field['options']) && is_array($field['options'])) {
                foreach ($field['options'] as $opt) {
                    $options .= '<option>' . htmlspecialchars($opt) . '</option>';
                }
            }
            return '<div class="mb-3"><label class="form-label">' . $label . '</label><select class="form-select">' . $options . '</select></div>';
        case 'textarea':
            return '<div class="mb-3"><label class="form-label">' . $label . '</label><textarea class="form-control" rows="3"></textarea></div>';
        default:
            return '<div class="mb-3"><label class="form-label">' . $label . '</label><input type="text" class="form-control"></div>';
    }
}

function generateTimeline($props) {
    $items = $props['items'] ?? [];
    if (empty($items)) {
        $items = [
            ['title' => 'Process Started', 'status' => 'completed', 'date' => date('Y-m-d')],
            ['title' => 'In Progress', 'status' => 'current', 'date' => ''],
            ['title' => 'Completion', 'status' => 'pending', 'date' => '']
        ];
    }
    
    $html = '<div class="timeline-container"><div class="timeline-line"></div>';
    
    foreach ($items as $item) {
        $title = htmlspecialchars($item['title'] ?? 'Step');
        $date = htmlspecialchars($item['date'] ?? 'TBD');
        $status = $item['status'] ?? 'pending';
        
        $html .= <<<HTML
<div class="timeline-item">
    <div class="timeline-marker">
        <i class="fas fa-check"></i>
    </div>
    <div class="timeline-content">
        <h6>{$title}</h6>
        <small class="text-muted">{$date}</small>
    </div>
</div>
HTML;
    }
    
    return $html . '</div>';
}

function generateLabel($props) {
    $text = htmlspecialchars($props['text'] ?? 'Label');
    $style = $props['style'] ?? 'default';
    
    return '<div class="mb-3"><span class="brc-label ' . $style . '">' . $text . '</span></div>';
}
?>