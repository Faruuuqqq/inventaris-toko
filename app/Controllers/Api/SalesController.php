<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class SalesController extends ResourceController
{
    use ResponseTrait;
    
    protected $salesModel;
    protected $salesDetailModel;
    
    public function __construct()
    {
        $this->salesModel = new \App\Models\SaleModel();
        $this->salesDetailModel = new \App\Models\SaleItemModel();
    }
    
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $customer = $this->request->getGet('customer') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        $status = $this->request->getGet('status') ?? 'Selesai';
        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 20;
        
        $builder = $this->salesModel;
        
        if ($customer) {
            $builder->where('customer_id', $customer);
        }
        
        if ($dateFrom) {
            $builder->where('date >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('date <=', $dateTo);
        }
        
        if ($status) {
            $builder->where('payment_status', $status);
        }
        
        $sales = $builder
            ->select('sales.*, customers.name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->orderBy('sales.created_at', 'DESC')
            ->paginate($limit, 'default', $page);
        
        $data = [
            'sales' => $sales,
            'pagination' => [
                'current_page' => $builder->pager->getCurrentPage(),
                'total_pages' => $builder->pager->getPageCount(),
                'per_page' => $limit,
                'total' => $builder->pager->getTotal()
            ]
        ];
        
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
        $sale = $this->salesModel
            ->select('sales.*, customers.name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->find($id);
        
        if (!$sale) {
            return $this->failNotFound('Sale not found');
        }
        
        // Get sale details
        $sale['details'] = $this->salesDetailModel
            ->select('sale_items.*, products.name, products.sku')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $id)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $sale
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
            'number' => 'required',
            'date' => 'required|valid_date[Y-m-d]',
            'customer_id' => 'required|is_natural_no_zero',
            'salesperson_id' => 'required|is_natural_no_zero',
            'payment_type' => 'required|in_list[CASH,CREDIT]',
            'notes' => 'max_length[500]',
            'products' => 'required',
            'products.*.product_id' => 'required|is_natural_no_zero',
            'products.*.quantity' => 'required|greater_than[0]',
            'products.*.price' => 'required|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Create sale
            $salesData = [
                'number' => $this->request->getPost('number'),
                'date' => $this->request->getPost('date'),
                'customer_id' => $this->request->getPost('customer_id'),
                'salesperson_id' => $this->request->getPost('salesperson_id'),
                'payment_type' => $this->request->getPost('payment_type'),
                'notes' => $this->request->getPost('notes'),
                'total_amount' => 0,
                'payment_status' => 'PAID',
                'created_by' => session()->get('user_id')
            ];
            
            $idSale = $this->salesModel->insert($salesData);
            
            // Calculate total and create details
            $totalAmount = 0;
            $products = $this->request->getPost('products');
            
            foreach ($products as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $totalAmount += $subtotal;
                
                $detailData = [
                    'sale_id' => $idSale,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? ''
                ];
                
                $this->salesDetailModel->insert($detailData);
                
                // Update product stock using ProductModel
                $productModel = new \App\Models\ProductModel();
                $productModel->updateStock($item['product_id'], 1, -$item['quantity'], 'OUT', $salesData['number'], 'Sales transaction');
            }
            
            // Update total
            $this->salesModel->update($idSale, [
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            $sale = $this->salesModel->find($idSale);
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Sale created successfully',
                'data' => $sale
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Failed to create sale: ' . $e->getMessage());
        }
    }
    
    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $sale = $this->salesModel->find($id);
        
        if (!$sale) {
            return $this->failNotFound('Sale not found');
        }
        
        if ($sale['payment_status'] === 'PAID') {
            return $this->failValidationError('Cannot update paid sale');
        }
        
        $rules = [
            'number' => "required",
            'date' => 'required|valid_date[Y-m-d]',
            'customer_id' => 'required|is_natural_no_zero',
            'salesperson_id' => 'required|is_natural_no_zero',
            'payment_type' => 'required|in_list[CASH,CREDIT]',
            'notes' => 'max_length[500]',
            'payment_status' => 'required|in_list[PENDING,PAID,UNPAID]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $data = [
            'number' => $this->request->getPost('number'),
            'date' => $this->request->getPost('date'),
            'customer_id' => $this->request->getPost('customer_id'),
            'salesperson_id' => $this->request->getPost('salesperson_id'),
            'payment_type' => $this->request->getPost('payment_type'),
            'notes' => $this->request->getPost('notes'),
            'payment_status' => $this->request->getPost('payment_status'),
            'created_by' => $this->request->getPost('user_id') ?? session()->get('user_id')
        ];
        
        $updated = $this->salesModel->update($id, $data);
        
        if ($updated) {
            $sale = $this->salesModel->find($id);
            return $this->respond([
                'status' => 'success',
                'message' => 'Sale updated successfully',
                'data' => $sale
            ]);
        } else {
            return $this->failServerError('Failed to update sale');
        }
    }
    
    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $sale = $this->salesModel->find($id);
        
        if (!$sale) {
            return $this->failNotFound('Sale not found');
        }
        
        if ($sale['payment_status'] === 'PAID') {
            return $this->failValidationError('Cannot delete paid sale');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Delete sale details and restore stock
            $details = $this->salesDetailModel->where('sale_id', $id)->findAll();
            
            foreach ($details as $detail) {
                // Restore product stock using ProductModel
                $productModel = new \App\Models\ProductModel();
                $productModel->updateStock($detail['product_id'], 1, $detail['quantity'], 'IN', $sale['number'], 'Sale cancellation');
            }
            
            $this->salesDetailModel->where('sale_id', $id)->delete();
            $this->salesModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Sale deleted successfully',
                'data' => $sale
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Failed to delete sale: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sales statistics
     *
     * @return mixed
     */
    public function stats()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $customer = $this->request->getGet('customer') ?? null;
        
        $builder = $this->salesModel;
        
        if ($customer) {
            $builder->where('customer_id', $customer);
        }
        
        $builder->where('date >=', $dateFrom);
        $builder->where('date <=', $dateTo);
        $builder->where('payment_status', 'PAID');
        
        $stats = [
            'total_sales' => $builder->countAllResults(),
            'total_revenue' => $builder->selectSum('final_amount')->get()->getRow()->final_amount ?? 0,
            'average_sale' => 0,
            'best_selling_product' => $this->getBestSellingProduct($dateFrom, $dateTo, $customer),
            'top_customer' => $this->getTopCustomer($dateFrom, $dateTo)
        ];
        
        if ($stats['total_sales'] > 0) {
            $stats['average_sale'] = $stats['total_revenue'] / $stats['total_sales'];
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => $stats
        ]);
    }
    
    /**
     * Get customer receivables
     *
     * @return mixed
     */
    public function receivables()
    {
        $customer = $this->request->getGet('customer') ?? null;
        
        $builder = $this->salesModel;
        
        if ($customer) {
            $builder->where('customer_id', $customer);
        }
        
        $receivables = $builder
            ->select('sales.id, sales.number, sales.created_at,
                    sales.final_amount, customers.name,
                    DATEDIFF(CURDATE(), sales.created_at) as days_overdue')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_type', 'CREDIT')
            ->where('sales.payment_status', 'UNPAID')
            ->orderBy('sales.created_at', 'ASC')
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'data' => $receivables
        ]);
    }
    
    /**
     * Generate sales report
     *
     * @return mixed
     */
    public function report()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-t');
        $customer = $this->request->getGet('customer') ?? null;
        $format = $this->request->getGet('format') ?? 'json';
        
        $report = $this->generateSalesReport($dateFrom, $dateTo, $customer);
        
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
     * Get best selling product
     */
    private function getBestSellingProduct($dateFrom, $dateTo, $customerId = null)
    {
        $builder = $this->salesDetailModel;
        
        $builder->select('products.id, products.name, SUM(sale_items.quantity) as total_sold, SUM(sale_items.subtotal) as revenue')
                   ->join('sales', 'sales.id = sale_items.sale_id')
                   ->join('products', 'products.id = sale_items.product_id')
                   ->where('sales.payment_status', 'PAID')
                   ->where('sales.created_at >=', $dateFrom)
                   ->where('sales.created_at <=', $dateTo);
        
        if ($customerId) {
            $builder->where('sales.customer_id', $customerId);
        }
        
        return $builder->groupBy('products.id, products.name')
                     ->orderBy('total_sold', 'DESC')
                     ->limit(1)
                     ->get()
                     ->getRow();
    }
    
    /**
     * Get top customer
     */
    private function getTopCustomer($dateFrom, $dateTo)
    {
        return $this->salesModel
            ->select('customers.id, customers.name, COUNT(*) as transaction_count, SUM(final_amount) as total_spent')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_status', 'PAID')
            ->where('sales.created_at >=', $dateFrom)
            ->where('sales.created_at <=', $dateTo)
            ->groupBy('customers.id, customers.name')
            ->orderBy('total_spent', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    
    /**
     * Generate sales report
     */
    private function generateSalesReport($dateFrom, $dateTo, $customerId = null)
    {
        // This would generate a comprehensive sales report
        // For now, return basic data structure
        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'summary' => [
                'total_sales' => 0,
                'total_revenue' => 0,
                'cash_sales' => 0,
                'credit_sales' => 0,
                'total_customers' => 0
            ],
            'details' => [],
            'charts' => []
        ];
    }
    
    /**
     * Update product stock
     */
    private function updateProductStock($idProduct, $quantity)
    {
        $productModel = new \App\Models\ProductModel();
        $product = $productModel->find($idProduct);
        $newStock = $product['stok'] + $quantity;
        
        $productModel->update($idProduct, ['stok' => $newStock]);
    }
    
    /**
     * Create stock mutation
     */
    private function createStockMutation($idProduct, $quantity, $harga, $idReferensi, $keterangan, $tipe = 'Keluar')
    {
        $stockMutationModel = new \App\Models\StockMutationModel();
        
        $mutationData = [
            'id_produk' => $idProduct,
            'id_warehouse' => 1, // Default warehouse
            'tipe_mutasi' => $tipe,
            'jumlah' => $quantity,
            'harga_beli' => $harga,
            'tanggal_mutasi' => date('Y-m-d'),
            'id_referensi' => $idReferensi,
            'tipe_referensi' => 'Penjualan',
            'keterangan' => $keterangan,
            'id_user' => session()->get('id_user')
        ];
        
        $stockMutationModel->insert($mutationData);
    }
}