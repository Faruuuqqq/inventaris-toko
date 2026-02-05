<?php

namespace App\Services;

use Mpdf\Mpdf;

class ExportService
{
    protected Mpdf $mpdf;

    protected array $config = [];

    public function __construct()
    {
        $this->initializeConfig();
    }

    /**
     * Initialize export configuration
     */
    protected function initializeConfig(): void
    {
        $this->config = [
            // Company Information
            'companyName' => 'INVENTARIS TOKO',
            'companyLogo' => 'assets/img/logo.png',
            'showLogo' => true,

            // PDF Page Settings
            'pageSize' => 'A4',
            'pageOrientation' => 'P',
            'pageMarginTop' => 15,
            'pageMarginBottom' => 15,
            'pageMarginLeft' => 10,
            'pageMarginRight' => 10,

            // PDF Font Settings
            'defaultFont' => 'Arial',
            'headerFontSize' => 14,
            'titleFontSize' => 12,
            'tableFontSize' => 10,
            'footerFontSize' => 9,

            // Export Settings
            'includeGeneratedAt' => true,
            'includePrintedBy' => true,
            'footerText' => 'Confidential - Printed Report',
            'autoRowNumbers' => true,

            // Column Configurations
            'columnConfigs' => [
                'products' => [
                    'no' => ['label' => 'No.', 'align' => 'center', 'width' => 5],
                    'sku' => ['label' => 'SKU', 'align' => 'left', 'width' => 12],
                    'name' => ['label' => 'Nama Produk', 'align' => 'left', 'width' => 25],
                    'category_name' => ['label' => 'Kategori', 'align' => 'left', 'width' => 15],
                    'unit' => ['label' => 'Satuan', 'align' => 'center', 'width' => 10],
                    'purchase_price' => ['label' => 'Harga Beli', 'align' => 'right', 'width' => 12],
                    'selling_price' => ['label' => 'Harga Jual', 'align' => 'right', 'width' => 12],
                    'stock' => ['label' => 'Stok', 'align' => 'center', 'width' => 8],
                    'total_value' => ['label' => 'Total Nilai', 'align' => 'right', 'width' => 12],
                ],
                'customers' => [
                    'no' => ['label' => 'No.', 'align' => 'center', 'width' => 5],
                    'code' => ['label' => 'Kode', 'align' => 'left', 'width' => 12],
                    'name' => ['label' => 'Nama Pelanggan', 'align' => 'left', 'width' => 25],
                    'phone' => ['label' => 'Telepon', 'align' => 'left', 'width' => 18],
                    'address' => ['label' => 'Alamat', 'align' => 'left', 'width' => 25],
                    'credit_limit' => ['label' => 'Limit Kredit', 'align' => 'right', 'width' => 15],
                    'status' => ['label' => 'Status', 'align' => 'center', 'width' => 10],
                ],
                'suppliers' => [
                    'no' => ['label' => 'No.', 'align' => 'center', 'width' => 5],
                    'code' => ['label' => 'Kode', 'align' => 'left', 'width' => 12],
                    'name' => ['label' => 'Nama Supplier', 'align' => 'left', 'width' => 25],
                    'phone' => ['label' => 'Telepon', 'align' => 'left', 'width' => 18],
                    'address' => ['label' => 'Alamat', 'align' => 'left', 'width' => 30],
                    'status' => ['label' => 'Status', 'align' => 'center', 'width' => 10],
                ],
            ],
        ];
    }

    /**
     * Get configuration value
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get column configuration for an entity
     */
    public function getColumnConfig(string $entity): array
    {
        return $this->config['columnConfigs'][$entity] ?? [];
    }

    /**
     * Generate PDF from data and view
     *
     * @param array $data Data to be exported
     * @param string $entity Entity type (products, customers, suppliers)
     * @param string $title Report title
     * @param array $filters Applied filters (for header display)
     * @return string PDF content as binary string
     */
    public function generatePDF(array $data, string $entity, string $title, array $filters = []): string
    {
        // Initialize mPDF with page settings
        $this->initializeMpdf();

        // Format data for export
        $formattedData = $this->formatDataForExport($data, $entity);

        // Get column configuration
        $columns = $this->getColumnConfig($entity);

        // Prepare export context
        $exportData = [
            'title' => $title,
            'entity' => $entity,
            'columns' => $columns,
            'data' => $formattedData,
            'filters' => $filters,
            'generatedAt' => date('Y-m-d H:i'),
            'generatedBy' => $this->getAuthenticatedUser(),
            'totalRecords' => count($formattedData),
            'config' => $this->config,
        ];

        // Render view to HTML
        $html = view('exports/master_data_pdf', $exportData);

        // Write HTML to PDF
        $this->mpdf->WriteHTML($html);

        // Return PDF as binary string
        return $this->mpdf->Output('', 'S');
    }

    /**
     * Format data for PDF export
     * - Add row numbers
     * - Format currency values
     * - Format status badges
     * - Calculate derived values (e.g., total_value)
     *
     * @param array $data Raw data from database
     * @param string $entity Entity type
     * @return array Formatted data
     */
    protected function formatDataForExport(array $data, string $entity): array
    {
        $formatted = [];
        $rowNumber = 1;

        foreach ($data as $record) {
            // Convert object to array if needed
            $item = (array) $record;

            // Add row number
            if ($this->config['autoRowNumbers']) {
                $item['no'] = $rowNumber++;
            }

            // Apply entity-specific formatting
            $formatted[] = $this->formatEntity($item, $entity);
        }

        return $formatted;
    }

    /**
     * Format entity-specific data
     *
     * @param array $item Single record
     * @param string $entity Entity type
     * @return array Formatted record
     */
    protected function formatEntity(array $item, string $entity): array
    {
        return match ($entity) {
            'products' => $this->formatProduct($item),
            'customers' => $this->formatCustomer($item),
            'suppliers' => $this->formatSupplier($item),
            default => $item,
        };
    }

    /**
     * Format product data
     */
    protected function formatProduct(array $item): array
    {
        $item['purchase_price'] = isset($item['purchase_price']) ? $this->formatCurrency($item['purchase_price']) : '-';
        $item['selling_price'] = isset($item['selling_price']) ? $this->formatCurrency($item['selling_price']) : '-';
        $item['stock'] = $item['stock'] ?? 0;

        // Calculate total value (stock * purchase_price)
        if (isset($item['stock']) && isset($item['purchase_price']) && is_numeric($item['purchase_price'])) {
            $totalValue = $item['stock'] * floatval(str_replace(['Rp ', '.', ','], ['', '', '.'], $item['purchase_price']));
            $item['total_value'] = $this->formatCurrency($totalValue);
        } else {
            $item['total_value'] = '-';
        }

        return $item;
    }

    /**
     * Format customer data
     */
    protected function formatCustomer(array $item): array
    {
        $item['credit_limit'] = isset($item['credit_limit']) ? $this->formatCurrency($item['credit_limit']) : '-';
        $item['status'] = $this->formatStatus($item['status'] ?? 'active');

        return $item;
    }

    /**
     * Format supplier data
     */
    protected function formatSupplier(array $item): array
    {
        $item['status'] = $this->formatStatus($item['status'] ?? 'active');

        return $item;
    }

    /**
     * Format currency value for display
     *
     * @param mixed $value Numeric value
     * @return string Formatted currency string (Rp X.XXX,00)
     */
    protected function formatCurrency($value): string
    {
        if (!is_numeric($value)) {
            return '-';
        }

        return 'Rp ' . number_format((float) $value, 2, ',', '.');
    }

    /**
     * Format status value
     *
     * @param string $status Status value
     * @return string Formatted status
     */
    protected function formatStatus(string $status): string
    {
        return match (strtolower($status)) {
            'active', '1' => 'Aktif',
            'inactive', '0' => 'Tidak Aktif',
            default => ucfirst($status),
        };
    }

    /**
     * Initialize mPDF with page settings
     */
    protected function initializeMpdf(): void
    {
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $this->config['pageSize'],
            'orientation' => $this->config['pageOrientation'],
            'margin_top' => $this->config['pageMarginTop'],
            'margin_bottom' => $this->config['pageMarginBottom'],
            'margin_left' => $this->config['pageMarginLeft'],
            'margin_right' => $this->config['pageMarginRight'],
            'default_font' => $this->config['defaultFont'],
        ]);

        // Set default font
        $this->mpdf->SetFont($this->config['defaultFont']);
    }

    /**
     * Save PDF to file
     *
     * @param string $pdfContent PDF binary content
     * @param string $filename Output filename
     * @param string $directory Output directory (relative to public folder)
     * @return string File path relative to public folder
     */
    public function savePDFToFile(string $pdfContent, string $filename, string $directory = 'exports'): string
    {
        $publicPath = FCPATH . $directory;

        // Create directory if it doesn't exist
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $filepath = $publicPath . DIRECTORY_SEPARATOR . $filename;

        // Write file
        file_put_contents($filepath, $pdfContent);

        return $directory . '/' . $filename;
    }

    /**
     * Generate filename for export
     *
     * @param string $entity Entity name
     * @return string Filename with timestamp
     */
    public function generateFilename(string $entity): string
    {
        $timestamp = date('Ymd_His');
        return "{$entity}_export_{$timestamp}.pdf";
    }

    /**
     * Get PDF download response
     *
     * @param string $pdfContent PDF binary content
     * @param string $filename Download filename
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getDownloadResponse(string $pdfContent, string $filename)
    {
        $response = service('response');
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");
        $response->setBody($pdfContent);

        return $response;
    }

    /**
     * Get authenticated user username safely
     */
    protected function getAuthenticatedUser(): string
    {
        try {
            if (function_exists('auth')) {
                return auth()->user()?->username ?? 'System';
            }
            return 'System';
        } catch (\Exception $e) {
            return 'System';
        }
    }
}
