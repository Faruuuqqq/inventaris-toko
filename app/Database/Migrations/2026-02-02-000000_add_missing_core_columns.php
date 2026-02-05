<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingCoreColumns extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add columns to products table if they don't exist
        $productFields = $db->getFieldNames('products');
        
        // Add min_stock (different from min_stock_alert)
        if (!in_array('min_stock', $productFields)) {
            $this->forge->addColumn('products', [
                'min_stock' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 10,
                    'null' => false,
                    'after' => 'min_stock_alert'
                ]
            ]);
        }
        
        // Add max_stock
        if (!in_array('max_stock', $productFields)) {
            $this->forge->addColumn('products', [
                'max_stock' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 100,
                    'null' => false,
                    'after' => 'min_stock'
                ]
            ]);
        }
        
        // Add price column (alias for price_sell for backwards compatibility)
        if (!in_array('price', $productFields)) {
            $this->forge->addColumn('products', [
                'price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'price_sell',
                    'comment' => 'Alias for price_sell'
                ]
            ]);
        }
        
        // Add cost_price (alias for price_buy)
        if (!in_array('cost_price', $productFields)) {
            $this->forge->addColumn('products', [
                'cost_price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '15,2',
                    'default' => 0,
                    'null' => false,
                    'after' => 'price_buy',
                    'comment' => 'Alias for price_buy'
                ]
            ]);
        }
        
        // Check if sales table exists
        if ($db->tableExists('sales')) {
            $salesFields = $db->getFieldNames('sales');
            
            // Add total_profit to sales table
            if (!in_array('total_profit', $salesFields)) {
                $this->forge->addColumn('sales', [
                    'total_profit' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0,
                        'null' => false,
                        'after' => 'total_amount'
                    ]
                ]);
            }
        }
        
        // Add deleted_at to categories table for soft delete support
        if ($db->tableExists('categories')) {
            $categoryFields = $db->getFieldNames('categories');
            if (!in_array('deleted_at', $categoryFields)) {
                $this->forge->addColumn('categories', [
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true
                    ]
                ]);
            }
        }
    }

    public function down()
    {
        // Remove added columns
        if ($this->db->fieldExists('min_stock', 'products')) {
            $this->forge->dropColumn('products', 'min_stock');
        }
        if ($this->db->fieldExists('max_stock', 'products')) {
            $this->forge->dropColumn('products', 'max_stock');
        }
        if ($this->db->fieldExists('price', 'products')) {
            $this->forge->dropColumn('products', 'price');
        }
        if ($this->db->fieldExists('cost_price', 'products')) {
            $this->forge->dropColumn('products', 'cost_price');
        }
        if ($this->db->fieldExists('total_profit', 'sales')) {
            $this->forge->dropColumn('sales', 'total_profit');
        }
        if ($this->db->fieldExists('deleted_at', 'categories')) {
            $this->forge->dropColumn('categories', 'deleted_at');
        }
    }
}
