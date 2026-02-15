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
        $includeHidden = session()->get('role') === 'OWNER' && $this->request->getGet('include_hidden') === '1';

        $sales = $this->getDailySales($date, $includeHidden);
        $purchases = $this->getDailyPurchases($date, $includeHidden);
        $returns = $this->getDailyReturns($date, $includeHidden);

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
            ],
            'isOwner' => session()->get('role') === 'OWNER'
        ];

        // CSV Export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportDailyReport($data, $date);
        }

        return view('info/reports/daily', $data);
    }

    public function profitLoss()
    {
        if (session()->get('role') !== 'OWNER') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $includeHidden = $this->request->getGet('include_hidden') === '1';

        $data = [
            'title' => 'Profit & Loss Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenue' => $this->calculateRevenue($startDate, $endDate, $includeHidden),
            'cogs' => $this->calculateCOGS($startDate, $endDate, $includeHidden),
            'returns' => $this->calculateReturns($startDate, $endDate, $includeHidden),
            'grossProfit' => 0,
            'expenses' => $this->calculateExpenses($startDate, $endDate),
            'netProfit' => 0
        ];

        $data['grossProfit'] = $data['revenue'] - $data['cogs'] - $data['returns'];
        $data['netProfit'] = $data['grossProfit'] - $data['expenses'];

        // CSV Export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportProfitLossReport($data, $startDate, $endDate);
        }

        return view('info/reports/profit_loss', $data);
    }

    public function cashFlow()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $includeHidden = $this->request->getGet('include_hidden') === '1';

        $data = [
            'title' => 'Cash Flow Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'cashInflows' => $this->getCashInflows($startDate, $endDate, $includeHidden),
            'cashOutflows' => $this->getCashOutflows($startDate, $endDate, $includeHidden),
            'netCashFlow' => 0
        ];

        $data['netCashFlow'] = array_sum(array_column($data['cashInflows'], 'amount')) - array_sum(array_column($data['cashOutflows'], 'amount'));

        // CSV Export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportCashFlowReport($data, $startDate, $endDate);
        }

        return view('info/reports/cash_flow', $data);
    }

    public function monthlySummary()
    {
        if (!in_array(session()->get('role'), ['OWNER', 'ADMIN'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $year = $this->request->getGet('year') ?? date('Y');
        $includeHidden = $this->request->getGet('include_hidden') === '1';

        $data = [
            'title' => 'Monthly Summary - ' . $year,
            'year' => $year,
            'monthlyData' => $this->getMonthlySummary($year, $includeHidden),
            'isOwner' => session()->get('role') === 'OWNER'
        ];

        // CSV Export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportMonthlySummary($data, $year);
        }

        return view('info/reports/monthly_summary', $data);
    }

    // CSV Export Methods
    private function exportDailyReport($data, $date)
    {
        $csvData = [];
        
        // Sales data
        foreach ($data['sales'] as $sale) {
            $csvData[] = [
                'Type' => 'Penjualan',
                'Date' => $date,
                'Invoice' => $sale['invoice_number'] ?? '',
                'Customer' => $sale['customer_name'] ?? '',
                'Amount' => $sale['total_amount'] ?? 0
            ];
        }
        
        // Purchase data
        foreach ($data['purchases'] as $purchase) {
            $csvData[] = [
                'Type' => 'Pembelian',
                'Date' => $date,
                'PO Number' => $purchase['nomor_po'] ?? '',
                'Supplier' => $purchase['supplier_name'] ?? '',
                'Amount' => $purchase['total_amount'] ?? 0
            ];
        }
        
        // Returns data
        foreach ($data['returns']['sales_returns'] as $return) {
            $csvData[] = [
                'Type' => 'Retur Penjualan',
                'Date' => $date,
                'Return Number' => $return['nomor_retur'] ?? '',
                'Customer' => $return['customer_name'] ?? '',
                'Amount' => $return['total_retur'] ?? 0
            ];
        }
        
        foreach ($data['returns']['purchase_returns'] as $return) {
            $csvData[] = [
                'Type' => 'Retur Pembelian',
                'Date' => $date,
                'Return Number' => $return['nomor_retur'] ?? '',
                'Supplier' => $return['supplier_name'] ?? '',
                'Amount' => $return['total_retur'] ?? 0
            ];
        }
        
        return $this->response->setCSV($csvData, "daily_report_{$date}.csv");
    }

    private function exportProfitLossReport($data, $startDate, $endDate)
    {
        $csvData = [
            ['Category', 'Amount', 'Description']
        ];
        
        $csvData[] = ['Revenue', $data['revenue'], 'Total sales revenue'];
        $csvData[] = ['COGS', $data['cogs'], 'Cost of goods sold'];
        $csvData[] = ['Returns', $data['returns'], 'Total returns'];
        $csvData[] = ['Gross Profit', $data['grossProfit'], 'Revenue - COGS - Returns'];
        $csvData[] = ['Expenses', $data['expenses'], 'Operating expenses'];
        $csvData[] = ['Net Profit', $data['netProfit'], 'Gross Profit - Expenses'];
        
        return $this->response->setCSV($csvData, "profit_loss_{$startDate}_to_{$endDate}.csv");
    }

    private function exportCashFlowReport($data, $startDate, $endDate)
    {
        $csvData = [['Type', 'Description', 'Amount', 'Date']];
        
        // Cash inflows
        foreach ($data['cashInflows'] as $inflow) {
            $csvData[] = [
                'Inflow',
                $inflow['description'] ?? '',
                $inflow['amount'],
                $inflow['date'] ?? ''
            ];
        }
        
        // Cash outflows
        foreach ($data['cashOutflows'] as $outflow) {
            $csvData[] = [
                'Outflow',
                $outflow['description'] ?? '',
                $outflow['amount'],
                $outflow['date'] ?? ''
            ];
        }
        
        // Summary
        $csvData[] = ['', '', '', ''];
        $csvData[] = ['Summary', 'Total Inflow', array_sum(array_column($data['cashInflows'], 'amount')), ''];
        $csvData[] = ['Summary', 'Total Outflow', array_sum(array_column($data['cashOutflows'], 'amount')), ''];
        $csvData[] = ['Summary', 'Net Cash Flow', $data['netCashFlow'], ''];
        
        return $this->response->setCSV($csvData, "cash_flow_{$startDate}_to_{$endDate}.csv");
    }

    private function exportMonthlySummary($data, $year)
    {
        $csvData = [['Month', 'Sales', 'Purchases', 'Net Profit']];
        
        foreach ($data['monthlyData'] as $month => $monthData) {
            $csvData[] = [
                $month,
                $monthData['sales'] ?? 0,
                $monthData['purchases'] ?? 0,
                $monthData['profit'] ?? 0
            ];
        }
        
        return $this->response->setCSV($csvData, "monthly_summary_{$year}.csv");
    }

    // Helper Methods (existing methods with includeHidden parameter)
    private function getDailySales($date, $includeHidden = false)
    {
        $builder = $this->saleModel
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('DATE(sales.created_at)', $date);
            
        if (!$includeHidden) {
            $builder->where('sales.is_hidden', 0);
        }
        
        return $builder->findAll();
    }

    private function getDailyPurchases($date, $includeHidden = false)
    {
        $builder = $this->purchaseOrderModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->where('DATE(purchase_orders.tanggal_po)', $date);
            
        if (!$includeHidden) {
            $builder->where('purchase_orders.is_hidden', 0);
        }
        
        return $builder->findAll();
    }

    private function getDailyReturns($date, $includeHidden = false)
    {
        $salesReturns = $this->salesReturnModel
            ->select('sales_returns.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales_returns.customer_id', 'left')
            ->where('DATE(sales_returns.created_at)', $date);
            
        if (!$includeHidden) {
            $salesReturns->where('sales_returns.is_hidden', 0);
        }
        
        $purchaseReturns = $this->purchaseReturnModel
            ->select('purchase_returns.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id', 'left')
            ->where('DATE(purchase_returns.created_at)', $date);
            
        if (!$includeHidden) {
            $purchaseReturns->where('purchase_returns.is_hidden', 0);
        }
        
        return [
            'sales_returns' => $salesReturns->findAll(),
            'purchase_returns' => $purchaseReturns->findAll()
        ];
    }

    // Add includeHidden parameter to other existing methods as needed
    private function calculateRevenue($startDate, $endDate, $includeHidden = false)
    {
        $builder = $this->saleModel
            ->select('SUM(total_amount) as total')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate);
            
        if (!$includeHidden) {
            $builder->where('is_hidden', 0);
        }
        
        $result = $builder->first();
        return $result['total'] ?? 0;
    }

    private function calculateCOGS($startDate, $endDate, $includeHidden = false)
    {
        $builder = $this->purchaseOrderModel
            ->select('SUM(total_amount) as total')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate);
            
        if (!$includeHidden) {
            $builder->where('is_hidden', 0);
        }
        
        $result = $builder->first();
        return $result['total'] ?? 0;
    }

    private function getCashInflows($startDate, $endDate, $includeHidden = false)
    {
        $builder = $this->saleModel
            ->select('total_amount as amount, created_at as date, "Sales Payment" as description')
            ->where('payment_type', 'CASH')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate);
            
        if (!$includeHidden) {
            $builder->where('is_hidden', 0);
        }
        
        return $builder->findAll();
    }

    private function getCashOutflows($startDate, $endDate, $includeHidden = false)
    {
        $builder = $this->purchaseOrderModel
            ->select('total_amount as amount, tanggal_po as date, "Purchase Payment" as description')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate);
            
        if (!$includeHidden) {
            $builder->where('is_hidden', 0);
        }
        
        return $builder->findAll();
    }

    private function getMonthlySummary($year, $includeHidden = false)
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $startDate = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
            $endDate = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
            
            $sales = $this->calculateRevenue($startDate, $endDate, $includeHidden);
            $purchases = $this->calculateCOGS($startDate, $endDate, $includeHidden);
            
            $monthlyData[date('F', mktime(0, 0, 0, $month, 1, $year))] = [
                'sales' => $sales,
                'purchases' => $purchases,
                'profit' => $sales - $purchases
            ];
        }
        
        return $monthlyData;
    }

    // Other existing methods remain the same...
    private function getSalesThisMonth() { /* existing implementation */ }
    private function getPurchasesThisMonth() { /* existing implementation */ }
    private function getTotalSales() { /* existing implementation */ }
    private function getTotalPurchases() { /* existing implementation */ }
    private function getTotalReturns() { /* existing implementation */ }
    private function getTopProducts($limit) { /* existing implementation */ }
    private function getTopCustomers($limit) { /* existing implementation */ }
    private function getLowStockProducts($limit) { /* existing implementation */ }
    private function calculateReturns($startDate, $endDate, $includeHidden = false) { /* existing implementation */ }
    private function calculateExpenses($startDate, $endDate) { /* existing implementation */ }
}