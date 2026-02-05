<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Transaction Returns Support
 * Purpose: Create sales_returns, sales_return_details, purchase_returns, purchase_return_details tables
 * Date: 2026-02-02
 */
class AddTransactionReturnsSupport extends Migration
{
    public function up()
    {
        // 1. Create sales_returns table
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
                'comment' => 'Original sale ID'
            ],
            'customer_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Customer ID'
            ],
            'date' => [
                'type' => 'DATE',
                'comment' => 'Return date'
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason for return'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'PROCESSED', 'REJECTED'],
                'default' => 'PENDING',
                'comment' => 'Return status'
            ],
            'final_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Total refund amount'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes'
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

        // 2. Create sales_return_details table
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
                'comment' => 'Sales return ID'
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Product ID'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Quantity returned'
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Unit price at time of return'
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Total refund for this item'
            ],
            'warehouse_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Warehouse to receive return'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sales_return_id', 'sales_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sales_return_details');

        // 3. Create purchase_returns table
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
                'comment' => 'Original PO ID'
            ],
            'supplier_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Supplier ID'
            ],
            'date' => [
                'type' => 'DATE',
                'comment' => 'Return date'
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason for return'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'PROCESSED', 'REJECTED'],
                'default' => 'PENDING',
                'comment' => 'Return status'
            ],
            'final_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Total refund amount'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes'
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

        // 4. Create purchase_return_details table
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
                'comment' => 'Purchase return ID'
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Product ID'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Quantity returned'
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Unit price at time of return'
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Total refund for this item'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_return_id', 'purchase_returns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_return_details');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_return_details');
        $this->forge->dropTable('purchase_returns');
        $this->forge->dropTable('sales_return_details');
        $this->forge->dropTable('sales_returns');
    }
}
