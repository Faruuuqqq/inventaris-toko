<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InvalidTransactionException;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class PurchaseReturns extends BaseController
{
    use ResponseTrait;
    
    protected $purchaseReturnModel;
    protected $purchaseReturnDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $warehouseModel;
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailModel;
    protected $stockService;
    protected $balanceService;
    
    public function __construct()
    {
        $this->purchaseReturnModel = new \App\Models\PurchaseReturnModel();
        $this->purchaseReturnDetailModel = new \App\Models\PurchaseReturnDetailModel();
        $this->supplierModel = new \App\Models\SupplierModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->purchaseOrderDetailModel = new \App\Models\PurchaseOrderDetailModel();
        $this->stockService = new StockService();
        $this->balanceService = new BalanceService();
    }
    
    /**
     * Display list of all purchase returns with filters
     */
    public function index()
    {
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'status' => $this->request->getGet('status')
        ];

        $query = $this->purchaseReturnModel
            ->select('purchase_returns.*, suppliers.nama_supplier')
            ->join('suppliers', 'suppliers.id_supplier = purchase_returns.id_supplier');

        // Apply filters
        if ($filters['start_date']) {
            $query->where('DATE(purchase_returns.tanggal_retur) >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->where('DATE(purchase_returns.tanggal_retur) <=', $filters['end_date']);
        }
        if ($filters['supplier_id']) {
            $query->where('purchase_returns.id_supplier', $filters['supplier_id']);
        }
        if ($filters['status']) {
            $query->where('purchase_returns.status', $filters['status']);
        }

        $data = [
            'title' => 'Retur Pembelian',
            'purchaseReturns' => $query->orderBy('purchase_returns.tanggal_retur', 'DESC')->findAll(),
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'filters' => $filters
        ];
        
        return view('transactions/purchase_returns/index', $data);
    }
    
    /**
     * Show create purchase return form
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Retur Pembelian',
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];
        
        return view('transactions/purchase_returns/create', $data);
    }
    
    /**
     * Create new purchase return (must link to original purchase order)
     * Pattern: Inverse of Purchases.store (deducts stock instead of adding, reduces debt)
     */
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[purchase_returns.nomor_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_po' => 'required|is_natural_no_zero',
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
            $supplierId = $this->request->getPost('id_supplier');
            $poId = $this->request->getPost('id_po');
            $warehouseId = $this->request->getPost('id_warehouse_asal');
            $produk = $this->request->getPost('produk');
            
            // Validate supplier exists
            $supplier = $this->supplierModel->find($supplierId);
            if (!$supplier) {
                throw new InvalidTransactionException('Supplier tidak ditemukan');
            }
            
            // Validate original PO exists
            $originalPO = $this->purchaseOrderModel->find($poId);
            if (!$originalPO) {
                throw new InvalidTransactionException('Pesanan pembelian asli tidak ditemukan');
            }
            
            // Validate supplier matches
            if ($originalPO['id_supplier'] != $supplierId) {
                throw new InvalidTransactionException('Supplier tidak sesuai dengan pesanan pembelian asli');
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
                
                // Get original PO item price
                $originalItem = $this->purchaseOrderDetailModel
                    ->where('id_po', $poId)
                    ->where('id_produk', $item['id_produk'])
                    ->first();
                
                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam pesanan pembelian asli');
                }
                
                // Validate return qty doesn't exceed original qty
                if ($qty > $originalItem['jumlah']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pemesanan untuk produk ' . $product['nama_produk']);
                }
                
                $hargaBeli = $originalItem['harga_beli'];
                $subtotal = $qty * $hargaBeli;
                $totalRefund += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal,
                    'alasan' => $item['alasan'] ?? '',
                    'keterangan' => $item['keterangan'] ?? ''
                ];
            }
            
            // Create purchase return record
            $purchaseReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_supplier' => $supplierId,
                'id_po' => $poId,
                'id_warehouse_asal' => $warehouseId,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_refund' => $totalRefund,
                'id_user' => session()->get('id_user')
            ];
            
            $idRetur = $this->purchaseReturnModel->insert($purchaseReturnData);
            
            // Create purchase return details and deduct stock
            foreach ($itemsData as $item) {
                // Create detail record
                $detailData = [
                    'id_retur_pembelian' => $idRetur,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan']
                ];
                
                $this->purchaseReturnDetailModel->insert($detailData);
                
                // Deduct stock via StockService (inverse of purchase addition)
                $this->stockService->deductStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'PURCHASE_RETURN',
                    $idRetur,
                    'Retur Pembelian: ' . $purchaseReturnData['nomor_retur']
                );
            }
            
            // If auto-approved, also reduce supplier debt balance
            if ($this->request->getPost('status') === 'Disetujui') {
                $this->balanceService->calculateSupplierDebt($supplierId);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns/' . $idRetur)
                ->with('success', 'Retur pembelian berhasil dibuat');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat retur pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Show purchase return detail
     */
    public function detail($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['originalPO'] = $this->purchaseOrderModel->find($purchaseReturn['id_po']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Detail Retur Pembelian',
            'purchaseReturn' => $purchaseReturn
        ];
        
        return view('transactions/purchase_returns/detail', $data);
    }
    
    /**
     * Show edit form for purchase return
     * Only allowed if status = 'Menunggu Persetujuan'
     */
    public function edit($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Ubah Retur Pembelian',
            'purchaseReturn' => $purchaseReturn,
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList()
        ];
        
        return view('transactions/purchase_returns/edit', $data);
    }
    
    /**
     * Update purchase return
     * Reverts old stock deductions and creates new ones
     * Recalculates supplier balance
     */
    public function update($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }
        
        $rules = [
            'nomor_retur' => "required|is_unique[purchase_returns.nomor_retur,id_retur_pembelian,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_po' => 'required|is_natural_no_zero',
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
            $supplierId = $this->request->getPost('id_supplier');
            $poId = $this->request->getPost('id_po');
            $warehouseId = $this->request->getPost('id_warehouse_asal');
            $produk = $this->request->getPost('produk');
            
            // Get old details to revert stock
            $oldDetails = $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->findAll();
            
            // Revert old stock deductions (add stock back)
            foreach ($oldDetails as $detail) {
                try {
                    $this->stockService->addStock(
                        $detail['id_produk'],
                        $purchaseReturn['id_warehouse_asal'],
                        $detail['jumlah'],
                        'PURCHASE_RETURN_REVERSAL',
                        $id,
                        'Pembalikan retur pembelian: ' . $purchaseReturn['nomor_retur']
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
                
                // Get original PO item price
                $originalItem = $this->purchaseOrderDetailModel
                    ->where('id_po', $poId)
                    ->where('id_produk', $item['id_produk'])
                    ->first();
                
                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam pesanan pembelian asli');
                }
                
                if ($qty > $originalItem['jumlah']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pemesanan untuk produk ' . $product['nama_produk']);
                }
                
                $hargaBeli = $originalItem['harga_beli'];
                $subtotal = $qty * $hargaBeli;
                $totalRefund += $subtotal;
                
                $itemsData[] = [
                    'id_produk' => $product['id_produk'],
                    'qty' => $qty,
                    'harga_beli' => $hargaBeli,
                    'subtotal' => $subtotal,
                    'alasan' => $item['alasan'] ?? '',
                    'keterangan' => $item['keterangan'] ?? ''
                ];
            }
            
            // Update purchase return
            $purchaseReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_supplier' => $supplierId,
                'id_po' => $poId,
                'id_warehouse_asal' => $warehouseId,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? '',
                'total_refund' => $totalRefund,
                'id_user' => session()->get('id_user')
            ];
            
            $this->purchaseReturnModel->update($id, $purchaseReturnData);
            
            // Delete old details
            $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->delete();
            
            // Create new details and deduct stock
            foreach ($itemsData as $item) {
                $detailData = [
                    'id_retur_pembelian' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan']
                ];
                
                $this->purchaseReturnDetailModel->insert($detailData);
                
                // Deduct new stock
                $this->stockService->deductStock(
                    $item['id_produk'],
                    $warehouseId,
                    $item['qty'],
                    'PURCHASE_RETURN',
                    $id,
                    'Retur Pembelian: ' . $purchaseReturnData['nomor_retur']
                );
            }
            
            // Recalculate supplier debt balance
            $this->balanceService->calculateSupplierDebt($supplierId);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns/' . $id)
                ->with('success', 'Retur pembelian berhasil diubah');
            
        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah retur pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete purchase return (soft delete)
     * Reverts all stock deductions
     * Recalculates supplier balance
     */
    public function delete($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat dihapus');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get all details to revert stock
            $details = $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->findAll();
            
            // Revert stock for each item (add stock back)
            foreach ($details as $detail) {
                try {
                    $this->stockService->addStock(
                        $detail['id_produk'],
                        $purchaseReturn['id_warehouse_asal'],
                        $detail['jumlah'],
                        'PURCHASE_RETURN_REVERSAL',
                        $id,
                        'Penghapusan retur pembelian: ' . $purchaseReturn['nomor_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                }
            }
            
            // Delete details
            $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->delete();
            
            // Soft delete purchase return
            $this->purchaseReturnModel->delete($id);
            
            // Recalculate supplier debt balance
            $this->balanceService->calculateSupplierDebt($purchaseReturn['id_supplier']);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Retur pembelian berhasil dihapus');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus retur pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Show approval form for purchase return
     * Only for status = 'Menunggu Persetujuan'
     */
    public function approve($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Retur tidak dapat disetujui');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Setujui Retur Pembelian',
            'purchaseReturn' => $purchaseReturn,
            'warehouses' => $this->warehouseModel->findAll()
        ];
        
        return view('transactions/purchase_returns/approve', $data);
    }
    
    /**
     * Process purchase return approval or rejection
     * Approval: marks as 'Selesai' (stock already deducted during creation)
     * Rejection: adds stock back, marks as 'Ditolak'
     */
    public function processApproval($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
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
                $this->purchaseReturnModel->update($id, [
                    'status' => 'Selesai',
                    'tanggal_proses' => $tanggalProses,
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
                
                // Reduce supplier debt balance since stock already deducted
                $this->balanceService->calculateSupplierDebt($purchaseReturn['id_supplier']);
                
            } else if ($action === 'reject') {
                // Add stock back and update status to 'Ditolak'
                $details = $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->findAll();
                
                // Add stock back for each item
                foreach ($details as $detail) {
                    try {
                        $this->stockService->addStock(
                            $detail['id_produk'],
                            $purchaseReturn['id_warehouse_asal'],
                            $detail['jumlah'],
                            'PURCHASE_RETURN_REJECTED',
                            $id,
                            'Penolakan retur pembelian: ' . $purchaseReturn['nomor_retur']
                        );
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
                    }
                }
                
                // Update status to 'Ditolak'
                $this->purchaseReturnModel->update($id, [
                    'status' => 'Ditolak',
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
                
                // Recalculate supplier debt since stock changed
                $this->balanceService->calculateSupplierDebt($purchaseReturn['id_supplier']);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Retur pembelian berhasil diproses');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses retur pembelian: ' . $e->getMessage());
        }
    }
    
    /**
     * Get list of received purchase orders for return selection
     */
    public function getPurchaseOrdersList()
    {
        return $this->purchaseOrderModel
            ->select('purchase_orders.id_po, purchase_orders.nomor_po, purchase_orders.tanggal_po, suppliers.nama_supplier')
            ->join('suppliers', 'suppliers.id_supplier = purchase_orders.id_supplier')
            ->where('purchase_orders.status', 'Diterima Semua')
            ->orderBy('purchase_orders.tanggal_po', 'DESC')
            ->findAll();
    }
    
    /**
     * AJAX endpoint to get details of a specific purchase order
     */
    public function getPurchaseOrderDetails()
    {
        $poId = $this->request->getPost('id_po');
        
        if (!$poId) {
            return $this->respond(['status' => 'error', 'message' => 'ID PO tidak ditemukan']);
        }
        
        $po = $this->purchaseOrderModel->find($poId);
        if (!$po) {
            return $this->respond(['status' => 'error', 'message' => 'Pesanan pembelian tidak ditemukan']);
        }
        
        $details = $this->purchaseOrderDetailModel
            ->select('purchase_order_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_order_details.id_produk')
            ->where('id_po', $poId)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'po' => $po,
            'details' => $details
        ]);
    }
    
    /**
     * Generate unique return number with date prefix
     * Format: PR-202501001, PR-202501002, etc.
     */
    private function generateNomorRetur()
    {
        $prefix = 'PR-' . date('Ym');
        
        $lastRetur = $this->purchaseReturnModel
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
