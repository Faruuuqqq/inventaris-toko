<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\WarehouseModel;
use App\Services\StockService;
use App\Services\BalanceService;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\CreditLimitExceededException;
use CodeIgniter\API\ResponseTrait;

class Sales extends BaseController
{
    use ResponseTrait;

    protected $saleModel;
    protected $saleItemModel;
    protected $productModel;
    protected $customerModel;
    protected $salespersonModel;
    protected $warehouseModel;
    protected $stockService;
    protected $balanceService;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->warehouseModel = new WarehouseModel();
        $this->stockService = new StockService();
        $this->balanceService = new BalanceService();
    }

    /**
     * View: List all sales transactions
     */
    public function index()
    {
        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'customer_id' => $this->request->getGet('customer_id'),
            'payment_status' => $this->request->getGet('payment_status'),
            'payment_type' => $this->request->getGet('payment_type')
        ];

        $sales = $this->saleModel->getAllSalesWithHidden(
            $filters['customer_id'],
            $filters['payment_type'],
            $filters['date_from'],
            $filters['date_to'],
            $filters['payment_status']
        );

        $data = [
            'title' => 'Daftar Penjualan',
            'subtitle' => 'Riwayat semua transaksi penjualan',
            'sales' => $sales,
            'customers' => $this->customerModel->orderBy('name', 'ASC')->findAll(),
            'filters' => $filters
        ];

        return view('transactions/sales/index', $data);
    }

    /**
     * Show create/type selection page
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Penjualan',
            'subtitle' => 'Pilih tipe penjualan',
        ];
        
        return view('transactions/sales/create', $data);
    }

    /**
     * View: Cash Sales Form
     */
    public function cash()
    {
        $data = [
            'title' => 'Penjualan Tunai',
            'subtitle' => 'Buat transaksi penjualan tunai baru',
            'customers' => $this->customerModel->orderBy('name', 'ASC')->findAll(),
            'salespersons' => $this->salespersonModel->orderBy('name', 'ASC')->findAll(),
            'warehouses' => $this->warehouseModel->orderBy('name', 'ASC')->findAll(),
            'products' => $this->productModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll(),
        ];

        return view('transactions/sales/cash', $data);
    }

    /**
     * View: Credit Sales Form
     */
    public function credit()
    {
        $data = [
            'title' => 'Penjualan Kredit',
            'subtitle' => 'Buat transaksi penjualan kredit baru',
            'customers' => $this->customerModel->orderBy('name', 'ASC')->findAll(),
            'salespersons' => $this->salespersonModel->orderBy('name', 'ASC')->findAll(),
            'warehouses' => $this->warehouseModel->orderBy('name', 'ASC')->findAll(),
            'products' => $this->productModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll(),
        ];

        return view('transactions/sales/credit', $data);
    }

    /**
     * Action: Store Cash Sale
     * Validates, calculates, deducts stock, creates sale record
     */
    public function storeCash()
    {
        // 1. Validation
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'items' => 'required',
            'warehouse_id' => 'required|numeric'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemsJson = $this->request->getPost('items');
        $items = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang dipilih.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Prepare data
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $userId = session()->get('id');
            $warehouseId = (int)$this->request->getPost('warehouse_id');
            $customerId = (int)$this->request->getPost('customer_id');
            $salespersonId = $this->request->getPost('salesperson_id') ?: null;

            // Validate customer exists
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new \Exception("Customer tidak ditemukan");
            }

            // Validate warehouse exists
            $warehouse = $this->warehouseModel->find($warehouseId);
            if (!$warehouse) {
                throw new \Exception("Gudang tidak ditemukan");
            }

            // 3. Calculate total securely and validate stock
            $calculatedTotal = 0;
            $saleItemsData = [];

            foreach ($items as $item) {
                // Fetch fresh product data
                $product = $this->productModel->find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan");
                }

                $qty = (int)$item['quantity'];
                if ($qty <= 0) {
                    continue;
                }

                // Get price from DB (don't trust client)
                $price = (float)$product['price_sell'];
                $discount = (float)($item['discount'] ?? 0);

                // Validate stock BEFORE deducting
                $this->stockService->validateStock($product['id'], $warehouseId, $qty);

                $subtotal = ($price * $qty) - $discount;
                $calculatedTotal += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($saleItemsData)) {
                throw new \Exception("Tidak ada barang yang valid untuk dijual");
            }

            // 4. Create sale record
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'salesperson_id' => $salespersonId,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId,
                'total_amount' => $calculatedTotal,
                'paid_amount' => $calculatedTotal,
                'payment_type' => 'CASH',
                'payment_status' => 'PAID',
                'is_hidden' => 0
            ]);

            if (!$saleId) {
                throw new \Exception("Gagal membuat faktur penjualan");
            }

            // 5. Insert sale items and deduct stock
            foreach ($saleItemsData as $sItem) {
                $sItem['sale_id'] = $saleId;
                $this->saleItemModel->insert($sItem);

                // Deduct stock using StockService
                $this->stockService->deductStock(
                    $sItem['product_id'],
                    $warehouseId,
                    $sItem['quantity'],
                    'SALE',
                    $saleId,
                    "Penjualan Tunai $invoiceNumber"
                );
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi database gagal");
            }

            return redirect()->to("transactions/sales/detail/$saleId")
                ->with('success', "Penjualan tunai berhasil! Invoice: $invoiceNumber");

        } catch (InsufficientStockException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * Action: Store Credit Sale
     * Same as cash but validates credit limit and updates balance
     */
    public function storeCredit()
    {
        // 1. Validation
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'items' => 'required',
            'warehouse_id' => 'required|numeric',
            'due_date' => 'required|valid_date[Y-m-d]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemsJson = $this->request->getPost('items');
        $items = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang dipilih.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Prepare data
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $userId = session()->get('id');
            $warehouseId = (int)$this->request->getPost('warehouse_id');
            $customerId = (int)$this->request->getPost('customer_id');
            $salespersonId = $this->request->getPost('salesperson_id') ?: null;
            $dueDate = $this->request->getPost('due_date');

            // Validate customer exists
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new \Exception("Customer tidak ditemukan");
            }

            // Validate warehouse exists
            $warehouse = $this->warehouseModel->find($warehouseId);
            if (!$warehouse) {
                throw new \Exception("Gudang tidak ditemukan");
            }

            // 3. Calculate total securely and validate stock
            $calculatedTotal = 0;
            $saleItemsData = [];

            foreach ($items as $item) {
                // Fetch fresh product data
                $product = $this->productModel->find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan");
                }

                $qty = (int)$item['quantity'];
                if ($qty <= 0) {
                    continue;
                }

                // Get price from DB (don't trust client)
                $price = (float)$product['price_sell'];
                $discount = (float)($item['discount'] ?? 0);

                // Validate stock BEFORE deducting
                $this->stockService->validateStock($product['id'], $warehouseId, $qty);

                $subtotal = ($price * $qty) - $discount;
                $calculatedTotal += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($saleItemsData)) {
                throw new \Exception("Tidak ada barang yang valid untuk dijual");
            }

            // 4. Check credit limit
            $currentReceivable = (float)($customer['receivable_balance'] ?? 0);
            $creditLimit = (float)($customer['credit_limit'] ?? 0);

            if ($currentReceivable + $calculatedTotal > $creditLimit) {
                throw new CreditLimitExceededException(
                    "Batas kredit akan terlampaui. Limit: " . number_format($creditLimit, 0) .
                    ", Outstanding: " . number_format($currentReceivable, 0) .
                    ", Penjualan baru: " . number_format($calculatedTotal, 0)
                );
            }

            // 5. Create sale record
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'salesperson_id' => $salespersonId,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId,
                'total_amount' => $calculatedTotal,
                'paid_amount' => 0,
                'due_date' => $dueDate,
                'payment_type' => 'CREDIT',
                'payment_status' => 'UNPAID',
                'is_hidden' => 0
            ]);

            if (!$saleId) {
                throw new \Exception("Gagal membuat faktur penjualan");
            }

            // 6. Insert sale items and deduct stock
            foreach ($saleItemsData as $sItem) {
                $sItem['sale_id'] = $saleId;
                $this->saleItemModel->insert($sItem);

                // Deduct stock using StockService
                $this->stockService->deductStock(
                    $sItem['product_id'],
                    $warehouseId,
                    $sItem['quantity'],
                    'SALE',
                    $saleId,
                    "Penjualan Kredit $invoiceNumber"
                );
            }

            // 7. Update customer receivable balance
            $this->balanceService->calculateCustomerReceivable($customerId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi database gagal");
            }

            return redirect()->to("transactions/sales/detail/$saleId")
                ->with('success', "Penjualan kredit berhasil! Invoice: $invoiceNumber");

        } catch (InsufficientStockException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (CreditLimitExceededException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * View: Show single sale detail with items
     */
    public function detail($id)
    {
        $sale = $this->saleModel->find($id);

        if (!$sale) {
            return redirect()->to('/transactions/sales')->with('error', 'Penjualan tidak ditemukan');
        }

        // Get sale items
        $items = $this->saleItemModel
            ->select('sale_items.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $id)
            ->findAll();

        // Get related data
        $customer = $this->customerModel->find($sale['customer_id']);
        $salesperson = $sale['salesperson_id'] ? $this->salespersonModel->find($sale['salesperson_id']) : null;
        $warehouse = $this->warehouseModel->find($sale['warehouse_id']);

        $data = [
            'title' => 'Detail Penjualan',
            'sale' => $sale,
            'items' => $items,
            'customer' => $customer,
            'salesperson' => $salesperson,
            'warehouse' => $warehouse
        ];

        return view('transactions/sales/detail', $data);
    }

    /**
     * View: Edit sale form (only if not fully paid)
     */
    public function edit($id)
    {
        $sale = $this->saleModel->find($id);

        if (!$sale) {
            return redirect()->to('/transactions/sales')->with('error', 'Penjualan tidak ditemukan');
        }

        // Only allow edit if not fully paid
        if ($sale['payment_status'] === 'PAID') {
            return redirect()->to("transactions/sales/detail/$id")
                ->with('warning', 'Penjualan yang sudah dibayar tidak dapat diubah');
        }

        // Get sale items
        $items = $this->saleItemModel
            ->select('sale_items.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $id)
            ->findAll();

        $data = [
            'title' => 'Edit Penjualan',
            'sale' => $sale,
            'items' => $items,
            'customers' => $this->customerModel->orderBy('name', 'ASC')->findAll(),
            'salespersons' => $this->salespersonModel->orderBy('name', 'ASC')->findAll(),
            'warehouses' => $this->warehouseModel->orderBy('name', 'ASC')->findAll(),
            'products' => $this->productModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll(),
        ];

        return view('transactions/sales/edit', $data);
    }

    /**
     * Action: Update sale
     */
    public function update($id)
    {
        $sale = $this->saleModel->find($id);

        if (!$sale) {
            return redirect()->to('/transactions/sales')->with('error', 'Penjualan tidak ditemukan');
        }

        if ($sale['payment_status'] === 'PAID') {
            return redirect()->to("transactions/sales/detail/$id")
                ->with('error', 'Penjualan yang sudah dibayar tidak dapat diubah');
        }

        if (!$this->validate([
            'items' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $itemsJson = $this->request->getPost('items');
        $items = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang dipilih');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $warehouseId = $sale['warehouse_id'];

            // Revert old stock movements
            $oldItems = $this->saleItemModel->where('sale_id', $id)->findAll();
            foreach ($oldItems as $oldItem) {
                $this->stockService->addStock(
                    $oldItem['product_id'],
                    $warehouseId,
                    $oldItem['quantity'],
                    'SALE_REVERSAL',
                    $id,
                    "Pembalikan stok dari edit penjualan"
                );
            }

            // Delete old items
            $this->saleItemModel->where('sale_id', $id)->delete();

            // Calculate new total
            $calculatedTotal = 0;
            $saleItemsData = [];

            foreach ($items as $item) {
                $product = $this->productModel->find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan");
                }

                $qty = (int)$item['quantity'];
                if ($qty <= 0) {
                    continue;
                }

                $price = (float)$product['price_sell'];
                $discount = (float)($item['discount'] ?? 0);

                // Validate stock
                $this->stockService->validateStock($product['id'], $warehouseId, $qty);

                $subtotal = ($price * $qty) - $discount;
                $calculatedTotal += $subtotal;

                $saleItemsData[] = [
                    'sale_id' => $id,
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($saleItemsData)) {
                throw new \Exception("Tidak ada barang yang valid");
            }

            // Check credit limit for credit sales
            if ($sale['payment_type'] === 'CREDIT') {
                $customer = $this->customerModel->find($sale['customer_id']);
                $creditLimit = (float)($customer['credit_limit'] ?? 0);
                $paidAmount = (float)$sale['paid_amount'];
                $newOutstanding = $calculatedTotal - $paidAmount;

                if ($newOutstanding > $creditLimit) {
                    throw new CreditLimitExceededException(
                        "Batas kredit akan terlampaui dengan perubahan ini"
                    );
                }
            }

            // Update sale
            $this->saleModel->update($id, [
                'total_amount' => $calculatedTotal
            ]);

            // Insert new items
            foreach ($saleItemsData as $sItem) {
                $this->saleItemModel->insert($sItem);

                // Deduct stock
                $this->stockService->deductStock(
                    $sItem['product_id'],
                    $warehouseId,
                    $sItem['quantity'],
                    'SALE',
                    $id,
                    "Penjualan Updated"
                );
            }

            // Update balance for credit sales
            if ($sale['payment_type'] === 'CREDIT') {
                $this->balanceService->calculateCustomerReceivable($sale['customer_id']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi database gagal");
            }

            return redirect()->to("transactions/sales/detail/$id")
                ->with('success', 'Penjualan berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * Action: Delete sale (soft delete)
     */
    public function delete($id)
    {
        $sale = $this->saleModel->find($id);

        if (!$sale) {
            return redirect()->to('/transactions/sales')->with('error', 'Penjualan tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get sale items
            $items = $this->saleItemModel->where('sale_id', $id)->findAll();
            $warehouseId = $sale['warehouse_id'];

            // Revert stock movements
            foreach ($items as $item) {
                $this->stockService->addStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'SALE_CANCELLATION',
                    $id,
                    "Pembatalan penjualan"
                );
            }

            // Delete items
            $this->saleItemModel->where('sale_id', $id)->delete();

            // Soft delete sale
            $this->saleModel->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

            // Update balance
            if ($sale['payment_type'] === 'CREDIT') {
                $this->balanceService->calculateCustomerReceivable($sale['customer_id']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi database gagal");
            }

            return redirect()->to('/transactions/sales')
                ->with('success', 'Penjualan berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * Action: Hide/unhide sale (OWNER only)
     */
    public function toggleHide($id)
    {
        if (session()->get('role') !== 'OWNER') {
            return redirect()->back()->with('error', 'Hanya OWNER yang dapat menyembunyikan penjualan');
        }

        try {
            $this->saleModel->toggleHide($id);
            return redirect()->back()->with('success', 'Status penyembunyian penjualan berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * API Helper: Get Products with stock
     */
    public function getProducts()
    {
        $warehouseId = $this->request->getGet('warehouse_id');

        if (!$warehouseId) {
            return $this->respond([], 400);
        }

        $products = $this->productModel
            ->select('products.*, COALESCE(product_stocks.quantity, 0) as stock')
            ->join('product_stocks', 'product_stocks.product_id = products.id AND product_stocks.warehouse_id = ' . (int)$warehouseId, 'left')
            ->where('products.deleted_at', null)
            ->findAll();

        return $this->respond($products);
    }
}
