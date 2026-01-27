<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== KONTRA BON TESTING ===\n\n";

// Test 1: Create Credit Sales for Kontra Bon
echo "Test 1: Create Additional Credit Sales\n";

for ($i = 0; $i < 3; $i++) {
    $invoice_number = 'INV-KB-' . date('YmdHis') . $i;
    $customer_id = 2; // CV Berkah Sejahtera
    $total_amount = ($i + 1) * 500000;
    $due_date = date('Y-m-d', strtotime('+' . ($i * 7) . ' days'));
    
    $sql = "INSERT INTO sales (invoice_number, customer_id, user_id, warehouse_id, 
              password_type, total_amount, paid_amount, payment_status, due_date, is_hidden) 
              VALUES ('$invoice_number', $customer_id, 1, 1, 'CREDIT', $total_amount, 
              0, 'UNPAID', '$due_date', 0)";
    $mysqli->query($sql);
    
    $sale_id = $mysqli->insert_id;
    $product_id = ($i % 5) + 1;
    $unit_price = $total_amount / 1;
    
    $sql_item = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) 
                  VALUES ($sale_id, $product_id, 1, $unit_price, $total_amount)";
    $mysqli->query($sql_item);
    
    echo "✓ Credit sale created: $invoice_number (Rp $total_amount)\n";
}

echo "\nTest 2: Create Kontra Bon\n";

$kb_number = 'KB-' . date('YmdHis');
$customer_id = 2;

// Get unpaid credit sales for customer
$unpaid_sales = $mysqli->query("SELECT * FROM sales WHERE customer_id = $customer_id 
                                  AND password_type = 'CREDIT' 
                                  AND payment_status = 'UNPAID'");

if ($unpaid_sales && $unpaid_sales->num_rows > 0) {
    $kb_total = 0;
    $sales_list = [];
    
    while ($sale = $unpaid_sales->fetch_assoc()) {
        $sales_list[] = $sale;
        $kb_total += $sale['total_amount'];
    }
    
    $mysqli->begin_transaction();
    
    try {
        // Create kontra bon
        $sql_kb = "INSERT INTO kontra_bons (kb_number, customer_id, total_amount, 
                     paid_amount, status, due_date, notes) 
                     VALUES ('$kb_number', $customer_id, $kb_total, 0, 'UNPAID', 
                     DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'Consolidated invoice')";
        $mysqli->query($sql_kb);
        $kb_id = $mysqli->insert_id;
        echo "✓ Kontra Bon created: $kb_number (ID: $kb_id)\n";
        echo "  Total Amount: Rp $kb_total\n";
        
        // Add sales to kontra bon
        foreach ($sales_list as $sale) {
            $sql_kb_item = "INSERT INTO kontra_bon_items (kontra_bon_id, sale_id, amount) 
                            VALUES ($kb_id, {$sale['id']}, {$sale['total_amount']})";
            $mysqli->query($sql_kb_item);
            echo "✓ Sale added: {$sale['invoice_number']} (Rp {$sale['total_amount']})\n";
            
            // Update sales status to PARTIAL (linked to KB)
            $sql_update_sale = "UPDATE sales SET payment_status = 'PARTIAL' 
                                 WHERE id = {$sale['id']}";
            $mysqli->query($sql_update_sale);
        }
        
        $mysqli->commit();
        echo "✓ Kontra Bon Transaction COMMITTED\n\n";
    } catch (Exception $e) {
        $mysqli->rollback();
        echo "✗ Error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "⚠ No unpaid credit sales found for Kontra Bon\n\n";
}

// Test 3: Make Payment for Kontra Bon
echo "Test 3: Make Payment for Kontra Bon\n";

if (isset($kb_id)) {
    $payment_amount = 1000000;
    $payment_number = 'PAY-KB-' . date('YmdHis');
    
    $mysqli->begin_transaction();
    
    try {
        // Create payment
        $sql_payment = "INSERT INTO payments (payment_number, type, reference_id, amount, 
                           payment_method, user_id, notes) 
                           VALUES ('$payment_number', 'RECEIVABLE', $kb_id, 
                           $payment_amount, 'CASH', 1, 'Payment for Kontra Bon')";
        $mysqli->query($sql_payment);
        echo "✓ Payment created: $payment_number (Rp $payment_amount)\n";
        
        // Update kontra bon
        $sql_update_kb = "UPDATE kontra_bons SET paid_amount = paid_amount + $payment_amount 
                             WHERE id = $kb_id";
        $mysqli->query($sql_update_kb);
        echo "✓ Kontra Bon paid_amount updated\n";
        
        // Get KB details
        $kb_details = $mysqli->query("SELECT * FROM kontra_bons WHERE id = $kb_id")->fetch_assoc();
        
        // Update status if fully paid
        if ($kb_details['total_amount'] <= ($kb_details['paid_amount'] + $payment_amount)) {
            $sql_status = "UPDATE kontra_bons SET status = 'PAID' WHERE id = $kb_id";
            $mysqli->query($sql_status);
            echo "✓ Kontra Bon status: PAID\n";
        } else {
            $sql_status = "UPDATE kontra_bons SET status = 'PARTIAL' WHERE id = $kb_id";
            $mysqli->query($sql_status);
            echo "✓ Kontra Bon status: PARTIAL\n";
        }
        
        // Update customer receivable
        $sql_customer = "UPDATE customers SET receivable_balance = receivable_balance - $payment_amount 
                          WHERE id = $customer_id";
        $mysqli->query($sql_customer);
        echo "✓ Customer receivable updated: -$payment_amount\n";
        
        $new_balance = $mysqli->query("SELECT receivable_balance FROM customers WHERE id = $customer_id")->fetch_assoc()['receivable_balance'];
        echo "  New balance: Rp $new_balance\n";
        
        $mysqli->commit();
        echo "✓ KB Payment Transaction COMMITTED\n\n";
    } catch (Exception $e) {
        $mysqli->rollback();
        echo "✗ Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 4: Check Kontra Bon History
echo "Test 4: Check Kontra Bon History\n";

$kb_list = $mysqli->query("SELECT kb.*, c.name as customer_name 
                              FROM kontra_bons kb
                              JOIN customers c ON kb.customer_id = c.id 
                              ORDER BY kb.created_at DESC");

if ($kb_list && $kb_list->num_rows > 0) {
    echo "✓ Kontra Bons found:\n";
    while ($kb = $kb_list->fetch_assoc()) {
        $percentage = $kb['total_amount'] > 0 ? 
                      ($kb['paid_amount'] / $kb['total_amount']) * 100 : 0;
        $items = $mysqli->query("SELECT COUNT(*) as count FROM kontra_bon_items 
                                   WHERE kontra_bon_id = {$kb['id']}")->fetch_assoc()['count'];
        
        echo sprintf("  - %s | %s | Rp %s | Rp %s | %.1f%% | %s | %d invoices\n",
                     $kb['kb_number'],
                     $kb['customer_name'],
                     number_format($kb['total_amount'], 0),
                     number_format($kb['paid_amount'], 0),
                     $percentage,
                     $kb['status'],
                     $items);
    }
} else {
    echo "✗ No kontra bons found\n";
}

echo "\n=== KONTRA BON TESTING COMPLETED ===\n";
$mysqli->close();
