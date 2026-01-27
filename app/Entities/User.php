<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}