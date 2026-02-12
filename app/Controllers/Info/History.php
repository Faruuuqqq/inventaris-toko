<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\SupplierModel;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;
use App\Models\SalesReturnModel;
use App\Models\PurchaseReturnModel;
use App\Models\PaymentModel;
use App\Models\ExpenseModel;
use App\Models\StockMutationModel;
use App\Traits\ApiResponseTrait;

class History extends BaseController
{
    use ApiResponseTrait;
    protected $saleModel;
    protected $customerModel;
    protected $salespersonModel;
    protected $supplierModel;
    protected $productModel;
    protected $purchaseOrderModel;
    protected $salesReturnModel;
    protected $purchaseReturnModel;
    protected $paymentModel;
    protected $expenseModel;
    protected $stockMutationModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->supplierModel = new SupplierModel();
        $this->productModel = new ProductModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->salesReturnModel = new SalesReturnModel();
        $this->purchaseReturnModel = new PurchaseReturnModel();
        $this->paymentModel = new PaymentModel();
        $this->expenseModel = new ExpenseModel();
        $this->stockMutationModel = new StockMutationModel();
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

        $sales = $this->saleModel->getAllSalesWithHidden(
            $customerId,
            $paymentType,
            $startDate,
            $endDate,
            $paymentStatus
        );

        return $this->respondData($sales);
    }

    /**
     * AJAX: Toggle sale visibility (hide/unhide). OWNER only.
     * POST /info/history/toggleSaleHide/{id}
     */
    public function toggleSaleHide($saleId)
    {
        if (session()->get('role') !== 'OWNER') {
            return $this->respondForbidden('Akses ditolak. Hanya Owner yang dapat melakukan ini.');
        }

        try {
            $this->saleModel->toggleHide($saleId);
            return $this->respondSuccess(null, 'Status visibilitas berhasil diubah');
        } catch (\Exception $e) {
            return $this->respondError($e->getMessage());
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
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $purchases = $this->purchaseOrderModel->getFilteredHistory($supplierId, $status, $startDate, $endDate);

        return $this->respondData($purchases);
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
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $returns = $this->salesReturnModel->getSalesReturns($customerId, $status, $startDate, $endDate);

        return $this->respondData($returns);
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
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $returns = $this->purchaseReturnModel->getPurchaseReturns($supplierId, $status, $startDate, $endDate);

        return $this->respondData($returns);
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
        $method = $this->request->getGet('payment_method');

        $payments = $this->paymentModel->getReceivableHistory($customerId, $startDate, $endDate, $method);

        return $this->respondData($payments);
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
        $method = $this->request->getGet('payment_method');

        $payments = $this->paymentModel->getPayableHistory($supplierId, $startDate, $endDate, $method);

        return $this->respondData($payments);
    }

    /**
     * Expense History (Biaya/Jasa)
     */
    public function expenses()
    {
        $data = [
            'title' => 'Histori Biaya/Jasa',
            'subtitle' => 'Riwayat pengeluaran operasional',
            'categories' => $this->expenseModel->getCategories(),
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

         $expenses = $this->expenseModel->getExpenses($category, $startDate, $endDate, $paymentMethod);

         return $this->respondData($expenses);
     }

     /**
      * Stock Movement History - View all stock mutations
      */
     public function stockMovements()
     {
         // Check if user has warehouse access
         if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
             return redirect()->to('/dashboard')->with('error', 'Access denied');
         }

         $data = [
             'title' => 'Histori Pergerakan Stok',
             'subtitle' => 'Riwayat lengkap pergerakan stok produk',
             'products' => $this->productModel->findAll(),
             'types' => ['SALE', 'PURCHASE', 'SALES_RETURN', 'PURCHASE_RETURN', 'ADJUSTMENT']
         ];

         return view('info/history/stock-movements', $data);
     }

     /**
      * Get Stock Movement Data
      */
     public function stockMovementsData()
     {
         $productId = $this->request->getGet('product_id');
         $type = $this->request->getGet('type');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');

         $movements = $this->stockMutationModel->getFilteredMovements($productId, $type, $startDate, $endDate);

         return $this->respondData($movements);
     }

     /**
      * Export Sales History to CSV
      */
     public function exportSalesCSV()
     {
         $customerId = $this->request->getGet('customer_id');
         $paymentType = $this->request->getGet('payment_type');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');
         $paymentStatus = $this->request->getGet('payment_status');

         $sales = $this->saleModel->getAllSalesWithHidden(
             $customerId,
             $paymentType,
             $startDate,
             $endDate,
             $paymentStatus
         );

         // Prepare CSV
         $filename = 'sales_history_' . date('Y-m-d_His') . '.csv';
         $csv = "Nomor Invoice,Tanggal,Customer,Tipe Pembayaran,Total,Dibayar,Status Pembayaran,Salesman\n";

         foreach ($sales as $sale) {
             $csv .= "\"{$sale['invoice_number']}\",\"{$sale['created_at']}\",\"{$sale['customer_name']}\",\"{$sale['payment_type']}\",\"{$sale['total_amount']}\",\"{$sale['paid_amount']}\",\"{$sale['payment_status']}\",\"{$sale['salesperson_name']}\"\n";
         }

         return $this->response
             ->setHeader('Content-Type', 'text/csv; charset=utf-8')
             ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
             ->setBody($csv);
     }

     /**
      * Export Purchases History to CSV
      */
     public function exportPurchasesCSV()
     {
         $supplierId = $this->request->getGet('supplier_id');
         $status = $this->request->getGet('status');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');

         $purchases = $this->purchaseOrderModel->getFilteredHistory($supplierId, $status, $startDate, $endDate);

         $filename = 'purchases_history_' . date('Y-m-d_His') . '.csv';
         $csv = "Nomor PO,Tanggal PO,Supplier,Total,Status\n";

         foreach ($purchases as $po) {
             $csv .= "\"{$po['nomor_po']}\",\"{$po['tanggal_po']}\",\"{$po['supplier_name']}\",\"{$po['total_amount']}\",\"{$po['status']}\"\n";
         }

         return $this->response
             ->setHeader('Content-Type', 'text/csv; charset=utf-8')
             ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
             ->setBody($csv);
     }

     /**
      * Export Payment History to CSV
      */
     public function exportPaymentsCSV()
     {
         $type = $this->request->getGet('type'); // RECEIVABLE or PAYABLE
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');

         if ($type === 'RECEIVABLE') {
             $payments = $this->paymentModel->getReceivableHistory(null, $startDate, $endDate);
             $filename = 'payments_receivable_' . date('Y-m-d_His') . '.csv';
             $csv = "No Pembayaran,Tanggal,Customer,Invoice,Metode,Jumlah\n";

             foreach ($payments as $payment) {
                 $csv .= "\"{$payment['payment_number']}\",\"{$payment['payment_date']}\",\"{$payment['customer_name']}\",\"{$payment['invoice_number']}\",\"{$payment['method']}\",\"{$payment['amount']}\"\n";
             }
         } else {
             $payments = $this->paymentModel->getPayableHistory(null, $startDate, $endDate);
             $filename = 'payments_payable_' . date('Y-m-d_His') . '.csv';
             $csv = "No Pembayaran,Tanggal,Supplier,No PO,Metode,Jumlah\n";

             foreach ($payments as $payment) {
                 $csv .= "\"{$payment['payment_number']}\",\"{$payment['payment_date']}\",\"{$payment['supplier_name']}\",\"{$payment['po_number']}\",\"{$payment['method']}\",\"{$payment['amount']}\"\n";
             }
         }

         return $this->response
             ->setHeader('Content-Type', 'text/csv; charset=utf-8')
             ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
             ->setBody($csv);
     }

     /**
      * Get Summary Statistics for Sales History
      */
     public function salesSummary()
     {
         $customerId = $this->request->getGet('customer_id');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');

         $db = \Config\Database::connect();
         $builder = $db->table('sales')
             ->select('
                 COUNT(DISTINCT sales.id) as total_transactions,
                 SUM(sales.total_amount) as total_amount,
                 SUM(sales.paid_amount) as total_paid,
                 SUM(CASE WHEN sales.payment_status IN ("UNPAID", "PARTIAL") THEN (sales.total_amount - sales.paid_amount) ELSE 0 END) as outstanding,
                 AVG(sales.total_amount) as average_transaction
             ');

         if ($customerId) {
             $builder->where('sales.customer_id', $customerId);
         }
         if ($startDate) {
             $builder->where('sales.created_at >=', $startDate);
         }
         if ($endDate) {
             $builder->where('sales.created_at <=', $endDate);
         }

         $summary = $builder->first();

         return $this->respondData($summary);
     }

     /**
      * Get Summary Statistics for Purchases History
      */
     public function purchasesSummary()
     {
         $supplierId = $this->request->getGet('supplier_id');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');

         $db = \Config\Database::connect();
         $builder = $db->table('purchase_orders')
             ->select('
                 COUNT(DISTINCT purchase_orders.id) as total_transactions,
                 SUM(purchase_orders.total_amount) as total_amount,
                 AVG(purchase_orders.total_amount) as average_transaction
             ');

         if ($supplierId) {
             $builder->where('purchase_orders.supplier_id', $supplierId);
         }
         if ($startDate) {
             $builder->where('purchase_orders.tanggal_po >=', $startDate);
         }
         if ($endDate) {
             $builder->where('purchase_orders.tanggal_po <=', $endDate);
         }

         $summary = $builder->first();

         return $this->respondData($summary);
     }
}

