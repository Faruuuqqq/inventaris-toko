<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase4TestDataSeeder extends Seeder
{
    public function run()
    {
        echo "📊 Adding Phase 4 Test Data..." . PHP_EOL . PHP_EOL;
        
        // 1. Add Categories (ignore duplicates)
        echo "📁 Adding categories..." . PHP_EOL;
        $categories = [
            ['id' => 1, 'name' => 'Elektronik'],
            ['id' => 2, 'name' => 'Pakaian'],
            ['id' => 3, 'name' => 'Makanan & Minuman'],
            ['id' => 4, 'name' => 'Alat Tulis'],
            ['id' => 5, 'name' => 'Kesehatan'],
        ];
        
        foreach ($categories as $cat) {
            try {
                $existing = $this->db->table('categories')->where('id', $cat['id'])->get()->getRow();
                if (!$existing) {
                    $this->db->table('categories')->insert($cat);
                    echo "   ✅ Added category: " . $cat['name'] . PHP_EOL;
                } else {
                    echo "   ⏭️  Category exists: " . $cat['name'] . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped category: " . $cat['name'] . PHP_EOL;
            }
        }

        // 2. Add Warehouses (if not exists)
        if ($this->db->table('warehouses')->countAll() == 0) {
            $this->db->table('warehouses')->insertBatch([
                [
                    'id' => 1,
                    'code' => 'WH-01',
                    'name' => 'Gudang Utama',
                    'address' => 'Jl. Raya Industri No. 123',
                    'is_active' => 1
                ],
                [
                    'id' => 2,
                    'code' => 'WH-02',
                    'name' => 'Gudang Cabang',
                    'address' => 'Jl. Perdagangan No. 45',
                    'is_active' => 1
                ]
            ]);
        }

        // 3. Add Users (if not exists)
        if ($this->db->table('users')->countAll() == 0) {
            $this->db->table('users')->insertBatch([
                [
                    'id' => 1,
                    'username' => 'admin',
                    'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                    'fullname' => 'Administrator',
                    'role' => 'ADMIN',
                    'is_active' => 1,
                    'email' => 'admin@tokomanager.com',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'username' => 'owner',
                    'password_hash' => password_hash('owner123', PASSWORD_DEFAULT),
                    'fullname' => 'Owner Toko',
                    'role' => 'OWNER',
                    'is_active' => 1,
                    'email' => 'owner@tokomanager.com',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // 4. Add Products with full data
        $products = [
            // Elektronik
            ['sku' => 'ELK-001', 'name' => 'Laptop ASUS ROG', 'category_id' => 1, 'unit' => 'Unit', 'price_buy' => 12000000, 'price_sell' => 15000000, 'price' => 15000000, 'cost_price' => 12000000, 'min_stock_alert' => 5, 'min_stock' => 5, 'max_stock' => 20],
            ['sku' => 'ELK-002', 'name' => 'Mouse Logitech Wireless', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 150000, 'price_sell' => 250000, 'price' => 250000, 'cost_price' => 150000, 'min_stock_alert' => 10, 'min_stock' => 10, 'max_stock' => 50],
            ['sku' => 'ELK-003', 'name' => 'Keyboard Mechanical RGB', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 400000, 'price_sell' => 650000, 'price' => 650000, 'cost_price' => 400000, 'min_stock_alert' => 8, 'min_stock' => 8, 'max_stock' => 30],
            ['sku' => 'ELK-004', 'name' => 'Headset Gaming', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 350000, 'price_sell' => 550000, 'price' => 550000, 'cost_price' => 350000, 'min_stock_alert' => 12, 'min_stock' => 12, 'max_stock' => 40],
            ['sku' => 'ELK-005', 'name' => 'Webcam HD 1080p', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 250000, 'price_sell' => 400000, 'price' => 400000, 'cost_price' => 250000, 'min_stock_alert' => 15, 'min_stock' => 15, 'max_stock' => 60],
            
            // Pakaian
            ['sku' => 'PAK-001', 'name' => 'Kaos Polos Premium', 'category_id' => 2, 'unit' => 'Pcs', 'price_buy' => 35000, 'price_sell' => 65000, 'price' => 65000, 'cost_price' => 35000, 'min_stock_alert' => 50, 'min_stock' => 50, 'max_stock' => 200],
            ['sku' => 'PAK-002', 'name' => 'Celana Jeans Slim Fit', 'category_id' => 2, 'unit' => 'Pcs', 'price_buy' => 120000, 'price_sell' => 200000, 'price' => 200000, 'cost_price' => 120000, 'min_stock_alert' => 30, 'min_stock' => 30, 'max_stock' => 100],
            ['sku' => 'PAK-003', 'name' => 'Jaket Hoodie', 'category_id' => 2, 'unit' => 'Pcs', 'price_buy' => 80000, 'price_sell' => 150000, 'price' => 150000, 'cost_price' => 80000, 'min_stock_alert' => 25, 'min_stock' => 25, 'max_stock' => 80],
            
            // Makanan & Minuman
            ['sku' => 'MKN-001', 'name' => 'Kopi Arabika Premium 250gr', 'category_id' => 3, 'unit' => 'Pack', 'price_buy' => 45000, 'price_sell' => 75000, 'price' => 75000, 'cost_price' => 45000, 'min_stock_alert' => 20, 'min_stock' => 20, 'max_stock' => 100],
            ['sku' => 'MKN-002', 'name' => 'Teh Hijau Organik', 'category_id' => 3, 'unit' => 'Box', 'price_buy' => 30000, 'price_sell' => 55000, 'price' => 55000, 'cost_price' => 30000, 'min_stock_alert' => 30, 'min_stock' => 30, 'max_stock' => 150],
            ['sku' => 'MKN-003', 'name' => 'Snack Kemasan 100gr', 'category_id' => 3, 'unit' => 'Pack', 'price_buy' => 8000, 'price_sell' => 15000, 'price' => 15000, 'cost_price' => 8000, 'min_stock_alert' => 100, 'min_stock' => 100, 'max_stock' => 500],
            
            // Alat Tulis
            ['sku' => 'ATK-001', 'name' => 'Pulpen Gel 0.5mm', 'category_id' => 4, 'unit' => 'Pcs', 'price_buy' => 2500, 'price_sell' => 5000, 'price' => 5000, 'cost_price' => 2500, 'min_stock_alert' => 200, 'min_stock' => 200, 'max_stock' => 1000],
            ['sku' => 'ATK-002', 'name' => 'Buku Tulis 100 Lembar', 'category_id' => 4, 'unit' => 'Pcs', 'price_buy' => 5000, 'price_sell' => 10000, 'price' => 10000, 'cost_price' => 5000, 'min_stock_alert' => 150, 'min_stock' => 150, 'max_stock' => 600],
            ['sku' => 'ATK-003', 'name' => 'Spidol Whiteboard', 'category_id' => 4, 'unit' => 'Pcs', 'price_buy' => 6000, 'price_sell' => 12000, 'price' => 12000, 'cost_price' => 6000, 'min_stock_alert' => 80, 'min_stock' => 80, 'max_stock' => 300],
            
            // Kesehatan
            ['sku' => 'KSH-001', 'name' => 'Masker Medis 50pcs', 'category_id' => 5, 'unit' => 'Box', 'price_buy' => 25000, 'price_sell' => 45000, 'price' => 45000, 'cost_price' => 25000, 'min_stock_alert' => 40, 'min_stock' => 40, 'max_stock' => 200],
            ['sku' => 'KSH-002', 'name' => 'Hand Sanitizer 500ml', 'category_id' => 5, 'unit' => 'Btl', 'price_buy' => 20000, 'price_sell' => 35000, 'price' => 35000, 'cost_price' => 20000, 'min_stock_alert' => 50, 'min_stock' => 50, 'max_stock' => 250],
            ['sku' => 'KSH-003', 'name' => 'Vitamin C 1000mg', 'category_id' => 5, 'unit' => 'Strip', 'price_buy' => 15000, 'price_sell' => 28000, 'price' => 28000, 'cost_price' => 15000, 'min_stock_alert' => 60, 'min_stock' => 60, 'max_stock' => 300],
        ];

        echo PHP_EOL . "📦 Adding products..." . PHP_EOL;
        foreach ($products as $product) {
            $product['created_at'] = date('Y-m-d H:i:s');
            try {
                $existing = $this->db->table('products')->where('sku', $product['sku'])->get()->getRow();
                if (!$existing) {
                    $this->db->table('products')->insert($product);
                    echo "   ✅ Added product: " . $product['name'] . PHP_EOL;
                } else {
                    echo "   ⏭️  Product exists: " . $product['name'] . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped product: " . $product['name'] . PHP_EOL;
            }
        }

        // 5. Add Product Stocks (varied levels - some low, some normal, some high, some zero)
        echo PHP_EOL . "📊 Adding product stocks..." . PHP_EOL;
        // First, get product IDs by SKU
        $productIds = [];
        $skus = ['ELK-001', 'ELK-002', 'ELK-003', 'ELK-004', 'ELK-005', 'PAK-001', 'PAK-002', 'PAK-003',
                 'MKN-001', 'MKN-002', 'MKN-003', 'ATK-001', 'ATK-002', 'ATK-003', 'KSH-001', 'KSH-002', 'KSH-003'];
        
        foreach ($skus as $sku) {
            $product = $this->db->table('products')->where('sku', $sku)->get()->getRow();
            if ($product) {
                $productIds[$sku] = $product->id;
            }
        }
        
        $stocks = [
            // Normal stock
            [$productIds['ELK-001'], 1, 15, 5], // Laptop
            [$productIds['ELK-002'], 1, 35, 10], // Mouse
            [$productIds['ELK-003'], 1, 22, 8], // Keyboard
            
            // Low stock
            [$productIds['ELK-004'], 1, 8, 12], // Headset (LOW)
            [$productIds['ELK-005'], 1, 12, 15], // Webcam (LOW)
            
            // Out of stock
            [$productIds['PAK-001'], 1, 0, 50], // Kaos (OUT)
            
            // Overstock
            [$productIds['PAK-002'], 1, 120, 30], // Celana (OVERSTOCK)
            
            // Normal
            [$productIds['PAK-003'], 1, 50, 25], // Jaket
            [$productIds['MKN-001'], 1, 65, 20], // Kopi
            [$productIds['MKN-002'], 1, 88, 30], // Teh
            
            // Low stock
            [$productIds['MKN-003'], 1, 85, 100], // Snack (LOW)
            
            // Normal
            [$productIds['ATK-001'], 1, 450, 200], // Pulpen
            [$productIds['ATK-002'], 1, 280, 150], // Buku
            [$productIds['ATK-003'], 1, 175, 80], // Spidol
            [$productIds['KSH-001'], 1, 95, 40], // Masker
            [$productIds['KSH-002'], 1, 130, 50], // Sanitizer
            [$productIds['KSH-003'], 1, 200, 60], // Vitamin
        ];

        foreach ($stocks as $stock) {
            try {
                $existing = $this->db->table('product_stocks')
                    ->where('product_id', $stock[0])
                    ->where('warehouse_id', $stock[1])
                    ->get()->getRow();
                    
                if (!$existing) {
                    $this->db->table('product_stocks')->insert([
                        'product_id' => $stock[0],
                        'warehouse_id' => $stock[1],
                        'quantity' => $stock[2],
                        'min_stock_alert' => $stock[3]
                    ]);
                    echo "   ✅ Added stock for product ID: " . $stock[0] . PHP_EOL;
                } else {
                    // Update existing stock
                    $this->db->table('product_stocks')
                        ->where('product_id', $stock[0])
                        ->where('warehouse_id', $stock[1])
                        ->update(['quantity' => $stock[2], 'min_stock_alert' => $stock[3]]);
                    echo "   🔄 Updated stock for product ID: " . $stock[0] . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped stock for product ID: " . $stock[0] . PHP_EOL;
            }
        }

        echo PHP_EOL . "👥 Adding customers..." . PHP_EOL;
        $customers = [
            [
                'id' => 1,
                'code' => 'CUST-001',
                'name' => 'PT Maju Jaya',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 100, Jakarta',
                'credit_limit' => 50000000,
                'receivable_balance' => 15000000,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'code' => 'CUST-002',
                'name' => 'CV Berkah Sentosa',
                'phone' => '082345678901',
                'address' => 'Jl. Gatot Subroto No. 50, Bandung',
                'credit_limit' => 30000000,
                'receivable_balance' => 8000000,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'code' => 'CUST-003',
                'name' => 'Toko Sejahtera',
                'phone' => '083456789012',
                'address' => 'Jl. Ahmad Yani No. 25, Surabaya',
                'credit_limit' => 20000000,
                'receivable_balance' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'code' => 'CUST-004',
                'name' => 'PT Indo Prima',
                'phone' => '084567890123',
                'address' => 'Jl. Diponegoro No. 75, Semarang',
                'credit_limit' => 40000000,
                'receivable_balance' => 22000000,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'code' => 'CUST-005',
                'name' => 'Andi Wijaya',
                'phone' => '085678901234',
                'address' => 'Jl. Veteran No. 12, Yogyakarta',
                'credit_limit' => 10000000,
                'receivable_balance' => 3500000,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        foreach ($customers as $customer) {
            try {
                $existing = $this->db->table('customers')->where('code', $customer['code'])->get()->getRow();
                if (!$existing) {
                    $this->db->table('customers')->insert($customer);
                    echo "   ✅ Added customer: " . $customer['name'] . PHP_EOL;
                } else {
                    echo "   ⏭️  Customer exists: " . $customer['name'] . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped customer: " . $customer['name'] . PHP_EOL;
            }
        }

        // 7. Add Suppliers
        echo PHP_EOL . "🏭 Adding suppliers..." . PHP_EOL;
        $suppliers = [
            [
                'id' => 1,
                'code' => 'SUP-001',
                'name' => 'PT Teknologi Maju',
                'phone' => '021-5551234',
                'address' => 'Jl. Industri Raya No. 88, Jakarta',
                'debt_balance' => 25000000,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'code' => 'SUP-002',
                'name' => 'CV Pakaian Nusantara',
                'phone' => '022-7771234',
                'address' => 'Jl. Tekstil No. 45, Bandung',
                'debt_balance' => 12000000,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'code' => 'SUP-003',
                'name' => 'PT Pangan Sejahtera',
                'phone' => '031-8881234',
                'address' => 'Jl. Makanan No. 20, Surabaya',
                'debt_balance' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        foreach ($suppliers as $supplier) {
            try {
                $existing = $this->db->table('suppliers')->where('code', $supplier['code'])->get()->getRow();
                if (!$existing) {
                    $this->db->table('suppliers')->insert($supplier);
                    echo "   ✅ Added supplier: " . $supplier['name'] . PHP_EOL;
                } else {
                    echo "   ⏭️  Supplier exists: " . $supplier['name'] . PHP_EOL;
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped supplier: " . $supplier['name'] . PHP_EOL;
            }
        }

        // 8. Add Salespersons (if table exists)
        if ($this->db->tableExists('salespersons')) {
            echo PHP_EOL . "💼 Adding salespersons..." . PHP_EOL;
            
            try {
                // Check if 'code' column exists
                if ($this->db->fieldExists('code', 'salespersons')) {
                    $salespersons = [
                        [
                            'id' => 1,
                            'code' => 'SALES-001',
                            'name' => 'Budi Santoso',
                            'phone' => '081111111111',
                            'commission_rate' => 2.5,
                            'is_active' => 1
                        ],
                        [
                            'id' => 2,
                            'code' => 'SALES-002',
                            'name' => 'Siti Nurhaliza',
                            'phone' => '082222222222',
                            'commission_rate' => 3.0,
                            'is_active' => 1
                        ],
                    ];
                } else {
                    // Without 'code' column
                    $salespersons = [
                        [
                            'id' => 1,
                            'name' => 'Budi Santoso',
                            'phone' => '081111111111',
                            'commission_rate' => 2.5,
                            'is_active' => 1
                        ],
                        [
                            'id' => 2,
                            'name' => 'Siti Nurhaliza',
                            'phone' => '082222222222',
                            'commission_rate' => 3.0,
                            'is_active' => 1
                        ],
                    ];
                }
                
                foreach ($salespersons as $salesperson) {
                    $existing = $this->db->table('salespersons')->where('id', $salesperson['id'])->get()->getRow();
                    if (!$existing) {
                        $this->db->table('salespersons')->insert($salesperson);
                        echo "   ✅ Added salesperson: " . $salesperson['name'] . PHP_EOL;
                    } else {
                        echo "   ⏭️  Salesperson exists: " . $salesperson['name'] . PHP_EOL;
                    }
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Skipped salespersons (table structure issue)" . PHP_EOL;
            }
        }

        echo "✅ Phase 4 Test Data Seeded Successfully!" . PHP_EOL;
        echo "   - 5 Categories" . PHP_EOL;
        echo "   - 17 Products (with varied stock levels)" . PHP_EOL;
        echo "   - 17 Product Stocks (Low: 3, Out: 1, Overstock: 1, Normal: 12)" . PHP_EOL;
        echo "   - 5 Customers (with credit data)" . PHP_EOL;
        echo "   - 3 Suppliers (with debt data)" . PHP_EOL;
        echo "   - 2 Warehouses" . PHP_EOL;
        echo "   - 2 Users (admin/admin123, owner/owner123)" . PHP_EOL;
    }
}
