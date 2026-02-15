<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockMutationsSeeder extends Seeder
{
    public function run()
    {
        echo "\n📦 Creating Stock Mutations...\n";

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Fetch all sales with items
            $sales = $db->table('sales')
                ->select('sales.id, sales.invoice_number, sales.created_at, sales.warehouse_id')
                ->get()
                ->getResultArray();

            $saleItems = $db->table('sale_items')
                ->select('sale_items.sale_id, sale_items.product_id, sale_items.quantity')
                ->get()
                ->getResultArray();

            // Create index for faster lookup
            $saleItemsMap = [];
            foreach ($saleItems as $item) {
                if (!isset($saleItemsMap[$item['sale_id']])) {
                    $saleItemsMap[$item['sale_id']] = [];
                }
                $saleItemsMap[$item['sale_id']][] = $item;
            }

            echo "   Generating OUT mutations from sales...\n";
            $outCount = 0;

            foreach ($sales as $sale) {
                if (isset($saleItemsMap[$sale['id']])) {
                    foreach ($saleItemsMap[$sale['id']] as $item) {
                        $db->table('stock_mutations')->insert([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $sale['warehouse_id'],
                            'type' => 'OUT',
                            'quantity' => $item['quantity'],
                            'current_balance' => 0, // Will be updated later
                            'reference_number' => $sale['invoice_number'],
                            'notes' => 'Penjualan #' . $sale['invoice_number'],
                            'created_at' => $sale['created_at'],
                        ]);
                        $outCount++;
                    }
                }
            }
            echo "   ✓ Generated {$outCount} OUT mutations\n";

            // 2. Fetch all purchase orders with items
            $pos = $db->table('purchase_orders')
                ->select('purchase_orders.id_po, purchase_orders.nomor_po, purchase_orders.tanggal_po, purchase_orders.supplier_id')
                ->get()
                ->getResultArray();

            $poItems = $db->table('purchase_order_items')
                ->select('purchase_order_items.po_id, purchase_order_items.product_id, purchase_order_items.quantity')
                ->get()
                ->getResultArray();

            // Create index
            $poItemsMap = [];
            foreach ($poItems as $item) {
                if (!isset($poItemsMap[$item['po_id']])) {
                    $poItemsMap[$item['po_id']] = [];
                }
                $poItemsMap[$item['po_id']][] = $item;
            }

            echo "   Generating IN mutations from purchase orders...\n";
            $inCount = 0;
            $warehouse1 = 1; // Default warehouse

            foreach ($pos as $po) {
                if (isset($poItemsMap[$po['id_po']])) {
                    foreach ($poItemsMap[$po['id_po']] as $item) {
                        // Convert DATE to DATETIME
                        $poDateTime = date('Y-m-d H:i:s', strtotime($po['tanggal_po']));
                        
                        $db->table('stock_mutations')->insert([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $warehouse1,
                            'type' => 'IN',
                            'quantity' => $item['quantity'],
                            'current_balance' => 0, // Will be updated later
                            'reference_number' => $po['nomor_po'],
                            'notes' => 'Pembelian #' . $po['nomor_po'],
                            'created_at' => $poDateTime,
                        ]);
                        $inCount++;
                    }
                }
            }
            echo "   ✓ Generated {$inCount} IN mutations\n";

            // 3. Generate random adjustments
            echo "   Generating ADJUSTMENT mutations...\n";
            $adjustCount = 0;
            $products = $db->table('products')->select('id')->get()->getResultArray();
            $warehouses = $db->table('warehouses')->select('id')->get()->getResultArray();

            if (!empty($products) && !empty($warehouses)) {
                $adjustmentCount = random_int(50, 100);
                $today = strtotime('2026-02-08');
                $startDate = strtotime('-60 days', $today);

                for ($i = 0; $i < $adjustmentCount; $i++) {
                    $randomProduct = $products[array_rand($products)];
                    $randomWarehouse = $warehouses[array_rand($warehouses)];
                    $randomDate = date('Y-m-d H:i:s', random_int($startDate, $today));
                    $randomType = random_int(0, 1) === 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
                    $randomQty = random_int(5, 50);

                    $db->table('stock_mutations')->insert([
                        'product_id' => $randomProduct['id'],
                        'warehouse_id' => $randomWarehouse['id'],
                        'type' => $randomType,
                        'quantity' => $randomQty,
                        'current_balance' => 0, // Will be updated later
                        'notes' => 'Penyesuaian stok manual',
                        'created_at' => $randomDate,
                    ]);
                    $adjustCount++;
                }
            }
            echo "   ✓ Generated {$adjustCount} ADJUSTMENT mutations\n";

            // 4. Calculate running balances for each product-warehouse combination
            echo "   Calculating running balances...\n";
            $mutations = $db->table('stock_mutations')
                ->select('id, product_id, warehouse_id, type, quantity, created_at')
                ->orderBy('created_at, id')
                ->get()
                ->getResultArray();

            // Track balance per product per warehouse
            $balances = [];

            foreach ($mutations as $mutation) {
                $key = $mutation['product_id'] . '_' . $mutation['warehouse_id'];
                
                if (!isset($balances[$key])) {
                    $balances[$key] = 0;
                }

                if ($mutation['type'] === 'IN' || $mutation['type'] === 'ADJUSTMENT_IN') {
                    $balances[$key] += $mutation['quantity'];
                } else {
                    $balances[$key] -= $mutation['quantity'];
                    // Ensure balance doesn't go negative
                    if ($balances[$key] < 0) {
                        $balances[$key] = 0;
                    }
                }

                // Update the mutation with correct running balance
                $db->table('stock_mutations')
                    ->where('id', $mutation['id'])
                    ->update(['current_balance' => $balances[$key]]);
            }

            // 5. Update product_stocks with final balances
            echo "   Updating product_stocks final balances...\n";
            foreach ($balances as $key => $balance) {
                [$productId, $warehouseId] = explode('_', $key);
                
                $db->table('product_stocks')
                    ->where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->update(['quantity' => $balance]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $totalMutations = $outCount + $inCount + $adjustCount;
            echo "\n✅ Stock mutations seeding complete!\n";
            echo "   OUT mutations: {$outCount}\n";
            echo "   IN mutations: {$inCount}\n";
            echo "   ADJUSTMENT mutations: {$adjustCount}\n";
            echo "   Total mutations: {$totalMutations}\n\n";

        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
}
