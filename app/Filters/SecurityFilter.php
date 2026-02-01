<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SecurityFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check for CSRF token
        if ($request->getMethod() === 'post') {
            $this->validateCSRF($request);
        }
        
        // Sanitize inputs
        $this->sanitizeInputs($request);
    }
    
    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Set security headers
        $this->setSecurityHeaders($response);
    }
    
    /**
     * Validate CSRF token
     */
    private function validateCSRF(RequestInterface $request)
    {
        $config = config('App');
        
        if ($config->CSRFProtection && $config->CSRFTokenName && $config->CSRFHeaderName) {
            $tokenName = $config->CSRFTokenName;
            $headerName = $config->CSRFHeaderName;
            
            $token = $request->getPost($tokenName) ?? $request->getHeaderLine($headerName);
            $sessionToken = session()->get($tokenName);
            
            if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Invalid CSRF token');
            }
        }
    }
    
    /**
     * Sanitize inputs
     */
    private function sanitizeInputs(RequestInterface $request)
    {
        // Only sanitize for HTML forms, not API calls
        if ($request->getHeaderLine('Content-Type') !== 'application/json') {
            $this->sanitizeGet($request);
            $this->sanitizePost($request);
        }
    }
    
    /**
     * Sanitize GET parameters
     */
    private function sanitizeGet(RequestInterface $request)
    {
        $get = $request->getGet();
        foreach ($get as $key => $value) {
            if (is_string($value)) {
                $_GET[$key] = $this->cleanString($value);
            }
        }
    }
    
    /**
     * Sanitize POST parameters
     */
    private function sanitizePost(RequestInterface $request)
    {
        $post = $request->getPost();
        foreach ($post as $key => $value) {
            if (is_string($value)) {
                $_POST[$key] = $this->cleanString($value);
            }
        }
    }
    
    /**
     * Clean string from potential XSS
     */
    private function cleanString($string)
    {
        // Remove tags
        $string = strip_tags($string);
        
        // Convert HTML entities
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        
        // Remove potential JavaScript
        $string = preg_replace('/(java|script):/i', '', $string);
        $string = preg_replace('/on\w+\s*=/i', '', $string);
        
        return trim($string);
    }
    
    /**
     * Set security headers
     */
    private function setSecurityHeaders(ResponseInterface $response)
    {
        // Prevent clickjacking
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME-type sniffing
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        
        // Permissions Policy (replaces Feature-Policy)
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        // Note: unsafe-eval is needed for Alpine.js x-data attribute evaluation
        // This is acceptable since Alpine.js input is from our trusted application code
        $response->setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'");
        
        // Referrer Policy
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // HSTS (HTTP Strict Transport Security)
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        // Disable server information disclosure
        $response->setHeader('Server', 'TokoManager');
        
        // Prevent caching of sensitive content
        $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
    }
}