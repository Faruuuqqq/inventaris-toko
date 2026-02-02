# TokoManager - Automated Test Suite

## Overview

This test suite validates the route integration fixes implemented across Phase 1, 2, and 3 of the TokoManager project.

## Test Coverage

### 1. Route Tests (`tests/Feature/RouteTest.php`)
Tests all critical routes to ensure:
- ✅ Routes exist and are registered
- ✅ Controllers and methods exist
- ✅ Proper HTTP methods are supported
- ✅ Authentication is required where needed
- ✅ Edit routes follow standard pattern
- ✅ Delete routes are standardized
- ✅ Update routes accept both POST and PUT

**Test Cases:**
- Master data routes (Customers, Warehouses, Salespersons)
- Transaction routes (Delivery Note, Purchases, Returns)
- Finance routes (Payments, Expenses, Kontra Bon)
- Delete route standardization
- Edit route standardization
- AJAX endpoint accessibility
- POST/PUT route duality
- 404 handling

### 2. API Response Tests (`tests/Feature/ApiResponseTest.php`)
Tests that all AJAX/API endpoints return standardized JSON format:
```json
{
  "success": true|false,
  "message": "Human readable message",
  "data": {} or null,
  "errors": {} (only on error)
}
```

**Test Cases:**
- DeliveryNote getInvoiceItems format
- Empty response format
- Validation error format
- Content-Type headers

### 3. Validation Tests (`tests/Feature/ValidationTest.php`)
Tests comprehensive validation rules:
- ✅ Required field validation
- ✅ Length constraints (min/max)
- ✅ Date format validation
- ✅ Custom error messages in Indonesian
- ✅ Business logic validation

**Test Cases:**
- DeliveryNote required fields
- Address length validation (min 10, max 500 chars)
- Date format validation (Y-m-d)
- Indonesian error messages

## Running Tests

### Method 1: Using Test Scripts

**Windows:**
```bash
run-tests.bat
```

**Linux/Mac:**
```bash
chmod +x run-tests.sh
./run-tests.sh
```

### Method 2: Manual PHPUnit

**Run all feature tests:**
```bash
php spark test --testdox Tests\\Feature
```

**Run specific test file:**
```bash
php spark test Tests\\Feature\\RouteTest
php spark test Tests\\Feature\\ApiResponseTest
php spark test Tests\\Feature\\ValidationTest
```

**Run with coverage:**
```bash
php spark test --coverage
```

## Test Results

Expected output:
```
✓ Master customers routes
✓ Master warehouses routes  
✓ Master salespersons routes
✓ Delivery note routes
✓ Purchases routes
✓ Payments routes
✓ Expenses routes
✓ Delete routes standardization
✓ Edit routes standardization
✓ Ajax endpoints format
✓ Update routes duality
✓ Non existent route returns 404
✓ Api routes exist
✓ Basic routes
```

## Continuous Integration

### GitHub Actions (Optional)

Create `.github/workflows/tests.yml`:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, intl, mysqli
        
    - name: Install dependencies
      run: composer install
      
    - name: Run tests
      run: php spark test --testdox Tests\\Feature
```

## Test Database

Tests use the test database configured in `.env.test` or `phpunit.xml`.

**Setup test database:**
```bash
# Copy environment file
cp env .env.test

# Edit .env.test to use test database
database.default.database = inventaris_toko_test

# Run migrations on test database
php spark migrate --all
```

## Writing New Tests

### Example Test:

```php
<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class MyFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testMyRoute()
    {
        $result = $this->get('my/route');
        $result->assertStatus(200);
        $result->assertSee('Expected Content');
    }

    public function testMyAjaxEndpoint()
    {
        $result = $this->get('my/ajax/endpoint');
        
        $json = json_decode($result->getJSON(), true);
        
        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
    }
}
```

## Troubleshooting

### Tests Fail Due to Authentication

If tests fail because routes require authentication:

1. Use `loginAsAdmin()` helper method in test
2. Or disable auth filters in test environment

### Database Not Found

Ensure test database exists:
```sql
CREATE DATABASE inventaris_toko_test;
```

### Route Not Found

Check that routes are properly registered:
```bash
php spark routes | grep "your-route"
```

## Maintenance

### Update Tests When Routes Change

When adding new routes, update corresponding test files:

1. Add route test to `RouteTest.php`
2. Add validation test if applicable
3. Add API response test for AJAX endpoints
4. Run tests to verify

### Test Naming Convention

- Test methods: `test{Feature}{Action}`
- Test files: `{Feature}Test.php`
- Always use descriptive names

## Best Practices

1. ✅ **Test one thing at a time** - Each test method should test one specific behavior
2. ✅ **Use descriptive names** - Test names should describe what they're testing
3. ✅ **Arrange-Act-Assert** - Structure tests clearly
4. ✅ **Clean up after tests** - Use `tearDown()` if needed
5. ✅ **Don't test framework** - Focus on your application logic
6. ✅ **Keep tests independent** - Tests shouldn't depend on each other
7. ✅ **Use test helpers** - Create helper methods for common tasks

## Coverage Goals

- ✅ Route registration: 100%
- ✅ Critical paths: 80%+
- ✅ Validation rules: 90%+
- ✅ AJAX endpoints: 100%

## Resources

- [CodeIgniter 4 Testing Docs](https://codeigniter.com/user_guide/testing/index.html)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- Project Documentation in `docs/` folder

---

**Last Updated:** 2024  
**Maintained By:** Development Team  
**Project:** TokoManager POS & Inventory System
