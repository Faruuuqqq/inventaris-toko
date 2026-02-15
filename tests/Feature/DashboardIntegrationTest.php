<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;

class DashboardIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed comprehensive data for dashboard
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\CustomerSeeder::class);
        $this->seed(\App\Database\Seeds\SaleSeeder::class);
        $this->seed(\App\Database\Seeds\PurchaseSeeder::class);
    }

    /** @test */
    public function it_displays_correct_sales_metrics()
    {
        // Arrange: Create sample sales
        $this->createMultipleSales();

        // Act: Get dashboard data
        $response = $this->get('dashboard/api/metrics');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('sales', $data);
        $this->assertArrayHasKey('today', $data['sales']);
        $this->assertArrayHasKey('this_month', $data['sales']);
        $this->assertArrayHasKey('total', $data['sales']);

        $this->assertGreaterThan(0, $data['sales']['today']['total']);
        $this->assertGreaterThan(0, $data['sales']['this_month']['total']);
        $this->assertGreaterThan(0, $data['sales']['total']);
    }

    /** @test */
    public function it_shows_inventory_summary()
    {
        // Arrange: Create products with varying stock levels
        $this->createProductsWithStock();

        // Act: Get inventory metrics
        $response = $this->get('dashboard/api/inventory');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('total_products', $data);
        $this->assertArrayHasKey('low_stock_count', $data);
        $this->assertArrayHasKey('total_stock_value', $data);
        $this->assertArrayHasKey('out_of_stock_count', $data);

        $this->assertGreaterThan(0, $data['total_products']);
        $this->assertGreaterThanOrEqual(0, $data['low_stock_count']);
    }

    /** @test */
    public function it_generates_sales_trend_data()
    {
        // Arrange: Create sales over the last 30 days
        $this->createSalesOverPeriod(30);

        // Act: Get trend data
        $response = $this->get('dashboard/api/trends?period=30days');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('daily_sales', $data);
        $this->assertArrayHasKey('moving_average', $data);
        $this->assertArrayHasKey('growth_rate', $data);

        // Should have 30 data points
        $this->assertCount(30, $data['daily_sales']);
        
        // Verify data structure
        $firstDay = $data['daily_sales'][0];
        $this->assertArrayHasKey('date', $firstDay);
        $this->assertArrayHasKey('total_sales', $firstDay);
        $this->assertArrayHasKey('transaction_count', $firstDay);
    }

    /** @test */
    public function it_displays_top_selling_products()
    {
        // Arrange: Create sales with different products
        $this->createSalesWithMultipleProducts();

        // Act: Get top products
        $response = $this->get('dashboard/api/top-products');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('products', $data);
        $this->assertNotEmpty($data['products']);

        // Verify structure
        $topProduct = $data['products'][0];
        $this->assertArrayHasKey('id', $topProduct);
        $this->assertArrayHasKey('name', $topProduct);
        $this->assertArrayHasKey('total_quantity', $topProduct);
        $this->assertArrayHasKey('total_revenue', $topProduct);
        $this->assertArrayHasKey('rank', $topProduct);

        // Should be sorted by quantity descending
        for ($i = 1; $i < count($data['products']); $i++) {
            $this->assertGreaterThanOrEqual(
                $data['products'][$i]['total_quantity'],
                $data['products'][$i-1]['total_quantity']
            );
        }
    }

    /** @test */
    public function it_shows_financial_overview()
    {
        // Arrange: Create financial transactions
        $this->createFinancialTransactions();

        // Act: Get financial overview
        $response = $this->get('dashboard/api/financial');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('revenue', $data);
        $this->assertArrayHasKey('expenses', $data);
        $this->assertArrayHasKey('profit', $data);
        $this->assertArrayHasKey('receivables', $data);
        $this->assertArrayHasKey('payables', $data);

        $this->assertEquals(
            $data['revenue']['total'] - $data['expenses']['total'],
            $data['profit']['net']
        );
    }

    /** @test */
    public function it_provides_real_time_notifications()
    {
        // Arrange: Create activities that should trigger notifications
        $this->createLowStockProducts();
        $this->createOverduePayments();

        // Act: Get notifications
        $response = $this->get('dashboard/api/notifications');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('notifications', $data);
        $this->assertArrayHasKey('unread_count', $data);

        // Should have low stock notifications
        $hasLowStock = false;
        foreach ($data['notifications'] as $notification) {
            if ($notification['type'] === 'low_stock') {
                $hasLowStock = true;
                break;
            }
        }
        $this->assertTrue($hasLowStock);
    }

    private function createMultipleSales()
    {
        $customer = $this->customerModel->first();
        $product = $this->productModel->first();

        // Create sales over different periods
        for ($i = 1; $i <= 20; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $this->db->table('sales')->insert([
                'customer_id' => $customer->id,
                'payment_type' => 'cash',
                'total_amount' => 500000 + ($i * 25000),
                'status' => 'completed',
                'created_at' => $date . ' 10:00:00'
            ]);
        }
    }

    private function createProductsWithStock()
    {
        $stockLevels = [0, 5, 10, 50, 100];
        
        foreach ($stockLevels as $index => $stock) {
            $this->db->table('products')->insert([
                'name' => "Product " . ($index + 1),
                'sku' => "PROD-" . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'price_buy' => 50000,
                'price_sell' => 75000,
                'stock' => $stock,
                'min_stock' => 10,
                'is_active' => 1
            ]);
        }
    }

    private function createSalesOverPeriod($days)
    {
        $customer = $this->customerModel->first();
        $product = $this->productModel->first();

        for ($day = 0; $day < $days; $day++) {
            $date = date('Y-m-d', strtotime("-{$day} days"));
            $this->db->table('sales')->insert([
                'customer_id' => $customer->id,
                'payment_type' => 'cash',
                'total_amount' => 500000 + rand(-50000, 100000),
                'status' => 'completed',
                'created_at' => $date . ' ' . rand(9, 17) . ':00:00'
            ]);
        }
    }

    private function createSalesWithMultipleProducts()
    {
        $customer = $this->customerModel->first();
        
        // Create products with different sales volumes
        for ($i = 1; $i <= 10; $i++) {
            $productId = $this->db->table('products')->insertGetId([
                'name' => "Product " . $i,
                'sku' => "PROD-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'price_buy' => 50000,
                'price_sell' => 75000,
                'stock' => 100,
                'is_active' => 1
            ]);

            $quantity = (11 - $i) * 10; // Product 1 sells most
            $this->db->table('sales')->insertGetId([
                'customer_id' => $customer->id,
                'payment_type' => 'cash',
                'total_amount' => $quantity * 75000,
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->table('sale_details')->insert([
                'sale_id' => $this->db->insertID(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => 75000,
                'subtotal' => $quantity * 75000
            ]);
        }
    }

    private function createFinancialTransactions()
    {
        // Create revenue
        for ($i = 1; $i <= 10; $i++) {
            $this->db->table('sales')->insert([
                'customer_id' => $this->customerModel->first()->id,
                'payment_type' => 'cash',
                'total_amount' => 1000000 + ($i * 100000),
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create expenses
        for ($i = 1; $i <= 5; $i++) {
            $this->db->table('expenses')->insert([
                'description' => 'Expense ' . $i,
                'amount' => 500000 + ($i * 100000),
                'expense_date' => date('Y-m-d'),
                'category' => 'operational'
            ]);
        }
    }

    private function createLowStockProducts()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->db->table('products')->insert([
                'name' => "Low Stock Product " . $i,
                'sku' => "LOW-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'price_buy' => 50000,
                'price_sell' => 75000,
                'stock' => 5, // Below min_stock
                'min_stock' => 10,
                'is_active' => 1
            ]);
        }
    }

    private function createOverduePayments()
    {
        // Create credit sales with overdue payments
        for ($i = 1; $i <= 3; $i++) {
            $saleId = $this->db->table('sales')->insertGetId([
                'customer_id' => $this->customerModel->first()->id,
                'payment_type' => 'credit',
                'total_amount' => 1000000,
                'paid_amount' => 0,
                'credit_terms' => 30,
                'status' => 'completed',
                'created_at' => date('Y-m-d', strtotime('-40 days'))
            ]);

            $this->db->table('customer_receivables')->insert([
                'customer_id' => $this->customerModel->first()->id,
                'sale_id' => $saleId,
                'amount' => 1000000,
                'due_date' => date('Y-m-d', strtotime('-10 days')),
                'status' => 'overdue'
            ]);
        }
    }
}