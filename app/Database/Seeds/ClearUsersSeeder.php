<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClearUsersSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->emptyTable();
        echo "Users table cleared\n";
    }
}