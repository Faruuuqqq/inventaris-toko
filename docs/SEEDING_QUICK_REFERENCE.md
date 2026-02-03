# 🌱 SEEDING QUICK REFERENCE

## ⚡ SUPER CEPAT

### 1. Fresh Start (Paling Sering)
```bash
php spark migrate:fresh --seed
```
✅ Drop semua → Run migrations → Populate test data → DONE!

---

### 2. Seed Ulang (Ada data lama)
```bash
php spark db:seed --force
```
✅ Keep table structure, clear & seed data lagi

---

### 3. Seed Spesifik
```bash
php spark db:seed InitialDataSeeder      # Users only
php spark db:seed Phase4TestDataSeeder   # Products, Customers
php spark db:seed SalesDataSeeder        # Transactions
```

---

## 🔑 TEST CREDENTIALS

```
Username: owner      | Password: password    | Role: OWNER
Username: admin      | Password: password    | Role: ADMIN
Username: sales      | Password: password    | Role: SALES
Username: gudang     | Password: password    | Role: GUDANG
```

---

## 📊 DATA TERSEDIA

```
✅ 4 Users (owner, admin, sales, gudang)
✅ 5 Categories (Elektronik, Pakaian, Makanan, Alat Tulis, Kesehatan)
✅ 2 Warehouses (Gudang Utama, Gudang Cabang)
✅ 15+ Products (dengan stock per warehouse)
✅ 10+ Customers (dengan credit limit)
✅ 5+ Suppliers
✅ 30+ Transactions (Tunai & Kredit)
✅ 10+ Purchase & Returns
```

---

## 🎯 COMMON USE CASES

### Scenario: Baru Pertama Kali Clone Project
```bash
# 1. Setup
composer install
copy env-example .env
# Edit .env dengan DB credentials

# 2. Database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS toko_distributor;"
php spark migrate:fresh --seed

# 3. Done! Login: owner/password
```

### Scenario: Mau Reset Data (Developer)
```bash
# Pilihan 1: Fresh (reset semua)
php spark migrate:fresh --seed

# Pilihan 2: Seed ulang (struktur tetap)
php spark db:seed --force
```

### Scenario: Testing Spesifik Feature
```bash
# Hanya butuh product & customer data
php spark db:seed Phase4TestDataSeeder

# Hanya butuh users & warehouse
php spark db:seed InitialDataSeeder
```

### Scenario: Production (NO SEED!)
```bash
# Migrations saja, NO seeding!
php spark migrate

# Data diisi manual via aplikasi
```

---

## 🛠️ TROUBLESHOOTING

| Problem | Solution |
|---------|----------|
| **Table doesn't exist** | `php spark migrate` |
| **Duplicate data** | `php spark migrate:fresh --seed` |
| **Permission denied** | `chmod 755 writable/` |
| **Seeder not found** | Check file di `app/Database/Seeds/` |

---

## 📁 SEEDER FILES

```
app/Database/Seeds/
├── DatabaseSeeder.php           ← MAIN (jalankan ini!)
├── InitialDataSeeder.php        ← Users, Categories, Warehouse
├── Phase4TestDataSeeder.php     ← Products, Customers, Suppliers
└── SalesDataSeeder.php          ← Transactions
```

---

## ✅ VERIFY SEEDING WORK

### Di Browser
```
1. Login: http://localhost/inventaris-toko/public/
   Username: owner / Password: password
   
2. Check Dashboard: Lihat data ada?
3. Master Data > Products: Ada 15+ products?
4. Master Data > Customers: Ada 10+ customers?
```

### Di MySQL
```bash
mysql -u root -p toko_distributor
SELECT COUNT(*) FROM users;        # Should be 4
SELECT COUNT(*) FROM products;     # Should be 15+
SELECT COUNT(*) FROM customers;    # Should be 10+
```

### Via API
```bash
curl http://localhost/inventaris-toko/public/master/products
# Should return JSON array with 15+ products
```

---

## 📚 DETAILED GUIDE

📖 Baca: `docs/SEEDING_GUIDE.md` untuk informasi lengkap

---

**Last Updated**: February 2024 | Status: ✅ Ready
