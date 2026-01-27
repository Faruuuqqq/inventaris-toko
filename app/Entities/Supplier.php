<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Supplier extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'debt_balance' => 'float',
    ];
}