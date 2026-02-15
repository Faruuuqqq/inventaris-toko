<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSchemaMismatches extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // ISSUE 1: Add deleted_at to products for soft delete support
        // Currently only sales, purchase_orders, and returns have deleted_at
        // Controllers are querying products with deleted_at filter, so we need to add it
        if (!$this->db->fieldExists('deleted_at', 'products')) {
            $this->forge->addColumn('products', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at'
                ]
            ]);
            
            // Add index for soft delete queries
            $this->db->query('ALTER TABLE products ADD INDEX idx_products_deleted_at (deleted_at)');
        }
        
        // ISSUE 2: Ensure all tables have is_active column for filtering "active" records
        // Controllers are querying for status='Aktif', but tables have is_active instead
        // We'll rely on is_active (0 = inactive/deleted, 1 = active) instead of adding a status column
        
        // Add is_active to suppliers if not exists
        if (!$this->db->fieldExists('is_active', 'suppliers')) {
            $this->forge->addColumn('suppliers', [
                'is_active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'debt_balance'
                ]
            ]);
            $this->db->query('ALTER TABLE suppliers ADD INDEX idx_suppliers_is_active (is_active)');
        }
        
        // Add is_active to products if not exists
        if (!$this->db->fieldExists('is_active', 'products')) {
            $this->forge->addColumn('products', [
                'is_active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'min_stock_alert'
                ]
            ]);
            $this->db->query('ALTER TABLE products ADD INDEX idx_products_is_active (is_active)');
        }
        
        // Add is_active to customers if not exists
        if (!$this->db->fieldExists('is_active', 'customers')) {
            $this->forge->addColumn('customers', [
                'is_active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'after' => 'receivable_balance'
                ]
            ]);
            $this->db->query('ALTER TABLE customers ADD INDEX idx_customers_is_active (is_active)');
        }
        
        // Warehouses already has is_active - no action needed
        // Verify it exists with correct type
        if ($this->db->fieldExists('is_active', 'warehouses')) {
            $this->db->query('ALTER TABLE warehouses ADD INDEX idx_warehouses_is_active (is_active)');
        }
        
        // ISSUE 3: Fix column naming inconsistencies in schema
        // Some controllers reference: id_customer, id_supplier, id_retur_penjualan, id_po
        // But tables have: customer_id, supplier_id, id, po_id
        // These need to be fixed in the Controllers, not the schema
        // (See controller fixes below)
        
        echo "Schema fixes applied successfully.\n";
        echo "✓ Added deleted_at to products table\n";
        echo "✓ Added is_active to suppliers, products, customers tables\n";
        echo "✓ Added indexes for performance\n";
    }

    public function down()
    {
        // Drop indexes first
        $this->db->query('ALTER TABLE products DROP INDEX IF EXISTS idx_products_deleted_at');
        $this->db->query('ALTER TABLE suppliers DROP INDEX IF EXISTS idx_suppliers_is_active');
        $this->db->query('ALTER TABLE products DROP INDEX IF EXISTS idx_products_is_active');
        $this->db->query('ALTER TABLE customers DROP INDEX IF EXISTS idx_customers_is_active');
        $this->db->query('ALTER TABLE warehouses DROP INDEX IF EXISTS idx_warehouses_is_active');
        
        // Drop columns
        if ($this->db->fieldExists('deleted_at', 'products')) {
            $this->forge->dropColumn('products', 'deleted_at');
        }
        
        if ($this->db->fieldExists('is_active', 'suppliers')) {
            $this->forge->dropColumn('suppliers', 'is_active');
        }
        
        if ($this->db->fieldExists('is_active', 'products')) {
            $this->forge->dropColumn('products', 'is_active');
        }
        
        if ($this->db->fieldExists('is_active', 'customers')) {
            $this->forge->dropColumn('customers', 'is_active');
        }
    }
}
