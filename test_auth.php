<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/login';

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/Config/Routes.php';

use App\Controllers\Auth;

// Create Auth controller and test
$auth = new Auth();
echo "Auth Controller class loaded successfully!\n";

// Test index method
try {
    ob_start();
    $result = $auth->index();
    $output = ob_get_clean();
    
    if ($output) {
        echo "Auth::index() returned output (length: " . strlen($output) . ")\n";
    } else {
        echo "Auth::index() returned empty output\n";
    }
} catch (Exception $e) {
    echo "Error calling Auth::index(): " . $e->getMessage() . "\n";
}
