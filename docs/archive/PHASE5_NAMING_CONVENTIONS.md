# PHASE 5: NAMING CONVENTIONS & BEST PRACTICES

## Overview
This document establishes and documents the naming conventions used throughout the Inventaris Toko application.

## URL Naming Convention

### Primary Pattern: kebab-case
All URLs should use lowercase letters with hyphens to separate words.

**Correct Examples:**
```
/master/products
/info/history/sales-returns
/info/history/sales-returns-data
/finance/expenses/delete
/transactions/sales-returns
/info/stock/get-mutations
```

**Incorrect Examples:**
```
✗ /info/history/salesReturns (camelCase)
✗ /info/history/sales_returns (snake_case)
✗ /master/Products (uppercase)
✗ /master/product_list (snake_case)
```

## PHP Method Naming Convention

### Primary Pattern: camelCase
All PHP methods should use camelCase format.

**Correct Examples:**
```php
public function salesReturnsData()
public function purchaseReturnsData()
public function paymentsReceivableData()
public function getMutations()
public function getSupplierPurchases()
```

**Incorrect Examples:**
```php
✗ public function salesReturnData() // singular inconsistent
✗ public function sales_returns_data() // snake_case
✗ public function SalesReturnsData() // PascalCase
```

## Route Group Naming

### Pattern: kebab-case
All route groups should use kebab-case.

**Examples:**
```php
$routes->group('sales-returns', function($routes) { ... });
$routes->group('purchase-returns', function($routes) { ... });
$routes->group('delivery-note', function($routes) { ... });
$routes->group('kontra-bon', function($routes) { ... });
```

## AJAX Endpoint Naming

### Pattern: kebab-case + Data suffix or Action name
For AJAX endpoints that return data:

**Data Endpoints:**
```
/info/history/sales-returns-data
/info/history/purchase-returns-data
/info/history/payments-receivable-data
/info/history/payments-payable-data
/finance/expenses/get-data
/master/customers/getList
```

**Action Endpoints:**
```
/info/stock/getMutations
/transactions/sales/getProducts
/finance/payments/getSupplierPurchases
```

## Model/Class Naming

### Pattern: PascalCase (StudlyCaps)

**Examples:**
```php
class SalesReturn { }
class PurchaseReturn { }
class FileController { }
class StockController { }
```

## Database Table Naming

### Pattern: snake_case (plural)

**Examples:**
```
sales_returns
purchase_returns
payment_methods
expense_categories
stock_movements
```

## Database Column Naming

### Pattern: snake_case

**Examples:**
```
sales_id
customer_name
payment_date
created_at
updated_at
```

## Variable Naming

### Pattern: camelCase

**Examples:**
```javascript
let customerName = 'John Doe';
let paymentAmount = 100000;
let isApproved = true;
let tableData = [];
```

## CSS Class Naming

### Pattern: kebab-case

**Examples:**
```html
<div class="sales-returns-table">
<button class="btn-primary">
<span class="error-message">
<div class="modal-backdrop">
```

## Constants Naming

### Pattern: UPPER_SNAKE_CASE

**Examples:**
```php
const MAX_UPLOAD_SIZE = 10485760; // 10MB
const DEFAULT_CURRENCY = 'IDR';
const PAYMENT_STATUS_PENDING = 'PENDING';
```

## File Naming

### Controllers
- **Pattern**: PascalCase
- **Examples**: `SalesReturn.php`, `PurchaseReturn.php`, `FileController.php`

### Models
- **Pattern**: PascalCase
- **Examples**: `Customer.php`, `Supplier.php`, `Expense.php`

### Views
- **Pattern**: kebab-case or snake_case (directory structure)
- **Examples**: `return-sales.php`, `payments-receivable.php`

### Migration Files
- **Pattern**: snake_case with timestamp
- **Examples**: `2026_02_03_create_sales_returns_table.php`

## Fixed Issues (Post-Audit)

### Before Fixes
These endpoints were using camelCase instead of kebab-case:

| View File | Old Endpoint | New Endpoint |
|-----------|--------------|--------------|
| return-sales.php | `/info/history/salesReturnsData` | `/info/history/sales-returns-data` |
| return-purchases.php | `/info/history/purchaseReturnsData` | `/info/history/purchase-returns-data` |
| payments-receivable.php | `/info/history/paymentsReceivableData` | `/info/history/payments-receivable-data` |
| payments-payable.php | `/info/history/paymentsPayableData` | `/info/history/payments-payable-data` |
| expenses.php | `/info/history/expensesData` | `/info/history/expenses-data` |

### Why This Matters
- **Consistency**: Makes the codebase more predictable
- **SEO**: Kebab-case is preferred for URLs
- **Readability**: Easier to understand URL structure
- **REST Convention**: Follows RESTful API best practices

## Summary Table

| Entity Type | Pattern | Example |
|-------------|---------|---------|
| URLs | kebab-case | `/info/sales-returns-data` |
| PHP Methods | camelCase | `salesReturnsData()` |
| Classes | PascalCase | `SalesReturn` |
| Database Tables | snake_case | `sales_returns` |
| Database Columns | snake_case | `customer_name` |
| Variables | camelCase | `customerName` |
| CSS Classes | kebab-case | `sales-returns-table` |
| Constants | UPPER_SNAKE_CASE | `MAX_UPLOAD_SIZE` |

## Enforcement

### Pre-commit Hooks
Consider implementing pre-commit hooks to enforce these conventions.

### Code Review
All code reviews should check for naming convention compliance.

### Documentation
Keep this document updated as new conventions are established.

---

**Document Version**: 1.0  
**Last Updated**: February 3, 2026  
**Status**: ✅ Complete
