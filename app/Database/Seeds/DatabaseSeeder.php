<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder utama yang mengatur urutan eksekusi seeder lainnya
     * 
     * Cara menggunakan:
     * 1. php spark db:seed DatabaseSeeder          (Run semua seeder)
     * 2. php spark db:seed InitialDataSeeder       (Run spesifik seeder)
     * 3. php spark db:seed Phase4TestDataSeeder    (Run test data)
     * 4. php spark db:seed SalesDataSeeder         (Run sales data)
     */
    public function run()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║     🌱 DATABASE SEEDING - INVENTARIS TOKO                ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n\n";

        try {
            // 1. Initial Data (Users, Categories, Warehouses, etc)
            echo "▶️  Step 1: Loading initial data...\n";
            $this->call('InitialDataSeeder');
            echo "✅ Step 1 complete!\n\n";

            // 2. Phase 4 Test Data (Products, Customers, Suppliers)
            echo "▶️  Step 2: Loading test data...\n";
            $this->call('Phase4TestDataSeeder');
            echo "✅ Step 2 complete!\n\n";

            // 3. Sales Data (Transactions)
            echo "▶️  Step 3: Loading sales data...\n";
            $this->call('SalesDataSeeder');
            echo "✅ Step 3 complete!\n\n";

            // Print Summary
            $this->printSummary();

        } catch (\Exception $e) {
            echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    /**
     * Print summary of seeded data
     */
    private function printSummary()
    {
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║                  ✅ SEEDING COMPLETE!                    ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n\n";

        $tables = [
            'users' => 'Users',
            'categories' => 'Categories',
            'warehouses' => 'Warehouses',
            'salespersons' => 'Salespersons',
            'products' => 'Products',
            'customers' => 'Customers',
            'suppliers' => 'Suppliers',
            'sales_transactions' => 'Sales (Tunai)',
            'credit_transactions' => 'Sales (Kredit)',
            'purchases' => 'Purchases',
            'sale_returns' => 'Sale Returns',
            'purchase_returns' => 'Purchase Returns',
        ];

        echo "📊 DATA SUMMARY:\n";
        foreach ($tables as $table => $name) {
            try {
                $count = $this->db->table($table)->countAll();
                printf("   %-25s: %4d records\n", $name, $count);
            } catch (\Exception $e) {
                echo "   ⚠️  $name - Not seeded\n";
            }
        }

        echo "\n🔑 DEFAULT CREDENTIALS:\n";
        echo "   Owner:  owner / password\n";
        echo "   Admin:  admin / password\n";
        echo "   Sales:  sales / password\n";
        echo "   Gudang: gudang / password\n";

        echo "\n📍 NEXT STEPS:\n";
        echo "   1. Login: http://localhost/inventaris-toko/public/\n";
        echo "   2. Explore dashboard dan features\n";
        echo "   3. Baca dokumentasi: docs/INDEX.md\n";
        echo "   4. API testing: docs/api/Inventaris_Toko_API.postman_collection.json\n";

        echo "\n";
    }
}
