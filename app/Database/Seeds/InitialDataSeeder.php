<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Truncate tables to clear existing data
        $db = \Config\Database::connect();
        
        // Disable foreign key checks temporarily
        $db->disableForeignKeyChecks();
        $db->table('users')->truncate();
        $db->table('warehouses')->truncate();
        $db->table('categories')->truncate();
        $db->table('products')->truncate();
        $db->table('product_stocks')->truncate();
        $db->table('customers')->truncate();
        $db->table('suppliers')->truncate();
        $db->table('salespersons')->truncate();
        $db->table('system_config')->truncate();
        $db->enableForeignKeyChecks();

        // 1. Insert Users (password: test123)
        $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
        
        $users = [
            [
                'username' => 'owner',
                'password_hash' => $passwordHash,
                'fullname' => 'Owner',
                'role' => 'OWNER',
                'is_active' => 1,
                'email' => 'owner@toko.com',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'admin',
                'password_hash' => $passwordHash,
                'fullname' => 'Administrator',
                'role' => 'ADMIN',
                'is_active' => 1,
                'email' => 'admin@toko.com',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'gudang',
                'password_hash' => $passwordHash,
                'fullname' => 'Staff Gudang',
                'role' => 'GUDANG',
                'is_active' => 1,
                'email' => 'gudang@toko.com',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'sales',
                'password_hash' => $passwordHash,
                'fullname' => 'Salesman',
                'role' => 'SALES',
                'is_active' => 1,
                'email' => 'sales@toko.com',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('users')->insertBatch($users);
        echo "✓ Inserted 4 users\n";

        // 2. Insert Warehouse
        $warehouse = [
            'code' => 'G01',
            'name' => 'Gudang Utama',
            'address' => 'Jl. Utama No. 1',
            'is_active' => 1,
        ];
        
        $this->db->table('warehouses')->insert($warehouse);
        echo "✓ Inserted 1 warehouse\n";

        // 3. Insert Categories
        $categories = [
            ['name' => 'Elektronik'],
            ['name' => 'Makanan'],
            ['name' => 'Minuman'],
            ['name' => 'Pakaian'],
            ['name' => 'Lainnya'],
        ];
        
        $this->db->table('categories')->insertBatch($categories);
        echo "✓ Inserted 5 categories\n";

        // 4. Insert Products
        $products = [
            [
                'sku' => 'SKU001',
                'name' => 'Laptop ASUS',
                'category_id' => 1,
                'unit' => 'Pcs',
                'price_buy' => 5000000,
                'price_sell' => 6000000,
                'min_stock_alert' => 5,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'sku' => 'SKU002',
                'name' => 'Mouse Wireless',
                'category_id' => 1,
                'unit' => 'Pcs',
                'price_buy' => 50000,
                'price_sell' => 75000,
                'min_stock_alert' => 20,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'sku' => 'SKU003',
                'name' => 'Keyboard RGB',
                'category_id' => 1,
                'unit' => 'Pcs',
                'price_buy' => 200000,
                'price_sell' => 300000,
                'min_stock_alert' => 15,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'sku' => 'SKU004',
                'name' => 'Monitor 24"',
                'category_id' => 1,
                'unit' => 'Pcs',
                'price_buy' => 1500000,
                'price_sell' => 1800000,
                'min_stock_alert' => 3,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'sku' => 'SKU005',
                'name' => 'Flashdisk 32GB',
                'category_id' => 1,
                'unit' => 'Pcs',
                'price_buy' => 35000,
                'price_sell' => 50000,
                'min_stock_alert' => 50,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('products')->insertBatch($products);
        echo "✓ Inserted 5 products\n";

        // 5. Insert Product Stocks
        $stocks = [
            ['product_id' => 1, 'warehouse_id' => 1, 'quantity' => 20, 'min_stock_alert' => 5],
            ['product_id' => 2, 'warehouse_id' => 1, 'quantity' => 50, 'min_stock_alert' => 20],
            ['product_id' => 3, 'warehouse_id' => 1, 'quantity' => 30, 'min_stock_alert' => 15],
            ['product_id' => 4, 'warehouse_id' => 1, 'quantity' => 10, 'min_stock_alert' => 3],
            ['product_id' => 5, 'warehouse_id' => 1, 'quantity' => 100, 'min_stock_alert' => 50],
        ];
        
        $this->db->table('product_stocks')->insertBatch($stocks);
        echo "✓ Inserted 5 product stocks\n";

        // 6. Insert Customers
        $customers = [
            [
                'code' => 'CUST001',
                'name' => 'PT Maju Jaya',
                'phone' => '08123456789',
                'address' => 'Jl. Sudirman No. 1',
                'credit_limit' => 50000000,
                'receivable_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'CUST002',
                'name' => 'CV Berkah Sejahtera',
                'phone' => '08987654321',
                'address' => 'Jl. Gatot Subroto No. 2',
                'credit_limit' => 30000000,
                'receivable_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'CUST003',
                'name' => 'Toko Sejahtera',
                'phone' => '08765432109',
                'address' => 'Jl. H. Rasuna Said No. 3',
                'credit_limit' => 10000000,
                'receivable_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('customers')->insertBatch($customers);
        echo "✓ Inserted 3 customers\n";

        // 7. Insert Suppliers
        $suppliers = [
            [
                'code' => 'SUP001',
                'name' => 'PT Teknologi Indonesia',
                'phone' => '021-12345678',
                'debt_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'SUP002',
                'name' => 'CV Elektronik Jaya',
                'phone' => '021-87654321',
                'debt_balance' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('suppliers')->insertBatch($suppliers);
        echo "✓ Inserted 2 suppliers\n";

        // 8. Insert Salespersons
        $salespersons = [
            ['name' => 'Budi Santoso', 'phone' => '08111111111', 'is_active' => 1],
            ['name' => 'Siti Aminah', 'phone' => '08222222222', 'is_active' => 1],
            ['name' => 'Joko Widodo', 'phone' => '08333333333', 'is_active' => 1],
        ];
        
        $this->db->table('salespersons')->insertBatch($salespersons);
        echo "✓ Inserted 3 salespersons\n";

        // 9. Insert System Config
        $configs = [
            ['config_key' => 'company_name', 'config_value' => 'Toko Distributors'],
            ['config_key' => 'company_address', 'config_value' => 'Jl. Raya No. 123'],
            ['config_key' => 'company_phone', 'config_value' => '021-12345678'],
            ['config_key' => 'session_timeout', 'config_value' => '7200'],
        ];
        
        $this->db->table('system_config')->insertBatch($configs);
        echo "✓ Inserted system config\n";

        echo "\n========================================\n";
        echo "✓ SAMPLE DATA SEEDED SUCCESSFULLY!\n";
        echo "========================================\n";
        echo "Database: inventaris_toko\n";
        echo "Users: 4 (owner, admin, gudang, sales)\n";
        echo "Products: 5\n";
        echo "Customers: 3\n";
        echo "Suppliers: 2\n";
        echo "\nDefault Login Credentials:\n";
        echo "Username: owner   | Password: test123\n";
        echo "Username: admin   | Password: test123\n";
        echo "Username: gudang  | Password: test123\n";
        echo "Username: sales   | Password: test123\n";
        echo "========================================\n";
    }
}
