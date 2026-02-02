<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\KontraBonModel;
use App\Models\CustomerModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class KontraBon extends BaseController
{
    protected $kontraBonModel;
    protected $customerModel;

    public function __construct()
    {
        $this->kontraBonModel = new KontraBonModel();
        $this->customerModel = new CustomerModel();
    }

    /**
     * Display list of all Kontra Bons
     */
    public function index()
    {
        $kontraBons = $this->kontraBonModel->getAllWithCustomer();
        $stats = $this->kontraBonModel->getStatistics();

        $data = [
            'title' => 'Kontra Bon',
            'subtitle' => 'Kelola data kontra bon pelanggan',
            'kontraBons' => $kontraBons,
            'stats' => $stats,
        ];

        return view('finance/kontra-bon/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $customers = $this->customerModel->asArray()->findAll();

        $data = [
            'title' => 'Tambah Kontra Bon',
            'subtitle' => 'Buat kontra bon baru',
            'customers' => $customers,
        ];

        return view('finance/kontra-bon/create', $data);
    }

    /**
     * Store new kontra bon
     */
    public function store()
    {
        // Validation
        $rules = [
            'customer_id' => 'required|numeric',
            'due_date' => 'permit_empty|valid_date',
            'total_amount' => 'required|decimal',
            'status' => 'required|in_list[PENDING,PAID,CANCELLED]',
            'notes' => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Generate document number
            $documentNumber = $this->kontraBonModel->generateDocumentNumber();

            $data = [
                'document_number' => $documentNumber,
                'customer_id' => $this->request->getPost('customer_id'),
                'due_date' => $this->request->getPost('due_date') ?: null,
                'total_amount' => $this->request->getPost('total_amount'),
                'status' => $this->request->getPost('status'),
                'notes' => $this->request->getPost('notes') ?: null,
            ];

            $this->kontraBonModel->insert($data);

            return redirect()
                ->to('finance/kontra-bon')
                ->with('success', 'Kontra Bon berhasil ditambahkan dengan nomor: ' . $documentNumber);
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $kontraBon = $this->kontraBonModel->find($id);

        if (!$kontraBon) {
            return redirect()->back()->with('error', 'Kontra Bon tidak ditemukan');
        }

        $customers = $this->customerModel->asArray()->findAll();

        $data = [
            'title' => 'Edit Kontra Bon',
            'subtitle' => 'Ubah data kontra bon',
            'kontraBon' => $kontraBon,
            'customers' => $customers,
        ];

        return view('finance/kontra-bon/edit', $data);
    }

    /**
     * Update kontra bon
     */
    public function update($id)
    {
        // Check if exists
        $kontraBon = $this->kontraBonModel->find($id);
        if (!$kontraBon) {
            return redirect()->back()->with('error', 'Kontra Bon tidak ditemukan');
        }

        // Validation
        $rules = [
            'customer_id' => 'required|numeric',
            'due_date' => 'permit_empty|valid_date',
            'total_amount' => 'required|decimal',
            'status' => 'required|in_list[PENDING,PAID,CANCELLED]',
            'notes' => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'customer_id' => $this->request->getPost('customer_id'),
                'due_date' => $this->request->getPost('due_date') ?: null,
                'total_amount' => $this->request->getPost('total_amount'),
                'status' => $this->request->getPost('status'),
                'notes' => $this->request->getPost('notes') ?: null,
            ];

            $this->kontraBonModel->update($id, $data);

            return redirect()
                ->to('finance/kontra-bon')
                ->with('success', 'Kontra Bon berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete kontra bon
     */
    public function delete($id)
    {
        try {
            $kontraBon = $this->kontraBonModel->find($id);

            if (!$kontraBon) {
                return redirect()->back()->with('error', 'Kontra Bon tidak ditemukan');
            }

            // Check if can be deleted (not PAID)
            if ($kontraBon['status'] === 'PAID') {
                return redirect()->back()->with('error', 'Kontra Bon yang sudah PAID tidak dapat dihapus');
            }

            $this->kontraBonModel->delete($id);

            return redirect()
                ->to('finance/kontra-bon')
                ->with('success', 'Kontra Bon berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status
     */
    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');

        if (!in_array($status, ['PENDING', 'PAID', 'CANCELLED'])) {
            return redirect()->back()->with('error', 'Status tidak valid');
        }

        try {
            $this->kontraBonModel->updateStatus($id, $status);

            return redirect()
                ->to('finance/kontra-bon')
                ->with('success', 'Status berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf($id)
    {
        $kontraBon = $this->kontraBonModel->getById($id);

        if (!$kontraBon) {
            return redirect()->back()->with('error', 'Kontra Bon tidak ditemukan');
        }

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Generate HTML
        $html = view('finance/kontra-bon/pdf', ['kontraBon' => $kontraBon]);

        // Load HTML to dompdf
        $dompdf->loadHtml($html);

        // Setup paper
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output PDF
        $filename = 'Kontra-Bon-' . $kontraBon['document_number'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * View detail
     */
    public function detail($id)
    {
        $kontraBon = $this->kontraBonModel->getById($id);

        if (!$kontraBon) {
            return redirect()->back()->with('error', 'Kontra Bon tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Kontra Bon',
            'subtitle' => 'Detail kontra bon #' . $kontraBon['document_number'],
            'kontraBon' => $kontraBon,
        ];

        return view('finance/kontra-bon/detail', $data);
    }
}
