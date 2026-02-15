<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        // Notifications table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true // NULL means system notification for all users
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'message' => [
                'type' => 'TEXT'
            ],
            'reference_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('type');
        $this->forge->addKey('is_read');
        $this->forge->addKey('created_at');
        $this->forge->createTable('notifications');

        // Notification settings table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true
            ],
            'low_stock' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'overdue_receivable' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'overdue_payable' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'pending_po' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'daily_report' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'email_notifications' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('notification_settings');
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('notification_settings', true);
    }
}
