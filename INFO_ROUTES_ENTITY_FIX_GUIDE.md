# 🔍 INFO ROUTES & ENTITY ISSUES AUDIT & FIX GUIDE

**Issue Date:** February 2024  
**Focus:** `/info` directory routes and entity object handling  
**Status:** 🔴 ERRORS FOUND - FIXES PROVIDED

---

## 🚨 CRITICAL ERRORS FOUND

### Error #1: Entity vs Array Mismatch in Saldo Stock View

**Error Message:**
```
Cannot use object of type App\Entities\Category as array
APPPATH\Views\info\saldo\stock.php at line 40
```

**Location:** `app/Views/info/saldo/stock.php` - Line 40

**Root Cause:**
```php
// Controller returns Entity objects
$categoryModel->findAll() // Returns Category[] entities

// View tries to access as arrays
$category['id']   // ❌ WRONG - Entity object, not array
$category['name'] // ❌ WRONG - Entity object, not array
```

**Current Code:**
```php
<?php foreach ($categories ?? [] as $category): ?>
<option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
<?php endforeach; ?>
```

**Fixed Code:**
```php
<?php foreach ($categories ?? [] as $category): ?>
<option value="<?= esc($category->id) ?>"><?= esc($category->name) ?></option>
<?php endforeach; ?>
```

**Change:** Use `->` for entity property access, not `[]` for array access

---

### Error #2: Entity vs Array Mismatch in Saldo Receivable Controller

**Error Message:**
```
Cannot use object of type App\Entities\Customer as array
APPPATH\Controllers\Info\Saldo.php at line 37
```

**Location:** `app/Controllers/Info/Saldo.php` - Lines 37, 43, 47

**Root Cause:**
```php
// Model returns Entity objects
$customers = $this->customerModel->findAll(); // Returns Customer[] entities

// Controller tries to access as arrays
$customer['id']                      // ❌ WRONG
$latestSale['created_at']            // ❌ WRONG
$customer['receivable_balance']      // ❌ WRONG
```

**Current Code:**
```php
foreach ($customers as $customer) {
    $latestSale = $this->saleModel
        ->where('customer_id', $customer['id'])      // ❌ Line 37
        ->where('payment_status !=', 'PAID')
        ->orderBy('created_at', 'DESC')
        ->first();
    
    if ($latestSale) {
        $daysOverdue = $this->calculateDaysOverdue(
            $latestSale['created_at'],               // ❌ Line 43
            $latestSale['due_date']
        );
        
        $agingData[$agingCategory]['total'] += $customer['receivable_balance']; // ❌ Line 47
    }
}
```

**Fixed Code:**
```php
foreach ($customers as $customer) {
    $latestSale = $this->saleModel
        ->where('customer_id', $customer->id)        // ✅ Fixed
        ->where('payment_status !=', 'PAID')
        ->orderBy('created_at', 'DESC')
        ->first();
    
    if ($latestSale) {
        $daysOverdue = $this->calculateDaysOverdue(
            $latestSale->created_at,                 // ✅ Fixed
            $latestSale->due_date
        );
        
        $agingData[$agingCategory]['total'] += $customer->receivable_balance; // ✅ Fixed
    }
}
```

---

### Error #3: Unknown Column 'purchase_orders.date'

**Error Message:**
```
Unknown column 'purchase_orders.date' in 'where clause'
```

**Root Cause:**
The database column is `created_at`, not `date`. Table uses standard CodeIgniter timestamps.

**Solution:** Use correct column names:
```php
$builder->where('purchase_orders.created_at >=', $startDate); // ✅ Correct
// NOT: $builder->where('purchase_orders.date >=', $startDate); // ❌ Wrong
```

---

## 📋 ALL FIXES NEEDED

### Fix #1: Saldo Stock View - Category Filter

**File:** `app/Views/info/saldo/stock.php`  
**Lines:** 40, 49 (and similar loops)

**Change from:** `$category['id']` → **to** `$category->id`  
**Change from:** `$warehouse['id']` → **to** `$warehouse->id`

---

### Fix #2: Saldo Stock View - Warehouse Filter

**File:** `app/Views/info/saldo/stock.php`  
**Line:** 49

Same as Fix #1 - change array notation to property notation

---

### Fix #3: Saldo Receivable Controller

**File:** `app/Controllers/Info/Saldo.php`  
**Lines:** 37, 43, 47, 51

```
Line 37: $customer['id'] → $customer->id
Line 43: $latestSale['created_at'] → $latestSale->created_at
Line 43: $latestSale['due_date'] → $latestSale->due_date
Line 47: $customer['receivable_balance'] → $customer->receivable_balance
Line 51: array_column($customers, 'receivable_balance') → needs different approach
```

For line 51, use:
```php
$totalReceivable = 0;
foreach ($customers as $customer) {
    $totalReceivable += $customer->receivable_balance;
}
```

---

### Fix #4: Saldo Payable Controller

**File:** `app/Controllers/Info/Saldo.php`  
**Line:** 73

```php
// Current (wrong):
$totalPayable = array_sum(array_column($suppliers, 'debt_balance'));

// Fixed:
$totalPayable = 0;
foreach ($suppliers as $supplier) {
    $totalPayable += $supplier->debt_balance;
}
```

---

## 🔧 IMPLEMENTATION STEPS

Let me apply all fixes systematically:

### Step 1: Fix Saldo Stock View (stock.php)

The view needs to use Entity property access instead of array access.

### Step 2: Fix Saldo Receivable Controller (Saldo.php)

Convert all array accesses to entity property accesses.

### Step 3: Fix Saldo Payable Controller (Saldo.php)

Update array_column usage to entity property access.

---

## 📊 ENTITY VS ARRAY REFERENCE

**Key Difference:**

```php
// ENTITY (returned by ORM)
$category = new Category();  // Instance of App\Entities\Category
$category->id;               // ✅ Use property notation
$category['id'];             // ❌ ERROR - not an array

// ARRAY (plain PHP array)
$category = ['id' => 1, 'name' => 'Cat 1'];
$category['id'];             // ✅ Use array notation
$category->id;               // ❌ ERROR - not an entity
```

---

## ✅ STANDARD CODEIGNITER 4 PATTERNS

**Always check what the Model returns:**

```php
// These return entities:
Model::find($id)      // Single entity
Model::findAll()      // Array of entities
Model::first()        // Single entity
Model::where(...)->first()  // Single entity

// These return arrays:
Model->asArray()->first()   // Single array
Model->asArray()->findAll() // Array of arrays
Query->getResultArray()     // Array of arrays
```

---

## 🎯 COMPLETE LIST OF FIXES NEEDED

| File | Line | Current | Fixed | Type |
|------|------|---------|-------|------|
| `app/Views/info/saldo/stock.php` | 40 | `$category['id']` | `$category->id` | Entity access |
| `app/Views/info/saldo/stock.php` | 40 | `$category['name']` | `$category->name` | Entity access |
| `app/Views/info/saldo/stock.php` | 49 | `$warehouse['id']` | `$warehouse->id` | Entity access |
| `app/Views/info/saldo/stock.php` | 49 | `$warehouse['name']` | `$warehouse->name` | Entity access |
| `app/Controllers/Info/Saldo.php` | 37 | `$customer['id']` | `$customer->id` | Entity access |
| `app/Controllers/Info/Saldo.php` | 43 | `$latestSale['created_at']` | `$latestSale->created_at` | Entity access |
| `app/Controllers/Info/Saldo.php` | 43 | `$latestSale['due_date']` | `$latestSale->due_date` | Entity access |
| `app/Controllers/Info/Saldo.php` | 47 | `$customer['receivable_balance']` | `$customer->receivable_balance` | Entity access |
| `app/Controllers/Info/Saldo.php` | 51 | `array_column($customers, ...)` | Manual loop | Entity access |
| `app/Controllers/Info/Saldo.php` | 73 | `array_column($suppliers, ...)` | Manual loop | Entity access |

---

## 📝 NOTES FOR SIMILAR ISSUES

When you see **"Cannot use object of type"** error:
1. It means an Entity is being accessed like an array
2. Change `$object['key']` to `$object->key`
3. Change `array_column($entities, 'field')` to manual loop with `$entity->field`

When you see **"Unknown column"** error:
1. Check the database schema for correct column names
2. Use CodeIgniter conventions: `created_at`, `updated_at`, `deleted_at`
3. Don't assume `date`, `time`, etc. - verify first

---

**Status:** Ready to apply all fixes  
**Estimated Time:** 15-20 minutes  
**Next Step:** Apply fixes to both files

