<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreMasterTables extends Migration
{
    public function up()
    {
        // Creates: users, warehouses, categories, products, product_stocks, customers, suppliers, salespersons, contra_bons, sales, sale_items, purchase_orders, purchase_order_items, stock_mutations, payments
        // 1. Users table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'fullname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'role' => ['type' => 'ENUM', 'constraint' => ['OWNER', 'ADMIN', 'GUDANG', 'SALES'], 'default' => 'ADMIN'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('username');
        $this->forge->addKey('role');
        $this->forge->createTable('users', true);

        // 2. Warehouses table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 10, 'unique' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'address' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('warehouses', true);

        // 3. Categories table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories', true);

        // 4. Products table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'sku' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'category_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pcs'],
            'price_buy' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'price_sell' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'min_stock_alert' => ['type' => 'INT', 'default' => 10],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sku');
        $this->forge->addKey('category_id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('products', true);

        // 5. Product Stocks table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity' => ['type' => 'INT', 'default' => 0],
            'min_stock_alert' => ['type' => 'INT', 'default' => 10],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'warehouse_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_stocks', true);

        // 6. Customers table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'credit_limit' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'receivable_balance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code');
        $this->forge->addKey('name');
        $this->forge->createTable('customers', true);

        // 7. Suppliers table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'debt_balance' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code');
        $this->forge->addKey('name');
        $this->forge->createTable('suppliers', true);

        // 8. Salespersons table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('salespersons', true);

        // 9. Contra Bons table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'document_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'customer_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at' => ['type' => 'DATE'],
            'due_date' => ['type' => 'DATE'],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'status' => ['type' => 'ENUM', 'constraint' => ['UNPAID', 'PARTIAL', 'PAID'], 'default' => 'UNPAID'],
            'notes' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('customer_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contra_bons', true);

        // 10. Sales table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'customer_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'salesperson_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'warehouse_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'payment_type' => ['type' => 'ENUM', 'constraint' => ['CASH', 'CREDIT']],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'payment_status' => ['type' => 'ENUM', 'constraint' => ['PAID', 'UNPAID', 'PARTIAL'], 'default' => 'PAID'],
            'is_hidden' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'contra_bon_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('invoice_number');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('payment_status');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('salesperson_id', 'salespersons', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('contra_bon_id', 'contra_bons', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('sales', true);

        // 11. Sale Items table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'sale_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity' => ['type' => 'INT'],
            'price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sale_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_items', true);

        // 12. Purchase Orders table
        $this->forge->addField([
            'id_po' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'nomor_po' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'tanggal_po' => ['type' => 'DATE'],
            'supplier_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['Dipesan', 'Sebagian', 'Diterima Semua', 'Dibatalkan'], 'default' => 'Dipesan'],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'received_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'notes' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id_po', true);
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_orders', true);

        // 13. Purchase Order Items table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'po_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity' => ['type' => 'INT'],
            'price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'received_qty' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('po_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id_po', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_order_items', true);

        // 14. Stock Mutations table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'warehouse_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'type' => ['type' => 'ENUM', 'constraint' => ['IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER']],
            'quantity' => ['type' => 'INT'],
            'current_balance' => ['type' => 'INT'],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addKey('warehouse_id');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_mutations', true);

        // 15. Payments table
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'payment_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'payment_date' => ['type' => 'DATE'],
            'type' => ['type' => 'ENUM', 'constraint' => ['RECEIVABLE', 'PAYABLE']],
            'reference_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'method' => ['type' => 'ENUM', 'constraint' => ['CASH', 'TRANSFER', 'CHEQUE'], 'default' => 'CASH'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('type');
        $this->forge->addKey('payment_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments', true);

        // Continue in next part...
        // 16-24 tables: returns, expenses, delivery_notes, audit_logs, system_config
    }

    public function down()
    {
        // Drop all tables in reverse order
        $this->forge->dropTable('payments', true);
        $this->forge->dropTable('stock_mutations', true);
        $this->forge->dropTable('purchase_order_items', true);
        $this->forge->dropTable('purchase_orders', true);
        $this->forge->dropTable('sale_items', true);
        $this->forge->dropTable('sales', true);
        $this->forge->dropTable('contra_bons', true);
        $this->forge->dropTable('salespersons', true);
        $this->forge->dropTable('suppliers', true);
        $this->forge->dropTable('customers', true);
        $this->forge->dropTable('product_stocks', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('warehouses', true);
        $this->forge->dropTable('users', true);
    }
}
