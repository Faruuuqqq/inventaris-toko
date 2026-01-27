<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class KontraBon extends Entity
{
    protected $dates = ['created_at', 'due_date'];
    protected $casts = [
        'total_amount' => 'float',
    ];
}