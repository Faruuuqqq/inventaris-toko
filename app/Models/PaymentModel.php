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
        'payment_date', 'amount', 'payment_method', 'type', 'reference_id', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'payment_date';
}