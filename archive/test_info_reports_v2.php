<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

echo "=== INFO & REPORTS TESTING ===\n\n";

// Test 1: Stock Card (Product Movements)
echo "Test 1: Stock Card (Product Movements)\n";

$product_id = 1;
$warehouse_id = 1;

$stock_mutations = $mysqli->query("SELECT sm.*, p.name as product_name, w.name as warehouse_name
                                    FROM stock_mutations sm
                                    JOIN products p ON sm.product_id = p.id
                                    JOIN warehouses w ON sm.warehouse_id = w.id
                                    WHERE sm.product_id = $product_id
                                    AND sm.warehouse_id = $warehouse_id
                                    ORDER BY sm.created_at DESC LIMIT 10");

if ($stock_mutations && $stock_mutations->num_rows > 0) {
    echo "✓ Stock Card for Product ID $product_id:\n";
    echo "  Type       Qty        Balance     Ref               Notes                 Date\n";
    echo "  " . str_repeat("-", 90) . "\n";
    
    while ($mut = $stock_mutations->fetch_assoc()) {
        printf("  %-10s %-10d %-15d %-10s %-20s %s\n",
               $mut['type'],
               $mut['quantity'],
               $mut['current_balance'],
               $mut['reference_number'],
               substr($mut['notes'], 0, 18),
               $mut['created_at']);
    }
} else {
    echo "✗ No stock mutations found\n";
}

// Test 2: History - Sales
echo "\nTest 2: Sales History\n";

$sales_history = $mysqli->query("SELECT s.*, c.name as customer_name, u.username as created_by
                                  FROM sales s
                                  JOIN customers c ON s.customer_id = c.id
                                  JOIN users u ON s.user_id = u.id
                                  ORDER BY s.created_at DESC LIMIT 10");

if ($sales_history && $sales_history->num_rows > 0) {
    echo "✓ Sales History:\n";
    echo "  Invoice             Customer             Type            Status          Total        Date\n";
    echo "  " . str_repeat("-", 100) . "\n";
    
    while ($sale = $sales_history->fetch_assoc()) {
        printf("  %-20s %-20s %-15s %-15s Rp %-10s %s\n",
               $sale['invoice_number'],
               $sale['customer_name'],
               $sale['password_type'],
               $sale['payment_status'],
               number_format($sale['total_amount'], 0),
               $sale['created_at']);
    }
} else {
    echo "✗ No sales found\n";
}

// Test 3: Saldo - Receivable (Piutang)
echo "\nTest 3: Saldo - Receivable (Piutang)\n";

echo "  Customer                   Credit Limit    Receivable     Available    Usage\n";
echo "  " . str_repeat("-", 80) . "\n";

$receivables = $mysqli->query("SELECT * FROM customers ORDER BY receivable_balance DESC");
while ($cust = $receivables->fetch_assoc()) {
    $available = $cust['credit_limit'] - $cust['receivable_balance'];
    $usage = $cust['credit_limit'] > 0 ? 
              ($cust['receivable_balance'] / $cust['credit_limit']) * 100 : 0;
    
    printf("  %-25s Rp %-13s Rp %-13s Rp %-13s %.1f%%\n",
           $cust['name'],
           number_format($cust['credit_limit'], 0),
           number_format($cust['receivable_balance'], 0),
           number_format($available, 0),
           $usage);
}

// Test 4: Saldo - Stock
echo "\nTest 4: Saldo - Stock (Inventory)\n";

echo "  Product             SKU        Stock         Unit Price     Total Value\n";
echo "  " . str_repeat("-", 80) . "\n";

$inventory = $mysqli->query("SELECT p.*, COALESCE(SUM(ps.quantity), 0) as stock
                                FROM products p
                                LEFT JOIN product_stocks ps ON p.id = ps.product_id
                                GROUP BY p.id
                                ORDER BY stock DESC");

$total_inventory_value = 0;
while ($prod = $inventory->fetch_assoc()) {
    $value = $prod['stock'] * $prod['price_sell'];
    $total_inventory_value += $value;
    
    printf("  %-20s %-10s %-15d Rp %-13s Rp %s\n",
           $prod['name'],
           $prod['sku'],
           $prod['stock'],
           number_format($prod['price_sell'], 0),
           number_format($value, 0));
}

echo "  " . str_repeat("-", 80) . "\n";
printf("  %-20s Rp %s\n", 'Total Inventory Value:', number_format($total_inventory_value, 0));

// Test 5: Reports - Daily Summary
echo "\nTest 5: Daily Reports Summary\n";

$today = date('Y-m-d');

$daily_sales = $mysqli->query("SELECT COALESCE(SUM(total_amount), 0) as total, 
                                   COUNT(*) as count 
                                   FROM sales 
                                   WHERE DATE(created_at) = '$today'")->fetch_assoc();

$daily_purchases = $mysqli->query("SELECT COALESCE(SUM(total_amount), 0) as total, 
                                      COUNT(*) as count 
                                      FROM purchase_orders 
                                      WHERE DATE(created_at) = '$today'")->fetch_assoc();

$daily_payments = $mysqli->query("SELECT COALESCE(SUM(amount), 0) as total, 
                                     COUNT(*) as count 
                                     FROM payments 
                                     WHERE DATE(created_at) = '$today'")->fetch_assoc();

echo "Daily Report ($today):\n";
echo "  Category                   Total (Rp)    Count\n";
echo "  " . str_repeat("-", 60) . "\n";
printf("  %-25s Rp %-12s %10d\n", 'Sales', 
       number_format($daily_sales['total'], 0), $daily_sales['count']);
printf("  %-25s Rp %-12s %10d\n", 'Purchases', 
       number_format($daily_purchases['total'], 0), $daily_purchases['count']);
printf("  %-25s Rp %-12s %10d\n", 'Payments', 
       number_format($daily_payments['total'], 0), $daily_payments['count']);

$net_cash_flow = $daily_sales['total'] + $daily_payments['total'];
echo "  " . str_repeat("-", 60) . "\n";
printf("  %-25s Rp %s\n", 'Net Cash Flow', number_format($net_cash_flow, 0));

echo "\n=== INFO & REPORTS TESTING COMPLETED ===\n";
$mysqli->close();
