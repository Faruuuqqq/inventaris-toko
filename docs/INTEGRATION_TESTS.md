# 🧪 Integration Tests for TokoManager

## 📋 **Setup Instructions**

### 1. Install Dependencies
```bash
# Install testing dependencies if not already installed
composer install --dev
```

### 2. Create Test Database
```bash
# Create test database (MySQL)
mysql -u root -p -e "CREATE DATABASE inventaris_test;"

# Or configure in phpunit.xml
```

### 3. Seed Test Data
```bash
# Run test-specific seeders
php spark db:seed DatabaseSeeder

# This creates:
# - 3 Users (admin, user, sales)
# - 10 Products with different stock levels
# - 3 Customers with credit limits
# - 3 Suppliers with payment terms
# - 3 Warehouses
```

### 4. Run Tests
```bash
# Run all integration tests
./vendor/bin/phpunit --group integration

# Run specific test suite
./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php

# Run with coverage
./vendor/bin/phpunit --group integration --coverage-html=build/logs/html
```

---

## 🧪 **Integration Test Suites**

### 1. **Sales Integration Test** (`SalesIntegrationTest.php`)

**Tests Covered:**
- ✅ Cash sale with stock deduction
- ✅ Credit sale with payment tracking
- ✅ Stock availability validation
- ✅ Sales pagination and filtering
- ✅ Sale status updates and history
- ✅ Receipt data generation

**Key Assertions:**
- Stock deducts correctly on sales
- Credit limits enforced
- Journal entries created for financial integrity
- No negative stock allowed

### 2. **Purchase Integration Test** (`PurchaseIntegrationTest.php`)

**Tests Covered:**
- ✅ Purchase order creation workflow
- ✅ Goods receiving with stock update
- ✅ Purchase returns with stock adjustment
- ✅ Supplier credit limit validation
- ✅ Purchase order filtering
- ✅ Purchase metrics calculation

**Key Assertions:**
- No stock update until goods received
- Stock mutations recorded properly
- Supplier credit limits enforced
- Accurate total calculations

### 3. **Inventory Integration Test** (`InventoryIntegrationTest.php`)

**Tests Covered:**
- ✅ Stock transfer between warehouses
- ✅ Stock adjustments with reasons
- ✅ Negative stock prevention
- ✅ Inventory valuation calculation
- ✅ Low stock reporting
- ✅ Complete stock history tracking

**Key Assertions:**
- Stock mutations recorded for all movements
- Transfer creates in/out mutations
- Adjustments properly documented
- Inventory value accurately calculated

### 4. **Financial Integration Test** (`FinancialIntegrationTest.php`)

**Tests Covered:**
- ✅ Journal entries for cash sales
- ✅ Credit sale with payment terms
- ✅ Customer payment processing
- ✅ Financial statement generation
- ✅ Trial balance validation
- ✅ Supplier payment handling
- ✅ Duplicate journal prevention

**Key Assertions:**
- Double-entry accounting maintained
- Trial balance always balances
- Receivables/Payables tracked
- No duplicate journal entries

### 5. **Dashboard Integration Test** (`DashboardIntegrationTest.php`)

**Tests Covered:**
- ✅ Sales metrics display
- ✅ Inventory summary
- ✅ Sales trend data
- ✅ Top selling products
- ✅ Financial overview
- ✅ Real-time notifications

**Key Assertions:**
- Accurate aggregation of data
- Proper sorting and ranking
- Trend calculations correct
- Notifications triggered appropriately

### 6. **Authentication Integration Test** (`AuthIntegrationTest.php`)

**Tests Covered:**
- ✅ Valid credential authentication
- ✅ Invalid credential rejection
- ✅ Login input validation
- ✅ User logout functionality
- ✅ Protected route access control
- ✅ Role-based permissions
- ✅ Password reset flow
- ✅ Session timeout handling
- ✅ Brute force prevention
- ✅ Concurrent session limit

**Key Assertions:**
- Session properly managed
- Invalid sessions rejected
- Role permissions enforced
- Security measures working

---

## 🔍 **Test Database Schema**

Tables used by integration tests:
- `users` - User authentication and roles
- `products` - Product catalog with stock
- `customers` - Customer data with credit limits
- `suppliers` - Supplier data with terms
- `warehouses` - Warehouse locations
- `sales` - Sales transactions
- `sale_details` - Sale line items
- `purchase_orders` - Purchase orders
- `purchase_order_details` - Purchase line items
- `stock_mutations` - All stock movements
- `journal_entries` - Financial journal
- `customer_receivables` - Customer debt tracking
- `supplier_payables` - Supplier debt tracking

---

## 📊 **Coverage Areas**

### Business Logic Coverage:
- ✅ **Sales Flow**: Quote → Order → Payment → Delivery
- ✅ **Purchase Flow**: PO → Receive → Pay
- ✅ **Inventory Flow**: Transfer → Adjust → Report
- ✅ **Financial Flow**: Transaction → Journal → Report
- ✅ **Authentication**: Login → Session → Logout

### Edge Cases Covered:
- ✅ Insufficient stock scenarios
- ✅ Credit limit exceeded
- ✅ Concurrent sessions
- ✅ Invalid data submissions
- ✅ Financial integrity violations

### Data Integrity Tests:
- ✅ **ACID Compliance**: All financial operations in transactions
- ✅ **Referential Integrity**: Foreign key constraints
- ✅ **Business Rules**: Credit limits, stock levels
- ✅ **Audit Trail**: All changes logged

---

## 🚀 **Running Tests Locally**

### Quick Test Commands:
```bash
# Test specific flow
./vendor/bin/phpunit tests/Feature/SalesIntegrationTest.php::it_can_create_cash_sale_with_stock_deduction

# Test with verbose output
./vendor/bin/phpunit --group integration --verbose

# Generate coverage report
./vendor/bin/phpunit --group integration --coverage-text

# Watch for changes
./vendor/bin/phpunit --group integration --repeat 10
```

### Test Configuration:
- **Test Database**: `inventaris_test`
- **Base URL**: `http://localhost:8000`
- **Cache**: File-based for tests
- **Environment**: Testing mode

---

## 📝 **Best Practices Demonstrated**

1. **Transaction Safety**: All financial operations wrapped in transactions
2. **Input Validation**: Comprehensive validation before processing
3. **Error Handling**: Proper exception handling and logging
4. **Data Integrity**: Referential integrity maintained
5. **Security**: SQL injection and XSS prevention
6. **Performance**: Efficient queries with proper indexing
7. **Maintainability**: Clean, readable, well-structured code

---

## ✅ **Success Criteria**

All integration tests pass when:
- 🧪 Database is properly seeded
- 🔑 Test credentials work
- 📊 All test data available
- 🔒 Security measures enforced
- 💰 Financial integrity maintained
- 📦 Inventory accurately tracked
- 👥 User authentication works
- 📈 Dashboard data displays correctly

Run `./vendor/bin/phpunit --group integration` to verify! 🚀