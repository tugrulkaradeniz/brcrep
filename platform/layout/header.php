<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Platform Admin' ?> - BRC Load</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/platform.css" rel="stylesheet">
</head>
<body class="platform-admin">
    
    <!-- Top Header -->
    <div class="top-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <div class="platform-brand">
                            <i class="fas fa-cubes me-2"></i>
                            <span>BRC Load Platform</span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <!-- Notifications -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge bg-danger">3</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">New company registered</a></li>
                                <li><a class="dropdown-item" href="#">Module subscription expired</a></li>
                                <li><a class="dropdown-item" href="#">System update available</a></li>
                            </ul>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <div class="user-avatar me-2">
                                    <?= strtoupper(substr($_SESSION['platform_admin_username'] ?? 'A', 0, 1)) ?>
                                </div>
                                <span><?= $_SESSION['platform_admin_username'] ?? 'Admin' ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?page=admin&action=profile">My Profile</a></li>
                                <li><a class="dropdown-item" href="?page=admin&action=settings">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?page=admin&action=logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>