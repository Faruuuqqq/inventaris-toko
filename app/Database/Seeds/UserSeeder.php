<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Clear existing users first (handle foreign keys)
        $this->db->disableForeignKeyChecks();
        $this->db->table('users')->emptyTable();
        
        $data = [
            [
                'username' => 'admin',
                'fullname' => 'Admin User',
                'email' => 'admin@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'ADMIN',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'owner',
                'fullname' => 'Owner User',
                'email' => 'owner@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'OWNER',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}