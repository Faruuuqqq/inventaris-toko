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
        'code', 'name', 'phone', 'debt_balance'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
}