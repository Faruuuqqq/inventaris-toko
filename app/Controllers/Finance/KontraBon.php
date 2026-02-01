<?php
namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\KontraBonModel;
use App\Models\SaleModel;
use App\Models\CustomerModel;
use App\Models\PaymentModel;
use App\Services\BalanceService;
use CodeIgniter\API\ResponseTrait;

class KontraBon extends BaseController
{
    use ResponseTrait;

    protected $kontraBonModel;
    protected $saleModel;
    protected $customerModel;
    protected $paymentModel;
    protected $balanceService;

    public function __construct()
    {
        $this->kontraBonModel = new KontraBonModel();
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
        $this->paymentModel = new PaymentModel();
        $this->balanceService = new BalanceService();
    }

    /**
     * View: List all Kontra Bons
     */
    public function index()
    {
        $kontraBons = $this->kontraBonModel
            ->select('kontra_bons.*, customers.name as customer_name')
            ->join('customers', 'customers.id = kontra_bons.customer_id')
            ->orderBy('kontra_bons.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Kontra Bon',
            'subtitle' => 'Konsolidasi invoice B2B',
            'kontraBons' => $kontraBons,
        ];

        return view('layout/main', $data)
            . view('finance/kontra-bon/index', $data);
    }

    /**
     * Action: Create Kontra Bon (Consolidation)
     * 
     * Groups multiple unpaid invoices into a single settlement document.
     * Consolidates receivables from multiple sales into one.
     */
    public function create()
    {
        // 1. Validation
        if (!$this->validate([
            'customer_id' => 'required|numeric',
            'sale_ids' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $customerId = (int)$this->request->getPost('customer_id');
        $selectedSales = $this->request->getPost('sale_ids');
        $notes = $this->request->getPost('notes') ?? '';
        $userId = session()->get('id');

        if (empty($selectedSales) || !is_array($selectedSales)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu invoice');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 2. Validate customer exists
            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new \Exception('Customer tidak ditemukan');
            }

            // 3. Validate and calculate total from selected sales
            $totalAmount = 0;
            $sales = [];

            foreach ($selectedSales as $saleId) {
                $sale = $this->saleModel->find((int)$saleId);

                if (!$sale) {
                    throw new \Exception("Invoice #{$saleId} tidak ditemukan");
                }

                // Verify customer matches
                if ((int)$sale['customer_id'] != $customerId) {
                    throw new \Exception('Invoice tidak sesuai dengan customer');
                }

                // Verify not already in Kontra Bon
                if ($sale['kontra_bon_id']) {
                    throw new \Exception("Invoice {$sale['invoice_number']} sudah masuk Kontra Bon lain");
                }

                // Verify unpaid or partial (not PAID)
                if ($sale['payment_status'] == 'PAID') {
                    throw new \Exception("Invoice {$sale['invoice_number']} sudah lunas");
                }

                // Only credit sales can be consolidated
                if ($sale['payment_type'] != 'CREDIT') {
                    throw new \Exception("Invoice {$sale['invoice_number']} bukan penjualan kredit");
                }

                // Calculate outstanding amount
                $outstanding = (float)$sale['total_amount'] - (float)$sale['paid_amount'];
                $totalAmount += $outstanding;
                $sales[] = $sale;
            }

            if (empty($sales)) {
                throw new \Exception('Tidak ada invoice yang valid untuk dikonsolidasi');
            }

            // 4. Create Kontra Bon record
            $documentNumber = 'KB-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $kontraBonId = $this->kontraBonModel->insert([
                'number' => $documentNumber,
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'DRAFT',
                'notes' => $notes,
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$kontraBonId) {
                throw new \Exception('Gagal membuat Kontra Bon');
            }

            // 5. Link sales to Kontra Bon
            foreach ($sales as $sale) {
                $this->saleModel->update($sale['id'], ['kontra_bon_id' => $kontraBonId]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('finance/kontra-bon')
                ->with('success', "Kontra Bon {$documentNumber} berhasil dibuat dengan " . count($sales) . " invoice");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * AJAX: Get unpaid invoices for consolidation
     */
    public function getUnpaidInvoices()
    {
        $customerId = $this->request->getGet('customer_id');
        
        if (!$customerId) {
            return $this->response->setJSON([]);
        }

        $invoices = $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.total_amount, sales.paid_amount, sales.created_at')
            ->where('sales.customer_id', $customerId)
            ->whereIn('sales.payment_status', ['UNPAID', 'PARTIAL'])
            ->where('sales.payment_type', 'CREDIT')
            ->where('sales.kontra_bon_id', null)
            ->where('sales.deleted_at', null)
            ->orderBy('sales.created_at', 'ASC')
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

        return $this->response->setJSON($result);
    }

    /**
     * Action: Record Payment for Kontra Bon
     * 
     * Processes payment against consolidated kontra bon.
     * Updates kontra bon status and linked sales payment status.
     * Recalculates customer balance using BalanceService.
     */
    public function makePayment()
    {
        // 1. Validation
        if (!$this->validate([
            'kontra_bon_id' => 'required|numeric',
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
            $kontraBonId = (int)$this->request->getPost('kontra_bon_id');
            $amount = (float)$this->request->getPost('amount');
            $paymentMethod = $this->request->getPost('payment_method');
            $paymentDate = $this->request->getPost('payment_date');
            $notes = $this->request->getPost('notes') ?? '';
            $userId = session()->get('id');

            $kontraBon = $this->kontraBonModel->find($kontraBonId);
            if (!$kontraBon) {
                throw new \Exception('Kontra Bon tidak ditemukan');
            }

            $customerId = (int)$kontraBon['customer_id'];

            // Validate payment amount
            $outstanding = (float)$kontraBon['total_amount'] - (float)($kontraBon['paid_amount'] ?? 0);
            if ($amount > $outstanding) {
                throw new \Exception(
                    'Jumlah pembayaran melebihi saldo Kontra Bon. ' .
                    'Tersisa: ' . number_format($outstanding, 0) .
                    ', Pembayaran: ' . number_format($amount, 0)
                );
            }

            // 3. Update Kontra Bon payment
            $newPaidAmount = (float)($kontraBon['paid_amount'] ?? 0) + $amount;
            $newStatus = 'DRAFT';
            
            if ($newPaidAmount >= (float)$kontraBon['total_amount']) {
                $newStatus = 'PAID';
            } elseif ($newPaidAmount > 0) {
                $newStatus = 'PARTIAL';
            }

            $this->kontraBonModel->update($kontraBonId, [
                'status' => $newStatus,
                'paid_amount' => $newPaidAmount
            ]);

            // 4. Create payment record
            $paymentNumber = $this->paymentModel->generatePaymentNumber();
            $this->paymentModel->insert([
                'payment_number' => $paymentNumber,
                'payment_date' => $paymentDate,
                'type' => 'RECEIVABLE',
                'reference_id' => $kontraBonId,
                'amount' => $amount,
                'method' => $paymentMethod,
                'notes' => $notes ?? '',
                'user_id' => $userId,
                'customer_id' => $customerId
            ]);

            // 5. Update linked sales payment status if Kontra Bon fully paid
            if ($newStatus == 'PAID') {
                $this->saleModel
                    ->where('kontra_bon_id', $kontraBonId)
                    ->set(['payment_status' => 'PAID'])
                    ->update();
            }

            // 6. Recalculate customer balance using BalanceService
            $this->balanceService->calculateCustomerReceivable($customerId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('finance/kontra-bon')
                ->with('success', "Pembayaran Kontra Bon berhasil dicatat! No. Bukti: $paymentNumber");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Gagal: " . $e->getMessage());
        }
    }
}