<?php
// platform/auth/logout.php - Admin Logout

session_start();

// Log kaydı (eğer log sistemi çalışıyorsa)
if (function_exists('logMessage') && isset($_SESSION['platform_admin_username'])) {
    logMessage("Admin logout: " . $_SESSION['platform_admin_username']);
}

// Session verilerini temizle
session_unset();
session_destroy();

// Yeni session başlat
session_start();
session_regenerate_id(true);

// Ana sayfaya yönlendir
header('Location: /brcproject/website/home');
exit;
?>