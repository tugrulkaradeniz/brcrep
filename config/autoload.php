<?php
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// SONRA CLASS AUTOLOADER
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../services/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../middleware/',
        __DIR__ . '/../config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// EN SON FUNCTIONS DOSYASINI YÜKLEYELİM (config.php'den sonra)
if (file_exists(__DIR__ . '/functions.php')) {
    require_once __DIR__ . '/functions.php';
}
?>

