<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run()
    {
        // Create sample sales transactions
        $customers = $this->db->table('customers')->limit(3)->get()->getResultArray();
        $products = $this->db->table('products')->limit(10)->get()->getResultArray();
        
        $sales = [];
        $saleDetails = [];
        
        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers[$i % count($customers)];
            $numItems = rand(1, 3);
            $totalAmount = 0;
            
            $saleId = $i;
            $saleData = [
                'customer_id' => $customer['id'],
                'payment_type' => $i % 3 == 0 ? 'credit' : 'cash',
                'total_amount' => 0, // Will be calculated
                'paid_amount' => 0,
                'status' => 'completed',
                'notes' => 'Sale transaction #' . $i,
                'sale_date' => date('Y-m-d', strtotime("-{$i} days")),
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days"))
            ];
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products[$j % count($products)];
                $quantity = rand(1, 5);
                $price = $product['price_sell'];
                $subtotal = $quantity * $price;
                $totalAmount += $subtotal;
                
                $saleDetails[] = [
                    'sale_id' => $saleId,
                    'product_id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days"))
                ];
            }
            
            $saleData['total_amount'] = $totalAmount;
            $saleData['paid_amount'] = $saleData['payment_type'] === 'cash' ? $totalAmount : ($totalAmount * 0.3);
            $sales[] = $saleData;
        }
        
        $this->db->table('sales')->insertBatch($sales);
        $this->db->table('sale_details')->insertBatch($saleDetails);
    }
}