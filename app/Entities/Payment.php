<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Payment extends Entity
{
    protected $dates = ['payment_date'];
    protected $casts = [
        'amount' => 'float',
    ];
}