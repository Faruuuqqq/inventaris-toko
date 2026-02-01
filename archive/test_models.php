<?php
// Test Models
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== MODEL TESTING ===\n\n";

// Test 1: UserModel - Find user by username
echo "Test 1: UserModel - Find user 'owner'\n";
$result = $mysqli->query("SELECT * FROM users WHERE username = 'owner'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "✓ User found: ID={$user['id']}, Username={$user['username']}, Role={$user['role']}\n";
    
    // Test password verification
    $password = 'test123';
    if (password_verify($password, $user['password_hash'])) {
        echo "✓ Password verification: PASSED\n";
    } else {
        echo "✗ Password verification: FAILED\n";
    }
} else {
    echo "✗ User not found\n";
}

echo "\nTest 2: ProductModel - Get products\n";
$result = $mysqli->query("SELECT p.*, ps.quantity, ps.warehouse_id 
                        FROM products p 
                        LEFT JOIN product_stocks ps ON p.id = ps.product_id 
                        LIMIT 3");
if ($result && $result->num_rows > 0) {
    echo "✓ Products found:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - {$row['name']} (SKU: {$row['sku']}, Stock: {$row['quantity']}, Price: {$row['price_sell']})\n";
    }
} else {
    echo "✗ No products found\n";
}

echo "\nTest 3: CustomerModel - Get customers\n";
$result = $mysqli->query("SELECT * FROM customers");
if ($result && $result->num_rows > 0) {
    echo "✓ Customers found:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - {$row['name']} (Code: {$row['code']}, Credit Limit: {$row['credit_limit']}, Receivable: {$row['receivable_balance']})\n";
    }
} else {
    echo "✗ No customers found\n";
}

echo "\nTest 4: Stock Validation\n";
$result = $mysqli->query("SELECT p.id, p.name, p.min_stock_alert, 
                        COALESCE(SUM(ps.quantity), 0) as total_stock
                        FROM products p
                        LEFT JOIN product_stocks ps ON p.id = ps.product_id
                        GROUP BY p.id
                        HAVING total_stock <= p.min_stock_alert");
if ($result && $result->num_rows > 0) {
    echo "⚠ Low stock products:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - {$row['name']} (Stock: {$row['total_stock']}, Min Alert: {$row['min_stock_alert']})\n";
    }
} else {
    echo "✓ All products have sufficient stock\n";
}

echo "\n=== MODEL TESTING COMPLETED ===\n";
$mysqli->close();
