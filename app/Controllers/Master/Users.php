<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Helpers\PaginationHelper;
use App\Models\UserModel;

class Users extends BaseCRUDController
{
    protected string $viewPath = 'master/users';

    protected string $routePath = '/master/users';

    protected string $entityName = 'Pengguna';

    protected string $entityNamePlural = 'Users';

    protected function getModel(): UserModel
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

    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $params = PaginationHelper::getSafeParams($page, $perPage);
            $page = $params['page'];
            $perPage = $params['perPage'];

            $users = $this->model->asArray()->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return view($this->viewPath . '/index', [
                'title' => 'Daftar Pengguna',
                'users' => $users,
                'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Users index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pengguna');
        }
    }

    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        return view($this->viewPath . '/create', [
            'title' => 'Tambah Pengguna',
            'subtitle' => 'Tambahkan pengguna baru',
        ]);
    }

    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        return view($this->viewPath . '/edit', [
            'title' => 'Edit Pengguna',
            'subtitle' => 'Ubah data pengguna',
            'user' => $record,
        ]);
    }

    public function detail($id)
    {
        $pengguna = $this->model->find($id);

        if (!$pengguna) {
            return redirect()->to($this->routePath)->with('error', 'Pengguna tidak ditemukan');
        }

        return view($this->viewPath . '/detail', [
            'title' => 'Detail Pengguna',
            'subtitle' => $pengguna->fullname,
            'pengguna' => $pengguna,
        ]);
    }

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
        if (session()->get('role') !== 'OWNER') {
            return false;
        }
        if ($id === session()->get('user_id')) {
            return false;
        }
        return true;
    }

    protected function beforeStore(array $data): array
    {
        $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        return $data;
    }

    protected function beforeUpdate($id, array $data): array
    {
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        return $data;
    }
}
