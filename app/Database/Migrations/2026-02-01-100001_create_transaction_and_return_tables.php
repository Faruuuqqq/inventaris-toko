<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionAndReturnTables extends Migration
{
    public function up()
    {
        // Creates: sales_returns, sales_return_items, purchase_returns, purchase_return_items, expenses, api_tokens, audit_logs
        // 16. Sales Returns table
        if (!$this->db->tableExists('sales_returns')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'no_retur' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
                'tanggal_retur' => ['type' => 'DATE'],
                'sale_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'customer_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'alasan' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Disetujui', 'Ditolak'], 'default' => 'Pending'],
                'total_retur' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('status');
            $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('sales_returns');
        }

        // 17. Sales Return Items table
        if (!$this->db->tableExists('sales_return_items')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'return_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'quantity' => ['type' => 'INT'],
                'price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('return_id', 'sales_returns', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('sales_return_items');
        }

        // 18. Purchase Returns table
        if (!$this->db->tableExists('purchase_returns')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'no_retur' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
                'tanggal_retur' => ['type' => 'DATE'],
                'po_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'supplier_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'alasan' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Disetujui', 'Ditolak'], 'default' => 'Pending'],
                'total_retur' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('status');
            $this->forge->addForeignKey('po_id', 'purchase_orders', 'id_po', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('purchase_returns');
        }

        // 19. Purchase Return Items table
        if (!$this->db->tableExists('purchase_return_items')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'return_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'quantity' => ['type' => 'INT'],
                'price' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('return_id', 'purchase_returns', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('purchase_return_items');
        }

        // 20. Expenses table (Biaya/Jasa)
        if (!$this->db->tableExists('expenses')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'expense_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
                'expense_date' => ['type' => 'DATE'],
                'category' => ['type' => 'VARCHAR', 'constraint' => 100],
                'description' => ['type' => 'TEXT', 'null' => true],
                'amount' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
                'payment_method' => ['type' => 'ENUM', 'constraint' => ['CASH', 'TRANSFER', 'CHEQUE'], 'default' => 'CASH'],
                'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('expense_date');
            $this->forge->addKey('category');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('expenses');
        }

        // 21. Delivery Notes table (Surat Jalan)
        if (!$this->db->tableExists('delivery_notes')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'delivery_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
                'delivery_date' => ['type' => 'DATE'],
                'sale_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'customer_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'recipient_address' => ['type' => 'TEXT', 'null' => true],
                'driver_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'vehicle_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
                'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Dikirim', 'Diterima'], 'default' => 'Pending'],
                'delivered_at' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('status');
            $this->forge->addKey('delivery_date');
            $this->forge->addForeignKey('sale_id', 'sales', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('delivery_notes');
        }

        // 22. Delivery Note Items table
        if (!$this->db->tableExists('delivery_note_items')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'delivery_note_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'product_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'quantity' => ['type' => 'INT'],
                'unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('delivery_note_id', 'delivery_notes', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('delivery_note_items');
        }

        // 23. Audit Logs table
        if (!$this->db->tableExists('audit_logs')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'action' => ['type' => 'VARCHAR', 'constraint' => 50],
                'table_name' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'record_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                'old_values' => ['type' => 'TEXT', 'null' => true],
                'new_values' => ['type' => 'TEXT', 'null' => true],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
                'user_agent' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('action');
            $this->forge->addKey('table_name');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('audit_logs');
        }

        // 24. System Config table
        if (!$this->db->tableExists('system_config')) {
            $this->forge->addField([
                'id_config' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'config_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
                'config_value' => ['type' => 'TEXT', 'null' => true],
            ]);
            $this->forge->addKey('id_config', true);
            $this->forge->createTable('system_config');
        }
    }

    public function down()
    {
        // Drop all tables in reverse order
        $this->forge->dropTable('system_config', true);
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('delivery_note_items', true);
        $this->forge->dropTable('delivery_notes', true);
        $this->forge->dropTable('expenses', true);
        $this->forge->dropTable('purchase_return_items', true);
        $this->forge->dropTable('purchase_returns', true);
        $this->forge->dropTable('sales_return_items', true);
        $this->forge->dropTable('sales_returns', true);
    }
}
