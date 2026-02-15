# 🧪 Testing Guide - TokoManager POS

## 📋 Table of Contents
- [🔧 Prerequisites](#-prerequisites)
- [🚀 Quick Testing Setup](#-quick-testing-setup)
- [🧪 Manual Testing Guide](#-manual-testing-guide)
- [🤖 Automated Testing](#-automated-testing)
- [📊 Testing Reports](#-testing-reports)
- [🐛 Bug Reporting](#-bug-reporting)

---

## 🔧 Prerequisites

### Required Tools
- **PHP** 8.1+ with required extensions
- **Composer** 2.0+
- **MySQL/MariaDB** 5.7+
- **PHPUnit** 10.x (installed via Composer)
- **Postman** (for API testing)
- **Browser** (Chrome/Firefox for manual testing)

### Database Setup
```bash
# 1. Create database
mysql -u root -p
CREATE DATABASE inventaris_toko;

# 2. Run migrations
php spark migrate

# 3. Seed test data
php spark db:seed DatabaseSeeder
php spark db:seed NotificationSeeder
```

---

## 🚀 Quick Testing Setup

### 1. Clone & Install
```bash
git clone <repository-url>
cd inventaris-toko
composer install
cp env .env
# Edit .env with your database credentials
php spark key:generate
```

### 2. Database Setup
```bash
# Method A: Migrations (Recommended)
php spark migrate
php spark db:seed DatabaseSeeder
php spark db:seed NotificationSeeder

# Method B: SQL Import (if available)
mysql -u root -p inventaris_toko < database.sql
```

### 3. Start Server
```bash
php spark serve --host localhost --port 8080
```

### 4. Access Application
- **Web App**: http://localhost:8080
- **API Base**: http://localhost:8080/api
- **Login**: admin@example.com / password123

---

## 🧪 Manual Testing Guide

### 🔐 Authentication Testing

| Test Case | Steps | Expected Result |
|-----------|-------|-----------------|
| **Valid Login** | 1. Go to /login<br>2. Enter admin@example.com<br>3. Enter password123 | Login successful, redirect to dashboard |
| **Invalid Login** | 1. Go to /login<br>2. Enter wrong email/password | Error message shown |
| **Logout** | 1. Click user menu<br>2. Click Logout | Session cleared, redirect to login |
| **Role Access** | 1. Login with different roles<br>2. Access restricted pages | Role-based access control working |

### 📊 Dashboard Testing

| Feature | Test Steps | Expected Result |
|---------|------------|-----------------|
| **Dashboard Load** | 1. Login<br>2. Navigate to /dashboard | Dashboard loads with statistics |
| **Charts Display** | 1. Check analytics section | Charts render with data |
| **Quick Actions** | 1. Click action buttons | Navigate to correct pages |
| **Responsive Design** | 1. Resize browser<br>2. Test mobile view | Layout adapts properly |

### 📦 Master Data Testing

#### Products
```bash
# Test Product CRUD
1. Navigate to /master/products
2. Test Create: Click "Tambah Produk"
3. Test Update: Click "Edit" on existing product
4. Test Delete: Click "Hapus" (test delete modal)
5. Test Search: Use search box
6. Test Pagination: Navigate through pages
```

#### Customers
```bash
# Test Customer Management
1. Navigate to /master/customers
2. Test adding new customer
3. Test credit limit functionality
4. Test customer editing
5. Test customer search/filter
```

#### Suppliers
```bash
# Test Supplier Management
1. Navigate to /master/suppliers
2. Test supplier creation
3. Test payment terms
4. Test supplier contact info
```

### 💰 Transaction Testing

#### Sales
```bash
# Test Cash Sales
1. Navigate to /transactions/sales/cash
2. Select customer
3. Add products to cart
4. Check calculations
5. Complete transaction
6. Verify inventory update

# Test Credit Sales
1. Navigate to /transactions/sales/credit
2. Select customer with credit limit
3. Add products
4. Set due date
5. Verify receivable created
```

#### Purchases
```bash
# Test Purchase Order
1. Navigate to /transactions/purchases
2. Select supplier
3. Add items
4. Check total calculation
5. Save PO
6. Test receiving items
```

### 🔔 Notification System Testing

| Test Case | Steps | Expected Result |
|-----------|-------|-----------------|
| **Notification Badge** | 1. Login with admin<br>2. Check header bell icon | Badge shows unread count |
| **Notification Dropdown** | 1. Click bell icon<br>2. View notifications | List of notifications with icons |
| **Mark as Read** | 1. Click notification item<br>2. Refresh page | Item marked as read |
| **Notification Settings** | 1. Go to /settings<br>2. Toggle notification types<br>3. Save changes | Settings persisted |
| **Real-time Updates** | 1. Keep page open<br>2. Create low stock item<br>3. Wait 30 seconds | New notification appears |

### 📊 Reports Testing

```bash
# Test Report Generation
1. Navigate to /info/reports/stock-card
2. Select date range
3. Select product
4. Generate report
5. Verify data accuracy

# Test Export Functionality
1. Generate any report
2. Click export button
3. Verify CSV download
4. Check file format and data
```

### 🎨 UI/UX Testing

| Test | Steps | Expected |
|-------|--------|----------|
| **Responsive Design** | 1. Test on mobile (320px+)<br>2. Test tablet (768px+)<br>3. Test desktop (1024px+) | Layout adapts properly |
| **Dark Mode** | 1. Toggle theme if available | UI updates correctly |
| **Loading States** | 1. Perform slow operation | Loading indicator shows |
| **Error Handling** | 1. Trigger validation error | User-friendly error message |
| **Accessibility** | 1. Navigate with keyboard<br>2. Test screen reader compatibility | Accessible to all users |

---

## 🤖 Automated Testing

### Running PHPUnit Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php

# Run specific test method
./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php --filter testCreateCashSale

# Run with coverage
./vendor/bin/phpunit --coverage-html=build/logs/html

# Run test groups
./vendor/bin/phpunit --group database
./vendor/bin/phpunit --group auth
```

### Available Test Suites

| Test Suite | Description | Command |
|------------|-------------|---------|
| **Auth Tests** | Authentication & authorization | `./vendor/bin/phpunit tests/Feature/AuthIntegrationTest.php` |
| **Sales Tests** | Sales transaction flows | `./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php` |
| **Purchase Tests** | Purchase order flows | `./vendor/bin/phpunit tests/Feature/PurchaseIntegrationTest.php` |
| **Inventory Tests** | Stock management | `./vendor/bin/phpunit tests/Feature/InventoryIntegrationTest.php` |
| **Financial Tests** | Payments & reports | `./vendor/bin/phpunit tests/Feature/FinancialIntegrationTest.php` |
| **Notification Tests** | Notification system | `./vendor/bin/phpunit tests/Feature/NotificationIntegrationTest.php` |
| **Dashboard Tests** | Dashboard functionality | `./vendor/bin/phpunit tests/Feature/DashboardIntegrationTest.php` |

### Writing New Tests

```php
<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;

class ExampleTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed required data
        $this->seed(\App\Database\Seeds\UserSeeder::class);
        
        // Login if needed
        $this->login('admin@example.com', 'password123');
    }
    
    public function testExampleFeature()
    {
        // Arrange: Setup test data
        
        // Act: Perform action
        $response = $this->get('/example-endpoint');
        
        // Assert: Verify results
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContains('Expected Text', $response->getBody());
    }
}
```

---

## 📊 Testing Reports

### Coverage Reports

```bash
# Generate HTML coverage report
./vendor/bin/phpunit --coverage-html=build/logs/html

# View report
open build/logs/html/index.html
```

### Test Documentation

All test documentation is maintained in:
- `tests/Feature/` - Feature/integration tests
- `tests/Unit/` - Unit tests
- `build/logs/` - Test reports and coverage

---

## 🐛 Bug Reporting

### Bug Report Template

```markdown
## Bug Description
Brief description of the issue

## Environment
- OS: [e.g., Windows 10, Ubuntu 22.04]
- PHP Version: [e.g., 8.2.10]
- Browser: [e.g., Chrome 120, Firefox 119]
- Database: [e.g., MySQL 8.0]

## Steps to Reproduce
1. Go to...
2. Click on...
3. See error...

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Screenshots
Add screenshots if applicable

## Additional Context
Any other relevant information
```

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| **Page Not Found (404)** | Check `.htaccess` and Apache `mod_rewrite` |
| **Database Connection Error** | Verify database credentials in `.env` |
| **Session Issues** | Ensure `writable/` folder has proper permissions |
| **Missing Dependencies** | Run `composer install` |
| **Test Failures** | Check database migrations and seeders |

---

## 📝 Testing Checklist

### Pre-Release Testing

- [ ] All authentication flows work
- [ ] CRUD operations for all master data
- [ ] Transaction flows (sales, purchases, returns)
- [ ] Notification system functional
- [ ] Reports generate correctly
- [ ] Responsive design on all devices
- [ ] Data validation working
- [ ] Error handling graceful
- [ ] Performance acceptable
- [ ] Security measures in place

### Regression Testing

- [ ] Existing features still work
- [ ] No new breaking changes
- [ ] Database migrations successful
- [ ] API endpoints responding
- [ ] UI elements rendering correctly

---

## 🔔 Notification System Testing

### Test Notification Features

| Feature | Test Steps | Expected Result |
|--------|------------|-----------------|
| **Notification Badge** | 1. Login with admin@example.com<br>2. Check header bell icon | Badge shows unread count |
| **Notification Dropdown** | 1. Click bell icon<br>2. View notifications | List of notifications with icons |
| **Mark as Read** | 1. Click notification item<br>2. Refresh page | Item marked as read |
| **Notification Settings** | 1. Go to /settings<br>2. Toggle notification types<br>3. Save changes | Settings persisted |
| **Real-time Updates** | 1. Keep page open<br>2. Create low stock item<br>3. Wait 30 seconds | New notification appears |

### Notification Test Data

```bash
# Seed notifications for testing
php spark db:seed NotificationSeeder

# Check notification count API
curl -X GET http://localhost:8080/notifications/getUnreadCount \
     -H "X-Requested-With: XMLHttpRequest" \
     -H "Cookie: ci_session=YOUR_SESSION_ID"

# Get recent notifications API
curl -X GET http://localhost:8080/notifications/getRecent \
     -H "X-Requested-With: XMLHttpRequest" \
     -H "Cookie: ci_session=YOUR_SESSION_ID"
```

### Hidden Transaction Testing (Owner Only)

```bash
# Login as owner
# Username: owner@example.com
# Password: password123

# Access daily report with hidden transactions
http://localhost:8080/info/reports/daily?include_hidden=1

# Test CSV export with hidden transactions
curl -X GET "http://localhost:8080/info/reports/daily?include_hidden=1&export=csv" \
     -H "X-Requested-With: XMLHttpRequest"
```

## 🔍 Debug Tools

### CodeIgniter Debug Toolbar

Enable in `.env`:
```env
CI_ENVIRONMENT = development
```

### Logging

```php
// Log to file
log_message('error', 'Error message: ' . $errorMessage);

// Log query results
log_message('info', 'Query result: ' . json_encode($result));
```

### Database Debug

```bash
# Enable query logging
# In Config/Database.php:
# 'DBDebug' => true,

# Check last queries
# In controller: echo $this->db->getLastQuery()->getQuery();
```

---

## 🚀 Performance Testing

### Load Testing Tools

- **Apache Benchmark (ab)**: Simple load testing
- **JMeter**: Advanced performance testing
- **Lighthouse**: Web performance audit

### Example Load Test

```bash
# Simple load test with Apache Benchmark
ab -n 100 -c 10 http://localhost:8080/dashboard
```

---

## 📚 Additional Resources

- **CodeIgniter Testing Guide**: https://codeigniter.com/user_guide/testing/
- **PHPUnit Documentation**: https://phpunit.de/documentation.html
- **Postman Testing**: Use provided collection in `docs/api/`
- **Browser DevTools**: F12 for debugging

---

**🎯 Happy Testing!**

Remember: Good testing prevents production bugs and ensures a smooth user experience.