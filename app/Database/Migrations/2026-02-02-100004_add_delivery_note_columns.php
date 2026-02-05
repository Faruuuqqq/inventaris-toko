<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeliveryNoteColumns extends Migration
{
    public function up()
    {
        // Add delivery note columns to sales table
        $fields = [
            'delivery_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Nomor Surat Jalan (SJ-YYYYMMDD-XXXX format)',
            ],
            'delivery_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Tanggal pengiriman barang',
            ],
            'delivery_address' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Alamat tujuan pengiriman',
            ],
            'delivery_notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Catatan pengiriman',
            ],
            'delivery_driver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID supir/pengantar dari tabel salespersons',
            ],
        ];

        $this->forge->addColumn('sales', $fields);

        // Add index for delivery_number for faster lookup
        $this->forge->addKey('delivery_number', false, false, 'idx_sales_delivery_number');
        
        // Add foreign key for driver
        $this->forge->addForeignKey(
            'delivery_driver_id',
            'salespersons',
            'id',
            'SET NULL',
            'CASCADE',
            'fk_sales_delivery_driver'
        );
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->DBDriver !== 'SQLite3') {
            $this->forge->dropForeignKey('sales', 'fk_sales_delivery_driver');
        }

        // Drop index
        $this->forge->dropKey('sales', 'idx_sales_delivery_number');

        // Drop columns
        $this->forge->dropColumn('sales', [
            'delivery_number',
            'delivery_date',
            'delivery_address',
            'delivery_notes',
            'delivery_driver_id',
        ]);
    }
}
