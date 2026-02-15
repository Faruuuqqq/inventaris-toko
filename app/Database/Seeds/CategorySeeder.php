<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Elektronik',
                'description' => 'Produk elektronik seperti laptop, monitor, dll',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Aksesoris',
                'description' => 'Aksesoris komputer dan elektronik',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Storage',
                'description' => 'Penyimpanan data seperti flashdisk, SSD',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Perlengkapan Kantor',
                'description' => 'Printer, scanner, dan perlengkapan kantor lainnya',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}