<?php
/**
 * =====================================================
 * BRC LOAD PLATFORM INSTALLATION SCRIPT
 * File: install.php
 * =====================================================
 * 
 * This script sets up the complete multi-tenant SaaS platform
 * Run this once to install the entire system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

// Configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'brcload_platform',
    'db_user' => 'root',
    'db_pass' => '',
    'base_url' => 'http://localhost/brcproject',
    'admin_email' => 'admin@brcload.com',
    'admin_password' => 'admin123'
];

// Check if already installed
if (file_exists('INSTALLED.lock')) {
    die('System is already installed. Delete INSTALLED.lock file to reinstall.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRC Load Platform Installation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .install-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-step {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .install-step.active {
            background: #f0f9ff;
            border-left: 4px solid #2563eb;
        }
        .install-step.completed {
            background: #f0fdf4;
            border-left: 4px solid #16a34a;
        }
        .install-step.error {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        .step-icon.waiting { background: #f3f4f6; color: #6b7280; }
        .step-icon.active { background: #dbeafe; color: #2563eb; }
        .step-icon.completed { background: #dcfce7; color: #16a34a; }
        .step-icon.error { background: #fee2e2; color: #dc2626; }
        .progress-bar {
            transition: width 0.5s ease;
        }
        .log-output {
            background: #1f2937;
            color: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            margin-top: 1rem;
        }
        .config-form {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <!-- Header -->
        <div class="install-header">
            <h1><i class="fas fa-cubes me-3"></i>BRC Load Platform</h1>
            <p class="mb-0">Multi-Tenant SaaS Installation Wizard</p>
        </div>

        <!-- Progress Bar -->
        <div class="px-4 pt-3">
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Configuration Form -->
        <div class="install-step active" id="step-config">
            <div class="d-flex align-items-center mb-3">
                <div class="step-icon active">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <h5 class="mb-0">Configuration</h5>
                    <small class="text-muted">Setup database and admin credentials</small>
                </div>
            </div>

            <form id="configForm" class="config-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" name="db_host" value="<?= $config['db_host'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="db_name" value="<?= $config['db_name'] ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Database User</label>
                            <input type="text" class="form-control" name="db_user" value="<?= $config['db_user'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="db_pass" value="<?= $config['db_pass'] ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Admin Email</label>
                            <input type="email" class="form-control" name="admin_email" value="<?= $config['admin_email'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Admin Password</label>
                            <input type="password" class="form-control" name="admin_password" value="<?= $config['admin_password'] ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-play me-2"></i>Start Installation
                </button>
            </form>
        </div>

        <!-- Installation Steps -->
        <div class="install-step" id="step-database">
            <div class="d-flex align-items-center">
                <div class="step-icon waiting">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h5 class="mb-0">Database Setup</h5>
                    <small class="text-muted">Creating database and tables</small>
                </div>
            </div>
        </div>

        <div class="install-step" id="step-structure">
            <div class="d-flex align-items-center">
                <div class="step-icon waiting">
                    <i class="fas fa-folder"></i>
                </div>
                <div>
                    <h5 class="mb-0">File Structure</h5>
                    <small class="text-muted">Creating directories and config files</small>
                </div>
            </div>
        </div>

        <div class="install-step" id="step-demo-data">
            <div class="d-flex align-items-center">
                <div class="step-icon waiting">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h5 class="mb-0">Demo Data</h5>
                    <small class="text-muted">Creating demo companies and modules</small>
                </div>
            </div>
        </div>

        <div class="install-step" id="step-permissions">
            <div class="d-flex align-items-center">
                <div class="step-icon waiting">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h5 class="mb-0">Permissions</h5>
                    <small class="text-muted">Setting up file permissions</small>
                </div>
            </div>
        </div>

        <div class="install-step" id="step-complete">
            <div class="d-flex align-items-center">
                <div class="step-icon waiting">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h5 class="mb-0">Installation Complete</h5>
                    <small class="text-muted">System ready to use</small>
                </div>
            </div>
        </div>

        <!-- Log Output -->
        <div class="p-4" id="logContainer" style="display: none;">
            <h6>Installation Log</h6>
            <div class="log-output" id="logOutput">
                Starting installation...<br>
            </div>
        </div>

        <!-- Success Message -->
        <div class="p-4 text-center" id="successMessage" style="display: none;">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            <h3 class="text-success mb-3">Installation Successful!</h3>
            <p class="text-muted mb-4">Your BRC Load Platform is now ready to use.</p>
            
            <div class="row text-start">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-cog me-2"></i>Platform Admin
                            </h6>
                            <p class="card-text">
                                <strong>URL:</strong> <a href="<?= $config['base_url'] ?>?page=admin" target="_blank"><?= $config['base_url'] ?>?page=admin</a><br>
                                <strong>Email:</strong> admin@brcload.com<br>
                                <strong>Password:</strong> admin123
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-building me-2"></i>Demo Company
                            </h6>
                            <p class="card-text">
                                <strong>URL:</strong> <a href="<?= $config['base_url'] ?>/demo" target="_blank"><?= $config['base_url'] ?>/demo</a><br>
                                <strong>Username:</strong> admin<br>
                                <strong>Password:</strong> password123
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 0;
        const steps = ['config', 'database', 'structure', 'demo-data', 'permissions', 'complete'];
        
        document.getElementById('configForm').addEventListener('submit', function(e) {
            e.preventDefault();
            startInstallation();
        });

        function startInstallation() {
            // Hide config form
            document.getElementById('step-config').style.display = 'none';
            
            // Show log container
            document.getElementById('logContainer').style.display = 'block';
            
            // Start installation process
            executeStep(1);
        }

        function executeStep(stepIndex) {
            if (stepIndex >= steps.length) {
                showSuccess();
                return;
            }

            const stepId = steps[stepIndex];
            const stepElement = document.getElementById(`step-${stepId}`);
            const iconElement = stepElement.querySelector('.step-icon');
            
            // Mark as active
            stepElement.classList.add('active');
            iconElement.classList.remove('waiting');
            iconElement.classList.add('active');
            
            // Update progress
            updateProgress((stepIndex / (steps.length - 1)) * 100);
            
            // Log step
            logMessage(`Starting ${stepId.replace('-', ' ')}...`);
            
            // Simulate step execution
            setTimeout(() => {
                // Mark as completed
                stepElement.classList.remove('active');
                stepElement.classList.add('completed');
                iconElement.classList.remove('active');
                iconElement.classList.add('completed');
                iconElement.innerHTML = '<i class="fas fa-check"></i>';
                
                logMessage(`✓ ${stepId.replace('-', ' ')} completed successfully`);
                
                // Execute next step
                setTimeout(() => executeStep(stepIndex + 1), 500);
            }, 2000 + Math.random() * 1000);
        }

        function updateProgress(percent) {
            document.getElementById('progressBar').style.width = percent + '%';
        }

        function logMessage(message) {
            const logOutput = document.getElementById('logOutput');
            const timestamp = new Date().toLocaleTimeString();
            logOutput.innerHTML += `[${timestamp}] ${message}<br>`;
            logOutput.scrollTop = logOutput.scrollHeight;
        }

        function showSuccess() {
            document.getElementById('logContainer').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
            updateProgress(100);
        }
    </script>
</body>
</html>

<?php
// Installation Process (when called via AJAX)
if ($_POST && $_POST['action'] === 'install') {
    header('Content-Type: application/json');
    
    try {
        $config = $_POST;
        
        // Step 1: Database Setup
        $pdo = new PDO(
            "mysql:host={$config['db_host']}", 
            $config['db_user'], 
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$config['db_name']}`");
        
        // Create tables
        createTables($pdo);
        
        // Step 2: Create file structure
        createFileStructure($config);
        
        // Step 3: Insert demo data
        insertDemoData($pdo, $config);
        
        // Step 4: Set permissions
        setPermissions();
        
        // Create lock file
        file_put_contents('INSTALLED.lock', date('Y-m-d H:i:s'));
        
        echo json_encode(['success' => true, 'message' => 'Installation completed successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

function createTables($pdo) {
    $sql = file_get_contents('install/database.sql');
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
}

function createFileStructure($config) {
    $dirs = [
        'platform/auth',
        'platform/pages',
        'platform/ajax',
        'platform/layout',
        'customer/auth',
        'customer/pages',
        'customer/ajax',
        'customer/layout',
        'customer/modules',
        'website/pages',
        'models',
        'services',
        'controllers',
        'middleware',
        'config',
        'assets/css',
        'assets/js',
        'assets/images',
        'uploads',
        'logs'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Create config file
    $configContent = "<?php\n";
    $configContent .= "// BRC Load Platform Configuration\n";
    $configContent .= "define('DB_HOST', '{$config['db_host']}');\n";
    $configContent .= "define('DB_NAME', '{$config['db_name']}');\n";
    $configContent .= "define('DB_USER', '{$config['db_user']}');\n";
    $configContent .= "define('DB_PASS', '{$config['db_pass']}');\n";
    $configContent .= "define('BASE_URL', '{$config['base_url']}');\n";
    $configContent .= "?>";
    
    file_put_contents('config/config.php', $configContent);
}

function insertDemoData($pdo, $config) {
    // Insert platform admin
    $adminPassword = password_hash($config['admin_password'], PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO platform_admins (username, email, password, first_name, last_name, role) VALUES
        ('admin', '{$config['admin_email']}', '$adminPassword', 'Platform', 'Admin', 'super_admin')
    ");
    
    // Insert demo companies
    $pdo->exec("
        INSERT INTO companies (company_name, subdomain, contact_name, contact_email, plan_type, status) VALUES
        ('Demo Company Ltd.', 'demo', 'John Doe', 'john@democompany.com', 'premium', 'active'),
        ('Test Corp', 'test', 'Jane Smith', 'jane@testcorp.com', 'basic', 'trial')
    ");
    
    // Insert demo company users
    $userPassword = password_hash('password123', PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO company_users (company_id, username, email, password, first_name, last_name, role) VALUES
        (1, 'admin', 'admin@democompany.com', '$userPassword', 'Company', 'Admin', 'owner'),
        (1, 'user1', 'user1@democompany.com', '$userPassword', 'Regular', 'User', 'user'),
        (2, 'admin', 'admin@testcorp.com', '$userPassword', 'Test', 'Admin', 'owner')
    ");
    
    // Insert demo modules
    $pdo->exec("
        INSERT INTO marketplace_modules (module_name, module_code, description, category, price, is_base_module, status, created_by) VALUES
        ('BRC Risk Assessment', 'brc_risk_assessment', 'Complete BRC compliant risk assessment module', 'BRC Compliance', 99.99, TRUE, 'published', 1),
        ('Quality Management', 'quality_mgmt', 'ISO 9001 compliant quality management system', 'Quality Management', 79.99, TRUE, 'published', 1),
        ('Safety Management', 'safety_mgmt', 'OHSAS 18001 safety management system', 'Safety & Health', 89.99, TRUE, 'published', 1)
    ");
    
    // Insert demo subscriptions
    $pdo->exec("
        INSERT INTO company_module_subscriptions (company_id, module_id, expires_at) VALUES
        (1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR)),
        (1, 2, DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR)),
        (2, 1, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY))
    ");
}

function setPermissions() {
    $dirs = ['uploads', 'logs'];
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            chmod($dir, 0777);
        }
    }
}
?>