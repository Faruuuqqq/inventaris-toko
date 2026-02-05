<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Inventory & Product Enhancements
 * Purpose: Add columns for better inventory tracking and product management
 * Date: 2026-02-03
 */
class AddInventoryAndProductEnhancements extends Migration
{
    public function up()
    {
        // 1. Enhance stock_mutations tracking
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
                'after' => 'reference_id',
                'comment' => 'Reference document number (Invoice/SJ)'
            ],
            'current_balance' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'quantity',
                'comment' => 'Stock balance after mutation'
            ],
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'current_balance',
                'comment' => 'Purchase price'
            ],
            'tanggal_mutasi' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'notes',
                'comment' => 'Mutation date'
            ],
        ]);

        // 2. Enhance product_stocks tracking
        $this->forge->addColumn('product_stocks', [
            'min_stock_alert' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
                'null' => true,
                'after' => 'quantity',
                'comment' => 'Minimum stock alert level'
            ],
        ]);

        // 3. Enhance warehouses (multi-type warehouse support)
        $this->forge->addColumn('warehouses', [
            'jenis' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'name',
                'comment' => 'Warehouse type (Baik/Rusak/Transit)'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'after' => 'jenis',
                'comment' => 'Warehouse status (Aktif/Nonaktif)'
            ],
        ]);

        // 4. Enhance products with better tracking
        $this->forge->addColumn('products', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'after' => 'min_stock_alert',
                'comment' => 'Product status (Aktif/Nonaktif)'
            ],
            'harga_beli_terakhir' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => true,
                'after' => 'price_buy',
                'comment' => 'Last purchase price'
            ],
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => true,
                'after' => 'harga_beli_terakhir',
                'comment' => 'Current stock aggregate'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_mutations', ['type', 'reference_number', 'current_balance', 'harga_beli', 'tanggal_mutasi']);
        $this->forge->dropColumn('product_stocks', ['min_stock_alert']);
        $this->forge->dropColumn('warehouses', ['jenis', 'status']);
        $this->forge->dropColumn('products', ['status', 'harga_beli_terakhir', 'stok']);
    }
}
