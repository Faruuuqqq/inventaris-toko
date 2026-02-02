<?php
namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\SaleModel;
use App\Models\KontraBonModel;
use App\Models\CustomerModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Services\BalanceService;
use App\Traits\ApiResponseTrait;

class Payments extends BaseController
{
    use ApiResponseTrait;

    protected $paymentModel;
    protected $saleModel;
    protected $kontraBonModel;
    protected $customerModel;
    protected $supplierModel;
    protected $poModel;
    protected $balanceService;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->saleModel = new SaleModel();
        $this->kontraBonModel = new KontraBonModel();
        $this->customerModel = new CustomerModel();
        $this->supplierModel = new SupplierModel();
        $this->poModel = new PurchaseOrderModel();
        $this->balanceService = new BalanceService();
    }

    /**
     * Index: Redirect to receivable payments page
     */
    public function index()
    {
        return redirect()->to('finance/payments/receivable');
    }

    /**
     * View: Customer Receivable Payments
     * Lists all customers with outstanding receivables
     */
    public function receivable()
    {
        $customers = $this->customerModel
            ->select('customers.*')
            ->where('customers.receivable_balance >', 0)
            ->orderBy('customers.receivable_balance', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Pembayaran Piutang',
            'subtitle' => 'Catat pembayaran piutang customer',
            'customers' => $customers,
        ];

        return view('layout/main', $data)
            . view('finance/payments/receivable', $data);
    }

    /**
     * Action: Record Customer Payment
     * 
     * Validates and records payment from customer, updates balance.
     * If specific sale is linked, updates that sale's payment status.
     */
    public function storeReceivable()
    {
        // 1. Validation
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|string',
            'payment_date' => 'required|valid_date[Y-m-d]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Fetch and validate data
            $customerId = (int)$this->request->getPost('customer_id');
            $amount = (float)$this->request->getPost('amount');
            $paymentMethod = $this->request->getPost('payment_method');
            $paymentDate = $this->request->getPost('payment_date');
            $notes = $this->request->getPost('notes') ?? '';
            $referenceId = (int)($this->request->getPost('reference_id') ?? 0);
            $userId = session()->get('id');

            // Validate customer exists
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new \Exception('Customer tidak ditemukan');
            }

            // Validate payment amount
            if ($amount > $customer['receivable_balance']) {
                throw new \Exception(
                    'Jumlah pembayaran melebihi saldo piutang. ' .
                    'Piutang: ' . number_format($customer['receivable_balance'], 0) .
                    ', Pembayaran: ' . number_format($amount, 0)
                );
            }

            // 3. Create payment record
            $paymentNumber = $this->paymentModel->generatePaymentNumber();
            $this->paymentModel->insert([
                'payment_number' => $paymentNumber,
                'payment_date' => $paymentDate,
                'type' => 'RECEIVABLE',
                'reference_id' => $referenceId > 0 ? $referenceId : null,
                'amount' => $amount,
                'method' => $paymentMethod,
                'notes' => $notes,
                'user_id' => $userId,
                'customer_id' => $customerId
            ]);

            // 4. Update specific sale if linked
            if ($referenceId > 0) {
                $sale = $this->saleModel->find($referenceId);
                if ($sale && $sale['customer_id'] == $customerId) {
                    $newPaidAmount = (float)$sale['paid_amount'] + $amount;
                    $saleTotal = (float)$sale['total_amount'];
                    
                    // Determine payment status
                    $newStatus = 'UNPAID';
                    if ($newPaidAmount >= $saleTotal) {
                        $newStatus = 'PAID';
                    } elseif ($newPaidAmount > 0 && $newPaidAmount < $saleTotal) {
                        $newStatus = 'PARTIAL';
                    }
                    
                    $this->saleModel->update($referenceId, [
                        'paid_amount' => $newPaidAmount,
                        'payment_status' => $newStatus
                    ]);
                }
            }

            // 5. Recalculate customer receivable balance using BalanceService
            $this->balanceService->calculateCustomerReceivable($customerId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('finance/payments/receivable')
                ->with('success', "Pembayaran piutang berhasil dicatat! No. Bukti: $paymentNumber");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * View: Supplier Payable Payments
     * Lists all suppliers with outstanding payables
     */
    public function payable()
    {
        $suppliers = $this->supplierModel
            ->select('suppliers.*')
            ->where('suppliers.debt_balance >', 0)
            ->orderBy('suppliers.debt_balance', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Pembayaran Utang',
            'subtitle' => 'Catat pembayaran utang ke supplier',
            'suppliers' => $suppliers,
        ];

        return view('layout/main', $data)
            . view('finance/payments/payable', $data);
    }

    /**
     * Action: Record Supplier Payment
     * 
     * Validates and records payment to supplier, updates balance.
     * If specific PO is linked, updates that PO's payment status.
     */
    public function storePayable()
    {
        // 1. Validation
        if (!$this->validate([
            'supplier_id' => 'required|numeric',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|string',
            'payment_date' => 'required|valid_date[Y-m-d]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Fetch and validate data
            $supplierId = (int)$this->request->getPost('supplier_id');
            $amount = (float)$this->request->getPost('amount');
            $paymentMethod = $this->request->getPost('payment_method');
            $paymentDate = $this->request->getPost('payment_date');
            $notes = $this->request->getPost('notes') ?? '';
            $referenceId = (int)($this->request->getPost('reference_id') ?? 0);
            $userId = session()->get('id');

            // Validate supplier exists
            $supplier = $this->supplierModel->find($supplierId);
            if (!$supplier) {
                throw new \Exception('Supplier tidak ditemukan');
            }

            // Validate payment amount
            if ($amount > $supplier['debt_balance']) {
                throw new \Exception(
                    'Jumlah pembayaran melebihi saldo utang. ' .
                    'Utang: ' . number_format($supplier['debt_balance'], 0) .
                    ', Pembayaran: ' . number_format($amount, 0)
                );
            }

            // 3. Create payment record
            $paymentNumber = $this->paymentModel->generatePaymentNumber();
            $this->paymentModel->insert([
                'payment_number' => $paymentNumber,
                'payment_date' => $paymentDate,
                'type' => 'PAYABLE',
                'reference_id' => $referenceId > 0 ? $referenceId : null,
                'amount' => $amount,
                'method' => $paymentMethod,
                'notes' => $notes,
                'user_id' => $userId,
                'supplier_id' => $supplierId
            ]);

            // 4. Update specific PO if linked
            if ($referenceId > 0) {
                $po = $this->poModel->find($referenceId);
                if ($po && $po['supplier_id'] == $supplierId) {
                    $newPaidAmount = (float)($po['paid_amount'] ?? 0) + $amount;
                    $poTotal = (float)$po['total_bayar'];
                    
                    // Determine payment status
                    $newStatus = 'UNPAID';
                    if ($newPaidAmount >= $poTotal) {
                        $newStatus = 'PAID';
                    } elseif ($newPaidAmount > 0 && $newPaidAmount < $poTotal) {
                        $newStatus = 'PARTIAL';
                    }
                    
                    $this->poModel->update($referenceId, [
                        'paid_amount' => $newPaidAmount,
                        'payment_status' => $newStatus
                    ]);
                }
            }

            // 5. Recalculate supplier debt balance using BalanceService
            $this->balanceService->calculateSupplierDebt($supplierId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('finance/payments/payable')
                ->with('success', "Pembayaran utang berhasil dicatat! No. Bukti: $paymentNumber");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * AJAX: Get outstanding invoices for a customer
     * Used to populate invoice selection in payment form
     */
    public function getCustomerInvoices()
    {
        $customerId = $this->request->getGet('customer_id');
        
        if (!$customerId) {
            return $this->respondEmpty();
        }

        $invoices = $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.total_amount, sales.paid_amount, sales.created_at')
            ->where('sales.customer_id', $customerId)
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.deleted_at', null)
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();

        $result = array_map(function($invoice) {
            return [
                'id' => $invoice['id'],
                'invoice_number' => $invoice['invoice_number'],
                'total_amount' => (float)$invoice['total_amount'],
                'paid_amount' => (float)$invoice['paid_amount'],
                'outstanding' => (float)$invoice['total_amount'] - (float)$invoice['paid_amount'],
                'created_at' => $invoice['created_at']
            ];
        }, $invoices);

        return $this->respondData($result);
    }

    /**
     * AJAX: Get outstanding purchase orders for a supplier
     * Used to populate PO selection in payment form
     * Renamed from getSupplierPOs to match route definition
     */
    public function getSupplierPurchases()
    {
        $supplierId = $this->request->getGet('supplier_id');
        
        if (!$supplierId) {
            return $this->respondEmpty();
        }

        $pos = $this->poModel
            ->select('purchase_orders.id_po, purchase_orders.id, purchase_orders.nomor_po, purchase_orders.total_bayar, purchase_orders.paid_amount, purchase_orders.tanggal_po')
            ->where('purchase_orders.supplier_id', $supplierId)
            ->whereIn('purchase_orders.status', ['Menunggu', 'Sebagian Diterima', 'Diterima'])
            ->where('purchase_orders.deleted_at', null)
            ->orderBy('purchase_orders.tanggal_po', 'DESC')
            ->findAll();

        $result = array_map(function($po) {
            return [
                'id' => $po['id'],
                'nomor_po' => $po['nomor_po'],
                'total_bayar' => (float)$po['total_bayar'],
                'paid_amount' => (float)($po['paid_amount'] ?? 0),
                'outstanding' => (float)$po['total_bayar'] - (float)($po['paid_amount'] ?? 0),
                'tanggal_po' => $po['tanggal_po']
            ];
        }, $pos);

        return $this->respondData($result);
    }

    /**
     * AJAX: Get Kontra Bon list for a customer
     * Used to populate Kontra Bon selection in payment form
     */
    public function getKontraBons()
    {
        $customerId = $this->request->getGet('customer_id');
        
        if (!$customerId) {
            return $this->respondEmpty();
        }
        
        $kontraBons = $this->kontraBonModel
            ->where('customer_id', $customerId)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        $result = array_map(function($kb) {
            return [
                'id' => $kb['id'],
                'nomor_kontra_bon' => $kb['nomor_kontra_bon'],
                'tanggal' => $kb['tanggal'],
                'total_amount' => (float)$kb['total_amount'],
                'status' => $kb['status']
            ];
        }, $kontraBons);
        
        return $this->respondData($result);
    }
}