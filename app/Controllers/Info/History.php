<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\SupplierModel;

class History extends BaseController
{
    protected $saleModel;
    protected $saleItemModel;
    protected $customerModel;
    protected $salespersonModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->supplierModel = new SupplierModel();
    }

    public function sales()
    {
        $data = [
            'title' => 'Histori Penjualan',
            'subtitle' => 'Lihat riwayat transaksi penjualan',
            'customers' => $this->customerModel->findAll(),
        ];

        return view('info/history/sales', $data);
    }

    public function salesData()
    {
        $customerId = $this->request->getGet('customer_id');
        $paymentType = $this->request->getGet('payment_type');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentStatus = $this->request->getGet('payment_status');

        // Use the model method that handles hidden sales properly
        $sales = $this->saleModel->getAllSalesWithHidden(
            $customerId,
            $paymentType,
            $startDate,
            $endDate,
            $paymentStatus
        );

        // Add isOwner flag to response for UI
        $isOwner = session()->get('role') === 'OWNER';

        return $this->response->setJSON([
            'data' => $sales,
            'isOwner' => $isOwner
        ]);
    }

    /**
     * Toggle hide status for a sale (OWNER only)
     */
    public function toggleSaleHide($saleId)
    {
        // Check if user is OWNER
        if (session()->get('role') !== 'OWNER') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Owner yang dapat melakukan ini.'
            ]);
        }

        try {
            $this->saleModel->toggleHide($saleId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status visibilitas berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function purchases()
    {
        $data = [
            'title' => 'Histori Pembelian',
            'subtitle' => 'Lihat riwayat transaksi pembelian',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('info/history/purchases', $data);
    }

    public function purchasesData()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_orders')
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id');

        if ($supplierId) {
            $builder->where('purchase_orders.supplier_id', $supplierId);
        }

        if ($status) {
            $builder->where('purchase_orders.status', $status);
        }

        if ($startDate) {
            $builder->where('purchase_orders.tanggal_po >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.tanggal_po <=', $endDate);
        }

        $purchases = $builder->orderBy('purchase_orders.tanggal_po', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($purchases);
    }

    public function returnSales()
    {
        $data = [
            'title' => 'Histori Retur Penjualan',
            'subtitle' => 'Lihat riwayat retur penjualan',
            'customers' => $this->customerModel->findAll(),
        ];

        return view('info/history/return-sales', $data);
    }

    public function salesReturnsData()
    {
        $customerId = $this->request->getGet('customer_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('sales_returns')
            ->select('sales_returns.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales_returns.customer_id');

        if ($customerId) {
            $builder->where('sales_returns.customer_id', $customerId);
        }

        if ($status) {
            $builder->where('sales_returns.status', $status);
        }

        if ($startDate) {
            $builder->where('sales_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $builder->where('sales_returns.tanggal_retur <=', $endDate);
        }

        $returns = $builder->orderBy('sales_returns.tanggal_retur', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($returns);
    }

    public function returnPurchases()
    {
        $data = [
            'title' => 'Histori Retur Pembelian',
            'subtitle' => 'Lihat riwayat retur pembelian',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('info/history/return-purchases', $data);
    }

    public function purchaseReturnsData()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_returns')
            ->select('purchase_returns.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id');

        if ($supplierId) {
            $builder->where('purchase_returns.supplier_id', $supplierId);
        }

        if ($status) {
            $builder->where('purchase_returns.status', $status);
        }

        if ($startDate) {
            $builder->where('purchase_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_returns.tanggal_retur <=', $endDate);
        }

        $returns = $builder->orderBy('purchase_returns.tanggal_retur', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($returns);
    }

    /**
     * Payment History - Receivable (Piutang)
     */
    public function paymentsReceivable()
    {
        $data = [
            'title' => 'Histori Pembayaran Piutang',
            'subtitle' => 'Riwayat pembayaran dari customer',
            'customers' => $this->customerModel->findAll(),
        ];

        return view('info/history/payments-receivable', $data);
    }

    /**
     * Get Receivable Payments Data
     */
    public function paymentsReceivableData()
    {
        $customerId = $this->request->getGet('customer_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentMethod = $this->request->getGet('payment_method');

        $db = \Config\Database::connect();
        $builder = $db->table('payments')
            ->select('payments.*, customers.name as customer_name, sales.invoice_number')
            ->join('customers', 'customers.id = payments.customer_id', 'left')
            ->join('sales', 'sales.id = payments.reference_id AND payments.reference_type = "SALE"', 'left')
            ->where('payments.payment_type', 'RECEIVABLE');

        if ($customerId) {
            $builder->where('payments.customer_id', $customerId);
        }

        if ($startDate) {
            $builder->where('payments.payment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('payments.payment_date <=', $endDate);
        }

        if ($paymentMethod) {
            $builder->where('payments.payment_method', $paymentMethod);
        }

        $payments = $builder->orderBy('payments.payment_date', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($payments);
    }

    /**
     * Payment History - Payable (Utang)
     */
    public function paymentsPayable()
    {
        $data = [
            'title' => 'Histori Pembayaran Utang',
            'subtitle' => 'Riwayat pembayaran ke supplier',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('info/history/payments-payable', $data);
    }

    /**
     * Get Payable Payments Data
     */
    public function paymentsPayableData()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentMethod = $this->request->getGet('payment_method');

        $db = \Config\Database::connect();
        $builder = $db->table('payments')
            ->select('payments.*, suppliers.name as supplier_name, purchase_orders.po_number')
            ->join('suppliers', 'suppliers.id = payments.supplier_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = payments.reference_id AND payments.reference_type = "PURCHASE"', 'left')
            ->where('payments.payment_type', 'PAYABLE');

        if ($supplierId) {
            $builder->where('payments.supplier_id', $supplierId);
        }

        if ($startDate) {
            $builder->where('payments.payment_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('payments.payment_date <=', $endDate);
        }

        if ($paymentMethod) {
            $builder->where('payments.payment_method', $paymentMethod);
        }

        $payments = $builder->orderBy('payments.payment_date', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($payments);
    }

    /**
     * Expense History (Biaya/Jasa)
     */
    public function expenses()
    {
        $expenseModel = new \App\Models\ExpenseModel();

        $data = [
            'title' => 'Histori Biaya/Jasa',
            'subtitle' => 'Riwayat pengeluaran operasional',
            'categories' => $expenseModel->getCategories(),
        ];

        return view('info/history/expenses', $data);
    }

    /**
     * Get Expenses Data
     */
    public function expensesData()
    {
        $category = $this->request->getGet('category');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentMethod = $this->request->getGet('payment_method');

        $expenseModel = new \App\Models\ExpenseModel();
        $expenses = $expenseModel->getExpenses($category, $startDate, $endDate, $paymentMethod);

        return $this->response->setJSON($expenses);
    }
}

