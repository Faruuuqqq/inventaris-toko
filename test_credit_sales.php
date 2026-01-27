<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== CREDIT SALES & PAYMENTS TESTING ===\n\n";

// Test 1: Create CREDIT Sales
echo "Test 1: Create CREDIT Sales Transaction\n";

$product = $mysqli->query("SELECT * FROM products WHERE id = 2")->fetch_assoc();
$customer = $mysqli->query("SELECT * FROM customers WHERE id = 2")->fetch_assoc();

$quantity = 10;
$total = $product['price_sell'] * $quantity;
$invoice_number = 'INV-' . date('YmdHis');
$due_date = date('Y-m-d', strtotime('+30 days'));

$mysqli->begin_transaction();

try {
    // Insert sales record (CREDIT)
    $sql_sales = "INSERT INTO sales (invoice_number, customer_id, user_id, warehouse_id, 
                  password_type, total_amount, paid_amount, payment_status, due_date, is_hidden) 
                  VALUES ('$invoice_number', 2, 1, 1, 'CREDIT', $total, 0, 'UNPAID', '$due_date', 0)";
    $mysqli->query($sql_sales);
    $sale_id = $mysqli->insert_id;
    echo "✓ Credit sales created: $invoice_number\n";
    
    // Insert sale items
    $sql_item = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                 VALUES ($sale_id, 2, $quantity, {$product['price_sell']}, $total)";
    $mysqli->query($sql_item);
    echo "✓ Sale item created: $quantity x {$product['name']}\n";
    
    // Update stock
    $sql_stock = "UPDATE product_stocks SET quantity = quantity - $quantity 
                  WHERE product_id = 2 AND warehouse_id = 1";
    $mysqli->query($sql_stock);
    echo "✓ Stock updated: -$quantity\n";
    
    // Update customer receivable
    $sql_customer = "UPDATE customers SET receivable_balance = receivable_balance + $total 
                    WHERE id = 2";
    $mysqli->query($sql_customer);
    echo "✓ Customer receivable updated: +$total\n";
    
    $new_balance = $mysqli->query("SELECT receivable_balance FROM customers WHERE id = 2")->fetch_assoc()['receivable_balance'];
    echo "  New balance: $new_balance\n";
    
    $mysqli->commit();
    echo "✓ Transaction COMMITTED\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Make Payment
echo "Test 2: Make Payment for Credit Sale\n";

$payment_number = 'PAY-' . date('YmdHis');
$payment_amount = 300000;

$mysqli->begin_transaction();

try {
    // Insert payment
    $sql_payment = "INSERT INTO payments (payment_number, type, reference_id, amount, 
                       payment_method, user_id) 
                       VALUES ('$payment_number', 'RECEIVABLE', 2, $payment_amount, 'CASH', 1)";
    $mysqli->query($sql_payment);
    echo "✓ Payment created: $payment_number\n";
    
    // Update customer balance
    $sql_update = "UPDATE customers SET receivable_balance = receivable_balance - $payment_amount 
                    WHERE id = 2";
    $mysqli->query($sql_update);
    echo "✓ Customer receivable updated: -$payment_amount\n";
    
    // Update sale payment status
    $current_paid = $mysqli->query("SELECT paid_amount FROM sales WHERE id = $sale_id")->fetch_assoc()['paid_amount'];
    $new_paid = $current_paid + $payment_amount;
    
    $new_status = ($new_paid >= $total) ? 'PAID' : 'PARTIAL';
    $sql_sale = "UPDATE sales SET paid_amount = $new_paid, payment_status = '$new_status' 
                  WHERE id = $sale_id";
    $mysqli->query($sql_sale);
    echo "✓ Sales payment updated: $new_paid / $total (Status: $new_status)\n";
    
    $final_balance = $mysqli->query("SELECT receivable_balance FROM customers WHERE id = 2")->fetch_assoc()['receivable_balance'];
    echo "  Final balance: $final_balance\n";
    
    $mysqli->commit();
    echo "✓ Payment Transaction COMMITTED\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Check Credit Limit Validation
echo "Test 3: Check Customer Credit Limit\n";

$customers = $mysqli->query("SELECT * FROM customers");
while ($cust = $customers->fetch_assoc()) {
    $available = $cust['credit_limit'] - $cust['receivable_balance'];
    $usage = ($cust['receivable_balance'] / $cust['credit_limit']) * 100;
    
    echo "Customer: {$cust['name']}\n";
    echo "  Credit Limit: {$cust['credit_limit']}\n";
    echo "  Receivable: {$cust['receivable_balance']}\n";
    echo "  Available: $available\n";
    echo "  Usage: " . number_format($usage, 2) . "%\n";
    
    if ($available < 0) {
        echo "  ⚠ OVER LIMIT!\n";
    }
    echo "\n";
}

echo "=== CREDIT SALES & PAYMENTS TESTING COMPLETED ===\n";
$mysqli->close();
