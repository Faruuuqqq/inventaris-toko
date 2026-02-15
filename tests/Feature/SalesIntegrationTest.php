<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\StockMutationModel;

class SalesIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed minimal data for testing
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\CustomerSeeder::class);
        $this->seed(\App\Database\Seeds\UserSeeder::class);
    }

    /** @test */
    public function it_can_create_cash_sale_with_stock_deduction()
    {
        // Arrange: Get seeded data
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $initialStock = $product->stock;

        // Act: Create cash sale
        $saleData = [
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'price' => $product->price_sell,
                    'subtotal' => 5 * $product->price_sell
                ]
            ],
            'total_amount' => 5 * $product->price_sell,
            'paid_amount' => 5 * $product->price_sell,
            'change_amount' => 0
        ];

        $response = $this->post('transactions/sales/store', $saleData);

        // Assert: Response success
        $response->assertStatus(201);

        // Assert: Sale created in database
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'total_amount' => 5 * $product->price_sell
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => $product->price_sell
        ]);

        // Assert: Stock properly deducted
        $updatedProduct = $this->productModel->find($product->id);
        $expectedStock = $initialStock - 5;
        $this->assertEquals($expectedStock, $updatedProduct->stock);

        // Assert: Stock mutation recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => -5,
            'reference_type' => 'sale',
            'reference_id' => $this->getLastSaleId()
        ]);
    }

    /** @test */
    public function it_can_create_credit_sale_with_payment_tracking()
    {
        // Arrange
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $totalAmount = 5 * $product->price_sell;

        // Act: Create credit sale
        $saleData = [
            'customer_id' => $customer->id,
            'payment_type' => 'credit',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'price' => $product->price_sell,
                    'subtotal' => 5 * $product->price_sell
                ]
            ],
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'credit_amount' => $totalAmount
        ];

        $response = $this->post('transactions/sales/store', $saleData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'payment_type' => 'credit',
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'credit_amount' => $totalAmount
        ]);

        // Assert: Customer credit updated
        $updatedCustomer = $this->customerModel->find($customer->id);
        $this->assertGreaterThan($customer->credit_limit_used, $updatedCustomer->credit_limit_used);
    }

    /** @test */
    public function it_validates_stock_availability_before_sale()
    {
        // Arrange
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $excessQuantity = $product->stock + 10;

        // Act: Attempt to sell more than available
        $saleData = [
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $excessQuantity,
                    'price' => $product->price_sell
                ]
            ]
        ];

        $response = $this->post('transactions/sales/store', $saleData);

        // Assert: Should fail
        $response->assertStatus(422);
        $this->assertArrayHasKey('items.0.quantity', $response->getJSON());

        // Assert: No sale created
        $this->assertDatabaseMissing('sales', [
            'customer_id' => $customer->id
        ]);
    }

    /** @test */
    public function it_can_list_sales_with_pagination()
    {
        // Arrange: Create multiple sales
        for ($i = 1; $i <= 15; $i++) {
            $this->createTestSale();
        }

        // Act
        $response = $this->get('transactions/sales?page=1&limit=10');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'customer_id', 'payment_type', 'total_amount',
                    'paid_amount', 'credit_amount', 'created_at',
                    'customer' => ['id', 'name'],
                    'details' => [
                        '*' => ['id', 'product_id', 'quantity', 'price', 'subtotal']
                    ]
                ]
            ],
            'pagination' => [
                'current_page', 'last_page', 'per_page', 'total'
            ]
        ]);

        $data = $response->getJSON();
        $this->assertCount(10, $data['data']);
        $this->assertEquals(15, $data['pagination']['total']);
    }

    /** @test */
    public function it_can_update_sale_status_and_history()
    {
        // Arrange: Create a sale
        $sale = $this->createTestSale();

        // Act: Update status
        $response = $this->put("transactions/sales/{$sale->id}/status", [
            'status' => 'completed',
            'notes' => 'Delivery completed successfully'
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'completed'
        ]);

        // Assert: History recorded
        $this->assertDatabaseHas('sale_histories', [
            'sale_id' => $sale->id,
            'status' => 'completed',
            'notes' => 'Delivery completed successfully'
        ]);
    }

    /** @test */
    public function it_generates_correct_receipt_data()
    {
        // Arrange
        $sale = $this->createTestSaleWithItems();

        // Act
        $response = $this->get("transactions/sales/{$sale->id}/receipt");

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('sale', $data);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('company_info', $data);
        
        $this->assertEquals($sale->id, $data['sale']['id']);
        $this->assertNotEmpty($data['items']);
        $this->assertArrayHasKey('subtotal', $data['sale']);
        $this->assertArrayHasKey('tax', $data['sale']);
        $this->assertArrayHasKey('grand_total', $data['sale']);
    }

    private function createTestSale($overrides = [])
    {
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();

        $saleData = array_merge([
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'total_amount' => $product->price_sell * 2,
            'paid_amount' => $product->price_sell * 2,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ], $overrides);

        return $this->saleModel->insert($saleData);
    }

    private function createTestSaleWithItems()
    {
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $quantity = 3;
        $price = $product->price_sell;
        $total = $quantity * $price;

        // Create sale
        $saleId = $this->saleModel->insert([
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'total_amount' => $total,
            'paid_amount' => $total,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Create sale details
        $this->saleDetailModel->insert([
            'sale_id' => $saleId,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $total
        ]);

        return $this->saleModel->find($saleId);
    }

    private function getLastSaleId()
    {
        $result = $this->db->table('sales')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
        return $result ? $result->id : null;
    }
}