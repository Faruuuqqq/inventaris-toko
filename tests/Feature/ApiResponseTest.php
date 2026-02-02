<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * API Response Format Test
 * 
 * Tests that all AJAX/API endpoints return standardized JSON format
 * Expected format:
 * {
 *   "success": true|false,
 *   "message": "...",
 *   "data": {...} or null,
 *   "errors": {...} (only on error)
 * }
 */
class ApiResponseTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $refresh = false;

    /**
     * Helper: Login as admin user
     */
    protected function loginAsAdmin()
    {
        // Create session data
        $session = service('session');
        $session->set([
            'user_id' => 1,
            'username' => 'admin',
            'role' => 'OWNER',
            'isLoggedIn' => true
        ]);
    }

    /**
     * Test DeliveryNote getInvoiceItems response format
     */
    public function testDeliveryNoteGetInvoiceItemsFormat()
    {
        $this->loginAsAdmin();

        $result = $this->get('transactions/delivery-note/getInvoiceItems/1');
        
        // Should return JSON
        $this->assertTrue(
            $result->getHeaderLine('Content-Type') === 'application/json' ||
            strpos($result->getHeaderLine('Content-Type'), 'application/json') !== false,
            'Should return JSON content type'
        );

        // Parse JSON
        $json = json_decode($result->getJSON(), true);
        
        // Should have standard keys
        $this->assertArrayHasKey('success', $json, 'Response should have success key');
        
        if ($json['success']) {
            $this->assertArrayHasKey('data', $json, 'Success response should have data key');
        } else {
            $this->assertArrayHasKey('message', $json, 'Error response should have message key');
        }
    }

    /**
     * Test empty response format
     */
    public function testEmptyResponseFormat()
    {
        $this->loginAsAdmin();

        // getList endpoints should return empty array or data
        $result = $this->get('master/customers/getList');
        
        $json = $result->getJSON();
        $this->assertTrue(
            is_array($json) || (is_object($json) && property_exists($json, 'success')),
            'Response should be array or standard object'
        );
    }

    /**
     * Test validation error format
     */
    public function testValidationErrorFormat()
    {
        $this->loginAsAdmin();

        // POST to delivery note without required fields
        $result = $this->post('transactions/delivery-note/store', []);
        
        // Should redirect with errors
        $this->assertTrue(
            $result->isRedirect(),
            'Invalid submission should redirect'
        );
        
        // Check session has errors
        $session = session();
        $errors = $session->getFlashdata('errors');
        
        if ($errors) {
            $this->assertIsArray($errors, 'Errors should be an array');
        }
    }
}
