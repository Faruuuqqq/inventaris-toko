<?php
namespace App\Models;

use App\Entities\Customer;
use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Customer::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'code', 'name', 'phone', 'address', 'credit_limit',
        'receivable_balance'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[200]',
        'phone' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'address' => 'permit_empty|max_length[500]',
        'credit_limit' => 'required|numeric|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama customer harus diisi',
            'min_length' => 'Nama customer minimal 2 karakter',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
        ],
        'credit_limit' => [
            'required' => 'Limit kredit harus diisi',
            'numeric' => 'Limit kredit harus berupa angka',
            'greater_than_equal_to' => 'Limit kredit tidak boleh negatif',
        ],
    ];

    /**
     * Check if customer can make a credit purchase
     *
     * @param int $customerId
     * @param float $newAmount
     * @return bool
     */
    public function canMakeCreditPurchase($customerId, $newAmount)
    {
        $customer = $this->find($customerId);

        if (!$customer) {
            return false;
        }

        $totalAfterPurchase = $customer['receivable_balance'] + $newAmount;

        return $totalAfterPurchase <= $customer['credit_limit'];
    }

    /**
     * Update receivable balance
     *
     * @param int $customerId
     * @param float $amount Positive to add debt, Negative to reduce
     */
    public function updateReceivableBalance($customerId, $amount)
    {
        $customer = $this->find($customerId);

        if (!$customer) {
            throw new \Exception('Customer not found');
        }

        $newBalance = $customer['receivable_balance'] + $amount;

        if ($newBalance < 0) {
            throw new \Exception('Saldo piutang tidak boleh negatif');
        }

        return $this->update($customerId, ['receivable_balance' => $newBalance]);
    }
    
    /**
     * Get customer with receivable aging
     */
    public function getWithAging($customerId)
    {
        $customer = $this->find($customerId);
        
        if (!$customer) {
            return null;
        }
        
        // Get aging data from unpaid sales
        $saleModel = new SaleModel();
        $unpaidSales = $saleModel->getCustomerSales($customerId, 'UNPAID');
        
        $aging = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '>90' => 0
        ];
        
        $today = new \DateTime();
        
        foreach ($unpaidSales as $sale) {
            $saleDate = new \DateTime($sale->created_at ?? $sale['created_at']);
            $daysDiff = $today->diff($saleDate)->days;
            $unpaidAmount = ($sale->total_amount ?? $sale['total_amount']) - ($sale->paid_amount ?? $sale['paid_amount']);

            if ($daysDiff <= 30) {
                $aging['0-30'] += $unpaidAmount;
            } elseif ($daysDiff <= 60) {
                $aging['31-60'] += $unpaidAmount;
            } elseif ($daysDiff <= 90) {
                $aging['61-90'] += $unpaidAmount;
            } else {
                $aging['>90'] += $unpaidAmount;
            }
        }
        
        $customer['aging'] = $aging;
        
        return $customer;
    }
    
    /**
     * Update customer receivable from payment
     */
    public function applyPayment($customerId, $amount)
    {
        $customer = $this->find($customerId);
        
        if (!$customer) {
            throw new \Exception('Customer not found');
        }
        
        $newBalance = $customer['receivable_balance'] - $amount;
        
        if ($newBalance < 0) {
            throw new \Exception('Payment amount exceeds receivable balance');
        }
        
        return $this->update($customerId, ['receivable_balance' => $newBalance]);
    }
}