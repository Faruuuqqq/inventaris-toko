<?php

namespace App\Traits;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * API Response Trait
 * 
 * Provides standardized JSON response methods for controllers
 * Use this trait in any controller that returns JSON responses
 * 
 * @package App\Traits
 */
trait ApiResponseTrait
{
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code (default: 200)
     * @return ResponseInterface
     */
    protected function respondSuccess($data = null, string $message = 'Success', int $statusCode = 200): ResponseInterface
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($response);
    }

    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code (default: 400)
     * @param mixed $errors Additional error details
     * @return ResponseInterface
     */
    protected function respondError(string $message = 'Error', int $statusCode = 400, $errors = null): ResponseInterface
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($response);
    }

    /**
     * Send created response (201)
     * 
     * @param mixed $data Created resource data
     * @param string $message Success message
     * @return ResponseInterface
     */
    protected function respondCreated($data = null, string $message = 'Resource created successfully'): ResponseInterface
    {
        return $this->respondSuccess($data, $message, 201);
    }

    /**
     * Send no content response (204)
     * 
     * @return ResponseInterface
     */
    protected function respondNoContent(): ResponseInterface
    {
        return $this->response->setStatusCode(204);
    }

    /**
     * Send not found response (404)
     * 
     * @param string $message Error message
     * @return ResponseInterface
     */
    protected function respondNotFound(string $message = 'Resource not found'): ResponseInterface
    {
        return $this->respondError($message, 404);
    }

    /**
     * Send unauthorized response (401)
     * 
     * @param string $message Error message
     * @return ResponseInterface
     */
    protected function respondUnauthorized(string $message = 'Unauthorized'): ResponseInterface
    {
        return $this->respondError($message, 401);
    }

    /**
     * Send forbidden response (403)
     * 
     * @param string $message Error message
     * @return ResponseInterface
     */
    protected function respondForbidden(string $message = 'Forbidden'): ResponseInterface
    {
        return $this->respondError($message, 403);
    }

    /**
     * Send validation error response (422)
     * 
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return ResponseInterface
     */
    protected function respondValidationError(array $errors, string $message = 'Validation failed'): ResponseInterface
    {
        return $this->respondError($message, 422, $errors);
    }

    /**
     * Send internal server error response (500)
     * 
     * @param string $message Error message
     * @return ResponseInterface
     */
    protected function respondInternalError(string $message = 'Internal server error'): ResponseInterface
    {
        return $this->respondError($message, 500);
    }

    /**
     * Send paginated response
     * 
     * @param array $items Array of items
     * @param int $total Total count
     * @param int $page Current page
     * @param int $perPage Items per page
     * @param string $message Success message
     * @return ResponseInterface
     */
    protected function respondPaginated(array $items, int $total, int $page, int $perPage, string $message = 'Success'): ResponseInterface
    {
        $data = [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage),
                'from' => (($page - 1) * $perPage) + 1,
                'to' => min($page * $perPage, $total),
            ]
        ];

        return $this->respondSuccess($data, $message);
    }

    /**
     * Send simple data response (for backward compatibility)
     * Returns data directly without wrapper
     * 
     * @param mixed $data Response data
     * @param int $statusCode HTTP status code (default: 200)
     * @return ResponseInterface
     */
    protected function respondData($data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    /**
     * Send empty array response
     * 
     * @return ResponseInterface
     */
    protected function respondEmpty(): ResponseInterface
    {
        return $this->response->setJSON([]);
    }
}
