<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class Purchases extends BaseController
{
    use ResponseTrait;
    
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $stockMutationModel;
    protected $warehouseModel;
    
    public function __construct()
    {
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->purchaseOrderDetailModel = new \App\Models\PurchaseOrderDetailModel();
        $this->supplierModel = new \App\Models\SupplierModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->stockMutationModel = new \App\Models\StockMutationModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Purchase Orders',
            'purchaseOrders' => $this->purchaseOrderModel
                ->select('purchase_orders.*, suppliers.nama_supplier')
                ->join('suppliers', 'suppliers.id_supplier = purchase_orders.id_supplier')
                ->orderBy('purchase_orders.tanggal_po', 'DESC')
                ->findAll(),
            'suppliers' => $this->supplierModel->findAll()
        ];
        
        return view('transactions/purchases/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create Purchase Order',
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'nomor_po' => $this->generateNomorPO()
        ];
        
        return view('transactions/purchases/create', $data);
    }
    
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
            // Create purchase order
            $purchaseOrderData = [
                'nomor_po' => $this->request->getPost('nomor_po'),
                'tanggal_po' => $this->request->getPost('tanggal_po'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_warehouse' => $this->request->getPost('id_warehouse'),
                'estimasi_tanggal' => $this->request->getPost('estimasi_tanggal'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'total_bayar' => 0,
                'id_user' => session()->get('id_user')
            ];
            
            $idPO = $this->purchaseOrderModel->insert($purchaseOrderData);
            
            // Calculate total and create details
            $totalBayar = 0;
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli'];
                $totalBayar += $subtotal;
                
                $detailData = [
                    'id_po' => $idPO,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                    'jumlah_diterima' => 0,
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->purchaseOrderDetailModel->insert($detailData);
            }
            
            // Update total
            $this->purchaseOrderModel->update($idPO, ['total_bayar' => $totalBayar]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases')->with('success', 'Purchase Order created successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
        }
        
        $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
        $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
        $purchaseOrder['details'] = $this->purchaseOrderDetailModel
            ->select('purchase_order_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_order_details.id_produk')
            ->where('id_po', $id)
            ->findAll();
        
        $data = [
            'title' => 'Edit Purchase Order',
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll()
        ];
        
        return view('transactions/purchases/edit', $data);
    }
    
    public function update($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
        }
        
        // Check if already received (can't edit)
        $receivedDetails = $this->purchaseOrderDetailModel
            ->where('id_po', $id)
            ->where('jumlah_diterima >', 0)
            ->findAll();
            
        if (!empty($receivedDetails)) {
            return redirect()->back()->with('error', 'Cannot edit purchase order that has been partially or fully received');
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
            // Update purchase order
            $purchaseOrderData = [
                'nomor_po' => $this->request->getPost('nomor_po'),
                'tanggal_po' => $this->request->getPost('tanggal_po'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_warehouse' => $this->request->getPost('id_warehouse'),
                'estimasi_tanggal' => $this->request->getPost('estimasi_tanggal'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'id_user' => session()->get('id_user')
            ];
            
            $this->purchaseOrderModel->update($id, $purchaseOrderData);
            
            // Delete old details
            $this->purchaseOrderDetailModel->where('id_po', $id)->delete();
            
            // Calculate total and create new details
            $totalBayar = 0;
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $subtotal = $item['jumlah'] * $item['harga_beli'];
                $totalBayar += $subtotal;
                
                $detailData = [
                    'id_po' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                    'jumlah_diterima' => 0,
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->purchaseOrderDetailModel->insert($detailData);
            }
            
            // Update total
            $this->purchaseOrderModel->update($id, ['total_bayar' => $totalBayar]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases')->with('success', 'Purchase Order updated successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }
    
    public function delete($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
        }
        
        // Check if already received (can't delete)
        $receivedDetails = $this->purchaseOrderDetailModel
            ->where('id_po', $id)
            ->where('jumlah_diterima >', 0)
            ->findAll();
            
        if (!empty($receivedDetails)) {
            return redirect()->back()->with('error', 'Cannot delete purchase order that has been partially or fully received');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $this->purchaseOrderDetailModel->where('id_po', $id)->delete();
            $this->purchaseOrderModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchases')->with('success', 'Purchase Order deleted successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }
    
    public function receive($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
        }
        
        if ($purchaseOrder['status'] === 'Diterima Semua') {
            return redirect()->back()->with('error', 'Purchase Order already fully received');
        }
        
        $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
        $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
        $purchaseOrder['details'] = $this->purchaseOrderDetailModel
            ->select('purchase_order_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_order_details.id_produk')
            ->where('id_po', $id)
            ->findAll();
        
        $data = [
            'title' => 'Receive Purchase Order',
            'purchaseOrder' => $purchaseOrder,
            'warehouses_good' => $this->warehouseModel->where('jenis', 'Baik')->findAll(),
            'warehouses_damaged' => $this->warehouseModel->where('jenis', 'Rusak')->findAll()
        ];
        
        return view('transactions/purchases/receive', $data);
    }
    
    public function processReceive($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
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
                $jumlahDiterima = $item['jumlah_diterima'];
                $jumlahBaik = $item['jumlah_baik'] ?? 0;
                $jumlahRusak = $item['jumlah_rusak'] ?? 0;
                $idWarehouseBaik = $item['id_warehouse_baik'] ?? null;
                $idWarehouseRusak = $item['id_warehouse_rusak'] ?? null;
                
                // Get current detail
                $detail = $this->purchaseOrderDetailModel->find($idDetail);
                
                // Validate received quantity
                if (($jumlahBaik + $jumlahRusak) > $jumlahDiterima) {
                    throw new \Exception('Total received quantity exceeds amount received');
                }
                
                if ($jumlahDiterima > ($detail['jumlah'] - $detail['jumlah_diterima'])) {
                    throw new \Exception('Received quantity exceeds remaining order quantity');
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
                
                // Create stock mutations for good items
                if ($jumlahBaik > 0 && $idWarehouseBaik) {
                    $mutationData = [
                        'id_produk' => $detail['id_produk'],
                        'id_warehouse' => $idWarehouseBaik,
                        'tipe_mutasi' => 'Masuk',
                        'jumlah' => $jumlahBaik,
                        'harga_beli' => $detail['harga_beli'],
                        'tanggal_mutasi' => $tanggalTerima,
                        'id_referensi' => $id,
                        'tipe_referensi' => 'PO',
                        'keterangan' => "Penerimaan PO: " . $purchaseOrder['nomor_po'] . " - " . $detail['keterangan'],
                        'id_user' => session()->get('id_user')
                    ];
                    
                    $this->stockMutationModel->insert($mutationData);
                    
                    // Update product stock
                    $this->updateProductStock($detail['id_produk'], $jumlahBaik);
                }
                
                // Create stock mutations for damaged items
                if ($jumlahRusak > 0 && $idWarehouseRusak) {
                    $mutationData = [
                        'id_produk' => $detail['id_produk'],
                        'id_warehouse' => $idWarehouseRusak,
                        'tipe_mutasi' => 'Masuk',
                        'jumlah' => $jumlahRusak,
                        'harga_beli' => $detail['harga_beli'],
                        'tanggal_mutasi' => $tanggalTerima,
                        'id_referensi' => $id,
                        'tipe_referensi' => 'PO',
                        'keterangan' => "Penerimaan PO (Rusak): " . $purchaseOrder['nomor_po'] . " - " . $detail['keterangan'],
                        'id_user' => session()->get('id_user')
                    ];
                    
                    $this->stockMutationModel->insert($mutationData);
                    
                    // Update product stock
                    $this->updateProductStock($detail['id_produk'], $jumlahRusak);
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
            
            return redirect()->to('/transactions/purchases')->with('success', 'Purchase Order received successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to receive purchase order: ' . $e->getMessage());
        }
    }
    
    public function detail($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);
        if (!$purchaseOrder) {
            return redirect()->to('/transactions/purchases')->with('error', 'Purchase Order not found');
        }
        
        $purchaseOrder['supplier'] = $this->supplierModel->find($purchaseOrder['id_supplier']);
        $purchaseOrder['warehouse'] = $this->warehouseModel->find($purchaseOrder['id_warehouse']);
        $purchaseOrder['details'] = $this->purchaseOrderDetailModel
            ->select('purchase_order_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_order_details.id_produk')
            ->where('id_po', $id)
            ->findAll();
        
        $data = [
            'title' => 'Purchase Order Detail',
            'purchaseOrder' => $purchaseOrder
        ];
        
        return view('transactions/purchases/detail', $data);
    }
    
    public function getProductPrice()
    {
        $idSupplier = $this->request->getPost('id_supplier');
        $idProduct = $this->request->getPost('id_produk');
        
        $product = $this->productModel->find($idProduct);
        if (!$product) {
            return $this->respond(['status' => 'error', 'message' => 'Product not found']);
        }
        
        return $this->respond([
            'status' => 'success',
            'harga_beli_terakhir' => $product['harga_beli_terakhir'],
            'stok' => $product['stok']
        ]);
    }
    
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
    
    private function updateProductStock($idProduct, $quantity)
    {
        $product = $this->productModel->find($idProduct);
        $newStock = $product['stok'] + $quantity;
        
        $this->productModel->update($idProduct, ['stok' => $newStock]);
    }
}