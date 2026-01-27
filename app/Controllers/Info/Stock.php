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

        return view('layout/main', $data)->renderSection('content', view('info/stock/card', $data));
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

        return view('layout/main', $data)->renderSection('content', view('info/stock/balance', $data));
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
}