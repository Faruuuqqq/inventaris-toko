<?php

if (!function_exists('is_htmx_request')) {
    /**
     * Check if the current request is an HTMX request
     * 
     * @return bool
     */
    function is_htmx_request(): bool
    {
        $request = \Config\Services::request();
        return $request->hasHeader('HX-Request') && $request->getHeaderLine('HX-Request') === 'true';
    }
}

if (!function_exists('htmx_redirect')) {
    /**
     * Send HTMX redirect header
     * 
     * @param string $url
     * @return void
     */
    function htmx_redirect(string $url): void
    {
        $response = \Config\Services::response();
        $response->setHeader('HX-Redirect', $url);
    }
}

if (!function_exists('htmx_refresh')) {
    /**
     * Trigger HTMX page refresh
     * 
     * @return void
     */
    function htmx_refresh(): void
    {
        $response = \Config\Services::response();
        $response->setHeader('HX-Refresh', 'true');
    }
}

if (!function_exists('htmx_trigger')) {
    /**
     * Trigger HTMX event
     * 
     * @param string $event
     * @param array $data
     * @return void
     */
    function htmx_trigger(string $event, array $data = []): void
    {
        $response = \Config\Services::response();
        $trigger = empty($data) ? $event : json_encode([$event => $data]);
        $response->setHeader('HX-Trigger', $trigger);
    }
}

if (!function_exists('htmx_response')) {
    /**
     * Return appropriate response based on request type
     * 
     * @param string $view
     * @param array $data
     * @return string
     */
    function htmx_response(string $view, array $data = []): string
    {
        if (is_htmx_request()) {
            return view($view, $data);
        }
        
        // For non-HTMX requests, wrap in layout
        $content = view($view, $data);
        return view('layout/main', array_merge($data, ['content' => $content]));
    }
}

if (!function_exists('toast_response')) {
    /**
     * Return JSON response with toast notification for HTMX
     * 
     * @param string $message
     * @param string $type (success, error, warning, info)
     * @param string|null $title
     * @param array $data Additional data to return
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    function toast_response(string $message, string $type = 'success', ?string $title = null, array $data = [])
    {
        $response = \Config\Services::response();
        
        $responseData = array_merge($data, [
            'toast' => [
                'type' => $type,
                'message' => $message,
                'title' => $title
            ]
        ]);
        
        return $response->setJSON($responseData);
    }
}
