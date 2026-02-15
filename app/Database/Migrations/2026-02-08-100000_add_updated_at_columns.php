<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtColumns extends Migration
{
    public function up()
    {
        // Add updated_at to payments table
        if (!$this->db->fieldExists('updated_at', 'payments')) {
            $this->forge->addColumn('payments', [
                'updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
            ]);
        }

        // Add updated_at to expenses table
        if (!$this->db->fieldExists('updated_at', 'expenses')) {
            $this->forge->addColumn('expenses', [
                'updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
            ]);
        }
    }

    public function down()
    {
        // Remove updated_at from payments
        if ($this->db->fieldExists('updated_at', 'payments')) {
            $this->forge->dropColumn('payments', 'updated_at');
        }

        // Remove updated_at from expenses
        if ($this->db->fieldExists('updated_at', 'expenses')) {
            $this->forge->dropColumn('expenses', 'updated_at');
        }
    }
}
