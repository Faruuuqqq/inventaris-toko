<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\PurchaseOrderModel;
use App\Models\SalesReturnModel;
use App\Models\PurchaseReturnModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\StockMutationModel;
use App\Models\ExpenseModel;
use App\Traits\ApiResponseTrait;

class Reports extends BaseController
{
    use ApiResponseTrait;
    protected $saleModel;
    protected $purchaseOrderModel;
    protected $salesReturnModel;
    protected $purchaseReturnModel;
    protected $productModel;
    protected $customerModel;
    protected $stockMutationModel;
    protected $expenseModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->salesReturnModel = new SalesReturnModel();
        $this->purchaseReturnModel = new PurchaseReturnModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->stockMutationModel = new StockMutationModel();
        $this->expenseModel = new ExpenseModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'OWNER') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $data = [
            'title' => 'Reports Dashboard',
            'salesThisMonth' => $this->getSalesThisMonth(),
            'purchasesThisMonth' => $this->getPurchasesThisMonth(),
            'totalSales' => $this->getTotalSales(),
            'totalPurchases' => $this->getTotalPurchases(),
            'totalReturns' => $this->getTotalReturns(),
            'topProducts' => $this->getTopProducts(10),
            'topCustomers' => $this->getTopCustomers(10),
            'lowStockProducts' => $this->getLowStockProducts(10)
        ];

        return view('info/reports/index', $data);
    }

    public function daily()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $sales = $this->getDailySales($date);
        $purchases = $this->getDailyPurchases($date);
        $returns = $this->getDailyReturns($date);

        $data = [
            'title' => 'Daily Report - ' . $date,
            'date' => $date,
            'sales' => $sales,
            'purchases' => $purchases,
            'returns' => $returns,
            'summary' => [
                'total_sales' => array_sum(array_column($sales, 'total_amount')),
                'total_purchases' => array_sum(array_column($purchases, 'total_amount')),
                'total_returns' => array_sum(array_column($returns['sales_returns'], 'total_retur')) + array_sum(array_column($returns['purchase_returns'], 'total_retur')),
                'transaction_count' => count($sales) + count($purchases) + count($returns['sales_returns']) + count($returns['purchase_returns']),
            ]
        ];

        return view('info/reports/daily', $data);
    }

    public function profitLoss()
    {
        if (session()->get('role') !== 'OWNER') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Profit & Loss Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenue' => $this->calculateRevenue($startDate, $endDate),
            'cogs' => $this->calculateCOGS($startDate, $endDate),
            'returns' => $this->calculateReturns($startDate, $endDate),
            'grossProfit' => 0,
            'expenses' => $this->calculateExpenses($startDate, $endDate),
            'netProfit' => 0
        ];

        $data['grossProfit'] = $data['revenue'] - $data['cogs'] - $data['returns'];
        $data['netProfit'] = $data['grossProfit'] - $data['expenses'];

        return view('info/reports/profit_loss', $data);
    }

    public function cashFlow()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Cash Flow Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'cashInflows' => $this->getCashInflows($startDate, $endDate),
            'cashOutflows' => $this->getCashOutflows($startDate, $endDate),
            'netCashFlow' => 0
        ];

        $data['netCashFlow'] = array_sum(array_column($data['cashInflows'], 'amount')) - array_sum(array_column($data['cashOutflows'], 'amount'));

        return view('info/reports/cash_flow', $data);
    }

    public function monthlySummary()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $year = $this->request->getGet('year') ?? date('Y');

        $data = [
            'title' => 'Monthly Summary - ' . $year,
            'year' => $year,
            'monthlyData' => $this->getMonthlyData($year)
        ];

        return view('info/reports/monthly_summary', $data);
    }

    public function productPerformance()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Product Performance Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productPerformance' => $this->getProductPerformance($startDate, $endDate)
        ];

        return view('info/reports/product_performance', $data);
    }

    public function customerAnalysis()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Customer Analysis Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'customerAnalysis' => $this->getCustomerAnalysis($startDate, $endDate)
        ];

        return view('info/reports/customer_analysis', $data);
    }

    // ─── Dashboard Summary Helpers ───────────────────────────────────

    private function getSalesThisMonth()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        return $this->saleModel
            ->select('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->where('created_at >=', $startOfMonth)
            ->where('created_at <=', $endOfMonth . ' 23:59:59')
            ->first();
    }

    private function getPurchasesThisMonth()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        return $this->purchaseOrderModel
            ->select('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->where('tanggal_po >=', $startOfMonth)
            ->where('tanggal_po <=', $endOfMonth)
            ->first();
    }

    private function getTotalSales()
    {
        $result = $this->saleModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('payment_status', 'PAID')
            ->asArray()
            ->first();
        return $result['total'] ?? 0;
    }

    private function getTotalPurchases()
    {
        $result = $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('status', 'Diterima Semua')
            ->asArray()
            ->first();
        return $result['total'] ?? 0;
    }

    private function getTotalReturns()
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Selesai')
            ->asArray()
            ->first()['total'] ?? 0;

        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Disetujui')
            ->asArray()
            ->first()['total'] ?? 0;

        return $salesReturns + $purchaseReturns;
    }

    private function getTopProducts($limit = 10)
    {
        return $this->saleModel
            ->select('products.id, products.name, products.sku, SUM(sale_items.quantity) as total_sold, SUM(sale_items.subtotal) as total_revenue')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.payment_status', 'PAID')
            ->groupBy('products.id, products.name, products.sku')
            ->orderBy('total_sold', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    private function getTopCustomers($limit = 10)
    {
        return $this->saleModel
            ->select('customers.id, customers.name, COUNT(*) as transaction_count, SUM(sales.total_amount) as total_spent')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_status', 'PAID')
            ->groupBy('customers.id, customers.name')
            ->orderBy('total_spent', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    private function getLowStockProducts($limit = 10)
    {
        return $this->productModel
            ->select('products.name, products.sku, SUM(product_stocks.quantity) as total_stock, products.min_stock_alert')
            ->join('product_stocks', 'product_stocks.product_id = products.id')
            ->groupBy('products.id, products.name, products.sku, products.min_stock_alert')
            ->having('total_stock <= min_stock_alert')
            ->orderBy('total_stock', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    // ─── Daily Report Helpers ────────────────────────────────────────

    private function getDailySales($date)
    {
        return $this->saleModel
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('DATE(sales.created_at)', $date)
            ->where('sales.payment_status', 'PAID')
            ->orderBy('sales.created_at', 'DESC')
            ->asArray()
            ->findAll();
    }

    private function getDailyPurchases($date)
    {
        return $this->purchaseOrderModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
            ->where('purchase_orders.tanggal_po', $date)
            ->where('purchase_orders.status', 'Diterima Semua')
            ->orderBy('purchase_orders.tanggal_po', 'DESC')
            ->asArray()
            ->findAll();
    }

    private function getDailyReturns($date)
    {
        $salesReturns = $this->salesReturnModel
            ->select('sales_returns.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales_returns.customer_id')
            ->where('sales_returns.tanggal_retur', $date)
            ->where('sales_returns.status', 'Selesai')
            ->asArray()
            ->findAll();

        $purchaseReturns = $this->purchaseReturnModel
            ->select('purchase_returns.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.tanggal_retur', $date)
            ->where('purchase_returns.status', 'Disetujui')
            ->asArray()
            ->findAll();

        return [
            'sales_returns' => $salesReturns,
            'purchase_returns' => $purchaseReturns
        ];
    }

    // ─── Profit & Loss Helpers ───────────────────────────────────────

    private function calculateRevenue($startDate, $endDate)
    {
        $result = $this->saleModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('payment_status', 'PAID')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->asArray()
            ->first();
        return $result['total'] ?? 0;
    }

    private function calculateCOGS($startDate, $endDate)
    {
        $result = $this->saleModel
            ->select('COALESCE(SUM(sale_items.quantity * products.price_buy), 0) as total')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.payment_status', 'PAID')
            ->where('DATE(sales.created_at) >=', $startDate)
            ->where('DATE(sales.created_at) <=', $endDate)
            ->asArray()
            ->first();
        return $result['total'] ?? 0;
    }

    private function calculateReturns($startDate, $endDate)
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->asArray()
            ->first()['total'] ?? 0;

        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Disetujui')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->asArray()
            ->first()['total'] ?? 0;

        return $salesReturns + $purchaseReturns;
    }

    private function calculateExpenses($startDate, $endDate)
    {
        return $this->expenseModel->getTotalExpenses($startDate, $endDate);
    }

    // ─── Cash Flow Helpers ───────────────────────────────────────────

    private function getCashInflows($startDate, $endDate)
    {
        $inflows = [];

        // Cash sales
        $cashSales = $this->saleModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('payment_type', 'CASH')
            ->where('payment_status', 'PAID')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->asArray()
            ->first();

        if (($cashSales['total'] ?? 0) > 0) {
            $inflows[] = [
                'type' => 'Cash Sales',
                'amount' => $cashSales['total']
            ];
        }

        // Customer payments (credit collections)
        $customerPayments = $this->saleModel
            ->select('COALESCE(SUM(paid_amount), 0) as total')
            ->where('payment_type', 'CREDIT')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->asArray()
            ->first();

        if (($customerPayments['total'] ?? 0) > 0) {
            $inflows[] = [
                'type' => 'Credit Collections',
                'amount' => $customerPayments['total']
            ];
        }

        return $inflows;
    }

    private function getCashOutflows($startDate, $endDate)
    {
        $outflows = [];

        // Purchases
        $cashPurchases = $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('status', 'Diterima Semua')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate)
            ->asArray()
            ->first();

        if (($cashPurchases['total'] ?? 0) > 0) {
            $outflows[] = [
                'type' => 'Purchases',
                'amount' => $cashPurchases['total']
            ];
        }

        // Sales Returns (refunds)
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->asArray()
            ->first();

        if (($salesReturns['total'] ?? 0) > 0) {
            $outflows[] = [
                'type' => 'Sales Returns',
                'amount' => $salesReturns['total']
            ];
        }

        // Expenses
        $expenses = $this->expenseModel->getTotalExpenses($startDate, $endDate);

        if ($expenses > 0) {
            $outflows[] = [
                'type' => 'Expenses',
                'amount' => $expenses
            ];
        }

        return $outflows;
    }

    // ─── Monthly Summary (batch queries, not N×12 loop) ──────────────

    private function getMonthlyData($year)
    {
        $db = \Config\Database::connect();

        // Query 1: Monthly revenue + sales count (1 query instead of 24)
        $monthlySales = $db->table('sales')
            ->select('MONTH(created_at) as month, COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as sales_count')
            ->where('payment_status', 'PAID')
            ->where('YEAR(created_at)', $year)
            ->groupBy('MONTH(created_at)')
            ->get()->getResultArray();

        // Query 2: Monthly COGS (1 query instead of 12)
        $monthlyCOGS = $db->table('sales')
            ->select('MONTH(sales.created_at) as month, COALESCE(SUM(sale_items.quantity * products.price_buy), 0) as cogs')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.payment_status', 'PAID')
            ->where('YEAR(sales.created_at)', $year)
            ->groupBy('MONTH(sales.created_at)')
            ->get()->getResultArray();

        // Query 3: Monthly sales returns (1 query instead of 12)
        $monthlySalesReturns = $db->table('sales_returns')
            ->select('MONTH(tanggal_retur) as month, COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Selesai')
            ->where('YEAR(tanggal_retur)', $year)
            ->where('deleted_at', null)
            ->groupBy('MONTH(tanggal_retur)')
            ->get()->getResultArray();

        // Query 4: Monthly purchase returns (1 query instead of 12)
        $monthlyPurchaseReturns = $db->table('purchase_returns')
            ->select('MONTH(tanggal_retur) as month, COALESCE(SUM(total_retur), 0) as total')
            ->where('status', 'Disetujui')
            ->where('YEAR(tanggal_retur)', $year)
            ->where('deleted_at', null)
            ->groupBy('MONTH(tanggal_retur)')
            ->get()->getResultArray();

        // Query 5: Monthly purchase count (1 query instead of 12)
        $monthlyPurchases = $db->table('purchase_orders')
            ->select('MONTH(tanggal_po) as month, COUNT(*) as purchase_count')
            ->where('status', 'Diterima Semua')
            ->where('YEAR(tanggal_po)', $year)
            ->where('deleted_at', null)
            ->groupBy('MONTH(tanggal_po)')
            ->get()->getResultArray();

        // Query 6: Monthly expenses (1 query instead of 12)
        $monthlyExpenses = $db->table('expenses')
            ->select('MONTH(expense_date) as month, COALESCE(SUM(amount), 0) as total')
            ->where('YEAR(expense_date)', $year)
            ->groupBy('MONTH(expense_date)')
            ->get()->getResultArray();

        // Index results by month for O(1) lookup
        $salesByMonth = array_column($monthlySales, null, 'month');
        $cogsByMonth = array_column($monthlyCOGS, null, 'month');
        $salesReturnsByMonth = array_column($monthlySalesReturns, null, 'month');
        $purchaseReturnsByMonth = array_column($monthlyPurchaseReturns, null, 'month');
        $purchasesByMonth = array_column($monthlyPurchases, null, 'month');
        $expensesByMonth = array_column($monthlyExpenses, null, 'month');

        // Build monthly data array
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $revenue = (float)($salesByMonth[$month]['revenue'] ?? 0);
            $cogs = (float)($cogsByMonth[$month]['cogs'] ?? 0);
            $salesReturnsTotal = (float)($salesReturnsByMonth[$month]['total'] ?? 0);
            $purchaseReturnsTotal = (float)($purchaseReturnsByMonth[$month]['total'] ?? 0);
            $returns = $salesReturnsTotal + $purchaseReturnsTotal;
            $expenses = (float)($expensesByMonth[$month]['total'] ?? 0);
            $grossProfit = $revenue - $cogs - $returns;

            $data[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'revenue' => $revenue,
                'cogs' => $cogs,
                'returns' => $returns,
                'gross_profit' => $grossProfit,
                'expenses' => $expenses,
                'net_profit' => $grossProfit - $expenses,
                'sales_count' => (int)($salesByMonth[$month]['sales_count'] ?? 0),
                'purchase_count' => (int)($purchasesByMonth[$month]['purchase_count'] ?? 0)
            ];
        }

        return $data;
    }

    // ─── Product & Customer Analysis ─────────────────────────────────

    private function getProductPerformance($startDate, $endDate)
    {
        return $this->saleModel
            ->select('products.id, products.name, products.sku,
                    SUM(sale_items.quantity) as total_sold,
                    SUM(sale_items.subtotal) as revenue,
                    COUNT(DISTINCT sales.id) as sales_count,
                    AVG(sale_items.price) as avg_price')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.payment_status', 'PAID')
            ->where('DATE(sales.created_at) >=', $startDate)
            ->where('DATE(sales.created_at) <=', $endDate)
            ->groupBy('products.id, products.name, products.sku')
            ->orderBy('revenue', 'DESC')
            ->findAll();
    }

    private function getCustomerAnalysis($startDate, $endDate)
    {
        return $this->saleModel
            ->select('customers.id, customers.name,
                    COUNT(*) as transaction_count,
                    SUM(sales.total_amount) as total_spent,
                    AVG(sales.total_amount) as avg_transaction_value,
                    MIN(DATE(sales.created_at)) as first_transaction,
                    MAX(DATE(sales.created_at)) as last_transaction')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_status', 'PAID')
            ->where('DATE(sales.created_at) >=', $startDate)
            ->where('DATE(sales.created_at) <=', $endDate)
            ->groupBy('customers.id, customers.name')
            ->orderBy('total_spent', 'DESC')
            ->findAll();
    }

    // ─── Stock Card Report ───────────────────────────────────────────

    public function stockCard()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $productId = $this->request->getGet('product_id');
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Stock Card Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'products' => $this->productModel->findAll(),
            'productId' => $productId,
            'movements' => $productId ? $this->getStockMovements($productId, $startDate, $endDate) : [],
            'summary' => $productId ? $this->getStockSummaryData($productId, $startDate, $endDate) : null
        ];

        return view('info/reports/stock_card', $data);
    }

    private function getStockMovements($productId, $startDate, $endDate)
    {
        return $this->stockMutationModel
            ->select('stock_mutations.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->where('stock_mutations.product_id', $productId)
            ->where('stock_mutations.created_at >=', $startDate)
            ->where('stock_mutations.created_at <=', $endDate . ' 23:59:59')
            ->orderBy('stock_mutations.created_at', 'ASC')
            ->asArray()
            ->findAll();
    }

    private function getStockSummaryData($productId, $startDate, $endDate)
    {
        $db = \Config\Database::connect();

        // Beginning balance: sum of all movements before start date
        $beginRow = $db->table('stock_mutations')
            ->select('COALESCE(SUM(CASE WHEN type IN ("IN","ADJUSTMENT_IN") THEN quantity ELSE -quantity END), 0) as balance')
            ->where('product_id', $productId)
            ->where('created_at <', $startDate)
            ->get()->getRow();
        $beginning = (float)($beginRow->balance ?? 0);

        // Period totals
        $periodRow = $db->table('stock_mutations')
            ->select('
                COALESCE(SUM(CASE WHEN type IN ("IN","ADJUSTMENT_IN") THEN quantity ELSE 0 END), 0) as total_in,
                COALESCE(SUM(CASE WHEN type IN ("OUT","ADJUSTMENT_OUT") THEN quantity ELSE 0 END), 0) as total_out
            ')
            ->where('product_id', $productId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()->getRow();

        $totalIn = (float)($periodRow->total_in ?? 0);
        $totalOut = (float)($periodRow->total_out ?? 0);

        return [
            'beginning_balance' => $beginning,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'ending_balance' => $beginning + $totalIn - $totalOut
        ];
    }

    // ─── Aging Analysis ──────────────────────────────────────────────

    public function agingAnalysis()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $asOfDate = $this->request->getGet('as_of_date') ?? date('Y-m-d');

        $data = [
            'title' => 'Aging Analysis Report',
            'asOfDate' => $asOfDate,
            'agingBuckets' => $this->getAgingBuckets($asOfDate),
            'totalOutstanding' => $this->getTotalOutstandingReceivables($asOfDate)
        ];

        return view('info/reports/aging_analysis', $data);
    }

    private function getAgingBuckets($asOfDate)
    {
        $today = strtotime($asOfDate);
        $thirtyDaysAgo = $today - (30 * 86400);
        $sixtyDaysAgo = $today - (60 * 86400);
        $ninetyDaysAgo = $today - (90 * 86400);

        return [
            'current' => [
                'label' => 'Current (0-30 days)',
                'from_date' => date('Y-m-d', $thirtyDaysAgo),
                'to_date' => $asOfDate,
                'data' => $this->getOutstandingByDateRange(date('Y-m-d', $thirtyDaysAgo), $asOfDate)
            ],
            'thirty_sixty' => [
                'label' => '31-60 days',
                'from_date' => date('Y-m-d', $sixtyDaysAgo),
                'to_date' => date('Y-m-d', $thirtyDaysAgo - 1),
                'data' => $this->getOutstandingByDateRange(date('Y-m-d', $sixtyDaysAgo), date('Y-m-d', $thirtyDaysAgo - 1))
            ],
            'sixty_ninety' => [
                'label' => '61-90 days',
                'from_date' => date('Y-m-d', $ninetyDaysAgo),
                'to_date' => date('Y-m-d', $sixtyDaysAgo - 1),
                'data' => $this->getOutstandingByDateRange(date('Y-m-d', $ninetyDaysAgo), date('Y-m-d', $sixtyDaysAgo - 1))
            ],
            'over_ninety' => [
                'label' => 'Over 90 days',
                'from_date' => '2000-01-01',
                'to_date' => date('Y-m-d', $ninetyDaysAgo - 1),
                'data' => $this->getOutstandingByDateRange('2000-01-01', date('Y-m-d', $ninetyDaysAgo - 1))
            ]
        ];
    }

    private function getOutstandingByDateRange($startDate, $endDate)
    {
        return $this->saleModel
            ->select('customers.id, customers.name, customers.phone,
                    SUM(CASE WHEN sales.payment_status IN ("UNPAID","PARTIAL") THEN (sales.total_amount - sales.paid_amount) ELSE 0 END) as outstanding_amount,
                    MAX(sales.created_at) as last_transaction_date,
                    COUNT(DISTINCT sales.id) as invoice_count')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.created_at >=', $startDate)
            ->where('sales.created_at <=', $endDate . ' 23:59:59')
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.payment_type', 'CREDIT')
            ->groupBy('customers.id, customers.name, customers.phone')
            ->orderBy('outstanding_amount', 'DESC')
            ->findAll();
    }

    private function getTotalOutstandingReceivables($asOfDate)
    {
        $result = $this->saleModel
            ->select('COALESCE(SUM(CASE WHEN sales.payment_status IN ("UNPAID","PARTIAL") THEN (sales.total_amount - sales.paid_amount) ELSE 0 END), 0) as total')
            ->where('sales.payment_type', 'CREDIT')
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.created_at <=', $asOfDate . ' 23:59:59')
            ->asArray()
            ->first();
        return $result['total'] ?? 0;
    }

    /**
     * AJAX: Get stock card data
     */
    public function getStockCardData()
    {
        $productId = $this->request->getGet('product_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$productId || !$startDate || !$endDate) {
            return $this->respondError('Missing parameters');
        }

        $movements = $this->getStockMovements($productId, $startDate, $endDate);
        $summary = $this->getStockSummaryData($productId, $startDate, $endDate);

        return $this->respondData([
            'movements' => $movements,
            'summary' => $summary
        ]);
    }
}
