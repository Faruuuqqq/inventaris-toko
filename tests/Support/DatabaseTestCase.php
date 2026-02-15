<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Base class for feature tests that need database setup
 */
abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $db;
    protected $productModel;
    protected $customerModel;
    protected $supplierModel;
    protected $userModel;
    protected $saleModel;
    protected $purchaseOrderModel;
    protected $warehouseModel;
    protected $stockMutationModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->db = \Config\Database::connect();
        
        // Load models
        $this->productModel = new \App\Models\ProductModel();
        $this->customerModel = new \App\Models\CustomerModel();
        $this->supplierModel = new \App\Models\SupplierModel();
        $this->userModel = new \App\Models\UserModel();
        $this->saleModel = new \App\Models\SaleModel();
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->stockMutationModel = new \App\Models\StockMutationModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up test data
        $this->db->table('sales')->truncate();
        $this->db->table('sale_details')->truncate();
        $this->db->table('purchase_orders')->truncate();
        $this->db->table('purchase_order_details')->truncate();
        $this->db->table('stock_mutations')->truncate();
        $this->db->table('journal_entries')->truncate();
        $this->db->table('customer_receivables')->truncate();
        $this->db->table('supplier_payables')->truncate();
        $this->db->table('customer_payments')->truncate();
        $this->db->table('supplier_payments')->truncate();
    }

    protected function createTestUser($overrides = [])
    {
        $userData = array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_active' => 1
        ], $overrides);

        return $this->userModel->insert($userData);
    }

    protected function createTestCustomer($overrides = [])
    {
        $customerData = array_merge([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'credit_limit' => 10000000,
            'credit_limit_used' => 0,
            'is_active' => 1
        ], $overrides);

        return $this->customerModel->insert($customerData);
    }

    protected function createTestSupplier($overrides = [])
    {
        $supplierData = array_merge([
            'name' => 'Test Supplier',
            'email' => 'supplier@example.com',
            'phone' => '08123456789',
            'address' => 'Supplier Address',
            'credit_limit' => 50000000,
            'credit_limit_used' => 0,
            'is_active' => 1
        ], $overrides);

        return $this->supplierModel->insert($supplierData);
    }

    protected function createTestProduct($overrides = [])
    {
        $productData = array_merge([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'category_id' => 1,
            'price_buy' => 50000,
            'price_sell' => 75000,
            'stock' => 100,
            'min_stock' => 10,
            'unit' => 'pcs',
            'is_active' => 1
        ], $overrides);

        return $this->productModel->insert($productData);
    }

    protected function createTestSale($overrides = [])
    {
        $customer = $this->customerModel->first();
        $product = $this->productModel->first();

        $saleData = array_merge([
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'total_amount' => $product->price_sell * 2,
            'paid_amount' => $product->price_sell * 2,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ], $overrides);

        $saleId = $this->saleModel->insert($saleData);

        // Create sale details
        $this->db->table('sale_details')->insert([
            'sale_id' => $saleId,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price_sell,
            'subtotal' => $product->price_sell * 2
        ]);

        return $saleId;
    }

    protected function createTestWarehouse($overrides = [])
    {
        $warehouseData = array_merge([
            'name' => 'Test Warehouse',
            'code' => 'WH-001',
            'address' => 'Warehouse Address',
            'capacity' => 10000,
            'is_active' => 1
        ], $overrides);

return $this->warehouseModel->insert($warehouseData);
    }
    
    /**
     * Helper function to login a user for testing
     */
    protected function login($email, $password)
    {
        // Find user
        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            throw new \Exception("User not found: $email");
        }
        
        // Verify password
        if (!password_verify($password, $user->password_hash)) {
            throw new \Exception("Invalid password for user: $email");
        }
        
        // Set session data
        session()->set([
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'fullname' => $user->fullname,
            'role' => $user->role,
            'logged_in' => true
        ]);
    }
}

    protected function assertDatabaseHas($table, array $data)
    {
        $result = $this->db->table($table)->where($data)->get()->getRow();
        $this->assertNotNull($result, "Failed to find record in {$table}");
    }

    protected function assertDatabaseMissing($table, array $data)
    {
        $result = $this->db->table($table)->where($data)->get()->getRow();
        $this->assertNull($result, "Unexpectedly found record in {$table}");
    }

    protected function assertJsonValidationErrors($response, array $expectedFields = null)
    {
        $this->assertEquals(422, $response->getStatusCode());
        
        if ($expectedFields !== null) {
            $json = $response->getJSON();
            foreach ($expectedFields as $field) {
                $this->assertArrayHasKey($field, $json['errors'] ?? []);
            }
        }
    }

    protected function get($uri, array $headers = [])
    {
        return $this->call('get', $uri, $headers);
    }

    protected function post($uri, array $data = [], array $headers = [])
    {
        return $this->call('post', $uri, $data, $headers);
    }

    protected function put($uri, array $data = [], array $headers = [])
    {
        return $this->call('put', $uri, $data, $headers);
    }

    protected function delete($uri, array $headers = [])
    {
        return $this->call('delete', $uri, $headers);
    }
}