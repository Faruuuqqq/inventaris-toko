# Database Seed Data Documentation

## Overview

This document describes the comprehensive seed data for the TokoManager inventory system. The seed data is divided into two SQL files:

1. **database_full_seed.sql** - Complete database schema and core seed data
2. **database_finance_seed.sql** - Additional financial data (import AFTER the first file)

---

## Files Summary

### 1. database_full_seed.sql

**Purpose**: Complete database schema with comprehensive test data for all features

**Total Records**:
- Users: 5
- Warehouses: 3
- Categories: 10
- Products: 50 (across 10 categories)
- Product Stocks: 150 (50 products × 3 warehouses)
- Customers: 20
- Suppliers: 10
- Salespersons: 5
- Contra Bons: 5
- Sales: 30 (10 January, 20 February)
- Sale Items: 80 items across 30 sales
- Purchase Orders: 10
- Purchase Order Items: 30
- Stock Mutations: 100 (20 IN, 77 OUT, 2 ADJUSTMENTS)
- Payments: 10
- Sales Returns: 3
- Sales Return Items: 3
- Purchase Returns: 2
- Purchase Return Items: 2
- Expenses: 10
- Delivery Notes: 10
- Delivery Note Items: 30
- Audit Logs: 10
- System Config: 10

**Financial Data Included**:
- Sales Revenue: ~Rp 426.7 million
- Total Expenses: ~Rp 25.5 million
- Total Payments: ~Rp 143.4 million
- Outstanding Receivables: ~Rp 95.5 million
- Outstanding Payables: ~Rp 88.5 million

### 2. database_finance_seed.sql

**Purpose**: Extended financial data for testing finance pages

**Additional Records**:
- Additional Payments: 10 (7 receivables, 3 payables)
- Additional Expenses: 15 (various categories)
- Financial Audit Logs: 20
- Financial Views: 5 summary views

**New Financial Categories**:
- Sewa Tempat (Rent)
- Marketing (Advertising)
- Pajak (Taxes)
- Komisi (Commissions)
- Lain-lain (Others)

---

## How to Import

### Step 1: Import Core Database

```bash
# Using MySQL command line
mysql -u root -p inventaris_toko < database_full_seed.sql

# Using phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select database "inventaris_toko"
# 3. Go to Import tab
# 4. Select database_full_seed.sql
# 5. Click Go
```

### Step 2: Import Financial Data (Optional)

```bash
# Using MySQL command line
mysql -u root -p inventaris_toko < database_finance_seed.sql

# Using phpMyAdmin
# Follow the same process as above
```

---

## Login Credentials

All user accounts have the same password: **password123**

| Username | Role | Full Name | Email |
|----------|------|-----------|-------|
| owner | OWNER | Budi Santoso | owner@tokomanager.com |
| admin | ADMIN | Siti Rahayu | admin@tokomanager.com |
| gudang | GUDANG | Agus Wijaya | gudang@tokomanager.com |
| sales1 | SALES | Dewi Lestari | dewi@tokomanager.com |
| sales2 | SALES | Eko Prasetyo | eko@tokomanager.com |

---

## Data Coverage by Module

### Master Data
- ✅ Products: 50 items across 10 categories
- ✅ Warehouses: 3 locations (Gudang Utama, Gudang Cabang, Gudang Display)
- ✅ Customers: 20 companies and individuals
- ✅ Suppliers: 10 suppliers with varied categories
- ✅ Categories: 10 product categories

### Sales & Purchases
- ✅ Sales: 30 transactions with mix of CASH and CREDIT
- ✅ Sale Items: 80 line items
- ✅ Purchase Orders: 10 POs with various statuses
- ✅ Purchase Order Items: 30 line items
- ✅ Contra Bons: 5 credit notes for customers

### Inventory
- ✅ Product Stocks: 150 records across 3 warehouses
- ✅ Stock Mutations: 100 mutations (IN, OUT, ADJUSTMENTS)
- ✅ Sales Returns: 3 returns
- ✅ Purchase Returns: 2 returns

### Finance
- ✅ Payments: 20 total (10 core + 10 finance)
  - 15 Receivable payments (customer payments)
  - 5 Payable payments (supplier payments)
- ✅ Expenses: 25 total (10 core + 15 finance)
  - Various categories: Transportasi, Listrik & Air, Gaji, ATK, Marketing, Pajak, etc.
- ✅ Contra Bons: 5 with payment tracking
- ✅ Customer & Supplier balances updated

### Logistics
- ✅ Delivery Notes: 10 with varied statuses
- ✅ Delivery Note Items: 30 line items
- ✅ Driver and vehicle information

### Audit
- ✅ Audit Logs: 30 logs tracking all major actions

---

## Financial Views Available

After importing database_finance_seed.sql, you'll have these summary views:

### 1. v_monthly_sales_summary
Monthly sales performance including:
- Total transactions
- Cash vs Credit breakdown
- Total sales, paid, and outstanding amounts

### 2. v_monthly_purchases_summary
Monthly purchase tracking including:
- Total POs by status
- Total purchases, paid, and outstanding

### 3. v_monthly_expenses_summary
Monthly expense breakdown by category

### 4. v_cash_flow_summary
Cash flow tracking:
- CASH IN (receivable payments)
- CASH OUT (payable payments)
- EXPENSES (cash expenses)

### 5. v_customer_aging
Customer aging report:
- Current receivables
- 30, 60, 90+ day overdue buckets

### 6. v_supplier_aging
Supplier aging report:
- Current payables
- 30, 60, 90+ day overdue buckets

---

## Key Scenarios Covered

### Sales Scenarios
1. **Cash Sales** - Immediate payment
2. **Credit Sales** - Contra bon tracking
3. **Partial Payments** - Multiple installments
4. **Paid Invoices** - Full settlement
5. **Outstanding Invoices** - Unpaid or partial

### Purchase Scenarios
1. **Fully Received POs** - Complete delivery
2. **Partial Receipts** - Split deliveries
3. **Pending POs** - Not yet received
4. **Paid POs** - Full payment to supplier
5. **Partial Payments** - Installments to supplier

### Return Scenarios
1. **Sales Returns** - Approved and Pending
2. **Purchase Returns** - Quality issues
3. **Return Items** - Detailed line items

### Financial Scenarios
1. **Daily Operations** - Regular expenses (ATK, transport)
2. **Fixed Costs** - Rent, utilities, salaries
3. **Variable Costs** - Marketing, commissions
4. **Tax Payments** - PPN payments
5. **Bank Fees** - Administrative costs

---

## Test Data Quality

### Realistic Business Logic
- ✅ Stock mutations reflect actual sales and purchases
- ✅ Payment amounts match invoice totals
- ✅ Contra bons link to multiple sales
- ✅ Delivery notes reference sales
- ✅ Balance calculations are accurate
- ✅ Date sequences are logical

### Data Variety
- ✅ Mix of B2B (companies) and B2C (individuals)
- ✅ Multiple payment methods (CASH, TRANSFER)
- ✅ Various document statuses
- ✅ Different product categories and price ranges
- ✅ Geographic variety (Jakarta, Bandung)

---

## Troubleshooting

### Common Issues

**Issue**: Foreign key constraints fail
**Solution**: Import tables in order (database_full_seed.sql handles this)

**Issue**: Duplicate key errors
**Solution**: Drop all tables before importing, or use fresh database

**Issue**: Date format issues
**Solution**: Check MySQL timezone settings (script sets to +07:00)

**Issue**: Views not created
**Solution**: Ensure database_finance_seed.sql is imported after main seed file

---

## Customizing Data

### Adding More Sales
```sql
INSERT INTO `sales` (`invoice_number`, `created_at`, `customer_id`, `user_id`, `warehouse_id`, `payment_type`, `total_amount`, `paid_amount`, `payment_status`) 
VALUES ('INV-2024-02-0023', NOW(), 1, 2, 1, 'CASH', 5000000.00, 5000000.00, 'PAID');
```

### Adding More Products
```sql
INSERT INTO `products` (`sku`, `name`, `category_id`, `unit`, `price_buy`, `price_sell`) 
VALUES ('PRD001', 'Produk Baru', 1, 'Pcs', 100000.00, 150000.00);
```

### Adding Stock Mutations
```sql
INSERT INTO `stock_mutations` (`product_id`, `warehouse_id`, `type`, `quantity`, `current_balance`, `reference_number`, `notes`, `created_at`)
VALUES (1, 1, 'IN', 10, 25, 'ADJUSTMENT', 'Stock adjustment', NOW());
```

---

## Notes

1. **Password Hash**: All user passwords are hashed using bcrypt. The plain text password is "password123"

2. **Currency**: All monetary values are in Indonesian Rupiah (IDR) using DECIMAL(15,2)

3. **Date Format**: All dates use Indonesian timezone (GMT+7)

4. **Stock Levels**: Initial stock levels are set to provide variety:
   - Gudang Utama: High stock (most items 20-200 units)
   - Gudang Cabang: Medium stock (most items 10-150 units)
   - Gudang Display: Low stock (display purposes, 2-50 units)

5. **Financial Accuracy**: All financial data balances correctly:
   - Sales = Sum of sale items
   - Payments = Linked to sales/POs
   - Balances = Calculated from transactions

---

## Summary Statistics

After importing both files, you'll have:

- **Total Tables**: 24
- **Total Records**: ~1,200+
- **Financial Transactions**: 80+
- **Inventory Movements**: 100+
- **Document Types**: 10+ (Invoices, POs, Contra Bons, etc.)

---

## Support

For issues or questions about the seed data:
1. Check this documentation
2. Review the SQL files for inline comments
3. Test queries on the financial views
4. Verify data integrity with SELECT queries

---

**Last Updated**: February 14, 2026
**Version**: 1.0
