<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToSuppliersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('suppliers', [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
                'comment' => 'Record last update timestamp'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('suppliers', 'updated_at');
    }
}
