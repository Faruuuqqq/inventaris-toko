<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * API Authentication Filter
 * 
 * Validates API tokens for API requests
 */
class ApiAuthFilter implements FilterInterface
{
    /**
     * Before filter - Check API token validity
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = Services::response();
        
        // Get Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Authorization header missing',
                'data' => null
            ])->setStatusCode(401);
        }
        
        // Extract token from "Bearer <token>"
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid authorization format. Use: Bearer <token>',
                'data' => null
            ])->setStatusCode(401);
        }
        
        $token = $matches[1];
        
        // Validate token in database
        $db = \Config\Database::connect();
        
        $result = $db->table('api_tokens')
            ->select('api_tokens.*, users.id, users.username, users.email, users.role')
            ->join('users', 'users.id = api_tokens.user_id')
            ->where('api_tokens.token', $token)
            ->where('api_tokens.expires_at >', date('Y-m-d H:i:s'))
            ->where('api_tokens.is_revoked', 0)
            ->get()
            ->getRowArray();
        
        if (!$result) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid or expired token',
                'data' => null
            ])->setStatusCode(401);
        }
        
        // Store user data in request for controller access
        $request->user_id = $result['user_id'];
        $request->user_email = $result['email'];
        $request->user_role = $result['role'];
        
        // Update last_used timestamp
        $db->table('api_tokens')
            ->where('id', $result['id'])
            ->update(['last_used_at' => date('Y-m-d H:i:s')]);
        
        // Token is valid, allow request to proceed
        return null;
    }

    /**
     * After filter - Not used for authentication
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add CORS headers for API responses
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        
        return $response;
    }
}
