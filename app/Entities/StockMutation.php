<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class StockMutation extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'quantity' => 'integer',
        'current_balance' => 'integer',
    ];
}