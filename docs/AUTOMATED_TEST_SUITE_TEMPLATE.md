# 🧪 AUTOMATED TEST SUITE TEMPLATE
## Inventaris Toko - Test Automation Framework

**Created**: February 3, 2026  
**Framework**: PHP Unit Tests (Can be adapted to other frameworks)  
**Purpose**: Enable automated testing of all endpoints  
**Status**: Ready for Implementation

---

## TABLE OF CONTENTS

1. [Introduction](#introduction)
2. [Setup Instructions](#setup-instructions)
3. [Test Structure](#test-structure)
4. [Example Test Cases](#example-test-cases)
5. [Running Tests](#running-tests)
6. [Continuous Integration](#continuous-integration)

---

## INTRODUCTION

This template provides a framework for automated testing of the Inventaris Toko API endpoints. It includes:

- Unit test structure
- Integration test templates
- Mock data setup
- Test case examples
- CI/CD integration guidance

### Benefits of Automated Testing

✅ Catch regressions early  
✅ Ensure API consistency  
✅ Speed up testing process  
✅ Improve code confidence  
✅ Enable continuous deployment  

---

## SETUP INSTRUCTIONS

### 1. Install Testing Framework

```bash
composer require --dev phpunit/phpunit
```

### 2. Create Test Directory Structure

```
tests/
├── Unit/
│   ├── Controllers/
│   │   ├── Master/
│   │   │   ├── ProductsControllerTest.php
│   │   │   ├── CustomersControllerTest.php
│   │   │   ├── SuppliersControllerTest.php
│   │   │   └── WarehousesControllerTest.php
│   │   ├── Transactions/
│   │   │   ├── SalesControllerTest.php
│   │   │   ├── PurchasesControllerTest.php
│   │   │   └── ReturnsControllerTest.php
│   │   ├── Finance/
│   │   │   ├── ExpensesControllerTest.php
│   │   │   ├── PaymentsControllerTest.php
│   │   │   └── KontraBonControllerTest.php
│   │   └── Info/
│   │       ├── HistoryControllerTest.php
│   │       ├── StockControllerTest.php
│   │       └── SaldoControllerTest.php
│   ├── Models/
│   │   ├── CustomerModelTest.php
│   │   ├── ProductModelTest.php
│   │   └── SalesModelTest.php
│   └── Traits/
├── Integration/
│   ├── SalesWorkflowTest.php
│   ├── PurchaseWorkflowTest.php
│   ├── PaymentWorkflowTest.php
│   └── InventoryWorkflowTest.php
├── Feature/
│   ├── AuthenticationTest.php
│   ├── MasterDataTest.php
│   ├── TransactionTest.php
│   └── ReportingTest.php
├── Fixtures/
│   ├── customer_data.json
│   ├── product_data.json
│   ├── sales_data.json
│   └── purchase_data.json
└── phpunit.xml
```

### 3. Configure phpunit.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheResultFile=".phpunit.cache/test-results"
         executionOrder="depends,defects"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">app</directory>
        </include>
        <exclude>
            <directory>app/Views</directory>
        </exclude>
    </coverage>

    <php>
        <ini name="display_errors" value="On"/>
        <ini name="error_reporting" value="-1"/>
        <env name="CI_ENVIRONMENT" value="testing"/>
    </php>
</phpunit>
```

---

## TEST STRUCTURE

### Base Test Class Template

```php
<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;

class TestBase extends CIUnitTestCase
{
    protected $baseURL = 'http://localhost/inventaris-toko';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset database state
        $this->resetDatabase();
        
        // Seed test data
        $this->seedTestData();
    }
    
    /**
     * Reset database to clean state
     */
    protected function resetDatabase()
    {
        // Truncate tables
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'users',
            'customers',
            'products',
            'suppliers',
            'warehouses',
            'sales',
            'purchases',
            'expenses',
            'payments'
        ];
        
        foreach ($tables as $table) {
            $db->query("TRUNCATE TABLE {$table}");
        }
        
        $db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    /**
     * Seed test data
     */
    protected function seedTestData()
    {
        // Create test customers
        $customerData = [
            [
                'code' => 'CUST001',
                'name' => 'PT Test Jaya',
                'phone' => '081234567890',
                'address' => 'Jakarta'
            ],
            [
                'code' => 'CUST002',
                'name' => 'CV Test Sukses',
                'phone' => '082234567890',
                'address' => 'Surabaya'
            ]
        ];
        
        $db = \Config\Database::connect();
        foreach ($customerData as $data) {
            $db->table('customers')->insert($data);
        }
    }
    
    /**
     * Login user
     */
    protected function login($email = 'user@test.com', $password = 'password')
    {
        $this->post('/login', [
            'username' => $email,
            'password' => $password
        ]);
    }
    
    /**
     * Assert response has success message
     */
    protected function assertSuccess()
    {
        $this->assertTrue(
            $this->response->getStatusCode() === 200 || 
            $this->response->getStatusCode() === 302
        );
    }
    
    /**
     * Assert response is JSON
     */
    protected function assertIsJson()
    {
        $this->assertStringContainsString(
            'application/json',
            $this->response->getHeaderLine('Content-Type')
        );
    }
}
```

---

## EXAMPLE TEST CASES

### 1. Customer Controller Test

```php
<?php

namespace Tests\Unit\Controllers\Master;

use Tests\TestBase;

class CustomersControllerTest extends TestBase
{
    private $controller = 'Master\Customers';
    
    /**
     * Test listing all customers
     */
    public function testListCustomers()
    {
        $this->login();
        $result = $this->get('/master/customers/');
        
        $this->assertSuccess();
        $this->assertResponseHasHeader('Content-Type');
    }
    
    /**
     * Test creating a customer
     */
    public function testCreateCustomer()
    {
        $this->login();
        
        $data = [
            'name' => 'PT New Customer',
            'code' => 'CUST003',
            'phone' => '083234567890',
            'address' => 'Bandung',
            'credit_limit' => 5000000
        ];
        
        $result = $this->post('/master/customers/store', $data);
        
        $this->assertSuccess();
        
        // Verify data was saved
        $db = \Config\Database::connect();
        $customer = $db->table('customers')
            ->where('code', 'CUST003')
            ->first();
        
        $this->assertNotNull($customer);
        $this->assertEquals('PT New Customer', $customer->name);
    }
    
    /**
     * Test validation error on missing required field
     */
    public function testCreateCustomerValidationError()
    {
        $this->login();
        
        $data = [
            'code' => 'CUST004',
            // Missing 'name' field (required)
            'phone' => '084234567890'
        ];
        
        $result = $this->post('/master/customers/store', $data);
        
        $this->assertResponseStatus(422); // Validation error
    }
    
    /**
     * Test getting customer detail
     */
    public function testGetCustomerDetail()
    {
        $this->login();
        $result = $this->get('/master/customers/1');
        
        $this->assertSuccess();
        $this->assertResponseHasHeader('Content-Type');
    }
    
    /**
     * Test updating customer
     */
    public function testUpdateCustomer()
    {
        $this->login();
        
        $data = [
            'name' => 'PT Updated Customer',
            'phone' => '081234567891',
            'credit_limit' => 6000000
        ];
        
        $result = $this->put('/master/customers/1', $data);
        
        $this->assertSuccess();
    }
    
    /**
     * Test deleting customer
     */
    public function testDeleteCustomer()
    {
        $this->login();
        $result = $this->delete('/master/customers/2');
        
        $this->assertSuccess();
        
        // Verify record was deleted
        $db = \Config\Database::connect();
        $customer = $db->table('customers')
            ->where('id', 2)
            ->first();
        
        $this->assertNull($customer);
    }
    
    /**
     * Test getting customer list for dropdown
     */
    public function testGetCustomerList()
    {
        $this->login();
        $result = $this->get('/master/customers/getList');
        
        $this->assertSuccess();
        $this->assertIsJson();
        
        $data = json_decode($this->getJSON(), true);
        $this->assertIsArray($data);
    }
}
```

### 2. Sales Transaction Test

```php
<?php

namespace Tests\Integration;

use Tests\TestBase;

class SalesWorkflowTest extends TestBase
{
    /**
     * Test complete cash sale workflow
     */
    public function testCashSaleWorkflow()
    {
        $this->login();
        
        // Create sale
        $saleData = [
            'customer_id' => 1,
            'warehouse_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 10,
                    'price' => 50000
                ],
                [
                    'product_id' => 2,
                    'quantity' => 5,
                    'price' => 100000
                ]
            ],
            'notes' => 'Test sale'
        ];
        
        $result = $this->post('/transactions/sales/storeCash', $saleData);
        $this->assertSuccess();
        
        // Verify sale was created
        $db = \Config\Database::connect();
        $sale = $db->table('sales')
            ->orderBy('id', 'DESC')
            ->first();
        
        $this->assertNotNull($sale);
        $this->assertEquals(1, $sale->customer_id);
        
        // Verify stock was decremented
        $stock = $db->table('stock')
            ->where('product_id', 1)
            ->first();
        
        $this->assertEquals(90, $stock->quantity); // 100 - 10
    }
    
    /**
     * Test credit sale workflow
     */
    public function testCreditSaleWorkflow()
    {
        $this->login();
        
        $saleData = [
            'customer_id' => 1,
            'warehouse_id' => 1,
            'salesperson_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 20,
                    'price' => 50000
                ]
            ],
            'due_date' => '2025-03-01'
        ];
        
        $result = $this->post('/transactions/sales/storeCredit', $saleData);
        $this->assertSuccess();
        
        // Verify receivable was created
        $db = \Config\Database::connect();
        $receivable = $db->table('receivables')
            ->where('customer_id', 1)
            ->first();
        
        $this->assertNotNull($receivable);
        $this->assertEquals(1000000, $receivable->amount); // 20 * 50000
    }
    
    /**
     * Test sale fails with insufficient stock
     */
    public function testSaleFailsWithInsufficientStock()
    {
        $this->login();
        
        $saleData = [
            'customer_id' => 1,
            'warehouse_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 1000, // More than available
                    'price' => 50000
                ]
            ]
        ];
        
        $result = $this->post('/transactions/sales/storeCash', $saleData);
        
        $this->assertResponseStatus(409); // Conflict
    }
}
```

### 3. AJAX Endpoint Test

```php
<?php

namespace Tests\Feature;

use Tests\TestBase;

class ReportingTest extends TestBase
{
    /**
     * Test sales history AJAX endpoint
     */
    public function testSalesHistoryAjax()
    {
        $this->login();
        
        $result = $this->get('/info/history/sales-data');
        
        $this->assertSuccess();
        $this->assertIsJson();
        
        $data = json_decode($this->getJSON(), true);
        $this->assertIsArray($data);
    }
    
    /**
     * Test sales history with filters
     */
    public function testSalesHistoryWithFilters()
    {
        $this->login();
        
        $result = $this->get('/info/history/sales-data', [
            'start_date' => '2025-01-01',
            'end_date' => '2025-02-28',
            'customer_id' => 1
        ]);
        
        $this->assertSuccess();
        $this->assertIsJson();
    }
    
    /**
     * Test stock data endpoint (Saldo) - CRITICAL FIX TEST
     */
    public function testSaldoStockDataEndpoint()
    {
        $this->login();
        
        // This tests the critical fix from Phase 3
        $result = $this->get('/info/saldo/stock-data');
        
        $this->assertSuccess();
        $this->assertIsJson();
        
        $data = json_decode($this->getJSON(), true);
        $this->assertIsArray($data);
        
        // Verify response contains expected fields
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('product_id', $first);
            $this->assertArrayHasKey('quantity', $first);
            $this->assertArrayHasKey('warehouse_id', $first);
        }
    }
    
    /**
     * Test supplier dropdown - CRITICAL FIX TEST
     */
    public function testSupplierGetList()
    {
        $this->login();
        
        // This tests the critical fix from Phase 3
        $result = $this->get('/master/suppliers/getList');
        
        $this->assertSuccess();
        $this->assertIsJson();
        
        $data = json_decode($this->getJSON(), true);
        $this->assertIsArray($data);
        
        // Verify response contains supplier data
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('id', $first);
            $this->assertArrayHasKey('name', $first);
        }
    }
}
```

---

## RUNNING TESTS

### Run All Tests

```bash
./vendor/bin/phpunit
```

### Run Specific Test Suite

```bash
./vendor/bin/phpunit tests/Unit/
./vendor/bin/phpunit tests/Integration/
./vendor/bin/phpunit tests/Feature/
```

### Run Specific Test File

```bash
./vendor/bin/phpunit tests/Unit/Controllers/Master/CustomersControllerTest.php
```

### Run Specific Test Method

```bash
./vendor/bin/phpunit --filter testCreateCustomer
```

### Generate Coverage Report

```bash
./vendor/bin/phpunit --coverage-html build/coverage
```

---

## CONTINUOUS INTEGRATION

### GitHub Actions Example

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: inventaris_toko_test
          MYSQL_ROOT_PASSWORD: root
        options: >-
          --health-cmd="mysqladmin ping -u root -proot"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=5

    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.0'
        extensions: mysqli, pdo_mysql
    
    - name: Install dependencies
      run: composer install
    
    - name: Run tests
      run: ./vendor/bin/phpunit
      env:
        DB_HOST: localhost
        DB_USER: root
        DB_PASSWORD: root
        DB_NAME: inventaris_toko_test
    
    - name: Upload coverage
      uses: codecov/codecov-action@v2
```

---

## TEST COVERAGE GOALS

| Component | Target | Current |
|-----------|--------|---------|
| Controllers | 90% | To be tracked |
| Models | 85% | To be tracked |
| Routes | 100% | To be verified |
| Critical Features | 95% | To be achieved |

---

## MOCK DATA

### Fixture: customer_data.json

```json
[
  {
    "code": "CUST001",
    "name": "PT Test Jaya",
    "phone": "081234567890",
    "address": "Jakarta",
    "credit_limit": 5000000
  },
  {
    "code": "CUST002",
    "name": "CV Test Sukses",
    "phone": "082234567890",
    "address": "Surabaya",
    "credit_limit": 3000000
  }
]
```

---

## BEST PRACTICES

1. **Test Isolation**: Each test should be independent
2. **Meaningful Names**: Test names should describe what is tested
3. **AAA Pattern**: Arrange, Act, Assert
4. **DRY Principle**: Use setup/teardown for common code
5. **Assertions**: Use multiple assertions per test where appropriate
6. **Mocking**: Mock external dependencies (email, file uploads)
7. **Performance**: Keep tests fast (<1ms per test)
8. **Documentation**: Document complex test logic

---

## CRITICAL TESTS TO IMPLEMENT

✅ **Test Phase 3 Fixes**:
- Suppliers::getList() returns proper JSON
- Saldo /stock-data endpoint works (not /stockData)

✅ **Test Core Workflows**:
- Complete sales transaction (create → delivery → payment)
- Purchase transaction (create → receive → payment)
- Return processing (create → approve/reject)

✅ **Test Validations**:
- Required fields validation
- Credit limit validation
- Stock availability validation

✅ **Test Integrations**:
- Stock updates after transactions
- Balance calculations
- Cascading deletes

---

## MAINTENANCE

### Regular Test Maintenance

- Update tests when features change
- Remove tests for deprecated features
- Add tests for new features
- Keep fixtures up to date
- Review and refactor test code periodically

### Test Evolution

Phase 1 (Current): Basic CRUD operations  
Phase 2 (Next): Workflow and integration tests  
Phase 3 (Future): Performance and load tests  
Phase 4 (Future): Security and penetration tests  

---

## SUMMARY

This test suite template provides a foundation for automated testing. Implementing these tests will:

✅ Ensure code quality  
✅ Catch regressions early  
✅ Enable continuous deployment  
✅ Provide documentation through examples  
✅ Increase team confidence in changes  

---

**Template Version**: 1.0  
**Created**: February 3, 2026  
**Status**: Ready for Implementation  

**Next Steps**:
1. Set up test environment
2. Implement base test class
3. Create unit tests for controllers
4. Add integration tests for workflows
5. Set up continuous integration
6. Monitor test coverage

---

*End of Test Suite Template*
