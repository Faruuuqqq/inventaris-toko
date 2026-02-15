<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExpensesSeeder extends Seeder
{
    public function run()
    {
        echo "\n💰 Creating Expenses...\n";

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get users
            $users = $db->table('users')->select('id')->limit(5)->get()->getResultArray();
            if (empty($users)) {
                echo "   ⚠️  No users found\n";
                return;
            }

            $expenseCount = 0;
            $today = strtotime('2026-02-08');
            $startDate = strtotime('-60 days', $today);

            // Define expense categories with probability and amount range
            $categories = [
                'OPERASIONAL' => ['probability' => 30, 'min' => 100000, 'max' => 500000],
                'TRANSPORTASI' => ['probability' => 20, 'min' => 200000, 'max' => 800000],
                'LISTRIK' => ['probability' => 15, 'min' => 500000, 'max' => 2000000],
                'GAJI' => ['probability' => 10, 'min' => 5000000, 'max' => 20000000],
                'SEWA' => ['probability' => 10, 'min' => 10000000, 'max' => 30000000],
                'PERBAIKAN' => ['probability' => 15, 'min' => 200000, 'max' => 1500000],
            ];

            $descriptions = [
                'OPERASIONAL' => [
                    'Pembelian supply kantor',
                    'Maintenance server',
                    'Pembelian kabel dan peralatan',
                    'Cleaning supplies',
                    'IT equipment',
                ],
                'TRANSPORTASI' => [
                    'Bensin kendaraan operasional',
                    'Biaya pengiriman customer',
                    'Maintenance kendaraan',
                    'Parkir dan tol',
                    'Oli dan sparepart',
                ],
                'LISTRIK' => [
                    'Tagihan listrik',
                    'Tagihan air',
                    'Internet dan telepon',
                ],
                'GAJI' => [
                    'Gaji karyawan bulan %month%',
                    'Tunjangan karyawan',
                    'Bonus tahunan',
                ],
                'SEWA' => [
                    'Biaya sewa gudang',
                    'Biaya sewa kantor',
                    'Biaya sewa showroom',
                ],
                'PERBAIKAN' => [
                    'Perbaikan mesin kasir',
                    'Perbaikan AC',
                    'Perbaikan pintu',
                    'Maintenance komputer',
                    'Renovasi toko',
                ],
            ];

            $paymentMethods = ['CASH', 'TRANSFER', 'CHEQUE'];

            echo "   Generating 30-50 expenses across 60 days...\n";

            $expenseNum = random_int(30, 50);

            for ($i = 0; $i < $expenseNum; $i++) {
                // Select category based on probability
                $rand = random_int(1, 100);
                $cumulative = 0;
                $selectedCategory = 'OPERASIONAL';

                foreach ($categories as $cat => $config) {
                    $cumulative += $config['probability'];
                    if ($rand <= $cumulative) {
                        $selectedCategory = $cat;
                        break;
                    }
                }

                $catConfig = $categories[$selectedCategory];
                $expenseDate = date('Y-m-d', random_int($startDate, $today));
                $amount = random_int($catConfig['min'], $catConfig['max']);
                
                // Generate description
                $descList = $descriptions[$selectedCategory];
                $description = $descList[array_rand($descList)];
                if (strpos($description, '%month%') !== false) {
                    $description = str_replace('%month%', date('F Y', strtotime($expenseDate)), $description);
                }

                // Generate expense number
                $expenseNo = 'EXP-' . date('Y-m', strtotime($expenseDate)) . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT);

                $db->table('expenses')->insert([
                    'expense_number' => $expenseNo,
                    'expense_date' => $expenseDate,
                    'category' => $selectedCategory,
                    'description' => $description,
                    'amount' => $amount,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'user_id' => $users[array_rand($users)]['id'],
                    'created_at' => date('Y-m-d H:i:s', strtotime($expenseDate)),
                ]);

                $expenseCount++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            echo "\n✅ Expenses seeding complete!\n";
            echo "   Total expenses: {$expenseCount}\n\n";

        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
}
