<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Helpers\PaginationHelper;
use App\Models\CustomerModel;
use App\Services\ExportService;
use App\Traits\ApiResponseTrait;

class Customers extends BaseCRUDController
{
    use ApiResponseTrait;

    protected string $viewPath = 'master/customers';

    protected string $routePath = '/master/customers';

    protected string $entityName = 'Customer';

    protected string $entityNamePlural = 'Customers';

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

    protected function getListSelectFields(): string
    {
        return 'id, code, name, phone, address, credit_limit, receivable_balance';
    }

    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $params = PaginationHelper::getSafeParams($page, $perPage);
            $page = $params['page'];
            $perPage = $params['perPage'];

            $customers = $this->model->asArray()->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            $data = [
                'title' => 'Daftar Customer',
                'customers' => $customers,
                'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
            ];

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Customers index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data customer');
        }
    }

    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        return view($this->viewPath . '/create', [
            'title' => 'Tambah Customer',
            'subtitle' => 'Tambahkan customer baru',
        ]);
    }

    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Customer tidak ditemukan');
        }

        return view($this->viewPath . '/edit', [
            'title' => 'Edit Customer',
            'subtitle' => 'Ubah data customer',
            'customer' => $record,
        ]);
    }

    public function detail($id)
    {
        $customer = $this->model->find($id);

        if (!$customer) {
            return redirect()->to($this->routePath)->with('error', 'Customer tidak ditemukan');
        }

        $db = \Config\Database::connect();

        $recentSales = $db->table('sales')
            ->select('id_sale, nomor_faktur, tanggal_penjualan, total_penjualan, status_pembayaran')
            ->where('id_customer', $id)
            ->orderBy('tanggal_penjualan', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $totalCredit = $db->table('sales')
            ->selectSum('sisa_pembayaran')
            ->where('id_customer', $id)
            ->where('status_pembayaran !=', 'PAID')
            ->get()
            ->getRow();

        $creditUsed = $totalCredit->sisa_pembayaran ?? 0;
        $creditLimit = $customer->credit_limit ?? 0;
        $creditAvailable = $creditLimit - $creditUsed;
        $creditPercentage = $creditLimit > 0 ? ($creditUsed / $creditLimit) * 100 : 0;

        $stats = $db->table('sales')
            ->select('COUNT(*) as total_transactions, SUM(total_penjualan) as total_sales, AVG(total_penjualan) as avg_sale')
            ->where('id_customer', $id)
            ->get()
            ->getRow();

        return view($this->viewPath . '/detail', array_merge([
            'title' => 'Detail Customer',
            'subtitle' => $customer->name,
            'customer' => $customer,
            'recentSales' => $recentSales,
            'creditUsed' => (int)$creditUsed,
            'creditLimit' => (int)$creditLimit,
            'creditAvailable' => (int)$creditAvailable,
            'creditPercentage' => min($creditPercentage, 100),
            'stats' => $stats,
        ]));
    }

    public function export()
    {
        try {
            $status = $this->request->getGet('status');
            $query = $this->model->asArray();

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $customers = $query->findAll();
            $exportService = new ExportService();

            $filename = $exportService->generateFilename('customers');
            $pdfContent = $exportService->generatePDF(
                $customers,
                'customers',
                'Daftar Pelanggan',
                !empty($status) ? ['status' => $status === 'active' ? 'Aktif' : 'Tidak Aktif'] : []
            );

            return $exportService->getDownloadResponse($pdfContent, $filename);
        } catch (\Exception $e) {
            log_message('error', 'Customers export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor customer: ' . $e->getMessage());
        }
    }
}
