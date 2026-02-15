<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressToSuppliersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('suppliers', [
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'phone',
                'comment' => 'Supplier address for delivery/pickup and documentation'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('suppliers', 'address');
    }
}
