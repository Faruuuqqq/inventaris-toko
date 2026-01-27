<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SaleItem extends Entity
{
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'float',
        'subtotal' => 'float',
    ];
}