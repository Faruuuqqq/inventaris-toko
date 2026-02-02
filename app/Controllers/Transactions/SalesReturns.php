<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InvalidTransactionException;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class SalesReturns extends BaseController
{
    use ResponseTrait;
    
    protected $salesReturnModel;
    protected $salesReturnDetailModel;
    protected $customerModel;
    protected $productModel;
    protected $warehouseModel;
    protected $saleModel;
    protected $saleItemModel;
    protected $stockService;
    protected $balanceService;
    
    public function __construct()
    {
        $this->salesReturnModel = new \App\Models\SalesReturnModel();
        $this->salesReturnDetailModel = new \App\Models\SalesReturnDetailModel();
        $this->customerModel = new \App\Models\CustomerModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->saleModel = new \App\Models\SaleModel();
        $this->saleItemModel = new \App\Models\SaleItemModel();
        $this->stockService = new StockService();
        $this->balanceService = new BalanceService();
    }
    
    /**
     * Display list of all sales returns with filters
     */
    public function index()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'customer_id' => $this->request->getGet('customer_id'),
            'status' => $this->request->getGet('status')
        ];

        $query = $this->salesReturnModel
            ->select('sales_returns.*, customers.name as nama_customer')
            ->join('customers', 'customers.id_customer = sales_returns.id_customer');

        // Apply filters
        if ($filters['start_date']) {
            $query->where('DATE(sales_returns.tanggal_retur) >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->where('DATE(sales_returns.tanggal_retur) <=', $filters['end_date']);
        }
        if ($filters['customer_id']) {
            $query->where('sales_returns.id_customer', $filters['customer_id']);
        }
        if ($filters['status']) {
            $query->where('sales_returns.status', $filters['status']);
        }

        $data = [
            'title' => 'Retur Penjualan',
            'salesReturns' => $query->orderBy('sales_returns.tanggal_retur', 'DESC')->findAll(),
            'customers' => $this->customerModel->where('status', 'Aktif')->findAll(),
            'filters' => $filters
        ];
        
        return view('transactions/sales_returns/index', $data);
    }
    
    /**
     * Show create sales return form
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Retur Penjualan',
            'customers' => $this->customerModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'salesList' => $this->getSalesList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];
        
        return view('transactions/sales_returns/create', $data);
    }
    
    /**
     * Create new sales return (must link to original sale)
     * Pattern: Inverse of Sales.storeCash (adds stock back, reduces balance)
     */
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[sales_returns.nomor_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_penjualan' => 'required|is_natural_no_zero',
            'id_warehouse_asal' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[Menunggu Persetujuan,Disetujui,Ditolak,Selesai]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
            'produk.*.alasan' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $customerId = $this->request->getPost('id_customer');
            $saleId = $this->request->getPost('id_penjualan');
            $warehouseId = $this->request->getPost('id_warehouse_asal');
            $produk = $this->request->getPost('produk');
            
            // Validate customer exists
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new InvalidTransactionException('Customer tidak ditemukan');
            }
            
            // Validate original sale exists
            $originalSale = $this->saleModel->find($saleId);
            if (!$originalSale) {
                throw new InvalidTransactionException('Penjualan asli tidak ditemukan');
            }
            
            // Validate customer matches
            if ($originalSale['id_customer'] != $customerId) {
                throw new InvalidTransactionException('Customer tidak sesuai dengan penjualan asli');
            }
            
            // Validate warehouse exists
            $warehouse = $this->warehouseModel->find($warehouseId);
            if (!$warehouse) {
                throw new InvalidTransactionException('Gudang tidak ditemukan');
            }
            
            // Validate items exist
            if (empty($produk)) {
                throw new InvalidTransactionException('Tidak ada barang yang dipilih');
            }
            
            // Validate products and calculate total refund
            $totalRefund = 0;
            $itemsData = [];
            
            foreach ($produk as $item) {
                $product = $this->productModel->find($item['id_produk']);
                if (!$product) {
                    throw new InvalidTransactionException('Produk ID ' . $item['id_produk'] . ' tidak ditemukan');
                }
                
                $qty = (int)$item['jumlah'];
                
                // Get original sale item price
                $originalItem = $this->saleItemModel
                    ->where('id_penjualan', $saleId)
                    ->where('id_produk', $item['id_produk'])
                    ->first();
                
                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam penjualan asli');
                }
                
                // Validate return qty doesn't exceed original qty
                if ($qty > $originalItem['qty']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pembelian untuk produk ' . $product['nama_produk']);
                }
                
                $hargaJual = $originalItem['harga'];
                $subtotal = $qty * $hargaJual;
                $totalRefund += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal,
                    'alasan' => $item['alasan'] ?? '',
                    'keterangan' => $item['keterangan'] ?? ''
                ];
            }
            
            // Create sales return record
            $salesReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_customer' => $customerId,
                'id_penjualan' => $saleId,
                'id_warehouse_asal' => $warehouseId,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_refund' => $totalRefund,
                'id_user' => session()->get('id_user')
            ];
            
            $idRetur = $this->salesReturnModel->insert($salesReturnData);
            
            // Create sales return details and add stock back
            foreach ($itemsData as $item) {
                // Create detail record
                $detailData = [
                    'id_retur_penjualan' => $idRetur,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $item['subtotal'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan']
                ];
                
                $this->salesReturnDetailModel->insert($detailData);
                
                // Add stock back via StockService (inverse of sales deduction)
                $this->stockService->addStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'SALES_RETURN',
                    $idRetur,
                    'Retur Penjualan: ' . $salesReturnData['nomor_retur']
                );
            }
            
            // If auto-approved, also reduce customer balance
            if ($this->request->getPost('status') === 'Disetujui') {
                // Reduce customer receivable balance
                $this->balanceService->calculateCustomerReceivable($customerId);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns/' . $idRetur)
                ->with('success', 'Retur penjualan berhasil dibuat');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat retur penjualan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show sales return detail
     */
    public function detail($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['originalSale'] = $this->saleModel->find($salesReturn['id_penjualan']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Detail Retur Penjualan',
            'salesReturn' => $salesReturn
        ];
        
        return view('transactions/sales_returns/detail', $data);
    }
    
    /**
     * Show edit form for sales return
     * Only allowed if status = 'Menunggu Persetujuan'
     */
    public function edit($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Ubah Retur Penjualan',
            'salesReturn' => $salesReturn,
            'customers' => $this->customerModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'salesList' => $this->getSalesList()
        ];
        
        return view('transactions/sales_returns/edit', $data);
    }
    
    /**
     * Update sales return
     * Reverts old stock additions and creates new ones
     * Recalculates customer balance
     */
    public function update($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }
        
        $rules = [
            'nomor_retur' => "required|is_unique[sales_returns.nomor_retur,id_retur_penjualan,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_penjualan' => 'required|is_natural_no_zero',
            'id_warehouse_asal' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[Menunggu Persetujuan,Disetujui,Ditolak,Selesai]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
            'produk.*.alasan' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $customerId = $this->request->getPost('id_customer');
            $saleId = $this->request->getPost('id_penjualan');
            $warehouseId = $this->request->getPost('id_warehouse_asal');
            $produk = $this->request->getPost('produk');
            
            // Get old details to revert stock
            $oldDetails = $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->findAll();
            
            // Revert old stock additions
            foreach ($oldDetails as $detail) {
                try {
                    $this->stockService->deductStock(
                        $detail['id_produk'],
                        $salesReturn['id_warehouse_asal'],
                        $detail['jumlah'],
                        'SALES_RETURN_REVERSAL',
                        $id,
                        'Pembalikan retur penjualan: ' . $salesReturn['nomor_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                }
            }
            
            // Validate products and calculate total refund
            $totalRefund = 0;
            $itemsData = [];
            
            foreach ($produk as $item) {
                $product = $this->productModel->find($item['id_produk']);
                if (!$product) {
                    throw new InvalidTransactionException('Produk ID ' . $item['id_produk'] . ' tidak ditemukan');
                }
                
                $qty = (int)$item['jumlah'];
                
                // Get original sale item price
                $originalItem = $this->saleItemModel
                    ->where('id_penjualan', $saleId)
                    ->where('id_produk', $item['id_produk'])
                    ->first();
                
                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam penjualan asli');
                }
                
                if ($qty > $originalItem['qty']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pembelian untuk produk ' . $product['nama_produk']);
                }
                
                $hargaJual = $originalItem['harga'];
                $subtotal = $qty * $hargaJual;
                $totalRefund += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal,
                    'alasan' => $item['alasan'] ?? '',
                    'keterangan' => $item['keterangan'] ?? ''
                ];
            }
            
            // Update sales return
            $salesReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_customer' => $customerId,
                'id_penjualan' => $saleId,
                'id_warehouse_asal' => $warehouseId,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_refund' => $totalRefund,
                'id_user' => session()->get('id_user')
            ];
            
            $this->salesReturnModel->update($id, $salesReturnData);
            
            // Delete old details
            $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->delete();
            
            // Create new details and add stock back
            foreach ($itemsData as $item) {
                $detailData = [
                    'id_retur_penjualan' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $item['subtotal'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan']
                ];
                
                $this->salesReturnDetailModel->insert($detailData);
                
                // Add new stock back
                $this->stockService->addStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'SALES_RETURN',
                    $id,
                    'Retur Penjualan: ' . $salesReturnData['nomor_retur']
                );
            }
            
            // Recalculate customer balance
            $this->balanceService->calculateCustomerReceivable($customerId);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns/' . $id)
                ->with('success', 'Retur penjualan berhasil diubah');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah retur penjualan: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete sales return (soft delete)
     * Reverts all stock additions
     * Recalculates customer balance
     */
    public function delete($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat dihapus');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get all details to revert stock
            $details = $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->findAll();
            
            // Revert stock for each item
            foreach ($details as $detail) {
                try {
                    $this->stockService->deductStock(
                        $detail['id_produk'],
                        $salesReturn['id_warehouse_asal'],
                        $detail['jumlah'],
                        'SALES_RETURN_REVERSAL',
                        $id,
                        'Penghapusan retur penjualan: ' . $salesReturn['nomor_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                }
            }
            
            // Delete details
            $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->delete();
            
            // Soft delete sales return
            $this->salesReturnModel->delete($id);
            
            // Recalculate customer balance
            $this->balanceService->calculateCustomerReceivable($salesReturn['id_customer']);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Retur penjualan berhasil dihapus');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus retur penjualan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show approval form for sales return
     * Only for status = 'Menunggu Persetujuan'
     */
    public function approve($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur tidak dapat disetujui');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Setujui Retur Penjualan',
            'salesReturn' => $salesReturn,
            'warehouses_good' => $this->warehouseModel->where('jenis', 'Baik')->findAll(),
            'warehouses_damaged' => $this->warehouseModel->where('jenis', 'Rusak')->findAll()
        ];
        
        return view('transactions/sales_returns/approve', $data);
    }
    
    /**
     * Process sales return approval or rejection
     * Updates status and optionally records received items
     */
    public function processApproval($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur tidak dapat diproses');
        }
        
        $action = $this->request->getPost('action');
        $approvalNotes = $this->request->getPost('approval_notes') ?? '';
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            if ($action === 'approve') {
                $rules = [
                    'tanggal_proses' => 'required|valid_date[Y-m-d]',
                ];
                
                if (!$this->validate($rules)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
                
                $tanggalProses = $this->request->getPost('tanggal_proses');
                
                // Update status to 'Selesai'
                $this->salesReturnModel->update($id, [
                    'status' => 'Selesai',
                    'tanggal_proses' => $tanggalProses,
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
                
                // Reduce customer receivable balance since stock already added
                $this->balanceService->calculateCustomerReceivable($salesReturn['id_customer']);
                
            } else if ($action === 'reject') {
                // Update status to 'Ditolak' and revert stock additions
                $details = $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->findAll();
                
                // Revert stock for each item
                foreach ($details as $detail) {
                    try {
                        $this->stockService->deductStock(
                            $detail['id_produk'],
                            $salesReturn['id_warehouse_asal'],
                            $detail['jumlah'],
                            'SALES_RETURN_REJECTED',
                            $id,
                            'Penolakan retur penjualan: ' . $salesReturn['nomor_retur']
                        );
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                    }
                }
                
                // Update status to 'Ditolak'
                $this->salesReturnModel->update($id, [
                    'status' => 'Ditolak',
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Retur penjualan berhasil diproses');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses retur penjualan: ' . $e->getMessage());
        }
    }
    
    /**
     * Get list of completed sales for return selection
     */
    public function getSalesList()
    {
        return $this->saleModel
            ->select('penjualan.id_penjualan, penjualan.nomor_penjualan, penjualan.tanggal_penjualan, customers.name as nama_customer')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.payment_status', 'PAID')
            ->orderBy('penjualan.tanggal_penjualan', 'DESC')
            ->findAll();
    }
    
    /**
     * AJAX endpoint to get details of a specific sale
     */
    public function getSalesDetails()
    {
        $saleId = $this->request->getPost('id_penjualan');
        
        if (!$saleId) {
            return $this->respond(['status' => 'error', 'message' => 'ID Penjualan tidak ditemukan']);
        }
        
        $sale = $this->saleModel->find($saleId);
        if (!$sale) {
            return $this->respond(['status' => 'error', 'message' => 'Penjualan tidak ditemukan']);
        }
        
        $details = $this->saleItemModel
            ->select('sale_items.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sale_items.product_id')
            ->where('sale_id', $saleId)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'sale' => $sale,
            'details' => $details
        ]);
    }
    
    /**
     * Generate unique return number with date prefix
     * Format: SR-202501001, SR-202501002, etc.
     */
    private function generateNomorRetur()
    {
        $prefix = 'SR-' . date('Ym');
        
        $lastRetur = $this->salesReturnModel
            ->like('nomor_retur', $prefix, 'after')
            ->orderBy('nomor_retur', 'DESC')
            ->first();
        
        if ($lastRetur) {
            $lastNumber = (int) substr($lastRetur['nomor_retur'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
