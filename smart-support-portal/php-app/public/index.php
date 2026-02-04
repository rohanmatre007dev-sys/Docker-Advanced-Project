<?php
// php-app/public/index.php
declare(strict_types=1);

// Front controller
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../src/Router.php';

// Autoload controllers and models (very small autoloader)
spl_autoload_register(function ($class) {
    $base = __DIR__ . '/../src/';
    $path = str_replace('\\', '/', $class) . '.php';
    $full = $base . $path;
    if (file_exists($full)) {
        require $full;
    } else {
        // Try Controllers and Models directories
        if (file_exists($base . 'Controllers/' . $class . '.php')) {
            require $base . 'Controllers/' . $class . '.php';
        } elseif (file_exists($base . 'Models/' . $class . '.php')) {
            require $base . 'Models/' . $class . '.php';
        }
    }
});

// Create Router and dispatch
$router = new Router($pdo, $redis ?? null, $analyzerHost, $analyzerPort);
$router->dispatch();
