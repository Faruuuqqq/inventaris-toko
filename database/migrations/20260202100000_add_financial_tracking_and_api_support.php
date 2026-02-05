<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Financial Tracking & API Support
 * Purpose: Create expenses table, api_tokens table, add audit columns
 * Date: 2026-02-02
 */
class AddFinancialTrackingAndApiSupport extends Migration
{
    public function up()
    {
        // 1. Create api_tokens table for API authentication
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'comment' => 'User ID'
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'API token'
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'comment' => 'Token expiration time'
            ],
            'is_revoked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Is token revoked'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('api_tokens');

        // 2. Create expenses table for financial tracking
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'expense_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'Expense document number'
            ],
            'expense_date' => [
                'type' => 'DATE',
                'comment' => 'Expense date'
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Expense category'
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Expense description'
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Expense amount'
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['CASH', 'TRANSFER', 'CHECK'],
                'comment' => 'Payment method'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes'
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'comment' => 'User who recorded expense'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('expenses');

        // 3. Add user authentication tracking
        $this->forge->addColumn('users', [
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'password_hash',
                'comment' => 'Password (additional field for compatibility)'
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
                'comment' => 'Last login timestamp'
            ],
        ]);

        // 4. Add financial tracking to suppliers
        $this->forge->addColumn('suppliers', [
            'debt_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'payable_balance',
                'null' => true,
                'comment' => 'Current debt balance'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Aktif',
                'null' => true,
                'comment' => 'Supplier status'
            ],
        ]);

        // 5. Add financial tracking to customers
        $this->forge->addColumn('customers', [
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'receivable_balance',
                'comment' => 'Payment due date'
            ],
        ]);

        // 6. Add invoice tracking to sales
        $this->forge->addColumn('sales', [
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'number',
                'comment' => 'Invoice number'
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'date',
                'comment' => 'Payment due date'
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'after' => 'notes',
                'comment' => 'User who created sale'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens');
        $this->forge->dropTable('expenses');
        $this->forge->dropColumn('users', ['password', 'last_login']);
        $this->forge->dropColumn('suppliers', ['debt_balance', 'status']);
        $this->forge->dropColumn('customers', ['due_date']);
        $this->forge->dropColumn('sales', ['invoice_number', 'due_date', 'created_by']);
    }
}
