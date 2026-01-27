<?php
namespace App\Models;

use App\Entities\SaleItem;
use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SaleItem::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'sale_id', 'product_id', 'quantity', 'price', 'subtotal'
    ];
    protected $useTimestamps = false;

    public function getSaleItems($saleId)
    {
        return $this->where('sale_id', $saleId)->findAll();
    }
}