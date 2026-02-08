<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SalesReturnsSeeder extends Seeder
{
    public function run()
    {
        echo "\n🔄 Creating Sales Returns...\n";

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Fetch paid/partial sales
            $sales = $db->table('sales')
                ->select('sales.id, sales.invoice_number, sales.customer_id, sales.created_at, sales.warehouse_id')
                ->whereIn('sales.payment_status', ['PAID', 'PARTIAL'])
                ->limit(100)
                ->get()
                ->getResultArray();

            if (empty($sales)) {
                echo "   ⚠️  No sales found for returns\n";
                return;
            }

            // Randomly select 20-30% for returns
            $returnCount = max(3, (int)(count($sales) * 0.25));
            $selectedSales = array_rand($sales, min($returnCount, count($sales)));
            if (!is_array($selectedSales)) {
                $selectedSales = [$selectedSales];
            }

            echo "   Creating {$returnCount} sales returns...\n";

            $returnReasons = [
                'Produk rusak',
                'Cacat/tidak sesuai',
                'Customer berubah pikiran',
                'Tidak sesuai spesifikasi',
            ];

            $returnStatuses = ['Disetujui' => 70, 'Pending' => 20, 'Ditolak' => 10];
            $returnCount = 0;
            $returnItemCount = 0;

            foreach ($selectedSales as $idx) {
                $sale = $sales[$idx];
                
                // Generate return number
                $returnNo = 'RET-' . date('Y-m-d', strtotime($sale['created_at'])) . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                
                // Return date: 2-5 days after sale
                $returnDate = date('Y-m-d', strtotime($sale['created_at'] . ' +' . random_int(2, 5) . ' days'));
                
                // Random status
                $statusRand = random_int(1, 100);
                $status = 'Disetujui';
                if ($statusRand <= 20) {
                    $status = 'Pending';
                } elseif ($statusRand <= 30) {
                    $status = 'Ditolak';
                }

                // Random reason
                $reason = $returnReasons[array_rand($returnReasons)];

                // Get sale items
                $saleItems = $db->table('sale_items')
                    ->select('product_id, quantity, price')
                    ->where('sale_id', $sale['id'])
                    ->limit(3) // Max 3 items per return
                    ->get()
                    ->getResultArray();

                if (empty($saleItems)) {
                    continue;
                }

                // Calculate total return
                $totalReturn = 0;
                $returnItems = [];

                foreach ($saleItems as $saleItem) {
                    // Return qty: 1-5 or all if only 1 item
                    $returnQty = min($saleItem['quantity'], random_int(1, max(1, (int)($saleItem['quantity'] * 0.8))));
                    $itemSubtotal = $returnQty * $saleItem['price'];
                    $totalReturn += $itemSubtotal;
                    
                    $returnItems[] = [
                        'product_id' => $saleItem['product_id'],
                        'quantity' => $returnQty,
                        'price' => $saleItem['price'],
                    ];
                }

                // Insert sales return
                $db->table('sales_returns')->insert([
                    'no_retur' => $returnNo,
                    'tanggal_retur' => $returnDate,
                    'sale_id' => $sale['id'],
                    'customer_id' => $sale['customer_id'],
                    'alasan' => $reason,
                    'status' => $status,
                    'total_retur' => $totalReturn,
                ]);

                $returnId = $db->insertID();
                $returnCount++;

                // Insert return items
                foreach ($returnItems as $item) {
                    $db->table('sales_return_items')->insert([
                        'return_id' => $returnId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    $returnItemCount++;

                    // Create IN mutation to restore stock if approved
                    if ($status === 'Disetujui') {
                        $db->table('stock_mutations')->insert([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $sale['warehouse_id'],
                            'type' => 'IN',
                            'quantity' => $item['quantity'],
                            'current_balance' => 0,
                            'reference_number' => $returnNo,
                            'notes' => 'Return produk #' . $returnNo,
                            'created_at' => date('Y-m-d H:i:s', strtotime($returnDate)),
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            echo "\n✅ Sales returns seeding complete!\n";
            echo "   Sales returns: {$returnCount}\n";
            echo "   Return items: {$returnItemCount}\n\n";

        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
}
