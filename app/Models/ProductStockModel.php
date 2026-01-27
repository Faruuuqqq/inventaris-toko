<?php
namespace App\Models;

use App\Entities\ProductStock;
use CodeIgniter\Model;

class ProductStockModel extends Model
{
    protected $table = 'product_stocks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = ProductStock::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['product_id', 'warehouse_id', 'quantity'];
    protected $useTimestamps = false;
}