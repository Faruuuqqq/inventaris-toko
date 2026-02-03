# 🌱 DATABASE SEEDING GUIDE - INVENTARIS TOKO

> Panduan lengkap untuk seeding data testing di Inventaris Toko

---

## 📋 Apa itu Seeding?

**Database Seeding** = Proses mengisi database dengan data testing/dummy secara otomatis.

**Keuntungan:**
- ✅ Cepat setup demo data
- ✅ Konsisten untuk semua developer
- ✅ Mudah di-reset dan di-populate ulang
- ✅ Ideal untuk testing & development

---

## 🚀 QUICK START

### 1️⃣ **Fresh Start (Reset Database + Seed)**

```bash
# Reset database dan jalankan semua seeder
php spark migrate:refresh --seed

# atau lebih detail:
php spark migrate:fresh --seed DatabaseSeeder
```

**Apa yang dilakukan:**
1. Drop semua tables
2. Run semua migrations
3. Run DatabaseSeeder (yang otomatis call seeders lain)

---

### 2️⃣ **Seed dengan Seeder Spesifik**

```bash
# Seed dengan seeder tertentu
php spark db:seed InitialDataSeeder          # Basic data only
php spark db:seed Phase4TestDataSeeder       # Products, Customers, Suppliers
php spark db:seed SalesDataSeeder            # Transactions
php spark db:seed DatabaseSeeder             # Run semua (recommended)
```

---

### 3️⃣ **Check Data yang Sudah Terseed**

```bash
# MySQL
mysql -u root -p toko_distributor

# Query:
SELECT 'Users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'Products', COUNT(*) FROM products
UNION ALL
SELECT 'Customers', COUNT(*) FROM customers
UNION ALL
SELECT 'Transactions', COUNT(*) FROM sales_transactions;
```

---

## 📁 Struktur Seeders yang Ada

### 1. **DatabaseSeeder.php** (Main Seeder) ⭐
- Mengatur urutan eksekusi seeder lain
- Print summary data yang di-seed
- Call: InitialDataSeeder → Phase4TestDataSeeder → SalesDataSeeder

**Lokasi**: `app/Database/Seeds/DatabaseSeeder.php`

---

### 2. **InitialDataSeeder.php** (Basic Setup)
- Users (Owner, Admin, Sales, Gudang)
- Categories
- Warehouses
- Salespersons

**Lokasi**: `app/Database/Seeds/InitialDataSeeder.php`  
**Lines**: 239  
**Data**: ~20 records

---

### 3. **Phase4TestDataSeeder.php** (Master Data)
- Products (15+ products)
- Customers (10+ customers)
- Suppliers (5+ suppliers)
- Stock di warehouse

**Lokasi**: `app/Database/Seeds/Phase4TestDataSeeder.php`  
**Lines**: 386  
**Data**: ~40+ records

---

### 4. **SalesDataSeeder.php** (Transactions)
- Sales transactions (Tunai & Kredit)
- Purchase transactions
- Returns
- Payments

**Lokasi**: `app/Database/Seeds/SalesDataSeeder.php`  
**Lines**: 223  
**Data**: ~50+ transactions

---

## 📊 DATA YANG TERSEDIA SETELAH SEEDING

### Users (4 accounts)
| Username | Role | Password |
|----------|------|----------|
| owner | OWNER | password |
| admin | ADMIN | password |
| sales | SALES | password |
| gudang | GUDANG | password |

**Password**: `password` (untuk semua)

---

### Master Data
- **Categories**: 5 (Elektronik, Pakaian, Makanan, Alat Tulis, Kesehatan)
- **Warehouses**: 2 (Gudang Utama, Gudang Cabang)
- **Products**: 15+ (dengan stock per warehouse)
- **Customers**: 10+ (dengan credit limit)
- **Suppliers**: 5+ (dengan contact info)
- **Salespersons**: 3 (Tim penjual)

---

### Transactions
- **Sales (Tunai)**: 20+ transactions
- **Sales (Kredit)**: 10+ transactions
- **Purchases**: 10+ transactions
- **Returns**: 5+ returns
- **Payments**: Payment records

---

## 🎯 Common Tasks

### Reset Data & Reseed

```bash
# Option 1: Fresh database + seed
php spark migrate:fresh --seed

# Option 2: Drop + Seed (tapi tetap struktur)
php spark db:seed --force DatabaseSeeder

# Option 3: Seed spesifik
php spark db:seed Phase4TestDataSeeder
```

---

### Lihat Data yang Sudah Ada

```bash
# Terminal MySQL
mysql -u root -p toko_distributor

# Check users
SELECT username, role, is_active FROM users;

# Check products
SELECT sku, name, price_sell FROM products LIMIT 5;

# Check transactions
SELECT * FROM sales_transactions LIMIT 5;
```

---

### Hapus Data (Tapi Tetap Structure)

```bash
# Clear specific table
php spark db:seed --force InitialDataSeeder

# atau manual di MySQL:
TRUNCATE TABLE sales_transactions;
TRUNCATE TABLE products;
-- etc
```

---

### Add Custom Data

**Buat seeder baru:**

```bash
# Generate template seeder baru
php spark make:seeder CustomDataSeeder
```

**Edit `app/Database/Seeds/CustomDataSeeder.php`:**

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomDataSeeder extends Seeder
{
    public function run()
    {
        // Insert custom data
        $this->db->table('products')->insert([
            'sku' => 'PROD-001',
            'name' => 'Product Name',
            'category_id' => 1,
            'price_buy' => 100000,
            'price_sell' => 150000,
        ]);
    }
}
```

**Jalankan:**

```bash
php spark db:seed CustomDataSeeder
```

---

## 🧪 Testing dengan Seeded Data

### 1. Manual Testing di Browser

```bash
# Start server
php spark serve

# Login dengan test credentials
Username: owner
Password: password

# Explore features dengan test data
- Dashboard (see stats)
- Master data (Products, Customers, etc)
- Transactions (see transaction examples)
```

---

### 2. API Testing dengan Postman

```bash
# 1. Import Postman collection
Open: docs/api/Inventaris_Toko_API.postman_collection.json

# 2. Set base_url variable
{{base_url}} = http://localhost/inventaris-toko/public

# 3. Test endpoints dengan seeded data
GET  /master/products           -> See 15+ products
GET  /master/customers          -> See 10+ customers
GET  /sales/list-all            -> See transactions
```

---

### 3. Unit Testing

```bash
# Run tests dengan seeded database
php spark db:seed --force
./vendor/bin/phpunit

# atau spesifik test file
./vendor/bin/phpunit tests/Feature/SalesTest.php
```

---

## 📋 Troubleshooting

### ❌ "Table tidak ada"

```bash
# Jalankan migrations dulu
php spark migrate

# Atau fresh + seed
php spark migrate:fresh --seed
```

---

### ❌ "Duplicate entry" Error

```bash
# Clear data dulu
php spark migrate:refresh

# Kemudian seed ulang
php spark db:seed DatabaseSeeder
```

---

### ❌ "Permission denied"

```bash
# Check folder permissions
chmod 755 writable/

# atau di Windows, pastikan folder writable accessible
```

---

### ❌ "Seeder tidak ditemukan"

```bash
# Check nama seeder match dengan class name
# File: DatabaseSeeder.php
# Class: class DatabaseSeeder extends Seeder

# Make sure di folder: app/Database/Seeds/
```

---

## 🔍 Verifikasi Seeding Sukses

### 1. Cek Database

```bash
mysql -u root -p toko_distributor -e "SELECT COUNT(*) FROM users;"
# Output: 4 users

mysql -u root -p toko_distributor -e "SELECT COUNT(*) FROM products;"
# Output: 15+ products
```

### 2. Cek di Browser

```
URL: http://localhost/inventaris-toko/public/
Login: owner / password
Check: Dashboard menampilkan data ✅
```

### 3. Test API

```bash
curl http://localhost/inventaris-toko/public/master/products
# Output: JSON array dengan 15+ products
```

---

## 📊 Default Test Data

### Sample Users
```php
owner (OWNER)       - Full akses semua fitur
admin (ADMIN)       - Akses transaksi & master data
sales (SALES)       - Akses penjualan
gudang (GUDANG)     - Akses warehouse management
```

### Sample Products
```
PROD-001: Laptop Dell
PROD-002: Mouse Logitech
PROD-003: Keyboard Mechanical
... (15+ products)
```

### Sample Customers
```
CUST-001: PT. Sejahtera Jaya
CUST-002: Toko Bangunan Lama
CUST-003: Warung Makan Sari
... (10+ customers)
```

### Sample Transactions
```
INV-001: Sales tunai Rp 2,500,000 (Jan 1)
INV-002: Sales kredit Rp 5,000,000 (Jan 2)
... (30+ transactions)
```

---

## 🎓 Advanced Tips

### Seed Tanpa Fresh Database

```bash
# Seed spesifik table saja
php spark db:seed Phase4TestDataSeeder --force
```

### Conditional Seeding

```php
// Dalam seeder, check apakah data sudah ada
$existingUser = $this->db->table('users')
    ->where('username', 'owner')
    ->first();

if (!$existingUser) {
    // Insert only if not exists
    $this->db->table('users')->insert($userData);
}
```

### Seed dengan Relations

```php
// Seed product dengan category
$categoryId = $this->db->table('categories')
    ->where('name', 'Elektronik')
    ->first()
    ->id;

$this->db->table('products')->insert([
    'category_id' => $categoryId,
    // ...
]);
```

---

## 📚 Additional Resources

| Topik | File |
|-------|------|
| **CodeIgniter Seeding Docs** | https://codeigniter.com/user_guide/dbutil/index.html |
| **API Testing Guide** | `docs/COMPREHENSIVE_API_DOCUMENTATION.md` |
| **Testing Setup** | `docs/AUTOMATED_TEST_SUITE_TEMPLATE.md` |
| **Developer Guide** | `docs/DEVELOPER_ONBOARDING_GUIDE.md` |

---

## 🚀 Quick Commands Reference

```bash
# Seed operations
php spark db:seed DatabaseSeeder          # Run main seeder
php spark db:seed InitialDataSeeder       # Run specific seeder
php spark migrate:fresh --seed            # Fresh + seed
php spark migrate:refresh --seed          # Refresh migrations + seed

# Database operations
php spark migrate                         # Run migrations
php spark migrate:rollback                # Rollback last migration
php spark make:seeder CustomSeeder        # Generate new seeder

# Testing
php spark serve                           # Run dev server
./vendor/bin/phpunit                      # Run tests
```

---

## 📞 Need Help?

1. **Dokumentasi**: Baca ini (seeding-guide.md)
2. **API Testing**: `docs/api/` folder
3. **Development**: `docs/DEVELOPER_ONBOARDING_GUIDE.md`
4. **Code Reference**: Lihat seeder files di `app/Database/Seeds/`

---

**Last Updated**: February 2024  
**Status**: Ready for production  
**Test Data**: 100+ records ready to seed

