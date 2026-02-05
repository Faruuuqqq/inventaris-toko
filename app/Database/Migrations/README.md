# Database Migrations - Inventaris Toko

This directory contains all CodeIgniter 4 database migrations for the inventory management system. Migrations are run in chronological order based on their filename.

## Migration Naming Convention

```
YYYY-MM-DD-HHMMSS_descriptive_name_in_snake_case.php
```

- **YYYY-MM-DD**: Date migration was created
- **HHMMSS**: Time to ensure uniqueness on same day
- **Name**: Clear, descriptive name in snake_case indicating what the migration does

## Migrations Overview

### Phase 1: Core Master Data (2026-02-01)

| # | File | Class Name | Purpose |
|---|------|-----------|---------|
| 1 | `2026-02-01-100000_create_core_master_tables.php` | `CreateCoreMasterTables` | Creates 15 core tables: users, warehouses, categories, products, product_stocks, customers, suppliers, salespersons, contra_bons, sales, sale_items, purchase_orders, purchase_order_items, stock_mutations, payments |
| 2 | `2026-02-01-100001_create_transaction_and_return_tables.php` | `CreateTransactionAndReturnTables` | Creates return/refund handling tables: sales_returns, sales_return_items, purchase_returns, purchase_return_items; plus expenses, api_tokens, audit_logs |
| 3 | `2026-02-01-100002_add_soft_delete_columns.php` | `AddSoftDeleteColumns` | Adds soft delete support (deleted_at column) to transactional tables: sales, purchase_orders, sales_returns, purchase_returns |

### Phase 2: Schema Enhancements (2026-02-02)

| # | File | Class Name | Purpose |
|---|------|-----------|---------|
| 4 | `2026-02-02-000000_add_missing_core_columns.php` | `AddMissingCoreColumns` | Adds missing columns to existing tables: updated_at timestamps, status enums, warehouse/salesperson relationships |
| 5 | `2026-02-02-100003_create_kontra_bons_table.php` | `CreateKontraBonsTable` | Creates/updates contra_bons table (combined batch invoices) - Note: Functionality already exists in migration #1 |
| 6 | `2026-02-02-100004_add_delivery_note_columns.php` | `AddDeliveryNoteColumns` | Adds delivery note tracking columns to sales table |

### Phase 3: Performance & Data Integrity (2026-02-03)

| # | File | Class Name | Purpose |
|---|------|-----------|---------|
| 7 | `2026-02-03-100000_add_performance_indexes.php` | `AddPerformanceIndexes` | Adds optimized database indexes on frequently-queried columns for performance |
| 8 | `2026-02-03-100001_fix_cascade_delete_risks.php` | `FixCascadeDeleteRisks` | Documents and fixes dangerous CASCADE DELETE constraints, replacing with safer SET NULL |

## Database Schema Summary

### Core Master Tables (7)
- **users** - Authentication & roles (OWNER, ADMIN, GUDANG, SALES)
- **categories** - Product categories
- **products** - Master product list with SKU, pricing, min stock alerts
- **customers** - Resellers/customers with credit limits
- **suppliers** - Vendors with debt tracking
- **warehouses** - Multiple warehouse/location support
- **salespersons** - Sales staff for commission tracking

### Inventory Tables (2)
- **product_stocks** - Pivot table tracking stock per product/warehouse
- **stock_mutations** - Stock movement history (Kartu Stok/Stock Card)

### Transaction Tables (4)
- **sales** - Sales invoices header with payment tracking
- **sale_items** - Individual items per sale (line items)
- **purchase_orders** - PO header with supplier tracking
- **purchase_order_items** - Individual items per purchase order

### Support Tables (8+)
- **payments** - Payment tracking (in/out)
- **contra_bons** - Combined/batch invoices for B2B
- **sales_returns** & **sales_return_items** - Sales return handling
- **purchase_returns** & **purchase_return_items** - Purchase return handling
- **expenses** - Cost tracking
- **api_tokens** - API authentication
- **audit_logs** - Activity audit trail

## Common Maintenance Commands

### Check Migration Status
```bash
php spark migrate:status
```

Shows which migrations have been applied and their batch numbers.

### Run Pending Migrations
```bash
php spark migrate
```

Applies all pending migrations in order.

### Rollback Last Batch
```bash
php spark migrate:rollback
```

Reverts all migrations from the last batch.

### Refresh Database (WARNING: Data Loss)
```bash
php spark migrate:refresh
php spark migrate:refresh --seed  # with seeding
```

**Caution**: This will drop all tables and re-run all migrations.

### Rollback All Migrations
```bash
php spark migrate:rollback --all
```

Reverts to the initial state (no tables).

## Creating New Migrations

### Using CodeIgniter Command
```bash
php spark make:migration AddNewFeatureColumns
```

This creates a new migration file in `app/Database/Migrations/` with the current timestamp.

### Naming Best Practices
1. **Be specific**: Not `AddColumns.php` but `AddDeliveryNoteColumns.php`
2. **Use snake_case**: `add_delivery_note_columns.php` (not camelCase)
3. **Prefix with action**: `create_`, `add_`, `drop_`, `rename_`, `alter_`, `fix_`
4. **One concern per file**: Don't mix unrelated changes

### Migration Template
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewFeatureColumns extends Migration
{
    public function up()
    {
        // Your code to create/alter tables
    }

    public function down()
    {
        // Your code to revert changes
    }
}
```

## Important Notes

### Financial Data Types
- ✅ **CORRECT**: `DECIMAL(15,2)` for money and stock quantities
- ❌ **WRONG**: `FLOAT` or `DOUBLE` (loses precision)

See `/app/Database/Migrations/2026-02-01-100000_create_core_master_tables.php` for examples.

### Transactional Safety
All write operations involving Money, Stock, or Journal Entries should be wrapped in database transactions:
```php
$db->transStart();
// ... operations ...
$db->transComplete();
```

### Foreign Key Constraints
- Current setup uses mostly CASCADE delete which can be dangerous
- Migration #8 documents the risks
- Plan: Convert to SET NULL or RESTRICT for critical tables
- See `FixCascadeDeleteRisks` class in migration #8

### Soft Deletes
Transactional tables support soft deletes via `deleted_at` column (migration #3):
- Records are marked deleted, not physically removed
- Query filters automatically exclude soft-deleted records
- Data remains recoverable

## Troubleshooting

### Migration Fails to Run
1. Check if table already exists (some migrations check with `tableExists()`)
2. Verify database is connected: `php spark db:show`
3. Check MySQL error logs for constraint violations

### "Table already exists" Error
This is normal! Most migrations check `if (!$this->db->tableExists(...))` before creating.

### Foreign Key Constraint Errors
Usually caused by:
1. Child table referencing non-existent parent table
2. Parent table data type mismatch with foreign key
3. Trying to add constraint to table with incompatible data

Solution: Check migration order and column types.

## References

- CodeIgniter 4 Migration Documentation: https://codeigniter.com/user_guide/dbutil/migration.html
- Project Architecture: `docs/ARCHITECTURE.md`
- API Reference: `docs/API.md`

---

**Last Updated**: 2026-02-05  
**Total Migrations**: 8  
**Status**: All active and documented
