<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMissingTables extends Migration
{
    public function up()
    {
        // 1. Fix users table - add password field (keep password_hash for compatibility)
        $this->forge->modifyColumn('users', [
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'password_hash'
            ]
        ]);

        // 2. Fix purchase_orders table - add missing columns
        $this->forge->addColumn('purchase_orders', [
            'nomor_po' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'number',
                'null' => true
            ],
            'id_supplier' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'supplier_id',
                'null' => true
            ],
            'id_warehouse' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'warehouse_id',
                'null' => true
            ],
            'tanggal_po' => [
                'type' => 'DATE',
                'after' => 'date',
                'null' => true
            ],
            'estimasi_tanggal' => [
                'type' => 'DATE',
                'after' => 'tanggal_po',
                'null' => true
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'after' => 'notes',
                'null' => true
            ],
            'total_bayar' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'total_amount',
                'null' => true
            ],
            'id_user' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'notes',
                'null' => true
            ],
        ]);

        // 3. Add indexes to purchase_orders
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_nomor_po (nomor_po)");
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_id_supplier (id_supplier)");
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_id_warehouse (id_warehouse)");

        // 4. Create purchase_order_items table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'po_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'received_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'jumlah_diterima' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
                'after' => 'received_qty'
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'jumlah_diterima'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_order_items');

        // 5. Add column to suppliers table
        $this->forge->addColumn('suppliers', [
            'debt_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'payable_balance',
                'null' => true
            ],
        ]);

        // 6. Add column to customers table
        $this->forge->addColumn('customers', [
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'receivable_balance'
            ],
        ]);

        // 7. Add column to sales table
        $this->forge->addColumn('sales', [
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'number'
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'date'
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'after' => 'notes'
            ],
        ]);

        // 8. Create api_tokens table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'is_revoked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('api_tokens');

        // 9. Create sales_returns table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sale_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'PROCESSED', 'REJECTED'],
                'default' => 'PENDING',
            ],
            'final_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sales_returns');

        // 10. Create sales_return_details table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sales_return_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'warehouse_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sales_return_id', 'sales_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sales_return_details');

        // 11. Create purchase_returns table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'purchase_order_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'supplier_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'PROCESSED', 'REJECTED'],
                'default' => 'PENDING',
            ],
            'final_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_order_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_returns');

        // 12. Create purchase_return_details table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'purchase_return_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_return_id', 'purchase_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_return_details');

        // 13. Create expenses table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'expense_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'expense_date' => [
                'type' => 'DATE',
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['CASH', 'TRANSFER', 'CHECK'],
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('expenses');

        // 14. Add columns to stock_mutations for better tracking
        $this->forge->addColumn('stock_mutations', [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'mutation_type',
                'comment' => 'Alias for mutation_type for compatibility'
            ],
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'reference_id'
            ],
            'current_balance' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'quantity'
            ],
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'current_balance'
            ],
            'tanggal_mutasi' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'notes'
            ],
        ]);

        // 15. Add columns to product_stocks for better tracking
        $this->forge->addColumn('product_stocks', [
            'min_stock_alert' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
                'null' => true,
                'after' => 'quantity'
            ],
        ]);

        // 16. Fix warehouses table - add jenis field
        $this->forge->addColumn('warehouses', [
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'name',
                'comment' => 'Type: Baik, Rusak, Transit'
            ],
        ]);

        // 17. Update warehouses to add status field
        $this->forge->addColumn('warehouses', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'after' => 'jenis'
            ],
        ]);

        // 18. Update products table to add status field
        $this->forge->addColumn('products', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'after' => 'min_stock_alert'
            ],
            'harga_beli_terakhir' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => true,
                'after' => 'price_buy'
            ],
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
                'after' => 'harga_beli_terakhir'
            ],
        ]);

        // 19. Update suppliers table to add status field
        $this->forge->addColumn('suppliers', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'after' => 'payable_balance'
            ],
        ]);

        // 20. Update users table to add last_login
        $this->forge->addColumn('users', [
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ],
        ]);
    }

    public function down()
    {
        // Drop created tables
        $this->forge->dropTable('purchase_return_details');
        $this->forge->dropTable('purchase_returns');
        $this->forge->dropTable('sales_return_details');
        $this->forge->dropTable('sales_returns');
        $this->forge->dropTable('api_tokens');
        $this->forge->dropTable('expenses');
        $this->forge->dropTable('purchase_order_items');

        // Drop added columns
        $this->forge->dropColumn('users', ['password', 'last_login']);
        $this->forge->dropColumn('purchase_orders', ['nomor_po', 'id_supplier', 'id_warehouse', 'tanggal_po', 'estimasi_tanggal', 'keterangan', 'total_bayar', 'id_user']);
        $this->forge->dropColumn('suppliers', ['debt_balance', 'status']);
        $this->forge->dropColumn('customers', ['due_date']);
        $this->forge->dropColumn('sales', ['invoice_number', 'due_date', 'created_by']);
        $this->forge->dropColumn('stock_mutations', ['type', 'reference_number', 'current_balance', 'harga_beli', 'tanggal_mutasi']);
        $this->forge->dropColumn('product_stocks', ['min_stock_alert']);
        $this->forge->dropColumn('warehouses', ['jenis', 'status']);
        $this->forge->dropColumn('products', ['status', 'harga_beli_terakhir', 'stok']);
    }
}
