<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class StockController extends ResourceController
{
    use ResponseTrait;
    
    protected $stockMutationModel;
    protected $productModel;
    protected $warehouseModel;
    
    public function __construct()
    {
        $this->stockMutationModel = new \App\Models\StockMutationModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
    }
    
    /**
     * Get stock information
     *
     * @return mixed
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
            $builder->where('id_produk', $product);
        }
        
        if ($warehouse) {
            $builder->where('id_warehouse', $warehouse);
        }
        
        if ($dateFrom) {
            $builder->where('tanggal_mutasi >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('tanggal_mutasi <=', $dateTo);
        }
        
        if ($type) {
            $builder->where('tipe_mutasi', $type);
        }
        
        $mutations = $builder
            ->select('stock_mutations.*, products.nama_produk, products.kode_produk, warehouses.nama_warehouse')
            ->join('products', 'products.id_produk = stock_mutations.id_produk')
            ->join('warehouses', 'warehouses.id_warehouse = stock_mutations.id_warehouse')
            ->orderBy('tanggal_mutasi', 'DESC')
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $mutations
        ]);
    }
    
    /**
     * Get stock summary for all products
     *
     * @return mixed
     */
    public function summary()
    {
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $lowStock = $this->request->getGet('low_stock') ?? false;
        
        if ($warehouse) {
            $stock = $this->stockMutationModel->getWarehouseStockSummary($warehouse);
        } else {
            $stock = $this->stockMutationModel->getAllProductsStock();
        }
        
        // Filter by low stock if requested
        if ($lowStock) {
            $filteredStock = [];
            foreach ($stock as $item) {
                if ($item['stok'] <= $item['minimal_stok']) {
                    $filteredStock[] = $item;
                }
            }
            $stock = $filteredStock;
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $stock
        ]);
    }
    
    /**
     * Get stock card for a specific product
     *
     * @param int|null $id
     * @return mixed
     */
    public function card($id = null)
    {
        if (!$id) {
            return $this->failValidationError('Product ID is required');
        }
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        
        $mutations = $this->stockMutationModel
            ->select('stock_mutations.*, warehouses.nama_warehouse')
            ->join('warehouses', 'warehouses.id_warehouse = stock_mutations.id_warehouse')
            ->where('id_produk', $id);
        
        if ($warehouse) {
            $mutations->where('id_warehouse', $warehouse);
        }
        
        if ($dateFrom) {
            $mutations->where('tanggal_mutasi >=', $dateFrom);
        }
        
        if ($dateTo) {
            $mutations->where('tanggal_mutasi <=', $dateTo);
        }
        
        $mutations = $mutations->orderBy('tanggal_mutasi', 'DESC')->findAll();
        
        // Calculate current stock by warehouse
        $stockByWarehouse = [];
        if (!$warehouse) {
            $stockByWarehouse = $this->stockMutationModel->getProductStockAllWarehouses($id);
        }
        
        $data = [
            'product' => $product,
            'stock_by_warehouse' => $stockByWarehouse,
            'mutations' => $mutations
        ];
        
        return $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }
    
    /**
     * Create stock adjustment
     *
     * @return mixed
     */
    public function adjust()
    {
        $rules = [
            'id_produk' => 'required|is_natural_no_zero',
            'id_warehouse' => 'required|is_natural_no_zero',
            'jumlah' => 'required|integer',
            'tipe_mutasi' => 'required|in_list[Penyesuaian,Masuk,Keluar]',
            'keterangan' => 'required|max_length[500]',
            'tanggal_mutasi' => 'required|valid_date[Y-m-d]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $product = $this->productModel->find($this->request->getPost('id_produk'));
        
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        
        $warehouse = $this->warehouseModel->find($this->request->getPost('id_warehouse'));
        
        if (!$warehouse) {
            return $this->failNotFound('Warehouse not found');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $mutationData = [
                'id_produk' => $this->request->getPost('id_produk'),
                'id_warehouse' => $this->request->getPost('id_warehouse'),
                'tipe_mutasi' => $this->request->getPost('tipe_mutasi'),
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_beli' => $product['harga_beli_terakhir'],
                'tanggal_mutasi' => $this->request->getPost('tanggal_mutasi'),
                'keterangan' => $this->request->getPost('keterangan'),
                'id_user' => $this->request->getPost('id_user') ?? session()->get('id_user')
            ];
            
            $this->stockMutationModel->insert($mutationData);
            
            // Update product stock
            $tipeMutasi = $this->request->getPost('tipe_mutasi');
            $jumlah = $this->request->getPost('jumlah');
            
            if ($tipeMutasi === 'Keluar' || $tipeMutasi === 'Penyesuaian') {
                $this->updateProductStock($mutationData['id_produk'], -$jumlah);
            } else {
                $this->updateProductStock($mutationData['id_produk'], $jumlah);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Stock adjustment created successfully',
                'data' => $mutationData
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Failed to create stock adjustment: ' . $e->getMessage());
        }
    }
    
    /**
     * Get stock movement report
     *
     * @return mixed
     */
    public function report()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $warehouse = $this->request->getGet('warehouse') ?? null;
        $product = $this->request->getGet('product') ?? null;
        $format = $this->request->getGet('format') ?? 'json';
        
        $report = $this->generateStockReport($dateFrom, $dateTo, $warehouse, $product);
        
        if ($format === 'json') {
            return $this->respond([
                'status' => 'success',
                'data' => $report
            ]);
        } else if ($format === 'pdf') {
            // Generate PDF report (would need PDF library)
            return $this->respond([
                'status' => 'success',
                'message' => 'PDF report generation not implemented yet',
                'data' => $report
            ]);
        } else {
            return $this->failValidationError('Invalid format');
        }
    }
    
    /**
     * Get stock statistics
     *
     * @return mixed
     */
    public function stats()
    {
        $warehouse = $this->request->getGet('warehouse') ?? null;
        
        $stats = [
            'total_products' => $this->productModel->where('status', 'Aktif')->countAllResults(),
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'total_stock_value' => 0,
            'monthly_movements' => $this->getMonthlyMovements($warehouse),
            'warehouse_distribution' => $this->getWarehouseDistribution()
        ];
        
        // Get low stock and out of stock products
        $stock = $this->stockMutationModel->getAllProductsStock();
        foreach ($stock as $item) {
            $stats['total_stock_value'] += $item['stok'] * $item['harga_beli_terakhir'];
            
            if ($item['stok'] == 0) {
                $stats['out_of_stock_products']++;
            } elseif ($item['stok'] <= $item['minimal_stok']) {
                $stats['low_stock_products']++;
            }
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $stats
        ]);
    }
    
    /**
     * Get product availability
     *
     * @return mixed
     */
    public function availability()
    {
        $products = $this->request->getPost('products');
        
        if (empty($products) || !is_array($products)) {
            return $this->failValidationError('Products array is required');
        }
        
        $availability = [];
        
        foreach ($products as $productId) {
            $stock = $this->stockMutationModel->getProductStockAllWarehouses($productId);
            $product = $this->productModel->find($productId);
            
            $availability[] = [
                'id_produk' => $productId,
                'nama_produk' => $product['nama_produk'] ?? 'Unknown',
                'kode_produk' => $product['kode_produk'] ?? 'Unknown',
                'available' => $stock,
                'total_stock' => array_sum(array_column($stock, 'stok'))
            ];
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $availability
        ]);
    }
    
    /**
     * Get monthly movements
     */
    private function getMonthlyMovements($warehouseId = null)
    {
        $builder = $this->stockMutationModel;
        
        if ($warehouseId) {
            $builder->where('id_warehouse', $warehouseId);
        }
        
        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));
        
        $currentMonthMovements = $builder
            ->select('SUM(jumlah) as total, tipe_mutasi')
            ->where('tanggal_mutasi >=', $currentMonth . '-01')
            ->where('tanggal_mutasi <=', $currentMonth . '-31')
            ->groupBy('tipe_mutasi')
            ->get()
            ->getResultArray();
        
        $lastMonthMovements = $builder
            ->select('SUM(jumlah) as total, tipe_mutasi')
            ->where('tanggal_mutasi >=', $lastMonth . '-01')
            ->where('tanggal_mutasi <=', $lastMonth . '-31')
            ->groupBy('tipe_mutasi')
            ->get()
            ->getResultArray();
        
        return [
            'current_month' => $currentMonthMovements,
            'last_month' => $lastMonthMovements
        ];
    }
    
    /**
     * Get warehouse distribution
     */
    private function getWarehouseDistribution()
    {
        return $this->stockMutationModel
            ->select('warehouses.nama_warehouse, SUM(stock_mutations.jumlah) as total_movements')
            ->join('warehouses', 'warehouses.id_warehouse = stock_mutations.id_warehouse')
            ->groupBy('warehouses.id_warehouse, warehouses.nama_warehouse')
            ->orderBy('total_movements', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Generate stock report
     */
    private function generateStockReport($dateFrom, $dateTo, $warehouseId = null, $productId = null)
    {
        // This would generate a comprehensive stock report
        // For now, return basic data structure
        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'summary' => [
                'opening_balance' => 0,
                'total_in' => 0,
                'total_out' => 0,
                'closing_balance' => 0
            ],
            'details' => [],
            'products' => [],
            'charts' => []
        ];
    }
    
    /**
     * Update product stock
     */
    private function updateProductStock($idProduct, $quantity)
    {
        $product = $this->productModel->find($idProduct);
        $newStock = $product['stok'] + $quantity;
        
        $this->productModel->update($idProduct, ['stok' => $newStock]);
    }
}