<?php
session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../models/PlatformAdmin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../?page=admin&action=login');
    exit;
}

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($login) || empty($password)) {
    header('Location: ../../?page=admin&action=login&error=1');
    exit;
}

$adminModel = new PlatformAdmin($baglanti);
$admin = $adminModel->authenticate($login, $password);

if ($admin) {
    $_SESSION['platform_admin_id'] = $admin['id'];
    $_SESSION['platform_admin_username'] = $admin['username'];
    $_SESSION['platform_admin_role'] = $admin['role'];
    
    // Success - redirect to admin dashboard
    header('Location: ../../?page=admin');
} else {
    // Failed - back to login with error
    header('Location: ../../?page=admin&action=login&error=1');
}
exit;
?>