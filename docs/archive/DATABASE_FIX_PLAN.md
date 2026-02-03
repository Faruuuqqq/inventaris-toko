# DATABASE FIX & IMPROVEMENT PLAN
## Inventaris Toko Application

**Document Version:** 1.0
**Date:** Current Session
**Status:** Analysis Complete - Ready for Implementation

---

## 📋 EXECUTIVE SUMMARY

### Current Status
- ✅ Database structure: 28 tables with proper foreign keys
- ✅ Migrations: 6 well-organized migration files
- ✅ Models: 25+ models for all tables
- ❌ Issues Found: 8 critical issues to fix
- ❌ Data Integrity: Several constraints and query issues

### Issues Identified
1. **Model Query Issues** - Wrong field names in queries
2. **Foreign Key Constraints** - Some may conflict during operations
3. **Database Configuration** - Needs verification
4. **Data Type Inconsistencies** - Between related tables
5. **Missing Indexes** - Performance optimization needed
6. **Soft Delete Handling** - Inconsistent usage
7. **Timestamp Issues** - Missing updatedField in some models
8. **Data Validation** - No pre-insert validation

---

## 🔍 DETAILED ISSUE ANALYSIS

### CRITICAL ISSUES (Must Fix)

#### 1. **SaleModel - Wrong Field References**
**Location:** `app/Models/SaleModel.php` (Lines 45, 60)
**Issue:** References `date` field but table has `created_at`
```php
// WRONG (Line 45)
return $builder->orderBy('date', 'DESC')->findAll();

// WRONG (Line 60)
return $builder->orderBy('date', 'ASC')->findAll();
```
**Impact:** Queries will fail when trying to sort sales
**Fix Required:** Replace `date` with `created_at`

---

#### 2. **Database Connection Configuration**
**Location:** `app/Config/Database.php` (Lines 27-52)
**Issue:** Config array is empty, relies entirely on .env
```php
// Config is empty:
'hostname' => '',
'username' => '',
'password' => '',
'database' => '',
```
**Impact:** If .env file is missing/corrupted, database won't connect
**Fix Required:** 
- Add fallback values in Database.php
- Verify .env is properly loaded
- Add connection validation in bootstrap

---

#### 3. **Missing Timestamp Fields**
**Location:** Multiple models
**Issue:** Some models have `useTimestamps = false` but need timestamps
**Models Affected:**
- `StockMutationModel` - Needs created_at for audit trail
- `PaymentModel` - Needs timestamps for transaction history
- Others missing `updatedField` configuration

**Fix Required:** 
- Add `updated_at` field to migrations
- Enable timestamps in all models
- Ensure all timestamp fields are DATETIME

---

#### 4. **Data Type Inconsistency**
**Issue:** Foreign key fields don't match parent table types
**Example:** 
- `customers.id` = BIGINT UNSIGNED
- `sales.customer_id` = BIGINT UNSIGNED ✓ (correct)
- But some other references may be INT instead of BIGINT

**Fix Required:**
- Audit all foreign keys
- Ensure all match parent table primary key types
- Update migrations if needed

---

#### 5. **Soft Delete Implementation**
**Location:** Multiple migration files
**Issue:** 
- `CreateInitialTables.php` sets `useSoftDeletes = false` 
- But some models may need soft deletes for audit
- Inconsistent approach across models

**Models to Check:**
- SaleModel
- PurchaseOrderModel
- ExpenseModel
- CustomerModel

**Fix Required:**
- Decide soft delete strategy (all or none)
- Add `deleted_at` column to important tables
- Update all models consistently

---

#### 6. **Missing Indexes for Performance**
**Issue:** Some frequently queried fields lack indexes
**Missing Indexes:**
- `sales.created_at` (for date range queries)
- `sales.payment_status` (for filtering)
- `customers.code` (already has index ✓)
- `stock_mutations.created_at` (for history)
- `products.sku` (already has index ✓)

**Fix Required:**
- Create new migration for performance indexes
- Add composite indexes for common queries
- Monitor query performance

---

#### 7. **Cascade Delete Risks**
**Issue:** Several CASCADE DELETE foreign keys could cause data loss
**Risky Relationships:**
```
sales → CASCADE → sale_items
purchase_orders → CASCADE → purchase_order_items
customers → CASCADE → sales (DANGER: Deletes all sales!)
suppliers → CASCADE → purchase_orders (DANGER!)
```

**Fix Required:**
- Change risky cascades to SET NULL
- Add soft deletes instead of hard deletes
- Create audit trail before deletion

---

#### 8. **No Pre-Insert Data Validation**
**Issue:** Models don't validate data before INSERT/UPDATE
**Missing Validations:**
- Customer credit limit vs. new sale amount
- Product stock before sales
- Duplicate invoice numbers
- Invalid date ranges

**Fix Required:**
- Add validation rules to all models
- Implement custom validators
- Add pre-save hooks

---

### HIGH PRIORITY ISSUES

#### 9. **Missing kontra_bon_id Foreign Key in Some Tables**
**Issue:** `sales` table has `kontra_bon_id` but migration may have ordering issue
**Status:** Verify order of table creation

#### 10. **Enum Field Limitations**
**Tables Using ENUM:**
- `users.role` - ['OWNER', 'ADMIN', 'GUDANG', 'SALES']
- `sales.payment_type` - ['CASH', 'CREDIT']
- `sales.payment_status` - ['PAID', 'UNPAID', 'PARTIAL']
- `purchase_orders.status` - Multiple statuses

**Issue:** Can't easily add new roles/statuses without migration
**Fix Required:**
- Consider separate lookup tables for status enums
- Create reference tables instead of ENUM

#### 11. **Stock Mutation Tracking**
**Issue:** `stock_mutations` table may not properly track all movements
**Missing Fields:**
- `reference_type` - What triggered the mutation (SALES, PURCHASE, ADJUSTMENT)
- `user_id` - Who made the mutation
- `reason` - Why the mutation occurred

#### 12. **Payment Reconciliation**
**Issue:** No way to track partial payments or payment installments
**Missing:**
- Payment history per sale
- Multiple payment records per invoice
- Payment method tracking
- Bank reconciliation fields

---

## 📊 ISSUE SEVERITY MATRIX

| Issue | Severity | Impact | Effort | Status |
|-------|----------|--------|--------|--------|
| SaleModel date field | 🔴 CRITICAL | Queries fail | 5 min | FIX |
| Database config empty | 🔴 CRITICAL | Connection fails | 10 min | FIX |
| Missing timestamps | 🔴 CRITICAL | Audit trail lost | 30 min | FIX |
| Data type mismatch | 🔴 CRITICAL | FK fails | 20 min | AUDIT |
| Soft delete inconsistent | 🟠 HIGH | Data loss risk | 2 hrs | DESIGN |
| Missing indexes | 🟠 HIGH | Slow queries | 30 min | ADD |
| Cascade delete risks | 🟠 HIGH | Data loss | 2 hrs | REDESIGN |
| No validation | 🟠 HIGH | Bad data | 4 hrs | IMPLEMENT |
| Enum limitations | 🟡 MEDIUM | Hard to extend | 2 hrs | REFACTOR |
| Stock tracking incomplete | 🟡 MEDIUM | Accuracy issues | 1 hr | ENHANCE |
| Payment tracking | 🟡 MEDIUM | Incomplete records | 3 hrs | ENHANCE |

---

## 🛠️ IMPLEMENTATION PLAN

### PHASE 1: CRITICAL FIXES (1-2 hours)

#### 1.1 Fix SaleModel Query Issues
```bash
File: app/Models/SaleModel.php
- Line 45: Change 'date' → 'created_at'
- Line 60: Change 'date' → 'created_at'
Commit: [FIX] Correct field references in SaleModel
```

#### 1.2 Verify Database Configuration
```bash
File: app/Config/Database.php
- Add fallback hostname: localhost
- Add fallback username: root
- Add fallback password: (empty)
- Add fallback database: inventaris_toko
- Add validation method to check connection
Commit: [FIX] Add database config fallback values
```

#### 1.3 Add Missing Timestamps
```bash
Files to Update:
- app/Database/Migrations/2026-02-02-000000_AddPhase4RequiredColumns.php
  (Add updated_at to all tables)
- app/Models/StockMutationModel.php (enable timestamps)
- app/Models/PaymentModel.php (enable timestamps)
Commit: [FIX] Add timestamp fields to all models
```

---

### PHASE 2: DATA INTEGRITY AUDIT (30-45 min)

#### 2.1 Audit Foreign Key Data Types
```bash
Script: Create audit script to check:
- All BIGINT UNSIGNED references match parent tables
- No orphaned records exist
- Constraint violations reported
File: scripts/audit_foreign_keys.php
```

#### 2.2 Verify Relationship Integrity
```bash
Checks:
- No sales without valid customer_id
- No sale_items without valid sale_id
- No purchase orders without valid supplier_id
- Stock movements properly tracked
```

#### 2.3 Check for Orphaned Records
```bash
Commands:
SELECT sales.* FROM sales 
LEFT JOIN customers ON sales.customer_id = customers.id 
WHERE customers.id IS NULL;

(Repeat for all FKs)
```

---

### PHASE 3: PERFORMANCE OPTIMIZATION (1 hour)

#### 3.1 Add Performance Indexes
```sql
-- Migration: 2026-02-03-000000_AddPerformanceIndexes.php

-- Sales table
ALTER TABLE sales ADD INDEX idx_created_at (created_at);
ALTER TABLE sales ADD INDEX idx_payment_status (payment_status);
ALTER TABLE sales ADD INDEX idx_customer_created (customer_id, created_at);

-- Stock mutations
ALTER TABLE stock_mutations ADD INDEX idx_created_at (created_at);
ALTER TABLE stock_mutations ADD INDEX idx_product_warehouse (product_id, warehouse_id);

-- Customers
ALTER TABLE customers ADD INDEX idx_name (name);
ALTER TABLE customers ADD INDEX idx_code (code);

-- Products
ALTER TABLE products ADD INDEX idx_name (name);
ALTER TABLE products ADD INDEX idx_sku (sku);

Commit: [PERF] Add performance indexes for common queries
```

---

### PHASE 4: SOFT DELETE IMPLEMENTATION (2 hours)

#### 4.1 Design Soft Delete Strategy
**Decision:** Implement soft deletes for master data only
```
Apply soft deletes to:
- customers (to preserve sales history)
- suppliers (to preserve purchase history)
- products (to preserve stock history)
- salespersons (to preserve sales attribution)
- users (to preserve audit trail)

DO NOT soft delete:
- sales/purchases (transaction records)
- stock_mutations (audit trail)
- payments (financial records)
```

#### 4.2 Create Soft Delete Migration
```bash
Migration: 2026-02-03-100000_AddSoftDeleteColumns.php
- Add deleted_at to: customers, suppliers, products, salespersons, users
- Create helper scope: withTrashed(), onlyTrashed()
```

#### 4.3 Update Models
```bash
Models to update:
- CustomerModel.php
- SupplierModel.php
- ProductModel.php
- SalespersonModel.php
- UserModel.php

Add to each:
protected $useSoftDeletes = true;
protected $deletedField = 'deleted_at';
```

---

### PHASE 5: REDUCE CASCADE DELETE RISKS (1.5 hours)

#### 5.1 Identify Risk Areas
```
HIGH RISK:
- customers → sales (CASCADE)
  Change to: SET NULL + soft delete
- suppliers → purchase_orders (CASCADE)
  Change to: SET NULL + soft delete
- users → sales (CASCADE)
  Change to: SET NULL (users should never be deleted)

LOW RISK (Keep CASCADE):
- sales → sale_items (OK)
- purchase_orders → items (OK)
```

#### 5.2 Create Migration to Fix
```bash
Migration: 2026-02-03-110000_FixCascadeDeleteConstraints.php

1. Drop existing foreign keys
2. Re-create with SET NULL where appropriate
3. Create backup trigger before cascade deletes

Commit: [FIX] Replace risky CASCADE deletes with SET NULL
```

---

### PHASE 6: ADD DATA VALIDATION (3-4 hours)

#### 6.1 Model Validation Rules
```php
// app/Models/SaleModel.php
protected $validationRules = [
    'invoice_number' => 'required|is_unique[sales.invoice_number]',
    'customer_id' => 'required|is_not_empty',
    'warehouse_id' => 'required|is_not_empty',
    'total_amount' => 'required|greater_than[0]',
    'due_date' => 'required_if[payment_type,CREDIT]|valid_date',
];

protected $validationMessages = [
    'invoice_number' => [
        'is_unique' => 'Nomor invoice {value} sudah ada'
    ],
];

// Repeat for all models
```

#### 6.2 Custom Validators
```php
// app/Validation/Validators.php
class Validators {
    public static function validateCustomerCreditLimit($value, $data) {
        // Verify customer has enough credit limit
    }
    
    public static function validateStockAvailable($value, $data) {
        // Verify product stock available
    }
    
    public static function validateDateRange($value, $startDate) {
        // Validate date range constraints
    }
}
```

#### 6.3 Pre-save Hooks
```php
// In each model
protected $beforeInsert = ['validateBeforeInsert'];
protected $beforeUpdate = ['validateBeforeUpdate'];

protected function validateBeforeInsert(array $data) {
    // Custom validation logic
    return $data;
}
```

---

### PHASE 7: ENHANCE DATA TRACKING (2-3 hours)

#### 7.1 Improve Stock Mutations
```bash
Add columns to stock_mutations:
- reference_type: ENUM ('SALES', 'PURCHASE', 'ADJUSTMENT', 'RETURN')
- reference_id: BIGINT (ID of sale/purchase/adjustment)
- user_id: BIGINT (Who made the mutation)
- reason: TEXT (Why the mutation occurred)
- is_approved: BOOLEAN (For approval workflows)

This creates complete audit trail.
```

#### 7.2 Implement Payment History
```bash
Create: payment_details table
- id (PK)
- payment_id (FK to payments)
- payment_date
- amount
- method (CASH, CHECK, TRANSFER)
- reference_number
- notes

Allows tracking of partial/multiple payments per invoice.
```

#### 7.3 Add Audit Fields to All Tables
```bash
Add to every table:
- created_by: user_id
- updated_by: user_id
- change_log: JSON field for tracking changes

Create: BaseModel with automatic audit
```

---

### PHASE 8: REPLACE ENUM WITH LOOKUP TABLES (1.5 hours)

#### 8.1 Create Lookup Tables
```bash
New tables:
- user_roles (id, code, name, description)
- payment_statuses (id, code, name, description)
- payment_types (id, code, name, description)
- stock_mutation_types (id, code, name, description)
```

#### 8.2 Update Foreign Keys
```bash
Replace:
payment_type ENUM → payment_type_id (FK to payment_types)
payment_status ENUM → payment_status_id (FK to payment_statuses)

Benefits:
- Easy to add new statuses
- Translatable status names
- Auditable status changes
```

---

## 📋 IMPLEMENTATION CHECKLIST

### Week 1: Critical Fixes
- [ ] Fix SaleModel date field references
- [ ] Verify database configuration
- [ ] Add missing timestamp fields
- [ ] Audit foreign key relationships
- [ ] Check for orphaned records

### Week 2: Optimization & Security
- [ ] Add performance indexes
- [ ] Implement soft deletes for master data
- [ ] Fix cascade delete constraints
- [ ] Add data validation rules
- [ ] Create custom validators

### Week 3: Enhancement & Audit
- [ ] Improve stock mutation tracking
- [ ] Implement payment history
- [ ] Add audit trail fields
- [ ] Replace ENUM with lookup tables
- [ ] Create audit scripts

---

## 🧪 TESTING STRATEGY

### Unit Tests to Create
```bash
tests/
├── Unit/Models/
│   ├── SaleModelTest.php
│   ├── CustomerModelTest.php
│   ├── ProductModelTest.php
│   └── StockMutationModelTest.php
├── Unit/Validation/
│   ├── SaleValidationTest.php
│   └── CustomValidatorsTest.php
└── Database/
    ├── ForeignKeyTest.php
    └── DataIntegrityTest.php
```

### Integration Tests
```bash
tests/Feature/
├── SalesTransactionTest.php
├── PurchaseTransactionTest.php
├── StockMovementTest.php
└── PaymentReconciliationTest.php
```

### Database Tests
```php
// Verify foreign key constraints work
// Verify soft deletes function properly
// Verify cascade operations work as intended
// Verify indexes improve query performance
```

---

## 📊 VERIFICATION CHECKLIST

After each phase, verify:
- [ ] All tests pass
- [ ] No database errors in logs
- [ ] Foreign keys valid
- [ ] Data integrity maintained
- [ ] Performance improved
- [ ] No orphaned records
- [ ] Audit trail working
- [ ] Soft deletes functioning
- [ ] Validation preventing bad data
- [ ] Migrations reversible

---

## 🚀 DEPLOYMENT PLAN

### Pre-Deployment
1. Backup production database
2. Run audit scripts
3. Generate migration rollback plan
4. Test all migrations on dev/staging
5. Verify zero data loss

### Deployment Steps
1. Run Phase 1 migrations (critical fixes)
2. Verify application still works
3. Run Phase 2-8 migrations in order
4. Run data validation/cleanup queries
5. Monitor logs for errors
6. Verify all functionality

### Post-Deployment
1. Check application logs
2. Verify data integrity
3. Run performance tests
4. Check audit trails working
5. Monitor query performance
6. Get user feedback

---

## 🔍 MONITORING & MAINTENANCE

### Regular Checks
```bash
# Weekly: Check for orphaned records
SELECT COUNT(*) FROM sales WHERE customer_id NOT IN (SELECT id FROM customers);

# Monthly: Index usage statistics
SELECT object_name, stat_name, stat_value FROM mysql.innodb_index_stats;

# Quarterly: Data validation report
Run complete audit suite
```

### Performance Monitoring
- Monitor query execution times
- Track index usage
- Monitor table sizes
- Alert on slow queries
- Review query logs

---

## 📝 DOCUMENTATION UPDATES

After fixing database:
1. Update ER diagram
2. Document all relationships
3. Create data dictionary
4. Document validation rules
5. Create troubleshooting guide
6. Update API documentation

---

## 💾 ROLLBACK PROCEDURES

Each migration should have:
```php
public function down() {
    // Reverse the changes
    // Restore from backup if needed
}
```

Create rollback script:
```bash
scripts/rollback_database.php
- Identifies last successful migration
- Rolls back one or more migrations
- Verifies data integrity after rollback
```

---

## 🎯 SUCCESS CRITERIA

✅ All database issues fixed
✅ All tests passing (>95% coverage)
✅ Zero orphaned records
✅ Foreign keys validated
✅ Performance improved (queries <100ms)
✅ Soft deletes working
✅ Audit trail complete
✅ No validation bypasses
✅ Documentation updated
✅ Zero breaking changes to API

---

## 📞 NEXT STEPS

1. **Review this plan** with the team
2. **Prioritize issues** based on impact
3. **Create implementation tickets** for each phase
4. **Schedule implementation** (estimated 2-3 weeks)
5. **Begin Phase 1: Critical Fixes** (1-2 hours)

Ready to start implementing? Let me know which phase to begin with!

---

**Plan Version:** 1.0
**Estimated Total Time:** 2-3 weeks
**Team Size:** 1-2 developers
**Risk Level:** Medium (with proper backup)
**Testing Coverage:** 90%+ recommended
