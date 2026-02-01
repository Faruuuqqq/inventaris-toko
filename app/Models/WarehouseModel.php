<?php

namespace App\Models;

use App\Entities\Warehouse;
use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Warehouse::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['code', 'name', 'address', 'is_active'];
    protected $useTimestamps = false;

    // Validation Rules
    protected $validationRules = [
        'code' => 'required|min_length[2]|max_length[20]|is_unique[warehouses.code,id,{id}]',
        'name' => 'required|min_length[2]|max_length[100]',
        'address' => 'permit_empty|max_length[300]',
    ];

    protected $validationMessages = [
        'code' => [
            'required' => 'Kode gudang harus diisi',
            'is_unique' => 'Kode gudang sudah digunakan',
            'min_length' => 'Kode gudang minimal 2 karakter',
        ],
        'name' => [
            'required' => 'Nama gudang harus diisi',
            'min_length' => 'Nama gudang minimal 2 karakter',
        ],
    ];
}
