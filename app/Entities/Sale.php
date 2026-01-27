<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Sale extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'is_hidden' => 'boolean',
    ];
}