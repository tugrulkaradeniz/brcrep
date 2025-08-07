<?php
// website/router.php
require_once __DIR__ . '/../config/config.php';

// Get current page from URL parameter
$page = $_GET['page'] ?? 'home';

// Remove any path traversal attempts
$page = basename($page);

// Define valid pages
$validPages = [
    'home',
    'pricing',
    'contact',
    'about',
    'features',
    'docs',
    'blog'
];

// Check if page is valid
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Route to appropriate page
$pagePath = __DIR__ . "/pages/{$page}.php";

if (file_exists($pagePath)) {
    include $pagePath;
} else {
    // Show 404 or redirect to home
    header('Location: /website/home');
    exit;
}
?>