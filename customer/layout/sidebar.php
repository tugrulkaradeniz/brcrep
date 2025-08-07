<div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="?page=dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'marketplace' ? 'active' : '' ?>" href="?page=marketplace">
                <i class="fas fa-store"></i>
                <span>Module Marketplace</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'modules' ? 'active' : '' ?>" href="?page=modules">
                <i class="fas fa-puzzle-piece"></i>
                <span>My Modules</span>
            </a>
            
            <!-- Dynamic Modules Section -->
            <?php
            $subscribedModules = CompanyContext::getSubscribedModules();
            if (!empty($subscribedModules)): ?>
                <div class="sidebar-section">
                    <h6 class="sidebar-heading">Active Modules</h6>
                    <?php foreach ($subscribedModules as $module): ?>
                        <a class="nav-link module-link" href="?module=<?= $module['module_code'] ?>">
                            <i class="fas fa-<?= getModuleIcon($module['module_code']) ?>"></i>
                            <span><?= htmlspecialchars($module['module_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <hr class="sidebar-divider">
            
            <a class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>" href="?page=users">
                <i class="fas fa-users"></i>
                <span>Team Members</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>" href="?page=settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a class="nav-link" href="?page=logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">

<?php
// Helper function for module icons
function getModuleIcon($moduleCode) {
    $icons = [
        'brc_risk_assessment' => 'shield-alt',
        'quality_mgmt' => 'award',
        'safety_mgmt' => 'hard-hat',
        'audit_mgmt' => 'clipboard-check',
        'document_control' => 'file-alt',
        'training_mgmt' => 'graduation-cap'
    ];
    
    return $icons[$moduleCode] ?? 'puzzle-piece';
}
?>