<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteColumns extends Migration
{
    public function up()
    {
        // Adds: deleted_at column with index to: sales, purchase_orders, sales_returns, purchase_returns
        // Add deleted_at to sales table
        if (!$this->db->fieldExists('deleted_at', 'sales')) {
            $this->forge->addColumn('sales', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            // Add index for soft delete queries
            $this->db->query('ALTER TABLE sales ADD INDEX idx_deleted_at (deleted_at)');
        }

        // Add deleted_at to purchase_orders table
        if (!$this->db->fieldExists('deleted_at', 'purchase_orders')) {
            $this->forge->addColumn('purchase_orders', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $this->db->query('ALTER TABLE purchase_orders ADD INDEX idx_deleted_at (deleted_at)');
        }

        // Add deleted_at to sales_returns table
        if (!$this->db->fieldExists('deleted_at', 'sales_returns')) {
            $this->forge->addColumn('sales_returns', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $this->db->query('ALTER TABLE sales_returns ADD INDEX idx_deleted_at (deleted_at)');
        }

        // Add deleted_at to purchase_returns table
        if (!$this->db->fieldExists('deleted_at', 'purchase_returns')) {
            $this->forge->addColumn('purchase_returns', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
            
            $this->db->query('ALTER TABLE purchase_returns ADD INDEX idx_deleted_at (deleted_at)');
        }

        // Add indexes for better query performance
        try {
            if (!$this->indexExists('sales', 'idx_created_at')) {
                $this->db->query('ALTER TABLE sales ADD INDEX idx_created_at (created_at)');
            }
        } catch (\Exception $e) {}
        
        try {
            if (!$this->indexExists('sales', 'idx_customer_id')) {
                $this->db->query('ALTER TABLE sales ADD INDEX idx_customer_id (customer_id)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('purchase_orders', 'idx_po_created_at')) {
                $this->db->query('ALTER TABLE purchase_orders ADD INDEX idx_po_created_at (created_at)');
            }
        } catch (\Exception $e) {}
        
        try {
            if (!$this->indexExists('purchase_orders', 'idx_supplier_id')) {
                $this->db->query('ALTER TABLE purchase_orders ADD INDEX idx_supplier_id (supplier_id)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('sales_returns', 'idx_sr_customer_id')) {
                $this->db->query('ALTER TABLE sales_returns ADD INDEX idx_sr_customer_id (customer_id)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('purchase_returns', 'idx_pr_supplier_id')) {
                $this->db->query('ALTER TABLE purchase_returns ADD INDEX idx_pr_supplier_id (supplier_id)');
            }
        } catch (\Exception $e) {}
    }
    
    /**
     * Helper method to check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $result = $this->db->query("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return $result->getResultArray() !== [];
    }

    public function down()
    {
        // Drop indexes first
        $this->db->query('ALTER TABLE sales DROP INDEX IF EXISTS idx_deleted_at');
        $this->db->query('ALTER TABLE sales DROP INDEX IF EXISTS idx_created_at');
        $this->db->query('ALTER TABLE sales DROP INDEX IF EXISTS idx_customer_id');

        $this->db->query('ALTER TABLE purchase_orders DROP INDEX IF EXISTS idx_deleted_at');
        $this->db->query('ALTER TABLE purchase_orders DROP INDEX IF EXISTS idx_po_created_at');
        $this->db->query('ALTER TABLE purchase_orders DROP INDEX IF EXISTS idx_supplier_id');

        $this->db->query('ALTER TABLE sales_returns DROP INDEX IF EXISTS idx_deleted_at');
        $this->db->query('ALTER TABLE sales_returns DROP INDEX IF EXISTS idx_sr_customer_id');

        $this->db->query('ALTER TABLE purchase_returns DROP INDEX IF EXISTS idx_deleted_at');
        $this->db->query('ALTER TABLE purchase_returns DROP INDEX IF EXISTS idx_pr_supplier_id');

        // Drop columns
        if ($this->db->fieldExists('deleted_at', 'sales')) {
            $this->forge->dropColumn('sales', 'deleted_at');
        }

        if ($this->db->fieldExists('deleted_at', 'purchase_orders')) {
            $this->forge->dropColumn('purchase_orders', 'deleted_at');
        }

        if ($this->db->fieldExists('deleted_at', 'sales_returns')) {
            $this->forge->dropColumn('sales_returns', 'deleted_at');
        }

        if ($this->db->fieldExists('deleted_at', 'purchase_returns')) {
            $this->forge->dropColumn('purchase_returns', 'deleted_at');
        }
    }
}
