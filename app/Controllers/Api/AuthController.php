<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    use ResponseTrait;
    
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
    }
    
    /**
     * API login
     *
     * @return mixed
     */
    public function login()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->userModel->where('username', $username)->first();
        
        if ($user && password_verify($password, $user['password'])) {
            // Generate API token
            $token = $this->generateApiToken($user);
            
            // Update last login
            $this->userModel->update($user['id_user'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user['id_user'],
                        'username' => $user['username'],
                        'fullname' => $user['fullname'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ],
                    'token' => $token,
                    'expires_in' => 3600 // 1 hour
                ]
            ]);
        } else {
            return $this->failUnauthorized('Invalid credentials');
        }
    }
    
    /**
     * API logout
     *
     * @return mixed
     */
    public function logout()
    {
        $token = $this->request->getHeaderLine('Authorization');
        
        if ($token) {
            $token = str_replace('Bearer ', '', $token);
            $this->invalidateApiToken($token);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Logout successful'
        ]);
    }
    
    /**
     * Refresh API token
     *
     * @return mixed
     */
    public function refresh()
    {
        $token = $this->request->getHeaderLine('Authorization');
        
        if (!$token) {
            return $this->failUnauthorized('Token is required');
        }
        
        $token = str_replace('Bearer ', '', $token);
        
        // Validate token
        $user = $this->validateApiToken($token);
        
        if (!$user) {
            return $this->failUnauthorized('Invalid or expired token');
        }
        
        // Generate new token
        $newToken = $this->generateApiToken($user);
        
        // Invalidate old token
        $this->invalidateApiToken($token);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $newToken,
                'expires_in' => 3600 // 1 hour
            ]
        ]);
    }
    
    /**
     * Get current user profile
     *
     * @return mixed
     */
    public function profile()
    {
        $token = $this->request->getHeaderLine('Authorization');
        
        if (!$token) {
            return $this->failUnauthorized('Token is required');
        }
        
        $token = str_replace('Bearer ', '', $token);
        
        // Validate token
        $user = $this->validateApiToken($token);
        
        if (!$user) {
            return $this->failUnauthorized('Invalid or expired token');
        }
        
        return $this->respond([
            'status' => 'success',
            'data' => [
                'id' => $user['id_user'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'email' => $user['email'],
                'role' => $user['role'],
                'created_at' => $user['created_at'],
                'last_login' => $user['last_login']
            ]
        ]);
    }
    
    /**
     * Update user profile
     *
     * @return mixed
     */
    public function updateProfile()
    {
        $token = $this->request->getHeaderLine('Authorization');
        
        if (!$token) {
            return $this->failUnauthorized('Token is required');
        }
        
        $token = str_replace('Bearer ', '', $token);
        
        // Validate token
        $user = $this->validateApiToken($token);
        
        if (!$user) {
            return $this->failUnauthorized('Invalid or expired token');
        }
        
        $rules = [
            'fullname' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|max_length[100]|is_unique[users.email,id_user,{$user['id_user']}]"
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $data = [
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email')
        ];
        
        $updated = $this->userModel->update($user['id_user'], $data);
        
        if ($updated) {
            $user = $this->userModel->find($user['id_user']);
            return $this->respond([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user['id_user'],
                    'username' => $user['username'],
                    'fullname' => $user['fullname'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            return $this->failServerError('Failed to update profile');
        }
    }
    
    /**
     * Change password
     *
     * @return mixed
     */
    public function changePassword()
    {
        $token = $this->request->getHeaderLine('Authorization');
        
        if (!$token) {
            return $this->failUnauthorized('Token is required');
        }
        
        $token = str_replace('Bearer ', '', $token);
        
        // Validate token
        $user = $this->validateApiToken($token);
        
        if (!$user) {
            return $this->failUnauthorized('Invalid or expired token');
        }
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return $this->failValidationError('Current password is incorrect');
        }
        
        // Update password
        $updated = $this->userModel->update($user['id_user'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        if ($updated) {
            // Invalidate all user tokens
            $this->invalidateAllUserTokens($user['id_user']);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } else {
            return $this->failServerError('Failed to change password');
        }
    }
    
    /**
     * Generate API token
     */
    private function generateApiToken($user)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        
        // Store token in database
        $db = \Config\Database::connect();
        $db->table('api_tokens')->insert([
            'user_id' => $user['id_user'],
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $token;
    }
    
    /**
     * Validate API token
     */
    private function validateApiToken($token)
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('api_tokens')
            ->select('api_tokens.user_id, users.*')
            ->join('users', 'users.id_user = api_tokens.user_id')
            ->where('api_tokens.token', $token)
            ->where('api_tokens.expires_at >', date('Y-m-d H:i:s'))
            ->where('api_tokens.is_revoked', 0)
            ->get()
            ->getRowArray();
        
        return $result;
    }
    
    /**
     * Invalidate API token
     */
    private function invalidateApiToken($token)
    {
        $db = \Config\Database::connect();
        
        $db->table('api_tokens')
            ->where('token', $token)
            ->update(['is_revoked' => 1]);
    }
    
    /**
     * Invalidate all user tokens
     */
    private function invalidateAllUserTokens($userId)
    {
        $db = \Config\Database::connect();
        
        $db->table('api_tokens')
            ->where('user_id', $userId)
            ->update(['is_revoked' => 1]);
    }
}