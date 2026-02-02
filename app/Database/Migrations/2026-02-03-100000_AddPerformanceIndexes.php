<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Add missing indexes for commonly filtered/sorted columns
        $db = \Config\Database::connect();

        // Stock Mutations - frequently queried by product_id and created_at
        try {
            if (!$this->indexExists('stock_mutations', 'idx_sm_product_id')) {
                $this->db->query('ALTER TABLE stock_mutations ADD INDEX idx_sm_product_id (product_id)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('stock_mutations', 'idx_sm_created_at')) {
                $this->db->query('ALTER TABLE stock_mutations ADD INDEX idx_sm_created_at (created_at)');
            }
        } catch (\Exception $e) {}

        // Payments - frequently queried by type and payment_date
        try {
            if (!$this->indexExists('payments', 'idx_payment_type')) {
                $this->db->query('ALTER TABLE payments ADD INDEX idx_payment_type (type)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('payments', 'idx_payment_date')) {
                $this->db->query('ALTER TABLE payments ADD INDEX idx_payment_date (payment_date)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('payments', 'idx_reference_id')) {
                $this->db->query('ALTER TABLE payments ADD INDEX idx_reference_id (reference_id)');
            }
        } catch (\Exception $e) {}

        // Products - frequently queried by name and sku
        try {
            if (!$this->indexExists('products', 'idx_product_name')) {
                $this->db->query('ALTER TABLE products ADD INDEX idx_product_name (name)');
            }
        } catch (\Exception $e) {}

        // Customers - frequently queried by name and code
        try {
            if (!$this->indexExists('customers', 'idx_customer_name')) {
                $this->db->query('ALTER TABLE customers ADD INDEX idx_customer_name (name)');
            }
        } catch (\Exception $e) {}

        try {
            if (!$this->indexExists('customers', 'idx_customer_phone')) {
                $this->db->query('ALTER TABLE customers ADD INDEX idx_customer_phone (phone)');
            }
        } catch (\Exception $e) {}

        // Suppliers - frequently queried by name
        try {
            if (!$this->indexExists('suppliers', 'idx_supplier_name')) {
                $this->db->query('ALTER TABLE suppliers ADD INDEX idx_supplier_name (name)');
            }
        } catch (\Exception $e) {}

        // Sale Items - frequently queried by product and sale
        try {
            if (!$this->indexExists('sale_items', 'idx_si_product_sale')) {
                $this->db->query('ALTER TABLE sale_items ADD INDEX idx_si_product_sale (sale_id, product_id)');
            }
        } catch (\Exception $e) {}

        // Purchase Order Items - frequently queried by product and PO
        try {
            if (!$this->indexExists('purchase_order_items', 'idx_poi_product_po')) {
                $this->db->query('ALTER TABLE purchase_order_items ADD INDEX idx_poi_product_po (po_id, product_id)');
            }
        } catch (\Exception $e) {}

        // Product Stocks - frequently queried by product and warehouse
        try {
            if (!$this->indexExists('product_stocks', 'idx_ps_product_warehouse')) {
                $this->db->query('ALTER TABLE product_stocks ADD INDEX idx_ps_product_warehouse (product_id, warehouse_id)');
            }
        } catch (\Exception $e) {}
    }

    public function down()
    {
        // Drop all performance indexes
        $indexesToDrop = [
            'stock_mutations' => ['idx_sm_product_id', 'idx_sm_created_at'],
            'payments' => ['idx_payment_type', 'idx_payment_date', 'idx_reference_id'],
            'products' => ['idx_product_name'],
            'customers' => ['idx_customer_name', 'idx_customer_phone'],
            'suppliers' => ['idx_supplier_name'],
            'sale_items' => ['idx_si_product_sale'],
            'purchase_order_items' => ['idx_poi_product_po'],
            'product_stocks' => ['idx_ps_product_warehouse'],
        ];

        foreach ($indexesToDrop as $table => $indexes) {
            foreach ($indexes as $index) {
                try {
                    $this->db->query("ALTER TABLE {$table} DROP INDEX IF EXISTS {$index}");
                } catch (\Exception $e) {}
            }
        }
    }

    /**
     * Helper method to check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $result = $this->db->query("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return $result->getResultArray() !== [];
    }
}
