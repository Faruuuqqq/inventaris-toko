<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\SupplierModel;
use App\Services\SupplierDataService;
use App\Services\ExportService;

class SuppliersController extends ResourceController
{
    use ResponseTrait;
    
    protected SupplierModel $supplierModel;
    protected SupplierDataService $dataService;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->dataService = new SupplierDataService();
    }
    
    public function index()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 20);

        $data = $this->dataService->getPaginatedData($page, $perPage);
        return $this->respond($data);
    }
    
    public function show($id = null)
    {
        $detailData = $this->dataService->getDetailData($id);
        if (empty($detailData)) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        return $this->respond($detailData);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        
        if (!$this->supplierModel->validate($data)) {
            return $this->failValidationErrors($this->supplierModel->errors());
        }
        
        $id = $this->supplierModel->insert($data);
        return $this->respondCreated(['id' => $id]);
    }
    
    public function update($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        
        $data = $this->request->getPost();
        
        if (!$this->supplierModel->update($id, $data)) {
            return $this->failValidationErrors($this->supplierModel->errors());
        }
        
        return $this->respond(['message' => 'Supplier berhasil diperbarui']);
    }
    
    public function delete($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        
        $this->supplierModel->delete($id);
        return $this->respondDeleted(['message' => 'Supplier berhasil dihapus']);
    }
    
    /**
     * Export suppliers to PDF
     * GET /api/v1/suppliers/export
     *
     * Query parameters:
     * - format: Export format (pdf only for now)
     * - status: Filter by status
     *
     * @return mixed PDF file or error response
     */
    public function export()
    {
        try {
            $format = $this->request->getGet('format') ?? 'pdf';

            // Only PDF supported for now
            if ($format !== 'pdf') {
                return $this->fail('Only PDF format is supported', 400);
            }

            // Get filters
            $filters = [
                'status' => $this->request->getGet('status'),
            ];

            // Get export data
            $suppliers = $this->dataService->getExportData($filters);

            // Generate PDF
            $exportService = new ExportService();
            $filename = $exportService->generateFilename('suppliers');
            $pdfContent = $exportService->generatePDF(
                $suppliers,
                'suppliers',
                'Daftar Supplier',
                $this->prepareFilterLabels($filters)
            );

            // Return download response
            return $exportService->getDownloadResponse($pdfContent, $filename);
        } catch (\Exception $e) {
            log_message('error', 'API Suppliers export error: ' . $e->getMessage());
            return $this->fail('Export failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Prepare human-readable filter labels for PDF header
     *
     * @param array $filters Raw filter values
     * @return array Filter labels for display
     */
    protected function prepareFilterLabels(array $filters): array
    {
        $labels = [];

        if (!empty($filters['status'])) {
            $labels['status'] = $filters['status'] === 'active' ? 'Aktif' : 'Tidak Aktif';
        }

        return $labels;
    }
}
