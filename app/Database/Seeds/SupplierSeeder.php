<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'PT. Distributor Indonesia',
                'email' => 'order@distributorindo.com',
                'phone' => '021-5559876',
                'address' => 'Jl. Industri No. 100, Jakarta Utara',
                'city' => 'Jakarta',
                'npwp' => '98.765.432.1-987.000',
                'credit_limit' => 100000000,
                'credit_limit_used' => 0,
                'payment_terms' => 30,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'CV. Sukses Makmur',
                'email' => 'info@sukesmakmur.co.id',
                'phone' => '022-7654321',
                'address' => 'Jl. Pabrik No. 50, Cimahi',
                'city' => 'Bandung',
                'npwp' => '87.654.321.0-876.000',
                'credit_limit' => 75000000,
                'credit_limit_used' => 25000000,
                'payment_terms' => 14,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'PT. Supplier Jaya',
                'email' => 'purchase@supplierjaya.com',
                'phone' => '031-8901234',
                'address' => 'Jl. Gresik No. 25, Surabaya',
                'city' => 'Surabaya',
                'npwp' => '76.543.210.9-765.000',
                'credit_limit' => 50000000,
                'credit_limit_used' => 10000000,
                'payment_terms' => 7,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('suppliers')->insertBatch($data);
    }
}