<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run database seeds.
     * 
     * Main seeder that controls the execution order of other seeders
     * 
     * Usage:
     * 1. php spark db:seed DatabaseSeeder          (Run all seeders)
     * 2. php spark db:seed UserSeeder            (Run specific seeder)
     * 3. php spark db:seed ProductSeeder         (Run specific seeder)
     */
    public function run()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║     🌱 DATABASE SEEDING - INVENTARIS TOKO                ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";

        try {
            // 1. Core Data (Users, Products, Customers, etc.)
            echo "▶️  Step 1: Loading core data for integration testing...\n";
            $this->call('UserSeeder');
            echo "   - Users: ✅\n";
            
            echo "✅ Core data seeding complete!\n\n";

            // Print integration test credentials
            $this->printTestCredentials();

        } catch (\Exception $e) {
            echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    /**
     * Print test credentials for integration testing
     */
    private function printTestCredentials()
    {
        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║                  ✅ INTEGRATION TEST READY!               ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";

        echo "🧪 INTEGRATION TEST CREDENTIALS:\n";
        echo "   Admin:  admin@example.com / password123\n";
        echo "   User:   user@example.com / password123\n";
        echo "   Sales:  sales@example.com / password123\n\n";

        echo "📋 AVAILABLE TEST DATA:\n";
        echo "   - 10 Products with varying stock levels\n";
        echo "   - 3 Customers with credit limits\n";
        echo "   - 3 Suppliers with payment terms\n";
        echo "   - 3 Warehouses with capacity\n\n";

        echo "🔧 TEST COMMANDS:\n";
        echo "   ./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit tests/Feature/PurchaseIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit tests/Feature/InventoryIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit tests/Feature/FinancialIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit tests/Feature/DashboardIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit tests/Feature/AuthIntegrationTest.php\n";
        echo "   ./vendor/bin/phpunit --group integration\n\n";

        echo "🌐 TEST URLs:\n";
        echo "   1. Dashboard: http://localhost:8000/dashboard\n";
        echo "   2. Sales:     http://localhost:8000/transactions/sales\n";
        echo "   3. Products:  http://localhost:8000/master/products\n";
        echo "   4. Reports:    http://localhost:8000/info/reports\n\n";

        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║           🚀 RUN TESTS TO VERIFY FUNCTIONALITY!         ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";
    }
}