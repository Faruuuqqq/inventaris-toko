<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Request/Response Logging Filter
 * 
 * Logs all HTTP requests and responses for debugging and monitoring
 * Can be enabled/disabled via environment variable
 */
class RequestLogger implements FilterInterface
{
    /**
     * Log incoming request
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only log if enabled
        if (!env('LOG_REQUESTS', false)) {
            return;
        }

        $method = $request->getMethod();
        $uri = (string) $request->getUri();
        $ip = $request->getIPAddress();
        $userAgent = $request->getUserAgent()->getAgentString();

        // Get request body (for POST/PUT)
        $body = '';
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $rawBody = $request->getBody();
            
            // Don't log passwords or sensitive data
            $bodyData = $request->getPost() ?: $request->getJSON(true);
            if (is_array($bodyData)) {
                $bodyData = $this->sanitizeSensitiveData($bodyData);
                $body = json_encode($bodyData);
            } else {
                $body = substr($rawBody, 0, 500); // Limit body size
            }
        }

        // Get query parameters
        $queryParams = $request->getGet();
        $queryString = http_build_query($queryParams);

        // Log request
        log_message('info', sprintf(
            '[REQUEST] %s %s | IP: %s | Query: %s | Body: %s | User-Agent: %s',
            $method,
            $uri,
            $ip,
            $queryString ?: 'none',
            $body ?: 'none',
            substr($userAgent, 0, 100)
        ));

        // Store start time for response time calculation
        $request->startTime = microtime(true);
    }

    /**
     * Log outgoing response
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only log if enabled
        if (!env('LOG_REQUESTS', false)) {
            return;
        }

        $method = $request->getMethod();
        $uri = (string) $request->getUri();
        $statusCode = $response->getStatusCode();
        
        // Calculate response time
        $responseTime = 0;
        if (isset($request->startTime)) {
            $responseTime = round((microtime(true) - $request->startTime) * 1000, 2);
        }

        // Get response body (only for JSON responses)
        $contentType = $response->getHeaderLine('Content-Type');
        $responseBody = '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $body = $response->getBody();
            if (strlen($body) < 1000) {
                $responseBody = $body;
            } else {
                $responseBody = substr($body, 0, 1000) . '... (truncated)';
            }
        }

        // Determine log level based on status code
        $logLevel = 'info';
        if ($statusCode >= 400 && $statusCode < 500) {
            $logLevel = 'warning';
        } elseif ($statusCode >= 500) {
            $logLevel = 'error';
        }

        // Log response
        log_message($logLevel, sprintf(
            '[RESPONSE] %s %s | Status: %d | Time: %sms | Body: %s',
            $method,
            $uri,
            $statusCode,
            $responseTime,
            $responseBody ?: 'none'
        ));

        // Log slow requests (over 1 second)
        if ($responseTime > 1000) {
            log_message('warning', sprintf(
                '[SLOW REQUEST] %s %s took %sms',
                $method,
                $uri,
                $responseTime
            ));
        }
    }

    /**
     * Remove sensitive data from request body
     */
    private function sanitizeSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'authorization',
            'credit_card',
            'cvv',
            'ssn',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }
}
