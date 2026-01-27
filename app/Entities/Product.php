<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Product extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'price_buy' => 'float',
        'price_sell' => 'float',
    ];
}