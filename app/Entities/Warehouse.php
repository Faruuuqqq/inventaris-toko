<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Warehouse extends Entity
{
    protected $casts = [
        'is_active' => 'boolean',
    ];
}