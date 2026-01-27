<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna',
            'subtitle' => 'Kelola pengguna sistem (Owner only)',
            'users' => $this->userModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('master/users/index', $data));
    }

    public function store()
    {
        // Check if current user is OWNER
        if (session()->get('role') !== 'OWNER') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'fullname' => 'required',
            'role' => 'required|in_list[OWNER,ADMIN,GUDANG,SALES]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->userModel->insert([
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'fullname' => $this->request->getPost('fullname'),
                'role' => $this->request->getPost('role'),
            ]);

            return redirect()->to('/master/users')->with('success', 'Pengguna berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function update($id)
    {
        // Check if current user is OWNER
        if (session()->get('role') !== 'OWNER') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $rules = [
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'fullname' => 'required',
            'role' => 'required|in_list[OWNER,ADMIN,GUDANG,SALES]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'fullname' => $this->request->getPost('fullname'),
                'role' => $this->request->getPost('role'),
            ];

            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->userModel->update($id, $data);

            return redirect()->to('/master/users')->with('success', 'Pengguna berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        // Check if current user is OWNER
        if (session()->get('role') !== 'OWNER') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri');
        }

        try {
            $this->userModel->delete($id);
            return redirect()->to('/master/users')->with('success', 'Pengguna berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
