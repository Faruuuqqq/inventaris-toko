<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\StockMutationModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;

class StockController extends BaseController
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
    
    /**
     * Get stock information
     */
    public function index()
    {
        $product = $this->request->getGet('product') ?? null;
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        $type = $this->request->getGet('type') ?? null;
        
        $builder = $this->stockMutationModel;
        
        if ($product) {
            $builder->where('product_id', $product);
        }
        
        if ($warehouse) {
            $builder->where('warehouse_id', $warehouse);
        }
        
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }
        
        if ($type) {
            $builder->where('mutation_type', $type);
        }
        
        $mutations = $builder
            ->select('stock_mutations.*, products.name as product_name, products.sku, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id')
            ->orderBy('stock_mutations.created_at', 'DESC')
            ->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $mutations
        ]);
    }
    
    /**
     * Get stock summary for all products
     */
    public function summary()
    {
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $lowStock = $this->request->getGet('low_stock') ?? false;
        
        $productStockModel = new \App\Models\ProductStockModel();
        $builder = $productStockModel;
        
        if ($warehouse) {
            $builder->where('warehouse_id', $warehouse);
        }
        
        $stocks = $builder
            ->select('product_stocks.*, products.name as product_name, products.sku, products.min_stock_alert')
            ->join('products', 'products.id = product_stocks.product_id')
            ->findAll();
        
        // Filter by low stock if requested
        if ($lowStock) {
            $filteredStock = [];
            foreach ($stocks as $stock) {
                if ($stock['quantity'] <= $stock['min_stock_alert']) {
                    $filteredStock[] = $stock;
                }
            }
            $stocks = $filteredStock;
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $stocks
        ]);
    }
    
    /**
     * Get stock card for a specific product
     */
    public function card($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product ID is required'
            ]);
        }
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
        
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        
        $mutations = $this->stockMutationModel->getProductMutations($id, $warehouse);
        
        // Filter by date range
        if ($dateFrom || $dateTo) {
            $filteredMutations = [];
            foreach ($mutations as $mutation) {
                $mutationDate = date('Y-m-d', strtotime($mutation['created_at']));
                
                if ($dateFrom && $mutationDate < $dateFrom) {
                    continue;
                }
                
                if ($dateTo && $mutationDate > $dateTo) {
                    continue;
                }
                
                $filteredMutations[] = $mutation;
            }
            $mutations = $filteredMutations;
        }
        
        // Get current stock by warehouse
        $stockByWarehouse = $this->productModel->getStockInAllWarehouses($id);
        
        // Filter by specific warehouse
        if ($warehouse) {
            $filteredStock = [];
            foreach ($stockByWarehouse as $stock) {
                if ($stock['warehouse_code'] == $warehouse) {
                    $filteredStock[] = $stock;
                }
            }
            $stockByWarehouse = $filteredStock;
        }
        
        $data = [
            'product' => $product,
            'stock_by_warehouse' => $stockByWarehouse,
            'mutations' => $mutations
        ];
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }
    
    /**
     * Create stock adjustment
     */
    public function adjust()
    {
        $rules = [
            'product_id' => 'required|is_natural_no_zero',
            'warehouse_id' => 'required|is_natural_no_zero',
            'quantity' => 'required|integer',
            'adjustment_type' => 'required|in_list[ADJUSTMENT,STOCK_IN,STOCK_OUT]',
            'notes' => 'required|max_length[500]',
            'date' => 'required|valid_date[Y-m-d]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $product = $this->productModel->find($this->request->getPost('product_id'));
        
        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
        
        $warehouse = $this->warehouseModel->find($this->request->getPost('warehouse_id'));
        
        if (!$warehouse) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Warehouse not found'
            ]);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $adjustmentType = $this->request->getPost('adjustment_type');
            $quantity = $this->request->getPost('quantity');
            $date = $this->request->getPost('date');
            $notes = $this->request->getPost('notes');
            
            // Determine mutation type and quantity
            $mutationType = 'ADJUSTMENT';
            $mutationQuantity = $quantity;
            
            if ($adjustmentType === 'STOCK_OUT') {
                $mutationQuantity = -$quantity;
            }
            
            // Create stock mutation record
            $this->stockMutationModel->createMutation(
                $product['id'],
                $warehouse['id'],
                $mutationType,
                $mutationQuantity,
                'STOCK_ADJUSTMENT',
                null,
                $notes
            );
            
            // Update product stock
            $this->productModel->updateStock(
                $product['id'],
                $warehouse['id'],
                $mutationQuantity,
                $adjustmentType,
                'ADJ-' . date('YmdHis'),
                $notes
            );
            
            $db->transComplete();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Stock adjustment created successfully'
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create stock adjustment: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stock transfer between warehouses
     */
    public function transfer()
    {
        $rules = [
            'product_id' => 'required|is_natural_no_zero',
            'from_warehouse_id' => 'required|is_natural_no_zero',
            'to_warehouse_id' => 'required|is_natural_no_zero',
            'quantity' => 'required|greater_than[0]',
            'notes' => 'permit_empty|max_length[500]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $productId = $this->request->getPost('product_id');
            $fromWarehouseId = $this->request->getPost('from_warehouse_id');
            $toWarehouseId = $this->request->getPost('to_warehouse_id');
            $quantity = $this->request->getPost('quantity');
            $notes = $this->request->getPost('notes') ?? 'Stock transfer';
            
            // Validate product exists
            $product = $this->productModel->find($productId);
            if (!$product) {
                throw new \Exception('Product not found');
            }
            
            // Check source warehouse stock
            $productStocks = $this->productModel->getStockInAllWarehouses($productId);
            $sourceStock = null;
            
            foreach ($productStocks as $stock) {
                if ($stock['warehouse_id'] == $fromWarehouseId) {
                    $sourceStock = $stock;
                    break;
                }
            }
            
            if (!$sourceStock || $sourceStock['quantity'] < $quantity) {
                throw new \Exception('Insufficient stock in source warehouse');
            }
            
            // Create transfer OUT mutation
            $this->stockMutationModel->createMutation(
                $productId,
                $fromWarehouseId,
                'TRANSFER',
                -$quantity,
                'STOCK_TRANSFER',
                null,
                "Transfer to warehouse {$toWarehouseId}: {$notes}"
            );
            
            // Create transfer IN mutation
            $this->stockMutationModel->createMutation(
                $productId,
                $toWarehouseId,
                'TRANSFER',
                $quantity,
                'STOCK_TRANSFER',
                null,
                "Transfer from warehouse {$fromWarehouseId}: {$notes}"
            );
            
            // Update stocks
            $this->productModel->updateStock(
                $productId,
                $fromWarehouseId,
                -$quantity,
                'OUT',
                'TR-' . date('YmdHis'),
                'Stock Transfer OUT'
            );
            
            $this->productModel->updateStock(
                $productId,
                $toWarehouseId,
                $quantity,
                'IN',
                'TR-' . date('YmdHis'),
                'Stock Transfer IN'
            );
            
            $db->transComplete();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Stock transfer successful'
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stock availability check
     */
    public function availability()
    {
        $products = $this->request->getPost('products');
        
        if (empty($products) || !is_array($products)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Products array is required'
            ]);
        }
        
        $availability = [];
        
        foreach ($products as $productRequest) {
            $productId = $productRequest['product_id'] ?? null;
            $requiredQuantity = $productRequest['quantity'] ?? 1;
            $warehouseId = $productRequest['warehouse_id'] ?? null;
            
            if (!$productId) {
                continue;
            }
            
            $productStocks = $this->productModel->getStockInAllWarehouses($productId);
            $product = $this->productModel->find($productId);
            
            $totalStock = array_sum(array_column($productStocks, 'quantity'));
            $canFulfill = $totalStock >= $requiredQuantity;
            
            $availability[] = [
                'product_id' => $productId,
                'product_name' => $product['name'] ?? 'Unknown',
                'product_sku' => $product['sku'] ?? 'Unknown',
                'required_quantity' => $requiredQuantity,
                'available_quantity' => $totalStock,
                'can_fulfill' => $canFulfill,
                'stock_by_warehouse' => $productStocks,
                'backorder_quantity' => $canFulfill ? 0 : $requiredQuantity - $totalStock
            ];
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $availability
        ]);
    }
    
    /**
     * Barcode scanner
     */
    public function barcode()
    {
        $barcode = $this->request->getGet('barcode');
        
        if (!$barcode) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Barcode required'
            ]);
        }
        
        $product = $this->productModel
            ->where('sku', $barcode)
            ->first();
        
        if ($product) {
            $stockData = $this->productModel->getStockInAllWarehouses($product['id']);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'product' => [
                        'id' => $product['id'],
                        'sku' => $product['sku'],
                        'name' => $product['name'],
                        'price_buy' => $product['price_buy'],
                        'price_sell' => $product['price_sell'],
                        'unit' => $product['unit']
                    ],
                    'stocks' => $stockData,
                    'total_stock' => array_sum(array_column($stockData, 'quantity'))
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
    }
    
    /**
     * Get stock statistics
     */
    public function stats()
    {
        $warehouse = $this->request->getGet('warehouse') ?? null;
        
        $productStockModel = new \App\Models\ProductStockModel();
        $builder = $productStockModel;
        
        if ($warehouse) {
            $builder->where('warehouse_id', $warehouse);
        }
        
        $stocks = $builder
            ->select('product_stocks.*, products.name as product_name, products.min_stock_alert')
            ->join('products', 'products.id = product_stocks.product_id')
            ->findAll();
        
        $stats = [
            'total_products' => count(array_unique(array_column($stocks, 'product_id'))),
            'total_stock' => array_sum(array_column($stocks, 'quantity')),
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'stock_value' => 0,
            'warehouse_distribution' => []
        ];
        
        // Get products from database for price calculations
        foreach ($stocks as $stock) {
            $product = $this->productModel->find($stock['product_id']);
            if ($product) {
                $stats['stock_value'] += $stock['quantity'] * $product['price_buy'];
            }
            
            if ($stock['quantity'] == 0) {
                $stats['out_of_stock_products']++;
            } elseif ($stock['quantity'] <= $stock['min_stock_alert']) {
                $stats['low_stock_products']++;
            }
        }
        
        // Warehouse distribution
        $warehouseModel = new WarehouseModel();
        $warehouses = $warehouseModel->findAll();
        
        foreach ($warehouses as $warehouseData) {
            $warehouseStock = $productStockModel
                ->where('warehouse_id', $warehouseData['id'])
                ->selectSum('quantity', 'total')
                ->first();
            
            $stats['warehouse_distribution'][] = [
                'warehouse_id' => $warehouseData['id'],
                'warehouse_name' => $warehouseData['name'],
                'total_stock' => $warehouseStock['total'] ?? 0
            ];
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $stats
        ]);
    }
    
    /**
     * Get stock movement report
     */
    public function report()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $productId = $this->request->getGet('product_id') ?? null;
        $warehouseId = $this->request->getGet('warehouse_id') ?? null;
        
        $mutations = $this->stockMutationModel
            ->select('stock_mutations.*, products.name as product_name, products.sku, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stock_mutations.product_id')
            ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id')
            ->where('stock_mutations.created_at >=', $startDate)
            ->where('stock_mutations.created_at <=', $endDate . ' 23:59:59');
        
        if ($productId) {
            $mutations->where('stock_mutations.product_id', $productId);
        }
        
        if ($warehouseId) {
            $mutations->where('stock_mutations.warehouse_id', $warehouseId);
        }
        
        $mutations = $mutations->orderBy('stock_mutations.created_at', 'DESC')->findAll();
        
        // Group by date for summary
        $dailySummary = [];
        $typeSummary = [
            'IN' => 0,
            'OUT' => 0,
            'ADJUSTMENT' => 0,
            'TRANSFER' => 0
        ];
        
        foreach ($mutations as $mutation) {
            $date = date('Y-m-d', strtotime($mutation['created_at']));
            
            if (!isset($dailySummary[$date])) {
                $dailySummary[$date] = [
                    'date' => $date,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'adjustment_quantity' => 0,
                    'transfer_quantity' => 0,
                    'total_mutations' => 0
                ];
            }
            
            $typeSummary[$mutation['mutation_type']]++;
            
            if ($mutation['mutation_type'] === 'IN') {
                $dailySummary[$date]['in_quantity'] += $mutation['quantity'];
            } elseif ($mutation['mutation_type'] === 'OUT') {
                $dailySummary[$date]['out_quantity'] += abs($mutation['quantity']);
            } elseif ($mutation['mutation_type'] === 'ADJUSTMENT') {
                $dailySummary[$date]['adjustment_quantity'] += $mutation['quantity'];
            } elseif ($mutation['mutation_type'] === 'TRANSFER') {
                $dailySummary[$date]['transfer_quantity'] += abs($mutation['quantity']);
            }
            
            $dailySummary[$date]['total_mutations']++;
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => [
                    'total_mutations' => count($mutations),
                    'type_breakdown' => $typeSummary,
                    'total_in' => array_sum(array_column($dailySummary, 'in_quantity')),
                    'total_out' => array_sum(array_column($dailySummary, 'out_quantity'))
                ],
                'daily_summary' => array_values($dailySummary),
                'mutations' => $mutations
            ]
        ]);
    }
}