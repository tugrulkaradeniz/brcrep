<?php
// platform/layout/sidebar.php
$current_page = basename($_SERVER['REQUEST_URI']);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-crown"></i> BRC Platform</h3>
        <small class="text-muted">Admin Panel</small>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($current_page, 'dashboard') !== false) ? 'active' : '' ?>" 
                   href="?page=dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                    <span class="badge badge-primary">Live</span>
                </a>
            </li>
            
            <!-- Divider -->
            <li class="nav-divider">
                <hr class="sidebar-divider">
                <small class="text-muted">MANAGEMENT</small>
            </li>
            
            <!-- Companies -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($current_page, 'companies') !== false) ? 'active' : '' ?>" 
                   href="?page=companies">
                    <i class="fas fa-building"></i>
                    <span>Companies</span>
                    <span class="badge badge-info" id="companies-count">0</span>
                </a>
            </li>
            
            <!-- Modules -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($current_page, 'modules') !== false && strpos($current_page, 'module-builder') === false) ? 'active' : '' ?>" 
                   href="?page=modules">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Modules</span>
                    <span class="badge badge-success" id="modules-count">0</span>
                </a>
            </li>
            
            <!-- Divider -->
            <li class="nav-divider">
                <hr class="sidebar-divider">
                <small class="text-muted">DEVELOPMENT</small>
            </li>
            
            <!-- Module Builder -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($current_page, 'module-builder') !== false) ? 'active' : '' ?>" 
                   href="?page=module-builder">
                    <i class="fas fa-magic"></i>
                    <span>Module Builder</span>
                    <span class="badge badge-warning">Pro</span>
                </a>
            </li>
            
            <!-- Templates -->
            <li class="nav-item">
                <a class="nav-link" href="?page=templates">
                    <i class="fas fa-layer-group"></i>
                    <span>Templates</span>
                    <span class="badge badge-secondary">New</span>
                </a>
            </li>
            
            <!-- API Management -->
            <li class="nav-item">
                <a class="nav-link" href="?page=api">
                    <i class="fas fa-code"></i>
                    <span>API Management</span>
                </a>
            </li>
            
            <!-- Divider -->
            <li class="nav-divider">
                <hr class="sidebar-divider">
                <small class="text-muted">ANALYTICS</small>
            </li>
            
            <!-- Analytics -->
            <li class="nav-item">
                <a class="nav-link" href="?page=analytics">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
            </li>
            
            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link" href="?page=reports">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </li>
            
            <!-- Revenue -->
            <li class="nav-item">
                <a class="nav-link" href="?page=revenue">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Revenue</span>
                </a>
            </li>
            
            <!-- Divider -->
            <li class="nav-divider">
                <hr class="sidebar-divider">
                <small class="text-muted">SYSTEM</small>
            </li>
            
            <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link" href="?page=settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            
            <!-- Logs -->
            <li class="nav-item">
                <a class="nav-link" href="?page=logs">
                    <i class="fas fa-list-alt"></i>
                    <span>System Logs</span>
                </a>
            </li>
            
            <!-- Backup -->
            <li class="nav-item">
                <a class="nav-link" href="?page=backup">
                    <i class="fas fa-download"></i>
                    <span>Backup</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- User Info -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-details">
                <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                <small class="d-block text-muted">Platform Administrator</small>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions mt-3">
            <a href="?page=profile" class="btn btn-sm btn-outline-light" title="Profile">
                <i class="fas fa-user"></i>
            </a>
            <a href="?page=notifications" class="btn btn-sm btn-outline-light" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge badge-danger badge-sm">3</span>
            </a>
            <a href="?page=logout" class="btn btn-sm btn-outline-danger" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<!-- Sidebar Toggle for Mobile -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<style>
.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-height: 100vh;
    padding: 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: bold;
}

.sidebar-menu {
    padding: 1rem 0;
}

.nav-link {
    color: rgba(255,255,255,0.8) !important;
    padding: 0.75rem 1.5rem;
    border: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white !important;
    transform: translateX(5px);
}

.nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white !important;
    border-right: 3px solid #ffd700;
}

.nav-link i {
    width: 20px;
    margin-right: 12px;
    text-align: center;
}

.nav-link span:first-of-type {
    flex: 1;
}

.nav-link .badge {
    margin-left: auto;
    font-size: 0.7rem;
}

.nav-divider {
    padding: 0.5rem 1.5rem;
}

.sidebar-divider {
    border-color: rgba(255,255,255,0.2);
    margin: 0.5rem 0;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.quick-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.quick-actions .btn {
    padding: 0.4rem 0.8rem;
    border-radius: 50px;
    position: relative;
}

.badge-sm {
    position: absolute;
    top: -5px;
    right: -5px;
    padding: 2px 6px;
    font-size: 0.6rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: -280px;
        width: 280px;
        z-index: 1050;
        transition: left 0.3s ease;
    }
    
    .sidebar.show {
        left: 0;
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }
}
</style>

<script>
// Update badges with real-time counts
document.addEventListener('DOMContentLoaded', function() {
    updateSidebarCounts();
});

function updateSidebarCounts() {
    // Companies count
    fetch('/platform/ajax/company-actions.php?action=count')
        .then(response => response.json())
        .then(data => {
            const companiesCountElement = document.getElementById('companies-count');
            if (companiesCountElement && data.count) {
                companiesCountElement.textContent = data.count;
            }
        })
        .catch(console.error);
    
    // Modules count
    fetch('/platform/ajax/module-builder.php?action=count')
        .then(response => response.json())
        .then(data => {
            const modulesCountElement = document.getElementById('modules-count');
            if (modulesCountElement && data.count) {
                modulesCountElement.textContent = data.count;
            }
        })
        .catch(console.error);
}

// Mobile sidebar toggle
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
}

// Auto-refresh counts every 30 seconds
setInterval(updateSidebarCounts, 30000);
</script>