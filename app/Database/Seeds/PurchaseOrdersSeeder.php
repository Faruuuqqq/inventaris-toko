<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseOrdersSeeder extends Seeder
{
    public function run()
    {
        echo "\n📦 Creating Purchase Orders...\n";
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get suppliers and users
            $suppliers = $db->table('suppliers')->select('id, name')->get()->getResultArray();
            $users = $db->table('users')->select('id')->get()->getResultArray();
            $products = $db->table('products')->select('id, sku, name, price_buy')->get()->getResultArray();
            
            if (empty($suppliers)) {
                echo "   ⚠️  No suppliers found. Run InitialDataSeeder first.\n";
                return;
            }
            
            if (empty($products)) {
                echo "   ⚠️  No products found. Run InitialDataSeeder first.\n";
                return;
            }
            
            // Purchase Order templates
            $poTemplates = [
                // Fully Received POs (4 POs)
                ['days_ago' => 60, 'supplier_id' => 1, 'status' => 'Diterima Semua', 'payment_status' => 'PAID', 'received_pct' => 100, 'paid_pct' => 100, 'products' => [[1, 5], [2, 10], [3, 15]]], // Electronics bulk
                ['days_ago' => 45, 'supplier_id' => 2, 'status' => 'Diterima Semua', 'payment_status' => 'PAID', 'received_pct' => 100, 'paid_pct' => 100, 'products' => [[11, 50], [12, 30]]], // Clothing bulk
                ['days_ago' => 30, 'supplier_id' => 3, 'status' => 'Diterima Semua', 'payment_status' => 'PAID', 'received_pct' => 100, 'paid_pct' => 100, 'products' => [[16, 100], [17, 50], [18, 150]]], // Food & beverage
                ['days_ago' => 20, 'supplier_id' => 4, 'status' => 'Diterima Semua', 'payment_status' => 'PAID', 'received_pct' => 100, 'paid_pct' => 100, 'products' => [[21, 30], [22, 25]]], // Household items
                
                // Partially Received POs (3 POs)
                ['days_ago' => 15, 'supplier_id' => 5, 'status' => 'Sebagian', 'payment_status' => 'PARTIAL', 'received_pct' => 60, 'paid_pct' => 70, 'products' => [[26, 50], [27, 40]]], // Health products
                ['days_ago' => 10, 'supplier_id' => 6, 'status' => 'Sebagian', 'payment_status' => 'PARTIAL', 'received_pct' => 50, 'paid_pct' => 40, 'products' => [[31, 80], [32, 40]]], // Stationery
                ['days_ago' => 5, 'supplier_id' => 8, 'status' => 'Sebagian', 'payment_status' => 'PARTIAL', 'received_pct' => 75, 'paid_pct' => 60, 'products' => [[41, 60], [42, 35]]], // Automotive parts
                
                // Pending POs (3 POs)
                ['days_ago' => 3, 'supplier_id' => 7, 'status' => 'Dipesan', 'payment_status' => 'UNPAID', 'received_pct' => 0, 'paid_pct' => 0, 'products' => [[36, 20], [37, 15], [38, 25]]], // Sports equipment
                ['days_ago' => 1, 'supplier_id' => 9, 'status' => 'Dipesan', 'payment_status' => 'UNPAID', 'received_pct' => 0, 'paid_pct' => 0, 'products' => [[46, 15], [47, 20], [48, 30]]], // Toys
                ['days_ago' => 0, 'supplier_id' => 10, 'status' => 'Dipesan', 'payment_status' => 'UNPAID', 'received_pct' => 0, 'paid_pct' => 0, 'products' => [[49, 25], [50, 40]]], // Accessories
            ];
            
            echo "   Creating " . count($poTemplates) . " purchase orders...\n";
            echo "   - 4 Fully received POs\n";
            echo "   - 3 Partially received POs\n";
            echo "   - 3 Pending POs\n\n";
            
            $poCount = 0;
            $itemCount = 0;
            $invoiceCounter = 1000;
            $paymentMethods = ['CASH', 'TRANSFER', 'CHEQUE'];
            
            foreach ($poTemplates as $template) {
                $poCount++;
                $invoiceCounter++;
                
                $poDate = date('Y-m-d', strtotime("-{$template['days_ago']} days"));
                
                // Calculate totals
                $totalAmount = 0;
                $poItems = [];
                
                foreach ($template['products'] as $item) {
                    $productId = $item[0];
                    $quantity = $item[1];
                    
                    if (!isset($products[$productId - 1])) {
                        continue;
                    }
                    
                    $product = $products[$productId - 1];
                    $price = $product['price_buy'];
                    $subtotal = $price * $quantity;
                    
                    $totalAmount += $subtotal;
                    
                    $receivedQty = intval($quantity * ($template['received_pct'] / 100));
                    
                    $poItems[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'received_qty' => $receivedQty,
                        'product_name' => $product['name']
                    ];
                    
                    $itemCount++;
                }
                
                // Calculate received and paid amounts
                $receivedAmount = $totalAmount * ($template['received_pct'] / 100);
                $paidAmount = $totalAmount * ($template['paid_pct'] / 100);
                
                // Insert purchase order
                $poData = [
                    'nomor_po' => 'PO-' . date('Ymd', strtotime($poDate)) . '-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                    'tanggal_po' => $poDate,
                    'supplier_id' => $template['supplier_id'],
                    'user_id' => $users[array_rand($users)]['id'],
                    'status' => $template['status'],
                    'total_amount' => $totalAmount,
                    'received_amount' => $receivedAmount,
                    'payment_status' => $template['payment_status'],
                    'paid_amount' => $paidAmount,
                    'notes' => $this->getPONotes($template['status'])
                ];
                
                $db->table('purchase_orders')->insert($poData);
                $poId = $db->insertID();
                
                // Insert purchase order items
                foreach ($poItems as $item) {
                    $db->table('purchase_order_items')->insert([
                        'po_id' => $poId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'received_qty' => $item['received_qty']
                    ]);
                }
                
                // Create IN stock mutations for received items
                foreach ($poItems as $item) {
                    if ($item['received_qty'] > 0) {
                        $db->table('stock_mutations')->insert([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => 1, // Main warehouse
                            'type' => 'IN',
                            'quantity' => $item['received_qty'],
                            'current_balance' => 0, // Will be updated separately
                            'reference_number' => $poData['nomor_po'],
                            'notes' => 'Penerimaan barang dari ' . $suppliers[array_rand($suppliers)]['name'],
                            'created_at' => date('Y-m-d H:i:s', strtotime($poDate . ' +' . random_int(1, 7) . ' days'))
                        ]);
                    }
                }
                
                // Update supplier debt balance for unpaid POs
                if ($template['payment_status'] !== 'PAID') {
                    $outstanding = $totalAmount - $paidAmount;
                    $db->query(
                        "UPDATE suppliers SET debt_balance = debt_balance + ? WHERE id = ?",
                        [$outstanding, $template['supplier_id']]
                    );
                }
                
                // Display progress
                $statusEmoji = $template['status'] === 'Diterima Semua' ? '✅' : ($template['status'] === 'Sebagian' ? '⚠️' : '⏳');
                echo sprintf(
                    "   %s [%d/%d] %s | %s | Items: %d | Total: Rp %s | Received: %d%% | Paid: %d%%\n",
                    $statusEmoji,
                    $poCount,
                    count($poTemplates),
                    $poData['nomor_po'],
                    $template['status'],
                    count($poItems),
                    number_format($totalAmount, 0, ',', '.'),
                    $template['received_pct'],
                    $template['paid_pct']
                );
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }
            
            echo "\n✅ Purchase Orders Seeding Complete!\n";
            echo "   📦 Total POs: {$poCount} purchase orders\n";
            echo "   📦 Total Items: {$itemCount} PO items\n";
            
            // Show summary
            $totalPOs = $db->table('purchase_orders')->countAll();
            $totalAmount = $db->query("SELECT SUM(total_amount) as total FROM purchase_orders")->getRow()->total;
            $paidAmount = $db->query("SELECT SUM(paid_amount) as total FROM purchase_orders")->getRow()->total;
            $receivedAmount = $db->query("SELECT SUM(received_amount) as total FROM purchase_orders")->getRow()->total;
            
            echo "\n📈 Purchase Summary:\n";
            echo "   💰 Total POs: {$totalPOs}\n";
            echo "   💰 Total Value: Rp " . number_format($totalAmount, 0, ',', '.') . "\n";
            echo "   💰 Paid Amount: Rp " . number_format($paidAmount, 0, ',', '.') . "\n";
            echo "   💰 Received Amount: Rp " . number_format($receivedAmount, 0, ',', '.') . "\n";
            
        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
    
    private function getPONotes($status)
    {
        $notes = [
            'Dipesan' => 'Pesanan baru, menunggu pengiriman',
            'Sebagian' => 'Penerimaan parsial, sisa menunggu pengiriman',
            'Diterima Semua' => 'Semua barang sudah diterima dengan lengkap',
            'Dibatalkan' => 'Pesanan dibatalkan atas permintaan'
        ];
        
        return $notes[$status] ?? '';
    }
}
