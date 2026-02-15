<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Laptop ASUS ROG Strix',
                'sku' => 'LAP-001',
                'category_id' => 1,
                'price_buy' => 15000000,
                'price_sell' => 18500000,
                'stock' => 25,
                'min_stock' => 5,
                'unit' => 'unit',
                'description' => 'Gaming laptop with high performance',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Mouse Logitech MX Master 3',
                'sku' => 'ACC-001',
                'category_id' => 2,
                'price_buy' => 850000,
                'price_sell' => 1100000,
                'stock' => 100,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Wireless gaming mouse',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Keyboard Mechanical RGB',
                'sku' => 'ACC-002',
                'category_id' => 2,
                'price_buy' => 650000,
                'price_sell' => 850000,
                'stock' => 50,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Mechanical keyboard with RGB backlight',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Monitor LG 27 inch 4K',
                'sku' => 'MON-001',
                'category_id' => 1,
                'price_buy' => 5500000,
                'price_sell' => 6750000,
                'stock' => 15,
                'min_stock' => 3,
                'unit' => 'unit',
                'description' => '4K IPS monitor with HDR',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'USB Flash Drive 64GB',
                'sku' => 'STO-001',
                'category_id' => 3,
                'price_buy' => 75000,
                'price_sell' => 95000,
                'stock' => 200,
                'min_stock' => 20,
                'unit' => 'pcs',
                'description' => 'High speed USB 3.0 flash drive',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Printer Epson L3150',
                'sku' => 'OFF-001',
                'category_id' => 4,
                'price_buy' => 1200000,
                'price_sell' => 1450000,
                'stock' => 8,
                'min_stock' => 2,
                'unit' => 'unit',
                'description' => 'Inkjet color printer',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Headset Bluetooth JBL',
                'sku' => 'AUD-001',
                'category_id' => 2,
                'price_buy' => 450000,
                'price_sell' => 600000,
                'stock' => 75,
                'min_stock' => 15,
                'unit' => 'pcs',
                'description' => 'Wireless bluetooth headset',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'External SSD 1TB',
                'sku' => 'STO-002',
                'category_id' => 3,
                'price_buy' => 1200000,
                'price_sell' => 1500000,
                'stock' => 35,
                'min_stock' => 5,
                'unit' => 'pcs',
                'description' => 'NVMe SSD with USB-C adapter',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Webcam Full HD 1080p',
                'sku' => 'ACC-003',
                'category_id' => 2,
                'price_buy' => 325000,
                'price_sell' => 425000,
                'stock' => 60,
                'min_stock' => 10,
                'unit' => 'pcs',
                'description' => 'Full HD webcam with microphone',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Power Bank 20000mAh',
                'sku' => 'ACC-004',
                'category_id' => 2,
                'price_buy' => 275000,
                'price_sell' => 350000,
                'stock' => 120,
                'min_stock' => 25,
                'unit' => 'pcs',
                'description' => 'High capacity power bank with fast charging',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('products')->insertBatch($data);
    }
}