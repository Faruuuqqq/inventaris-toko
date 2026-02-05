<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SupplierModel;
use App\Services\SupplierDataService;
use App\Services\ExportService;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

class Suppliers extends BaseCRUDController
{
    use ApiResponseTrait;
    
    protected string $viewPath = 'master/suppliers';
    protected string $routePath = '/master/suppliers';
    protected string $entityName = 'Supplier';
    protected string $entityNamePlural = 'Suppliers';

    protected SupplierDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new SupplierDataService();
    }

    protected function getModel(): SupplierModel
    {
        return new SupplierModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ];
    }

    /**
     * Override index to use SupplierDataService
     */
    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $data = array_merge(
                ['title' => 'Daftar Supplier'],
                $this->dataService->getPaginatedData($page, $perPage)
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Suppliers index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data supplier');
        }
    }

    /**
     * Override create to use SupplierDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Supplier',
                'subtitle' => 'Tambahkan supplier baru',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use SupplierDataService and pass 'supplier' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Supplier tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Supplier',
                'subtitle' => 'Ubah data supplier',
                'supplier' => $record,
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use SupplierDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Supplier tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Supplier',
                'subtitle' => $detailData['supplier']->name,
            ],
            $detailData
        );

        return view($this->viewPath . '/detail', $data);
    }

    /**
     * Export suppliers to PDF
     * GET /master/suppliers/export-pdf
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
            $suppliers = $this->dataService->getExportData($filters);

            // Initialize export service
            $exportService = new ExportService();

            // Generate PDF
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
            log_message('error', 'Suppliers export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor supplier: ' . $e->getMessage());
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
     * AJAX: Get supplier list for dropdown/select2
     * Returns simplified supplier data for forms
     */
    public function getList()
    {
        $suppliers = $this->model
            ->select('id, code, name, phone')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respondData($suppliers);
    }
}

