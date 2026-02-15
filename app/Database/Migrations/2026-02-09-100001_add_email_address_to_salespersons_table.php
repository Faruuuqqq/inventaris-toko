<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailAddressToSalespersonsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('salespersons', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'phone',
                'comment' => 'Salesperson email for communication'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email',
                'comment' => 'Salesperson address for documentation'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('salespersons', ['email', 'address']);
    }
}
