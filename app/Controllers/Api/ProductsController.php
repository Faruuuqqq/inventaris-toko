<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class ProductsController extends ResourceController
{
    use ResponseTrait;
    
    protected $productModel;
    
    public function __construct()
    {
        $this->productModel = new \App\Models\ProductModel();
    }
    
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $search = $this->request->getGet('search') ?? '';
        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 20;
        $warehouse = $this->request->getGet('warehouse') ?? null;
        
        $builder = $this->productModel;
        
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('name', $search)
                   ->orLike('sku', $search)
                   ->orLike('name', $search)
                   ->groupEnd();
        }
        
        $products = $builder->paginate($limit, 'default', $page);
        $data = [
            'products' => $products,
            'pagination' => [
                'current_page' => $builder->pager->getCurrentPage(),
                'total_pages' => $builder->pager->getPageCount(),
                'per_page' => $limit,
                'total' => $builder->pager->getTotal()
            ]
        ];
        
        // Add stock information if warehouse is specified
        if ($warehouse) {
            $stockMutationModel = new \App\Models\StockMutationModel();
            foreach ($data['products'] as &$product) {
                $product['stock'] = $stockMutationModel->getProductStock($product['id_produk'], $warehouse);
            }
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }
    
    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        
        $warehouse = $this->request->getGet('warehouse') ?? null;
        
        // Add stock information
        if ($warehouse) {
            $stockMutationModel = new \App\Models\StockMutationModel();
            $product['stock'] = $stockMutationModel->getProductStock($product['id_produk'], $warehouse);
        }
        
        // Add sales history
        $salesDetailModel = new \App\Models\SalesDetailModel();
        $product['recent_sales'] = $salesDetailModel
            ->select('penjualan.tanggal_penjualan, penjualan_detail.jumlah, penjualan_detail.harga_jual, customers.name as nama_customer')
            ->join('penjualan', 'penjualan.id_penjualan = penjualan_detail.id_penjualan')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan_detail.id_produk', $id)
            ->where('penjualan.status', 'Selesai')
            ->orderBy('penjualan.tanggal_penjualan', 'DESC')
            ->limit(10)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $product
        ]);
    }
    
    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
     {
         $rules = [
             'name' => 'required|min_length[3]|max_length[255]',
             'sku' => 'required|min_length[2]|max_length[50]|is_unique[products.sku]',
             'description' => 'max_length[1000]',
             'price_sell' => 'required|numeric|greater_than[0]',
             'price_buy' => 'required|numeric|greater_than[0]',
             'unit' => 'required|max_length[20]',
             'min_stock_alert' => 'required|integer|greater_than[0]',
             'category_id' => 'required|integer'
         ];
         
         if (!$this->validate($rules)) {
             return $this->fail($this->validator->getErrors());
         }
         
         $data = [
             'name' => $this->request->getPost('name'),
             'sku' => $this->request->getPost('sku'),
             'category_id' => $this->request->getPost('category_id'),
             'price_sell' => $this->request->getPost('price_sell'),
             'price_buy' => $this->request->getPost('price_buy'),
             'unit' => $this->request->getPost('unit'),
             'min_stock_alert' => $this->request->getPost('min_stock_alert'),
         ];
         
         $id = $this->productModel->insert($data);
         
         if ($id) {
             $product = $this->productModel->find($id);
             return $this->respondCreated([
                 'status' => 'success',
                 'message' => 'Product created successfully',
                 'data' => $product
             ]);
         } else {
             return $this->failServerError('Failed to create product');
         }
     }
    
    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
     public function update($id = null)
     {
         $product = $this->productModel->find($id);
         
         if (!$product) {
             return $this->failNotFound('Product not found');
         }
         
         $rules = [
             'name' => 'required|min_length[3]|max_length[255]',
             'sku' => "required|min_length[2]|max_length[50]|is_unique[products.sku,id,{$id}]",
             'description' => 'max_length[1000]',
             'price_sell' => 'required|numeric|greater_than[0]',
             'price_buy' => 'required|numeric|greater_than[0]',
             'unit' => 'required|max_length[20]',
             'min_stock_alert' => 'required|integer|greater_than[0]',
             'category_id' => 'required|integer'
         ];
         
         if (!$this->validate($rules)) {
             return $this->fail($this->validator->getErrors());
         }
         
         $data = [
             'name' => $this->request->getPost('name'),
             'sku' => $this->request->getPost('sku'),
             'category_id' => $this->request->getPost('category_id'),
             'price_sell' => $this->request->getPost('price_sell'),
             'price_buy' => $this->request->getPost('price_buy'),
             'unit' => $this->request->getPost('unit'),
             'min_stock_alert' => $this->request->getPost('min_stock_alert'),
         ];
         
         $updated = $this->productModel->update($id, $data);
         
         if ($updated) {
             $product = $this->productModel->find($id);
             return $this->respond([
                 'status' => 'success',
                 'message' => 'Product updated successfully',
                 'data' => $product
             ]);
         } else {
             return $this->failServerError('Failed to update product');
         }
     }
    
    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        
        $deleted = $this->productModel->delete($id);
        
        if ($deleted) {
            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Product deleted successfully',
                'data' => $product
            ]);
        } else {
            return $this->failServerError('Failed to delete product');
        }
    }
    
    /**
     * Get product stock information
     *
     * @param int|null $id
     * @return mixed
     */
    public function stock($id = null)
    {
        if ($id) {
            // Get stock for specific product
            $product = $this->productModel->find($id);
            if (!$product) {
                return $this->failNotFound('Product not found');
            }
            
            $stockMutationModel = new \App\Models\StockMutationModel();
            $stock = $stockMutationModel->getProductStockAllWarehouses($id);
            
            return $this->respond([
                'status' => 'success',
                'data' => $stock
            ]);
        } else {
            // Get stock for all products
            $stockMutationModel = new \App\Models\StockMutationModel();
            $stock = $stockMutationModel->getAllProductsStock();
            
            return $this->respond([
                'status' => 'success',
                'data' => $stock
            ]);
        }
    }
    
    /**
     * Get product price history
     *
     * @param int $id
     * @return mixed
     */
    public function priceHistory($id = null)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->failNotFound('Product not found');
        }
        
        $stockMutationModel = new \App\Models\StockMutationModel();
        $history = $stockMutationModel
            ->select('tanggal_mutasi, harga_beli, tipe_mutasi, keterangan')
            ->where('id_produk', $id)
            ->where('tipe_mutasi', 'Masuk')
            ->orderBy('tanggal_mutasi', 'DESC')
            ->limit(20)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $history
        ]);
    }
    
    /**
     * Search products by barcode
     *
     * @return mixed
     */
    public function barcode()
    {
        $barcode = $this->request->getGet('barcode');
        
         if (empty($barcode)) {
             return $this->failValidationError('Barcode is required');
         }
         
         $product = $this->productModel
             ->where('sku', $barcode)
             ->orWhere('barcode', $barcode)
             ->first();
         
         if (!$product) {
             return $this->failNotFound('Product not found for this barcode');
         }
         
         $warehouse = $this->request->getGet('warehouse') ?? null;
        
         // Add stock information
         if ($warehouse) {
             $stockMutationModel = new \App\Models\StockMutationModel();
             $product['stock'] = $stockMutationModel->getProductStock($product['id'], $warehouse);
         }
         
         return $this->respond([
             'status' => 'success',
             'data' => $product
         ]);
    }
}