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

class Reports extends BaseController
{
    protected $saleModel;
    protected $purchaseOrderModel;
    protected $salesReturnModel;
    protected $purchaseReturnModel;
    protected $productModel;
    protected $customerModel;
    protected $stockMutationModel;
    
    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->salesReturnModel = new SalesReturnModel();
        $this->purchaseReturnModel = new PurchaseReturnModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->stockMutationModel = new StockMutationModel();
    }
    
    public function index()
    {
        // Check if user is Owner for full reports
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
        // Check if user is Owner or Admin
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
                'total_sales' => array_sum(array_column($sales, 'final_amount')),
                'total_purchases' => array_sum(array_column($purchases, 'total_amount')),
                'total_returns' => array_sum(array_column($returns['sales_returns'], 'final_amount')) + array_sum(array_column($returns['purchase_returns'], 'final_amount')),
                'transaction_count' => count($sales) + count($purchases) + count($returns['sales_returns']) + count($returns['purchase_returns']),
            ]
        ];
        
        return view('info/reports/daily', $data);
    }
    
    public function profitLoss()
    {
        // Check if user is Owner
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
        // Check if user is Owner or Admin
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
        // Check if user is Owner or Admin
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
        // Check if user is Owner, Admin, or Warehouse staff
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
        // Check if user is Owner, Admin, or Sales staff
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
    
    private function getSalesThisMonth()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        return $this->saleModel
            ->select('COUNT(*) as count, COALESCE(SUM(final_amount), 0) as total')
            ->where('date >=', $startOfMonth)
            ->where('date <=', $endOfMonth)
            ->first();
    }
    
    private function getPurchasesThisMonth()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        return $this->purchaseOrderModel
            ->select('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->where('date >=', $startOfMonth)
            ->where('date <=', $endOfMonth)
            ->first();
    }
    
    private function getTotalSales()
    {
        return $this->saleModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('payment_status', 'PAID')
            ->first()['total'] ?? 0;
    }
    
    private function getTotalPurchases()
    {
        return $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_amount), 0) as total')
            ->where('status', 'RECEIVED')
            ->first()['total'] ?? 0;
    }
    
    private function getTotalReturns()
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('status', 'APPROVED')
            ->first()['total'] ?? 0;
            
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('status', 'APPROVED')
            ->first()['total'] ?? 0;
            
        return $salesReturns + $purchaseReturns;
    }
    
    private function getTopProducts($limit = 10)
    {
        return $this->saleModel
            ->select('products.id, products.name, products.sku, SUM(sale_items.quantity) as total_sold, SUM(sale_items.total_price) as total_revenue')
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
            ->select('customers.id, customers.name, COUNT(*) as transaction_count, SUM(sales.final_amount) as total_spent')
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
            ->where('purchase_orders.status', 'RECEIVED')
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
            ->where('sales_returns.status', 'Disetujui')
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
    
    private function calculateRevenue($startDate, $endDate)
    {
        return $this->saleModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('payment_status', 'PAID')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first()['total'] ?? 0;
    }
    
    private function calculateCOGS($startDate, $endDate)
    {
        // For simplicity, using purchase price * quantity sold
        // In real implementation, this should be more complex with FIFO/LIFO calculation
        return $this->saleModel
            ->select('COALESCE(SUM(sale_items.quantity * products.price_buy), 0) as total')
            ->join('sale_items', 'sale_items.sale_id = sales.id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.payment_status', 'PAID')
            ->where('DATE(sales.created_at) >=', $startDate)
            ->where('DATE(sales.created_at) <=', $endDate)
            ->first()['total'] ?? 0;
    }
    
    private function calculateReturns($startDate, $endDate)
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('status', 'APPROVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first()['total'] ?? 0;
            
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total')
            ->where('status', 'APPROVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first()['total'] ?? 0;
            
        return $salesReturns + $purchaseReturns;
    }
    
    private function calculateExpenses($startDate, $endDate)
    {
        // This would calculate operational expenses
        // For now, returning a placeholder
        return 0;
    }
    
    private function getCashInflows($startDate, $endDate)
    {
        $inflows = [];
        
        // Cash sales
        $cashSales = $this->saleModel
            ->select('COALESCE(SUM(final_amount), 0) as total, "Cash Sales" as type')
            ->where('payment_type', 'CASH')
            ->where('payment_status', 'PAID')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first();
        
        if ($cashSales['total'] > 0) {
            $inflows[] = [
                'type' => $cashSales['type'],
                'amount' => $cashSales['total']
            ];
        }
        
        // Customer payments (credit)
        $customerPayments = $this->saleModel
            ->select('COALESCE(SUM(paid_amount), 0) as total, "Credit Collections" as type')
            ->where('payment_type', 'CREDIT')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first();
        
        if ($customerPayments['total'] > 0) {
            $inflows[] = [
                'type' => $customerPayments['type'],
                'amount' => $customerPayments['total']
            ];
        }
        
        return $inflows;
    }
    
    private function getCashOutflows($startDate, $endDate)
    {
        $outflows = [];
        
        // Cash purchases
        $cashPurchases = $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_amount), 0) as total, "Purchases" as type')
            ->where('status', 'RECEIVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first();
        
        if ($cashPurchases['total'] > 0) {
            $outflows[] = [
                'type' => $cashPurchases['type'],
                'amount' => $cashPurchases['total']
            ];
        }
        
        // Returns (refunds)
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total, "Sales Returns" as type')
            ->where('status', 'APPROVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first();
        
        if ($salesReturns['total'] > 0) {
            $outflows[] = [
                'type' => $salesReturns['type'],
                'amount' => $salesReturns['total']
            ];
        }
        
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(final_amount), 0) as total, "Purchase Returns" as type')
            ->where('status', 'APPROVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->first();
        
        if ($purchaseReturns['total'] > 0) {
            $outflows[] = [
                'type' => $purchaseReturns['type'],
                'amount' => $purchaseReturns['total']
            ];
        }
        
        return $outflows;
    }
    
    private function getMonthlyData($year)
    {
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $startDate = date("$year-m-01", mktime(0, 0, 0, $month, 1));
            $endDate = date("$year-m-t", mktime(0, 0, 0, $month, 1));
            
            $monthData = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'revenue' => $this->calculateRevenue($startDate, $endDate),
                'cogs' => $this->calculateCOGS($startDate, $endDate),
                'returns' => $this->calculateReturns($startDate, $endDate),
                'gross_profit' => 0,
                'expenses' => $this->calculateExpenses($startDate, $endDate),
                'net_profit' => 0,
                'sales_count' => $this->getSalesCount($startDate, $endDate),
                'purchase_count' => $this->getPurchaseCount($startDate, $endDate)
            ];
            
            $monthData['gross_profit'] = $monthData['revenue'] - $monthData['cogs'] - $monthData['returns'];
            $monthData['net_profit'] = $monthData['gross_profit'] - $monthData['expenses'];
            
            $data[] = $monthData;
        }
        
        return $data;
    }
    
    private function getSalesCount($startDate, $endDate)
    {
        return $this->saleModel
            ->where('payment_status', 'PAID')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->countAllResults();
    }
    
    private function getPurchaseCount($startDate, $endDate)
    {
        return $this->purchaseOrderModel
            ->where('status', 'RECEIVED')
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->countAllResults();
    }
    
    private function getProductPerformance($startDate, $endDate)
    {
        return $this->saleModel
            ->select('products.id, products.name, products.sku, 
                    SUM(sale_items.quantity) as total_sold, 
                    SUM(sale_items.total_price) as revenue,
                    COUNT(DISTINCT sales.id) as sales_count,
                    AVG(sale_items.unit_price) as avg_price')
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

    /**
     * Stock Card Report - Complete stock movement history for a product
     */
    public function stockCard()
    {
        // Check if user is Owner, Admin, or Warehouse staff
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
            'summary' => $productId ? $this->getStockSummary($productId, $startDate, $endDate) : null
        ];

        return view('info/reports/stock_card', $data);
    }

    /**
     * Get stock movements for a product within date range
     */
    private function getStockMovements($productId, $startDate, $endDate)
    {
        return $this->stockMutationModel
            ->select('stock_mutations.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->where('stock_mutations.product_id', $productId)
            ->where('stock_mutations.created_at >=', $startDate)
            ->where('stock_mutations.created_at <=', $endDate)
            ->orderBy('stock_mutations.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get stock summary for a product
     */
    private function getStockSummary($productId, $startDate, $endDate)
    {
        $beginning = $this->stockMutationModel
            ->select('COALESCE(SUM(qty_in) - SUM(qty_out), 0) as balance')
            ->where('product_id', $productId)
            ->where('created_at <', $startDate)
            ->first()['balance'] ?? 0;

        $totalIn = $this->stockMutationModel
            ->select('COALESCE(SUM(qty_in), 0) as total')
            ->where('product_id', $productId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->first()['total'] ?? 0;

        $totalOut = $this->stockMutationModel
            ->select('COALESCE(SUM(qty_out), 0) as total')
            ->where('product_id', $productId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->first()['total'] ?? 0;

        return [
            'beginning_balance' => $beginning,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'ending_balance' => $beginning + $totalIn - $totalOut
        ];
    }

    /**
     * Aging Analysis Report - Shows outstanding receivables by age
     */
    public function agingAnalysis()
    {
        // Check if user is Owner or Admin
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

    /**
     * Get aging buckets for receivables
     */
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

    /**
     * Get outstanding receivables for a date range
     */
    private function getOutstandingByDateRange($startDate, $endDate)
    {
        return $this->saleModel
            ->select('customers.id, customers.name, customers.phone,
                    SUM(CASE WHEN sales.payment_status = "UNPAID" THEN (sales.total_amount - sales.paid_amount) 
                        WHEN sales.payment_status = "PARTIAL" THEN (sales.total_amount - sales.paid_amount) 
                        ELSE 0 END) as outstanding_amount,
                    MAX(sales.created_at) as last_transaction_date,
                    COUNT(DISTINCT sales.id) as invoice_count')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.created_at >=', $startDate)
            ->where('sales.created_at <=', $endDate)
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.payment_type', 'CREDIT')
            ->groupBy('customers.id, customers.name, customers.phone')
            ->orderBy('outstanding_amount', 'DESC')
            ->findAll();
    }

    /**
     * Get total outstanding receivables
     */
    private function getTotalOutstandingReceivables($asOfDate)
    {
        return $this->saleModel
            ->select('COALESCE(SUM(CASE WHEN sales.payment_status = "UNPAID" THEN (sales.total_amount - sales.paid_amount) 
                        WHEN sales.payment_status = "PARTIAL" THEN (sales.total_amount - sales.paid_amount) 
                        ELSE 0 END), 0) as total')
            ->where('sales.payment_type', 'CREDIT')
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.created_at <=', $asOfDate)
            ->first()['total'] ?? 0;
    }

    /**
     * AJAX: Get stock movements for autocomplete
     */
    public function getStockCardData()
    {
        $productId = $this->request->getGet('product_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$productId || !$startDate || !$endDate) {
            return $this->response->setJSON(['error' => 'Missing parameters']);
        }

        $movements = $this->getStockMovements($productId, $startDate, $endDate);
        $summary = $this->getStockSummary($productId, $startDate, $endDate);

        return $this->response->setJSON([
            'movements' => $movements,
            'summary' => $summary
        ]);
    }
}