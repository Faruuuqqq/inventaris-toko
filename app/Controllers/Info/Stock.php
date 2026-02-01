<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\StockMutationModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;

class Stock extends BaseController
{
    protected $stockMutationModel;
    protected $productModel;
    protected $warehouseModel;

    public function __construct()
    {
        $this->stockMutationModel = new StockMutationModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
    }

    public function card()
    {
        $data = [
            'title' => 'Kartu Stok',
            'subtitle' => 'Histori pergerakan barang',
            'products' => $this->productModel->findAll(),
            'warehouses' => $this->warehouseModel->findAll(),
        ];

        return view('info/stock/card', $data);
    }

    public function balance()
    {
        $productStockModel = new \App\Models\ProductStockModel();
        
        // Get all stock data
        $stocks = $productStockModel
            ->select('products.name as product_name, product_stocks.quantity, warehouses.name as warehouse_name')
            ->join('products', 'products.id = product_stocks.product_id')
            ->join('warehouses', 'warehouses.id = product_stocks.warehouse_id')
            ->findAll();
        
        // Group by product
        $productStocks = [];
        $totalStock = 0;
        $totalValue = 0;
        
        foreach ($stocks as $stock) {
            $productId = $stock['product_name'];
            
            if (!isset($productStocks[$productId])) {
                $productStocks[$productId] = [
                    'name' => $stock['product_name'],
                    'total_stock' => 0,
                    'warehouses' => []
                ];
            }
            
            $productStocks[$productId]['total_stock'] += $stock['quantity'];
            $productStocks[$productId]['warehouses'][] = [
                'warehouse' => $stock['warehouse_name'],
                'quantity' => $stock['quantity']
            ];
            
            $totalStock += $stock['quantity'];
        }
        
        $data = [
            'title' => 'Saldo Stok',
            'subtitle' => 'Total stok dan nilai persediaan',
            'productStocks' => $productStocks,
            'totalStock' => $totalStock,
            'totalValue' => 0, // Will be calculated when product prices are available
        ];

        return view('info/stock/balance', $data);
    }

    /**
     * Inventory Management - Advanced stock monitoring and reorder management
     */
    public function management()
    {
        $db = \Config\Database::connect();
        
        // Get all products with current stock levels
        $products = $db->table('products')
            ->select('products.id, products.name, products.sku, products.price, products.category_id, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'LEFT')
            ->where('products.deleted_at', null)
            ->orderBy('products.name', 'ASC')
            ->get()
            ->getResultArray();

        // Add current stock for each product
        foreach ($products as &$product) {
            $stock = $db->table('product_stocks')
                ->selectSum('quantity', 'current_stock')
                ->where('product_id', $product['id'])
                ->get()
                ->getRow();
            
            $product['current_stock'] = (int)($stock->current_stock ?? 0);
            $product['min_stock'] = $product['min_stock'] ?? 10; // Default min stock
            $product['max_stock'] = $product['max_stock'] ?? 100; // Default max stock
        }

        // Get categories
        $categories = $db->table('categories')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Manajemen Inventaris',
            'subtitle' => 'Pantau stok, atur reorder, dan kelola tingkat stok produk',
            'products' => $products,
            'categories' => $categories,
        ];

        return view('info/inventory/management', $data);
    }

    public function getMutations()
    {
        // For AJAX calls
        $productId = $this->request->getGet('product_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $query = $this->stockMutationModel
            ->select('stock_mutations.*, products.name as product_name, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id')
            ->orderBy('stock_mutations.created_at', 'DESC');
        
        if ($productId) {
            $query->where('stock_mutations.product_id', $productId);
        }
        
        if ($warehouseId) {
            $query->where('stock_mutations.warehouse_id', $warehouseId);
        }
        
        if ($startDate) {
            $query->where('stock_mutations.created_at >=', $startDate);
        }
        
        if ($endDate) {
            $query->where('stock_mutations.created_at <=', $endDate . ' 23:59:59');
        }
        
        $mutations = $query->findAll();
        return $this->response->setJSON($mutations);
    }
    
    /**
     * Get stock card data
     */
    public function getStockCard()
    {
        $productId = $this->request->getGet('product_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        
        if (!$productId) {
            return $this->response->setJSON([]);
        }
        
        // Get product info
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([]);
        }
        
        // Get current stock
        $productStocks = $this->productModel->getStockInAllWarehouses($productId);
        
        // Get mutations with proper field names
        $mutations = $this->stockMutationModel
            ->select('stock_mutations.*, products.name as product_name, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id')
            ->where('stock_mutations.product_id', $productId)
            ->orderBy('stock_mutations.created_at', 'DESC');
            
        if ($warehouseId) {
            $mutations->where('stock_mutations.warehouse_id', $warehouseId);
        }
        
        $mutations = $mutations->findAll();
        
        // Calculate running balance
        $runningBalance = [];
        $balance = 0;
        
        foreach ($mutations as $mutation) {
            if ($mutation['mutation_type'] === 'IN') {
                $balance += $mutation['quantity'];
            } else {
                $balance -= $mutation['quantity'];
            }
            
            $mutation['running_balance'] = $balance;
            $runningBalance[] = $mutation;
        }
        
        $data = [
            'product' => $product,
            'currentStocks' => $productStocks,
            'mutations' => $runningBalance,
            'finalBalance' => $balance
        ];
        
        return $this->response->setJSON($data);
    }
    
    /**
     * Get stock summary
     */
    public function getStockSummary()
    {
        $warehouseId = $this->request->getGet('warehouse_id');
        $categoryId = $this->request->getGet('category_id');
        
        $productStockModel = new \App\Models\ProductStockModel();
        $query = $productStockModel
            ->select('products.name as product_name, products.sku, SUM(product_stocks.quantity) as total_quantity')
            ->join('products', 'products.id = product_stocks.product_id')
            ->groupBy('product_stocks.product_id');
            
        if ($warehouseId) {
            $query->where('product_stocks.warehouse_id', $warehouseId);
        }
        
        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }
        
        $summary = $query->findAll();
        
        return $this->response->setJSON($summary);
    }
}