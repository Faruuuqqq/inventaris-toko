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

    /**
     * Create sale items
     */
    public function createSaleItems($saleId, $items)
    {
        foreach ($items as $item) {
            $price = $item['price'] ?? $item['unit_price'] ?? 0;
            $quantity = $item['quantity'];
            $subtotal = $item['subtotal'] ?? $item['total_price'] ?? ($price * $quantity);

            $data = [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal
            ];

            $this->insert($data);
        }
    }
}