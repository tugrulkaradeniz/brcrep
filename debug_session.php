<?php
// debug_session.php (Geçici test dosyası)
session_start();

echo "<h2>Session Debug Info</h2>";
echo "<strong>Session ID:</strong> " . session_id() . "<br>";
echo "<strong>Session Status:</strong> " . session_status() . "<br>";
echo "<strong>Session Data:</strong><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>URL Debug Info</h3>";
echo "<strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'empty') . "<br>";
echo "<strong>GET Parameters:</strong><br>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Test login
if (isset($_POST['test_login'])) {
    $_SESSION['platform_admin_id'] = 1;
    $_SESSION['admin_name'] = 'Test Admin';
    echo "<div style='color: green; font-weight: bold;'>Test session created!</div>";
}

// Clear session
if (isset($_POST['clear_session'])) {
    session_destroy();
    echo "<div style='color: red; font-weight: bold;'>Session cleared!</div>";
}

echo "<h3>Test Actions</h3>";
echo "<form method='post' style='margin: 10px 0;'>";
echo "<button name='test_login' type='submit' style='background: green; color: white; padding: 10px;'>Create Test Session</button>";
echo "</form>";

echo "<form method='post' style='margin: 10px 0;'>";
echo "<button name='clear_session' type='submit' style='background: red; color: white; padding: 10px;'>Clear Session</button>";
echo "</form>";

echo "<h3>Test Links</h3>";
echo "<a href='?page=admin'>Admin Dashboard</a><br>";
echo "<a href='platform/pages/dashboard.php'>Direct Dashboard</a><br>";
echo "<a href='/brcproject/?page=admin'>Full URL Admin</a><br>";
?>

<style>
body { font-family: Arial; margin: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>