<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CustomerModel;
use App\Services\CustomerDataService;
use App\Services\ExportService;

class CustomersController extends ResourceController
{
    use ResponseTrait;
    
    protected CustomerModel $customerModel;
    protected CustomerDataService $dataService;
    
    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->dataService = new CustomerDataService();
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
            return $this->failNotFound('Customer tidak ditemukan');
        }
        return $this->respond($detailData);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        
        // Validate using model
        if (!$this->customerModel->validate($data)) {
            return $this->failValidationErrors($this->customerModel->errors());
        }
        
        $id = $this->customerModel->insert($data);
        return $this->respondCreated(['id' => $id]);
    }
    
    public function update($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        
        $data = $this->request->getPost();
        
        if (!$this->customerModel->update($id, $data)) {
            return $this->failValidationErrors($this->customerModel->errors());
        }
        
        return $this->respond(['message' => 'Customer berhasil diperbarui']);
    }
    
    public function delete($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        
        $this->customerModel->delete($id);
        return $this->respondDeleted(['message' => 'Customer berhasil dihapus']);
    }
    
    public function receivable($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        return $this->respond($customer);
    }
    
     public function creditLimit()
     {
         $customers = $this->customerModel->findAll();
         return $this->respond($customers);
     }

     /**
      * Export customers to PDF
      * GET /api/v1/customers/export
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
             $customers = $this->dataService->getExportData($filters);

             // Generate PDF
             $exportService = new ExportService();
             $filename = $exportService->generateFilename('customers');
             $pdfContent = $exportService->generatePDF(
                 $customers,
                 'customers',
                 'Daftar Pelanggan',
                 $this->prepareFilterLabels($filters)
             );

             // Return download response
             return $exportService->getDownloadResponse($pdfContent, $filename);
         } catch (\Exception $e) {
             log_message('error', 'API Customers export error: ' . $e->getMessage());
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
