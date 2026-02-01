<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PurchaseOrder extends Entity
{
    protected $dates = ['created_at', 'date'];
    protected $casts = [
        'id' => 'integer',
        'supplier_id' => 'integer',
        'warehouse_id' => 'integer',
        'total_amount' => 'float',
        'paid_amount' => 'float',
    ];
}
