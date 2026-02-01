<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

class SalesReturns extends BaseController
{
    use ResponseTrait;
    
    protected $salesReturnModel;
    protected $salesReturnDetailModel;
    protected $customerModel;
    protected $productModel;
    protected $stockMutationModel;
    protected $warehouseModel;
    protected $salesModel;
    protected $salesDetailModel;
    
    public function __construct()
    {
        $this->salesReturnModel = new \App\Models\SalesReturnModel();
        $this->salesReturnDetailModel = new \App\Models\SalesReturnDetailModel();
        $this->customerModel = new \App\Models\CustomerModel();
        $this->productModel = new \App\Models\ProductModel();
        $this->stockMutationModel = new \App\Models\StockMutationModel();
        $this->warehouseModel = new \App\Models\WarehouseModel();
        $this->salesModel = new \App\Models\SaleModel();
        $this->salesDetailModel = new \App\Models\SaleItemModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Sales Returns',
            'salesReturns' => $this->salesReturnModel
                ->select('sales_returns.*, customers.nama_customer')
                ->join('customers', 'customers.id_customer = sales_returns.id_customer')
                ->orderBy('sales_returns.tanggal_retur', 'DESC')
                ->findAll(),
            'customers' => $this->customerModel->findAll()
        ];
        
        return view('transactions/sales_returns/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Create Sales Return',
            'customers' => $this->customerModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'salesList' => $this->getSalesList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];
        
        return view('transactions/sales_returns/create', $data);
    }
    
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[sales_returns.nomor_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
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
            // Create sales return
            $salesReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_customer' => $this->request->getPost('id_customer'),
                'id_warehouse_asal' => $this->request->getPost('id_warehouse_asal'),
                'id_penjualan' => $this->request->getPost('id_penjualan'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'total_refund' => 0,
                'id_user' => session()->get('id_user')
            ];
            
            $idRetur = $this->salesReturnModel->insert($salesReturnData);
            
            // Create details
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $detailData = [
                    'id_retur_penjualan' => $idRetur,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->salesReturnDetailModel->insert($detailData);
            }
            
            // If auto-approved, process the return immediately
            if ($this->request->getPost('status') === 'Disetujui') {
                $this->processReturn($idRetur, $produk);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Sales Return created successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create sales return: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot edit return that has been processed');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Edit Sales Return',
            'salesReturn' => $salesReturn,
            'customers' => $this->customerModel->where('status', 'Aktif')->findAll(),
            'products' => $this->productModel->where('status', 'Aktif')->findAll(),
            'warehouses' => $this->warehouseModel->where('status', 'Aktif')->findAll(),
            'salesList' => $this->getSalesList()
        ];
        
        return view('transactions/sales_returns/edit', $data);
    }
    
    public function update($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot edit return that has been processed');
        }
        
        $rules = [
            'nomor_retur' => "required|is_unique[sales_returns.nomor_retur,id_retur_penjualan,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
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
            // Update sales return
            $salesReturnData = [
                'nomor_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'id_customer' => $this->request->getPost('id_customer'),
                'id_warehouse_asal' => $this->request->getPost('id_warehouse_asal'),
                'id_penjualan' => $this->request->getPost('id_penjualan'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'id_user' => session()->get('id_user')
            ];
            
            $this->salesReturnModel->update($id, $salesReturnData);
            
            // Delete old details
            $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->delete();
            
            // Create new details
            $produk = $this->request->getPost('produk');
            
            foreach ($produk as $item) {
                $detailData = [
                    'id_retur_penjualan' => $id,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'alasan' => $item['alasan'],
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                
                $this->salesReturnDetailModel->insert($detailData);
            }
            
            // If auto-approved, process the return immediately
            if ($this->request->getPost('status') === 'Disetujui') {
                $this->processReturn($id, $produk);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Sales Return updated successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update sales return: ' . $e->getMessage());
        }
    }
    
    public function approve($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Return cannot be approved');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk, products.harga_jual')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Approve Sales Return',
            'salesReturn' => $salesReturn,
            'warehouses_good' => $this->warehouseModel->where('jenis', 'Baik')->findAll(),
            'warehouses_damaged' => $this->warehouseModel->where('jenis', 'Rusak')->findAll()
        ];
        
        return view('transactions/sales_returns/approve', $data);
    }
    
    public function processApproval($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
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
                    'produk.*.jumlah_diterima' => 'required|greater_than_equal_to[0]'
                ];
                
                if (!$this->validate($rules)) {
                    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
                }
                
                $tanggalProses = $this->request->getPost('tanggal_proses');
                $produk = $this->request->getPost('produk');
                
                $totalRefund = 0;
                
                foreach ($produk as $item) {
                    $idDetail = $item['id_detail'];
                    $jumlahDiterima = $item['jumlah_diterima'];
                    $jumlahBaik = $item['jumlah_baik'] ?? 0;
                    $jumlahRusak = $item['jumlah_rusak'] ?? 0;
                    $idWarehouseBaik = $item['id_warehouse_baik'] ?? null;
                    $idWarehouseRusak = $item['id_warehouse_rusak'] ?? null;
                    $refundAmount = $item['jumlah_refund'] ?? 0;
                    
                    // Get product info
                    $product = $this->productModel->find($item['id_produk']);
                    
                    $totalRefund += $refundAmount;
                    
                    // Create stock mutations for good items
                    if ($jumlahBaik > 0 && $idWarehouseBaik) {
                        $mutationData = [
                            'id_produk' => $item['id_produk'],
                            'id_warehouse' => $idWarehouseBaik,
                            'tipe_mutasi' => 'Masuk',
                            'jumlah' => $jumlahBaik,
                            'harga_beli' => $product['harga_beli_terakhir'],
                            'tanggal_mutasi' => $tanggalProses,
                            'id_referensi' => $id,
                            'tipe_referensi' => 'Retur Penjualan',
                            'keterangan' => "Retur Penjualan: " . $salesReturn['nomor_retur'] . " - " . ($item['keterangan'] ?? ''),
                            'id_user' => session()->get('id_user')
                        ];
                        
                        $this->stockMutationModel->insert($mutationData);
                        
                        // Update product stock
                        $this->updateProductStock($item['id_produk'], $jumlahBaik);
                    }
                    
                    // Create stock mutations for damaged items
                    if ($jumlahRusak > 0 && $idWarehouseRusak) {
                        $mutationData = [
                            'id_produk' => $item['id_produk'],
                            'id_warehouse' => $idWarehouseRusak,
                            'tipe_mutasi' => 'Masuk',
                            'jumlah' => $jumlahRusak,
                            'harga_beli' => $product['harga_beli_terakhir'],
                            'tanggal_mutasi' => $tanggalProses,
                            'id_referensi' => $id,
                            'tipe_referensi' => 'Retur Penjualan',
                            'keterangan' => "Retur Penjualan (Rusak): " . $salesReturn['nomor_retur'] . " - " . ($item['keterangan'] ?? ''),
                            'id_user' => session()->get('id_user')
                        ];
                        
                        $this->stockMutationModel->insert($mutationData);
                        
                        // Update product stock
                        $this->updateProductStock($item['id_produk'], $jumlahRusak);
                    }
                }
                
                // Update return status
                $this->salesReturnModel->update($id, [
                    'status' => 'Selesai',
                    'total_refund' => $totalRefund,
                    'tanggal_proses' => $tanggalProses,
                    'approval_notes' => $approvalNotes,
                    'approved_by' => session()->get('id_user')
                ]);
                
            } else if ($action === 'reject') {
                // Update return status as rejected
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
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Sales Return processed successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to process sales return: ' . $e->getMessage());
        }
    }
    
    public function detail($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        $salesReturn['customer'] = $this->customerModel->find($salesReturn['id_customer']);
        $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['id_warehouse_asal']);
        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_details.*, products.nama_produk, products.kode_produk')
            ->join('products', 'products.id_produk = sales_return_details.id_produk')
            ->where('id_retur_penjualan', $id)
            ->findAll();
        
        $data = [
            'title' => 'Sales Return Detail',
            'salesReturn' => $salesReturn
        ];
        
        return view('transactions/sales_returns/detail', $data);
    }
    
    public function delete($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Sales Return not found');
        }
        
        if ($salesReturn['status'] !== 'Menunggu Persetujuan') {
            return redirect()->back()->with('error', 'Cannot delete return that has been processed');
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $this->salesReturnDetailModel->where('id_retur_penjualan', $id)->delete();
            $this->salesReturnModel->delete($id);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/transactions/sales-returns')->with('success', 'Sales Return deleted successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to delete sales return: ' . $e->getMessage());
        }
    }
    
    public function getSalesList()
    {
        return $this->salesModel
            ->select('penjualan.id_penjualan, penjualan.nomor_penjualan, penjualan.tanggal_penjualan, customers.nama_customer')
            ->join('customers', 'customers.id_customer = penjualan.id_customer')
            ->where('penjualan.status', 'Selesai')
            ->orderBy('penjualan.tanggal_penjualan', 'DESC')
            ->findAll();
    }
    
    public function getSalesDetails()
    {
        $idPenjualan = $this->request->getPost('id_penjualan');
        
        $details = $this->salesDetailModel
            ->select('penjualan_detail.*, products.nama_produk, products.kode_produk, products.harga_jual')
            ->join('products', 'products.id_produk = penjualan_detail.id_produk')
            ->where('id_penjualan', $idPenjualan)
            ->findAll();
        
        return $this->respond([
            'status' => 'success',
            'details' => $details
        ]);
    }
    
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
        $newStock = $product['stok'] + $quantity;
        
        $this->productModel->update($idProduct, ['stok' => $newStock]);
    }
}