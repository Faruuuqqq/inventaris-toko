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

     /**
      * Get expense analysis data (AJAX)
      */
     public function analyzeData()
     {
         $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
         $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
         $type = $this->request->getGet('type') ?? 'category'; // category, paymentMethod, monthly

         $db = \Config\Database::connect();

         if ($type === 'category') {
             // Expense breakdown by category
             $result = $db->table('expenses')
                 ->select('category, COUNT(*) as count, SUM(amount) as total')
                 ->where('expense_date >=', $startDate)
                 ->where('expense_date <=', $endDate)
                 ->groupBy('category')
                 ->orderBy('total', 'DESC')
                 ->get()
                 ->getResultArray();
         } elseif ($type === 'paymentMethod') {
             // Expense breakdown by payment method
             $result = $db->table('expenses')
                 ->select('payment_method, COUNT(*) as count, SUM(amount) as total')
                 ->where('expense_date >=', $startDate)
                 ->where('expense_date <=', $endDate)
                 ->groupBy('payment_method')
                 ->orderBy('total', 'DESC')
                 ->get()
                 ->getResultArray();
         } elseif ($type === 'monthly') {
             // Monthly expense trend
             $result = $db->table('expenses')
                 ->select('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
                 ->where('expense_date >=', $startDate)
                 ->where('expense_date <=', $endDate)
                 ->groupBy('month')
                 ->orderBy('month', 'ASC')
                 ->get()
                 ->getResultArray();
         } else {
             $result = [];
         }

         return $this->response->setJSON([
             'success' => true,
             'type' => $type,
             'data' => $result
         ]);
     }

     /**
      * Get expense summary statistics
      */
     public function summaryStats()
     {
         $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
         $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

         $db = \Config\Database::connect();

         // Total and average expenses
         $stats = $db->table('expenses')
             ->select('
                 COUNT(*) as total_transactions,
                 SUM(amount) as total_amount,
                 AVG(amount) as average_amount,
                 MAX(amount) as max_amount,
                 MIN(amount) as min_amount
             ')
             ->where('expense_date >=', $startDate)
             ->where('expense_date <=', $endDate)
             ->first();

         // Most frequent category
         $topCategory = $db->table('expenses')
             ->select('category, COUNT(*) as count, SUM(amount) as total')
             ->where('expense_date >=', $startDate)
             ->where('expense_date <=', $endDate)
             ->groupBy('category')
             ->orderBy('total', 'DESC')
             ->first();

         return $this->response->setJSON([
             'stats' => $stats,
             'top_category' => $topCategory
         ]);
     }

     /**
      * Compare expenses between periods
      */
     public function compareData()
     {
         $startDate1 = $this->request->getGet('start_date1');
         $endDate1 = $this->request->getGet('end_date1');
         $startDate2 = $this->request->getGet('start_date2');
         $endDate2 = $this->request->getGet('end_date2');

         if (!$startDate1 || !$endDate1 || !$startDate2 || !$endDate2) {
             return $this->response->setJSON(['error' => 'Missing date parameters']);
         }

         $db = \Config\Database::connect();

         // Period 1 by category
         $period1 = $db->table('expenses')
             ->select('category, SUM(amount) as total')
             ->where('expense_date >=', $startDate1)
             ->where('expense_date <=', $endDate1)
             ->groupBy('category')
             ->get()
             ->getResultArray();

         // Period 2 by category
         $period2 = $db->table('expenses')
             ->select('category, SUM(amount) as total')
             ->where('expense_date >=', $startDate2)
             ->where('expense_date <=', $endDate2)
             ->groupBy('category')
             ->get()
             ->getResultArray();

         // Build comparison
         $comparison = [];
         $allCategories = array_unique(
             array_merge(
                 array_column($period1, 'category'),
                 array_column($period2, 'category')
             )
         );

         foreach ($allCategories as $category) {
             $total1 = 0;
             $total2 = 0;

             foreach ($period1 as $row) {
                 if ($row['category'] === $category) {
                     $total1 = $row['total'];
                     break;
                 }
             }

             foreach ($period2 as $row) {
                 if ($row['category'] === $category) {
                     $total2 = $row['total'];
                     break;
                 }
             }

             $variance = $total2 - $total1;
             $percentChange = $total1 > 0 ? (($variance / $total1) * 100) : 0;

             $comparison[] = [
                 'category' => $category,
                 'period1' => $total1,
                 'period2' => $total2,
                 'variance' => $variance,
                 'percent_change' => round($percentChange, 2)
             ];
         }

         return $this->response->setJSON([
             'period1' => ['start' => $startDate1, 'end' => $endDate1],
             'period2' => ['start' => $startDate2, 'end' => $endDate2],
             'comparison' => $comparison
         ]);
     }

     /**
      * Export expenses to CSV
      */
     public function exportCSV()
     {
         $category = $this->request->getGet('category');
         $startDate = $this->request->getGet('start_date');
         $endDate = $this->request->getGet('end_date');
         $paymentMethod = $this->request->getGet('payment_method');

         $expenses = $this->expenseModel->getExpenses($category, $startDate, $endDate, $paymentMethod);

         // Prepare CSV
         $filename = 'expenses_' . date('Y-m-d_His') . '.csv';
         $csv = "Nomor Biaya,Tanggal,Kategori,Deskripsi,Jumlah,Metode Pembayaran,Catatan\n";

         foreach ($expenses as $expense) {
             $amount = is_object($expense) ? $expense->amount : $expense['amount'];
             $expenseNumber = is_object($expense) ? $expense->expense_number : $expense['expense_number'];
             $expenseDate = is_object($expense) ? $expense->expense_date : $expense['expense_date'];
             $expenseCategory = is_object($expense) ? $expense->category : $expense['category'];
             $description = is_object($expense) ? $expense->description : $expense['description'];
             $payMethod = is_object($expense) ? $expense->payment_method : $expense['payment_method'];
             $notes = is_object($expense) ? $expense->notes : $expense['notes'];

             $csv .= "\"{$expenseNumber}\",\"{$expenseDate}\",\"{$expenseCategory}\",\"{$description}\",\"{$amount}\",\"{$payMethod}\",\"{$notes}\"\n";
         }

         return $this->response
             ->setHeader('Content-Type', 'text/csv; charset=utf-8')
             ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
             ->setBody($csv);
     }

     /**
      * View budget tracking
      */
     public function budget()
     {
         $month = $this->request->getGet('month') ?? date('Y-m');

         $data = [
             'title' => 'Budget & Pengeluaran',
             'subtitle' => 'Perbandingan budget vs realisasi',
             'month' => $month,
             'categories' => $this->expenseModel->getCategories(),
             'monthName' => date('F Y', strtotime($month . '-01'))
         ];

         return view('finance/expenses/budget', $data);
     }

     /**
      * Get budget vs actual comparison
      */
     public function getBudgetData()
     {
         $month = $this->request->getGet('month') ?? date('Y-m');

         $db = \Config\Database::connect();
         $startDate = $month . '-01';
         $endDate = date('Y-m-t', strtotime($startDate));

         // Get actual expenses by category
         $actual = $db->table('expenses')
             ->select('category, SUM(amount) as total')
             ->where('expense_date >=', $startDate)
             ->where('expense_date <=', $endDate)
             ->groupBy('category')
             ->get()
             ->getResultArray();

         return $this->response->setJSON([
             'actual' => $actual,
             'month' => $month
         ]);
     }
}
