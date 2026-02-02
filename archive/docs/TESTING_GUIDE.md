# Phase 1 Testing Guide - Transaction System

## Overview
This document provides a complete manual testing checklist for Phase 1 backend implementation.

## Prerequisites
- Application is running on `http://localhost:8080`
- Login with test account (OWNER or ADMIN role)
- Test data exists (Customers, Suppliers, Products, Warehouses)

---

## Test Case 1: Sales - Create Cash Sale ✅

### Steps:
1. Go to **Penjualan → Penjualan Tunai**
2. Fill in form:
   - **Customer:** Select any active customer
   - **Warehouse:** Select warehouse
   - **Items:** Add 2-3 products with quantities
3. Submit form

### Expected Results:
- ✅ Sale record created in database
- ✅ Sale items recorded
- ✅ Stock deducted from warehouse (check product_stocks table)
- ✅ Stock movement logged (check stock_movements table)
- ✅ Customer balance **NOT CHANGED** (payment_status = PAID)
- ✅ Redirect to detail page with success message

### Verification Queries:
```sql
SELECT * FROM sales ORDER BY id DESC LIMIT 1;
SELECT * FROM sale_items WHERE sale_id = X;
SELECT * FROM product_stocks WHERE product_id = Y;
SELECT * FROM stock_movements WHERE reference_id = X AND reference_type = 'SALE';
```

---

## Test Case 2: Sales - Create Credit Sale ✅

### Steps:
1. Go to **Penjualan → Penjualan Kredit**
2. Fill in form:
   - **Customer:** Select customer with credit_limit > 0
   - **Items:** Add products
3. Verify total doesn't exceed credit limit
4. Submit form

### Expected Results:
- ✅ Sale record created with payment_status = UNPAID
- ✅ Stock deducted
- ✅ Stock movements logged
- ✅ **Customer receivable_balance INCREASED** by sale total
- ✅ Detail page shows "Unpaid" status
- ✅ Can edit/delete (since unpaid)

### Verification:
```sql
SELECT receivable_balance FROM customers WHERE id = X;
-- Should equal: SUM of all unpaid sales for customer
```

---

## Test Case 3: Sales - Edit Sale ✅

### Steps:
1. Create a cash sale (Test Case 1)
2. Go to sale detail page
3. Click "Edit" button
4. Change quantities:
   - Increase qty of item 1
   - Decrease qty of item 2
   - Add new item 3
5. Submit

### Expected Results:
- ✅ Old stock deductions **REVERSED** (added back)
- ✅ New stock deductions **CREATED**
- ✅ Stock movements show both reversals and new deductions
- ✅ Sale items updated
- ✅ Balance recalculated (if credit sale)
- ✅ No stock inconsistencies

---

## Test Case 4: Sales - Delete Sale ✅

### Steps:
1. Create a cash or credit sale
2. Go to sale detail page
3. Click "Delete" button
4. Confirm deletion

### Expected Results:
- ✅ Sale marked as deleted (soft delete)
- ✅ Sale NOT visible in sales list
- ✅ All stock deductions **REVERSED** (added back)
- ✅ Stock movements show reversals
- ✅ Balance updated (if credit sale)
- ✅ Data preserved in database (can query with withDeleted())

---

## Test Case 5: Purchases - Create PO ✅

### Steps:
1. Go to **Pembelian → Pesanan Pembelian**
2. Fill in form:
   - **Supplier:** Select supplier
   - **Warehouse:** Select warehouse
   - **Items:** Add 2-3 products
3. Submit

### Expected Results:
- ✅ PO record created
- ✅ Stock **ADDED** to warehouse
- ✅ Stock movements logged as PURCHASE
- ✅ Supplier debt_balance **INCREASED**
- ✅ Detail page shows PO status

### Verification:
```sql
SELECT debt_balance FROM suppliers WHERE id = X;
SELECT * FROM stock_movements WHERE reference_type = 'PURCHASE';
```

---

## Test Case 6: Purchases - Edit PO ✅

### Steps:
1. Create PO (Test Case 5)
2. Click Edit
3. Change quantities
4. Submit

### Expected Results:
- ✅ Old stock additions **REVERSED** (deducted)
- ✅ New stock additions **CREATED**
- ✅ Stock movements show reversals and new additions
- ✅ Debt balance recalculated

---

## Test Case 7: Purchases - Receive PO (Partial) ✅

### Steps:
1. Create PO with 100 units of product A
2. Go to detail page
3. Click "Terima" (Receive)
4. Enter received quantity: 50 units
5. Submit

### Expected Results:
- ✅ PO status changes to "Diterima Sebagian"
- ✅ Detail shows 50/100 received
- ✅ Can still receive remaining 50
- ✅ Stock already added in DB (no change)

---

## Test Case 8: Sales Returns - Create Return ✅

### Steps:
1. Create a sale with 5 units of product A (Test Case 1 or 2)
2. Go to **Retur Penjualan → Buat Retur**
3. Select the original sale
4. Select product A with quantity 2
5. Submit

### Expected Results:
- ✅ Return record created
- ✅ Stock **ADDED BACK** (returned to warehouse)
- ✅ Stock movements logged as SALES_RETURN
- ✅ If credit sale: customer balance **REDUCED**
- ✅ Return status = "Menunggu Persetujuan"
- ✅ Cannot return more than original qty (validation error)

---

## Test Case 9: Sales Returns - Approve Return ✅

### Steps:
1. Create a sales return (Test Case 8)
2. Go to return detail page
3. Click "Setujui Retur" (Approve Return)
4. Approve with notes
5. Submit

### Expected Results:
- ✅ Status changes to "Selesai"
- ✅ Customer balance further reduced (if not already)
- ✅ Stock still in warehouse (already added in step 8)

---

## Test Case 10: Sales Returns - Reject Return ✅

### Steps:
1. Create a sales return (Test Case 8)
2. Go to return detail page
3. Click "Reject" action
4. Enter rejection notes
5. Submit

### Expected Results:
- ✅ Status changes to "Ditolak"
- ✅ Stock **REMOVED** from warehouse (since we added it in creation)
- ✅ Stock movements show reversal as SALES_RETURN_REJECTED
- ✅ Customer balance **RESTORED** to original
- ✅ Return cannot be approved again

---

## Test Case 11: Purchase Returns - Create Return ✅

### Steps:
1. Create PO and receive it (Test Case 5)
2. Go to **Retur Pembelian → Buat Retur**
3. Select the received PO
4. Select product with quantity to return
5. Submit

### Expected Results:
- ✅ Return record created
- ✅ Stock **DEDUCTED** from warehouse
- ✅ Stock movements logged as PURCHASE_RETURN
- ✅ Supplier debt_balance **REDUCED**
- ✅ Return status = "Menunggu Persetujuan"

---

## Test Case 12: Purchase Returns - Approve Return ✅

### Steps:
1. Create purchase return (Test Case 11)
2. Click "Setujui Retur"
3. Approve with notes
4. Submit

### Expected Results:
- ✅ Status = "Selesai"
- ✅ Supplier debt further reduced

---

## Test Case 13: Error Case - Oversell ❌

### Steps:
1. Product A has stock = 10 units
2. Try to create sale with quantity = 15
3. Submit

### Expected Results:
- ❌ **Error message:** "Stok tidak cukup untuk produk ini. Tersedia: 10, Diminta: 15"
- ❌ Sale NOT created
- ❌ Stock unchanged
- ❌ Database transaction rolled back
- ❌ Form still populated for retry

---

## Test Case 14: Error Case - Credit Limit Exceeded ❌

### Steps:
1. Customer has credit_limit = 1,000,000
2. Customer has outstanding = 800,000
3. Try to create credit sale with total = 300,000
4. Submit

### Expected Results:
- ❌ **Error message:** "Batas kredit akan terlampaui"
- ❌ Sale NOT created
- ❌ Balance unchanged
- ❌ Database rolled back

---

## Test Case 15: Error Case - Invalid Inputs ❌

### Steps:
1. Create a sale form
2. Leave customer blank
3. Try to submit

### Expected Results:
- ❌ **Error message:** "Customer tidak ditemukan"
- ❌ Form rejected with input preserved
- ❌ No DB changes

---

## Test Case 16: Stock Movement Audit Trail ✅

### Steps:
1. Create sale (Test Case 1)
2. Edit sale (Test Case 3)
3. Delete sale (Test Case 4)

### Verification:
```sql
SELECT * FROM stock_movements WHERE product_id = X ORDER BY id DESC LIMIT 10;
```

### Expected Results:
- ✅ Each operation creates log entries:
  - SALE deduction
  - PURCHASE_REVERSAL reversal
  - SALE new deduction (on edit)
  - SALE_RETURN_REVERSAL reversal (on delete)
- ✅ Movement type shows operation
- ✅ Reference ID links to transaction
- ✅ Notes describe the operation
- ✅ Quantity and balance fields accurate

---

## Test Case 17: Balance Calculation Accuracy ✅

### Steps:
1. Customer A: Create 3 credit sales ($100, $200, $300)
2. Create payment for $200 (when payments implemented)
3. Check customer balance

### Expected Results:
- ✅ Balance = 100 + 200 + 300 = $600 (before payment)
- ✅ After payment of $200: Balance = $400
- ✅ Balance matches: SUM(unpaid sales) - SUM(payments)
- ✅ Verify with query:
```sql
SELECT SUM(total_amount) FROM sales 
WHERE custome
