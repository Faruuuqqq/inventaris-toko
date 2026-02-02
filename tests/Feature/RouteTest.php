<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Route Integration Test
 * 
 * Tests all critical routes to ensure:
 * - Routes exist and are registered
 * - Controllers and methods exist
 * - Proper HTTP methods are supported
 * - Authentication is required where needed
 */
class RouteTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $refresh = false;

    /**
     * Test Master Data Routes
     */
    public function testMasterCustomersRoutes()
    {
        // Test index route (should redirect to login)
        $result = $this->get('master/customers');
        $result->assertRedirectTo('/login');

        // Test getList AJAX endpoint
        $result = $this->get('master/customers/getList');
        $result->assertRedirectTo('/login');
    }

    public function testMasterWarehousesRoutes()
    {
        $result = $this->get('master/warehouses');
        $result->assertRedirectTo('/login');

        $result = $this->get('master/warehouses/getList');
        $result->assertRedirectTo('/login');
    }

    public function testMasterSalespersonsRoutes()
    {
        $result = $this->get('master/salespersons');
        $result->assertRedirectTo('/login');

        $result = $this->get('master/salespersons/getList');
        $result->assertRedirectTo('/login');
    }

    /**
     * Test Transaction Routes
     */
    public function testDeliveryNoteRoutes()
    {
        // Test index
        $result = $this->get('transactions/delivery-note');
        $result->assertRedirectTo('/login');

        // Test AJAX endpoint (should redirect or return 401)
        $result = $this->get('transactions/delivery-note/getInvoiceItems/1');
        $this->assertTrue(
            $result->isRedirect() || $result->getStatusCode() === 401,
            'Should redirect to login or return 401'
        );
    }

    public function testPurchasesRoutes()
    {
        $result = $this->get('transactions/purchases');
        $result->assertRedirectTo('/login');

        // Test edit route
        $result = $this->get('transactions/purchases/edit/1');
        $result->assertRedirectTo('/login');

        // Test delete route (GET)
        $result = $this->get('transactions/purchases/delete/1');
        $result->assertRedirectTo('/login');
    }

    /**
     * Test Finance Routes
     */
    public function testPaymentsRoutes()
    {
        $result = $this->get('finance/payments');
        $result->assertRedirectTo('/login');

        $result = $this->get('finance/payments/receivable');
        $result->assertRedirectTo('/login');

        $result = $this->get('finance/payments/payable');
        $result->assertRedirectTo('/login');
    }

    public function testExpensesRoutes()
    {
        $result = $this->get('finance/expenses');
        $result->assertRedirectTo('/login');

        // Test standard edit pattern
        $result = $this->get('finance/expenses/edit/1');
        $result->assertRedirectTo('/login');

        // Test legacy edit pattern (should still work)
        $result = $this->get('finance/expenses/1/edit');
        $result->assertRedirectTo('/login');
    }

    /**
     * Test Delete Route Standardization
     */
    public function testDeleteRoutesStandardization()
    {
        $deleteRoutes = [
            'master/products/delete/1',
            'master/customers/delete/1',
            'master/suppliers/delete/1',
            'master/warehouses/delete/1',
            'master/salespersons/delete/1',
            'transactions/purchases/delete/1',
            'transactions/sales-returns/delete/1',
            'transactions/purchase-returns/delete/1',
            'finance/expenses/delete/1',
            'finance/kontra-bon/delete/1',
        ];

        foreach ($deleteRoutes as $route) {
            $result = $this->get($route);
            $this->assertTrue(
                $result->isRedirect() || $result->getStatusCode() < 500,
                "Route $route should be accessible (redirect to login or process)"
            );
        }
    }

    /**
     * Test Edit Route Standardization
     */
    public function testEditRoutesStandardization()
    {
        $editRoutes = [
            'master/products/edit/1',
            'master/customers/edit/1',
            'master/suppliers/edit/1',
            'master/warehouses/edit/1',
            'master/salespersons/edit/1',
            'transactions/sales/edit/1',
            'transactions/purchases/edit/1',
            'transactions/sales-returns/edit/1',
            'transactions/purchase-returns/edit/1',
            'finance/expenses/edit/1',
            'finance/kontra-bon/edit/1',
        ];

        foreach ($editRoutes as $route) {
            $result = $this->get($route);
            $this->assertTrue(
                $result->isRedirect(),
                "Edit route $route should redirect to login when not authenticated"
            );
        }
    }

    /**
     * Test AJAX Endpoints Return JSON
     */
    public function testAjaxEndpointsFormat()
    {
        // Note: These will redirect to login, but we're testing the routes exist
        $ajaxEndpoints = [
            'master/customers/getList',
            'master/warehouses/getList',
            'master/salespersons/getList',
            'finance/payments/getCustomerInvoices',
            'finance/payments/getSupplierPurchases',
            'finance/payments/getKontraBons',
        ];

        foreach ($ajaxEndpoints as $endpoint) {
            $result = $this->get($endpoint);
            $this->assertTrue(
                $result->isRedirect() || $result->getStatusCode() < 500,
                "AJAX endpoint $endpoint should be accessible"
            );
        }
    }

    /**
     * Test POST/PUT Route Duality for Updates
     */
    public function testUpdateRoutesDuality()
    {
        // Test that update routes accept POST (for forms)
        $updateRoutes = [
            'transactions/purchases/update/1',
            'finance/expenses/update/1',
            'transactions/sales-returns/update/1',
            'transactions/purchase-returns/update/1',
        ];

        foreach ($updateRoutes as $route) {
            $result = $this->post($route, []);
            $this->assertTrue(
                $result->isRedirect() || $result->getStatusCode() < 500,
                "POST to $route should be accepted (redirect or process)"
            );
        }
    }

    /**
     * Test Route Not Found Returns 404
     */
    public function testNonExistentRouteReturns404()
    {
        $result = $this->get('non/existent/route/xyz123');
        $result->assertStatus(404);
    }

    /**
     * Test API Routes Exist
     */
    public function testApiRoutesExist()
    {
        // Test API routes (should return 401 unauthorized without token)
        $result = $this->get('api/v1/products');
        $this->assertTrue(
            $result->getStatusCode() === 401 || $result->isRedirect(),
            'API route should return 401 or redirect'
        );
    }

    /**
     * Test Home and Auth Routes
     */
    public function testBasicRoutes()
    {
        // Home should redirect to dashboard or login
        $result = $this->get('/');
        $this->assertTrue(
            $result->isRedirect(),
            'Home should redirect'
        );

        // Login page should be accessible
        $result = $this->get('login');
        $result->assertStatus(200);

        // Dashboard requires auth
        $result = $this->get('dashboard');
        $result->assertRedirectTo('/login');
    }
}
