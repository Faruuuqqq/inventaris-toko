<?php

namespace App\Listeners;

class SecurityListener
{
    /**
     * Handle request events
     */
    public function onRequest()
    {
        // Sanitize all GET, POST inputs
        $this->sanitizeInputs();
        
        // Set security headers
        $this->setSecurityHeaders();
        
        // Check for suspicious activities
        $this->checkSuspiciousActivity();
    }
    
    /**
     * Sanitize user inputs
     */
    private function sanitizeInputs()
    {
        $request = service('request');
        
        // Sanitize GET data
        foreach ($request->getGet() as $key => $value) {
            if (is_string($value)) {
                $_GET[$key] = $this->cleanString($value);
            }
        }
        
        // Sanitize POST data
        foreach ($request->getPost() as $key => $value) {
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
        // Remove potential XSS tags
        $string = strip_tags($string);
        
        // Remove potentially dangerous JavaScript
        $string = preg_replace('/(java|script):/i', '', $string);
        $string = preg_replace('/on\w+\s*=/i', '', $string);
        
        // Convert HTML entities
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        
        return trim($string);
    }
    
    /**
     * Set security headers
     */
    private function setSecurityHeaders()
    {
        $response = service('response');
        
        // Prevent clickjacking
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME-type sniffing
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        
        // Content Security Policy
        $response->setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'");
        
        // HSTS
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
    
    /**
     * Check for suspicious activities
     */
    private function checkSuspiciousActivity()
    {
        $request = service('request');
        $session = session();
        
        // Check for SQL injection patterns
        $this->checkSQLInjection($request);
        
        // Check for XSS patterns
        $this->checkXSS($request);
        
        // Check for suspicious user agent
        $this->checkUserAgent($request);
        
        // Rate limiting
        $this->checkRateLimiting($request, $session);
    }
    
    /**
     * Check for SQL injection attempts
     */
    private function checkSQLInjection($request)
    {
        $suspiciousPatterns = [
            '/(\s|^)(select|insert|update|delete|drop|union|exec|script)(\s|$)/i',
            '/(\s|^)(or|and)\s+\w+\s*=\s*\w+/i',
            '/(\s|^)(\')|(\")(\s|$)/i'
        ];
        
        $allInputs = array_merge($request->getGet(), $request->getPost());
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSuspiciousActivity('SQL Injection Attempt', [
                            'input' => $key,
                            'value' => $value,
                            'ip' => $request->getIPAddress()
                        ]);
                        
                        // Block the request
                        throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
                    }
                }
            }
        }
    }
    
    /**
     * Check for XSS attempts
     */
    private function checkXSS($request)
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i'
        ];
        
        $allInputs = array_merge($request->getGet(), $request->getPost());
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $this->logSuspiciousActivity('XSS Attempt', [
                            'input' => $key,
                            'value' => $value,
                            'ip' => $request->getIPAddress()
                        ]);
                        
                        // Sanitize the input
                        $_POST[$key] = strip_tags($value);
                    }
                }
            }
        }
    }
    
    /**
     * Check suspicious user agents
     */
    private function checkUserAgent($request)
    {
        $userAgent = $request->getUserAgent();
        $bannedAgents = [
            'sqlmap',
            'nikto',
            'w3af',
            'burp',
            'nmap',
            'python-requests'
        ];
        
        foreach ($bannedAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $this->logSuspiciousActivity('Suspicious User Agent', [
                    'user_agent' => $userAgent,
                    'ip' => $request->getIPAddress()
                ]);
                
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
            }
        }
    }
    
    /**
     * Check rate limiting
     */
    private function checkRateLimiting($request, $session)
    {
        $maxRequestsPerMinute = 60;
        $ip = $request->getIPAddress();
        
        $rateLimitKey = "rate_limit_{$ip}";
        $requestCount = $session->get($rateLimitKey) ?? 0;
        
        // Check if rate limit exceeded
        if ($requestCount >= $maxRequestsPerMinute) {
            $this->logSuspiciousActivity('Rate Limit Exceeded', [
                'ip' => $ip,
                'requests' => $requestCount
            ]);
            
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Too many requests');
        }
        
        // Increment request counter
        $session->set($rateLimitKey, $requestCount + 1);
        
        // Reset counter after 1 minute
        $session->markAsTempdata($rateLimitKey, 60);
    }
    
    /**
     * Log suspicious activities
     */
    private function logSuspiciousActivity($type, $data)
    {
        $logMessage = sprintf(
            "[%s] %s: %s",
            date('Y-m-d H:i:s'),
            $type,
            json_encode($data)
        );
        
        log_message('warning', $logMessage);
        
        // You could also store in database or send alerts here
    }
}