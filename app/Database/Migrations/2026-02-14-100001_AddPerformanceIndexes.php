<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Sales table indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sales_customer ON sales(customer_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sales_status ON sales(payment_status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sales_date ON sales(created_at)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sales_customer_status ON sales(customer_id, payment_status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sales_invoice ON sales(invoice_number)");
        
        // Sale Items indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sale_items_sale ON sale_items(sale_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_sale_items_product ON sale_items(product_id)");
        
        // Purchase Orders indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_supplier ON purchase_orders(supplier_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_status ON purchase_orders(status)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_date ON purchase_orders(tanggal_po)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_payment_status ON purchase_orders(payment_status)");
        
        // Purchase Order Items indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_items_po ON purchase_order_items(po_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_po_items_product ON purchase_order_items(product_id)");
        
        // Products indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_products_sku ON products(sku)");
        
        // Product Stocks indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_stocks_product ON product_stocks(product_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_stocks_warehouse ON product_stocks(warehouse_id)");
        
        // Stock Mutations indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_mutations_product ON stock_mutations(product_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_mutations_type ON stock_mutations(type)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_mutations_date ON stock_mutations(created_at)");
        
        // Payments indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_payments_type ON payments(type)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_payments_date ON payments(payment_date)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_payments_user ON payments(user_id)");
        
        // Expenses indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_expenses_date ON expenses(expense_date)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_expenses_category ON expenses(category)");
        
        // Delivery Notes indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_delivery_notes_sale ON delivery_notes(sale_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_delivery_notes_status ON delivery_notes(status)");
        
        // Audit Logs indexes
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_logs(user_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_logs(action)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_audit_date ON audit_logs(created_at)");
        
        // Notifications indexes (new table)
        // These will be created by CreateNotificationsTable migration
    }

    public function down()
    {
        // Drop indexes (optional - usually not needed)
        $tables = [
            'sales' => ['idx_sales_customer', 'idx_sales_status', 'idx_sales_date', 'idx_sales_customer_status', 'idx_sales_invoice'],
            'sale_items' => ['idx_sale_items_sale', 'idx_sale_items_product'],
            'purchase_orders' => ['idx_po_supplier', 'idx_po_status', 'idx_po_date', 'idx_po_payment_status'],
            'purchase_order_items' => ['idx_po_items_po', 'idx_po_items_product'],
            'products' => ['idx_products_category', 'idx_products_sku'],
            'product_stocks' => ['idx_stocks_product', 'idx_stocks_warehouse'],
            'stock_mutations' => ['idx_mutations_product', 'idx_mutations_type', 'idx_mutations_date'],
            'payments' => ['idx_payments_type', 'idx_payments_date', 'idx_payments_user'],
            'expenses' => ['idx_expenses_date', 'idx_expenses_category'],
            'delivery_notes' => ['idx_delivery_notes_sale', 'idx_delivery_notes_status'],
            'audit_logs' => ['idx_audit_user', 'idx_audit_action', 'idx_audit_date']
        ];
        
        foreach ($tables as $table => $indexes) {
            foreach ($indexes as $index) {
                try {
                    $this->db->query("DROP INDEX IF EXISTS {$index} ON {$table}");
                } catch (\Exception $e) {
                    // Index might not exist, ignore error
                }
            }
        }
    }
}
