<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Helpers\PaginationHelper;
use App\Models\SupplierModel;
use App\Services\ExportService;
use App\Traits\ApiResponseTrait;

class Suppliers extends BaseCRUDController
{
    use ApiResponseTrait;

    protected string $viewPath = 'master/suppliers';

    protected string $routePath = '/master/suppliers';

    protected string $entityName = 'Supplier';

    protected string $entityNamePlural = 'Suppliers';

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
            'address' => $this->request->getPost('address'),
        ];
    }

    protected function getListSelectFields(): string
    {
        return 'id, code, name, phone';
    }

    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $params = PaginationHelper::getSafeParams($page, $perPage);
            $page = $params['page'];
            $perPage = $params['perPage'];

            $suppliers = $this->model->asArray()->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            $data = [
                'title' => 'Daftar Supplier',
                'suppliers' => $suppliers,
                'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
            ];

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Suppliers index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data supplier');
        }
    }

    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        return view($this->viewPath . '/create', [
            'title' => 'Tambah Supplier',
            'subtitle' => 'Tambahkan supplier baru',
        ]);
    }

    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Supplier tidak ditemukan');
        }

        return view($this->viewPath . '/edit', [
            'title' => 'Edit Supplier',
            'subtitle' => 'Ubah data supplier',
            'supplier' => $record,
        ]);
    }

    public function detail($id)
    {
        $supplier = $this->model->find($id);

        if (!$supplier) {
            return redirect()->to($this->routePath)->with('error', 'Supplier tidak ditemukan');
        }

        $db = \Config\Database::connect();

        $recentPOs = $db->table('purchase_orders')
            ->select('id_po, nomor_po, tanggal_po, total_amount, status')
            ->where('supplier_id', $id)
            ->orderBy('tanggal_po', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $debtStatus = $db->table('purchase_orders')
            ->select('SUM(total_amount - received_amount) as total_debt, COUNT(*) as pending_count')
            ->where('supplier_id', $id)
            ->where('status !=', 'Dibatalkan')
            ->get()
            ->getRow();

        $totalDebt = $debtStatus->total_debt ?? 0;
        $pendingCount = $debtStatus->pending_count ?? 0;

        $stats = $db->table('purchase_orders')
            ->select('COUNT(*) as total_pos, SUM(total_amount) as total_purchases, AVG(total_amount) as avg_po')
            ->where('supplier_id', $id)
            ->get()
            ->getRow();

        return view($this->viewPath . '/detail', [
            'title' => 'Detail Supplier',
            'subtitle' => $supplier->name,
            'supplier' => $supplier,
            'recentPOs' => $recentPOs,
            'totalDebt' => (int)$totalDebt,
            'pendingCount' => (int)$pendingCount,
            'stats' => $stats,
        ]);
    }

    public function export()
    {
        try {
            $status = $this->request->getGet('status');
            $query = $this->model->asArray();

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $suppliers = $query->findAll();
            $exportService = new ExportService();

            $filename = $exportService->generateFilename('suppliers');
            $pdfContent = $exportService->generatePDF(
                $suppliers,
                'suppliers',
                'Daftar Supplier',
                !empty($status) ? ['status' => $status === 'active' ? 'Aktif' : 'Tidak Aktif'] : []
            );

            return $exportService->getDownloadResponse($pdfContent, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Suppliers export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor supplier: ' . $e->getMessage());
        }
    }
}
