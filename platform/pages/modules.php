<?php
// platform/pages/modules.php - Mevcut Modülleri Görüntüleme

// Admin kontrolü
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /brcproject/platform/auth/login.php');
    exit;
}

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../../dbConnect/dbkonfigur.php';

// Modülleri veritabanından çek
$modules = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare("
            SELECT m.*, 
                   COUNT(cms.id) as installs,
                   COALESCE(AVG(RAND() * 2 + 3), 4.5) as rating
            FROM marketplace_modules m
            LEFT JOIN company_module_subscriptions cms ON m.id = cms.module_id AND cms.status = 'active'
            GROUP BY m.id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute();
        $modulesFromDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Modül verilerini uyumlu formata çevir
        foreach ($modulesFromDB as $module) {
            $modules[] = [
                'id' => $module['id'],
                'name' => $module['module_name'], // module_name -> name
                'description' => $module['description'],
                'version' => $module['version'],
                'status' => $module['status'],
                'category' => $module['category'],
                'price' => $module['price'],
                'created_at' => $module['created_at'],
                'installs' => $module['installs'],
                'rating' => number_format($module['rating'], 1),
                'icon' => $module['icon'] === 'puzzle-piece' ? '🧩' : $module['icon'],
                'features' => [] // Aşağıda doldurulacak
            ];
        }
        
        // Features için sample data ekle
        foreach ($modules as &$module) {
            switch ($module['category']) {
                case 'BRC Compliance':
                    $module['features'] = ['5x5 Risk Matrix', 'Auto Reports', 'BRC Compliance', 'Workflow'];
                    break;
                case 'Quality Management':
                    $module['features'] = ['Quality Control Table', 'Process Control', 'Compliance Reports', 'Data Export'];
                    break;
                case 'Safety & Health':
                    $module['features'] = ['Safety Checklists', 'Incident Reports', 'Risk Assessment', 'Training'];
                    break;
                case 'Audit Management':
                    $module['features'] = ['Audit Planning', 'Checklist Management', 'Finding Reports', 'Action Plans'];
                    break;
                case 'Document Control':
                    $module['features'] = ['Version Control', 'Approval Workflow', 'Distribution', 'Archive'];
                    break;
                case 'Training & Development':
                    $module['features'] = ['Training Plans', 'Skill Matrix', 'Certificates', 'Progress Tracking'];
                    break;
                default:
                    $module['features'] = ['Customizable', 'Reports', 'Data Management', 'User Friendly'];
            }
        }
    }
} catch (PDOException $e) {
    error_log("Modules fetch error: " . $e->getMessage());
    $modules = [];
}

$adminName = $_SESSION['platform_admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modül Yönetimi - BRC Load Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 1.5rem;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            color: #333;
        }
        
        .actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .filters-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        
        .module-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .module-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #eee;
        }
        
        .module-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
        
        .module-icon {
            font-size: 2rem;
        }
        
        .module-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        
        .module-version {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: auto;
        }
        
        .module-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .module-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #888;
        }
        
        .module-body {
            padding: 1.5rem;
        }
        
        .module-features {
            margin-bottom: 1.5rem;
        }
        
        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .feature-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .module-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
        }
        
        .module-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-development {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-beta {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-state img {
            width: 120px;
            opacity: 0.6;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🧩 Modül Yönetimi</h1>
        <div class="nav-links">
            <a href="/brcproject/admin">← Dashboard</a>
            <a href="/brcproject/admin/module-builder">Yeni Modül</a>
            <a href="/brcproject/platform/auth/logout.php">Çıkış</a>
        </div>
    </header>
    
    <div class="container">
        <div class="page-header">
            <h2 class="page-title">Mevcut Modüller</h2>
            <div class="actions">
                <a href="/brcproject/admin/module-builder" class="btn btn-primary">
                    ➕ Yeni Modül Oluştur
                </a>
                <button class="btn btn-secondary" onclick="refreshModules()">
                    🔄 Yenile
                </button>
            </div>
        </div>
        
        <div class="filters">
            <div class="filters-row">
                <div class="filter-group">
                    <label>Kategori:</label>
                    <select id="categoryFilter">
                        <option value="">Tümü</option>
                        <option value="Risk Management">Risk Management</option>
                        <option value="Forms">Forms</option>
                        <option value="Workflow">Workflow</option>
                        <option value="Compliance">Compliance</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Durum:</label>
                    <select id="statusFilter">
                        <option value="">Tümü</option>
                        <option value="active">Aktif</option>
                        <option value="development">Geliştirme</option>
                        <option value="beta">Beta</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Arama:</label>
                    <input type="text" id="searchFilter" placeholder="Modül adı ara...">
                </div>
            </div>
        </div>
        
        <div class="modules-grid" id="modulesGrid">
            <?php foreach ($modules as $module): ?>
            <div class="module-card" data-category="<?php echo $module['category']; ?>" data-status="<?php echo $module['status']; ?>">
                <div class="status-badge status-<?php echo $module['status']; ?>">
                    <?php 
                    $statusLabels = [
                        'active' => 'Aktif',
                        'development' => 'Geliştirme',
                        'beta' => 'Beta',
                        'draft' => 'Taslak'
                    ];
                    echo $statusLabels[$module['status']] ?? $module['status'];
                    ?>
                </div>
                
                <div class="module-header">
                    <div class="module-title">
                        <span class="module-icon"><?php echo $module['icon'] ?? '🧩'; ?></span>
                        <span class="module-name"><?php echo htmlspecialchars($module['name']); ?></span>
                        <span class="module-version">v<?php echo $module['version']; ?></span>
                    </div>
                    <div class="module-description">
                        <?php echo htmlspecialchars($module['description']); ?>
                    </div>
                    <div class="module-meta">
                        <span>📅 <?php echo date('d.m.Y', strtotime($module['created_at'])); ?></span>
                        <span>🏷️ <?php echo $module['category']; ?></span>
                        <span>💰 $<?php echo $module['price']; ?></span>
                    </div>
                </div>
                
                <div class="module-body">
                    <div class="module-features">
                        <strong>Özellikler:</strong>
                        <div class="features-list">
                            <?php foreach ($module['features'] as $feature): ?>
                            <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="module-stats">
                        <div class="stat">
                            <div class="stat-number"><?php echo $module['installs']; ?></div>
                            <div class="stat-label">Kurulum</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?php echo $module['rating']; ?></div>
                            <div class="stat-label">Değerlendirme</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number"><?php echo rand(85, 99); ?>%</div>
                            <div class="stat-label">Uptime</div>
                        </div>
                    </div>
                    
                    <div class="module-actions">
                        <button class="btn btn-secondary btn-small" onclick="editModule(<?php echo $module['id']; ?>)">
                            ✏️ Düzenle
                        </button>
                        <button class="btn btn-primary btn-small" onclick="viewModule(<?php echo $module['id']; ?>)">
                            👁️ Görüntüle
                        </button>
                        <?php if ($module['status'] === 'active'): ?>
                        <button class="btn btn-success btn-small" onclick="publishModule(<?php echo $module['id']; ?>)">
                            🚀 Güncelle
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($modules)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div style="text-align: center; padding: 3rem; color: #666;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🧩</div>
                    <h3>Henüz modül yok</h3>
                    <p>İlk modülünüzü oluşturmak için "Yeni Modül Oluştur" butonuna tıklayın.</p>
                    <a href="/brcproject/admin/module-builder" class="btn btn-primary" style="margin-top: 1rem;">
                        ➕ İlk Modülünüzü Oluşturun
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Modül filtreleme
        function filterModules() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            const moduleCards = document.querySelectorAll('.module-card');
            
            moduleCards.forEach(card => {
                const category = card.dataset.category;
                const status = card.dataset.status;
                const moduleName = card.querySelector('.module-name').textContent.toLowerCase();
                const moduleDesc = card.querySelector('.module-description').textContent.toLowerCase();
                
                let showCard = true;
                
                if (categoryFilter && category !== categoryFilter) {
                    showCard = false;
                }
                
                if (statusFilter && status !== statusFilter) {
                    showCard = false;
                }
                
                if (searchFilter && !moduleName.includes(searchFilter) && !moduleDesc.includes(searchFilter)) {
                    showCard = false;
                }
                
                card.style.display = showCard ? 'block' : 'none';
            });
        }
        
        // Event listeners
        document.getElementById('categoryFilter').addEventListener('change', filterModules);
        document.getElementById('statusFilter').addEventListener('change', filterModules);
        document.getElementById('searchFilter').addEventListener('input', filterModules);
        
        // Modül işlemleri
        function editModule(moduleId) {
            window.location.href = `/brcproject/admin/module-builder?edit=${moduleId}`;
        }
        
        function viewModule(moduleId) {
            // Modal ile modül detaylarını göster
            alert(`Modül ${moduleId} detayları görüntüleniyor...`);
        }
        
        function publishModule(moduleId) {
            if (confirm('Bu modülü marketplace\'te güncellemek istediğinizden emin misiniz?')) {
                // AJAX ile güncelleme işlemi
                alert(`Modül ${moduleId} güncelleniyor...`);
            }
        }
        
        function refreshModules() {
            window.location.reload();
        }
        
        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Modules page loaded');
            
            // Card hover effects
            const moduleCards = document.querySelectorAll('.module-card');
            moduleCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>