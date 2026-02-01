<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SalesReturn extends Entity
{
    protected $dates = ['created_at', 'date'];
    protected $casts = [
        'id' => 'integer',
        'sale_id' => 'integer',
        'customer_id' => 'integer',
        'final_amount' => 'float',
    ];
}
