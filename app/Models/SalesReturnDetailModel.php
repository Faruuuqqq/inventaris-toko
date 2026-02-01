<?php
namespace App\Models;

use App\Entities\SalesReturnDetail;
use CodeIgniter\Model;

class SalesReturnDetailModel extends Model
{
    protected $table = 'sales_return_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SalesReturnDetail::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'return_id', 'product_id', 'quantity', 'price'
    ];
    protected $useTimestamps = false;

    /**
     * Create sales return items
     */
    public function createReturnItems($salesReturnId, $items)
    {
        foreach ($items as $item) {
            $data = [
                'return_id' => $salesReturnId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? 0
            ];

            $this->insert($data);
        }
    }

    /**
     * Get return items with product details
     */
    public function getReturnItems($returnId)
    {
        return $this->select('sales_return_items.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = sales_return_items.product_id')
            ->where('sales_return_items.return_id', $returnId)
            ->findAll();
    }
}