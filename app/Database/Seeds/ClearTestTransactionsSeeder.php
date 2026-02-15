<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClearTestTransactionsSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("DELETE FROM sales WHERE invoice_number LIKE 'INV-" . date('Ymd') . "%'");
        $this->db->query("DELETE FROM purchase_orders WHERE nomor_po LIKE 'PO-" . date('Ymd') . "%'");
        
        echo "Cleared test transactions\n";
    }
}