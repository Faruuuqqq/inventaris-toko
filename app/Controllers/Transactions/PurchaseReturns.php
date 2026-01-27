<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class PurchaseReturns extends BaseController
{
    use ResponseTrait;
    
    protected $purchaseReturnModel;
    protected $purchaseReturnDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $stockMutationModel;
    protected $warehouseModel;
    protected $purchaseOrderModel;
    protected $purchaseOrderDetailModel;
    
    public function __construct()
    {
        $this->purchaseReturnModel = new \App\Models\PurchaseReturnModel();
        $this->purchaseReturnDetailModel = new \App\Models\PurchaseReturnDetailModel();
        $this->supplierModel = new \App\Models\SupplierModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->stockMutationModel = new \App\Models\StockMutationModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $this->purchaseOrderDetailModel = new \App\Models\PurchaseOrderDetailModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Purchase Returns',
            'purchaseReturns' => $this->purchaseReturnModel
                ->select('purchase_returns.*, suppliers.nama_supplier')
                ->join('suppliers', 'suppliers.id_supplier = purchase_returns.id_supplier')
                ->orderBy('purchase_returns.tanggal_retur', 'DESC')
                ->findAll(),
            'suppliers' => $this->supplierModel->findAll()
        ];
        
        return view('transactions/purchase_returns/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create Purchase Return',
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];
        
        return view('transactions/purchase_returns/create', $data);
    }
    
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[purchase_returns.nomor_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
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
            // Create purchase return
            $purchaseReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_warehouse_asal' => $this->request->getPost('id_warehouse_asal'),
                'id_po' => $this->request->getPost('id_po'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'total_refund' => 0,
                'id_user' => session()->get('id_user')
            ];
            
            $idRetur = $this->purchaseReturnModel->insert($purchaseReturnData);
            
            // Create details
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $detailData = [
                    'id_retur_pembelian' => $idRetur,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->purchaseReturnDetailModel->insert($detailData);
            }
            
            // If auto-approved, process the return immediately
            if ($this->request->getPost('status') === 'Disetujui') {
                $this->processReturn($idRetur, $produk);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Purchase Return created successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase return: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot edit return that has been processed');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Edit Purchase Return',
            'purchaseReturn' => $purchaseReturn,
            'suppliers' => $this->supplierModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList()
        ];
        
        return view('transactions/purchase_returns/edit', $data);
    }
    
    public function update($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot edit return that has been processed');
        }
        
        $rules = [
            'nomor_retur' => "required|is_unique[purchase_returns.nomor_retur,id_retur_pembelian,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
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
            // Update purchase return
            $purchaseReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_warehouse_asal' => $this->request->getPost('id_warehouse_asal'),
                'id_po' => $this->request->getPost('id_po'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'id_user' => session()->get('id_user')
            ];
            
            $this->purchaseReturnModel->update($id, $purchaseReturnData);
            
            // Delete old details
            $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->delete();
            
            // Create new details
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $detailData = [
                    'id_retur_pembelian' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->purchaseReturnDetailModel->insert($detailData);
            }
            
            // If auto-approved, process the return immediately
            if ($this->request->getPost('status') === 'Disetujui') {
                $this->processReturn($id, $produk);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Purchase Return updated successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase return: ' . $e->getMessage());
        }
    }
    
    public function approve($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Return cannot be approved');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk, products.harga_beli_terakhir')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Approve Purchase Return',
            'purchaseReturn' => $purchaseReturn,
            'warehouses' => $this->warehouseModel->findAll()
        ];
        
        return view('transactions/purchase_returns/approve', $data);
    }
    
    public function processApproval($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Return cannot be approved');
        }
        
        $action = $this->request->getPost('action');
        $approvalNotes = $this->request->getPost('approval_notes');
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            if ($action === 'approve') {
                $rules = [
                    'tanggal_proses' => 'required|valid_date[Y-m-d]',
                    'produk' => 'required',
                    'produk.*.id_produk' => 'required|is_natural_no_zero',
                    'produk.*.jumlah_dikembalikan' => 'required|greater_than_equal_to[0]'
                ];
                
                if (!$this->validate($rules)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
                
                $tanggalProses = $this->request->getPost('tanggal_proses');
                $produk = $this->request->getPost('produk');
                
                $totalRefund = 0;
                
                foreach ($produk as $item) {
                    $idDetail = $item['id_detail'];
                    $jumlahDikembalikan = $item['jumlah_dikembalikan'];
                    $refundAmount = $item['jumlah_refund'] ?? 0;
                    
                    // Get product info
                    $product = $this->productModel->find($item['id_produk']);
                    
                    $totalRefund += $refundAmount;
                    
                    // Create stock mutations for returned items (stock out)
                    if ($jumlahDikembalikan > 0) {
                        $mutationData = [
                            'id_produk' => $item['id_produk'],
                            'id_warehouse' => $purchaseReturn['id_warehouse_asal'],
                            'tipe_mutasi' => 'Keluar',
                            'jumlah' => $jumlahDikembalikan,
                            'harga_beli' => $product['harga_beli_terakhir'],
                            'tanggal_mutasi' => $tanggalProses,
                            'id_referensi' => $id,
                            'tipe_referensi' => 'Retur Pembelian',
                            'keterangan' => "Retur Pembelian: " . $purchaseReturn['nomor_retur'] . " - " . ($item['keterangan'] ?? ''),
                            'id_user' => session()->get('id_user')
                        ];
                        
                        $this->stockMutationModel->insert($mutationData);
                        
                        // Update product stock (decrease)
                        $this->updateProductStock($item['id_produk'], -$jumlahDikembalikan);
                    }
                }
                
                // Update return status
                $this->purchaseReturnModel->update($id, [
                    'status' => 'Selesai',
                    'total_refund' => $totalRefund,
                    'tanggal_proses' => $tanggalProses,
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
                
            } else if ($action === 'reject') {
                // Update return status as rejected
                $this->purchaseReturnModel->update($id, [
                    'status' => 'Ditolak',
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Purchase Return processed successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to process purchase return: ' . $e->getMessage());
        }
    }
    
    public function detail($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['id_supplier']);
        $purchaseReturn['warehouse'] = $this->warehouseModel->find($purchaseReturn['id_warehouse_asal']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = purchase_return_details.id_produk')
            ->where('id_retur_pembelian', $id)
            ->findAll();
        
        $data = [
            'title' => 'Purchase Return Detail',
            'purchaseReturn' => $purchaseReturn
        ];
        
        return view('transactions/purchase_returns/detail', $data);
    }
    
    public function delete($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Purchase Return not found');
        }
        
        if ($purchaseReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot delete return that has been processed');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $this->purchaseReturnDetailModel->where('id_retur_pembelian', $id)->delete();
            $this->purchaseReturnModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/purchase-returns')->with('success', 'Purchase Return deleted successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to delete purchase return: ' . $e->getMessage());
        }
    }
    
    public function getPurchaseOrdersList()
    {
        return $this->purchaseOrderModel
            ->select('purchase_orders.id_po, purchase_orders.nomor_po, purchase_orders.tanggal_po, suppliers.nama_supplier')
            ->join('suppliers', 'suppliers.id_supplier = purchase_orders.id_supplier')
            ->where('purchase_orders.status', 'Diterima Semua')
            ->orderBy('purchase_orders.tanggal_po', 'DESC')
            ->findAll();
    }
    
    public function getPurchaseOrderDetails()
    {
        $idPO = $this->request->getPost('id_po');
        
        $details = $this->purchaseOrderDetailModel
            ->select('purchase_order_details.*, products.nama_produk, products.kode_produk, products.harga_beli_terakhir')
            ->join('products', 'products.id_produk = purchase_order_details.id_produk')
            ->where('id_po', $idPO)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'details' => $details
        ]);
    }
    
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
    
    private function processReturn($idRetur, $produk)
    {
        // This method handles automatic return processing
        // Similar logic as in processApproval but with default values
        
        foreach ($produk as $item) {
            // Process return logic here
            // Create stock mutations, update refunds, etc.
        }
    }
    
    private function updateProductStock($idProduct, $quantity)
    {
        $product = $this->productModel->find($idProduct);
        $newStock = $product['stok'] + $quantity; // quantity can be negative
        
        $this->productModel->update($idProduct, ['stok' => $newStock]);
    }
}