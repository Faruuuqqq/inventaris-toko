<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SupplierModel;
use App\Services\SupplierDataService;
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
            $data = array_merge(
                ['title' => 'Daftar Supplier'],
                $this->dataService->getIndexData()
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

    protected function getIndexData(): array
    {
        return $this->model->asArray()->findAll();
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

    /**
     * Show supplier detail page
     */
    public function detail($id)
    {
        $supplier = $this->model->find($id);
        
        if (!$supplier) {
            return redirect()->to($this->routePath)->with('error', 'Supplier tidak ditemukan');
        }

        $db = \Config\Database::connect();
        
        // Get recent purchase orders
        $recentPOs = $db->table('purchase_orders')
            ->select('id_po, nomor_po, tanggal_po, total_amount, status')
            ->where('supplier_id', $id)
            ->orderBy('tanggal_po', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get debt/unpaid balance
        $debtStatus = $db->table('purchase_orders')
            ->select('SUM(total_amount - received_amount) as total_debt, COUNT(*) as pending_count')
            ->where('purchase_orders.supplier_id', $id)
            ->where('purchase_orders.status !=', 'Dibatalkan')
            ->get()
            ->getRow();

        $totalDebt = $debtStatus->total_debt ?? 0;
        $pendingCount = $debtStatus->pending_count ?? 0;

        // Get statistics
        $stats = $db->table('purchase_orders')
            ->select('COUNT(*) as total_pos, SUM(total_amount) as total_purchases, AVG(total_amount) as avg_po')
            ->where('supplier_id', $id)
            ->get()
            ->getRow();

        $data = [
            'title' => 'Detail Supplier',
            'subtitle' => $supplier->name,
            'supplier' => $supplier,
            'recentPOs' => $recentPOs,
            'totalDebt' => $totalDebt,
            'pendingCount' => $pendingCount,
            'stats' => $stats,
        ];

        return view($this->viewPath . '/detail', $data);
    }
}
