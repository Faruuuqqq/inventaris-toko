<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = User::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'username', 'email', 'password_hash', 'fullname', 'role', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'fullname' => 'required|min_length[2]|max_length[100]',
        'role' => 'required|in_list[OWNER,ADMIN,GUDANG,SALES]',
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'alpha_numeric_punct' => 'Username hanya boleh berisi huruf, angka, dan tanda baca',
            'is_unique' => 'Username sudah digunakan',
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan',
        ],
        'fullname' => [
            'required' => 'Nama lengkap harus diisi',
            'min_length' => 'Nama lengkap minimal 2 karakter',
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role tidak valid',
        ],
    ];

    /**
     * Find user by username for authentication
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Find user by email for authentication
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
