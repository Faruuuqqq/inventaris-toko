<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestTransactionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get test data
        $customers = $db->table('customers')->limit(2)->get()->getResultArray();
        $suppliers = $db->table('suppliers')->limit(2)->get()->getResultArray();
        $products = $db->table('products')->limit(5)->get()->getResultArray();
        
        if (empty($customers) || empty($suppliers) || empty($products)) {
            echo "Skipping transaction seeding - missing master data\n";
            return;
        }
        
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Regular Sales
        $salesData = [
            [
                'invoice_number' => 'INV-' . date('Ymd') . '001',
                'customer_id' => $customers[0]['id'],
                'user_id' => 1, // Admin
                'payment_type' => 'CASH',
                'payment_status' => 'PAID',
                'total_amount' => 1500000,
                'paid_amount' => 1500000,
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 hours'))
            ],
            [
                'invoice_number' => 'INV-' . date('Ymd') . '002',
                'customer_id' => $customers[1]['id'],
                'user_id' => 1,
                'payment_type' => 'CREDIT',
                'payment_status' => 'UNPAID',
                'total_amount' => 2500000,
                'paid_amount' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-20 hours'))
            ]
        ];
        
        // Regular Purchases
        $purchaseData = [
            [
                'nomor_po' => 'PO-' . date('Ymd') . '001',
                'supplier_id' => $suppliers[0]['id'],
                'user_id' => 1,
                'status' => 'Diterima',
                'payment_status' => 'PAID',
                'total_amount' => 3000000,
                'paid_amount' => 3000000,
                'tanggal_po' => $today,
                'created_at' => date('Y-m-d H:i:s', strtotime('-8 hours'))
            ]
        ];
        
        // Insert Sales
        $db->disableForeignKeyChecks();
        foreach ($salesData as $sale) {
            $db->table('sales')->insert($sale);
            echo "Inserted sale: {$sale['invoice_number']}\n";
        }
        
        // Insert Purchases
        foreach ($purchaseData as $purchase) {
            $db->table('purchase_orders')->insert($purchase);
            echo "Inserted purchase: {$purchase['nomor_po']}\n";
        }
        
        $db->enableForeignKeyChecks();
        
        echo "Test transactions seeded successfully!\n";
        echo "- 2 Sales (regular transactions)\n";
        echo "- 1 Purchase (regular transaction)\n";
        echo "- CSV export functionality ready for testing\n";
    }
}