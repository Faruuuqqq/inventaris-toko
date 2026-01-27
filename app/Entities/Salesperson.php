<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Salesperson extends Entity
{
    protected $casts = [
        'is_active' => 'boolean',
    ];
}