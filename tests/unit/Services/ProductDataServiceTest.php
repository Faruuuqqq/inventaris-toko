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

    /**
     * Test that getPaginatedData method exists and is callable
     */
    public function testGetPaginatedDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getPaginatedData'));
    }

    /**
     * Test getPaginatedData returns array with pagination key
     */
    public function testGetPaginatedDataReturnsArrayWithPaginationKey(): void
    {
        $data = $this->service->getPaginatedData(1, 20);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertArrayHasKey('products', $data);
    }

    /**
     * Test getPaginatedData with default parameters
     */
    public function testGetPaginatedDataWithDefaultParameters(): void
    {
        $data = $this->service->getPaginatedData();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('pagination', $data);
        $pagination = $data['pagination'];
        $this->assertEquals(1, $pagination['currentPage']);
        $this->assertEquals(20, $pagination['perPage']);
    }

    /**
     * Test getPaginatedData with custom page and perPage
     */
    public function testGetPaginatedDataWithCustomParameters(): void
    {
        $data = $this->service->getPaginatedData(2, 10);
        $pagination = $data['pagination'];
        $this->assertEquals(2, $pagination['currentPage']);
        $this->assertEquals(10, $pagination['perPage']);
    }

    /**
     * Test getPaginatedData pagination structure
     */
    public function testGetPaginatedDataPaginationStructure(): void
    {
        $data = $this->service->getPaginatedData(1, 20);
        $pagination = $data['pagination'];
        
        // Check all required pagination keys exist
        $requiredKeys = [
            'currentPage',
            'totalPages',
            'perPage',
            'total',
            'hasNextPage',
            'hasPreviousPage',
            'pages',
            'from',
            'to',
            'showPagination'
        ];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $pagination, "Missing key: $key");
        }
    }

    /**
     * Test that getExportData method exists
     */
    public function testGetExportDataMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getExportData'));
    }

    /**
     * Test getExportData returns array
     */
    public function testGetExportDataReturnsArray(): void
    {
        $data = $this->service->getExportData();
        $this->assertIsArray($data);
    }

    /**
     * Test getExportData with empty filters
     */
    public function testGetExportDataWithEmptyFilters(): void
    {
        $data = $this->service->getExportData([]);
        $this->assertIsArray($data);
    }

    /**
     * Test getExportData returns proper structure
     * Each item should have export-required fields
     */
    public function testGetExportDataReturnsProperStructure(): void
    {
        $data = $this->service->getExportData();
        
        // If there are products, check structure
        if (!empty($data)) {
            $firstProduct = $data[0];
            
            // Check expected fields for export
            $expectedFields = [
                'sku',
                'name',
                'category_name',
                'unit',
                'purchase_price',
                'selling_price',
                'stock'
            ];
            
            foreach ($expectedFields as $field) {
                $this->assertTrue(
                    isset($firstProduct->$field) || property_exists($firstProduct, $field),
                    "Missing field: $field in export data"
                );
            }
        }
    }

    /**
     * Test getCategoryById method exists
     */
    public function testGetCategoryByIdMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'getCategoryById'));
    }

    /**
     * Test getCategoryById returns null for non-existent category
     */
    public function testGetCategoryByIdReturnsNullForNonExistent(): void
    {
        $category = $this->service->getCategoryById(99999);
        $this->assertNull($category);
    }
}
