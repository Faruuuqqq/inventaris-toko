<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRoleSystemToAdminOwner extends Migration
{
    public function up()
    {
        // Update existing users - change GUDANG and SALES to ADMIN
        $this->db->query("UPDATE users SET role = 'ADMIN' WHERE role IN ('GUDANG', 'SALES')");
        
        // Alter table to remove unused roles - do this first
        try {
            $this->db->query("
                ALTER TABLE users 
                MODIFY role ENUM('OWNER', 'ADMIN') NOT NULL DEFAULT 'ADMIN'
            ");
        } catch (\Exception $e) {
            // If this fails, roles might already be updated
            log_message('info', 'Role column might already be updated: ' . $e->getMessage());
        }
        
        // Check if is_hidden column exists before adding
        $hasIsHidden = false;
        try {
            $result = $this->db->query("SHOW COLUMNS FROM sales LIKE 'is_hidden'")->getResult();
            $hasIsHidden = count($result) > 0;
        } catch (\Exception $e) {
            // Table might not exist or other issue
            $hasIsHidden = false;
        }
        
        if (!$hasIsHidden) {
            // Add hidden flag to transactions for owner feature
            $this->forge->addColumn('sales', [
                'is_hidden' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => 'Hidden from history view (Owner only)'
                ]
            ]);
            
            $this->forge->addColumn('purchase_orders', [
                'is_hidden' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => 'Hidden from history view (Owner only)'
                ]
            ]);
            
            $this->forge->addColumn('sales_returns', [
                'is_hidden' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => 'Hidden from history view (Owner only)'
                ]
            ]);
            
            $this->forge->addColumn('purchase_returns', [
                'is_hidden' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => 'Hidden from history view (Owner only)'
                ]
            ]);
            
            // Add index for hidden transactions
            $this->db->query("ALTER TABLE sales ADD INDEX idx_is_hidden (is_hidden)");
            $this->db->query("ALTER TABLE purchase_orders ADD INDEX idx_is_hidden (is_hidden)");
            $this->db->query("ALTER TABLE sales_returns ADD INDEX idx_is_hidden (is_hidden)");
            $this->db->query("ALTER TABLE purchase_returns ADD INDEX idx_is_hidden (is_hidden)");
        }
    }

    public function down()
    {
        // Add back the old roles
        $this->db->query("
            ALTER TABLE users 
            MODIFY role ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN'
        ");
        
        // Remove hidden columns safely
        try {
            $this->forge->dropColumn('sales', 'is_hidden');
        } catch (\Exception $e) {
            // Column might not exist
        }
        
        try {
            $this->forge->dropColumn('purchase_orders', 'is_hidden');
        } catch (\Exception $e) {
            // Column might not exist
        }
        
        try {
            $this->forge->dropColumn('sales_returns', 'is_hidden');
        } catch (\Exception $e) {
            // Column might not exist
        }
        
        try {
            $this->forge->dropColumn('purchase_returns', 'is_hidden');
        } catch (\Exception $e) {
            // Column might not exist
        }
    }
}