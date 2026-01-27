<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== TRANSACTION TESTING ===\n\n";

// Test 1: Create Sales Transaction (CASH)
echo "Test 1: Create CASH Sales Transaction\n";

// Get product and customer
$product = $mysqli->query("SELECT * FROM products WHERE id = 1")->fetch_assoc();
$customer = $mysqli->query("SELECT * FROM customers WHERE id = 1")->fetch_assoc();
$warehouse = $mysqli->query("SELECT * FROM warehouses WHERE id = 1")->fetch_assoc();

echo "Product: {$product['name']} (Price: {$product['price_sell']})\n";
echo "Customer: {$customer['name']}\n";
echo "Warehouse: {$warehouse['name']}\n";

$quantity = 2;
$total = $product['price_sell'] * $quantity;
$invoice_number = 'INV-' . date('YmdHis');

// Start transaction
$mysqli->begin_transaction();

try {
    // Insert sales record
    $sql_sales = "INSERT INTO sales (invoice_number, customer_id, user_id, warehouse_id, 
                  password_type, total_amount, paid_amount, payment_status, is_hidden) 
                  VALUES ('$invoice_number', 1, 1, 1, 'CASH', $total, $total, 'PAID', 0)";
    if (!$mysqli->query($sql_sales)) {
        throw new Exception("Failed to create sales: " . $mysqli->error);
    }
    $sale_id = $mysqli->insert_id;
    echo "✓ Sales created: ID=$sale_id, Invoice=$invoice_number\n";
    
    // Insert sale items
    $sql_item = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                 VALUES ($sale_id, 1, $quantity, {$product['price_sell']}, $total)";
    if (!$mysqli->query($sql_item)) {
        throw new Exception("Failed to create sale item: " . $mysqli->error);
    }
    echo "✓ Sale item created: $quantity x {$product['name']}\n";
    
    // Update product stock
    $sql_stock = "UPDATE product_stocks SET quantity = quantity - $quantity 
                  WHERE product_id = 1 AND warehouse_id = 1";
    if (!$mysqli->query($sql_stock)) {
        throw new Exception("Failed to update stock: " . $mysqli->error);
    }
    echo "✓ Stock updated: -$quantity units\n";
    
    // Get current stock balance
    $current_stock = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = 1 AND warehouse_id = 1")->fetch_assoc()['quantity'];
    echo "  Current stock: $current_stock\n";
    
    // Create stock mutation
    $sql_mutation = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                      current_balance, reference_number, notes) 
                      VALUES (1, 1, 'OUT', $quantity, $current_stock, '$invoice_number', 'Sale: $invoice_number')";
    if (!$mysqli->query($sql_mutation)) {
        throw new Exception("Failed to create stock mutation: " . $mysqli->error);
    }
    echo "✓ Stock mutation created\n";
    
    $mysqli->commit();
    echo "✓ Transaction COMMITTED successfully\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Transaction ROLLED BACK: " . $e->getMessage() . "\n\n";
}

// Test 2: Validate Customer Credit Limit
echo "Test 2: Validate Customer Credit Limit\n";

$customer_data = $mysqli->query("SELECT * FROM customers WHERE id = 1")->fetch_assoc();
$credit_limit = $customer_data['credit_limit'];
$receivable_balance = $customer_data['receivable_balance'];
$available_credit = $credit_limit - $receivable_balance;

echo "Customer: {$customer_data['name']}\n";
echo "Credit Limit: $credit_limit\n";
echo "Receivable Balance: $receivable_balance\n";
echo "Available Credit: $available_credit\n";

$test_amount = 10000000;
if ($test_amount <= $available_credit) {
    echo "✓ Credit limit validation PASSED for amount: $test_amount\n";
} else {
    echo "✗ Credit limit validation FAILED for amount: $test_amount\n";
}

echo "\nTest 3: Check Stock Mutation History\n";
$mutations = $mysqli->query("SELECT * FROM stock_mutations ORDER BY created_at DESC LIMIT 3");
if ($mutations && $mutations->num_rows > 0) {
    echo "✓ Stock mutations found:\n";
    while ($mut = $mutations->fetch_assoc()) {
        echo "  - ID: {$mut['id']}, Type: {$mut['type']}, Qty: {$mut['quantity']}, 
             Balance: {$mut['current_balance']}, Ref: {$mut['reference_number']}\n";
    }
} else {
    echo "✗ No stock mutations found\n";
}

echo "\nTest 4: Check Sales History\n";
$sales = $mysqli->query("SELECT s.*, c.name as customer_name FROM sales s 
                        JOIN customers c ON s.customer_id = c.id 
                        ORDER BY s.created_at DESC LIMIT 3");
if ($sales && $sales->num_rows > 0) {
    echo "✓ Sales history found:\n";
    while ($sale = $sales->fetch_assoc()) {
        echo "  - Invoice: {$sale['invoice_number']}, Customer: {$sale['customer_name']}, 
             Total: {$sale['total_amount']}, Status: {$sale['payment_status']}\n";
    }
} else {
    echo "✗ No sales found\n";
}

echo "\n=== TRANSACTION TESTING COMPLETED ===\n";
$mysqli->close();
