<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\CustomerDataService;

class CustomerDataServiceTest extends CIUnitTestCase
{
    protected CustomerDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomerDataService();
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CustomerDataService::class, $this->service);
    }

    public function testGetIndexDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getIndexData'));
    }

    public function testGetCreateDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getCreateData'));
    }

    public function testGetEditDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getEditData'));
    }

    public function testGetDetailDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getDetailData'));
    }
}
