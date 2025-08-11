<?php
// Customer Marketplace Page

// Check if session is not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Include required files with correct paths
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/functions.php');

// Database connection
global $pdo;
if (!isset($pdo)) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Database connection
global $pdo;
if (!isset($pdo)) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Get company information
$companyId = $_SESSION['company_id'] ?? 1;
$companyStmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$companyStmt->execute([$companyId]);
$company = $companyStmt->fetch();

// Get all marketplace modules
$modulesStmt = $pdo->prepare("
    SELECT mm.*, 
           CASE WHEN cms.id IS NOT NULL THEN 1 ELSE 0 END as is_subscribed,
           cms.status as subscription_status
    FROM marketplace_modules mm
    LEFT JOIN company_module_subscriptions cms ON mm.id = cms.module_id AND cms.company_id = ?
    WHERE mm.status = 'published'
    ORDER BY mm.is_featured DESC, mm.created_at DESC
");
$modulesStmt->execute([$companyId]);
$modules = $modulesStmt->fetchAll();

// Get subscription stats
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_available,
        SUM(CASE WHEN cms.id IS NOT NULL THEN 1 ELSE 0 END) as subscribed,
        COUNT(CASE WHEN mm.category LIKE '%BRC%' THEN 1 END) as brc_modules
    FROM marketplace_modules mm
    LEFT JOIN company_module_subscriptions cms ON mm.id = cms.module_id AND cms.company_id = ?
    WHERE mm.status = 'published'
");
$statsStmt->execute([$companyId]);
$stats = $statsStmt->fetch();

// Calculate compliance percentage
$compliancePercentage = $stats['brc_modules'] > 0 ? round(($stats['subscribed'] / $stats['brc_modules']) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Marketplace - <?php echo htmlspecialchars($company['name'] ?? 'Company'); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?php echo $company['theme_color'] ?? '#2c5aa0'; ?>;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .marketplace-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .category-filter {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .category-btn {
            border: none;
            background: rgba(44, 90, 160, 0.1);
            border-radius: 25px;
            padding: 8px 20px;
            margin: 3px;
            transition: all 0.3s ease;
            color: var(--primary-color);
            font-weight: 500;
        }

        .category-btn:hover, .category-btn.active {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);
        }

        .module-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
            backdrop-filter: blur(10px);
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .module-icon {
            background: linear-gradient(135deg, var(--primary-color), #667eea);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .price-badge {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            border-radius: 25px;
            padding: 5px 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .free-badge {
            background: linear-gradient(135deg, var(--info-color), #6f42c1);
        }

        .featured-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .subscription-btn {
            background: linear-gradient(135deg, var(--primary-color), #667eea);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .subscription-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(44, 90, 160, 0.3);
            color: white;
        }

        .subscription-btn.subscribed {
            background: linear-gradient(135deg, var(--success-color), #20c997);
        }

        .brc-badge {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border-radius: 12px;
            padding: 3px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            border: none;
            backdrop-filter: blur(10px);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .search-box {
            border-radius: 25px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }

        .module-version {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .category-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="marketplace-header p-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-store text-primary"></i>
                        BRC Module Marketplace
                    </h1>
                    <p class="text-muted mb-0">Professional compliance modules for <?php echo htmlspecialchars($company['name'] ?? 'your business'); ?></p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $stats['total_available']; ?></div>
                                <small class="text-muted">Available Modules</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $stats['subscribed']; ?></div>
                                <small class="text-muted">Subscribed</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $compliancePercentage; ?>%</div>
                                <small class="text-muted">BRC Compliance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-md-8">
                <input type="text" class="form-control search-box" id="searchModules" placeholder="Search modules by name, category, or BRC clause...">
            </div>
            <div class="col-md-4">
                <select class="form-select search-box" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="BRC Compliance">BRC Compliance</option>
                    <option value="Food Safety">Food Safety</option>
                    <option value="Supply Chain">Supply Chain</option>
                    <option value="Product Development">Product Development</option>
                    <option value="Audit Management">Audit Management</option>
                    <option value="Training & Development">Training & Development</option>
                    <option value="Hygiene & Sanitation">Hygiene & Sanitation</option>
                    <option value="Crisis Management">Crisis Management</option>
                </select>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="category-filter">
            <div class="d-flex flex-wrap justify-content-center">
                <button class="category-btn active" data-category="">All Modules</button>
                <button class="category-btn" data-category="BRC Compliance">BRC Compliance</button>
                <button class="category-btn" data-category="Food Safety">Food Safety</button>
                <button class="category-btn" data-category="Supply Chain">Supply Chain</button>
                <button class="category-btn" data-category="Audit Management">Audit Management</button>
                <button class="category-btn" data-category="Training & Development">Training</button>
                <button class="category-btn" data-category="Security Management">Security</button>
            </div>
        </div>

        <!-- Featured Modules -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="text-white mb-3">
                    <i class="fas fa-star text-warning"></i>
                    Professional BRC Modules
                </h3>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="row" id="modulesGrid">
            <?php foreach ($modules as $module): ?>
            <div class="col-lg-4 col-md-6 mb-4 module-item" 
                 data-category="<?php echo htmlspecialchars($module['category'] ?? ''); ?>"
                 data-name="<?php echo htmlspecialchars($module['module_name']); ?>">
                <div class="card module-card">
                    <div class="card-body position-relative">
                        <?php if ($module['is_featured']): ?>
                            <span class="featured-badge">Featured</span>
                        <?php endif; ?>
                        
                        <div class="module-icon">
                            <i class="fas fa-<?php echo $module['icon'] ?? 'puzzle-piece'; ?>"></i>
                        </div>
                        
                        <h5 class="card-title"><?php echo htmlspecialchars($module['module_name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($module['description'] ?? ''); ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <?php if ($module['category']): ?>
                                <span class="badge category-badge" style="background-color: <?php 
                                    $categoryColors = [
                                        'BRC Compliance' => '#e74c3c',
                                        'Food Safety' => '#e67e22',
                                        'Supply Chain' => '#27ae60',
                                        'Product Development' => '#3498db',
                                        'Audit Management' => '#f39c12',
                                        'Training & Development' => '#9b59b6',
                                        'Hygiene & Sanitation' => '#1abc9c',
                                        'Crisis Management' => '#e74c3c',
                                        'Security Management' => '#34495e'
                                    ];
                                    echo $categoryColors[$module['category']] ?? '#6c757d';
                                ?>;"><?php echo htmlspecialchars($module['category']); ?></span>
                            <?php endif; ?>
                            
                            <?php if (stripos($module['description'], 'BRC Clause') !== false): ?>
                                <?php preg_match('/BRC Clause ([\d\.]+)/', $module['description'], $matches); ?>
                                <?php if (isset($matches[1])): ?>
                                    <span class="brc-badge">Clause <?php echo $matches[1]; ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <?php if ($module['price'] > 0): ?>
                                <span class="price-badge">$<?php echo number_format($module['price'], 2); ?>/month</span>
                            <?php else: ?>
                                <span class="price-badge free-badge">Free</span>
                            <?php endif; ?>
                            <span class="module-version">v<?php echo htmlspecialchars($module['version']); ?></span>
                        </div>
                        
                        <?php if ($module['is_subscribed']): ?>
                            <div class="d-flex gap-2">
                                <button class="subscription-btn subscribed flex-fill" 
                                        data-module-id="<?php echo $module['id']; ?>"
                                        onclick="toggleSubscription(<?php echo $module['id']; ?>, this)">
                                    <i class="fas fa-check me-2"></i>Subscribed
                                </button>
                                <a href="/brcproject/demo?page=module&module_code=<?php echo $module['module_code']; ?>" 
                                class="btn btn-success" style="border-radius: 25px; padding: 10px 15px; font-weight: 600;">
                                    <i class="fas fa-play me-1"></i>Launch
                                </a>
                            </div>
                        <?php else: ?>
                            <button class="subscription-btn" 
                                    data-module-id="<?php echo $module['id']; ?>"
                                    onclick="toggleSubscription(<?php echo $module['id']; ?>, this)">
                                <i class="fas fa-plus me-2"></i>Subscribe
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- No results message -->
        <div id="noResults" class="text-center text-white" style="display: none;">
            <i class="fas fa-search fa-3x mb-3"></i>
            <h4>No modules found</h4>
            <p>Try adjusting your search or filter criteria</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category filter functionality
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                filterModules(category, document.getElementById('searchModules').value);
            });
        });

        // Search functionality
        document.getElementById('searchModules').addEventListener('input', function(e) {
            const activeCategory = document.querySelector('.category-btn.active').dataset.category;
            filterModules(activeCategory, e.target.value);
        });

        // Category select filter
        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            const category = e.target.value;
            // Update active button
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            const matchingBtn = document.querySelector(`[data-category="${category}"]`);
            if (matchingBtn) {
                matchingBtn.classList.add('active');
            } else {
                document.querySelector('[data-category=""]').classList.add('active');
            }
            
            filterModules(category, document.getElementById('searchModules').value);
        });

        function filterModules(category, searchTerm) {
            const modules = document.querySelectorAll('.module-item');
            let visibleCount = 0;

            modules.forEach(module => {
                const moduleCategory = module.dataset.category;
                const moduleName = module.dataset.name.toLowerCase();
                const moduleText = module.textContent.toLowerCase();

                const categoryMatch = !category || moduleCategory === category;
                const searchMatch = !searchTerm || moduleName.includes(searchTerm.toLowerCase()) || moduleText.includes(searchTerm.toLowerCase());

                if (categoryMatch && searchMatch) {
                    module.style.display = 'block';
                    visibleCount++;
                } else {
                    module.style.display = 'none';
                }
            });

            // Show/hide no results message
            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Subscription toggle functionality
        function toggleSubscription(moduleId, button) {
            const isSubscribed = button.classList.contains('subscribed');
            const action = isSubscribed ? 'unsubscribe' : 'subscribe';
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            // AJAX request to toggle subscription
            fetch('/brcproject/customer/ajax/module-actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    module_id: moduleId
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Success:', data);
                if (data.success) {
                    // Update button state
                    if (action === 'subscribe') {
                        button.classList.add('subscribed');
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed';
                    } else {
                        button.classList.remove('subscribed');
                        button.innerHTML = '<i class="fas fa-plus me-2"></i>Subscribe';
                    }
                    
                    alert(data.message);
                    
                    // Update stats (optional)
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error occurred');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    </script>
</body>
</html>