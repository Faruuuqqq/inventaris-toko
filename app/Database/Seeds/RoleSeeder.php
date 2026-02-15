<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access',
                'permissions' => json_encode([
                    'users.create', 'users.read', 'users.update', 'users.delete',
                    'products.create', 'products.read', 'products.update', 'products.delete',
                    'sales.create', 'sales.read', 'sales.update', 'sales.delete',
                    'purchases.create', 'purchases.read', 'purchases.update', 'purchases.delete',
                    'reports.read', 'settings.update'
                ])
            ],
            [
                'name' => 'user',
                'description' => 'Regular user with limited access',
                'permissions' => json_encode([
                    'products.read', 'sales.create', 'sales.read',
                    'purchases.create', 'purchases.read', 'reports.read'
                ])
            ],
            [
                'name' => 'sales',
                'description' => 'Sales personnel with sales focus',
                'permissions' => json_encode([
                    'products.read', 'customers.read', 'sales.create', 'sales.read', 'sales.update',
                    'purchases.create', 'purchases.read', 'reports.read'
                ])
            ],
            [
                'name' => 'gudang',
                'description' => 'Warehouse staff with inventory focus',
                'permissions' => json_encode([
                    'products.read', 'products.update', 'inventory.read', 'inventory.update',
                    'purchases.read', 'reports.read'
                ])
            ]
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}