<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get the admin user ID
        $db = \Config\Database::connect();
        $admin = $db->table('users')->where('role', 'ADMIN')->get()->getRow();
        
        if (!$admin) {
            echo "No admin user found, skipping notification seeding\n";
            return;
        }
        
        // Create sample notifications
        $notifications = [
            [
                'user_id' => null, // System notification
                'type' => 'low_stock',
                'title' => 'Stok Menipis',
                'message' => 'Produk Laptop ASUS ROG stok tersisa 2 unit (min: 5)',
                'reference_id' => 1,
                'reference_type' => 'product',
                'link' => base_url('master/products'),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'user_id' => null,
                'type' => 'overdue_receivable',
                'title' => 'Piutang Jatuh Tempo',
                'message' => 'Invoice INV-001 - PT. Maju Jaya jatuh tempo 15/02/2026 (Rp 5.000.000)',
                'reference_id' => 1,
                'reference_type' => 'sale',
                'link' => base_url('finance/payments/receivable'),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
            ],
            [
                'user_id' => null,
                'type' => 'pending_po',
                'title' => 'PO Menunggu Penerimaan',
                'message' => 'PO-001 - Supplier PT Tech Supply sudah 3 hari menunggu',
                'reference_id' => 1,
                'reference_type' => 'purchase_order',
                'link' => base_url('transactions/purchases'),
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'user_id' => null,
                'type' => 'info',
                'title' => 'Sistem Diperbarui',
                'message' => 'Sistem telah berhasil diperbarui ke versi 1.0.0',
                'reference_id' => null,
                'reference_type' => null,
                'link' => null,
                'is_read' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ]
        ];
        
        $this->db->table('notifications')->insertBatch($notifications);
        echo "Notifications seeded: " . count($notifications) . " records\n";
        
        // Create notification settings for the admin user
        $settings = [
            'user_id' => $admin->id,
            'low_stock' => 1,
            'overdue_receivable' => 1,
            'overdue_payable' => 1,
            'pending_po' => 1,
            'daily_report' => 0,
            'email_notifications' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('notification_settings')->insert($settings);
        echo "Notification settings created for admin user\n";
    }
}