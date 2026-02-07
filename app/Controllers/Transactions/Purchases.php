<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InvalidTransactionException;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class Purchases extends BaseController
{
    use ResponseTrait;
    
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $warehouseModel;
    protected $stockService;
    protected $balanceService;
    
    public function __construct()
    {
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->purchaseOrderDetailModel = new \App\Models\PurchaseOrderDetailModel();
        $this->supplierModel = new \App\Models\SupplierModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->stockService = new StockService();
        $this->balanceService = new BalanceService();
    }
    
    /**
     * Display list of all purchase orders with filters
     */
    public function index()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'status' => $this->request->getGet('status')
        ];

        $query = $this->purchaseOrderModel
            ->select('purchase_orders.*, suppliers.name as nama_supplier')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id');

        // Apply filters
        if ($filters['start_date']) {
            $query->where('DATE(purchase_orders.tanggal_po) >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->where('DATE(purchase_orders.tanggal_po) <=', $filters['end_date']);
        }
        if ($filters['supplier_id']) {
            $query->where('purchase_orders.id_supplier', $filters['supplier_id']);
        }
        if ($filters['status']) {
            $query->where('purchase_orders.status', $filters['status']);
        }

        $data = [
            'title' => 'Pembelian',
            'purchaseOrders' => $query->orderBy('purchase_orders.tanggal_po', 'DESC')->findAll(),
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
            'filters' => $filters
        ];
        
        return view('transactions/purchases/index', $data);
    }
    
    /**
     * Show create purchase order form
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Pesanan Pembelian',
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'nomor_po' => $this->generateNomorPO()
        ];
        
        return view('transactions/purchases/create', $data);
    }
    
    /**
     * Create new purchase order with stock addition and balance update
     * Pattern: Inverse of Sales.storeCash (adds stock instead of deducting)
     */
    public function store()
    {
        $rules = [
            'nomor_po' => 'required|is_unique[purchase_orders.nomor_po]',
            'tanggal_po' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_warehouse' => 'required|is_natural_no_zero',
            'estimasi_tanggal' => 'required|valid_date[Y-m-d]',
            'status' => 'required|in_list[Dipesan,Diterima Sebagian,Diterima Semua,Dibatalkan]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
            'produk.*.harga_beli' => 'required|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $supplierId = $this->request->getPost('id_supplier');
            $warehouseId = $this->request->getPost('id_warehouse');
            $produk = $this->request->getPost('produk');
            
            // Validate supplier exists
            $supplier = $this->supplierModel->find($supplierId);
            if (!$supplier) {
                throw new InvalidTransactionException('Supplier tidak ditemukan');
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
            
            // Validate all products exist and get fresh prices from DB
            $totalBayar = 0;
            $itemsData = [];
            
            foreach ($produk as $item) {
                $product = $this->productModel->find($item['id_produk']);
                if (!$product) {
                    throw new InvalidTransactionException('Produk ID ' . $item['id_produk'] . ' tidak ditemukan');
                }
                
                $qty = (int)$item['jumlah'];
                $hargaBeli = (float)$item['harga_beli'];
                $subtotal = $qty * $hargaBeli;
                $totalBayar += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal
                ];
            }
            
            // Create purchase order record
            $purchaseOrderData = [
                'nomor_po' => $this->request->getPost('nomor_po'),
                'tanggal_po' => $this->request->getPost('tanggal_po'),
                'id_supplier' => $supplierId,
                'id_warehouse' => $warehouseId,
                'estimasi_tanggal' => $this->request->getPost('estimasi_tanggal'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_bayar' => $totalBayar,
                'id_user' => session()->get('id_user')
            ];
            
            $idPO = $this->purchaseOrderModel->insert($purchaseOrderData);
            
            // Create purchase order details and add stock
            foreach ($itemsData as $item) {
                // Create detail record
                $detailData = [
                    'id_po' => $idPO,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                    'jumlah_diterima' => 0
                ];
                
                $this->purchaseOrderDetailModel->insert($detailData);
                
                // Add stock via StockService (inverse of sales deduction)
                $this->stockService->addStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'PURCHASE',
                    $idPO,
                    'PO: ' . $purchaseOrderData['nomor_po']
                );
            }
            
            // Update supplier debt balance
            $this->balanceService->calculateSupplierDebt($supplierId);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases/' . $idPO)
                ->with('success', 'Pesanan pembelian berhasil dibuat');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Show edit form for purchase order
     * Only allowed if not fully received
     */
    public function edit($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
        // Check if already fully received
        if ($purchaseOrder['status'] === 'Diterima Semua') {
            return redirect()->back()->with('error', 'Pesanan pembelian yang sudah diterima penuh tidak dapat diubah');
        }
        
        $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
         $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
         $purchaseOrder['details'] = $this->purchaseOrderDetailModel
             ->select('purchase_order_details.*, products.name, products.sku')
             ->join('products', 'products.id = purchase_order_details.product_id')
             ->where('purchase_order_details.po_id', $id)
             ->findAll();
         
         $data = [
             'title' => 'Ubah Pesanan Pembelian',
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll()
        ];
        
        return view('transactions/purchases/edit', $data);
    }
    
    /**
     * Update purchase order
     * Reverts old stock additions and creates new ones
     * Recalculates supplier debt
     */
    public function update($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
        // Check if fully received
        if ($purchaseOrder['status'] === 'Diterima Semua') {
            return redirect()->back()->with('error', 'Pesanan pembelian yang sudah diterima penuh tidak dapat diubah');
        }
        
        $rules = [
            'nomor_po' => "required|is_unique[purchase_orders.nomor_po,id_po,{$id}]",
            'tanggal_po' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_warehouse' => 'required|is_natural_no_zero',
            'estimasi_tanggal' => 'required|valid_date[Y-m-d]',
            'status' => 'required|in_list[Dipesan,Diterima Sebagian,Diterima Semua,Dibatalkan]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
            'produk.*.harga_beli' => 'required|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $supplierId = $this->request->getPost('id_supplier');
            $warehouseId = $this->request->getPost('id_warehouse');
            $produk = $this->request->getPost('produk');
            
            // Validate supplier exists
            $supplier = $this->supplierModel->find($supplierId);
            if (!$supplier) {
                throw new InvalidTransactionException('Supplier tidak ditemukan');
            }
            
            // Validate warehouse exists
            $warehouse = $this->warehouseModel->find($warehouseId);
            if (!$warehouse) {
                throw new InvalidTransactionException('Gudang tidak ditemukan');
            }
            
            // Get old details to revert stock
            $oldDetails = $this->purchaseOrderDetailModel->where('id_po', $id)->findAll();
            
            // Revert old stock additions
            foreach ($oldDetails as $detail) {
                try {
                    // Deduct the previously added stock
                    $this->stockService->deductStock(
                        $detail['id_produk'],
                        $purchaseOrder['id_warehouse'],
                        $detail['jumlah'],
                        'PURCHASE_REVERSAL',
                        $id,
                        'Pembalikan PO: ' . $purchaseOrder['nomor_po']
                    );
                } catch (\Exception $e) {
                    // Log the error but continue
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                }
            }
            
            // Validate all products exist and get fresh prices
            $totalBayar = 0;
            $itemsData = [];
            
            foreach ($produk as $item) {
                $product = $this->productModel->find($item['id_produk']);
                if (!$product) {
                    throw new InvalidTransactionException('Produk ID ' . $item['id_produk'] . ' tidak ditemukan');
                }
                
                $qty = (int)$item['jumlah'];
                $hargaBeli = (float)$item['harga_beli'];
                $subtotal = $qty * $hargaBeli;
                $totalBayar += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal
                ];
            }
            
            // Update purchase order
            $purchaseOrderData = [
                'nomor_po' => $this->request->getPost('nomor_po'),
                'tanggal_po' => $this->request->getPost('tanggal_po'),
                'id_supplier' => $supplierId,
                'id_warehouse' => $warehouseId,
                'estimasi_tanggal' => $this->request->getPost('estimasi_tanggal'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_bayar' => $totalBayar,
                'id_user' => session()->get('id_user')
            ];
            
            $this->purchaseOrderModel->update($id, $purchaseOrderData);
            
            // Delete old details
            $this->purchaseOrderDetailModel->where('id_po', $id)->delete();
            
            // Create new details and add stock
            foreach ($itemsData as $item) {
                $detailData = [
                    'id_po' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                    'jumlah_diterima' => 0
                ];
                
                $this->purchaseOrderDetailModel->insert($detailData);
                
                // Add new stock
                $this->stockService->addStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'PURCHASE',
                    $id,
                    'PO: ' . $purchaseOrderData['nomor_po']
                );
            }
            
            // Recalculate supplier debt
            $this->balanceService->calculateSupplierDebt($supplierId);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases/' . $id)
                ->with('success', 'Pesanan pembelian berhasil diubah');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete purchase order (soft delete)
     * Reverts all stock additions
     * Recalculates supplier debt
     */
    public function delete($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get all details
            $details = $this->purchaseOrderDetailModel->where('id_po', $id)->findAll();
            
            // Revert stock for each item
            foreach ($details as $detail) {
                try {
                    $this->stockService->deductStock(
                        $detail['id_produk'],
                        $purchaseOrder['id_warehouse'],
                        $detail['jumlah'],
                        'PURCHASE_REVERSAL',
                        $id,
                        'Penghapusan PO: ' . $purchaseOrder['nomor_po']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                }
            }
            
            // Delete details
            $this->purchaseOrderDetailModel->where('id_po', $id)->delete();
            
            // Soft delete purchase order
            $this->purchaseOrderModel->delete($id);
            
            // Recalculate supplier debt
            $this->balanceService->calculateSupplierDebt($purchaseOrder['id_supplier']);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases')->with('success', 'Pesanan pembelian berhasil dihapus');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Show receive form for purchase order
     */
    public function receive($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
        if ($purchaseOrder['status'] === 'Diterima Semua') {
            return redirect()->back()->with('error', 'Pesanan pembelian sudah diterima penuh');
        }
        
         $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
         $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
         $purchaseOrder['details'] = $this->purchaseOrderDetailModel
             ->select('purchase_order_details.*, products.name, products.sku')
             ->join('products', 'products.id = purchase_order_details.product_id')
             ->where('purchase_order_details.po_id', $id)
             ->findAll();
         
         $data = [
             'title' => 'Terima Pesanan Pembelian',
            'purchaseOrder' => $purchaseOrder,
            'warehouses_good' => $this->warehouseModel->where('jenis', 'Baik')->findAll(),
            'warehouses_damaged' => $this->warehouseModel->where('jenis', 'Rusak')->findAll()
        ];
        
        return view('transactions/purchases/receive', $data);
    }
    
    /**
     * Process purchase order receipt
     * Updates stock status and warehouse allocation
     */
    public function processReceive($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
        $rules = [
            'tanggal_terima' => 'required|valid_date[Y-m-d]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah_diterima' => 'required|greater_than_equal_to[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $tanggalTerima = $this->request->getPost('tanggal_terima');
            $produk = $this->request->getPost('produk');
            
            $allReceived = true;
            $someReceived = false;
            
            foreach ($produk as $item) {
                $idDetail = $item['id_detail'];
                $jumlahDiterima = (int)$item['jumlah_diterima'];
                $jumlahBaik = (int)($item['jumlah_baik'] ?? 0);
                $jumlahRusak = (int)($item['jumlah_rusak'] ?? 0);
                
                // Get current detail
                $detail = $this->purchaseOrderDetailModel->find($idDetail);
                if (!$detail) {
                    throw new InvalidTransactionException('Detail pesanan pembelian tidak ditemukan');
                }
                
                // Validate received quantity
                if (($jumlahBaik + $jumlahRusak) > $jumlahDiterima) {
                    throw new InvalidTransactionException('Total barang diterima melebihi jumlah yang diterima');
                }
                
                if ($jumlahDiterima > ($detail['jumlah'] - $detail['jumlah_diterima'])) {
                    throw new InvalidTransactionException('Jumlah diterima melebihi sisa pesanan');
                }
                
                // Update detail
                $newJumlahDiterima = $detail['jumlah_diterima'] + $jumlahDiterima;
                $this->purchaseOrderDetailModel->update($idDetail, [
                    'jumlah_diterima' => $newJumlahDiterima
                ]);
                
                // Check if fully received
                if ($newJumlahDiterima < $detail['jumlah']) {
                    $allReceived = false;
                }
                
                if ($jumlahDiterima > 0) {
                    $someReceived = true;
                }
                
                // Log the received items via StockService
                // Since we already added stock during PO creation, we just log the receipt
                if ($jumlahBaik > 0 || $jumlahRusak > 0) {
                    $warehouseGood = $item['id_warehouse_baik'] ?? $purchaseOrder['id_warehouse'];
                    $warehouseDamaged = $item['id_warehouse_rusak'] ?? null;
                    
                    if ($jumlahBaik > 0) {
                        $this->stockService->logStockMovement(
                            $detail['id_produk'],
                            $warehouseGood,
                            $jumlahBaik,
                            0,
                            0, // balance will be updated by StockService
                            'PURCHASE_RECEIVED',
                            $id,
                            'Penerimaan PO: ' . $purchaseOrder['nomor_po']
                        );
                    }
                    
                    if ($jumlahRusak > 0 && $warehouseDamaged) {
                        $this->stockService->logStockMovement(
                            $detail['id_produk'],
                            $warehouseDamaged,
                            $jumlahRusak,
                            0,
                            0,
                            'PURCHASE_RECEIVED_DAMAGED',
                            $id,
                            'Penerimaan PO (Rusak): ' . $purchaseOrder['nomor_po']
                        );
                    }
                }
            }
            
            // Update PO status
            $newStatus = 'Dibatalkan';
            if ($someReceived) {
                $newStatus = $allReceived ? 'Diterima Semua' : 'Diterima Sebagian';
            }
            
            $this->purchaseOrderModel->update($id, [
                'status' => $newStatus
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases')->with('success', 'Pesanan pembelian berhasil diterima');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menerima pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Show purchase order detail
     */
    public function detail($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Pesanan pembelian tidak ditemukan');
        }
        
         $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
         $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
         $purchaseOrder['details'] = $this->purchaseOrderDetailModel
             ->select('purchase_order_details.*, products.name, products.sku')
             ->join('products', 'products.id = purchase_order_details.product_id')
             ->where('purchase_order_details.po_id', $id)
             ->findAll();
         
         $data = [
             'title' => 'Detail Pesanan Pembelian',
            'purchaseOrder' => $purchaseOrder
        ];
        
        return view('transactions/purchases/detail', $data);
    }
    
    /**
     * AJAX endpoint to get product price
     */
    public function getProductPrice()
    {
        $idSupplier = $this->request->getPost('id_supplier');
        $idProduct = $this->request->getPost('id_produk');
        
        $product = $this->productModel->find($idProduct);
        if (!$product) {
            return $this->respond(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
        }
        
        return $this->respond([
            'status' => 'success',
            'harga_beli_terakhir' => $product['harga_beli_terakhir'] ?? 0,
            'stok' => $product['stok'] ?? 0
        ]);
    }
    
    /**
     * Generate unique PO number with date prefix
     * Format: PO-202501001, PO-202501002, etc.
     */
    private function generateNomorPO()
    {
        $prefix = 'PO-' . date('Ym');
        
        $lastPO = $this->purchaseOrderModel
            ->like('nomor_po', $prefix, 'after')
            ->orderBy('nomor_po', 'DESC')
            ->first();
        
        if ($lastPO) {
            $lastNumber = (int) substr($lastPO['nomor_po'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}