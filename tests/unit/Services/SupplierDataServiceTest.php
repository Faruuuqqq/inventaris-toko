<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\SupplierDataService;

class SupplierDataServiceTest extends CIUnitTestCase
{
    protected SupplierDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SupplierDataService();
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SupplierDataService::class, $this->service);
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
