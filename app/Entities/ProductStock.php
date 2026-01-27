<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ProductStock extends Entity
{
    protected $casts = [
        'quantity' => 'integer',
    ];
}