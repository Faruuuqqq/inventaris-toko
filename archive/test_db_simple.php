<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== DATABASE CONNECTION TEST ===\n\n";

// Test tables
$tables = ['users', 'products', 'customers', 'suppliers', 'warehouses', 'sales', 'product_stocks'];
foreach ($tables as $table) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "$table: $count records\n";
    } else {
        echo "$table: ERROR - " . $mysqli->error . "\n";
    }
}

echo "\n=== USER DATA TEST ===\n";
$users = $mysqli->query('SELECT id, username, fullname, role FROM users');
while ($user = $users->fetch_assoc()) {
    echo "ID: {$user['id']}, Username: {$user['username']}, Role: {$user['role']}\n";
}

echo "\n=== PRODUCT DATA TEST ===\n";
$products = $mysqli->query('SELECT id, sku, name, price_sell FROM products LIMIT 3');
while ($product = $products->fetch_assoc()) {
    echo "ID: {$product['id']}, SKU: {$product['sku']}, Name: {$product['name']}, Price: {$product['price_sell']}\n";
}

echo "\nDatabase test PASSED!\n";
$mysqli->close();
