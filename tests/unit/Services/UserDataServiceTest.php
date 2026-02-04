<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\UserDataService;

class UserDataServiceTest extends CIUnitTestCase
{
    protected UserDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserDataService();
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserDataService::class, $this->service);
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

    public function testGetPaginatedDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getPaginatedData'));
    }

    public function testGetPaginatedDataReturnsArrayWithPagination(): void
    {
        $data = $this->service->getPaginatedData(1, 20);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertArrayHasKey('users', $data);
    }

    public function testGetPaginatedDataPaginationStructure(): void
    {
        $data = $this->service->getPaginatedData(1, 20);
        $pagination = $data['pagination'];
        
        $requiredKeys = ['currentPage', 'totalPages', 'perPage', 'total', 'pages', 'showPagination'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $pagination, "Missing key: $key");
        }
    }
}
