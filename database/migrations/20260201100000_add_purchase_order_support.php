<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Purchase Order Support
 * Purpose: Add PO-related columns to purchase_orders, create purchase_order_items table
 * Date: 2026-02-01
 */
class AddPurchaseOrderSupport extends Migration
{
    public function up()
    {
        // 1. Add columns to purchase_orders table
        $this->forge->addColumn('purchase_orders', [
            'nomor_po' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'number',
                'null' => true,
                'comment' => 'PO Number'
            ],
            'id_supplier' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'supplier_id',
                'null' => true,
                'comment' => 'Supplier ID (alias)'
            ],
            'id_warehouse' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'warehouse_id',
                'null' => true,
                'comment' => 'Warehouse ID (alias)'
            ],
            'tanggal_po' => [
                'type' => 'DATE',
                'after' => 'date',
                'null' => true,
                'comment' => 'PO Date'
            ],
            'estimasi_tanggal' => [
                'type' => 'DATE',
                'after' => 'tanggal_po',
                'null' => true,
                'comment' => 'Estimated delivery date'
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'after' => 'notes',
                'null' => true,
                'comment' => 'Description/Notes'
            ],
            'total_bayar' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'total_amount',
                'null' => true,
                'comment' => 'Total to pay'
            ],
            'id_user' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'after' => 'notes',
                'null' => true,
                'comment' => 'User who created PO'
            ],
        ]);

        // 2. Add indexes to purchase_orders
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_nomor_po (nomor_po)");
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_id_supplier (id_supplier)");
        $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_id_warehouse (id_warehouse)");

        // 3. Create purchase_order_items table
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
                'comment' => 'Purchase Order ID'
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'Product ID'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Quantity ordered'
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Unit price'
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Subtotal (qty * price)'
            ],
            'received_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Quantity received'
            ],
            'jumlah_diterima' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
                'comment' => 'Quantity received (duplicate for compatibility)'
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Description'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_order_items');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order_items');
        $this->forge->dropColumn('purchase_orders', ['nomor_po', 'id_supplier', 'id_warehouse', 'tanggal_po', 'estimasi_tanggal', 'keterangan', 'total_bayar', 'id_user']);
    }
}
