<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentColumnsToPurchaseOrders extends Migration
{
    public function up()
    {
        $fields = [
            'paid_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'total_amount'
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['UNPAID', 'PARTIAL', 'PAID'],
                'default'    => 'UNPAID',
                'after'      => 'paid_amount'
            ]
        ];

        $this->forge->addColumn('purchase_orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_orders', ['paid_amount', 'payment_status']);
    }
}
