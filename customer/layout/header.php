<?php 
$companyData = CompanyContext::getCompanyData();
$companyName = $companyData['company_name'] ?? 'Company';
$themeColor = $companyData['theme_color'] ?? '#007bff';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - <?= htmlspecialchars($companyName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/customer.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: <?= $themeColor ?>;
        }
    </style>
</head>
<body class="customer-panel">

    <!-- Top Header -->
    <div class="top-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <div class="company-brand">
                            <div class="company-logo" style="background: <?= $themeColor ?>">
                                <?= strtoupper(substr($companyName, 0, 2)) ?>
                            </div>
                            <div class="company-info">
                                <h6><?= htmlspecialchars($companyName) ?></h6>
                                <small><?= $companyData['subdomain'] ?? '' ?>.brcload.com</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="header-actions">
                        <!-- Notifications -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge bg-danger">2</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">Risk assessment due tomorrow</a></li>
                                <li><a class="dropdown-item" href="#">New team member added</a></li>
                            </ul>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <?php
                            $userName = $_SESSION['company_username'] ?? 'User';
                            $userInitials = strtoupper(substr($userName, 0, 1));
                            ?>
                            <button class="btn btn-outline-secondary d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <div class="user-avatar me-2" style="background: <?= $themeColor ?>">
                                    <?= $userInitials ?>
                                </div>
                                <span><?= htmlspecialchars($userName) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?page=profile">My Profile</a></li>
                                <li><a class="dropdown-item" href="?page=settings">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?page=logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>