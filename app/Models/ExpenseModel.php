<?php

namespace App\Models;

use App\Entities\Expense;
use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Expense::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'expense_number',
        'expense_date',
        'category',
        'description',
        'amount',
        'payment_method',
        'notes',
        'user_id',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'expense_date' => 'required|valid_date',
        'category' => 'required|max_length[100]',
        'description' => 'required|max_length[255]',
        'amount' => 'required|numeric|greater_than[0]',
        'payment_method' => 'required|in_list[CASH,TRANSFER,CHECK]',
    ];

    protected $validationMessages = [
        'expense_date' => [
            'required' => 'Tanggal biaya harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'category' => [
            'required' => 'Kategori biaya harus diisi',
            'max_length' => 'Kategori maksimal 100 karakter'
        ],
        'description' => [
            'required' => 'Deskripsi biaya harus diisi',
            'max_length' => 'Deskripsi maksimal 255 karakter'
        ],
        'amount' => [
            'required' => 'Jumlah biaya harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ],
        'payment_method' => [
            'required' => 'Metode pembayaran harus dipilih',
            'in_list' => 'Metode pembayaran tidak valid'
        ]
    ];

    /**
     * Generate unique expense number
     */
    public function generateExpenseNumber(): string
    {
        $date = date('Ymd');
        $count = $this->where('DATE(expense_date)', date('Y-m-d'))->countAllResults();
        return 'EXP-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get expenses with filters
     */
    public function getExpenses($category = null, $startDate = null, $endDate = null, $paymentMethod = null)
    {
        $builder = $this->select('expenses.*, users.name as user_name')
            ->join('users', 'users.id = expenses.user_id', 'left');

        if ($category) {
            $builder->where('expenses.category', $category);
        }

        if ($startDate) {
            $builder->where('expenses.expense_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('expenses.expense_date <=', $endDate);
        }

        if ($paymentMethod) {
            $builder->where('expenses.payment_method', $paymentMethod);
        }

        return $builder->orderBy('expenses.expense_date', 'DESC')->findAll();
    }

    /**
     * Get expense categories for dropdown
     */
    public function getCategories(): array
    {
        return [
            'OPERASIONAL' => 'Operasional',
            'TRANSPORTASI' => 'Transportasi',
            'LISTRIK' => 'Listrik & Air',
            'TELEPON' => 'Telepon & Internet',
            'GAJI' => 'Gaji Karyawan',
            'SEWA' => 'Sewa',
            'PERBAIKAN' => 'Perbaikan & Maintenance',
            'ATK' => 'Alat Tulis Kantor',
            'LAINNYA' => 'Lainnya',
        ];
    }

    /**
     * Get total expenses for a period
     */
    public function getTotalExpenses($startDate = null, $endDate = null): float
    {
        $builder = $this->selectSum('amount', 'total');

        if ($startDate) {
            $builder->where('expense_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('expense_date <=', $endDate);
        }

        $result = $builder->first();
        return $result ? (float)($result->total ?? 0) : 0;
    }

    /**
     * Get expenses grouped by category for reporting
     */
    public function getExpensesByCategory($startDate = null, $endDate = null): array
    {
        $builder = $this->select('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category');

        if ($startDate) {
            $builder->where('expense_date >=', $startDate);
        }

        if ($endDate) {
            $builder->where('expense_date <=', $endDate);
        }

        return $builder->findAll();
    }
}
