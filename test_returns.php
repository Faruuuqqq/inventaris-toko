<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== SALES & PURCHASE RETURNS TESTING ===\n\n";

// Test 1: Create Sales Return
echo "Test 1: Create Sales Return\n";

// Get existing sale
$sale = $mysqli->query("SELECT * FROM sales WHERE id = 2")->fetch_assoc();
$sale_items = $mysqli->query("SELECT * FROM sale_items WHERE sale_id = 2")->fetch_assoc();
$product = $mysqli->query("SELECT * FROM products WHERE id = {$sale_items['product_id']}")->fetch_assoc();

$return_number = 'SR-' . date('YmdHis');
$return_qty = 2;
$return_amount = $sale_items['unit_price'] * $return_qty;

$mysqli->begin_transaction();

try {
    // Insert sales return
    $sql_sr = "INSERT INTO sales_returns (return_number, sale_id, customer_id, user_id, 
                   warehouse_id, total_amount, status, reason) 
                   VALUES ('$return_number', 2, {$sale['customer_id']}, 1, 
                   {$sale['warehouse_id']}, $return_amount, 'PENDING', 'Defective product')";
    $mysqli->query($sql_sr);
    $sr_id = $mysqli->insert_id;
    echo "✓ Sales Return created: $return_number (ID: $sr_id)\n";
    
    // Insert sales return items
    $sql_sri = "INSERT INTO sales_return_items (sales_return_id, product_id, quantity, 
                    unit_price, subtotal) 
                    VALUES ($sr_id, {$sale_items['product_id']}, $return_qty, 
                    {$sale_items['unit_price']}, $return_amount)";
    $mysqli->query($sql_sri);
    echo "✓ Sales Return Item created: $return_qty x {$product['name']}\n";
    
    // Approve the return (simulate approval)
    $sql_approve = "UPDATE sales_returns SET status = 'APPROVED' WHERE id = $sr_id";
    $mysqli->query($sql_approve);
    echo "✓ Sales Return approved\n";
    
    // Add stock back
    $sql_stock = "UPDATE product_stocks SET quantity = quantity + $return_qty 
                   WHERE product_id = {$sale_items['product_id']} AND warehouse_id = {$sale['warehouse_id']}";
    $mysqli->query($sql_stock);
    echo "✓ Stock updated: +$return_qty units\n";
    
    // Get current stock
    $current_stock = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = {$sale_items['product_id']} AND warehouse_id = {$sale['warehouse_id']}")->fetch_assoc()['quantity'];
    
    // Create stock mutation
    $sql_mutation = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                      current_balance, reference_number, notes) 
                      VALUES ({$sale_items['product_id']}, {$sale['warehouse_id']}, 'IN', 
                      $return_qty, $current_stock, '$return_number', 'Sales Return: $return_number')";
    $mysqli->query($sql_mutation);
    echo "✓ Stock mutation created\n";
    
    // Update customer receivable (reduce amount due to return)
    $sql_customer = "UPDATE customers SET receivable_balance = receivable_balance - $return_amount 
                      WHERE id = {$sale['customer_id']}";
    $mysqli->query($sql_customer);
    echo "✓ Customer receivable updated: -$return_amount\n";
    
    $mysqli->commit();
    echo "✓ Sales Return Transaction COMMITTED\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Create Purchase Return
echo "Test 2: Create Purchase Return\n";

// Get existing PO
$po = $mysqli->query("SELECT * FROM purchase_orders WHERE id = 1")->fetch_assoc();
$po_items = $mysqli->query("SELECT * FROM purchase_items WHERE purchase_id = 1")->fetch_assoc();
$product_po = $mysqli->query("SELECT * FROM products WHERE id = {$po_items['product_id']}")->fetch_assoc();
$supplier = $mysqli->query("SELECT * FROM suppliers WHERE id = {$po['supplier_id']}")->fetch_assoc();

$return_po_number = 'PR-' . date('YmdHis');
$return_po_qty = 3;
$return_po_amount = $po_items['unit_price'] * $return_po_qty;

$mysqli->begin_transaction();

try {
    // Insert purchase return
    $sql_pr = "INSERT INTO purchase_returns (return_number, purchase_id, supplier_id, user_id, 
                    total_amount, status, reason) 
                    VALUES ('$return_po_number', 1, {$po['supplier_id']}, 1, 
                    $return_po_amount, 'PENDING', 'Damaged goods')";
    $mysqli->query($sql_pr);
    $pr_id = $mysqli->insert_id;
    echo "✓ Purchase Return created: $return_po_number (ID: $pr_id)\n";
    
    // Insert purchase return items
    $sql_pri = "INSERT INTO purchase_return_items (purchase_return_id, product_id, quantity, 
                     unit_price, subtotal) 
                     VALUES ($pr_id, {$po_items['product_id']}, $return_po_qty, 
                     {$po_items['unit_price']}, $return_po_amount)";
    $mysqli->query($sql_pri);
    echo "✓ Purchase Return Item created: $return_po_qty x {$product_po['name']}\n";
    
    // Approve the return
    $sql_approve_po = "UPDATE purchase_returns SET status = 'APPROVED' WHERE id = $pr_id";
    $mysqli->query($sql_approve_po);
    echo "✓ Purchase Return approved\n";
    
    // Reduce stock
    $sql_stock_po = "UPDATE product_stocks SET quantity = quantity - $return_po_qty 
                       WHERE product_id = {$po_items['product_id']} AND warehouse_id = {$po['warehouse_id']}";
    $mysqli->query($sql_stock_po);
    echo "✓ Stock updated: -$return_po_qty units\n";
    
    // Get current stock
    $current_stock_po = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = {$po_items['product_id']} AND warehouse_id = {$po['warehouse_id']}")->fetch_assoc()['quantity'];
    
    // Create stock mutation
    $sql_mutation_po = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                          current_balance, reference_number, notes) 
                          VALUES ({$po_items['product_id']}, {$po['warehouse_id']}, 'OUT', 
                          $return_po_qty, $current_stock_po, '$return_po_number', 'Purchase Return: $return_po_number')";
    $mysqli->query($sql_mutation_po);
    echo "✓ Stock mutation created\n";
    
    // Reduce supplier debt (return money to supplier)
    $sql_supplier = "UPDATE suppliers SET debt_balance = debt_balance - $return_po_amount 
                      WHERE id = {$po['supplier_id']}";
    $mysqli->query($sql_supplier);
    echo "✓ Supplier debt updated: -$return_po_amount\n";
    
    $mysqli->commit();
    echo "✓ Purchase Return Transaction COMMITTED\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Check Returns History
echo "Test 3: Check Returns History\n";

$sales_returns = $mysqli->query("SELECT sr.*, c.name as customer_name 
                                  FROM sales_returns sr
                                  JOIN customers c ON sr.customer_id = c.id 
                                  ORDER BY sr.created_at DESC");

if ($sales_returns && $sales_returns->num_rows > 0) {
    echo "✓ Sales Returns found:\n";
    while ($sr = $sales_returns->fetch_assoc()) {
        echo "  - {$sr['return_number']} | {$sr['customer_name']} | Amount: {$sr['total_amount']} | Status: {$sr['status']}\n";
    }
} else {
    echo "✗ No sales returns found\n";
}

$purchase_returns = $mysqli->query("SELECT pr.*, s.name as supplier_name 
                                    FROM purchase_returns pr
                                    JOIN suppliers s ON pr.supplier_id = s.id 
                                    ORDER BY pr.created_at DESC");

if ($purchase_returns && $purchase_returns->num_rows > 0) {
    echo "✓ Purchase Returns found:\n";
    while ($pr = $purchase_returns->fetch_assoc()) {
        echo "  - {$pr['return_number']} | {$pr['supplier_name']} | Amount: {$pr['total_amount']} | Status: {$pr['status']}\n";
    }
} else {
    echo "✗ No purchase returns found\n";
}

echo "\n=== SALES & PURCHASE RETURNS TESTING COMPLETED ===\n";
$mysqli->close();
