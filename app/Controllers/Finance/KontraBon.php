<?php
namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\KontraBonModel;
use App\Models\SaleModel;
use App\Models\CustomerModel;

class KontraBon extends BaseController
{
    protected $kontraBonModel;
    protected $saleModel;
    protected $customerModel;

    public function __construct()
    {
        $this->kontraBonModel = new KontraBonModel();
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
    }

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

        return view('layout/main', $data)->renderSection('content', view('finance/kontra-bon/index', $data));
    }

    public function create()
    {
        $customerId = $this->request->getPost('customer_id');
        $selectedSales = $this->request->getPost('sale_ids');
        $notes = $this->request->getPost('notes');
        $dueDate = $this->request->getPost('due_date');

        if (empty($selectedSales)) {
            return redirect()->back()->with('error', 'Pilih minimal satu invoice');
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Calculate total
            $totalAmount = 0;
            $sales = [];

            foreach ($selectedSales as $saleId) {
                $sale = $this->saleModel->find($saleId);

                // Verify customer matches
                if ($sale['customer_id'] != $customerId) {
                    throw new \Exception('Invoice tidak sesuai dengan customer');
                }

                // Verify not already in Kontra Bon
                if ($sale['kontra_bon_id']) {
                    throw new \Exception('Invoice sudah masuk Kontra Bon lain');
                }

                // Verify unpaid or partial
                if ($sale['payment_status'] == 'PAID') {
                    throw new \Exception('Invoice sudah lunas');
                }

                $totalAmount += ($sale['total_amount'] - $sale['paid_amount']);
                $sales[] = $sale;
            }

            // Create Kontra Bon
            $customer = $this->customerModel->find($customerId);
            $documentNumber = 'KB-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $kontraBonId = $this->kontraBonModel->insert([
                'document_number' => $documentNumber,
                'customer_id' => $customerId,
                'created_at' => date('Y-m-d'),
                'due_date' => $dueDate ?: date('Y-m-d', strtotime('+30 days')),
                'total_amount' => $totalAmount,
                'status' => 'UNPAID',
                'notes' => $notes,
            ]);

            // Link sales to Kontra Bon
            foreach ($sales as $sale) {
                $this->saleModel->update($sale['id'], ['kontra_bon_id' => $kontraBonId]);
            }

            $db->transComplete();

            return redirect()->to('/finance/kontra-bon')
                ->with('success', "Kontra Bon {$documentNumber} berhasil dibuat");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getUnpaidInvoices()
    {
        // For AJAX calls
        $customerId = $this->request->getGet('customer_id');
        
        if (!$customerId) {
            return $this->response->setJSON([]);
        }

        $invoices = $this->saleModel
            ->select('sales.id, sales.invoice_number, sales.total_amount, sales.paid_amount, sales.created_at')
            ->where('sales.customer_id', $customerId)
            ->where('sales.payment_status !=', 'PAID')
            ->where('sales.kontra_bon_id IS NULL')
            ->orderBy('sales.created_at', 'ASC')
            ->findAll();

        return $this->response->setJSON($invoices);
    }

    public function makePayment()
    {
        $kontraBonId = $this->request->getPost('kontra_bon_id');
        $amount = $this->request->getPost('amount');
        $paymentMethod = $this->request->getPost('payment_method');
        $notes = $this->request->getPost('notes');

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $kontraBon = $this->kontraBonModel->find($kontraBonId);
            if (!$kontraBon) {
                throw new \Exception('Kontra Bon tidak ditemukan');
            }

            // Update Kontra Bon status
            $newStatus = 'UNPAID';
            if (($kontraBon['total_amount'] - $amount) <= 0) {
                $newStatus = 'PAID';
            } else {
                $newStatus = 'PARTIAL';
            }

            $this->kontraBonModel->update($kontraBonId, ['status' => $newStatus]);

            // Create payment record
            $paymentModel = new \App\Models\PaymentModel();
            $paymentModel->insert([
                'payment_date' => date('Y-m-d H:i:s'),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'type' => 'RECEIVABLE',
                'reference_id' => $kontraBonId,
                'notes' => $notes,
            ]);

            // Update sales payment status
            if ($newStatus == 'PAID') {
                // Mark all linked sales as paid
                $this->saleModel
                    ->where('kontra_bon_id', $kontraBonId)
                    ->set(['payment_status' => 'PAID'])
                    ->update();
            }

            $db->transComplete();

            return redirect()->to('/finance/kontra-bon')
                ->with('success', "Pembayaran berhasil");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}