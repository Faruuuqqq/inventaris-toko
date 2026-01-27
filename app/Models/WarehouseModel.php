<?php
namespace App\Models;

use App\Entities\Warehouse;
use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Warehouse::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['code', 'name', 'address', 'is_active'];
    protected $useTimestamps = false;
}