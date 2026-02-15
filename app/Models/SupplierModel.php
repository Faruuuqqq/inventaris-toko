<?php
namespace App\Models;

use App\Entities\Supplier;
use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Supplier::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'code', 'name', 'phone', 'address', 'debt_balance'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'address' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama supplier harus diisi',
            'min_length' => 'Nama supplier minimal 2 karakter',
        ],
    ];

    /**
     * Update supplier debt balance
     */
    public function updateDebtBalance($supplierId, $amount)
    {
        $supplier = $this->find($supplierId);

        if (!$supplier) {
            throw new \Exception('Supplier not found');
        }

        $newBalance = ($supplier->debt_balance ?? 0) + $amount;

        if ($newBalance < 0) {
            throw new \Exception('Payment exceeds debt balance');
        }

        return $this->update($supplierId, ['debt_balance' => $newBalance]);
    }

    /**
     * Apply payment to reduce debt
     */
    public function applyPayment($supplierId, $amount)
    {
        return $this->updateDebtBalance($supplierId, -$amount);
    }
}