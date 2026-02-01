<?php
namespace App\Models;

use App\Entities\Payment;
use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Payment::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'payment_number', 'payment_date', 'type', 'reference_id',
        'amount', 'method', 'notes', 'user_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get receivable payments
     */
    public function getReceivablePayments($referenceId = null)
    {
        $builder = $this->where('type', 'RECEIVABLE');
        if ($referenceId) {
            $builder->where('reference_id', $referenceId);
        }
        return $builder->orderBy('payment_date', 'DESC')->findAll();
    }

    /**
     * Get payable payments
     */
    public function getPayablePayments($referenceId = null)
    {
        $builder = $this->where('type', 'PAYABLE');
        if ($referenceId) {
            $builder->where('reference_id', $referenceId);
        }
        return $builder->orderBy('payment_date', 'DESC')->findAll();
    }

    /**
     * Create payment record
     */
    public function createPayment($type, $refId, $amount, $method, $date, $notes = null, $userId = null)
    {
        $data = [
            'payment_number' => 'PAY-' . date('YmdHis') . '-' . rand(100, 999),
            'type' => $type,
            'reference_id' => $refId,
            'amount' => $amount,
            'method' => $method,
            'payment_date' => $date,
            'notes' => $notes,
            'user_id' => $userId ?? session()->get('user_id') ?? 1
        ];

        return $this->insert($data);
    }

    /**
     * Generate payment number
     */
    public function generatePaymentNumber(): string
    {
        $date = date('Ymd');
        $count = $this->where('DATE(payment_date)', date('Y-m-d'))->countAllResults();
        return 'PAY-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}