<?php

namespace App\Controllers\Info;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    protected $salesModel;
    protected $purchaseOrderModel;
    protected $salesReturnModel;
    protected $purchaseReturnModel;
    protected $salesDetailModel;
    protected $purchaseOrderDetailModel;
    protected $stockMutationModel;
    protected $productModel;
    
    public function __construct()
    {
        $this->salesModel = new \App\Models\SalesModel();
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->salesReturnModel = new \App\Models\SalesReturnModel();
        $this->purchaseReturnModel = new \App\Models\PurchaseReturnModel();
        $this->salesDetailModel = new \App\Models\SalesDetailModel();
        $this->purchaseOrderDetailModel = new \App\Models\PurchaseOrderDetailModel();
        $this->stockMutationModel = new \App\Models\StockMutationModel();
        $this->productModel = new \App\Models\ProductModel();
    }
    
    public function index()
    {
        // Check if user is Owner for profit/loss access
        if (session()->get('role') !== 'Owner') {
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
    
    public function profitLoss()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
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
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
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
        
        $data['netCashFlow'] = array_sum($data['cashInflows']) - array_sum($data['cashOutflows']);
        
        return view('info/reports/cash_flow', $data);
    }
    
    public function monthlySummary()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
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
        if (!in_array(session()->get('role'), ['Owner', 'Admin', 'Gudang'])) {
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
        if (!in_array(session()->get('role'), ['Owner', 'Admin', 'Sales'])) {
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
        
        return $this->salesModel
            ->select('COUNT(*) as count, COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_penjualan >=', $startOfMonth)
            ->where('tanggal_penjualan <=', $endOfMonth)
            ->first();
    }
    
    private function getPurchasesThisMonth()
    {
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        return $this->purchaseOrderModel
            ->select('COUNT(*) as count, COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Diterima Semua')
            ->where('tanggal_po >=', $startOfMonth)
            ->where('tanggal_po <=', $endOfMonth)
            ->first();
    }
    
    private function getTotalSales()
    {
        return $this->salesModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Selesai')
            ->first()['total'] ?? 0;
    }
    
    private function getTotalPurchases()
    {
        return $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Diterima Semua')
            ->first()['total'] ?? 0;
    }
    
    private function getTotalReturns()
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->first()['total'] ?? 0;
            
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->first()['total'] ?? 0;
            
        return $salesReturns + $purchaseReturns;
    }
    
    private function getTopProducts($limit = 10)
    {
        return $this->salesDetailModel
            ->select('products.id_produk, products.nama_produk, products.kode_produk, SUM(penjualan_detail.jumlah) as total_sold, SUM(penjualan_detail.subtotal) as revenue')
            ->join('products', 'products.id_produk = penjualan_detail.id_produk')
            ->join('penjualan', 'penjualan.id_penjualan = penjualan_detail.id_penjualan')
            ->where('penjualan.status', 'Selesai')
            ->groupBy('products.id_produk, products.nama_produk, products.kode_produk')
            ->orderBy('total_sold', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    private function getTopCustomers($limit = 10)
    {
        return $this->salesModel
            ->select('customers.id_customer, customers.nama_customer, COUNT(*) as transaction_count, SUM(total_bayar) as total_spent')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.status', 'Selesai')
            ->groupBy('customers.id_customer, customers.nama_customer')
            ->orderBy('total_spent', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    private function getLowStockProducts($limit = 10)
    {
        return $this->productModel
            ->select('id_produk, nama_produk, kode_produk, stok, minimal_stok, status')
            ->where('stok <= minimal_stok')
            ->where('status', 'Aktif')
            ->orderBy('stok', 'ASC')
            ->limit($limit)
            ->findAll();
    }
    
    private function calculateRevenue($startDate, $endDate)
    {
        return $this->salesModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_penjualan >=', $startDate)
            ->where('tanggal_penjualan <=', $endDate)
            ->first()['total'] ?? 0;
    }
    
    private function calculateCOGS($startDate, $endDate)
    {
        return $this->salesDetailModel
            ->select('COALESCE(SUM(penjualan_detail.jumlah * products.harga_beli_terakhir), 0) as total')
            ->join('penjualan', 'penjualan.id_penjualan = penjualan_detail.id_penjualan')
            ->join('products', 'products.id_produk = penjualan_detail.id_produk')
            ->where('penjualan.status', 'Selesai')
            ->where('penjualan.tanggal_penjualan >=', $startDate)
            ->where('penjualan.tanggal_penjualan <=', $endDate)
            ->first()['total'] ?? 0;
    }
    
    private function calculateReturns($startDate, $endDate)
    {
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->first()['total'] ?? 0;
            
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->first()['total'] ?? 0;
            
        return $salesReturns + $purchaseReturns;
    }
    
    private function calculateExpenses($startDate, $endDate)
    {
        // This would calculate operational expenses
        // For now, we'll return a placeholder
        return 0;
    }
    
    private function getCashInflows($startDate, $endDate)
    {
        $inflows = [];
        
        // Cash sales
        $cashSales = $this->salesModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Selesai')
            ->where('tipe_bayar', 'Cash')
            ->where('tanggal_penjualan >=', $startDate)
            ->where('tanggal_penjualan <=', $endDate)
            ->first()['total'] ?? 0;
        
        $inflows['Cash Sales'] = $cashSales;
        
        // Customer payments (credit)
        $payments = $this->salesModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Selesai')
            ->where('tipe_bayar', 'Kredit')
            ->where('tanggal_penjualan >=', $startDate)
            ->where('tanggal_penjualan <=', $endDate)
            ->first()['total'] ?? 0;
        
        $inflows['Credit Sales'] = $payments;
        
        return $inflows;
    }
    
    private function getCashOutflows($startDate, $endDate)
    {
        $outflows = [];
        
        // Cash purchases
        $cashPurchases = $this->purchaseOrderModel
            ->select('COALESCE(SUM(total_bayar), 0) as total')
            ->where('status', 'Diterima Semua')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate)
            ->first()['total'] ?? 0;
        
        $outflows['Purchases'] = $cashPurchases;
        
        // Returns (refunds)
        $salesReturns = $this->salesReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->first()['total'] ?? 0;
            
        $purchaseReturns = $this->purchaseReturnModel
            ->select('COALESCE(SUM(total_refund), 0) as total')
            ->where('status', 'Selesai')
            ->where('tanggal_retur >=', $startDate)
            ->where('tanggal_retur <=', $endDate)
            ->first()['total'] ?? 0;
            
        $outflows['Returns'] = $salesReturns + $purchaseReturns;
        
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
        return $this->salesModel
            ->where('status', 'Selesai')
            ->where('tanggal_penjualan >=', $startDate)
            ->where('tanggal_penjualan <=', $endDate)
            ->countAllResults();
    }
    
    private function getPurchaseCount($startDate, $endDate)
    {
        return $this->purchaseOrderModel
            ->where('status', 'Diterima Semua')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate)
            ->countAllResults();
    }
    
    private function getProductPerformance($startDate, $endDate)
    {
        return $this->salesDetailModel
            ->select('products.id_produk, products.nama_produk, products.kode_produk, 
                    SUM(penjualan_detail.jumlah) as total_sold, 
                    SUM(penjualan_detail.subtotal) as revenue,
                    COUNT(DISTINCT penjualan_detail.id_penjualan) as sales_count,
                    AVG(penjualan_detail.harga_jual) as avg_price')
            ->join('products', 'products.id_produk = penjualan_detail.id_produk')
            ->join('penjualan', 'penjualan.id_penjualan = penjualan_detail.id_penjualan')
            ->where('penjualan.status', 'Selesai')
            ->where('penjualan.tanggal_penjualan >=', $startDate)
            ->where('penjualan.tanggal_penjualan <=', $endDate)
            ->groupBy('products.id_produk, products.nama_produk, products.kode_produk')
            ->orderBy('revenue', 'DESC')
            ->findAll();
    }
    
    private function getCustomerAnalysis($startDate, $endDate)
    {
        return $this->salesModel
            ->select('customers.id_customer, customers.nama_customer, 
                    COUNT(*) as transaction_count, 
                    SUM(total_bayar) as total_spent,
                    AVG(total_bayar) as avg_transaction_value,
                    MIN(tanggal_penjualan) as first_transaction,
                    MAX(tanggal_penjualan) as last_transaction')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.status', 'Selesai')
            ->where('penjualan.tanggal_penjualan >=', $startDate)
            ->where('penjualan.tanggal_penjualan <=', $endDate)
            ->groupBy('customers.id_customer, customers.nama_customer')
            ->orderBy('total_spent', 'DESC')
            ->findAll();
    }
}