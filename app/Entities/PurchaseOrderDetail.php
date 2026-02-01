<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PurchaseOrderDetail extends Entity
{
    protected $casts = [
        'id' => 'integer',
        'purchase_order_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'float',
        'discount_percent' => 'float',
        'total_price' => 'float',
    ];
}
