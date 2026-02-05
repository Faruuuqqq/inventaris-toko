<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * CRUD Operations Test
 * 
 * Tests all CREATE, READ, UPDATE, DELETE operations for:
 * - Customers
 * - Suppliers
 * - Warehouses (Gudang)
 * - Salespersons
 * - Products
 */
class CRUDOperationsTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $refresh = false;

    protected function loginAsAdmin()
    {
        $session = service('session');
        $session->set([
            'user_id' => 1,
            'username' => 'admin',
            'role' => 'OWNER',
            'isLoggedIn' => true
        ]);
    }

    // ========== CUSTOMERS CRUD TESTS ==========

    /**
     * Test: CREATE Customer with valid data
     */
    public function testCreateCustomerSuccess()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/customers', [
            'code' => 'CUST-TEST-001',
            'name' => 'PT Test Customer',
            'phone' => '081234567890',
            'credit_limit' => 5000000,
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(201);
        $result = json_decode($response->getBody(), true);
        $this->assertEquals('Data pelanggan berhasil ditambahkan', $result['message']);
    }

    /**
     * Test: CREATE Customer with validation error (missing name)
     */
    public function testCreateCustomerValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/customers', [
            'code' => 'CUST-TEST-002',
            // 'name' => missing required field
            'phone' => '081234567890',
            'credit_limit' => 5000000,
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /**
     * Test: CREATE Customer with invalid credit_limit (should be numeric)
     */
    public function testCreateCustomerInvalidCreditLimit()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/customers', [
            'code' => 'CUST-TEST-003',
            'name' => 'PT Test Customer',
            'phone' => '081234567890',
            'credit_limit' => 'not-a-number', // Invalid
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('credit_limit', $result['errors']);
    }

    /**
     * Test: READ Customer (GET index)
     */
    public function testReadCustomersIndex()
    {
        $this->loginAsAdmin();

        $response = $this->get('master/customers');
        
        $response->assertStatus(200);
        $this->assertStringContainsString('Daftar Pelanggan', $response->getBody());
    }

    /**
     * Test: UPDATE Customer with valid data
     */
    public function testUpdateCustomerSuccess()
    {
        $this->loginAsAdmin();

        // First create a customer
        $createResponse = $this->post('master/customers', [
            'code' => 'CUST-UPDATE-001',
            'name' => 'PT Original Name',
            'phone' => '081234567890',
            'credit_limit' => 5000000,
            'address' => 'Original Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $customerId = $createResult['id'] ?? 1;

        // Then update it
        $updateResponse = $this->post("master/customers/{$customerId}", [
            '_method' => 'PUT',
            'code' => 'CUST-UPDATE-001',
            'name' => 'PT Updated Name',
            'phone' => '082345678901',
            'credit_limit' => 7000000,
            'address' => 'Updated Address'
        ]);

        $updateResponse->assertStatus(200);
        $result = json_decode($updateResponse->getBody(), true);
        $this->assertEquals('Data pelanggan berhasil diperbarui', $result['message']);
    }

    /**
     * Test: UPDATE Customer with validation error
     */
    public function testUpdateCustomerValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/customers/999', [
            '_method' => 'PUT',
            'code' => 'CUST-UPDATE-999',
            'name' => '', // Invalid - empty name
            'phone' => '081234567890',
            'credit_limit' => 5000000,
            'address' => 'Address'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test: DELETE Customer
     */
    public function testDeleteCustomerSuccess()
    {
        $this->loginAsAdmin();

        // First create a customer
        $createResponse = $this->post('master/customers', [
            'code' => 'CUST-DELETE-001',
            'name' => 'PT Delete Me',
            'phone' => '081234567890',
            'credit_limit' => 5000000,
            'address' => 'Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $customerId = $createResult['id'] ?? 1;

        // Then delete it
        $deleteResponse = $this->delete("master/customers/{$customerId}");

        $deleteResponse->assertStatus(200);
        $result = json_decode($deleteResponse->getBody(), true);
        $this->assertEquals('Data pelanggan berhasil dihapus', $result['message']);
    }

    // ========== SUPPLIERS CRUD TESTS ==========

    /**
     * Test: CREATE Supplier with valid data
     */
    public function testCreateSupplierSuccess()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/suppliers', [
            'code' => 'SUP-TEST-001',
            'name' => 'PT Test Supplier',
            'phone' => '081234567890',
            'payment_terms' => '30',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(201);
        $result = json_decode($response->getBody(), true);
        $this->assertEquals('Data pemasok berhasil ditambahkan', $result['message']);
    }

    /**
     * Test: CREATE Supplier with validation error (missing name)
     */
    public function testCreateSupplierValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/suppliers', [
            'code' => 'SUP-TEST-002',
            // 'name' => missing required field
            'phone' => '081234567890',
            'payment_terms' => '30',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /**
     * Test: UPDATE Supplier with valid data
     */
    public function testUpdateSupplierSuccess()
    {
        $this->loginAsAdmin();

        // First create a supplier
        $createResponse = $this->post('master/suppliers', [
            'code' => 'SUP-UPDATE-001',
            'name' => 'PT Original Supplier',
            'phone' => '081234567890',
            'payment_terms' => '30',
            'address' => 'Original Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $supplierId = $createResult['id'] ?? 1;

        // Then update it
        $updateResponse = $this->post("master/suppliers/{$supplierId}", [
            '_method' => 'PUT',
            'code' => 'SUP-UPDATE-001',
            'name' => 'PT Updated Supplier',
            'phone' => '082345678901',
            'payment_terms' => '60',
            'address' => 'Updated Address'
        ]);

        $updateResponse->assertStatus(200);
        $result = json_decode($updateResponse->getBody(), true);
        $this->assertEquals('Data pemasok berhasil diperbarui', $result['message']);
    }

    /**
     * Test: DELETE Supplier
     */
    public function testDeleteSupplierSuccess()
    {
        $this->loginAsAdmin();

        // First create a supplier
        $createResponse = $this->post('master/suppliers', [
            'code' => 'SUP-DELETE-001',
            'name' => 'PT Delete Me',
            'phone' => '081234567890',
            'payment_terms' => '30',
            'address' => 'Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $supplierId = $createResult['id'] ?? 1;

        // Then delete it
        $deleteResponse = $this->delete("master/suppliers/{$supplierId}");

        $deleteResponse->assertStatus(200);
        $result = json_decode($deleteResponse->getBody(), true);
        $this->assertEquals('Data pemasok berhasil dihapus', $result['message']);
    }

    // ========== WAREHOUSES CRUD TESTS ==========

    /**
     * Test: CREATE Warehouse with valid data
     */
    public function testCreateWarehouseSuccess()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/warehouses', [
            'code' => 'GDG-TEST-001',
            'name' => 'Gudang Test',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(201);
        $result = json_decode($response->getBody(), true);
        $this->assertEquals('Data gudang berhasil ditambahkan', $result['message']);
    }

    /**
     * Test: CREATE Warehouse with validation error (missing name)
     */
    public function testCreateWarehouseValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/warehouses', [
            'code' => 'GDG-TEST-002',
            // 'name' => missing required field
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /**
     * Test: UPDATE Warehouse with valid data
     */
    public function testUpdateWarehouseSuccess()
    {
        $this->loginAsAdmin();

        // First create a warehouse
        $createResponse = $this->post('master/warehouses', [
            'code' => 'GDG-UPDATE-001',
            'name' => 'Gudang Original',
            'phone' => '081234567890',
            'address' => 'Original Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $warehouseId = $createResult['id'] ?? 1;

        // Then update it
        $updateResponse = $this->post("master/warehouses/{$warehouseId}", [
            '_method' => 'PUT',
            'code' => 'GDG-UPDATE-001',
            'name' => 'Gudang Updated',
            'phone' => '082345678901',
            'address' => 'Updated Address'
        ]);

        $updateResponse->assertStatus(200);
        $result = json_decode($updateResponse->getBody(), true);
        $this->assertEquals('Data gudang berhasil diperbarui', $result['message']);
    }

    /**
     * Test: DELETE Warehouse
     */
    public function testDeleteWarehouseSuccess()
    {
        $this->loginAsAdmin();

        // First create a warehouse
        $createResponse = $this->post('master/warehouses', [
            'code' => 'GDG-DELETE-001',
            'name' => 'Gudang Delete Me',
            'phone' => '081234567890',
            'address' => 'Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $warehouseId = $createResult['id'] ?? 1;

        // Then delete it
        $deleteResponse = $this->delete("master/warehouses/{$warehouseId}");

        $deleteResponse->assertStatus(200);
        $result = json_decode($deleteResponse->getBody(), true);
        $this->assertEquals('Data gudang berhasil dihapus', $result['message']);
    }

    // ========== SALESPERSONS CRUD TESTS ==========

    /**
     * Test: CREATE Salesperson with valid data
     */
    public function testCreateSalespersonSuccess()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/salespersons', [
            'code' => 'SALES-TEST-001',
            'name' => 'John Salesman',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(201);
        $result = json_decode($response->getBody(), true);
        $this->assertEquals('Data salesman berhasil ditambahkan', $result['message']);
    }

    /**
     * Test: CREATE Salesperson with validation error (missing name)
     */
    public function testCreateSalespersonValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/salespersons', [
            'code' => 'SALES-TEST-002',
            // 'name' => missing required field
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123'
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /**
     * Test: UPDATE Salesperson with valid data
     */
    public function testUpdateSalespersonSuccess()
    {
        $this->loginAsAdmin();

        // First create a salesperson
        $createResponse = $this->post('master/salespersons', [
            'code' => 'SALES-UPDATE-001',
            'name' => 'John Original',
            'phone' => '081234567890',
            'address' => 'Original Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $salespersonId = $createResult['id'] ?? 1;

        // Then update it
        $updateResponse = $this->post("master/salespersons/{$salespersonId}", [
            '_method' => 'PUT',
            'code' => 'SALES-UPDATE-001',
            'name' => 'John Updated',
            'phone' => '082345678901',
            'address' => 'Updated Address'
        ]);

        $updateResponse->assertStatus(200);
        $result = json_decode($updateResponse->getBody(), true);
        $this->assertEquals('Data salesman berhasil diperbarui', $result['message']);
    }

    /**
     * Test: DELETE Salesperson
     */
    public function testDeleteSalespersonSuccess()
    {
        $this->loginAsAdmin();

        // First create a salesperson
        $createResponse = $this->post('master/salespersons', [
            'code' => 'SALES-DELETE-001',
            'name' => 'John Delete Me',
            'phone' => '081234567890',
            'address' => 'Address'
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $salespersonId = $createResult['id'] ?? 1;

        // Then delete it
        $deleteResponse = $this->delete("master/salespersons/{$salespersonId}");

        $deleteResponse->assertStatus(200);
        $result = json_decode($deleteResponse->getBody(), true);
        $this->assertEquals('Data salesman berhasil dihapus', $result['message']);
    }

    // ========== PRODUCTS CRUD TESTS ==========

    /**
     * Test: CREATE Product with valid data
     */
    public function testCreateProductSuccess()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/products', [
            'sku' => 'PRD-TEST-001',
            'name' => 'Test Product',
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $response->assertStatus(201);
        $result = json_decode($response->getBody(), true);
        $this->assertEquals('Data produk berhasil ditambahkan', $result['message']);
    }

    /**
     * Test: CREATE Product with validation error (missing name)
     */
    public function testCreateProductValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/products', [
            'sku' => 'PRD-TEST-002',
            // 'name' => missing required field
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    /**
     * Test: CREATE Product with invalid price_buy (should be numeric)
     */
    public function testCreateProductInvalidPrice()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/products', [
            'sku' => 'PRD-TEST-003',
            'name' => 'Test Product',
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 'not-a-number', // Invalid
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('price_buy', $result['errors']);
    }

    /**
     * Test: UPDATE Product with valid data
     */
    public function testUpdateProductSuccess()
    {
        $this->loginAsAdmin();

        // First create a product
        $createResponse = $this->post('master/products', [
            'sku' => 'PRD-UPDATE-001',
            'name' => 'Original Product',
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $productId = $createResult['id'] ?? 1;

        // Then update it
        $updateResponse = $this->post("master/products/{$productId}", [
            '_method' => 'PUT',
            'sku' => 'PRD-UPDATE-001',
            'name' => 'Updated Product',
            'category_id' => 1,
            'unit' => 'BOX',
            'price_buy' => 60000,
            'price_sell' => 85000,
            'min_stock_alert' => 15
        ]);

        $updateResponse->assertStatus(200);
        $result = json_decode($updateResponse->getBody(), true);
        $this->assertEquals('Data produk berhasil diperbarui', $result['message']);
    }

    /**
     * Test: UPDATE Product with validation error
     */
    public function testUpdateProductValidationError()
    {
        $this->loginAsAdmin();

        $response = $this->post('master/products/999', [
            '_method' => 'PUT',
            'sku' => 'PRD-UPDATE-999',
            'name' => '', // Invalid - empty name
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $response->assertStatus(422);
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('errors', $result);
    }

    /**
     * Test: DELETE Product
     */
    public function testDeleteProductSuccess()
    {
        $this->loginAsAdmin();

        // First create a product
        $createResponse = $this->post('master/products', [
            'sku' => 'PRD-DELETE-001',
            'name' => 'Product Delete Me',
            'category_id' => 1,
            'unit' => 'PCS',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'min_stock_alert' => 10
        ]);

        $createResult = json_decode($createResponse->getBody(), true);
        $productId = $createResult['id'] ?? 1;

        // Then delete it
        $deleteResponse = $this->delete("master/products/{$productId}");

        $deleteResponse->assertStatus(200);
        $result = json_decode($deleteResponse->getBody(), true);
        $this->assertEquals('Data produk berhasil dihapus', $result['message']);
    }
}
