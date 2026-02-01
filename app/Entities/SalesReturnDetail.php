<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SalesReturnDetail extends Entity
{
    protected $casts = [
        'id' => 'integer',
        'sales_return_id' => 'integer',
        'sale_item_id' => 'integer',
        'product_id' => 'integer',
        'warehouse_id' => 'integer',
        'quantity' => 'integer',
        'approved_quantity' => 'integer',
    ];
}
