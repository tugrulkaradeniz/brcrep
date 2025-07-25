<div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="?page=admin">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'companies' ? 'active' : '' ?>" href="?page=admin&action=companies">
                <i class="fas fa-building"></i>
                <span>Companies</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'modules' ? 'active' : '' ?>" href="?page=admin&action=modules">
                <i class="fas fa-puzzle-piece"></i>
                <span>Modules</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'module-builder' ? 'active' : '' ?>" href="?page=admin&action=module-builder">
                <i class="fas fa-tools"></i>
                <span>Module Builder</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'marketplace' ? 'active' : '' ?>" href="?page=admin&action=marketplace">
                <i class="fas fa-store"></i>
                <span>Marketplace</span>
            </a>
            <a class="nav-link <?= ($currentPage ?? '') === 'analytics' ? 'active' : '' ?>" href="?page=admin&action=analytics">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
            
            <hr class="sidebar-divider">
            
            <a class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>" href="?page=admin&action=settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a class="nav-link" href="?page=admin&action=logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">