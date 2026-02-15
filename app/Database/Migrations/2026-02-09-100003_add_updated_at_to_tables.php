<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToTables extends Migration
{
    public function up()
    {
        // Add updated_at to customers table
        if ($this->db->fieldExists('updated_at', 'customers') === false) {
            $this->forge->addColumn('customers', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Add updated_at to products table
        if ($this->db->fieldExists('updated_at', 'products') === false) {
            $this->forge->addColumn('products', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Add updated_at to contra_bons table (named as kontra_bons in database)
        if ($this->db->fieldExists('updated_at', 'kontra_bons') === false) {
            $this->forge->addColumn('kontra_bons', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Add updated_at to stock_mutations table
        if ($this->db->fieldExists('updated_at', 'stock_mutations') === false) {
            $this->forge->addColumn('stock_mutations', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Add updated_at to users table
        if ($this->db->fieldExists('updated_at', 'users') === false) {
            $this->forge->addColumn('users', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // Add updated_at to sales table
        if ($this->db->fieldExists('updated_at', 'sales') === false) {
            $this->forge->addColumn('sales', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove updated_at from all tables
        if ($this->db->fieldExists('updated_at', 'customers')) {
            $this->forge->dropColumn('customers', 'updated_at');
        }

        if ($this->db->fieldExists('updated_at', 'products')) {
            $this->forge->dropColumn('products', 'updated_at');
        }

        if ($this->db->fieldExists('updated_at', 'kontra_bons')) {
            $this->forge->dropColumn('kontra_bons', 'updated_at');
        }

        if ($this->db->fieldExists('updated_at', 'stock_mutations')) {
            $this->forge->dropColumn('stock_mutations', 'updated_at');
        }

        if ($this->db->fieldExists('updated_at', 'users')) {
            $this->forge->dropColumn('users', 'updated_at');
        }

        if ($this->db->fieldExists('updated_at', 'sales')) {
            $this->forge->dropColumn('sales', 'updated_at');
        }
    }
}
