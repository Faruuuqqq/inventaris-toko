<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuditLogsSeeder extends Seeder
{
    public function run()
    {
        echo "\n📝 Creating Audit Logs...\n";
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get users
            $users = $db->table('users')->select('id, username, fullname')->get()->getResultArray();
            
            if (empty($users)) {
                echo "   ⚠️  No users found. Run InitialDataSeeder first.\n";
                return;
            }
            
            // Get record IDs from various tables
            $salesIds = $db->table('sales')->select('id, invoice_number')->limit(20)->get()->getResultArray();
            $productIds = $db->table('products')->select('id, name, sku')->limit(15)->get()->getResultArray();
            $customerIds = $db->table('customers')->select('id, name')->limit(15)->get()->getResultArray();
            $supplierIds = $db->table('suppliers')->select('id, name')->limit(10)->get()->getResultArray();
            $poIds = $db->table('purchase_orders')->select('id_po, nomor_po')->limit(10)->get()->getResultArray();
            
            echo "   Creating audit logs for various actions...\n\n";
            
            $logCount = 0;
            
            // Action templates
            $actions = [
                'CREATE' => ['users', 'sales', 'purchase_orders', 'customers', 'suppliers', 'products'],
                'UPDATE' => ['users', 'sales', 'customers', 'products'],
                'DELETE' => ['sales', 'products'],
                'LOGIN' => ['users'],
                'LOGOUT' => ['users'],
                'VIEW' => ['sales', 'products', 'customers']
            ];
            
            // Generate logs for the past 90 days
            $startDate = strtotime('-90 days');
            $endDate = time();
            
            // CREATE logs (25% of logs)
            $createCount = 0;
            for ($i = 0; $i < 30; $i++) {
                $logDate = date('Y-m-d H:i:s', rand($startDate, $endDate));
                $user = $users[array_rand($users)];
                
                // Random table
                $tables = ['sales', 'purchase_orders', 'customers', 'suppliers', 'products', 'expenses'];
                $table = $tables[array_rand($tables)];
                $recordId = $this->getRecordId($table, $db, $salesIds, $productIds, $customerIds, $supplierIds, $poIds);
                
                if ($recordId) {
                    $newValues = $this->generateNewValues($table);
                    
                    $db->table('audit_logs')->insert([
                        'user_id' => $user['id'],
                        'action' => 'CREATE',
                        'table_name' => $table,
                        'record_id' => $recordId,
                        'old_values' => null,
                        'new_values' => json_encode($newValues),
                        'ip_address' => $this->generateIP(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => $logDate
                    ]);
                    
                    $logCount++;
                    $createCount++;
                }
            }
            
            // UPDATE logs (35% of logs)
            $updateCount = 0;
            for ($i = 0; $i < 40; $i++) {
                $logDate = date('Y-m-d H:i:s', rand($startDate, $endDate));
                $user = $users[array_rand($users)];
                
                $tables = ['sales', 'customers', 'products', 'users'];
                $table = $tables[array_rand($tables)];
                $recordId = $this->getRecordId($table, $db, $salesIds, $productIds, $customerIds, $supplierIds, $poIds);
                
                if ($recordId) {
                    $oldValues = $this->generateNewValues($table);
                    $newValues = $this->generateNewValues($table);
                    
                    $db->table('audit_logs')->insert([
                        'user_id' => $user['id'],
                        'action' => 'UPDATE',
                        'table_name' => $table,
                        'record_id' => $recordId,
                        'old_values' => json_encode($oldValues),
                        'new_values' => json_encode($newValues),
                        'ip_address' => $this->generateIP(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => $logDate
                    ]);
                    
                    $logCount++;
                    $updateCount++;
                }
            }
            
            // LOGIN/LOGOUT logs (25% of logs)
            $authCount = 0;
            for ($i = 0; $i < 25; $i++) {
                $logDate = date('Y-m-d H:i:s', rand($startDate, $endDate));
                $user = $users[array_rand($users)];
                $action = rand(0, 1) ? 'LOGIN' : 'LOGOUT';
                
                $db->table('audit_logs')->insert([
                    'user_id' => $user['id'],
                    'action' => $action,
                    'table_name' => 'users',
                    'record_id' => $user['id'],
                    'old_values' => null,
                    'new_values' => json_encode(['username' => $user['username'], 'fullname' => $user['fullname']]),
                    'ip_address' => $this->generateIP(),
                    'user_agent' => $this->generateUserAgent(),
                    'created_at' => $logDate
                ]);
                
                $logCount++;
                $authCount++;
            }
            
            // VIEW logs (15% of logs)
            $viewCount = 0;
            for ($i = 0; $i < 15; $i++) {
                $logDate = date('Y-m-d H:i:s', rand($startDate, $endDate));
                $user = $users[array_rand($users)];
                
                $tables = ['sales', 'products', 'customers'];
                $table = $tables[array_rand($tables)];
                $recordId = $this->getRecordId($table, $db, $salesIds, $productIds, $customerIds, $supplierIds, $poIds);
                
                if ($recordId) {
                    $db->table('audit_logs')->insert([
                        'user_id' => $user['id'],
                        'action' => 'VIEW',
                        'table_name' => $table,
                        'record_id' => $recordId,
                        'old_values' => null,
                        'new_values' => json_encode(['viewed' => true]),
                        'ip_address' => $this->generateIP(),
                        'user_agent' => $this->generateUserAgent(),
                        'created_at' => $logDate
                    ]);
                    
                    $logCount++;
                    $viewCount++;
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }
            
            echo "✅ Audit Logs Seeding Complete!\n";
            echo "   📝 Total Logs: {$logCount}\n\n";
            
            echo "📊 Log Distribution:\n";
            echo "   ➕ CREATE: {$createCount}\n";
            echo "   ✏️  UPDATE: {$updateCount}\n";
            echo "   🔐 LOGIN/LOGOUT: {$authCount}\n";
            echo "   👁️  VIEW: {$viewCount}\n\n";
            
            // Show summary by action type
            $actionsSummary = $db->query("
                SELECT action, COUNT(*) as count 
                FROM audit_logs 
                GROUP BY action 
                ORDER BY count DESC
            ")->getResultArray();
            
            echo "📈 Action Summary:\n";
            foreach ($actionsSummary as $row) {
                $emoji = match($row['action']) {
                    'CREATE' => '➕',
                    'UPDATE' => '✏️',
                    'DELETE' => '🗑️',
                    'LOGIN' => '🔐',
                    'LOGOUT' => '🔓',
                    'VIEW' => '👁️',
                    default => '📋'
                };
                echo "   {$emoji} {$row['action']}: {$row['count']}\n";
            }
            
        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
    
    private function getRecordId($table, $db, $salesIds, $productIds, $customerIds, $supplierIds, $poIds)
    {
        switch ($table) {
            case 'sales':
                return $salesIds[array_rand($salesIds)]['id'] ?? null;
            case 'products':
                return $productIds[array_rand($productIds)]['id'] ?? null;
            case 'customers':
                return $customerIds[array_rand($customerIds)]['id'] ?? null;
            case 'suppliers':
                return $supplierIds[array_rand($supplierIds)]['id'] ?? null;
            case 'purchase_orders':
                return $poIds[array_rand($poIds)]['id_po'] ?? null;
            case 'expenses':
            case 'users':
            default:
                return rand(1, 10);
        }
    }
    
    private function generateNewValues($table)
    {
        $values = [];
        
        switch ($table) {
            case 'sales':
                $values = [
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'total_amount' => rand(100000, 50000000),
                    'payment_status' => ['PAID', 'UNPAID', 'PARTIAL'][array_rand(['PAID', 'UNPAID', 'PARTIAL'])]
                ];
                break;
            case 'products':
                $values = [
                    'name' => 'Product ' . rand(1, 100),
                    'price_sell' => rand(10000, 1000000),
                    'quantity' => rand(10, 500)
                ];
                break;
            case 'customers':
                $values = [
                    'name' => 'Customer ' . rand(1, 50),
                    'receivable_balance' => rand(0, 10000000),
                    'credit_limit' => rand(5000000, 50000000)
                ];
                break;
            case 'users':
                $values = [
                    'username' => 'user' . rand(1, 100),
                    'fullname' => 'User ' . rand(1, 100),
                    'is_active' => rand(0, 1)
                ];
                break;
            case 'suppliers':
                $values = [
                    'name' => 'Supplier ' . rand(1, 20),
                    'debt_balance' => rand(0, 50000000)
                ];
                break;
            case 'expenses':
                $values = [
                    'amount' => rand(50000, 5000000),
                    'category' => ['OPERASIONAL', 'TRANSPORTASI', 'GAJI', 'SEWA'][array_rand(['OPERASIONAL', 'TRANSPORTASI', 'GAJI', 'SEWA'])]
                ];
                break;
            default:
                $values = ['updated' => true];
        }
        
        return $values;
    }
    
    private function generateIP()
    {
        $ips = [
            '127.0.0.1',
            '192.168.1.' . rand(1, 255),
            '192.168.0.' . rand(1, 255),
            '10.0.0.' . rand(1, 255),
            '172.16.0.' . rand(1, 255)
        ];
        
        return $ips[array_rand($ips)];
    }
    
    private function generateUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];
        
        return $userAgents[array_rand($userAgents)];
    }
}
