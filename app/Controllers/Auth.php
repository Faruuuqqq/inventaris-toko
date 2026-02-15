<?php
namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login', ['title' => 'Login']);
    }

    public function login()
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $selectedRole = $this->request->getPost('role');

            if (empty($username) || empty($password)) {
                return redirect()->back()
                    ->with('error', 'Username dan password wajib diisi')
                    ->withInput();
            }

            $user = $this->userModel->where('username', $username)->first();

            if (!$user || !password_verify($password, $user->password_hash)) {
                return redirect()->back()
                    ->with('error', 'Username atau password salah')
                    ->withInput();
            }

            // Validate role matches user's actual role
            if ($selectedRole && $user->role !== $selectedRole) {
                return redirect()->back()
                    ->with('error', 'Akun Anda tidak memiliki akses sebagai ' . ucfirst($selectedRole))
                    ->withInput();
            }

            // Password correct, set session
            $sessionData = [
                'user_id' => $user->id,
                'username' => $user->username,
                'fullname' => $user->fullname,
                'role' => $user->role,
                'isLoggedIn' => true,
            ];
            session()->set($sessionData);

            // Regenerate session ID for security
            session()->regenerate();

            log_message('info', "User logged in: {$user->username} (Role: {$user->role})");

            return redirect()->to('/dashboard')->with('success', 'Login berhasil');
        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function logout()
    {
        try {
            $username = session()->get('username');
            session()->destroy();
            log_message('info', "User logged out: {$username}");
            return redirect()->to('/login')->with('success', 'Logout berhasil');
        } catch (\Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            return redirect()->to('/login')->with('success', 'Logout berhasil');
        }
    }
}