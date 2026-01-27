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
        $this->salesModel = new \App\Models\SalesModel();
        $this->salesDetailModel = new \App\Models\SalesDetailModel();
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
            $builder->where('id_customer', $customer);
        }
        
        if ($dateFrom) {
            $builder->where('tanggal_penjualan >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('tanggal_penjualan <=', $dateTo);
        }
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        $sales = $builder
            ->select('penjualan.*, customers.nama_customer')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            ->select('penjualan.*, customers.nama_customer')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->find($id);
        
        if (!$sale) {
            return $this->failNotFound('Sale not found');
        }
        
        // Get sale details
        $sale['details'] = $this->salesDetailModel
            ->select('penjualan_detail.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = penjualan_detail.id_produk')
            ->where('id_penjualan', $id)
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
            'nomor_penjualan' => 'required|is_unique[penjualan.nomor_penjualan]',
            'tanggal_penjualan' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_salesperson' => 'required|is_natural_no_zero',
            'tipe_bayar' => 'required|in_list[Cash,Kredit]',
            'keterangan' => 'max_length[500]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
            'produk.*.harga_jual' => 'required|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Create sale
            $salesData = [
                'nomor_penjualan' => $this->request->getPost('nomor_penjualan'),
                'tanggal_penjualan' => $this->request->getPost('tanggal_penjualan'),
                'id_customer' => $this->request->getPost('id_customer'),
                'id_salesperson' => $this->request->getPost('id_salesperson'),
                'tipe_bayar' => $this->request->getPost('tipe_bayar'),
                'keterangan' => $this->request->getPost('keterangan'),
                'total_bayar' => 0,
                'status' => 'Selesai',
                'id_user' => $this->request->getPost('id_user') ?? session()->get('id_user')
            ];
            
            $idSale = $this->salesModel->insert($salesData);
            
            // Calculate total and create details
            $totalBayar = 0;
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $subtotal = $item['jumlah'] * $item['harga_jual'];
                $totalBayar += $subtotal;
                
                $detailData = [
                    'id_penjualan' => $idSale,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $subtotal,
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->salesDetailModel->insert($detailData);
                
                // Update product stock
                $this->updateProductStock($item['id_produk'], -$item['jumlah']);
                
                // Create stock mutation
                $this->createStockMutation($item['id_produk'], $item['jumlah'], $item['harga_jual'], $idSale, 'Penjualan');
            }
            
            // Update total
            $this->salesModel->update($idSale, ['total_bayar' => $totalBayar]);
            
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
        
        if ($sale['status'] === 'Selesai') {
            return $this->failValidationError('Cannot update completed sale');
        }
        
        $rules = [
            'nomor_penjualan' => "required|is_unique[penjualan.nomor_penjualan,id_penjualan,{$id}]",
            'tanggal_penjualan' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_salesperson' => 'required|is_natural_no_zero',
            'tipe_bayar' => 'required|in_list[Cash,Kredit]',
            'keterangan' => 'max_length[500]',
            'status' => 'required|in_list[Proses,Selesai,Batal]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $data = [
            'nomor_penjualan' => $this->request->getPost('nomor_penjualan'),
            'tanggal_penjualan' => $this->request->getPost('tanggal_penjualan'),
            'id_customer' => $this->request->getPost('id_customer'),
            'id_salesperson' => $this->request->getPost('id_salesperson'),
            'tipe_bayar' => $this->request->getPost('tipe_bayar'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => $this->request->getPost('status'),
            'id_user' => $this->request->getPost('id_user') ?? session()->get('id_user')
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
        
        if ($sale['status'] === 'Selesai') {
            return $this->failValidationError('Cannot delete completed sale');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Delete sale details and restore stock
            $details = $this->salesDetailModel->where('id_penjualan', $id)->findAll();
            
            foreach ($details as $detail) {
                // Restore product stock
                $this->updateProductStock($detail['id_produk'], $detail['jumlah']);
                
                // Create stock mutation
                $this->createStockMutation($detail['id_produk'], $detail['jumlah'], $detail['harga_jual'], $id, 'Batal Penjualan', 'Masuk');
            }
            
            $this->salesDetailModel->where('id_penjualan', $id)->delete();
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
            $builder->where('id_customer', $customer);
        }
        
        $builder->where('tanggal_penjualan >=', $dateFrom);
        $builder->where('tanggal_penjualan <=', $dateTo);
        $builder->where('status', 'Selesai');
        
        $stats = [
            'total_sales' => $builder->countAllResults(),
            'total_revenue' => $builder->selectSum('total_bayar')->get()->getRow()->total_bayar ?? 0,
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
            $builder->where('id_customer', $customer);
        }
        
        $receivables = $builder
            ->select('penjualan.id_penjualan, penjualan.nomor_penjualan, penjualan.tanggal_penjualan, 
                    penjualan.total_bayar, customers.nama_customer, customers.nama_customer,
                    DATEDIFF(CURDATE(), penjualan.tanggal_penjualan) as days_overdue')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.tipe_bayar', 'Kredit')
            ->where('penjualan.status', 'Selesai')
            ->where('penjualan.total_bayar > (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayaran WHERE id_penjualan = penjualan.id_penjualan)')
            ->orderBy('penjualan.tanggal_penjualan', 'ASC')
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
        
        $builder->select('products.id_produk, products.nama_produk, SUM(penjualan_detail.jumlah) as total_sold, SUM(penjualan_detail.subtotal) as revenue')
                   ->join('penjualan', 'penjualan.id_penjualan = penjualan_detail.id_penjualan')
                   ->join('products', 'products.id_produk = penjualan_detail.id_produk')
                   ->where('penjualan.status', 'Selesai')
                   ->where('penjualan.tanggal_penjualan >=', $dateFrom)
                   ->where('penjualan.tanggal_penjualan <=', $dateTo);
        
        if ($customerId) {
            $builder->where('penjualan.id_customer', $customerId);
        }
        
        return $builder->groupBy('products.id_produk, products.nama_produk')
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
            ->select('customers.id_customer, customers.nama_customer, COUNT(*) as transaction_count, SUM(total_bayar) as total_spent')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.status', 'Selesai')
            ->where('penjualan.tanggal_penjualan >=', $dateFrom)
            ->where('penjualan.tanggal_penjualan <=', $dateTo)
            ->groupBy('customers.id_customer, customers.nama_customer')
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