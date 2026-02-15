<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Gudang Utama Jakarta',
                'code' => 'WH-JKT-01',
                'address' => 'Jl. Industri Raya No. 100, Jakarta Utara',
                'city' => 'Jakarta',
                'capacity' => 5000,
                'manager' => 'Budi Santoso',
                'phone' => '021-5551234',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Gudang Cabang Bandung',
                'code' => 'WH-BDG-01',
                'address' => 'Jl. Pabrik No. 50, Cimahi',
                'city' => 'Bandung',
                'capacity' => 3000,
                'manager' => 'Ahmad Wijaya',
                'phone' => '022-7654321',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Gudang Surabaya',
                'code' => 'WH-SBY-01',
                'address' => 'Jl. Gresik No. 25, Surabaya Timur',
                'city' => 'Surabaya',
                'capacity' => 4000,
                'manager' => 'Siti Nurhayati',
                'phone' => '031-8901234',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('warehouses')->insertBatch($data);
    }
}