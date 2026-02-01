<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SalesDataSeeder extends Seeder
{
    public function run()
    {
        echo "💰 Creating Sales Transaction Data..." . PHP_EOL . PHP_EOL;
        
        // Get existing data
        $products = $this->db->table('products')->select('id, sku, name, price, cost_price')->get()->getResultArray();
        $customers = $this->db->table('customers')->select('id, name, receivable_balance')->get()->getResultArray();
        
        if (empty($products)) {
            echo "❌ Error: No products found. Please run Phase4TestDataSeeder first." . PHP_EOL;
            return;
        }
        
        if (empty($customers)) {
            echo "❌ Error: No customers found. Please run Phase4TestDataSeeder first." . PHP_EOL;
            return;
        }
        
        // Check if sales already exist
        $existingSales = $this->db->table('sales')->countAll();
        if ($existingSales > 0) {
            echo "⚠️  Warning: Found {$existingSales} existing sales." . PHP_EOL;
            echo "❓ This seeder will add MORE sales data." . PHP_EOL;
            echo "   If you want to start fresh, truncate 'sales' and 'sale_items' tables first." . PHP_EOL . PHP_EOL;
        }
        
        $productMap = [];
        foreach ($products as $p) {
            $productMap[$p['id']] = $p;
        }
        
        // Sales templates: mix of cash and credit over 90 days
        $salesTemplates = [
            // CASH SALES (10 transactions) - All PAID
            ['days_ago' => 2, 'type' => 'CASH', 'customer_id' => 1, 'status' => 'PAID', 'products' => [[6, 2], [7, 3], [8, 1]]], // 2 Laptop ROG, 3 Mouse, 1 Keyboard = 31.4M
            ['days_ago' => 5, 'type' => 'CASH', 'customer_id' => 2, 'status' => 'PAID', 'products' => [[14, 10], [15, 15], [16, 20]]], // Coffee, Tea, Snacks = 1.875M
            ['days_ago' => 7, 'type' => 'CASH', 'customer_id' => 3, 'status' => 'PAID', 'products' => [[17, 50], [18, 30], [19, 25]]], // Stationery = 850K
            ['days_ago' => 10, 'type' => 'CASH', 'customer_id' => 5, 'status' => 'PAID', 'products' => [[20, 20], [21, 15], [22, 25]]], // Health products = 1.825M
            ['days_ago' => 15, 'type' => 'CASH', 'customer_id' => 1, 'status' => 'PAID', 'products' => [[1, 3], [2, 10], [3, 5]]], // Old products = 19.25M
            ['days_ago' => 20, 'type' => 'CASH', 'customer_id' => 2, 'status' => 'PAID', 'products' => [[12, 10], [13, 15]]], // Clothing = 4.25M
            ['days_ago' => 25, 'type' => 'CASH', 'customer_id' => 4, 'status' => 'PAID', 'products' => [[9, 8], [10, 10]]], // Headset, Webcam = 8.4M
            ['days_ago' => 30, 'type' => 'CASH', 'customer_id' => 3, 'status' => 'PAID', 'products' => [[14, 20], [16, 50]]], // Coffee, Snacks = 2.25M
            ['days_ago' => 45, 'type' => 'CASH', 'customer_id' => 5, 'status' => 'PAID', 'products' => [[17, 100], [18, 50], [19, 40]]], // Stationery = 1.48M
            ['days_ago' => 60, 'type' => 'CASH', 'customer_id' => 1, 'status' => 'PAID', 'products' => [[4, 5], [5, 20]]], // Monitor, Flashdisk = 10M
            
            // CREDIT SALES - PAID (5 transactions) - Fully paid off
            ['days_ago' => 8, 'type' => 'CREDIT', 'customer_id' => 1, 'status' => 'PAID', 'paid_pct' => 100, 'products' => [[6, 1], [8, 2]]], // 1 Laptop ROG, 2 Keyboard = 16.3M
            ['days_ago' => 12, 'type' => 'CREDIT', 'customer_id' => 2, 'status' => 'PAID', 'paid_pct' => 100, 'products' => [[12, 20], [13, 10]]], // Clothing = 5.5M
            ['days_ago' => 18, 'type' => 'CREDIT', 'customer_id' => 3, 'status' => 'PAID', 'paid_pct' => 100, 'products' => [[9, 15], [10, 20]]], // Gaming gear = 16.25M
            ['days_ago' => 35, 'type' => 'CREDIT', 'customer_id' => 4, 'status' => 'PAID', 'paid_pct' => 100, 'products' => [[1, 5], [2, 20]]], // Old laptops = 31.5M
            ['days_ago' => 50, 'type' => 'CREDIT', 'customer_id' => 5, 'status' => 'PAID', 'paid_pct' => 100, 'products' => [[20, 50], [21, 40], [22, 60]]], // Health bulk = 5.93M
            
            // CREDIT SALES - PARTIAL (7 transactions) - 40-80% paid
            ['days_ago' => 3, 'type' => 'CREDIT', 'customer_id' => 1, 'status' => 'PARTIAL', 'paid_pct' => 60, 'products' => [[6, 1], [7, 5]]], // Laptop ROG, Mice = 16.25M
            ['days_ago' => 6, 'type' => 'CREDIT', 'customer_id' => 2, 'status' => 'PARTIAL', 'paid_pct' => 50, 'products' => [[14, 50], [15, 60]]], // Coffee, Tea bulk = 7.05M
            ['days_ago' => 11, 'type' => 'CREDIT', 'customer_id' => 4, 'status' => 'PARTIAL', 'paid_pct' => 70, 'products' => [[8, 10], [9, 8]]], // Keyboards, Headsets = 10.9M
            ['days_ago' => 16, 'type' => 'CREDIT', 'customer_id' => 1, 'status' => 'PARTIAL', 'paid_pct' => 80, 'products' => [[3, 20], [4, 10]]], // Old products = 24M
            ['days_ago' => 22, 'type' => 'CREDIT', 'customer_id' => 5, 'status' => 'PARTIAL', 'paid_pct' => 40, 'products' => [[12, 15], [13, 20]]], // Clothing = 6M
            ['days_ago' => 40, 'type' => 'CREDIT', 'customer_id' => 2, 'status' => 'PARTIAL', 'paid_pct' => 55, 'products' => [[17, 200], [18, 150]]], // Stationery bulk = 2.5M
            ['days_ago' => 55, 'type' => 'CREDIT', 'customer_id' => 3, 'status' => 'PARTIAL', 'paid_pct' => 65, 'products' => [[20, 30], [21, 25], [22, 40]]], // Health = 3.245M
            
            // CREDIT SALES - UNPAID (8 transactions) - 0% paid
            ['days_ago' => 1, 'type' => 'CREDIT', 'customer_id' => 4, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[6, 2], [9, 5]]], // Recent large order = 32.75M
            ['days_ago' => 4, 'type' => 'CREDIT', 'customer_id' => 1, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[7, 10], [8, 5]]], // Peripherals = 5.75M
            ['days_ago' => 9, 'type' => 'CREDIT', 'customer_id' => 2, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[14, 30], [15, 40]]], // Coffee, Tea = 4.45M
            ['days_ago' => 13, 'type' => 'CREDIT', 'customer_id' => 5, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[12, 25], [13, 30]]], // Clothing = 9.5M
            ['days_ago' => 19, 'type' => 'CREDIT', 'customer_id' => 3, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[17, 150], [18, 100], [19, 80]]], // Stationery = 2.71M
            ['days_ago' => 28, 'type' => 'CREDIT', 'customer_id' => 4, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[10, 15], [20, 40]]], // Webcams, Masks = 7.8M
            ['days_ago' => 42, 'type' => 'CREDIT', 'customer_id' => 1, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[1, 4], [2, 15]]], // Old laptops = 25.125M
            ['days_ago' => 65, 'type' => 'CREDIT', 'customer_id' => 2, 'status' => 'UNPAID', 'paid_pct' => 0, 'products' => [[21, 50], [22, 80]]], // Health products = 3.99M
        ];
        
        echo "📝 Creating " . count($salesTemplates) . " sales transactions..." . PHP_EOL;
        echo "   - 10 Cash sales (all PAID)" . PHP_EOL;
        echo "   - 5 Credit sales (PAID - fully settled)" . PHP_EOL;
        echo "   - 7 Credit sales (PARTIAL - 40-80% paid)" . PHP_EOL;
        echo "   - 8 Credit sales (UNPAID - outstanding)" . PHP_EOL . PHP_EOL;
        
        $saleCount = 0;
        $itemCount = 0;
        $invoiceCounter = 1000;
        
        foreach ($salesTemplates as $template) {
            $saleCount++;
            $invoiceCounter++;
            
            // Calculate sale date
            $saleDate = date('Y-m-d H:i:s', strtotime("-{$template['days_ago']} days"));
            $dueDate = date('Y-m-d', strtotime("-{$template['days_ago']} days +30 days"));
            
            // Calculate totals
            $totalAmount = 0;
            $totalProfit = 0;
            $saleItems = [];
            
            foreach ($template['products'] as $item) {
                $productId = $item[0];
                $quantity = $item[1];
                
                if (!isset($productMap[$productId])) {
                    continue;
                }
                
                $product = $productMap[$productId];
                $price = $product['price'];
                $costPrice = $product['cost_price'];
                $subtotal = $price * $quantity;
                $profit = ($price - $costPrice) * $quantity;
                
                $totalAmount += $subtotal;
                $totalProfit += $profit;
                
                $saleItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'product_name' => $product['name']
                ];
                
                $itemCount++;
            }
            
            // Calculate paid amount
            $paidAmount = 0;
            if ($template['type'] === 'CASH') {
                $paidAmount = $totalAmount; // Cash always fully paid
            } else {
                $paidPct = $template['paid_pct'] ?? 0;
                $paidAmount = ($totalAmount * $paidPct) / 100;
            }
            
            // Insert sale
            $saleData = [
                'invoice_number' => 'INV-' . date('Ymd', strtotime($saleDate)) . '-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                'created_at' => $saleDate,
                'customer_id' => $template['customer_id'],
                'user_id' => 1, // admin user
                'salesperson_id' => rand(1, 2), // Random salesperson
                'warehouse_id' => 1, // Main warehouse
                'payment_type' => $template['type'],
                'due_date' => $dueDate,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'payment_status' => $template['status'],
                'total_profit' => $totalProfit,
                'is_hidden' => 0,
                'deleted_at' => null
            ];
            
            $this->db->table('sales')->insert($saleData);
            $saleId = $this->db->insertID();
            
            // Insert sale items
            foreach ($saleItems as $item) {
                $this->db->table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);
            }
            
            // Update customer receivable balance for credit sales
            if ($template['type'] === 'CREDIT' && $template['status'] !== 'PAID') {
                $outstanding = $totalAmount - $paidAmount;
                $this->db->query(
                    "UPDATE customers SET receivable_balance = receivable_balance + ? WHERE id = ?",
                    [$outstanding, $template['customer_id']]
                );
            }
            
            // Display progress
            $statusEmoji = $template['status'] === 'PAID' ? '✅' : ($template['status'] === 'PARTIAL' ? '⚠️' : '❌');
            echo sprintf(
                "   %s [%d/%d] %s | %s | %s | Items: %d | Total: Rp %s | Profit: Rp %s\n",
                $statusEmoji,
                $saleCount,
                count($salesTemplates),
                $saleData['invoice_number'],
                $template['type'],
                $template['status'],
                count($saleItems),
                number_format($totalAmount, 0, ',', '.'),
                number_format($totalProfit, 0, ',', '.')
            );
        }
        
        echo PHP_EOL . "✅ Sales Data Seeding Complete!" . PHP_EOL;
        echo "   📊 Total Sales: {$saleCount} transactions" . PHP_EOL;
        echo "   📦 Total Items: {$itemCount} sale items" . PHP_EOL;
        
        // Show summary
        $totalRevenue = $this->db->query("SELECT SUM(total_amount) as total FROM sales")->getRow()->total;
        $totalProfit = $this->db->query("SELECT SUM(total_profit) as total FROM sales")->getRow()->total;
        $cashRevenue = $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE payment_type = 'CASH'")->getRow()->total;
        $creditRevenue = $this->db->query("SELECT SUM(total_amount) as total FROM sales WHERE payment_type = 'CREDIT'")->getRow()->total;
        
        echo PHP_EOL . "📈 Sales Summary:" . PHP_EOL;
        echo "   💰 Total Revenue: Rp " . number_format($totalRevenue, 0, ',', '.') . PHP_EOL;
        echo "   💵 Total Profit: Rp " . number_format($totalProfit, 0, ',', '.') . PHP_EOL;
        echo "   💵 Cash Sales: Rp " . number_format($cashRevenue, 0, ',', '.') . PHP_EOL;
        echo "   💳 Credit Sales: Rp " . number_format($creditRevenue, 0, ',', '.') . PHP_EOL;
        echo "   📊 Profit Margin: " . number_format(($totalProfit / $totalRevenue) * 100, 2) . "%" . PHP_EOL;
        
        echo PHP_EOL . "🎯 Next Steps:" . PHP_EOL;
        echo "   1. Visit Analytics Dashboard: http://localhost:8080/info/analytics/dashboard" . PHP_EOL;
        echo "   2. Test date filters (Last 7 Days, 30 Days, etc.)" . PHP_EOL;
        echo "   3. Export CSV and verify data" . PHP_EOL;
        echo "   4. Check Sales List: http://localhost:8080/transactions/sales" . PHP_EOL;
        echo "   5. View Customer receivables updated" . PHP_EOL;
    }
}
