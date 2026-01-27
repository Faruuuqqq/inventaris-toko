<?php
namespace App\Models;

use App\Entities\StockMutation;
use CodeIgniter\Model;

class StockMutationModel extends Model
{
    protected $table = 'stock_mutations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = StockMutation::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_id', 'warehouse_id', 'type', 'quantity',
        'current_balance', 'reference_number', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
}