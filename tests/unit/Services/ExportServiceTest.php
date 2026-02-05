<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\ExportService;

class ExportServiceTest extends CIUnitTestCase
{
    protected ExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExportService();
    }

    /**
     * Test that ExportService can be instantiated
     */
    public function testExportServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ExportService::class, $this->service);
    }

    /**
     * Test that generatePDF method exists
     */
    public function testGeneratePDFMethodExists(): void
    {
        $this->assertTrue(method_exists($this->service, 'generatePDF'));
    }

    /**
     * Test generatePDF returns binary string (PDF content)
     */
    public function testGeneratePDFReturnsBinaryString(): void
    {
        $data = [
            (object) [
                'no' => 1,
                'sku' => 'PROD001',
                'name' => 'Test Product',
                'category_name' => 'Test Category',
                'unit' => 'pcs',
                'purchase_price' => 10000,
                'selling_price' => 15000,
                'stock' => 100,
            ],
        ];

        $pdf = $this->service->generatePDF($data, 'products', 'Test Report');

        $this->assertIsString($pdf);
        $this->assertNotEmpty($pdf);
        // PDF files typically start with %PDF
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    /**
     * Test generatePDF with empty data
     */
    public function testGeneratePDFWithEmptyData(): void
    {
        $pdf = $this->service->generatePDF([], 'products', 'Empty Report');

        $this->assertIsString($pdf);
        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    /**
     * Test generateFilename method
     */
    public function testGenerateFilename(): void
    {
        $filename = $this->service->generateFilename('products');

        $this->assertStringContainsString('products_export_', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
    }

    /**
     * Test generateFilename includes timestamp
     */
    public function testGenerateFilenameIncludesTimestamp(): void
    {
        $before = date('Ymd_Hi');
        $filename = $this->service->generateFilename('customers');
        $after = date('Ymd_Hi');

        $this->assertStringContainsString('customers_export_', $filename);
        // Filename should have a timestamp close to current time
        $this->assertNotEmpty($filename);
    }

    /**
     * Test savePDFToFile creates file
     */
    public function testSavePDFToFileCreatesFile(): void
    {
        $pdfContent = '%PDF-1.4 Test Content';
        $filename = 'test_export_' . time() . '.pdf';
        $directory = 'exports';

        $filepath = $this->service->savePDFToFile($pdfContent, $filename, $directory);

        $this->assertStringContainsString($filename, $filepath);
        $this->assertTrue(file_exists(FCPATH . $filepath));

        // Cleanup
        @unlink(FCPATH . $filepath);
    }

    /**
     * Test savePDFToFile creates directory if not exists
     */
    public function testSavePDFToFileCreatesDirectory(): void
    {
        $testDir = 'exports_test_' . time();
        $pdfContent = '%PDF-1.4 Test';
        $filename = 'test.pdf';

        $filepath = $this->service->savePDFToFile($pdfContent, $filename, $testDir);

        $this->assertTrue(is_dir(FCPATH . $testDir));

        // Cleanup
        @unlink(FCPATH . $filepath);
        @rmdir(FCPATH . $testDir);
    }

    /**
     * Test getDownloadResponse returns response interface
     */
    public function testGetDownloadResponseReturnsResponse(): void
    {
        $pdfContent = '%PDF-1.4 Test';
        $filename = 'test.pdf';

        $response = $this->service->getDownloadResponse($pdfContent, $filename);

        $this->assertInstanceOf('CodeIgniter\HTTP\ResponseInterface', $response);
    }

    /**
     * Test getDownloadResponse sets correct headers
     */
    public function testGetDownloadResponseSetsHeaders(): void
    {
        $pdfContent = '%PDF-1.4 Test';
        $filename = 'test_download.pdf';

        $response = $this->service->getDownloadResponse($pdfContent, $filename);

        $this->assertEquals($response->getHeader('Content-Type')->getValue(), 'application/pdf');
        $this->assertStringContainsString('attachment', $response->getHeader('Content-Disposition')->getValue());
        $this->assertStringContainsString($filename, $response->getHeader('Content-Disposition')->getValue());
    }

    /**
     * Test formatCurrency method
     */
    public function testFormatCurrency(): void
    {
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatCurrency');
        $method->setAccessible(true);

        // Test valid number
        $result = $method->invoke($this->service, 10000);
        $this->assertStringContainsString('Rp', $result);
        $this->assertStringContainsString('10.000', $result);

        // Test zero
        $result = $method->invoke($this->service, 0);
        $this->assertEquals('Rp 0,00', $result);

        // Test decimal
        $result = $method->invoke($this->service, 1000.50);
        $this->assertStringContainsString('Rp', $result);

        // Test invalid
        $result = $method->invoke($this->service, 'invalid');
        $this->assertEquals('-', $result);
    }

    /**
     * Test formatStatus method
     */
    public function testFormatStatus(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatStatus');
        $method->setAccessible(true);

        $this->assertEquals('Aktif', $method->invoke($this->service, 'active'));
        $this->assertEquals('Aktif', $method->invoke($this->service, '1'));
        $this->assertEquals('Tidak Aktif', $method->invoke($this->service, 'inactive'));
        $this->assertEquals('Tidak Aktif', $method->invoke($this->service, '0'));
    }

    /**
     * Test formatProduct method calculates total value
     */
    public function testFormatProductCalculatesTotalValue(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatProduct');
        $method->setAccessible(true);

        $data = [
            'sku' => 'PROD001',
            'name' => 'Test Product',
            'stock' => 100,
            'purchase_price' => 10000,
            'selling_price' => 15000,
        ];

        $result = $method->invoke($this->service, $data);

        $this->assertArrayHasKey('total_value', $result);
        $this->assertNotEmpty($result['total_value']);
    }

    /**
     * Test formatCustomer formats credit limit
     */
    public function testFormatCustomerFormatsCreditLimit(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatCustomer');
        $method->setAccessible(true);

        $data = [
            'code' => 'CUST001',
            'name' => 'Test Customer',
            'credit_limit' => 50000000,
            'status' => 'active',
        ];

        $result = $method->invoke($this->service, $data);

        $this->assertArrayHasKey('credit_limit', $result);
        $this->assertStringContainsString('Rp', $result['credit_limit']);
    }

    /**
     * Test formatSupplier formats status
     */
    public function testFormatSupplierFormatsStatus(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatSupplier');
        $method->setAccessible(true);

        $data = [
            'code' => 'SUP001',
            'name' => 'Test Supplier',
            'status' => 'active',
        ];

        $result = $method->invoke($this->service, $data);

        $this->assertEquals('Aktif', $result['status']);
    }

    /**
     * Test Export configuration exists in service
     */
    public function testExportConfigExists(): void
    {
        $companyName = $this->service->getConfig('companyName');
        $this->assertNotEmpty($companyName);
        $this->assertEquals('INVENTARIS TOKO', $companyName);
    }

    /**
     * Test Export configuration has column configurations
     */
    public function testExportConfigHasColumnConfigs(): void
    {
        $productColumns = $this->service->getColumnConfig('products');
        $this->assertNotEmpty($productColumns);
        $this->assertArrayHasKey('sku', $productColumns);
        $this->assertArrayHasKey('name', $productColumns);

        $customerColumns = $this->service->getColumnConfig('customers');
        $this->assertNotEmpty($customerColumns);
        $this->assertArrayHasKey('code', $customerColumns);
        $this->assertArrayHasKey('credit_limit', $customerColumns);

        $supplierColumns = $this->service->getColumnConfig('suppliers');
        $this->assertNotEmpty($supplierColumns);
        $this->assertArrayHasKey('code', $supplierColumns);
    }

    /**
     * Test PDF generation with products data
     */
    public function testGeneratePDFWithProductsData(): void
    {
        $data = [
            (object) [
                'sku' => 'PROD001',
                'name' => 'Laptop Dell',
                'category_name' => 'Electronics',
                'unit' => 'pcs',
                'purchase_price' => 5000000,
                'selling_price' => 6500000,
                'stock' => 10,
            ],
            (object) [
                'sku' => 'PROD002',
                'name' => 'Mouse Logitech',
                'category_name' => 'Accessories',
                'unit' => 'pcs',
                'purchase_price' => 150000,
                'selling_price' => 250000,
                'stock' => 50,
            ],
        ];

        $pdf = $this->service->generatePDF($data, 'products', 'Daftar Produk', ['warehouse' => 'Semua']);

        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    /**
     * Test PDF generation with customers data
     */
    public function testGeneratePDFWithCustomersData(): void
    {
        $data = [
            (object) [
                'code' => 'CUST001',
                'name' => 'PT Maju Jaya',
                'phone' => '021-1234567',
                'address' => 'Jl. Sudirman 123, Jakarta',
                'credit_limit' => 100000000,
                'status' => 'active',
            ],
            (object) [
                'code' => 'CUST002',
                'name' => 'Toko Bangunan ABC',
                'phone' => '0812-3456789',
                'address' => 'Jl. Gatot Subroto 456, Bandung',
                'credit_limit' => 50000000,
                'status' => 'inactive',
            ],
        ];

        $pdf = $this->service->generatePDF($data, 'customers', 'Daftar Pelanggan');

        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }
}
