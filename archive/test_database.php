<?php
require __DIR__ . '/vendor/autoload.php';

// Initialize CI4
define('ENVIRONMENT', 'development');
$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== DATABASE CONNECTION TEST ===\n\n";

// Test tables
$tables = ['users', 'products', 'customers', 'suppliers', 'warehouses', 'sales', 'product_stocks'];
foreach ($tables as $table) {
    try {
        $result = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->getRow()->count;
        echo "$table: $count records\n";
    } catch (Exception $e) {
        echo "$table: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n=== USER DATA TEST ===\n";
$users = $db->query('SELECT id, username, fullname, role FROM users')->getResult();
foreach ($users as $user) {
    echo "ID: $user->id, Username: $user->username, Role: $user->role\n";
}

echo "\n=== PRODUCT DATA TEST ===\n";
$products = $db->query('SELECT id, sku, name, price_sell FROM products LIMIT 3')->getResult();
foreach ($products as $product) {
    echo "ID: $product->id, SKU: $product->sku, Name: $product->name, Price: $product->price_sell\n";
}

echo "\nDatabase test PASSED!\n";
