<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SalespersonModel;
use App\Traits\ApiResponseTrait;
use App\Traits\DebugLoggingTrait;

class DeliveryNote extends BaseController
{
    use ApiResponseTrait;
    use DebugLoggingTrait;

    protected $saleModel;
    protected $saleItemModel;
    protected $customerModel;
    protected $productModel;
    protected $salespersonModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->salespersonModel = new SalespersonModel();
    }

    /**
     * Display delivery note creation form
     */
    public function index()
    {
        // Get all paid/delivered invoices for delivery note creation
        $invoices = $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.created_at, customers.name as customer_name, customers.address as customer_address')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->whereIn('sales.payment_status', ['PAID', 'PARTIAL', 'UNPAID'])
            ->where('sales.deleted_at', null)
            ->orderBy('sales.created_at', 'DESC')
            ->limit(100)
            ->findAll();

        // Get all products for manual item addition
        $products = $this->productModel
            ->select('id, name, unit')
            ->orderBy('name', 'ASC')
            ->findAll();

        // Get active salespersons
        $salespersons = $this->salespersonModel
            ->select('id, name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        // For drivers, we can use salespersons or create a separate driver table
        // For now, use salespersons as drivers
        $drivers = $salespersons;

        $data = [
            'title' => 'Buat Surat Jalan',
            'subtitle' => 'Buat dokumen bukti serah terima barang',
            'invoices' => $invoices,
            'products' => $products,
            'salespersons' => $salespersons,
            'drivers' => $drivers,
        ];

        return view('layout/main', $data)
            . view('transactions/delivery-note/index', $data);
    }

    /**
     * AJAX: Get invoice items for delivery note
     * Called when user selects an invoice
     */
    public function getInvoiceItems($invoiceId)
    {
        if (!$invoiceId) {
            return $this->respondError('Invoice ID required');
        }

        // Get sale details
        $sale = $this->saleModel->find($invoiceId);
        
        if (!$sale) {
            return $this->respondNotFound('Invoice not found');
        }

        // Get sale items
        $items = $this->saleItemModel
            ->select('sale_items.*, products.name as product_name, products.unit')
            ->join('products', 'products.id = sale_items.product_id', 'left')
            ->where('sale_items.sale_id', $invoiceId)
            ->findAll();

        // Get customer details
        $customer = $this->customerModel->find($sale['customer_id']);

        return $this->respondSuccess([
            'sale' => $sale,
            'customer' => $customer,
            'items' => $items
        ], 'Invoice data retrieved successfully');
    }

    /**
     * Store new delivery note
     */
    public function store()
    {
        // Start performance monitoring
        $this->startTimer('delivery_note_creation');
        
        // Log action
        $this->logAction('store', [
            'invoice_id' => $this->request->getPost('invoice_id')
        ]);

        // Comprehensive validation rules
        $validationRules = [
            'invoice_id' => [
                'rules' => 'required|numeric|is_not_unique[sales.id]',
                'errors' => [
                    'required' => 'Invoice harus dipilih',
                    'numeric' => 'Invoice ID harus berupa angka',
                    'is_not_unique' => 'Invoice tidak ditemukan dalam sistem'
                ]
            ],
            'delivery_date' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal pengiriman harus diisi',
                    'valid_date' => 'Format tanggal pengiriman tidak valid (Y-m-d)'
                ]
            ],
            'delivery_address' => [
                'rules' => 'required|min_length[10]|max_length[500]',
                'errors' => [
                    'required' => 'Alamat pengiriman harus diisi',
                    'min_length' => 'Alamat pengiriman minimal 10 karakter',
                    'max_length' => 'Alamat pengiriman maksimal 500 karakter'
                ]
            ],
            'driver_id' => [
                'rules' => 'required|numeric|is_not_unique[salespersons.id]',
                'errors' => [
                    'required' => 'Driver harus dipilih',
                    'numeric' => 'Driver ID harus berupa angka',
                    'is_not_unique' => 'Driver tidak ditemukan dalam sistem'
                ]
            ],
            'salesperson_id' => [
                'rules' => 'required|numeric|is_not_unique[salespersons.id]',
                'errors' => [
                    'required' => 'Salesperson harus dipilih',
                    'numeric' => 'Salesperson ID harus berupa angka',
                    'is_not_unique' => 'Salesperson tidak ditemukan dalam sistem'
                ]
            ],
            'notes' => [
                'rules' => 'permit_empty|max_length[1000]',
                'errors' => [
                    'max_length' => 'Catatan maksimal 1000 karakter'
                ]
            ],
        ];

        if (!$this->validate($validationRules)) {
            $this->logValidationError($this->validator->getErrors());
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $invoiceId = $this->request->getPost('invoice_id');
            $deliveryDate = $this->request->getPost('delivery_date');
            $deliveryAddress = $this->request->getPost('delivery_address');
            $driverId = $this->request->getPost('driver_id');
            $salespersonId = $this->request->getPost('salesperson_id');
            $notes = $this->request->getPost('notes') ?? '';

            // Validate invoice exists and hasn't been delivered yet
            $sale = $this->saleModel->find($invoiceId);
            if (!$sale) {
                throw new \Exception('Invoice tidak ditemukan');
            }

            // Check if delivery note already exists for this invoice
            if (!empty($sale['delivery_number'])) {
                throw new \Exception('Surat jalan sudah dibuat untuk invoice ini: ' . $sale['delivery_number']);
            }

            // Validate delivery date is not in the future
            if (strtotime($deliveryDate) > time()) {
                throw new \Exception('Tanggal pengiriman tidak boleh lebih dari hari ini');
            }

            // Validate delivery date is not before invoice date
            if (strtotime($deliveryDate) < strtotime($sale['created_at'])) {
                throw new \Exception('Tanggal pengiriman tidak boleh lebih awal dari tanggal invoice');
            }

            // Generate delivery note number
            $dnNumber = $this->generateDeliveryNoteNumber();

            // Update sale with delivery info
            $updateResult = $this->saleModel->update($invoiceId, [
                'delivery_date' => $deliveryDate,
                'delivery_address' => $deliveryAddress,
                'delivery_notes' => $notes,
                'delivery_number' => $dnNumber,
                'delivery_driver_id' => $driverId,
            ]);

            if (!$updateResult) {
                throw new \Exception('Gagal menyimpan surat jalan ke database');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Log success
            $this->logSuccess('Delivery note created', $invoiceId);
            
            // Log user activity (audit trail)
            $this->logActivity('Created delivery note', $invoiceId, 'DeliveryNote');
            
            // Check performance
            $duration = $this->stopTimer('delivery_note_creation', false);
            $this->logSlowOperation('Delivery note creation', $duration, 0.5);

            return redirect()->to('transactions/sales/' . $invoiceId)
                ->with('success', "Surat jalan berhasil dibuat dengan nomor: $dnNumber");

        } catch (\Exception $e) {
            $db->transRollback();
            
            // Log error with context
            $this->logError('Failed to create delivery note', $e, [
                'invoice_id' => $invoiceId ?? 'N/A',
                'user_id' => session()->get('user_id')
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Print delivery note
     */
    public function print($id = null)
    {
        // Get delivery note ID from GET parameter if not in URL
        if (!$id) {
            $id = $this->request->getGet('id');
        }

        // Validate ID exists
        if (!$id || !is_numeric($id)) {
            return redirect()->to('transactions/delivery-note')
                ->with('error', 'ID Surat Jalan tidak valid');
        }

        // Get sale details with customer info
        $sale = $this->saleModel
            ->select('sales.*, customers.name as customer_name, customers.address as customer_address, customers.phone as customer_phone, salespersons.name as driver_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->join('salespersons', 'salespersons.id = sales.delivery_driver_id', 'left')
            ->find($id);

        if (!$sale) {
            return redirect()->to('transactions/delivery-note')
                ->with('error', 'Invoice tidak ditemukan');
        }

        // Validate delivery note exists for this sale
        if (empty($sale['delivery_number'])) {
            return redirect()->to('transactions/delivery-note')
                ->with('error', 'Surat jalan belum dibuat untuk invoice ini. Silakan buat surat jalan terlebih dahulu.');
        }

        // Get sale items with product details
        $items = $this->saleItemModel
            ->select('sale_items.*, products.name as product_name, products.unit, products.sku')
            ->join('products', 'products.id = sale_items.product_id', 'left')
            ->where('sale_items.sale_id', $id)
            ->findAll();

        if (empty($items)) {
            return redirect()->to('transactions/delivery-note')
                ->with('error', 'Tidak ada item dalam surat jalan ini');
        }

        $data = [
            'title' => 'Surat Jalan - ' . $sale['delivery_number'],
            'sale' => $sale,
            'items' => $items,
        ];

        // Check if preview mode
        $preview = $this->request->getGet('preview');
        if ($preview) {
            return view('transactions/delivery-note/print', $data);
        }

        // Otherwise return print view
        return view('transactions/delivery-note/print', $data);
    }

    /**
     * Generate delivery note number
     */
    private function generateDeliveryNoteNumber()
    {
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return 'SJ-' . $date . '-' . $random;
    }
}
