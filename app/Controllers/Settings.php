<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ConfigModel;

class Settings extends BaseController
{
    protected $userModel;
    protected $configModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->configModel = new \App\Models\ConfigModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        $config = $this->configModel->getConfig();

        $data = [
            'title' => 'Pengaturan',
            'subtitle' => 'Kelola pengaturan sistem',
            'user' => $user,
            'config' => $config,
        ];

        return view('layout/main', $data)
            . view('settings/index', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('user_id');
        
        $rules = [
            'fullname' => 'required',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->userModel->update($userId, [
                'fullname' => $this->request->getPost('fullname'),
                'email' => $this->request->getPost('email'),
            ]);

            // Update session
            session()->set('fullname', $this->request->getPost('fullname'));
            session()->set('email', $this->request->getPost('email'));

            return redirect()->back()->with('success', 'Profil berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function changePassword()
    {
        $userId = session()->get('user_id');
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $user = $this->userModel->find($userId);

            // Verify current password
            if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Password saat ini salah');
            }

            // Update password
            $this->userModel->update($userId, [
                'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            ]);

            return redirect()->back()->with('success', 'Password berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStore()
    {
        // Check if current user is OWNER or ADMIN
        $role = session()->get('role');
        if (!in_array($role, ['OWNER', 'ADMIN'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        try {
            $this->configModel->updateMultipleConfig([
                'company_name' => $this->request->getPost('store_name'),
                'company_phone' => $this->request->getPost('store_phone'),
                'company_address' => $this->request->getPost('store_address'),
                'tax_number' => $this->request->getPost('store_npwp'),
            ]);

            return redirect()->back()->with('success', 'Informasi toko berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updatePreferences()
    {
        $userId = session()->get('user_id');
        
        try {
            // Update user preferences (if you have a preferences table)
            // For now, just return success
            return redirect()->back()->with('success', 'Preferensi berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
