<?php
namespace App\Models;

use App\Entities\Salesperson;
use CodeIgniter\Model;

class SalespersonModel extends Model
{
    protected $table = 'salespersons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Salesperson::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'phone', 'is_active'];
    protected $useTimestamps = false;
}