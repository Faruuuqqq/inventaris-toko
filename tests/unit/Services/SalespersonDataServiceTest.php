<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\SalespersonDataService;

class SalespersonDataServiceTest extends CIUnitTestCase
{
    protected SalespersonDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SalespersonDataService();
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SalespersonDataService::class, $this->service);
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
