# 📋 Backend Issues - Action Plan & Fixing Guide

## 🎯 **Quick Start - Yang Harus Dilakukan Dulu**

### **STEP 1: Setup Database** ⭐ **PALING PENTING!**

Anda punya 2 pilihan:

#### **Option A: Gunakan Script Setup** (RECOMMENDED - PALING MUDAH)

1. **Fix database name**:
   - Edit `.env` line 37: `database.default.database = inventaris_toko`
   - ATAU edit `archive/setup_working.php` line 10-11 ganti `inventaris_toko` jadi `toko_distributor`

2. **Run setup script**:
```bash
php archive/setup_working.php
```

3. **Verify**:
   - Buka phpMyAdmin: `http://localhost/phpmyadmin`
   - Check database `inventaris_toko` atau `toko_distributor` sudah ada
   - Check ada 20+ tables
   - Check ada 4 users (owner, admin, gudang, sales)

**Default Login:**
- Username: `owner` / Password: `test123`
- Username: `admin` / Password: `test123`

---

#### **Option B: Create Migrations** (Lebih profesional tapi lebih lama)

Saya bisa buatkan migrations untuk CodeIgniter yang proper.

---

### **STEP 2: Fix Backend Issues**

Setelah database setup, kita fix issues satu per satu.

---

## 🐛 **Issue List & Status**

### ✅ **Issue 1: Database Migration** 
**Status:** Belum ada migrations
**Action:** Run setup script (Step 1 di atas)
**Priority:** ⭐⭐⭐⭐⭐ (HIGHEST)

---

### 🔧 **Issue 2: ProductModel.updateStock()**
**Status:** Perlu fix - change 'type' to 'mutation_type'
**File:** `app/Models/ProductModel.php`
**Priority:** ⭐⭐⭐⭐

**Problem:**
```php
// Current (WRONG):
'type' => $type

// Should be (CORRECT):
'mutation_type' => $type
```

**Why:**  
Database column di `stock_mutations` table adalah `type`, bukan `mutation_type`.
Tapi jika model menggunakan `mutation_type`, berarti ada mismatch.

**Fix:**
Check database column name dulu, lalu adjust model.

---

### 🔑 **Issue 3: AuthController - password_hash**
**Status:** Perlu fix
**File:** `app/Controllers/Auth.php` atau `AuthController.php`
**Priority:** ⭐⭐⭐⭐⭐ (CRITICAL - Security!)

**Problem:**
```php
// WRONG - Jangan pakai ini:
'password' => password('user_password')

// CORRECT - Harus pakai ini:
'password_hash' => password_hash($password, PASSWORD_DEFAULT)
```

**Why:**  
- Column di database: `password_hash` bukan `password`
- Harus pakai `password_hash()` bukan `password()`
- Security issue jika password tidak di-hash

**Fix:**
Update AuthController untuk:
1. Registrasi: `password_hash($input, PASSWORD_DEFAULT)`
2. Login: `password_verify($input, $user['password_hash'])`

---

### 🔒 **Issue 4: Apply AuthFilter**
**Status:** Perlu implement
**File:** `app/Config/Filters.php`
**Priority:** ⭐⭐⭐⭐

**Action:**
```php
// In app/Config/Filters.php

public array $filters = [
    // Apply AuthFilter to all routes except login/register
    'authFilter' => [
        'before' => [
            '*',  // All routes
        ],
        'except' => [
            'login',
            'login/*',
            'register',
            'register/*',
            'auth/*',
        ]
    ],
];
```

**Why:** Protect semua routes dari akses tanpa login

---

### 👮 **Issue 5: Apply RoleFilter**
**Status:** Perlu implement
**File:** `app/Config/Filters.php`
**Priority:** ⭐⭐⭐

**Action:**
```php
// In app/Config/Filters.php

public array $filters = [
    'roleFilter:OWNER,ADMIN' => [
        'before' => [
            'users/*',
            'settings/*',
        ]
    ],
    'roleFilter:OWNER,ADMIN,GUDANG' => [
        'before' => [
            'products/*',
            'stock/*',
            'purchase/*',
        ]
    ],
];
```

**Why:** Restrict routes berdasarkan user role

---

### 🛡️ **Issue 6: Fix XSS Vulnerabilities**
**Status:** Perlu fix
**Files:** `app/Helpers/ui_helper.php` dan views
**Priority:** ⭐⭐⭐⭐ (Security!)

**Problem:**
Views kemungkinan pakai:
```php
// WRONG:
<?= $data ?>

// CORRECT:
<?= esc($data) ?>
```

**Fix:**
1. Audit semua views
2. Wrap semua output dengan `esc()`
3. Exception: HTML yang sudah safe (gunakan `{!! !!}` atau `esc($data, 'raw')`)

---

### 💰 **Issue 7: Sales Controller Inconsistencies**
**Status:** Perlu fix
**File:** `app/Controllers/Sales.php` atau `SalesController.php`
**Priority:** ⭐⭐⭐⭐

**Problem:**
Field names tidak konsisten:
- Database: `price` dan `subtotal`
- Code: mungkin pakai `unit_price` dan `total_price`

**Action:**
1. Check database schema: `sale_items` table
2. Check SalesController code
3. Match field names
4. Update controller

---

### 📦 **Issue 8: PurchaseOrder Model**
**Status:** Perlu fix
**File:** `app/Models/PurchaseOrderModel.php`
**Priority:** ⭐⭐⭐

**Problem:**
Field names tidak match dengan database:
- Database: `id_po`, `nomor_po`, `tanggal_po`
- Model: mungkin pakai `id`, `po_number`, `po_date`

**Action:**
Update model untuk match database schema

---

### ⏰ **Issue 9: Session Timeout**
**Status:** Belum ada
**File:** `app/Config/App.php` atau `.env`
**Priority:** ⭐⭐

**Action:**
```env
# Add to .env
session.expiration = 7200  # 2 hours in seconds
```

OR

```php
// In app/Config/App.php
public int $sessionExpiration = 7200;  // 2 hours
```

**Why:** Security - auto logout setelah idle

---

## 📊 **Priority Order**

1. ⭐⭐⭐⭐⭐ **Database Setup** (Issue #1)
2. ⭐⭐⭐⭐⭐ **AuthController password_hash** (Issue #3)
3. ⭐⭐⭐⭐ **AuthFilter** (Issue #4)
4. ⭐⭐⭐⭐ **ProductModel** (Issue #2)
5. ⭐⭐⭐⭐ **Sales Controller** (Issue #7)
6. ⭐⭐⭐⭐ **XSS Fix** (Issue #6)
7. ⭐⭐⭐ **PurchaseOrder Model** (Issue #8)
8. ⭐⭐⭐ **RoleFilter** (Issue #5)
9. ⭐⭐ **Session Timeout** (Issue #9)

---

## 🎯 **Recommended Flow**

### **Phase 1: Setup** (30 menit)
1. Setup database (Issue #1)
2. Test login dengan credentials default
3. Verify data ada (products, customers, etc)

### **Phase 2: Security** (1 jam)
1. Fix AuthController (Issue #3)
2. Apply AuthFilter (Issue #4)
3. Fix XSS (Issue #6)
4. Add session timeout (Issue #9)

### **Phase 3: Business Logic** (1 jam)
1. Fix ProductModel (Issue #2)
2. Fix Sales Controller (Issue #7)
3. Fix PurchaseOrder Model (Issue #8)
4. Apply RoleFilter (Issue #5)

**Total Time: ~2.5 jam**

---

## 🚦 **Next Steps**

Pilih salah satu:

### **Option 1: Yang paling simple (RECOMMENDED)**
```bash
# 1. Run setup script
php archive/setup_working.php

# 2. Test login
# http://localhost:8080/login
# Username: admin / Password: test123

# 3. Selesai! Database ready
```

### **Option 2: Saya buatkan migrations**
Saya akan create proper CodeIgniter migrations untuk semua tables.

---

## ❓ **FAQ**

**Q: Database mana yang benar?**
A: Terserah Anda! Pilih salah satu:
- `inventaris_toko` (dari setup_working.php)
- `toko_distributor` (dari .env)
Yang penting konsisten di semua file.

**Q: Apakah harus fix semua sekarang?**
A: Tidak! Yang WAJIB cuma:
1. Database setup (Issue #1)
2. AuthController password fix (Issue #3)
Yang lain bisa nanti.

**Q: Apakah UI enhancement akan affected?**
A: Tidak! UI yang sudah kita implement tidak akan terpengaruh.

---

## ✅ **Ready to Start?**

Jawab pertanyaan ini:
1. **Database name mana yang mau dipakai?**
   - `inventaris_toko` (dari setup script)
   - `toko_distributor` (dari .env)
   
2. **Mau saya buatkan migrations atau pakai setup script?**
   - Setup script (cepat, 5 menit)
   - Migrations (proper, 30 menit)

Setelah jawab, saya akan langsung fix semuanya! 🚀
