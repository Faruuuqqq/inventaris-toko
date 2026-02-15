<?php

namespace App\Models;

use App\Entities\Salesperson;
use CodeIgniter\Model;

class SalespersonModel extends Model
{
    protected $table = 'salespersons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Salesperson::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'phone', 'email', 'address', 'is_active'];
     protected $useTimestamps = false;

     // Validation Rules
     protected $validationRules = [
         'name' => 'required|min_length[2]|max_length[100]',
         'phone' => 'permit_empty|max_length[20]',
         'email' => 'permit_empty|valid_email',
         'address' => 'permit_empty|max_length[500]',
     ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama sales harus diisi',
            'min_length' => 'Nama sales minimal 2 karakter',
        ],
    ];
}
