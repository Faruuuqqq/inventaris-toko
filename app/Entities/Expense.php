<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Expense extends Entity
{
    protected $dates = ['expense_date', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'amount' => 'float',
        'user_id' => 'integer',
    ];
}
