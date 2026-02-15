<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\StockMutationModel;

class InventoryIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\WarehouseSeeder::class);
        $this->seed(\App\Database\Seeds\UserSeeder::class);
    }

    /** @test */
    public function it_can_transfer_stock_between_warehouses()
    {
        // Arrange: Get data
        $product = $this->productModel->first();
        $sourceWarehouse = $this->warehouseModel->first();
        $targetWarehouse = $this->warehouseModel->find($sourceWarehouse->id + 1);
        
        // Set initial stock
        $this->productModel->update($product->id, ['stock' => 100]);

        // Act: Transfer stock
        $transferData = [
            'product_id' => $product->id,
            'source_warehouse_id' => $sourceWarehouse->id,
            'target_warehouse_id' => $targetWarehouse->id,
            'quantity' => 30,
            'transfer_date' => date('Y-m-d'),
            'notes' => 'Stock transfer test'
        ];

        $response = $this->post('inventory/transfers', $transferData);

        // Assert
        $response->assertStatus(201);

        // Assert: Stock mutations created
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => -30,
            'warehouse_id' => $sourceWarehouse->id,
            'reference_type' => 'transfer_out'
        ]);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 30,
            'warehouse_id' => $targetWarehouse->id,
            'reference_type' => 'transfer_in'
        ]);
    }

    /** @test */
    public function it_can_perform_stock_adjustment_with_reason()
    {
        // Arrange
        $product = $this->productModel->first();
        $this->productModel->update($product->id, ['stock' => 100]);

        // Act: Adjustment (stock loss)
        $adjustmentData = [
            'product_id' => $product->id,
            'adjustment_type' => 'loss',
            'quantity' => 5,
            'adjustment_date' => date('Y-m-d'),
            'reason' => 'Damaged goods found during inventory check'
        ];

        $response = $this->post('inventory/adjustments', $adjustmentData);

        // Assert
        $response->assertStatus(201);

        // Assert: Stock adjusted
        $updatedProduct = $this->productModel->find($product->id);
        $this->assertEquals(95, $updatedProduct->stock);

        // Assert: Mutation recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => -5,
            'reference_type' => 'stock_adjustment',
            'notes' => 'Damaged goods found during inventory check'
        ]);
    }

    /** @test */
    public function it_prevents_negative_stock_during_operations()
    {
        // Arrange
        $product = $this->productModel->first();
        $this->productModel->update($product->id, ['stock' => 10]);

        // Act: Try to transfer more than available
        $transferData = [
            'product_id' => $product->id,
            'quantity' => 15, // More than available
            'source_warehouse_id' => $this->warehouseModel->first()->id,
            'target_warehouse_id' => $this->warehouseModel->find(2)->id,
        ];

        $response = $this->post('inventory/transfers', $transferData);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
        $this->assertStringContainsString('Insufficient stock', $response->getJSON());

        // Assert: No changes made
        $this->assertEquals(10, $this->productModel->find($product->id)->stock);
    }

    /** @test */
    public function it_calculates_inventory_value_correctly()
    {
        // Arrange: Create multiple products with stock
        $products = [
            ['stock' => 100, 'price_buy' => 50000],
            ['stock' => 50, 'price_buy' => 75000],
            ['stock' => 25, 'price_buy' => 100000]
        ];

        $totalValue = 0;
        foreach ($products as $i => $data) {
            $productId = $this->productModel->insert([
                'name' => "Test Product " . ($i + 1),
                'sku' => "TEST-" . ($i + 1),
                'stock' => $data['stock'],
                'price_buy' => $data['price_buy'],
                'price_sell' => $data['price_buy'] * 1.3
            ]);
            $totalValue += $data['stock'] * $data['price_buy'];
        }

        // Act: Get inventory valuation
        $response = $this->get('inventory/valuation');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('total_value', $data);
        $this->assertArrayHasKey('total_items', $data);
        $this->assertArrayHasKey('total_quantity', $data);
        
        $this->assertEquals($totalValue, $data['total_value']);
        $this->assertEquals(count($products), $data['total_items']);
    }

    /** @test */
    public function it_generates_low_stock_report()
    {
        // Arrange: Create products with low stock
        $products = $this->productModel->findAll();
        foreach ($products as $product) {
            if ($product->id % 3 == 0) {
                $this->productModel->update($product->id, ['stock' => 5, 'min_stock' => 10]);
            }
        }

        // Act: Get low stock report
        $response = $this->get('inventory/reports/low-stock');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('low_stock_items', $data);
        $this->assertNotEmpty($data['low_stock_items']);
        
        foreach ($data['low_stock_items'] as $item) {
            $this->assertLessThan($item['min_stock'], $item['current_stock']);
            $this->assertGreaterThan(0, $item['needed_stock']);
        }
    }

    /** @test */
    public function it_tracks_complete_stock_history()
    {
        // Arrange
        $product = $this->productModel->first();
        $this->productModel->update($product->id, ['stock' => 100]);

        // Act: Create various stock operations
        // 1. Initial purchase
        $this->createStockMutation($product->id, 'in', 50, 'purchase', 1);
        
        // 2. Sale
        $this->createStockMutation($product->id, 'out', -30, 'sale', 1);
        
        // 3. Transfer out
        $this->createStockMutation($product->id, 'out', -20, 'transfer_out', 1);
        
        // 4. Adjustment
        $this->createStockMutation($product->id, 'adjustment', 5, 'stock_adjustment', null);

        // Get history
        $response = $this->get("inventory/history/{$product->id}");

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('mutations', $data);
        $this->assertArrayHasKey('summary', $data);
        
        $this->assertCount(4, $data['mutations']);
        $this->assertEquals(105, $data['summary']['current_stock']); // 100+50-30-20+5
        
        // Verify operation types
        $operationTypes = array_column($data['mutations'], 'type');
        $this->assertContains('in', $operationTypes);
        $this->assertContains('out', $operationTypes);
        $this->assertContains('adjustment', $operationTypes);
    }

    private function createStockMutation($productId, $type, $quantity, $referenceType, $referenceId)
    {
        return $this->stockMutationModel->insert([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}