<?php

namespace App\Traits;

/**
 * Debug Logging Trait
 * 
 * Provides convenient logging methods for controllers
 * Use this trait to add structured logging to your controllers
 */
trait DebugLoggingTrait
{
    /**
     * Log controller action
     * 
     * @param string $action Action name
     * @param array $data Additional data to log
     * @param string $level Log level (info, error, debug, etc.)
     */
    protected function logAction(string $action, array $data = [], string $level = 'info'): void
    {
        $controllerName = get_class($this);
        $userId = session()->get('user_id') ?? 'guest';
        
        $message = sprintf(
            '[CONTROLLER] %s::%s | User: %s | Data: %s',
            $controllerName,
            $action,
            $userId,
            json_encode($data)
        );
        
        log_message($level, $message);
    }

    /**
     * Log successful operation
     * 
     * @param string $operation Operation description
     * @param mixed $id Resource ID
     */
    protected function logSuccess(string $operation, $id = null): void
    {
        $message = sprintf(
            '[SUCCESS] %s | ID: %s | User: %s',
            $operation,
            $id ?? 'N/A',
            session()->get('user_id') ?? 'guest'
        );
        
        log_message('info', $message);
    }

    /**
     * Log error with context
     * 
     * @param string $operation Operation that failed
     * @param \Exception $exception Exception object
     * @param array $context Additional context
     */
    protected function logError(string $operation, \Exception $exception, array $context = []): void
    {
        $message = sprintf(
            '[ERROR] %s | Exception: %s | Message: %s | File: %s:%d | Context: %s',
            $operation,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            json_encode($context)
        );
        
        log_message('error', $message);
        
        // Also log stack trace for debugging
        if (ENVIRONMENT === 'development') {
            log_message('debug', $exception->getTraceAsString());
        }
    }

    /**
     * Log validation failure
     * 
     * @param array $errors Validation errors
     */
    protected function logValidationError(array $errors): void
    {
        $message = sprintf(
            '[VALIDATION FAILED] User: %s | Errors: %s',
            session()->get('user_id') ?? 'guest',
            json_encode($errors)
        );
        
        log_message('warning', $message);
    }

    /**
     * Log database query for debugging
     * 
     * @param string $query SQL query
     * @param array $bindings Query bindings
     */
    protected function logQuery(string $query, array $bindings = []): void
    {
        if (ENVIRONMENT !== 'development') {
            return; // Only log queries in development
        }
        
        $message = sprintf(
            '[QUERY] SQL: %s | Bindings: %s',
            $query,
            json_encode($bindings)
        );
        
        log_message('debug', $message);
    }

    /**
     * Log slow operation (performance monitoring)
     * 
     * @param string $operation Operation name
     * @param float $duration Duration in seconds
     * @param float $threshold Threshold for "slow" (default 1 second)
     */
    protected function logSlowOperation(string $operation, float $duration, float $threshold = 1.0): void
    {
        if ($duration < $threshold) {
            return; // Not slow, don't log
        }
        
        $message = sprintf(
            '[SLOW OPERATION] %s took %.2f seconds (threshold: %.2f)',
            $operation,
            $duration,
            $threshold
        );
        
        log_message('warning', $message);
    }

    /**
     * Log API call to external service
     * 
     * @param string $service Service name
     * @param string $endpoint Endpoint URL
     * @param string $method HTTP method
     * @param array $data Request data
     */
    protected function logApiCall(string $service, string $endpoint, string $method, array $data = []): void
    {
        $message = sprintf(
            '[API CALL] Service: %s | %s %s | Data: %s',
            $service,
            $method,
            $endpoint,
            json_encode($data)
        );
        
        log_message('info', $message);
    }

    /**
     * Log user activity (audit trail)
     * 
     * @param string $activity Activity description
     * @param mixed $resourceId Resource ID
     * @param string $resourceType Resource type
     */
    protected function logActivity(string $activity, $resourceId = null, string $resourceType = null): void
    {
        $message = sprintf(
            '[USER ACTIVITY] User: %s | Activity: %s | Resource: %s #%s',
            session()->get('username') ?? 'guest',
            $activity,
            $resourceType ?? 'N/A',
            $resourceId ?? 'N/A'
        );
        
        log_message('info', $message);
    }

    /**
     * Start performance timer
     * 
     * @param string $timerName Timer identifier
     */
    protected function startTimer(string $timerName): void
    {
        if (!isset($this->timers)) {
            $this->timers = [];
        }
        
        $this->timers[$timerName] = microtime(true);
    }

    /**
     * Stop performance timer and log
     * 
     * @param string $timerName Timer identifier
     * @param bool $logResult Whether to log the result
     * @return float Duration in seconds
     */
    protected function stopTimer(string $timerName, bool $logResult = true): float
    {
        if (!isset($this->timers[$timerName])) {
            return 0.0;
        }
        
        $duration = microtime(true) - $this->timers[$timerName];
        
        if ($logResult) {
            log_message('debug', sprintf(
                '[TIMER] %s: %.4f seconds',
                $timerName,
                $duration
            ));
        }
        
        unset($this->timers[$timerName]);
        
        return $duration;
    }
}
