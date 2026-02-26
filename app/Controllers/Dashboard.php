<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Models\PurchaseOrderModel;
use App\Models\SaleModel;

class Dashboard extends BaseController
{
    protected $saleModel;

    protected $customerModel;

    protected $productModel;

    protected $productStockModel;

    protected $purchaseOrderModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->productStockModel = new ProductStockModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
    }

    public function index()
    {
        try {
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));

            $todaySalesResult = $this->saleModel
                ->where('DATE(created_at)', $today)
                ->selectSum('total_amount')
                ->first();
            $todaySales = $todaySalesResult->total_amount ?? 0;

            $yesterdaySalesResult = $this->saleModel
                ->where('DATE(created_at)', $yesterday)
                ->selectSum('total_amount')
                ->first();
            $yesterdaySales = $yesterdaySalesResult->total_amount ?? 0;

            $salesGrowth = $yesterdaySales > 0
                ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
                : ($todaySales > 0 ? 100 : 0);

            $todayPurchasesResult = $this->purchaseOrderModel
                ->where('DATE(tanggal_po)', $today)
                ->selectSum('total_bayar')
                ->first();
            $todayPurchases = $todayPurchasesResult->total_bayar ?? 0;

            $todayPurchaseCount = $this->purchaseOrderModel
                ->where('DATE(tanggal_po)', $today)
                ->countAllResults();

            $totalStockResult = $this->productStockModel->selectSum('quantity')->first();
            $totalStock = $totalStockResult->quantity ?? 0;

            $activeCustomers = $this->customerModel->where('is_active', 1)->countAllResults();

            $newCustomersThisWeek = $this->customerModel
                ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->countAllResults();

            $recentTransactions = $this->saleModel
                ->join('customers', 'customers.id = sales.customer_id')
                ->select('sales.*, customers.name as customer_name')
                ->orderBy('sales.created_at', 'DESC')
                ->limit(5)
                ->find();

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
                'salesGrowth' => $salesGrowth,
                'todayPurchaseCount' => $todayPurchaseCount,
                'newCustomersThisWeek' => $newCustomersThisWeek,
            ];

            return view('dashboard/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage());
        }
    }
}
