<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InvalidTransactionException;
use CodeIgniter\API\ResponseTrait;

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
            ->select('purchase_returns.*, suppliers.name as nama_supplier')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id');

        // Apply filters
        if ($filters['start_date']) {
            $query->where('purchase_returns.tanggal_retur >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->where('purchase_returns.tanggal_retur <=', $filters['end_date']);
        }
        if ($filters['supplier_id']) {
            $query->where('purchase_returns.supplier_id', $filters['supplier_id']);
        }
        if ($filters['status']) {
            $query->where('purchase_returns.status', $filters['status']);
        }

        $data = [
            'title' => 'Retur Pembelian',
            'purchaseReturns' => $query->orderBy('purchase_returns.tanggal_retur', 'DESC')->findAll(),
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
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
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];

        return view('transactions/purchase_returns/create', $data);
    }

    /**
     * Create new purchase return
     */
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[purchase_returns.no_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_po' => 'required|is_natural_no_zero',
            'id_warehouse_asal' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[Pending,Disetujui,Ditolak]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
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
            $status = $this->request->getPost('status');

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
            if ($originalPO['supplier_id'] != $supplierId) {
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
                    ->where('po_id', $poId)
                    ->where('product_id', $item['id_produk'])
                    ->first();

                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam pesanan pembelian asli');
                }

                // Validate return qty doesn't exceed original qty
                if ($qty > $originalItem['quantity']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pemesanan untuk produk ' . $product['name']);
                }

                $price = $originalItem['price'];
                $totalRefund += ($qty * $price);

                $itemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Create purchase return record
            $purchaseReturnData = [
                'no_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'supplier_id' => $supplierId,
                'po_id' => $poId,
                'status' => $status,
                'alasan' => $this->request->getPost('alasan') ?? '',
                'total_retur' => $totalRefund,
            ];

            $idRetur = $this->purchaseReturnModel->insert($purchaseReturnData);

            // Create purchase return details and deduct stock
            foreach ($itemsData as $item) {
                $detailData = [
                    'return_id' => $idRetur,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];

                $this->purchaseReturnDetailModel->insert($detailData);

                // Deduct stock via StockService (inverse of purchase addition)
                $this->stockService->deductStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'PURCHASE_RETURN',
                    $idRetur,
                    'Retur Pembelian: ' . $purchaseReturnData['no_retur']
                );
            }

            // If auto-approved, also reduce supplier debt balance
            if ($status === 'Disetujui') {
                $this->balanceService->calculateSupplierDebt($supplierId);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/transactions/purchase-returns/detail/' . $idRetur)
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

        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['supplier_id']);
        // Attempt to find warehouse from mutations since it's not in return table
        $warehouseId = $this->getWarehouseFromMutation($id);
        $purchaseReturn['warehouse'] = $warehouseId ? $this->warehouseModel->find($warehouseId) : null;

        $purchaseReturn['originalPO'] = $this->purchaseOrderModel->find($purchaseReturn['po_id']);
        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_items.*, products.name, products.sku')
            ->join('products', 'products.id = purchase_return_items.product_id')
            ->where('return_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Retur Pembelian',
            'purchaseReturn' => $purchaseReturn
        ];

        return view('transactions/purchase_returns/detail', $data);
    }

    /**
     * Show edit form for purchase return
     */
    public function edit($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }

        if ($purchaseReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }

        $purchaseReturn['supplier'] = $this->supplierModel->find($purchaseReturn['supplier_id']);
        $warehouseId = $this->getWarehouseFromMutation($id);
        $purchaseReturn['id_warehouse_asal'] = $warehouseId; // Pass to view

        $purchaseReturn['details'] = $this->purchaseReturnDetailModel
            ->select('purchase_return_items.*, products.name, products.sku')
            ->join('products', 'products.id = purchase_return_items.product_id')
            ->where('return_id', $id)
            ->findAll();

        $data = [
            'title' => 'Ubah Retur Pembelian',
            'purchaseReturn' => $purchaseReturn,
            'suppliers' => $this->supplierModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'purchaseOrdersList' => $this->getPurchaseOrdersList()
        ];

        return view('transactions/purchase_returns/edit', $data);
    }

    /**
     * Update purchase return
     */
    public function update($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }

        if ($purchaseReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }

        $rules = [
            'nomor_retur' => "required|is_unique[purchase_returns.no_retur,id,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_supplier' => 'required|is_natural_no_zero',
            'id_po' => 'required|is_natural_no_zero',
            'id_warehouse_asal' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[Pending,Disetujui,Ditolak]',
            'produk' => 'required',
            'produk.*.id_produk' => 'required|is_natural_no_zero',
            'produk.*.jumlah' => 'required|greater_than[0]',
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
            $status = $this->request->getPost('status');

            // Find old warehouse to revert stock
            $oldWarehouseId = $this->getWarehouseFromMutation($id);
            if (!$oldWarehouseId) {
                $oldWarehouseId = $warehouseId; // Fallback to current if can't find
            }

            // Get old details to revert stock
            $oldDetails = $this->purchaseReturnDetailModel->where('return_id', $id)->findAll();

            // Revert old stock deductions (add stock back)
            foreach ($oldDetails as $detail) {
                try {
                    $this->stockService->addStock(
                        $detail['product_id'],
                        $oldWarehouseId,
                        $detail['quantity'],
                        'PURCHASE_RETURN_REVERSAL',
                        $id,
                        'Pembalikan retur: ' . $purchaseReturn['no_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock: ' . $e->getMessage());
                }
            }

            // Validate products and calculate new total
            $totalRefund = 0;
            $itemsData = [];

            foreach ($produk as $item) {
                $product = $this->productModel->find($item['id_produk']);
                if (!$product) {
                    throw new InvalidTransactionException('Produk ID ' . $item['id_produk'] . ' tidak ditemukan');
                }

                $qty = (int)$item['jumlah'];

                $originalItem = $this->purchaseOrderDetailModel
                    ->where('po_id', $poId)
                    ->where('product_id', $item['id_produk'])
                    ->first();

                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam pesanan pembelian asli');
                }

                if ($qty > $originalItem['quantity']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pemesanan');
                }

                $price = $originalItem['price'];
                $totalRefund += ($qty * $price);

                $itemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Update purchase return
            $purchaseReturnData = [
                'no_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'supplier_id' => $supplierId,
                'po_id' => $poId,
                'status' => $status,
                'alasan' => $this->request->getPost('alasan') ?? '',
                'total_retur' => $totalRefund,
            ];

            $this->purchaseReturnModel->update($id, $purchaseReturnData);

            // Delete old details
            $this->purchaseReturnDetailModel->where('return_id', $id)->delete();

            // Create new details and deduct stock
            foreach ($itemsData as $item) {
                $detailData = [
                    'return_id' => $id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];

                $this->purchaseReturnDetailModel->insert($detailData);

                // Deduct new stock
                $this->stockService->deductStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'PURCHASE_RETURN',
                    $id,
                    'Retur Updated: ' . $purchaseReturnData['no_retur']
                );
            }

            if ($status === 'Disetujui') {
                $this->balanceService->calculateSupplierDebt($supplierId);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/transactions/purchase-returns/detail/' . $id)
                ->with('success', 'Retur pembelian berhasil diubah');

        } catch (InvalidTransactionException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah retur: ' . $e->getMessage());
        }
    }

    /**
     * Delete purchase return
     */
    public function delete($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }

        if ($purchaseReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat dihapus');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Find warehouse
            $warehouseId = $this->getWarehouseFromMutation($id);
            if (!$warehouseId) {
                throw new \Exception("Gudang asal tidak dapat ditemukan, tidak dapat mengembalikan stok");
            }

            $details = $this->purchaseReturnDetailModel->where('return_id', $id)->findAll();

            // Revert stock
            foreach ($details as $detail) {
                $this->stockService->addStock(
                    $detail['product_id'],
                    $warehouseId,
                    $detail['quantity'],
                    'PURCHASE_RETURN_REVERSAL',
                    $id,
                    'Hapus retur: ' . $purchaseReturn['no_retur']
                );
            }

            // Delete details
            $this->purchaseReturnDetailModel->where('return_id', $id)->delete();

            // Soft delete header
            $this->purchaseReturnModel->delete($id);

            $this->balanceService->calculateSupplierDebt($purchaseReturn['supplier_id']);

            $db->transComplete();

            return redirect()->to('/transactions/purchase-returns')->with('success', 'Retur pembelian berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus retur: ' . $e->getMessage());
        }
    }

    /**
     * Process purchase return approval or rejection
     */
    public function processApproval($id)
    {
        $purchaseReturn = $this->purchaseReturnModel->find($id);
        if (!$purchaseReturn) {
            return redirect()->to('/transactions/purchase-returns')->with('error', 'Retur pembelian tidak ditemukan');
        }

        if ($purchaseReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur tidak dapat diproses');
        }

        $action = $this->request->getPost('action');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($action === 'approve') {
                $this->purchaseReturnModel->update($id, ['status' => 'Disetujui']);
                $this->balanceService->calculateSupplierDebt($purchaseReturn['supplier_id']);

            } else if ($action === 'reject') {
                // Add stock back and update status to 'Ditolak'
                $warehouseId = $this->getWarehouseFromMutation($id);
                if (!$warehouseId) {
                    throw new \Exception("Gudang asal tidak ditemukan");
                }

                $details = $this->purchaseReturnDetailModel->where('return_id', $id)->findAll();

                foreach ($details as $detail) {
                    $this->stockService->addStock(
                        $detail['product_id'],
                        $warehouseId,
                        $detail['quantity'],
                        'PURCHASE_RETURN_REJECTED',
                        $id,
                        'Penolakan retur: ' . $purchaseReturn['no_retur']
                    );
                }

                $this->purchaseReturnModel->update($id, ['status' => 'Ditolak']);
                // Balance update not needed as debt wasn't reduced yet (only on approve) or was it?
                // Wait, store() doesn't reduce debt unless status=Disetujui.
                // So rejecting pending return just reverts stock. Correct.
            }

            $db->transComplete();

            return redirect()->to('/transactions/purchase-returns')->with('success', 'Retur pembelian berhasil diproses');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }

    /**
     * Get list of received purchase orders
     */
    public function getPurchaseOrdersList()
    {
        return $this->purchaseOrderModel
            ->select('purchase_orders.id_po, purchase_orders.nomor_po, purchase_orders.tanggal_po, suppliers.name as nama_supplier')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
            ->where('purchase_orders.status', 'Diterima Semua')
            ->orderBy('purchase_orders.tanggal_po', 'DESC')
            ->findAll();
    }

    /**
     * AJAX: Get details of a purchase order
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
            ->select('purchase_order_items.*, products.name, products.sku')
            ->join('products', 'products.id = purchase_order_items.product_id')
            ->where('po_id', $poId)
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'po' => $po,
            'details' => $details
        ]);
    }

    private function generateNomorRetur()
    {
        $prefix = 'PR-' . date('Ym');

        $lastRetur = $this->purchaseReturnModel
            ->like('no_retur', $prefix, 'after')
            ->orderBy('no_retur', 'DESC')
            ->first();

        if ($lastRetur) {
            $lastNumber = (int) substr($lastRetur['no_retur'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Helper to find which warehouse was used for a return
     * by looking up the stock mutation log
     */
    private function getWarehouseFromMutation($returnId)
    {
        $db = \Config\Database::connect();
        // Look for any mutation for this return reference
        // Note: StockService uses 'PURCHASE_RETURN' and referenceId = returnId
        $mutation = $db->table('stock_mutations')
            ->where('reference_number', 'PURCHASE_RETURN-' . $returnId)
            ->orWhere('reference_number LIKE', "%: %" . $returnId) // Fallback if format differs
            ->orderBy('id', 'DESC')
            ->get()->getRow();

        // If not found via standard ref, try to check if we can match by return number?
        // StockService format: "{$type}-{$referenceId}"
        // So 'PURCHASE_RETURN-' . $id should match.

        return $mutation ? $mutation->warehouse_id : null;
    }
}
