<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'description' => 'Pembelian ATK Kantor',
                'amount' => 1500000,
                'expense_date' => date('Y-m-d', strtotime('-15 days')),
                'category' => 'operational',
                'payment_method' => 'cash',
                'receipt_number' => 'EXP-001',
                'status' => 'approved',
                'notes' => 'Kertas, pulpen, stapler, map plastik',
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
            ],
            [
                'description' => 'Biaya Marketing Digital',
                'amount' => 2500000,
                'expense_date' => date('Y-m-d', strtotime('-10 days')),
                'category' => 'marketing',
                'payment_method' => 'transfer',
                'receipt_number' => 'EXP-002',
                'status' => 'approved',
                'notes' => 'Iklan Google Ads, Instagram Ads',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'description' => 'Gaji Karyawan Bulanan',
                'amount' => 15000000,
                'expense_date' => date('Y-m-d', strtotime('-5 days')),
                'category' => 'salary',
                'payment_method' => 'transfer',
                'receipt_number' => 'EXP-003',
                'status' => 'approved',
                'notes' => 'Gaji bulan Januari 2024',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'description' => 'Maintenance Server',
                'amount' => 3500000,
                'expense_date' => date('Y-m-d', strtotime('-3 days')),
                'category' => 'maintenance',
                'payment_method' => 'transfer',
                'receipt_number' => 'EXP-004',
                'status' => 'pending', // For approval test
                'notes' => 'Upgrade RAM dan cleaning service',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'description' => 'Transportasi Pengiriman',
                'amount' => 850000,
                'expense_date' => date('Y-m-d', strtotime('-1 days')),
                'category' => 'operational',
                'payment_method' => 'cash',
                'receipt_number' => 'EXP-005',
                'status' => 'approved',
                'notes' => 'Ongkos kirim ke customer Jakarta',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days'))
            ]
        ];

        $this->db->table('expenses')->insertBatch($data);
    }
}