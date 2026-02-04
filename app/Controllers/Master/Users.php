<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\UserModel;
use App\Services\UserDataService;
use CodeIgniter\Model;

class Users extends BaseCRUDController
{
    protected string $viewPath = 'master/users';
    protected string $routePath = '/master/users';
    protected string $entityName = 'Pengguna';
    protected string $entityNamePlural = 'Users';

    protected UserDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new UserDataService();
    }

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

    /**
     * Override index to use UserDataService
     */
    public function index()
    {
        try {
            $data = array_merge(
                ['title' => 'Daftar Pengguna'],
                $this->dataService->getIndexData()
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Users index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pengguna');
        }
    }

    /**
     * Override create to use UserDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Pengguna',
                'subtitle' => 'Tambahkan pengguna baru',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use UserDataService and pass 'user' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Pengguna',
                'subtitle' => 'Ubah data pengguna',
                'user' => $record,
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use UserDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Pengguna tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Pengguna',
                'subtitle' => $detailData['pengguna']->fullname,
            ],
            $detailData
        );

        return view($this->viewPath . '/detail', $data);
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
