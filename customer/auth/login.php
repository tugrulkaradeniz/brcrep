<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= CompanyContext::getCompanyData()['company_name'] ?? 'Company' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/customer.css" rel="stylesheet">
</head>
<body class="customer-login-page">
    <?php 
    $companyData = CompanyContext::getCompanyData();
    $companyName = $companyData['company_name'] ?? 'Demo Company';
    $themeColor = $companyData['theme_color'] ?? '#007bff';
    ?>
    
    <style>
        :root {
            --company-color: <?= $themeColor ?>;
        }
    </style>

    <div class="login-container">
        <div class="login-card">
            <div class="company-header">
                <div class="company-logo" style="background: <?= $themeColor ?>">
                    <?= strtoupper(substr($companyName, 0, 2)) ?>
                </div>
                <h2><?= htmlspecialchars($companyName) ?></h2>
                <p>Welcome back! Please sign in to your account.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    Invalid credentials. Please try again.
                </div>
            <?php endif; ?>

            <form method="POST" action="customer/auth/login-process.php" class="login-form">
                <div class="form-group">
                    <label>Username or Email</label>
                    <input type="text" name="login" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="login-footer">
                <small class="text-muted">
                    Powered by <strong>BRC Load</strong> Platform
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>