<?php
namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;

class Dashboard extends BaseController
{
    protected $saleModel;
    protected $customerModel;
    protected $productModel;
    protected $productStockModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->productStockModel = new ProductStockModel();
    }

    public function index()
    {
        try {
            // Get today's stats
            $today = date('Y-m-d');
            
            // Today's sales
            $todaySalesResult = $this->saleModel
                ->where('DATE(created_at)', $today)
                ->selectSum('total_amount')
                ->first();
            $todaySales = $todaySalesResult->total_amount ?? 0;
            
            // Today's purchases (simplified for now)
            $todayPurchases = 0;
            
            // Total products in stock
            $totalStockResult = $this->productStockModel->selectSum('quantity')->first();
            $totalStock = $totalStockResult->quantity ?? 0;
            
            // Active customers
            $activeCustomers = $this->customerModel->countAll();
            
            // Recent transactions
            $recentTransactions = $this->saleModel
                ->join('customers', 'customers.id = sales.customer_id')
                ->select('sales.*, customers.name as customer_name')
                ->orderBy('sales.created_at', 'DESC')
                ->limit(5)
                ->find();
            
            // Low stock items
            $lowStockItems = $this->productModel
                ->join('product_stocks', 'product_stocks.product_id = products.id')
                ->where('product_stocks.quantity < products.min_stock_alert')
                ->select('products.*, product_stocks.quantity, product_stocks.warehouse_id')
                ->limit(5)
                ->find();
            
            $data = [
                'title' => 'Dashboard',
                'subtitle' => 'Selamat datang, ' . session()->get('fullname'),
                'todaySales' => $todaySales,
                'todayPurchases' => $todayPurchases,
                'totalStock' => $totalStock,
                'activeCustomers' => $activeCustomers,
                'recentTransactions' => $recentTransactions,
                'lowStockItems' => $lowStockItems,
            ];
            
            return view('dashboard/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage());
        }
    }
}