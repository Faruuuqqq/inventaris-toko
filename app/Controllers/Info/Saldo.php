<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\CustomerModel;

class Saldo extends BaseController
{
    protected $saleModel;
    protected $customerModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
    }

    public function receivable()
    {
        // Get customers with outstanding balances
        $customers = $this->customerModel
            ->where('receivable_balance >', 0)
            ->findAll();
        
        // Calculate aging
        $agingData = [
            '0-30' => ['customers' => [], 'total' => 0],
            '31-60' => ['customers' => [], 'total' => 0],
            '61-90' => ['customers' => [], 'total' => 0],
            '90+' => ['customers' => [], 'total' => 0],
        ];
        
        foreach ($customers as $customer) {
            // Get latest unpaid sale for aging calculation
            $latestSale = $this->saleModel
                ->where('customer_id', $customer->id)
                ->where('payment_status !=', 'PAID')
                ->orderBy('created_at', 'DESC')
                ->first();
            
            if ($latestSale) {
                $daysOverdue = $this->calculateDaysOverdue($latestSale->created_at, $latestSale->due_date);
                $agingCategory = $this->getAgingCategory($daysOverdue);
                
                $agingData[$agingCategory]['customers'][] = $customer;
                $agingData[$agingCategory]['total'] += $customer->receivable_balance;
            }
        }
        
        $totalReceivable = 0;
        foreach ($customers as $customer) {
            $totalReceivable += $customer->receivable_balance;
        }
        
        $data = [
            'title' => 'Saldo Piutang',
            'subtitle' => 'Daftar piutang customer',
            'customers' => $customers,
            'agingData' => $agingData,
            'totalReceivable' => $totalReceivable,
        ];
        
        return view('info/saldo/receivable', $data);
    }

    public function payable()
    {
        $supplierModel = new \App\Models\SupplierModel();
        
        // Get suppliers with outstanding debts
        $suppliers = $supplierModel
            ->where('debt_balance >', 0)
            ->findAll();
        
        $totalPayable = 0;
        foreach ($suppliers as $supplier) {
            $totalPayable += $supplier->debt_balance;
        }
        
        $data = [
            'title' => 'Saldo Utang',
            'subtitle' => 'Daftar utang ke supplier',
            'suppliers' => $suppliers,
            'totalPayable' => $totalPayable,
        ];
        
        return view('info/saldo/payable', $data);
    }

    private function calculateDaysOverdue($invoiceDate, $dueDate)
    {
        $now = new \DateTime();
        $due = new \DateTime($dueDate);
        return $now->diff($due)->days;
    }

    private function getAgingCategory($daysOverdue)
    {
        if ($daysOverdue <= 0) return '0-30';
        if ($daysOverdue <= 30) return '0-30';
        if ($daysOverdue <= 60) return '31-60';
        if ($daysOverdue <= 90) return '61-90';
        return '90+';
    }

    public function stock()
    {
        $productModel = new \App\Models\ProductModel();
        $categoryModel = new \App\Models\CategoryModel();
        $warehouseModel = new \App\Models\WarehouseModel();

        $data = [
            'title' => 'Saldo Stok',
            'subtitle' => 'Laporan saldo stok semua produk',
            'categories' => $categoryModel->findAll(),
            'warehouses' => $warehouseModel->findAll(),
        ];

        return view('info/saldo/stock', $data);
    }

    public function stockData()
    {
        $categoryId = $this->request->getGet('category_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $stockStatus = $this->request->getGet('stock_status');

        $db = \Config\Database::connect();
        $builder = $db->table('product_stocks')
            ->select('
                product_stocks.quantity,
                product_stocks.min_stock_alert,
                products.id as product_id,
                products.code as product_code,
                products.name as product_name,
                products.price_buy,
                categories.name as category_name,
                warehouses.name as warehouse_name
            ')
            ->join('products', 'products.id = product_stocks.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('warehouses', 'warehouses.id = product_stocks.warehouse_id');

        if ($categoryId) {
            $builder->where('products.category_id', $categoryId);
        }

        if ($warehouseId) {
            $builder->where('product_stocks.warehouse_id', $warehouseId);
        }

        if ($stockStatus === 'low') {
            $builder->where('product_stocks.quantity <= product_stocks.min_stock_alert');
        } elseif ($stockStatus === 'normal') {
            $builder->where('product_stocks.quantity > product_stocks.min_stock_alert');
        } elseif ($stockStatus === 'high') {
            $builder->where('product_stocks.quantity >', 100);
        }

        $stocks = $builder->get()->getResultArray();

        return $this->response->setJSON($stocks);
    }
}