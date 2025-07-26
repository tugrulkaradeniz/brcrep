<?php
// platform/pages/dashboard.php - Admin Dashboard

// Admin kontrolü
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /brcproject/platform/auth/login.php');
    exit;
}

$adminName = $_SESSION['platform_admin_name'] ?? 'Administrator';
$username = $_SESSION['platform_admin_username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BRC Load Platform</title>
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
        }
        
        .action-card h3 {
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-card p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #28a745;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🛡️ BRC Load Platform - Admin Panel</h1>
        <div class="user-info">
            <span>Hoşgeldin, <?php echo htmlspecialchars($adminName); ?></span>
            <a href="/brcproject/platform/auth/logout.php" class="btn">Çıkış Yap</a>
        </div>
    </header>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Admin Dashboard</h2>
            <p><span class="status-indicator"></span>Sistem aktif ve çalışıyor</p>
            <p><strong>Son giriş:</strong> <?php echo date('d.m.Y H:i'); ?></p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Aktif Şirket</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">12</div>
                <div class="stat-label">Kullanıcı</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Modül</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">25</div>
                <div class="stat-label">Aktif Abonelik</div>
            </div>
        </div>
        
        <div class="actions-grid">
            <div class="action-card">
                <h3>🏢 Şirket Yönetimi</h3>
                <p>Müşteri şirketleri oluşturun, düzenleyin ve yönetin. Şirket bilgileri, kullanıcılar ve abonelikleri kontrol edin.</p>
                <a href="/brcproject/admin/companies" class="action-btn">Şirketleri Yönet</a>
            </div>
            
            <div class="action-card">
                <h3>🎨 Modül Oluşturucu</h3>
                <p>Drag & Drop arayüzü ile yeni modüller oluşturun. Formlar, workflowlar ve raporlama araçları ekleyin.</p>
                <a href="/brcproject/admin/module-builder" class="action-btn">Modül Oluştur</a>
            </div>
            
            <div class="action-card">
                <h3>🧩 Modül Mağazası</h3>
                <p>Mevcut modülleri görüntüleyin, düzenleyin ve yeni modüller ekleyin. Modül kategorileri ve fiyatlandırması yönetin.</p>
                <a href="/brcproject/admin/modules" class="action-btn">Modülleri Görüntüle</a>
            </div>
            
            <div class="action-card">
                <h3>📊 Sistem İstatistikleri</h3>
                <p>Platform kullanımı, performans metrikleri ve sistem sağlığı hakkında detaylı raporlar alın.</p>
                <a href="/brcproject/admin/stats" class="action-btn">İstatistikleri Görüntüle</a>
            </div>
            
            <div class="action-card">
                <h3>⚙️ Sistem Ayarları</h3>
                <p>Platform konfigürasyonu, güvenlik ayarları ve genel sistem parametrelerini düzenleyin.</p>
                <a href="/brcproject/admin/settings" class="action-btn">Ayarları Düzenle</a>
            </div>
            
            <div class="action-card">
                <h3>🔄 Yedekleme</h3>
                <p>Veritabanı ve dosya yedeklemesi alın, geri yükleme işlemleri yapın ve yedekleme programı oluşturun.</p>
                <a href="/brcproject/admin/backup" class="action-btn">Yedekleme Yönet</a>
            </div>
        </div>
    </div>
    
    <script>
        // Basit dashboard interactions
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin Dashboard loaded successfully');
            
            // Action card hover effects
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                });
            });
        });
    </script>
</body>
</html>