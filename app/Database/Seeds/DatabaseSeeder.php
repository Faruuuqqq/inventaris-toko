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

            // 4. Stock Mutations
            echo "▶️  Step 4: Loading stock mutations...\n";
            $this->call('StockMutationsSeeder');
            echo "✅ Step 4 complete!\n\n";

            // 5. Sales Returns
            echo "▶️  Step 5: Loading sales returns...\n";
            $this->call('SalesReturnsSeeder');
            echo "✅ Step 5 complete!\n\n";

            // 6. Purchase Returns
            echo "▶️  Step 6: Loading purchase returns...\n";
            $this->call('PurchaseReturnsSeeder');
            echo "✅ Step 6 complete!\n\n";

            // 7. Payments
            echo "▶️  Step 7: Loading payments...\n";
            $this->call('PaymentsSeeder');
            echo "✅ Step 7 complete!\n\n";

            // 8. Expenses
            echo "▶️  Step 8: Loading expenses...\n";
            $this->call('ExpensesSeeder');
            echo "✅ Step 8 complete!\n\n";

            // 9. Purchase Orders
            echo "▶️  Step 9: Loading purchase orders...\n";
            $this->call('PurchaseOrdersSeeder');
            echo "✅ Step 9 complete!\n\n";

            // 10. Delivery Notes
            echo "▶️  Step 10: Loading delivery notes...\n";
            $this->call('DeliveryNotesSeeder');
            echo "✅ Step 10 complete!\n\n";

            // 11. Audit Logs
            echo "▶️  Step 11: Loading audit logs...\n";
            $this->call('AuditLogsSeeder');
            echo "✅ Step 11 complete!\n\n";

            // 12. Finance Reporting
            echo "▶️  Step 12: Loading financial reporting data...\n";
            $this->call('FinanceReportingSeeder');
            echo "✅ Step 12 complete!\n\n";
            
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
            'product_stocks' => 'Product Stocks',
            'customers' => 'Customers',
            'suppliers' => 'Suppliers',
            'sales' => 'Sales Transactions',
            'sale_items' => 'Sale Items',
            'purchase_orders' => 'Purchase Orders',
            'purchase_order_items' => 'Purchase Order Items',
            'stock_mutations' => 'Stock Mutations',
            'sales_returns' => 'Sales Returns',
            'purchase_returns' => 'Purchase Returns',
            'payments' => 'Payments',
            'expenses' => 'Expenses',
            'delivery_notes' => 'Delivery Notes',
            'delivery_note_items' => 'Delivery Note Items',
            'contra_bons' => 'Contra Bons',
            'audit_logs' => 'Audit Logs',
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
        echo "   Owner:  owner / test123\n";
        echo "   Admin:  admin / test123\n";
        echo "   Sales:  sales / test123\n";
        echo "   Gudang: gudang / test123\n";

        echo "\n📍 NEXT STEPS:\n";
        echo "   1. Login: http://localhost:8000/login\n";
        echo "   2. Explore dashboard dan features\n";
        echo "   3. Test financial reports: /finance/reports\n";
        echo "   4. Check aging reports: /finance/aging\n";
        echo "   5. View delivery tracking: /logistics/delivery-notes\n";
        echo "   6. Audit trail: /admin/audit-logs\n";

        echo "\n";
    }
}
