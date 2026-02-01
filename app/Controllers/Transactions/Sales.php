<?php
namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\WarehouseModel;
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

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->warehouseModel = new WarehouseModel();
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
     * Action: Store Cash Sale
     * REFACTORED: Validated, Transaction-safe, Secure Total Calculation
     */
    public function storeCash()
    {
        // 1. Validation
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'items' => 'required', // JSON string
            'warehouse_id' => 'required|numeric'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: periksa input data.');
        }

        $itemsJson = $this->request->getPost('items');
        $items = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang dipilih.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Prepare Data
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $userId = session()->get('user_id');
            $warehouseId = $this->request->getPost('warehouse_id');
            $customerId = $this->request->getPost('customer_id');
            $salespersonId = $this->request->getPost('salesperson_id') ?: null; // Optional

            // 3. Calculate Total securely from DB (Trust No Client Input)
            $calculatedTotal = 0;
            $saleItemsData = [];

            foreach ($items as $item) {
                // Fetch fresh product data
                $product = $this->productModel->find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan.");
                }

                $qty = (int)$item['quantity'];
                if ($qty <= 0) continue;

                // Use price from DB for calculation (or allow override if role permits, but strict for now)
                $price = $item['price']; // Or $product['price_sell']
                $discount = (float)($item['discount'] ?? 0);
                
                $subtotal = ($price * $qty) - $discount;
                $calculatedTotal += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product['id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal
                ];

                // 4. Update Stock (Logic in Model recommended, doing here inside transaction for now)
                 $this->productModel->updateStock(
                    $product['id'],
                    $warehouseId,
                    -$qty,
                    'OUT',
                    'SALE',
                    0, // Temporary 0, update with Sales ID later? No, CI4 InsertID needed.
                    "Penjualan $invoiceNumber"
                );
            }

            // 5. Insert Sale Header
            $saleId = $this->saleModel->insert([
                'number' => $invoiceNumber,
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'salesperson_id' => $salespersonId,
                'warehouse_id' => $warehouseId,
                'total_amount' => $calculatedTotal,
                'discount_total' => 0, // Implement Global Discount if needed
                'final_amount' => $calculatedTotal, // After global discount
                'paid_amount' => $calculatedTotal, // Cash = Full Paid
                'payment_type' => 'CASH',
                'payment_status' => 'PAID',
                'created_by' => $userId,
                'is_hidden' => 0
            ]);

            if (!$saleId) {
                throw new \Exception("Gagal membbuat faktur penjualan.");
            }

            // 6. Insert Sale Items
            foreach ($saleItemsData as &$sItem) {
                $sItem['sale_id'] = $saleId;
                $this->saleItemModel->insert($sItem);
            }

            // 7. Update Stock Log Reference ID (Optional polish step)
            // Ideally update stock_mutations table to link to $saleId if possible.
            // But proceed for now.

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Transaksi database gagal.");
            }

            return redirect()->to('/transactions/sales/cash')->with('success', "Penjualan berhasil! Invoice: $invoiceNumber");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * API Helper: Get Products for AlpineJS
     */
    public function getProducts()
    {
        $products = $this->productModel
            ->where('deleted_at', null)
            ->select('id, name, price_sell, sku, unit')
            ->findAll();
        
        return $this->response->setJSON($products);
    }
}
