<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\ProductDataService;

class ProductDataServiceTest extends CIUnitTestCase
{
    protected ProductDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductDataService();
    }

    /**
     * Test that ProductDataService can be instantiated
     */
    public function testProductDataServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ProductDataService::class, $this->service);
    }

    /**
     * Test that getIndexData method exists and is callable
     */
    public function testGetIndexDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getIndexData'));
    }

    /**
     * Test that getCreateData method exists and is callable
     */
    public function testGetCreateDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getCreateData'));
    }

    /**
     * Test that getEditData method exists and is callable
     */
    public function testGetEditDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getEditData'));
    }

    /**
     * Test that getDetailData method exists and is callable
     */
    public function testGetDetailDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getDetailData'));
    }
}
