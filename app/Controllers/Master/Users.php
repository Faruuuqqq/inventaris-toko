<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\UserModel;
use CodeIgniter\Model;

class Users extends BaseCRUDController
{
    protected string $viewPath = 'master/users';
    protected string $routePath = '/master/users';
    protected string $entityName = 'Pengguna';
    protected string $entityNamePlural = 'Users';

    protected function getModel(): Model
    {
        return new UserModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'fullname' => 'required',
            'role' => 'required|in_list[OWNER,ADMIN,GUDANG,SALES]',
        ];
    }

    protected function getUpdateValidationRules(int|string $id): array
    {
        return [
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'fullname' => 'required',
            'role' => 'required|in_list[OWNER,ADMIN,GUDANG,SALES]',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'fullname' => $this->request->getPost('fullname'),
            'role' => $this->request->getPost('role'),
        ];
    }

    protected function getIndexData(): array
    {
        return $this->model->asArray()->orderBy('created_at', 'DESC')->findAll();
    }

    protected function getAdditionalViewData(): array
    {
        return [
            'subtitle' => 'Kelola pengguna sistem (Owner only)',
        ];
    }

    // Access control - only OWNER can manage users
    protected function checkStoreAccess(): bool
    {
        return session()->get('role') === 'OWNER';
    }

    protected function checkUpdateAccess($id): bool
    {
        return session()->get('role') === 'OWNER';
    }

    protected function checkDeleteAccess($id): bool
    {
        // Owner only and cannot delete yourself
        if (session()->get('role') !== 'OWNER') {
            return false;
        }
        if ($id == session()->get('user_id')) {
            return false;
        }
        return true;
    }

    // Hash password before store
    protected function beforeStore(array $data): array
    {
        $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        return $data;
    }

    // Only update password if provided
    protected function beforeUpdate($id, array $data): array
    {
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        return $data;
    }
}
