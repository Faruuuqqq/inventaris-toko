<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\StockMutationModel;

class ReportingIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\CategorySeeder::class);
        $this->seed(\App\Database\Seeds\SaleSeeder::class);
    }

    /** @test */
    public function it_generates_comprehensive_sales_report()
    {
        // Arrange: Create sales data
        $this->createSalesWithVariations();

        // Act: Generate sales report
        $response = $this->get('info/reports/sales?period=monthly&group_by=product');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('details', $data);
        $this->assertArrayHasKey('charts', $data);

        // Verify summary structure
        $summary = $data['summary'];
        $this->assertArrayHasKey('total_sales', $summary);
        $this->assertArrayHasKey('total_revenue', $summary);
        $this->assertArrayHasKey('total_items', $summary);
        $this->assertArrayHasKey('average_transaction', $summary);
    }

    /** @test */
    public function it_produces_inventory_movement_report()
    {
        // Arrange: Create stock movements
        $this->createStockMovements();

        // Act: Get movement report
        $response = $this->get('info/reports/inventory?report=movements&period=30days');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('movements', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('analysis', $data);

        // Verify movement types
        $movements = $data['movements'];
        $this->assertNotEmpty($movements);
        
        $movementTypes = array_unique(array_column($movements, 'type'));
        $this->assertContains('in', $movementTypes);
        $this->assertContains('out', $movementTypes);
    }

    /** @test */
    public function it_generates_customer_aging_report()
    {
        // Arrange: Create customer receivables
        $this->createCustomerReceivables();

        // Act: Get aging report
        $response = $this->get('finance/reports/customer-aging');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('aging_buckets', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('customers', $data);

        // Verify aging buckets
        $buckets = $data['aging_buckets'];
        $this->assertArrayHasKey('current', $buckets);
        $this->assertArrayHasKey('1_30_days', $buckets);
        $this->assertArrayHasKey('31_60_days', $buckets);
        $this->assertArrayHasKey('61_90_days', $buckets);
        $this->assertArrayHasKey('over_90_days', $buckets);
    }

    /** @test */
    public function it_produces_supplier_performance_report()
    {
        // Arrange: Create purchase data
        $this->createPurchaseData();

        // Act: Get supplier performance report
        $response = $this->get('info/reports/supplier-performance');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('suppliers', $data);
        $this->assertArrayHasKey('metrics', $data);
        $this->assertArrayHasKey('rankings', $data);

        // Verify supplier data
        $suppliers = $data['suppliers'];
        foreach ($suppliers as $supplier) {
            $this->assertArrayHasKey('total_purchases', $supplier);
            $this->assertArrayHasKey('on_time_delivery', $supplier);
            $this->assertArrayHasKey('quality_score', $supplier);
            $this->assertArrayHasKey('payment_compliance', $supplier);
        }
    }

    /** @test */
    public function it_generates_profit_loss_statement()
    {
        // Arrange: Create financial transactions
        $this->createFinancialData();

        // Act: Get P&L statement
        $response = $this->get('finance/reports/profit-loss?period=monthly&month=2024-01');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('revenue', $data);
        $this->assertArrayHasKey('cost_of_goods_sold', $data);
        $this->assertArrayHasKey('gross_profit', $data);
        $this->assertArrayHasKey('operating_expenses', $data);
        $this->assertArrayHasKey('net_profit', $data);

        // Verify profit calculation
        $revenue = $data['revenue']['total'];
        $cogs = $data['cost_of_goods_sold']['total'];
        $expenses = $data['operating_expenses']['total'];
        
        $expectedGrossProfit = $revenue - $cogs;
        $expectedNetProfit = $expectedGrossProfit - $expenses;
        
        $this->assertEquals($expectedGrossProfit, $data['gross_profit']['total']);
        $this->assertEquals($expectedNetProfit, $data['net_profit']['total']);
    }

    /** @test */
    public function it_generates_cash_flow_statement()
    {
        // Arrange: Create cash flow activities
        $this->createCashFlowActivities();

        // Act: Get cash flow statement
        $response = $this->get('finance/reports/cash-flow?period=monthly');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('cash_flows', $data);
        $this->assertArrayHasKey('beginning_balance', $data);
        $this->assertArrayHasKey('ending_balance', $data);
        $this->assertArrayHasKey('net_change', $data);

        // Verify cash flow components
        $cashFlows = $data['cash_flows'];
        $this->assertArrayHasKey('operating', $cashFlows);
        $this->assertArrayHasKey('investing', $cashFlows);
        $this->assertArrayHasKey('financing', $cashFlows);
    }

    /** @test */
    public function it_exports_reports_in_multiple_formats()
    {
        // Arrange: Create sample data
        $this->createSalesWithVariations();

        // Act: Export as CSV
        $csvResponse = $this->get('info/reports/sales/export?format=csv');
        
        // Assert CSV export
        $csvResponse->assertStatus(200);
        $this->assertStringContains('Content-Type: text/csv', $csvResponse->getHeaderLine('Content-Type'));
        $this->assertStringContains('attachment; filename=sales_report.csv', $csvResponse->getHeaderLine('Content-Disposition'));

        // Act: Export as PDF
        $pdfResponse = $this->get('info/reports/sales/export?format=pdf');
        
        // Assert PDF export
        $pdfResponse->assertStatus(200);
        $this->assertStringContains('Content-Type: application/pdf', $pdfResponse->getHeaderLine('Content-Type'));
        $this->assertStringContains('attachment; filename=sales_report.pdf', $pdfResponse->getHeaderLine('Content-Disposition'));
    }

    /** @test */
    public function it_generates_real_time_dashboard_data()
    {
        // Arrange: Create dynamic data
        $this->createDynamicData();

        // Act: Get dashboard analytics
        $response = $this->get('info/reports/dashboard-analytics');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('kpi_metrics', $data);
        $this->assertArrayHasKey('trends', $data);
        $this->assertArrayHasKey('alerts', $data);
        $this->assertArrayHasKey('quick_stats', $data);

        // Verify KPI calculations
        $kpis = $data['kpi_metrics'];
        $this->assertArrayHasKey('daily_sales', $kpis);
        $this->assertArrayHasKey('inventory_turnover', $kpis);
        $this->assertArrayHasKey('profit_margin', $kpis);
        $this->assertArrayHasKey('collection_rate', $kpis);
    }

    /** @test */
    public function it_validates_report_date_ranges()
    {
        // Act: Test invalid date range
        $response = $this->get('info/reports/sales?start_date=2024-01-31&end_date=2024-01-01');

        // Assert: Should validate date range
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_range']);
    }

    /** @test */
    public function it_caches_report_generation()
    {
        // Arrange: Create data
        $this->createSalesWithVariations();

        // Act: First request (cache miss)
        $start = microtime(true);
        $response1 = $this->get('info/reports/sales?period=monthly');
        $firstRequestTime = microtime(true) - $start;

        // Act: Second request (cache hit)
        $start = microtime(true);
        $response2 = $this->get('info/reports/sales?period=monthly');
        $secondRequestTime = microtime(true) - $start;

        // Assert: Both successful
        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verify caching (second request should be faster)
        $this->assertLessThan($firstRequestTime, $secondRequestTime);
    }

    private function createSalesWithVariations()
    {
        $products = $this->productModel->findAll(5);
        $customer = $this->db->table('customers')->limit(1)->get()->getRow();

        for ($i = 1; $i <= 30; $i++) {
            $product = $products[$i % count($products)];
            $quantity = rand(1, 10);
            $price = $product->price_sell;

            $saleId = $this->db->table('sales')->insertGetId([
                'customer_id' => $customer->id,
                'payment_type' => $i % 3 == 0 ? 'credit' : 'cash',
                'total_amount' => $quantity * $price,
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days"))
            ]);

            $this->db->table('sale_details')->insert([
                'sale_id' => $saleId,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $quantity * $price
            ]);
        }
    }

    private function createStockMovements()
    {
        $products = $this->productModel->findAll(3);
        
        for ($i = 1; $i <= 20; $i++) {
            $product = $products[$i % count($products)];
            $type = $i % 3 == 0 ? 'out' : 'in';
            $quantity = rand(1, 50);

            $this->stockMutationModel->insert([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $type === 'out' ? -$quantity : $quantity,
                'reference_type' => $type === 'out' ? 'sale' : 'purchase',
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} hours"))
            ]);
        }
    }

    private function createCustomerReceivables()
    {
        $customers = $this->db->table('customers')->limit(5)->get()->getResultArray();
        
        foreach ($customers as $index => $customer) {
            $amount = 1000000 * ($index + 1);
            $dueDate = date('Y-m-d', strtotime('+'.($index * 10).' days'));

            $this->db->table('customer_receivables')->insert([
                'customer_id' => $customer['id'],
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'unpaid',
                'created_at' => date('Y-m-d H:i:s', strtotime('-'.($index * 5).' days'))
            ]);
        }
    }

    private function createPurchaseData()
    {
        $suppliers = $this->db->table('suppliers')->limit(3)->get()->getResultArray();
        
        foreach ($suppliers as $index => $supplier) {
            $amount = 5000000 * ($index + 1);
            $deliveryDate = date('Y-m-d', strtotime('+'.($index * 5).' days'));
            $onTime = $index % 3 != 0;

            $this->db->table('purchase_orders')->insert([
                'supplier_id' => $supplier['id'],
                'total_amount' => $amount,
                'status' => 'received',
                'delivery_date' => $deliveryDate,
                'on_time_delivery' => $onTime,
                'quality_score' => rand(80, 100),
                'created_at' => date('Y-m-d H:i:s', strtotime('-'.($index * 10).' days'))
            ]);
        }
    }

    private function createFinancialData()
    {
        // Create revenue
        for ($i = 1; $i <= 20; $i++) {
            $this->db->table('sales')->insert([
                'payment_type' => 'cash',
                'total_amount' => 1000000 + ($i * 100000),
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create expenses
        for ($i = 1; $i <= 15; $i++) {
            $this->db->table('expenses')->insert([
                'description' => 'Expense ' . $i,
                'amount' => 500000 + ($i * 50000),
                'category' => 'operational',
                'status' => 'approved',
                'expense_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function createCashFlowActivities()
    {
        // Operating activities
        for ($i = 1; $i <= 10; $i++) {
            $this->db->table('journal_entries')->insert([
                'account_code' => $i % 2 == 0 ? '111' : '411',
                'reference_type' => 'sale',
                'debit' => $i % 2 == 0 ? 1000000 : 0,
                'credit' => $i % 2 == 0 ? 0 : 1000000,
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days"))
            ]);
        }
    }

    private function createDynamicData()
    {
        // Create recent activities for dashboard
        for ($i = 1; $i <= 5; $i++) {
            $this->db->table('sales')->insert([
                'payment_type' => 'cash',
                'total_amount' => 1000000 + ($i * 250000),
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} hours"))
            ]);
        }

        // Create stock alerts
        for ($i = 1; $i <= 3; $i++) {
            $this->db->table('products')->insert([
                'name' => 'Low Stock Product ' . $i,
                'sku' => 'LOW-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'stock' => 5,
                'min_stock' => 10,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}