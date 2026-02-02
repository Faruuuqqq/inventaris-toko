<?php
namespace App\Models;

use App\Entities\Category;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Category::class;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $allowedFields = ['name'];
    protected $useTimestamps = false;

    // Validation Rules
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Kategori harus diisi',
            'min_length' => 'Kategori minimal 2 karakter',
            'max_length' => 'Kategori maksimal 100 karakter',
            'is_unique' => 'Kategori sudah terdaftar',
        ],
    ];
}