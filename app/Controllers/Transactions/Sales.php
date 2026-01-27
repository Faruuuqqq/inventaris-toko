<?php
namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\WarehouseModel;

class Sales extends BaseController
{
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

    public function cash()
    {
        $data = [
            'title' => 'Penjualan Tunai',
            'subtitle' => 'Buat transaksi penjualan tunai',
            'customers' => $this->customerModel->findAll(),
            'salespersons' => $this->salespersonModel->findAll(),
            'warehouses' => $this->warehouseModel->findAll(),
            'products' => $this->productModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('transactions/sales/cash', $data));
    }

    public function storeCash()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Get form data
            $customerId = $this->request->getPost('customer_id');
            $items = $this->request->getPost('items');
            $totalAmount = $this->request->getPost('total_amount');
            $warehouseId = $this->request->getPost('warehouse_id');

            // Insert sale header
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => session()->get('user_id'),
                'salesperson_id' => $this->request->getPost('salesperson_id'),
                'warehouse_id' => $warehouseId,
                'payment_type' => 'CASH',
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount,
                'payment_status' => 'PAID',
                'is_hidden' => 0,
            ]);

            // Insert sale items and update stock
            if (is_array($items)) {
                foreach ($items as $item) {
                    $productId = $item['product_id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $discount = $item['discount'] ?? 0;
                    $subtotal = $price * $quantity - $discount;

                    // Insert item
                    $this->saleItemModel->insert([
                        'sale_id' => $saleId,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    // Update stock
                    $this->productModel->updateStock(
                        $productId,
                        $warehouseId,
                        -$quantity, // Negative for OUT
                        'OUT',
                        $invoiceNumber,
                        'Penjualan ' . $invoiceNumber
                    );
                }
            }

            $db->transComplete();

            return redirect()->to('/transactions/sales/cash')
                ->with('success', "Penjualan tunai {$invoiceNumber} berhasil disimpan");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function credit()
    {
        $data = [
            'title' => 'Penjualan Kredit',
            'subtitle' => 'Buat transaksi penjualan kredit',
            'customers' => $this->customerModel->findAll(),
            'salespersons' => $this->salespersonModel->findAll(),
            'warehouses' => $this->warehouseModel->findAll(),
            'products' => $this->productModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('transactions/sales/credit', $data));
    }

    public function storeCredit()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Get form data
            $customerId = $this->request->getPost('customer_id');
            $items = $this->request->getPost('items');
            $totalAmount = $this->request->getPost('total_amount');
            $warehouseId = $this->request->getPost('warehouse_id');
            $dueDate = $this->request->getPost('due_date');

            // Check credit limit
            if (!$this->customerModel->canMakeCreditPurchase($customerId, $totalAmount)) {
                $customer = $this->customerModel->find($customerId);
                throw new \Exception('Total melebihi limit kredit (' . format_currency($customer['credit_limit']) . ')');
            }

            // Insert sale header
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => session()->get('user_id'),
                'salesperson_id' => $this->request->getPost('salesperson_id'),
                'warehouse_id' => $warehouseId,
                'payment_type' => 'CREDIT',
                'due_date' => $dueDate,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'payment_status' => 'UNPAID',
                'is_hidden' => $this->request->getPost('is_hidden') ? 1 : 0, // OWNER only
            ]);

            // Insert sale items and update stock
            if (is_array($items)) {
                foreach ($items as $item) {
                    $productId = $item['product_id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $discount = $item['discount'] ?? 0;
                    $subtotal = $price * $quantity - $discount;

                    $this->saleItemModel->insert([
                        'sale_id' => $saleId,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    $this->productModel->updateStock(
                        $productId,
                        $warehouseId,
                        -$quantity,
                        'OUT',
                        $invoiceNumber,
                        'Penjualan Kredit ' . $invoiceNumber
                    );
                }
            }

            // Update customer receivable balance
            $this->customerModel->updateReceivableBalance($customerId, $totalAmount);

            $db->transComplete();

            return redirect()->to('/transactions/sales/credit')
                ->with('success', "Penjualan kredit {$invoiceNumber} berhasil disimpan");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function getProducts()
    {
        // For AJAX calls
        $products = $this->productModel->findAll();
        return $this->response->setJSON($products);
    }

    public function getProductDetail($id)
    {
        // For AJAX calls
        $product = $this->productModel->find($id);
        return $this->response->setJSON($product);
    }

    public function printDeliveryNote($id)
    {
        $sale = $this->saleModel->find($id);
        
        if (!$sale) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Penjualan tidak ditemukan');
        }

        $customer = $this->customerModel->find($sale['customer_id']);
        $salesperson = $this->salespersonModel->find($sale['salesperson_id']);
        $warehouse = $this->warehouseModel->find($sale['warehouse_id']);
        $items = $this->saleItemModel->getSaleItems($id);

        foreach ($items as &$item) {
            $product = $this->productModel->find($item['product_id']);
            $item['product_name'] = $product['name'];
            $item['product_code'] = $product['code'];
            $item['unit'] = $product['unit'];
        }

        $data = [
            'sale' => $sale,
            'customer' => $customer,
            'salesperson' => $salesperson,
            'warehouse' => $warehouse,
            'items' => $items,
        ];

        return view('transactions/delivery-note/print', $data);
    }
}