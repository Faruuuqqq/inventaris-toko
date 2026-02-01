<?php
namespace App\Models;

use App\Entities\PurchaseOrderDetail;
use CodeIgniter\Model;

class PurchaseOrderDetailModel extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = PurchaseOrderDetail::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'po_id', 'product_id', 'quantity', 'price', 'received_qty'
    ];
    protected $useTimestamps = false;

    /**
     * Create purchase order items
     */
    public function createPurchaseOrderItems($purchaseOrderId, $items)
    {
        foreach ($items as $item) {
            $data = [
                'po_id' => $purchaseOrderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? $item['unit_price'] ?? 0,
                'received_qty' => $item['received_qty'] ?? 0
            ];

            $this->insert($data);
        }
    }
}