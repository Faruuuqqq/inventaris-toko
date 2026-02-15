<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\ProductModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderDetailModel;
use App\Models\StockMutationModel;

class PurchaseIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\SupplierSeeder::class);
        $this->seed(\App\Database\Seeds\UserSeeder::class);
    }

    /** @test */
    public function it_can_create_purchase_order_with_proper_workflow()
    {
        // Arrange: Get seeded data
        $product = $this->productModel->first();
        $supplier = $this->supplierModel->first();

        // Act: Create PO
        $poData = [
            'supplier_id' => $supplier->id,
            'order_date' => date('Y-m-d'),
            'expected_date' => date('Y-m-d', strtotime('+7 days')),
            'notes' => 'Test Purchase Order',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 100,
                    'price' => 50000,
                    'subtotal' => 100 * 50000
                ]
            ],
            'total_amount' => 100 * 50000,
            'status' => 'pending'
        ];

        $response = $this->post('transactions/purchases/store', $poData);

        // Assert: Response success
        $response->assertStatus(201);

        // Assert: PO created in database
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'total_amount' => 100 * 50000,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('purchase_order_details', [
            'product_id' => $product->id,
            'quantity' => 100,
            'price' => 50000
        ]);

        // Assert: No stock update yet (still pending)
        $updatedProduct = $this->productModel->find($product->id);
        $this->assertEquals($product->stock, $updatedProduct->stock);
    }

    /** @test */
    public function it_can_receive_goods_and_update_stock()
    {
        // Arrange: Create PO first
        $po = $this->createTestPurchaseOrder();

        // Act: Receive goods
        $receiveData = [
            'received_date' => date('Y-m-d'),
            'items' => [
                [
                    'po_detail_id' => $po->details[0]->id,
                    'received_quantity' => 95, // Partial receive
                    'notes' => 'Good quality'
                ]
            ],
            'notes' => 'Partial goods received'
        ];

        $response = $this->post("transactions/purchases/receive/{$po->id}", $receiveData);

        // Assert
        $response->assertStatus(200);

        // Assert: Stock updated
        $product = $this->productModel->find($po->details[0]->product_id);
        $this->assertGreaterThan(0, $product->stock);

        // Assert: Stock mutation recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $po->details[0]->product_id,
            'type' => 'in',
            'quantity' => 95,
            'reference_type' => 'purchase',
            'reference_id' => $po->id
        ]);

        // Assert: PO status updated
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'partially_received'
        ]);
    }

    /** @test */
    public function it_handles_purchase_returns_with_stock_adjustment()
    {
        // Arrange: Create and receive PO first
        $po = $this->createTestPurchaseOrder();
        $this->receivePurchaseOrder($po, 100);

        // Act: Create return
        $returnData = [
            'return_date' => date('Y-m-d'),
            'reason' => 'Defective items',
            'items' => [
                [
                    'product_id' => $po->details[0]->product_id,
                    'quantity' => 10, // Return 10 items
                    'reason' => 'Quality issues'
                ]
            ]
        ];

        $response = $this->post("transactions/purchases/return/{$po->id}", $returnData);

        // Assert
        $response->assertStatus(201);

        // Assert: Stock deducted
        $product = $this->productModel->find($po->details[0]->product_id);
        $this->assertLessThan(100, $product->stock);

        // Assert: Stock mutation for return
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $po->details[0]->product_id,
            'type' => 'out',
            'quantity' => -10,
            'reference_type' => 'purchase_return',
            'reference_id' => $this->getLastReturnId()
        ]);
    }

    /** @test */
    public function it_validates_supplier_credit_limit()
    {
        // Arrange
        $supplier = $this->supplierModel->first();
        $excessiveAmount = $supplier->credit_limit + 1000000;

        // Act
        $poData = [
            'supplier_id' => $supplier->id,
            'total_amount' => $excessiveAmount,
            'items' => [
                [
                    'product_id' => $this->productModel->first()->id,
                    'quantity' => 1000,
                    'price' => 100000
                ]
            ]
        ];

        $response = $this->post('transactions/purchases/store', $poData);

        // Assert: Should fail for credit limit
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['supplier_id']);
    }

    /** @test */
    public function it_can_list_purchase_orders_with_filters()
    {
        // Arrange: Create POs with different statuses
        $this->createTestPurchaseOrder(['status' => 'pending']);
        $this->createTestPurchaseOrder(['status' => 'completed']);
        $this->createTestPurchaseOrder(['status' => 'cancelled']);

        // Act: Filter by status
        $response = $this->get('transactions/purchases?status=pending');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertNotEmpty($data['data']);
        foreach ($data['data'] as $po) {
            $this->assertEquals('pending', $po['status']);
        }
    }

    /** @test */
    public function it_calculates_purchase_metrics_correctly()
    {
        // Arrange: Create PO with multiple items
        $product1 = $this->productModel->first();
        $product2 = $this->productModel->find($this->productModel->first()->id + 1);
        $supplier = $this->supplierModel->first();

        $poData = [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 10,
                    'price' => 50000
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 5,
                    'price' => 75000
                ]
            ]
        ];

        // Act
        $response = $this->post('transactions/purchases/store', $poData);

        // Assert: Calculations correct
        $response->assertStatus(201);
        
        $expectedTotal = (10 * 50000) + (5 * 75000);
        $this->assertDatabaseHas('purchase_orders', [
            'total_amount' => $expectedTotal
        ]);
    }

    private function createTestPurchaseOrder($overrides = [])
    {
        $product = $this->productModel->first();
        $supplier = $this->supplierModel->first();

        $poData = array_merge([
            'supplier_id' => $supplier->id,
            'order_date' => date('Y-m-d'),
            'total_amount' => 100 * 50000,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ], $overrides);

        $poId = $this->purchaseOrderModel->insert($poData);

        // Create PO details
        $this->purchaseOrderDetailModel->insert([
            'purchase_order_id' => $poId,
            'product_id' => $product->id,
            'quantity' => 100,
            'price' => 50000,
            'subtotal' => 100 * 50000
        ]);

        $po = $this->purchaseOrderModel->find($poId);
        $po->details = [$this->purchaseOrderDetailModel->where('purchase_order_id', $poId)->first()];
        
        return $po;
    }

    private function receivePurchaseOrder($po, $quantity)
    {
        $this->purchaseOrderModel->update($po->id, ['status' => 'received']);
        
        $product = $this->productModel->find($po->details[0]->product_id);
        $this->productModel->update($po->details[0]->product_id, [
            'stock' => $product->stock + $quantity
        ]);

        $this->stockMutationModel->insert([
            'product_id' => $po->details[0]->product_id,
            'type' => 'in',
            'quantity' => $quantity,
            'reference_type' => 'purchase',
            'reference_id' => $po->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getLastReturnId()
    {
        $result = $this->db->table('purchase_returns')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
        return $result ? $result->id : null;
    }
}