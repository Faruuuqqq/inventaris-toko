<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\WarehouseModel;
use App\Models\StockMutationModel;

class ApiIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
        $this->seed(\App\Database\Seeds\UserSeeder::class);
        $this->seed(\App\Database\Seeds\CustomerSeeder::class);
    }

    /** @test */
    public function it_authenticates_via_api_and_returns_token()
    {
        // Arrange
        $credentials = [
            'email' => 'admin@example.com',
            'password' => 'password123'
        ];

        // Act: API Login
        $response = $this->post('api/v1/auth/login', $credentials, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        // Assert: Successful authentication
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('expires_in', $data);
        $this->assertNotNull($data['token']);
    }

    /** @test */
    public function it_validates_api_authentication_token()
    {
        // Arrange: Create valid token
        $token = $this->generateApiToken();

        // Act: Protected API call with token
        $response = $this->get('api/v1/products', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]);

        // Assert: Access granted
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    /** @test */
    public function it_rejects_api_calls_without_token()
    {
        // Act: API call without token
        $response = $this->get('api/v1/products', [
            'Accept' => 'application/json'
        ]);

        // Assert: Unauthorized
        $response->assertStatus(401);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Token required', $data['error']);
    }

    /** @test */
    public function it_handles_product_crud_via_api()
    {
        // Arrange: Get auth token
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // Act: Create product via API
        $productData = [
            'name' => 'API Test Product',
            'sku' => 'API-001',
            'price_buy' => 50000,
            'price_sell' => 75000,
            'stock' => 100
        ];

        $createResponse = $this->post('api/v1/products', $productData, $headers);
        $createResponse->assertStatus(201);
        
        $productId = $createResponse->getJSON()['data']['id'];

        // Act: Get product via API
        $getResponse = $this->get("api/v1/products/{$productId}", $headers);
        $getResponse->assertStatus(200);
        
        $product = $getResponse->getJSON()['data'];
        $this->assertEquals('API Test Product', $product['name']);

        // Act: Update product via API
        $updateData = ['price_sell' => 80000];
        $updateResponse = $this->put("api/v1/products/{$productId}", $updateData, $headers);
        $updateResponse->assertStatus(200);

        // Act: Delete product via API
        $deleteResponse = $this->delete("api/v1/products/{$productId}", $headers);
        $deleteResponse->assertStatus(200);
    }

    /** @test */
    public function it_handles_sales_transaction_via_api()
    {
        // Arrange
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // Act: Create sale via API
        $saleData = [
            'customer_id' => 1,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 5,
                    'price' => 75000
                ]
            ]
        ];

        $response = $this->post('api/v1/sales', $saleData, $headers);

        // Assert
        $response->assertStatus(201);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('sale_id', $data);
        $this->assertNotNull($data['sale_id']);

        // Verify stock was deducted
        $product = $this->productModel->find(1);
        $this->assertLessThan(100, $product->stock);
    }

    /** @test */
    public function it_handles_stock_mutations_via_api()
    {
        // Arrange
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // Act: Create stock transfer
        $transferData = [
            'product_id' => 1,
            'source_warehouse_id' => 1,
            'target_warehouse_id' => 2,
            'quantity' => 20,
            'notes' => 'API Test Transfer'
        ];

        $response = $this->post('api/v1/inventory/transfers', $transferData, $headers);

        // Assert
        $response->assertStatus(201);
        
        // Verify mutations created
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => 1,
            'type' => 'out',
            'quantity' => -20,
            'reference_type' => 'transfer_out'
        ]);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => 1,
            'type' => 'in',
            'quantity' => 20,
            'reference_type' => 'transfer_in'
        ]);
    }

    /** @test */
    public function it_handles_api_error_responses_consistently()
    {
        // Arrange
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // Act: Test validation error
        $response = $this->post('api/v1/products', [
            'name' => '', // Invalid: empty name
            'price_sell' => -1000 // Invalid: negative price
        ], $headers);

        // Assert: Consistent error format
        $response->assertStatus(422);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('messages', $data);
        $this->assertArrayHasKey('code', $data);
        $this->assertEquals('VALIDATION_ERROR', $data['code']);
    }

    /** @test */
    public function it_handles_api_pagination_and_filtering()
    {
        // Arrange
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ];

        // Create test data
        $this->createMultipleProducts();

        // Act: Paginated request
        $response = $this->get('api/v1/products?page=1&limit=5&search=test&sort=name&order=asc', $headers);

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        // Verify pagination structure
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);
        
        $pagination = $data['pagination'];
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('last_page', $pagination);
        $this->assertArrayHasKey('per_page', $pagination);
        $this->assertArrayHasKey('total', $pagination);

        // Verify filtering worked
        $this->assertLessThanOrEqual(5, count($data['data']));
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(5, $pagination['per_page']);
    }

    /** @test */
    public function it_handles_api_rate_limiting()
    {
        // Arrange
        $token = $this->generateApiToken();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ];

        // Act: Make multiple rapid requests
        for ($i = 1; $i <= 25; $i++) {
            $response = $this->get('api/v1/products', $headers);
            
            // Check for rate limiting after many requests
            if ($i > 20) {
                $this->assertEquals(429, $response->getStatusCode());
                break;
            }
        }
    }

    /** @test */
    public function it_generates_api_documentation_endpoints()
    {
        // Act: Get API documentation
        $response = $this->get('api/v1/docs');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('endpoints', $data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('base_url', $data);
        
        // Verify endpoint documentation
        $endpoints = $data['endpoints'];
        $this->assertArrayHasKey('/products', $endpoints);
        $this->assertArrayHasKey('/sales', $endpoints);
        $this->assertArrayHasKey('/purchases', $endpoints);
    }

    /** @test */
    public function it_handles_api_file_uploads()
    {
        // Arrange
        $token = $this->generateApiToken();
        
        // Mock file upload
        $_FILES = [
            'import_file' => [
                'name' => 'products.csv',
                'type' => 'text/csv',
                'size' => 1024,
                'tmp_name' => '/tmp/test_products.csv'
            ]
        ];

        // Act: Import products via API
        $response = $this->post('api/v1/products/import', [], [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'multipart/form-data'
        ]);

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();
        
        $this->assertArrayHasKey('imported', $data);
        $this->assertArrayHasKey('failed', $data);
        $this->assertArrayHasKey('total', $data);
    }

    private function generateApiToken()
    {
        // Create valid API token for testing
        $user = $this->userModel->first();
        $token = bin2hex(random_bytes(32));
        
        $this->db->table('api_tokens')->insert([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $token;
    }

    private function createMultipleProducts()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->db->table('products')->insert([
                'name' => $i % 2 == 0 ? 'Test Product ' . $i : 'API Test Product ' . $i,
                'sku' => 'TEST-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'price_buy' => 50000,
                'price_sell' => 75000,
                'stock' => 100,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}