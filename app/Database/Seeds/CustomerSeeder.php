<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'PT. Maju Jaya',
                'email' => 'billing@majujaya.com',
                'phone' => '021-5551234',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'city' => 'Jakarta',
                'npwp' => '12.345.678.9-123.000',
                'credit_limit' => 50000000,
                'credit_limit_used' => 0,
                'payment_terms' => 30,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'CV. Sentosa Abadi',
                'email' => 'finance@sentosa.com',
                'phone' => '022-7654321',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung',
                'city' => 'Bandung',
                'npwp' => '23.456.789.0-456.000',
                'credit_limit' => 30000000,
                'credit_limit_used' => 15000000,
                'payment_terms' => 14,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'UD. Makmur Sejahtera',
                'email' => 'order@makmur.com',
                'phone' => '031-8765432',
                'address' => 'Jl. Ahmad Yani No. 789, Surabaya',
                'city' => 'Surabaya',
                'npwp' => '34.567.890.1-789.000',
                'credit_limit' => 20000000,
                'credit_limit_used' => 5000000,
                'payment_terms' => 7,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('customers')->insertBatch($data);
    }
}