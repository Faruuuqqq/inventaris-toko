<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InvalidTransactionException;
use CodeIgniter\API\ResponseTrait;

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
            ->join('customers', 'customers.id = sales_returns.customer_id');

        // Apply filters
        if ($filters['start_date']) {
            $query->where('sales_returns.tanggal_retur >=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->where('sales_returns.tanggal_retur <=', $filters['end_date']);
        }
        if ($filters['customer_id']) {
            $query->where('sales_returns.customer_id', $filters['customer_id']);
        }
        if ($filters['status']) {
            $query->where('sales_returns.status', $filters['status']);
        }

        $data = [
            'title' => 'Retur Penjualan',
            'salesReturns' => $query->orderBy('sales_returns.tanggal_retur', 'DESC')->findAll(),
            'customers' => $this->customerModel->where('is_active', 1)->findAll(),
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
            'customers' => $this->customerModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'salesList' => $this->getSalesList(),
            'nomor_retur' => $this->generateNomorRetur()
        ];

        return view('transactions/sales_returns/create', $data);
    }

    /**
     * Create new sales return
     */
    public function store()
    {
        $rules = [
            'nomor_retur' => 'required|is_unique[sales_returns.no_retur]',
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_penjualan' => 'required|is_natural_no_zero',
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
            $customerId = $this->request->getPost('id_customer');
            $saleId = $this->request->getPost('id_penjualan');
            // Warehouse taken from original sale

            $produk = $this->request->getPost('produk');
            $status = $this->request->getPost('status');

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
            if ($originalSale['customer_id'] != $customerId) {
                throw new InvalidTransactionException('Customer tidak sesuai dengan penjualan asli');
            }

            $warehouseId = $originalSale['warehouse_id'];

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
                    ->where('sale_id', $saleId)
                    ->where('product_id', $item['id_produk'])
                    ->first();

                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam penjualan asli');
                }

                // Validate return qty doesn't exceed original qty
                if ($qty > $originalItem['quantity']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pembelian untuk produk ' . $product['name']);
                }

                $price = $originalItem['price'];
                $subtotal = $qty * $price;
                $totalRefund += $subtotal;

                $itemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Create sales return record
            $salesReturnData = [
                'no_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'customer_id' => $customerId,
                'sale_id' => $saleId,
                'status' => $status,
                'alasan' => $this->request->getPost('alasan') ?? '',
                'total_retur' => $totalRefund,
            ];

            $idRetur = $this->salesReturnModel->insert($salesReturnData);

            // Create sales return details and add stock back
            foreach ($itemsData as $item) {
                $detailData = [
                    'return_id' => $idRetur,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];

                $this->salesReturnDetailModel->insert($detailData);

                // Add stock back via StockService (inverse of sales deduction)
                $this->stockService->addStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'SALES_RETURN',
                    $idRetur,
                    'Retur Penjualan: ' . $salesReturnData['no_retur']
                );
            }

            // If auto-approved, also reduce customer balance
            if ($status === 'Disetujui') {
                $this->balanceService->calculateCustomerReceivable($customerId);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/transactions/sales-returns/detail/' . $idRetur)
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

        $salesReturn['customer'] = $this->customerModel->find($salesReturn['customer_id']);
        $salesReturn['originalSale'] = $this->saleModel->find($salesReturn['sale_id']);

        // Warehouse from original sale
        if ($salesReturn['originalSale']) {
             $salesReturn['warehouse'] = $this->warehouseModel->find($salesReturn['originalSale']['warehouse_id']);
        }

        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_items.*, products.name, products.sku')
            ->join('products', 'products.id = sales_return_items.product_id')
            ->where('return_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Retur Penjualan',
            'salesReturn' => $salesReturn
        ];

        return view('transactions/sales_returns/detail', $data);
    }

    /**
     * Show edit form for sales return
     */
    public function edit($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }

        if ($salesReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }

        $salesReturn['customer'] = $this->customerModel->find($salesReturn['customer_id']);
        $originalSale = $this->saleModel->find($salesReturn['sale_id']);
        if ($originalSale) {
            $salesReturn['warehouse'] = $this->warehouseModel->find($originalSale['warehouse_id']);
        }

        $salesReturn['details'] = $this->salesReturnDetailModel
            ->select('sales_return_items.*, products.name, products.sku')
            ->join('products', 'products.id = sales_return_items.product_id')
            ->where('return_id', $id)
            ->findAll();

        $data = [
            'title' => 'Ubah Retur Penjualan',
            'salesReturn' => $salesReturn,
            'customers' => $this->customerModel->where('is_active', 1)->findAll(),
            'products' => $this->productModel->where('is_active', 1)->findAll(),
            'warehouses' => $this->warehouseModel->where('is_active', 1)->findAll(),
            'salesList' => $this->getSalesList()
        ];

        return view('transactions/sales_returns/edit', $data);
    }

    /**
     * Update sales return
     */
    public function update($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }

        if ($salesReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat diubah');
        }

        $rules = [
            'nomor_retur' => "required|is_unique[sales_returns.no_retur,id,{$id}]",
            'tanggal_retur' => 'required|valid_date[Y-m-d]',
            'id_customer' => 'required|is_natural_no_zero',
            'id_penjualan' => 'required|is_natural_no_zero',
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
            $customerId = $this->request->getPost('id_customer');
            $saleId = $this->request->getPost('id_penjualan');
            $produk = $this->request->getPost('produk');
            $status = $this->request->getPost('status');

            $originalSale = $this->saleModel->find($saleId);
            $warehouseId = $originalSale['warehouse_id'];

            // Get old details to revert stock
            $oldDetails = $this->salesReturnDetailModel->where('return_id', $id)->findAll();

            // Revert old stock additions (deduct stock back)
            foreach ($oldDetails as $detail) {
                try {
                    $this->stockService->deductStock(
                        $detail['product_id'],
                        $warehouseId,
                        $detail['quantity'],
                        'SALES_RETURN_REVERSAL',
                        $id,
                        'Pembalikan retur penjualan: ' . $salesReturn['no_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock for product ' . $detail['id_produk'] . ': ' . $e->getMessage());
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

                $originalItem = $this->saleItemModel
                    ->where('sale_id', $saleId)
                    ->where('product_id', $item['id_produk'])
                    ->first();

                if (!$originalItem) {
                    throw new InvalidTransactionException('Produk tidak ditemukan dalam penjualan asli');
                }

                if ($qty > $originalItem['quantity']) {
                    throw new InvalidTransactionException('Jumlah retur melebihi jumlah pembelian');
                }

                $price = $originalItem['price'];
                $subtotal = $qty * $price;
                $totalRefund += $subtotal;

                $itemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }

            // Update sales return
            $salesReturnData = [
                'no_retur' => $this->request->getPost('nomor_retur'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'customer_id' => $customerId,
                'sale_id' => $saleId,
                'status' => $status,
                'alasan' => $this->request->getPost('alasan') ?? '',
                'total_retur' => $totalRefund,
            ];

            $this->salesReturnModel->update($id, $salesReturnData);

            // Delete old details
            $this->salesReturnDetailModel->where('return_id', $id)->delete();

            // Create new details and add stock back
            foreach ($itemsData as $item) {
                $detailData = [
                    'return_id' => $id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];

                $this->salesReturnDetailModel->insert($detailData);

                // Add new stock back
                $this->stockService->addStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'SALES_RETURN',
                    $id,
                    'Retur Penjualan: ' . $salesReturnData['no_retur']
                );
            }

            if ($status === 'Disetujui') {
                $this->balanceService->calculateCustomerReceivable($customerId);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/transactions/sales-returns/detail/' . $id)
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
     * Delete sales return
     */
    public function delete($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }

        if ($salesReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur yang sudah diproses tidak dapat dihapus');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $originalSale = $this->saleModel->find($salesReturn['sale_id']);
            $warehouseId = $originalSale['warehouse_id'];

            $details = $this->salesReturnDetailModel->where('return_id', $id)->findAll();

            // Revert stock
            foreach ($details as $detail) {
                try {
                    $this->stockService->deductStock(
                        $detail['product_id'],
                        $warehouseId,
                        $detail['quantity'],
                        'SALES_RETURN_REVERSAL',
                        $id,
                        'Penghapusan retur penjualan: ' . $salesReturn['no_retur']
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to revert stock: ' . $e->getMessage());
                }
            }

            // Delete details
            $this->salesReturnDetailModel->where('return_id', $id)->delete();

            // Soft delete sales return
            $this->salesReturnModel->delete($id);

            $this->balanceService->calculateCustomerReceivable($salesReturn['customer_id']);

            $db->transComplete();

            return redirect()->to('/transactions/sales-returns')->with('success', 'Retur penjualan berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus retur penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Process sales return approval or rejection
     */
    public function processApproval($id)
    {
        $salesReturn = $this->salesReturnModel->find($id);
        if (!$salesReturn) {
            return redirect()->to('/transactions/sales-returns')->with('error', 'Retur penjualan tidak ditemukan');
        }

        if ($salesReturn['status'] !== 'Pending') {
            return redirect()->back()->with('error', 'Retur tidak dapat diproses');
        }

        $action = $this->request->getPost('action');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($action === 'approve') {
                $this->salesReturnModel->update($id, ['status' => 'Disetujui']);
                $this->balanceService->calculateCustomerReceivable($salesReturn['customer_id']);

            } else if ($action === 'reject') {
                // Update status to 'Ditolak' and revert stock additions
                $originalSale = $this->saleModel->find($salesReturn['sale_id']);
                $warehouseId = $originalSale['warehouse_id'];

                $details = $this->salesReturnDetailModel->where('return_id', $id)->findAll();

                foreach ($details as $detail) {
                    try {
                        $this->stockService->deductStock(
                            $detail['product_id'],
                            $warehouseId,
                            $detail['quantity'],
                            'SALES_RETURN_REJECTED',
                            $id,
                            'Penolakan retur penjualan: ' . $salesReturn['no_retur']
                        );
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to revert stock: ' . $e->getMessage());
                    }
                }

                $this->salesReturnModel->update($id, ['status' => 'Ditolak']);
            }

            $db->transComplete();

            return redirect()->to('/transactions/sales-returns')->with('success', 'Retur penjualan berhasil diproses');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses retur penjualan: ' . $e->getMessage());
        }
    }

    public function getSalesList()
    {
        return $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.created_at, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_status', 'PAID')
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();
    }

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
            ->select('sale_items.*, products.name, products.sku')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $saleId)
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'sale' => $sale,
            'details' => $details
        ]);
    }

    private function generateNomorRetur()
    {
        $prefix = 'SR-' . date('Ym');

        $lastRetur = $this->salesReturnModel
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
}
