<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== PURCHASE ORDERS TESTING ===\n\n";

// Test 1: Create Purchase Order
echo "Test 1: Create Purchase Order\n";

$po_number = 'PO-' . date('YmdHis');
$product = $mysqli->query("SELECT * FROM products WHERE id = 4")->fetch_assoc();
$supplier = $mysqli->query("SELECT * FROM suppliers WHERE id = 2")->fetch_assoc();

$quantity = 15;
$total = $product['price_buy'] * $quantity;

$mysqli->begin_transaction();

try {
    // Insert purchase order
    $sql_po = "INSERT INTO purchase_orders (po_number, supplier_id, user_id, warehouse_id, 
                  total_amount, status, notes) 
                  VALUES ('$po_number', 2, 1, 1, $total, 'ORDERED', 'Test PO')";
    $mysqli->query($sql_po);
    $po_id = $mysqli->insert_id;
    echo "✓ Purchase Order created: $po_number (ID: $po_id)\n";
    
    // Insert purchase items
    $sql_po_item = "INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price, subtotal) 
                    VALUES ($po_id, 4, $quantity, {$product['price_buy']}, $total)";
    $mysqli->query($sql_po_item);
    echo "✓ PO Item created: $quantity x {$product['name']}\n";
    
    // Mark as RECEIVED (simulate receiving stock)
    $sql_update = "UPDATE purchase_orders SET status = 'RECEIVED' WHERE id = $po_id";
    $mysqli->query($sql_update);
    echo "✓ PO status updated: RECEIVED\n";
    
    // Update stock (stock in)
    $sql_stock = "UPDATE product_stocks SET quantity = quantity + $quantity 
                  WHERE product_id = 4 AND warehouse_id = 1";
    $mysqli->query($sql_stock);
    echo "✓ Stock updated: +$quantity units\n";
    
    // Get current stock
    $current_stock = $mysqli->query("SELECT quantity FROM product_stocks WHERE product_id = 4 AND warehouse_id = 1")->fetch_assoc()['quantity'];
    
    // Create stock mutation
    $sql_mutation = "INSERT INTO stock_mutations (product_id, warehouse_id, type, quantity, 
                      current_balance, reference_number, notes) 
                      VALUES (4, 1, 'IN', $quantity, $current_stock, '$po_number', 'Purchase: $po_number')";
    $mysqli->query($sql_mutation);
    echo "✓ Stock mutation created\n";
    
    // Update supplier debt
    $sql_supplier = "UPDATE suppliers SET debt_balance = debt_balance + $total 
                     WHERE id = 2";
    $mysqli->query($sql_supplier);
    echo "✓ Supplier debt updated: +$total\n";
    
    $mysqli->commit();
    echo "✓ Purchase Order Transaction COMMITTED\n\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Check Purchase History
echo "Test 2: Check Purchase History\n";

$po_history = $mysqli->query("SELECT po.*, s.name as supplier_name 
                                FROM purchase_orders po
                                JOIN suppliers s ON po.supplier_id = s.id 
                                ORDER BY po.created_at DESC");

if ($po_history && $po_history->num_rows > 0) {
    echo "✓ Purchase Orders found:\n";
    while ($po = $po_history->fetch_assoc()) {
        $items = $mysqli->query("SELECT COUNT(*) as count FROM purchase_items WHERE purchase_id = {$po['id']}")->fetch_assoc()['count'];
        echo "  - {$po['po_number']} | {$po['supplier_name']} | Total: {$po['total_amount']} | Status: {$po['status']} | Items: {$items}\n";
    }
} else {
    echo "✗ No purchase orders found\n";
}

// Test 3: Check Supplier Debt
echo "\nTest 3: Check Supplier Debt\n";

$suppliers = $mysqli->query("SELECT * FROM suppliers");
while ($sup = $suppliers->fetch_assoc()) {
    echo "Supplier: {$sup['name']}\n";
    echo "  Phone: {$sup['phone']}\n";
    echo "  Debt Balance: {$sup['debt_balance']}\n";
    echo "\n";
}

echo "=== PURCHASE ORDERS TESTING COMPLETED ===\n";
$mysqli->close();
