<?php
require 'vendor/autoload.php';

// Load config
$config = new \Config\Database();
$db = $config->getConnection();

// Get database and connect
$connection = new \CodeIgniter\Database\MySQLi\Connection($config->default);

echo "========================================\n";
echo "DATA INTEGRITY AUDIT\n";
echo "========================================\n\n";

$queries = [
    "1. Orphaned SALE ITEMS" => "SELECT COUNT(*) as c FROM sale_items si LEFT JOIN sales s ON si.sale_id = s.id WHERE s.id IS NULL",
    "2. Invalid PRODUCT refs" => "SELECT COUNT(*) as c FROM sale_items si LEFT JOIN products p ON si.product_id = p.id WHERE p.id IS NULL",
    "3. Orphaned PO ITEMS" => "SELECT COUNT(*) as c FROM purchase_order_items poi LEFT JOIN purchase_orders po ON poi.po_id = po.id_po WHERE po.id_po IS NULL",
    "4. SALES invalid CUSTOMERS" => "SELECT COUNT(*) as c FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE c.id IS NULL",
    "5. SALES invalid USERS" => "SELECT COUNT(*) as c FROM sales s LEFT JOIN users u ON s.user_id = u.id WHERE u.id IS NULL",
    "6. STOCK invalid PRODUCTS" => "SELECT COUNT(*) as c FROM stock_mutations sm LEFT JOIN products p ON sm.product_id = p.id WHERE p.id IS NULL",
];

foreach ($queries as $desc => $sql) {
    try {
        $result = $connection->query($sql)->getRow();
        echo "$desc: " . $result->c . " issues\n";
    } catch (Exception $e) {
        echo "$desc: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "Audit complete\n";
?>
