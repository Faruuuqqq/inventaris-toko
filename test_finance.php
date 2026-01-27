<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== FINANCE TESTING ===\n\n";

// Test 1: Create CREDIT Sales Transaction
echo "Test 1: Create CREDIT Sales Transaction\n";

$product = $mysqli->query("SELECT * FROM products WHERE id = 2")->fetch_assoc();
$customer = $mysqli->query("SELECT * FROM customers WHERE id = 1")->fetch_assoc();

$quantity = 10;
$total = $product['price_sell'] * $quantity;
$invoice_number = 'INV-' . date('YmdHis');
$due_date = date('Y-m-d', strtotime('+30 days'));

$mysqli->begin_transaction();

try {
    // Insert sales record (CREDIT)
    $sql_sales = "INSERT INTO sales (invoice_number, customer_id, user_id, warehouse_id, 
                  password_type, total_amount, paid_amount, payment_status, due_date, is_hidden) 
                  VALUES ('$invoice_number', 1, 1, 1, 'CREDIT', $total, 0, 'UNPAID', '$due_date', 0)";
    if (!$mysqli->query($sql_sales)) {
        throw new Exception("Failed to create sales: " . $mysqli->error);
    }
    $sale_id = $mysqli->insert_id;
    echo "✓ Credit sales created: ID=$sale_id, Invoice=$invoice_number\n";
    
    // Insert sale items
    $sql_item = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                 VALUES ($sale_id, 2, $quantity, {$product['price_sell']}, $total)";
    if (!$mysqli->query($sql_item)) {
        throw new Exception("Failed to create sale item: " . $mysqli->error);
    }
    echo "✓ Sale item created: $quantity x {$product['name']}\n";
    
    // Update product stock
    $sql_stock = "UPDATE product_stocks SET quantity = quantity - $quantity 
                  WHERE product_id = 2 AND warehouse_id = 1";
    if (!$mysqli->query($sql_stock)) {
        throw new Exception("Failed to update stock: " . $mysqli->error);
    }
    echo "✓ Stock updated: -$quantity units\n";
    
    // Get current stock balance
    $current_stock = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = 2 AND warehouse_id = 1")->fetch_assoc()['quantity'];
    
    // Create stock mutation
    $sql_mutation = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                      current_balance, reference_number, notes) 
                      VALUES (2, 1, 'OUT', $quantity, $current_stock, '$invoice_number', 'Credit Sale: $invoice_number')";
    if (!$mysqli->query($sql_mutation)) {
        throw new Exception("Failed to create stock mutation: " . $mysqli->error);
    }
    echo "✓ Stock mutation created\n";
    
    // Update customer receivable balance
    $sql_customer = "UPDATE customers SET receivable_balance = receivable_balance + $total 
                    WHERE id = 1";
    if (!$mysqli->query($sql_customer)) {
        throw new Exception("Failed to update customer balance: " . $mysqli->error);
    }
    echo "✓ Customer receivable updated: +$total\n";
    
    $mysqli->commit();
    echo "✓ Credit Transaction COMMITTED successfully\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Transaction ROLLED BACK: " . $e->getMessage() . "\n\n";
}

// Test 2: Create Purchase Order
echo "Test 2: Create Purchase Order\n";

$po_number = 'PO-' . date('YmdHis');
$product_purchase = $mysqli->query("SELECT * FROM products WHERE id = 3")->fetch_assoc();
$supplier = $mysqli->query("SELECT * FROM suppliers WHERE id = 1")->fetch_assoc();

$po_quantity = 20;
$po_total = $product_purchase['price_buy'] * $po_quantity;

$mysqli->begin_transaction();

try {
    // Insert purchase order
    $sql_po = "INSERT INTO purchase_orders (po_number, supplier_id, user_id, warehouse_id, 
                  total_amount, status) 
                  VALUES ('$po_number', 1, 1, 1, $po_total, 'RECEIVED')";
    if (!$mysqli->query($sql_po)) {
        throw new Exception("Failed to create PO: " . $mysqli->error);
    }
    $po_id = $mysqli->insert_id;
    echo "✓ Purchase Order created: ID=$po_id, PO=$po_number\n";
    
    // Insert purchase items
    $sql_po_item = "INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price, subtotal) 
                    VALUES ($po_id, 3, $po_quantity, {$product_purchase['price_buy']}, $po_total)";
    if (!$mysqli->query($sql_po_item)) {
        throw new Exception("Failed to create PO item: " . $mysqli->error);
    }
    echo "✓ PO item created: $po_quantity x {$product_purchase['name']}\n";
    
    // Update product stock (stock in)
    $sql_stock_in = "UPDATE product_stocks SET quantity = quantity + $po_quantity 
                      WHERE product_id = 3 AND warehouse_id = 1";
    if (!$mysqli->query($sql_stock_in)) {
        throw new Exception("Failed to update stock: " . $mysqli->error);
    }
    echo "✓ Stock updated: +$po_quantity units\n";
    
    // Get current stock balance
    $current_stock_po = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = 3 AND warehouse_id = 1")->fetch_assoc()['quantity'];
    
    // Create stock mutation
    $sql_mutation_po = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                          current_balance, reference_number, notes) 
                          VALUES (3, 1, 'IN', $po_quantity, $current_stock_po, '$po_number', 'Purchase: $po_number')";
    if (!$mysqli->query($sql_mutation_po)) {
        throw new Exception("Failed to create stock mutation: " . $mysqli->error);
    }
    echo "✓ Stock mutation created\n";
    
    // Update supplier debt
    $sql_supplier = "UPDATE suppliers SET debt_balance = debt_balance + $po_total 
                     WHERE id = 1";
    if (!$mysqli->query($sql_supplier)) {
        throw new Exception("Failed to update supplier debt: " . $mysqli->error);
    }
    echo "✓ Supplier debt updated: +$po_total\n";
    
    $mysqli->commit();
    echo "✓ Purchase Transaction COMMITTED successfully\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Transaction ROLLED BACK: " . $e->getMessage() . "\n\n";
}

// Test 3: Create Payment
echo "Test 3: Create Payment (Receivable)\n";

$payment_number = 'PAY-' . date('YmdHis');
$payment_amount = 5000000;

$mysqli->begin_transaction();

try {
    // Insert payment
    $sql_payment = "INSERT INTO payments (payment_number, type, reference_id, amount, 
                       payment_method, user_id) 
                       VALUES ('$payment_number', 'RECEIVABLE', 1, $payment_amount, 'CASH', 1)";
    if (!$mysqli->query($sql_payment)) {
        throw new Exception("Failed to create payment: " . $mysqli->error);
    }
    echo "✓ Payment created: $payment_number\n";
    
    // Update customer receivable
    $sql_update_cust = "UPDATE customers SET receivable_balance = receivable_balance - $payment_amount 
                         WHERE id = 1";
    if (!$mysqli->query($sql_update_cust)) {
        throw new Exception("Failed to update customer: " . $mysqli->error);
    }
    echo "✓ Customer receivable updated: -$payment_amount\n";
    
    // Check customer balance
    $cust_balance = $mysqli->query("SELECT receivable_balance FROM customers WHERE id = 1")->fetch_assoc()['receivable_balance'];
    echo "  Current receivable balance: $cust_balance\n";
    
    $mysqli->commit();
    echo "✓ Payment Transaction COMMITTED successfully\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Transaction ROLLED BACK: " . $e->getMessage() . "\n\n";
}

// Test 4: Create Kontra Bon
echo "Test 4: Create Kontra Bon\n";

$kb_number = 'KB-' . date('YmdHis');

// Get unpaid credit sales
$unpaid_sales = $mysqli->query("SELECT * FROM sales WHERE password_type = 'CREDIT' AND payment_status = 'UNPAID' LIMIT 2");
if ($unpaid_sales && $unpaid_sales->num_rows > 0) {
    $kb_total = 0;
    $sales_list = [];
    
    while ($sale = $unpaid_sales->fetch_assoc()) {
        $sales_list[] = $sale;
        $kb_total += $sale['total_amount'];
    }
    
    $mysqli->begin_transaction();
    
    try {
        // Insert kontra bon
        $sql_kb = "INSERT INTO kontra_bons (kb_number, customer_id, total_amount, status, due_date) 
                    VALUES ('$kb_number', 1, $kb_total, 'UNPAID', DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
 
