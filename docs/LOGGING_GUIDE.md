# Request/Response Logging Guide

## Overview

TokoManager includes comprehensive logging capabilities for debugging, monitoring, and audit trails.

## Components

### 1. RequestLogger Filter (`app/Filters/RequestLogger.php`)

Automatically logs all HTTP requests and responses.

**Features:**
- ✅ Logs incoming requests (method, URI, IP, user-agent)
- ✅ Logs outgoing responses (status code, response time)
- ✅ Sanitizes sensitive data (passwords, tokens)
- ✅ Detects and logs slow requests (>1 second)
- ✅ Logs JSON response bodies
- ✅ Can be enabled/disabled via environment variable

### 2. DebugLoggingTrait (`app/Traits/DebugLoggingTrait.php`)

Provides convenient logging methods for controllers.

**Features:**
- ✅ `logAction()` - Log controller actions
- ✅ `logSuccess()` - Log successful operations
- ✅ `logError()` - Log errors with full context
- ✅ `logValidationError()` - Log validation failures
- ✅ `logQuery()` - Log database queries (dev only)
- ✅ `logSlowOperation()` - Log slow operations
- ✅ `logApiCall()` - Log external API calls
- ✅ `logActivity()` - Log user activities (audit trail)
- ✅ `startTimer() / stopTimer()` - Performance monitoring

---

## Setup

### Enable Request Logging

Add to `.env`:
```env
# Enable request/response logging
LOG_REQUESTS=true
```

### Enable in Routes (Optional)

To log all routes globally, add to `app/Config/Filters.php`:

```php
public array $globals = [
    'before' => [
        'requestlog',  // Enable request logging
    ],
    'after' => [
        'requestlog',  // Enable response logging
    ],
];
```

Or enable for specific routes:
```php
public array $filters = [
    'requestlog' => [
        'before' => ['api/*', 'transactions/*'],
        'after' => ['api/*', 'transactions/*']
    ],
];
```

---

## Usage Examples

### Using DebugLoggingTrait in Controllers

```php
<?php

namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Traits\DebugLoggingTrait;

class DeliveryNote extends BaseController
{
    use DebugLoggingTrait;

    public function store()
    {
        // Start performance timer
        $this->startTimer('delivery_note_creation');

        // Log action
        $this->logAction('store', [
            'invoice_id' => $this->request->getPost('invoice_id')
        ]);

        try {
            // Validation
            if (!$this->validate($rules)) {
                $this->logValidationError($this->validator->getErrors());
                return redirect()->back()->withInput();
            }

            // Business logic
            $result = $this->createDeliveryNote($data);

            // Log success
            $this->logSuccess('Delivery note created', $result['id']);

            // Log user activity (audit trail)
            $this->logActivity('Created delivery note', $result['id'], 'DeliveryNote');

            // Check performance
            $duration = $this->stopTimer('delivery_note_creation');
            $this->logSlowOperation('Delivery note creation', $duration, 0.5);

            return redirect()->to('...');

        } catch (\Exception $e) {
            // Log error with full context
            $this->logError('Failed to create delivery note', $e, [
                'invoice_id' => $data['invoice_id'],
                'user_id' => session()->get('user_id')
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getInvoiceItems($invoiceId)
    {
        // Log API call (if calling external service)
        $this->logApiCall('InvoiceService', '/api/invoices/' . $invoiceId, 'GET');

        $items = $this->saleItemModel->where('sale_id', $invoiceId)->findAll();

        return $this->respondSuccess($items);
    }
}
```

### Log Levels

CodeIgniter supports these log levels:
- `emergency` - System is unusable
- `alert` - Action must be taken immediately
- `critical` - Critical conditions
- `error` - Error conditions
- `warning` - Warning conditions
- `notice` - Normal but significant
- `info` - Informational messages
- `debug` - Debug-level messages

### Example Log Output

**Request Log:**
```
[2024-02-02 13:30:45] INFO --> [REQUEST] POST /transactions/delivery-note/store | IP: 127.0.0.1 | Query: none | Body: {"invoice_id":"1","delivery_date":"2024-02-02"} | User-Agent: Mozilla/5.0...
```

**Response Log:**
```
[2024-02-02 13:30:46] INFO --> [RESPONSE] POST /transactions/delivery-note/store | Status: 302 | Time: 245.67ms | Body: none
```

**Controller Action Log:**
```
[2024-02-02 13:30:45] INFO --> [CONTROLLER] App\Controllers\Transactions\DeliveryNote::store | User: 1 | Data: {"invoice_id":"1"}
```

**Error Log:**
```
[2024-02-02 13:30:46] ERROR --> [ERROR] Failed to create delivery note | Exception: Exception | Message: Invoice not found | File: /app/Controllers/Transactions/DeliveryNote.php:145 | Context: {"invoice_id":"999","user_id":"1"}
```

**Slow Request Log:**
```
[2024-02-02 13:30:50] WARNING --> [SLOW REQUEST] POST /transactions/delivery-note/store took 1523.45ms
```

**User Activity Log:**
```
[2024-02-02 13:30:46] INFO --> [USER ACTIVITY] User: admin | Activity: Created delivery note | Resource: DeliveryNote #15
```

---

## Sensitive Data Protection

The RequestLogger automatically sanitizes these fields:
- password
- password_confirmation
- token
- api_key
- secret
- authorization
- credit_card
- cvv
- ssn

**Before sanitization:**
```json
{
  "username": "admin",
  "password": "secret123",
  "api_key": "sk_live_xyz123"
}
```

**After sanitization (logged):**
```json
{
  "username": "admin",
  "password": "***REDACTED***",
  "api_key": "***REDACTED***"
}
```

---

## Log File Locations

Logs are stored in `writable/logs/`:

```
writable/logs/
├── log-2024-02-02.log    (Today's log)
├── log-2024-02-01.log    (Yesterday's log)
└── ...
```

**Log Rotation:**
- Daily rotation (one file per day)
- Automatic cleanup of old logs (configurable)

---

## Performance Monitoring

### Track Slow Operations

```php
public function complexOperation()
{
    $this->startTimer('complex_op');

    // ... complex logic ...

    $duration = $this->stopTimer('complex_op');
    
    // Log if slower than 500ms
    $this->logSlowOperation('Complex operation', $duration, 0.5);
}
```

### Multiple Timers

```php
public function multiStep()
{
    $this->startTimer('step1');
    $this->doStep1();
    $this->stopTimer('step1');  // Logs: [TIMER] step1: 0.1234 seconds

    $this->startTimer('step2');
    $this->doStep2();
    $this->stopTimer('step2');  // Logs: [TIMER] step2: 0.2345 seconds
}
```

---

## Audit Trail

Track user activities for compliance:

```php
// User creates record
$this->logActivity('Created customer', $customerId, 'Customer');

// User updates record
$this->logActivity('Updated product price', $productId, 'Product');

// User deletes record
$this->logActivity('Deleted invoice', $invoiceId, 'Invoice');

// User exports data
$this->logActivity('Exported sales report', null, 'Report');
```

**Output:**
```
[USER ACTIVITY] User: admin | Activity: Created customer | Resource: Customer #123
[USER ACTIVITY] User: admin | Activity: Updated product price | Resource: Product #45
[USER ACTIVITY] User: admin | Activity: Deleted invoice | Resource: Invoice #789
[USER ACTIVITY] User: admin | Activity: Exported sales report | Resource: Report #N/A
```

---

## Debugging Tips

### Enable Debug Mode

In `.env`:
```env
CI_ENVIRONMENT = development
LOG_THRESHOLD = 9  # Log everything
```

### View Logs in Real-Time

**Linux/Mac:**
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

**Windows:**
```powershell
Get-Content writable/logs/log-$(Get-Date -Format yyyy-MM-dd).log -Wait -Tail 50
```

### Search Logs

```bash
# Find all errors
grep "ERROR" writable/logs/log-*.log

# Find slow requests
grep "SLOW REQUEST" writable/logs/log-*.log

# Find specific user activity
grep "User: admin" writable/logs/log-*.log
```

---

## Best Practices

### 1. ✅ Log at Appropriate Levels
- Use `debug` for development-only logs
- Use `info` for general information
- Use `warning` for potential issues
- Use `error` for actual errors

### 2. ✅ Include Context
```php
// Bad
$this->logError('Operation failed', $e);

// Good
$this->logError('Failed to create delivery note', $e, [
    'invoice_id' => $invoiceId,
    'user_id' => session()->get('user_id'),
    'warehouse_id' => $warehouseId
]);
```

### 3. ✅ Don't Log Sensitive Data
```php
// Bad
log_message('info', 'User password: ' . $password);

// Good
log_message('info', 'User authentication attempt');
```

### 4. ✅ Use Structured Logging
```php
// Good - Easy to parse
log_message('info', sprintf(
    'Order created | ID: %d | Total: %s | Customer: %d',
    $orderId, $total, $customerId
));
```

### 5. ✅ Monitor Log File Size
- Implement log rotation
- Archive old logs
- Set up disk space alerts

---

## Production Considerations

### Disable Verbose Logging

In `.env` for production:
```env
CI_ENVIRONMENT = production
LOG_REQUESTS = false  # Disable request logging
LOG_THRESHOLD = 4     # Only log warnings and errors
```

### Log Monitoring

Consider integrating with:
- **Sentry** - Error tracking
- **LogStash** - Log aggregation
- **New Relic** - Application monitoring
- **CloudWatch** - AWS logging

### Log Analysis

Use tools like:
- `grep`, `awk`, `sed` for CLI analysis
- **GoAccess** - Real-time web log analyzer
- **Splunk** - Enterprise log management
- **ELK Stack** - Elasticsearch + Logstash + Kibana

---

## Troubleshooting

### Logs Not Being Written

1. Check permissions:
```bash
chmod -R 777 writable/logs/
```

2. Check LOG_THRESHOLD in `.env`

3. Check disk space:
```bash
df -h
```

### Too Many Logs

1. Reduce LOG_THRESHOLD
2. Disable REQUEST logging
3. Use filters to log only specific routes

### Performance Impact

- Request logging adds ~5-10ms per request
- Only enable in development or for debugging
- Use separate logging server for production

---

## Resources

- [CodeIgniter 4 Logging Docs](https://codeigniter.com/user_guide/general/logging.html)
- [PSR-3 Logger Interface](https://www.php-fig.org/psr/psr-3/)
- Project Documentation in `docs/` folder

---

**Last Updated:** 2024  
**Maintained By:** Development Team  
**Project:** TokoManager POS & Inventory System
