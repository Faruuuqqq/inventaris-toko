# Test Plans for Critical Paths

## 1. Sales - Cash Transaction

### Test: createCashSales_ShouldSucceed_WithValidData
- Given: Customer exists, product has sufficient stock
- When: Create cash sale with valid data
- Then: 
  - Sale record created
  - Invoice number generated
  - Stock deducted correctly
  - Paid amount equals total amount

### Test: createCashSales_ShouldFail_WhenInsufficientStock
- Given: Product stock = 10, trying to sell qty = 15
- When: Create cash sale
- Then: Throw InsufficientStockException

## How to Run Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Feature/SalesTest.php
```
