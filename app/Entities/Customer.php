<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Customer extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'credit_limit' => 'float',
        'receivable_balance' => 'float',
    ];
}