<?php
require __DIR__ . '/../vendor/autoload.php';
$app = \Config\Services::codeigniter();
$app->initialize();
$routes = \Config\Services::routes();
$definedRoutes = $routes->getRoutes();
echo "Defined Routes Count: " . count($definedRoutes) . "\n";
echo "First 10 routes:\n";
$i = 0;
foreach ($definedRoutes as $route) {
    if ($i >= 10) break;
    echo "  " . $route . "\n";
    $i++;
}
