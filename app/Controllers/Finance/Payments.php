<?php
namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\SaleModel;
use App\Models\KontraBonModel;
use App\Models\CustomerModel;
use App\Models\SupplierModel;

class Payments extends BaseController
{
    protected $paymentModel;
    protected $saleModel;
    protected $kontraBonModel;
    protected $customerModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->saleModel = new SaleModel();
        $this->kontraBonModel = new KontraBonModel();
        $this->customerModel = new CustomerModel();
        $this->supplierModel = new SupplierModel();
    }

    public function receivable()
    {
        $customers = $this->customerModel
            ->where('receivable_balance >', 0)
            ->findAll();

        $data = [
            'title' => 'Pembayaran Piutang',
            'subtitle' => 'Catat pembayaran piutang customer',
            'customers' => $customers,
        ];

        return view('layout/main', $data)->renderSection('content', view('finance/payments/receivable', $data));
    }

    public function storeReceivable()
    {
        $customerId = $this->request->getPost('customer_id');
        $amount = $this->request->getPost('amount');
        $paymentMethod = $this->request->getPost('payment_method');
        $notes = $this->request->getPost('notes');
        $referenceType = $this->request->getPost('reference_type'); // 'sale' or 'kontra_bon'
        $referenceId = $this->request->getPost('reference_id');

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $customer = $this->customerModel->find($customerId);
            if (!$customer) {
                throw new \Exception('Customer tidak ditemukan');
            }

            // Create payment record
            $paymentId = $this->paymentModel->insert([
                'payment_date' => date('Y-m-d H:i:s'),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'type' => 'RECEIVABLE',
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);

            // Update customer receivable balance
            $this->customerModel->updateReceivableBalance($customerId, -$amount);

            // If paying specific sale, update sale payment status
            if ($referenceType === 'sale' && $referenceId) {
                $sale = $this->saleModel->find($referenceId);
                if ($sale) {
                    $newPaidAmount = $sale['paid_amount'] + $amount;
                    $newStatus = 'UNPAID';
                    
                    if ($newPaidAmount >= $sale['total_amount']) {
                        $newStatus = 'PAID';
                    } elseif ($newPaidAmount > 0 && $newPaidAmount < $sale['total_amount']) {
                        $newStatus = 'PARTIAL';
                    }
                    
                    $this->saleModel->update($referenceId, [
                        'paid_amount' => $newPaidAmount,
                        'payment_status' => $newStatus
                    ]);
                }
            }

            $db->transComplete();

            return redirect()->to('/finance/payments/receivable')
                ->with('success', 'Pembayaran piutang berhasil dicatat');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function payable()
    {
        $suppliers = $this->supplierModel
            ->where('debt_balance >', 0)
            ->findAll();

        $data = [
            'title' => 'Pembayaran Utang',
            'subtitle' => 'Catat pembayaran utang ke supplier',
            'suppliers' => $suppliers,
        ];

        return view('layout/main', $data)->renderSection('content', view('finance/payments/payable', $data));
    }

    public function storePayable()
    {
        $supplierId = $this->request->getPost('supplier_id');
        $amount = $this->request->getPost('amount');
        $paymentMethod = $this->request->getPost('payment_method');
        $notes = $this->request->getPost('notes');

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $supplier = $this->supplierModel->find($supplierId);
            if (!$supplier) {
                throw new \Exception('Supplier tidak ditemukan');
            }

            // Create payment record
            $this->paymentModel->insert([
                'payment_date' => date('Y-m-d H:i:s'),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'type' => 'PAYABLE',
                'notes' => $notes,
            ]);

            // Update supplier debt balance
            $newDebtBalance = $supplier['debt_balance'] - $amount;
            $this->supplierModel->update($supplierId, ['debt_balance' => $newDebtBalance]);

            $db->transComplete();

            return redirect()->to('/finance/payments/payable')
                ->with('success', 'Pembayaran utang berhasil dicatat');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getCustomerInvoices()
    {
        // For AJAX calls
        $customerId = $this->request->getGet('customer_id');
        
        if (!$customerId) {
            return $this->response->setJSON([]);
        }

        $invoices = $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.total_amount, sales.paid_amount, sales.created_at, sales.kontra_bon_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.payment_status !=', 'PAID')
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON($invoices);
    }
}