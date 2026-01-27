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
        'code', 'name', 'phone', 'address', 'credit_limit', 'receivable_balance'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

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
}