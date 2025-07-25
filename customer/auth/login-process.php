<?php
session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../models/CompanyUser.php';
require_once '../../services/TenantContext.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../?page=login');
    exit;
}

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($login) || empty($password)) {
    header('Location: ../../?page=login&error=1');
    exit;
}

// Get company context
$tenantContext = TenantContext::detect();
$companyId = $tenantContext->getCompanyId();

if (!$companyId) {
    header('Location: ../../?page=login&error=1');
    exit;
}

$userModel = new CompanyUser($baglanti);
$user = $userModel->authenticate($login, $password, $companyId);

if ($user) {
    $_SESSION['company_user_id'] = $user['id'];
    $_SESSION['company_id'] = $user['company_id'];
    $_SESSION['company_username'] = $user['username'];
    $_SESSION['company_user_role'] = $user['role'];
    
    // Redirect to original URL or dashboard
    $redirect = $_GET['redirect'] ?? '?page=dashboard';
    header('Location: ../../' . $redirect);
} else {
    header('Location: ../../?page=login&error=1');
}
exit;
?>