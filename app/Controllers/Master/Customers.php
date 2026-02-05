<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\CustomerModel;
use App\Services\CustomerDataService;
use App\Services\ExportService;
use App\Traits\ApiResponseTrait;

class Customers extends BaseCRUDController
{
    use ApiResponseTrait;
    protected string $viewPath = 'master/customers';
    protected string $routePath = '/master/customers';
    protected string $entityName = 'Customer';
    protected string $entityNamePlural = 'Customers';

    protected CustomerDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new CustomerDataService();
    }

    protected function getModel(): CustomerModel
    {
        return new CustomerModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
            'address' => 'permit_empty',
            'credit_limit' => 'required|numeric',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'credit_limit' => $this->request->getPost('credit_limit'),
        ];
    }

    /**
     * Override index to use CustomerDataService with pagination
     */
    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $data = array_merge(
                ['title' => 'Daftar Customer'],
                $this->dataService->getPaginatedData($page, $perPage)
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Customers index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data customer');
        }
    }

    /**
     * Override create to use CustomerDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Customer',
                'subtitle' => 'Tambahkan customer baru',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use CustomerDataService and pass 'customer' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Customer tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Customer',
                'subtitle' => 'Ubah data customer',
                'customer' => $record,
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use CustomerDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Customer tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Customer',
                'subtitle' => $detailData['customer']->name,
            ],
            $detailData
        );

        return view($this->viewPath . '/detail', $data);
    }

    /**
     * Export customers to PDF
     * GET /master/customers/export-pdf
     *
     * Query parameters:
     * - status: Filter by status (active/inactive)
     *
     * @return \CodeIgniter\HTTP\Response PDF file download
     */
    public function export()
    {
        try {
            // Get filters from query string
            $filters = [
                'status' => $this->request->getGet('status'),
            ];

            // Get export data from service
            $customers = $this->dataService->getExportData($filters);

            // Initialize export service
            $exportService = new ExportService();

            // Generate PDF
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
            log_message('error', 'Customers export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor customer: ' . $e->getMessage());
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

    /**
     * AJAX: Get customer list for dropdown/select2
     * Returns simplified customer data for forms
     */
    public function getList()
    {
        $customers = $this->model
            ->select('id, code, name, phone, address, credit_limit, receivable_balance')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respondData($customers);
    }
}
