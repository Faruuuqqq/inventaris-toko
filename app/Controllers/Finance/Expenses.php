<?php

namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;

class Expenses extends BaseController
{
    protected $expenseModel;

    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
    }

    /**
     * Display expense list
     */
    public function index()
    {
        $data = [
            'title' => 'Biaya & Jasa',
            'subtitle' => 'Daftar biaya operasional toko',
            'categories' => $this->expenseModel->getCategories(),
        ];

        return view('finance/expenses/index', $data);
    }

    /**
     * Get expenses data for AJAX
     */
    public function getData()
    {
        $category = $this->request->getGet('category');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentMethod = $this->request->getGet('payment_method');

        $expenses = $this->expenseModel->getExpenses($category, $startDate, $endDate, $paymentMethod);

        // Calculate totals
        $total = 0;
        foreach ($expenses as $expense) {
            $total += $expense->amount ?? $expense['amount'] ?? 0;
        }

        return $this->response->setJSON([
            'data' => $expenses,
            'total' => $total,
            'count' => count($expenses)
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Biaya',
            'subtitle' => 'Input biaya baru',
            'categories' => $this->expenseModel->getCategories(),
            'expense_number' => $this->expenseModel->generateExpenseNumber(),
        ];

        return view('finance/expenses/create', $data);
    }

    /**
     * Store new expense
     */
    public function store()
    {
        $rules = [
            'expense_date' => 'required|valid_date',
            'category' => 'required',
            'description' => 'required|max_length[255]',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[CASH,TRANSFER,CHECK]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $expenseData = [
                'expense_number' => $this->expenseModel->generateExpenseNumber(),
                'expense_date' => $this->request->getPost('expense_date'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'amount' => $this->request->getPost('amount'),
                'payment_method' => $this->request->getPost('payment_method'),
                'notes' => $this->request->getPost('notes'),
                'user_id' => session()->get('user_id'),
            ];

            $this->expenseModel->insert($expenseData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data biaya');
            }

            return redirect()->to('/finance/expenses')->with('success', 'Biaya berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/finance/expenses')->with('error', 'Data biaya tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Biaya',
            'subtitle' => 'Ubah data biaya',
            'expense' => $expense,
            'categories' => $this->expenseModel->getCategories(),
        ];

        return view('finance/expenses/edit', $data);
    }

    /**
     * Update expense
     */
    public function update($id)
    {
        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return redirect()->to('/finance/expenses')->with('error', 'Data biaya tidak ditemukan');
        }

        $rules = [
            'expense_date' => 'required|valid_date',
            'category' => 'required',
            'description' => 'required|max_length[255]',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[CASH,TRANSFER,CHECK]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $expenseData = [
                'expense_date' => $this->request->getPost('expense_date'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'amount' => $this->request->getPost('amount'),
                'payment_method' => $this->request->getPost('payment_method'),
                'notes' => $this->request->getPost('notes'),
            ];

            $this->expenseModel->update($id, $expenseData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal mengupdate data biaya');
            }

            return redirect()->to('/finance/expenses')->with('success', 'Biaya berhasil diupdate');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete expense
     */
    public function delete($id)
    {
        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data biaya tidak ditemukan']);
        }

        try {
            $this->expenseModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Biaya berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get expense summary for reporting
     */
    public function summary()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Ringkasan Biaya',
            'subtitle' => 'Laporan biaya periode ' . format_date($startDate) . ' - ' . format_date($endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => $this->expenseModel->getTotalExpenses($startDate, $endDate),
            'byCategory' => $this->expenseModel->getExpensesByCategory($startDate, $endDate),
            'categories' => $this->expenseModel->getCategories(),
        ];

        return view('finance/expenses/summary', $data);
    }
}
