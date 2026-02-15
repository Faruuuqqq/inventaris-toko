<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeliveryNotesSeeder extends Seeder
{
    public function run()
    {
        echo "\n🚛 Creating Delivery Notes...\n";
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get sales that need delivery notes
            $sales = $db->table('sales')
                ->select('id, invoice_number, created_at, customer_id, payment_type')
                ->where('is_hidden', 0)
                ->orderBy('created_at', 'DESC')
                ->limit(25)
                ->get()
                ->getResultArray();
            
            if (empty($sales)) {
                echo "   ⚠️  No sales found. Run SalesDataSeeder first.\n";
                return;
            }
            
            $customers = $db->table('customers')->select('id, name, address')->get()->getResultArray();
            $salespersons = $db->table('salespersons')->select('id, name')->get()->getResultArray();
            
            // Driver and vehicle data
            $drivers = ['Pak Budi', 'Pak Andi', 'Pak Joko', 'Pak Dodi', 'Pak Eko', 'Pak Gilang', 'Pak Iwan', 'Pak Koko'];
            $vehicles = ['B 1234 CD', 'B 5678 EF', 'B 9012 GH', 'B 3456 IJ', 'B 7890 KL', 'B 2345 MN', 'B 6789 OP', 'B 0123 QR'];
            
            echo "   Creating delivery notes for " . count($sales) . " sales...\n";
            echo "   - 15 Delivered (Diterima)\n";
            echo "   - 5 In Transit (Dikirim)\n";
            echo "   - 5 Pending (Menunggu)\n\n";
            
            $dnCount = 0;
            $itemCount = 0;
            $invoiceCounter = 1000;
            
            foreach ($sales as $sale) {
                // 70% of sales get delivery notes
                if (rand(1, 100) > 70) {
                    continue;
                }
                
                $dnCount++;
                $invoiceCounter++;
                
                $saleDate = $sale['created_at'];
                $deliveryDate = date('Y-m-d', strtotime($saleDate . ' +' . random_int(1, 3) . ' days'));
                
                // Determine status based on delivery date
                $daysSinceDelivery = (strtotime(date('Y-m-d')) - strtotime($deliveryDate)) / 86400;
                
                if ($daysSinceDelivery > 2) {
                    $status = 'Diterima';
                    $deliveredAt = date('Y-m-d H:i:s', strtotime($deliveryDate . ' +' . random_int(1, 3) . ' days'));
                } elseif ($daysSinceDelivery >= 0) {
                    $status = 'Dikirim';
                    $deliveredAt = null;
                } else {
                    $status = 'Pending';
                    $deliveredAt = null;
                }
                
                // Get customer
                $customer = null;
                foreach ($customers as $c) {
                    if ($c['id'] == $sale['customer_id']) {
                        $customer = $c;
                        break;
                    }
                }
                
                // Get sale items
                $saleItems = $db->table('sale_items')
                    ->select('sale_items.product_id, sale_items.quantity, products.name as product_name, products.unit')
                    ->join('products', 'products.id = sale_items.product_id')
                    ->where('sale_id', $sale['id'])
                    ->get()
                    ->getResultArray();
                
                // Insert delivery note
                $dnData = [
                    'delivery_number' => 'SJ-' . date('Ymd', strtotime($deliveryDate)) . '-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                    'delivery_date' => $deliveryDate,
                    'sale_id' => $sale['id'],
                    'customer_id' => $sale['customer_id'],
                    'recipient_name' => $customer ? $customer['name'] : 'Unknown',
                    'recipient_address' => $customer ? $customer['address'] : 'Unknown',
                    'driver_name' => $drivers[array_rand($drivers)],
                    'vehicle_number' => $vehicles[array_rand($vehicles)],
                    'notes' => $this->getDNNotes($status),
                    'status' => $status,
                    'delivered_at' => $deliveredAt,
                    'created_at' => $saleDate
                ];
                
                $db->table('delivery_notes')->insert($dnData);
                $dnId = $db->insertID();
                
                // Insert delivery note items
                foreach ($saleItems as $item) {
                    $db->table('delivery_note_items')->insert([
                        'delivery_note_id' => $dnId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'notes' => $item['product_name']
                    ]);
                    
                    $itemCount++;
                }
                
                // Display progress
                $statusEmoji = $status === 'Diterima' ? '✅' : ($status === 'Dikirim' ? '🚛' : '⏳');
                echo sprintf(
                    "   %s [%d] %s | %s | Items: %d | Status: %s\n",
                    $statusEmoji,
                    $dnCount,
                    $dnData['delivery_number'],
                    $sale['invoice_number'],
                    count($saleItems),
                    $status
                );
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }
            
            echo "\n✅ Delivery Notes Seeding Complete!\n";
            echo "   📦 Total Delivery Notes: {$dnCount}\n";
            echo "   📦 Total Items: {$itemCount}\n";
            
            // Show summary
            $totalDNs = $db->table('delivery_notes')->countAll();
            $deliveredCount = $db->table('delivery_notes')->where('status', 'Diterima')->countAll();
            $shippedCount = $db->table('delivery_notes')->where('status', 'Dikirim')->countAll();
            $pendingCount = $db->table('delivery_notes')->where('status', 'Pending')->countAll();
            
            echo "\n📊 Delivery Status Summary:\n";
            echo "   ✅ Delivered: {$deliveredCount}\n";
            echo "   🚛 In Transit: {$shippedCount}\n";
            echo "   ⏳ Pending: {$pendingCount}\n";
            echo "   📦 Total: {$totalDNs}\n";
            
        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
    
    private function getDNNotes($status)
    {
        $notes = [
            'Pending' => 'Menunggu jadwal pengiriman',
            'Dikirim' => 'Barang dalam perjalanan menuju lokasi tujuan',
            'Diterima' => 'Barang sudah diterima dengan baik oleh penerima'
        ];
        
        return $notes[$status] ?? '';
    }
}
