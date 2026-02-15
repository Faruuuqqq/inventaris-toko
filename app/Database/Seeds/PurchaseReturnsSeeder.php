<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseReturnsSeeder extends Seeder
{
    public function run()
    {
        echo "\n🔄 Creating Purchase Returns...\n";

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Fetch purchase orders with received status
            $pos = $db->table('purchase_orders')
                ->select('purchase_orders.id_po, purchase_orders.nomor_po, purchase_orders.supplier_id, purchase_orders.tanggal_po')
                ->whereIn('purchase_orders.status', ['Sebagian', 'Diterima Semua'])
                ->limit(100)
                ->get()
                ->getResultArray();

            if (empty($pos)) {
                echo "   ⚠️  No purchase orders found for returns\n";
                return;
            }

            // Randomly select 15-20% for returns
            $returnCount = max(2, (int)(count($pos) * 0.18));
            $selectedPos = array_rand($pos, min($returnCount, count($pos)));
            if (!is_array($selectedPos)) {
                $selectedPos = [$selectedPos];
            }

            echo "   Creating {$returnCount} purchase returns...\n";

            $returnReasons = [
                'Produk cacat',
                'Tidak sesuai spesifikasi',
                'Quantity lebih dari order',
                'Expired/kadaluarsa',
            ];

            $returnCount = 0;
            $returnItemCount = 0;

            foreach ($selectedPos as $idx) {
                $po = $pos[$idx];
                
                // Generate return number
                $returnNo = 'RET-PO-' . date('Y-m-d', strtotime($po['tanggal_po'])) . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                
                // Return date: 3-10 days after PO
                $returnDate = date('Y-m-d', strtotime($po['tanggal_po'] . ' +' . random_int(3, 10) . ' days'));
                
                // Random status (mostly approved)
                $status = random_int(1, 100) <= 80 ? 'Disetujui' : 'Pending';

                // Random reason
                $reason = $returnReasons[array_rand($returnReasons)];

                // Get PO items
                $poItems = $db->table('purchase_order_items')
                    ->select('product_id, quantity, price')
                    ->where('po_id', $po['id_po'])
                    ->limit(2)
                    ->get()
                    ->getResultArray();

                if (empty($poItems)) {
                    continue;
                }

                // Calculate total return
                $totalReturn = 0;
                $returnItems = [];

                foreach ($poItems as $poItem) {
                    // Return qty: 1-3 or all if only 1
                    $returnQty = min($poItem['quantity'], random_int(1, max(1, (int)($poItem['quantity'] * 0.5))));
                    $itemSubtotal = $returnQty * $poItem['price'];
                    $totalReturn += $itemSubtotal;
                    
                    $returnItems[] = [
                        'product_id' => $poItem['product_id'],
                        'quantity' => $returnQty,
                        'price' => $poItem['price'],
                    ];
                }

                // Insert purchase return
                $db->table('purchase_returns')->insert([
                    'no_retur' => $returnNo,
                    'tanggal_retur' => $returnDate,
                    'po_id' => $po['id_po'],
                    'supplier_id' => $po['supplier_id'],
                    'alasan' => $reason,
                    'status' => $status,
                    'total_retur' => $totalReturn,
                ]);

                $returnId = $db->insertID();
                $returnCount++;

                // Insert return items and create OUT mutations
                foreach ($returnItems as $item) {
                    $db->table('purchase_return_items')->insert([
                        'return_id' => $returnId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    $returnItemCount++;

                    // Create OUT mutation if approved (reduce stock)
                    if ($status === 'Disetujui') {
                        $db->table('stock_mutations')->insert([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => 1, // Default warehouse
                            'type' => 'OUT',
                            'quantity' => $item['quantity'],
                            'current_balance' => 0,
                            'reference_number' => $returnNo,
                            'notes' => 'Retur pembelian #' . $returnNo,
                            'created_at' => date('Y-m-d H:i:s', strtotime($returnDate)),
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            echo "\n✅ Purchase returns seeding complete!\n";
            echo "   Purchase returns: {$returnCount}\n";
            echo "   Return items: {$returnItemCount}\n\n";

        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
}
