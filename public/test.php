<?php
echo "PHP is working!\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'not set' . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";

// Try to load CI4
require __DIR__ . '/../vendor/autoload.php';
echo "\nComposer autoload: SUCCESS\n";

// Try to load App
try {
    $app = \Config\Services::codeigniter();
    $app->initialize();
    echo "CI4 App initialized: SUCCESS\n";
} catch (Exception $e) {
    echo "CI4 App Error: " . $e->getMessage() . "\n";
}

// Test routes
try {
    $routes = \Config\Services::routes();
    echo "Routes loaded: SUCCESS\n";
    echo "Total routes: " . count($routes->getRoutes()) . "\n";
} catch (Exception $e) {
    echo "Routes Error: " . $e->getMessage() . "\n";
}
