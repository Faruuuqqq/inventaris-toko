<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run()
    {
        echo "\n💳 Creating Payment Records...\n";

        $db = \Config\Database::connect();

        try {
            // Get users for reference
            $users = $db->table('users')->select('id')->limit(5)->get()->getResultArray();
            if (empty($users)) {
                echo "   ⚠️  No users found\n";
                return;
            }

            echo "   Creating RECEIVABLE payments (from customers)...\n";
            
            // 1. RECEIVABLE PAYMENTS - From customers
            $sales = $db->table('sales')
                ->select('id, total_amount, created_at, customer_id, invoice_number')
                ->orderBy('created_at', 'DESC')
                ->limit(100)
                ->get()
                ->getResultArray();

            $receivableCount = 0;
            $today = strtotime('2026-02-08');
            $paymentMethods = ['CASH', 'TRANSFER', 'CHEQUE'];

            foreach ($sales as $sale) {
                $rand = random_int(1, 100);

                // 70% get payments
                if ($rand <= 70) {
                    // Fully paid (60% of 70%)
                    if ($rand <= 42) {
                        $paymentDate = date('Y-m-d', strtotime($sale['created_at'] . ' +' . random_int(1, 3) . ' days'));
                        $this->createPayment($db, $sale['id'], $sale['total_amount'], 
                            $paymentDate, 'RECEIVABLE', $users[array_rand($users)]['id'], $paymentMethods[array_rand($paymentMethods)]);
                        $receivableCount++;
                    }
                    // Partial paid (40% of 70%)
                    else {
                        // Create 1-3 payments
                        $paymentNum = random_int(1, 3);
                        $remaining = $sale['total_amount'];

                        for ($i = 0; $i < $paymentNum; $i++) {
                            $paidPct = ($i === $paymentNum - 1) ? $remaining : $remaining * random_int(20, 60) / 100;
                            $paymentDate = date('Y-m-d', strtotime($sale['created_at'] . ' +' . ($i * 15 + random_int(1, 14)) . ' days'));
                            
                            $this->createPayment($db, $sale['id'], $paidPct, 
                                $paymentDate, 'RECEIVABLE', $users[array_rand($users)]['id'], $paymentMethods[array_rand($paymentMethods)]);
                            
                            $remaining -= $paidPct;
                            $receivableCount++;
                        }
                    }
                }
                // 30% remain unpaid (for aging analysis)
                // Do nothing - leave unpaid
            }

            echo "   ✓ Created {$receivableCount} receivable payments\n";

            echo "   Creating PAYABLE payments (to suppliers)...\n";

            // 2. PAYABLE PAYMENTS - To suppliers
            $pos = $db->table('purchase_orders')
                ->select('id_po, total_amount, tanggal_po, supplier_id, nomor_po')
                ->orderBy('tanggal_po', 'DESC')
                ->limit(100)
                ->get()
                ->getResultArray();

            $payableCount = 0;

            foreach ($pos as $po) {
                $rand = random_int(1, 100);

                // 60% get fully paid
                if ($rand <= 60) {
                    $paymentDate = date('Y-m-d', strtotime($po['tanggal_po'] . ' +' . random_int(1, 5) . ' days'));
                    $this->createPayment($db, $po['id_po'], $po['total_amount'], 
                        $paymentDate, 'PAYABLE', $users[array_rand($users)]['id'], $paymentMethods[array_rand($paymentMethods)]);
                    $payableCount++;
                }
                // 20% partially paid
                elseif ($rand <= 80) {
                    $paymentNum = random_int(1, 2);
                    $remaining = $po['total_amount'];

                    for ($i = 0; $i < $paymentNum; $i++) {
                        $paidPct = ($i === $paymentNum - 1) ? $remaining : $remaining * random_int(30, 70) / 100;
                        $paymentDate = date('Y-m-d', strtotime($po['tanggal_po'] . ' +' . ($i * 20 + random_int(1, 19)) . ' days'));
                        
                        $this->createPayment($db, $po['id_po'], $paidPct, 
                            $paymentDate, 'PAYABLE', $users[array_rand($users)]['id'], $paymentMethods[array_rand($paymentMethods)]);
                        
                        $remaining -= $paidPct;
                        $payableCount++;
                    }
                }
                // 20% remain unpaid
            }

            echo "   ✓ Created {$payableCount} payable payments\n";

            echo "\n✅ Payments seeding complete!\n";
            echo "   Receivable payments: {$receivableCount}\n";
            echo "   Payable payments: {$payableCount}\n\n";

        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    private function createPayment($db, $referenceId, $amount, $paymentDate, $type, $userId, $method)
    {
        // Generate payment number
        $paymentNo = 'PAY-' . date('Y-m', strtotime($paymentDate)) . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

        $data = [
            'payment_number' => $paymentNo,
            'payment_date' => $paymentDate,
            'type' => $type,
            'reference_id' => $referenceId,
            'amount' => $amount,
            'method' => $method,
            'notes' => $type === 'RECEIVABLE' ? 'Pembayaran dari customer' : 'Pembayaran ke supplier',
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s', strtotime($paymentDate)),
        ];

        $db->table('payments')->insert($data);
    }

}
