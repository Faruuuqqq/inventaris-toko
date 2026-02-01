<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\SupplierModel;
use App\Models\StockMutationModel;
use App\Models\ProductModel;

class History extends BaseController
{
    protected $saleModel;
    protected $saleItemModel;
    protected $customerModel;
    protected $salespersonModel;
    protected $supplierModel;
    protected $stockMutationModel;
    protected $productModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->supplierModel = new SupplierModel();
        $this->stockMutationModel = new StockMutationModel();
        $this->productModel = new ProductModel();
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

     /**
      * Stock Movement History - View all stock mutations
      */
     public function stockMovements()
     {
         // Check if user has warehouse access
         if (!in_array(session()->get('role'), ['OWNER', 'ADMIN', 'GUDANG'])) {
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

         $db = \Config\Database::connect();
         $builder = $db->table('stock_mutations')
             ->select('stock_mutations.*, products.name as product_name, products.sku, warehouses.name as warehouse_name')
             ->join('products', 'products.id = stock_mutations.product_id')
             ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id');

         if ($productId) {
             $builder->where('stock_mutations.product_id', $productId);
         }

         if ($type) {
             $builder->where('stock_mutations.type', $type);
         }

         if ($startDate) {
             $builder->where('stock_mutations.created_at >=', $startDate);
         }

         if ($endDate) {
             $builder->where('stock_mutations.created_at <=', $endDate);
         }

         $movements = $builder->orderBy('stock_mutations.created_at', 'DESC')->get()->getResultArray();

         return $this->response->setJSON($movements);
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

         // Prepare CSV
         $filename = 'purchases_history_' . date('Y-m-d_His') . '.csv';
         $csv = "Nomor PO,Tanggal PO,Supplier,Total,Status\n";

         foreach ($purchases as $po) {
             $csv .= "\"{$po['po_number']}\",\"{$po['tanggal_po']}\",\"{$po['supplier_name']}\",\"{$po['total_amount']}\",\"{$po['status']}\"\n";
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

         $db = \Config\Database::connect();

         if ($type === 'RECEIVABLE') {
             $builder = $db->table('payments')
                 ->select('payments.*, customers.name as customer_name')
                 ->join('customers', 'customers.id = payments.customer_id', 'left')
                 ->where('payments.payment_type', 'RECEIVABLE');
             $filename = 'payments_receivable_' . date('Y-m-d_His') . '.csv';
             $csv = "ID Pembayaran,Tanggal,Customer,Metode Pembayaran,Jumlah\n";
         } else {
             $builder = $db->table('payments')
                 ->select('payments.*, suppliers.name as supplier_name')
                 ->join('suppliers', 'suppliers.id = payments.supplier_id', 'left')
                 ->where('payments.payment_type', 'PAYABLE');
             $filename = 'payments_payable_' . date('Y-m-d_His') . '.csv';
             $csv = "ID Pembayaran,Tanggal,Supplier,Metode Pembayaran,Jumlah\n";
         }

         if ($startDate) {
             $builder->where('payments.payment_date >=', $startDate);
         }
         if ($endDate) {
             $builder->where('payments.payment_date <=', $endDate);
         }

         $payments = $builder->orderBy('payments.payment_date', 'DESC')->get()->getResultArray();

         // Build CSV rows
         foreach ($payments as $payment) {
             if ($type === 'RECEIVABLE') {
                 $csv .= "\"{$payment['id']}\",\"{$payment['payment_date']}\",\"{$payment['customer_name']}\",\"{$payment['payment_method']}\",\"{$payment['amount']}\"\n";
             } else {
                 $csv .= "\"{$payment['id']}\",\"{$payment['payment_date']}\",\"{$payment['supplier_name']}\",\"{$payment['payment_method']}\",\"{$payment['amount']}\"\n";
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

         return $this->response->setJSON($summary);
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

         return $this->response->setJSON($summary);
     }
}

