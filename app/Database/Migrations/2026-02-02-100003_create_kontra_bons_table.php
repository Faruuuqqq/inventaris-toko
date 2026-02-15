<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKontraBonsTable extends Migration
{
    public function up()
    {
        // Skip if table already created in core migration
        if ($this->db->tableExists('kontra_bons')) {
            return;
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'document_number' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
            ],
            'customer_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'PAID', 'CANCELLED'],
                'default' => 'PENDING',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('kontra_bons');
    }

    public function down()
    {
        $this->forge->dropTable('kontra_bons');
    }
}
