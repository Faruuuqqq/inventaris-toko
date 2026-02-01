# 📦 Database Migrations & Setup Guide

## ✅ **Migration Files Created**

Saya sudah buatkan **2 migration files** + **1 seeder**:

### **Migration Files:**
1. ✅ `2026-02-01-100000_CreateInitialTables.php` (15 tables)
   - users, warehouses, categories, products, product_stocks
   - customers, suppliers, salespersons
   - contra_bons, sales, sale_items
   - purchase_orders, purchase_order_items
   - stock_mutations, payments

2. ✅ `2026-02-01-100001_CreateAdditionalTables.php` (9 tables)
   - sales_returns, sales_return_items
   - purchase_returns, purchase_return_items
   - **expenses** (Biaya/Jasa) ⭐
   - **delivery_notes**, delivery_note_items (Surat Jalan) ⭐
   - **audit_logs** (Tracking) ⭐
   - system_config

### **Seeder File:**
✅ `InitialDataSeeder.php` - Sample data (users, products, customers, etc)

**Total: 24 Tables** covering ALL features!

---

## 🚀 **How to Run (Setelah MySQL Fixed)**

### **Step 1: Check Migration Status**
```bash
cd d:\laragon\www\inventaris-toko
php spark migrate:status
```

### **Step 2: Run Migrations**
```bash
# Run all migrations
php spark migrate

# Jika ada error, rollback:
php spark migrate:rollback
```

### **Step 3: Run Seeder (Insert Sample Data)**
```bash
php spark db:seed InitialDataSeeder
```

### **Step 4: Verify**
```bash
# Check tables created
php spark db:table --show

# Or via phpMyAdmin:
# http://localhost/phpmyadmin
# Check database: inventaris_toko
# Should have 24 tables
```

---

## 📝 **Alternative: Import SQL File**

Jika masih ada masalah dengan MySQL authentication, pakai SQL file:

### **Via phpMyAdmin:**
1. Buka http://localhost/phpmyadmin
2. Klik tab "Import"
3. Choose file: `import_database.sql`
4. Click "Go"
5. Done! ✅

### **Via MySQL Command (after fix):**
```bash
cd d:\laragon\www\inventaris-toko
mysql -u root inventaris_toko < import_database.sql
```

---

## 🔧 **Troubleshooting MySQL Issue**

Saya lihat Anda sudah edit `my.ini` dan remove authentication plugin. 

**Next steps to fix MySQL:**

### **1. Restart MySQL Service**
```powershell
# Stop Laragon
# Or via CMD/PowerShell:
net stop mysql
net start mysql

# Via Laragon:
# 1. Stop All (klik Stop All)
# 2. Start All
```

### **2. Check MySQL Running**
```bash
mysql --version
# Should show: mysql Ver 8.4.3

# Test connection:
mysql -u root -e "SELECT 1"
# Should return: 1
```

### **3. Create Database**
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS inventaris_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

### **4. Run Migrations**
```bash
php spark migrate
php spark db:seed InitialDataSeeder
```

---

## ✅ **What's Included**

### **Sample Data:**
- ✅ 4 Users (owner, admin, gudang, sales) - Password: **test123**
- ✅ 1 Warehouse (Gudang Utama)
- ✅ 5 Categories
- ✅ 5 Products (Laptop, Mouse, Keyboard, Monitor, Flashdisk)
- ✅ 5 Product Stocks
- ✅ 3 Customers
- ✅ 2 Suppliers
- ✅ 3 Salespersons
- ✅ System Config (company info, session timeout)

### **Features Supported:**
- ✅ Master Data (users, products, customers, suppliers, warehouses)
- ✅ Transaksi (sales, purchases, payments, contra bon)
- ✅ **Biaya/Jasa** (expenses table)
- ✅ **Surat Jalan** (delivery_notes table)
- ✅ Retur (sales returns, purchase returns)
- ✅ Kartu Stok (stock_mutations)
- ✅ **Audit Logs** (tracking all activities)
- ✅ Multi-role system (OWNER, ADMIN, GUDANG, SALES)

---

## 🔑 **Default Login**

```
┌──────────┬──────────┬───────┐
│ Username │ Password │ Role  │
├──────────┼──────────┼───────┤
│ owner    │ test123  │ OWNER │
│ admin    │ test123  │ ADMIN │
│ gudang   │ test123  │ GUDANG│
│ sales    │ test123  │ SALES │
└──────────┴──────────┴───────┘
```

---

## 📊 **Database Schema Overview**

```
inventaris_toko/
├── Master Data (7 tables)
│   ├── users
│   ├── warehouses
│   ├── categories
│   ├── products
│   ├── customers
│   ├── suppliers
│   └── salespersons
│
├── Transactions (8 tables)
│   ├── sales + sale_items
│   ├── purchase_orders + purchase_order_items
│   ├── payments
│   ├── contra_bons
│   ├── expenses ⭐ NEW
│   └── delivery_notes + items ⭐ NEW
│
├── Returns (4 tables)
│   ├── sales_returns + items
│   └── purchase_returns + items
│
├── Inventory (2 tables)
│   ├── product_stocks
│   └── stock_mutations (kartu stok)
│
└── System (3 tables)
    ├── audit_logs ⭐ NEW
    └── system_config
```

---

## 🎯 **Next Steps After Database Ready**

1. ✅ **Test Connection**
   ```bash
   php spark db:connect test
   ```

2. ✅ **Test Login**
   - Navigate to: http://localhost:8080/login
   - Login dengan: admin / test123

3. ✅ **Fix Backend Issues** (from checklist):
   - Fix AuthController password_hash
   - Apply AuthFilter
   - Fix ProductModel
   - Fix Sales controller
   - Apply RoleFilter
   - Add session timeout

4. ✅ **Test Features**
   - Dashboard
   - Master data pages
   - Transaction pages

---

## 💡 **Commands Reference**

```bash
# Migrations
php spark migrate                 # Run all pending migrations
php spark migrate:rollback        # Rollback last batch
php spark migrate:refresh         # Rollback all + re-run
php spark migrate:status          # Check migration status

# Seeders
php spark db:seed InitialDataSeeder  # Run initial data seeder
php spark db:seed --all              # Run all seeders

# Database
php spark db:table --show         # Show all tables
php spark db:connect test         # Test database connection
```

---

## ⚠️ **Important Notes**

1. **MySQL Must Be Running** - Fix authentication issue first
2. **Database Name** - Make sure `.env` has `inventaris_toko`
3. **Backup** - Migrations akan DROP existing tables!
4. **Password Hash** - Semua user pakai password: `test123`
5. **Foreign Keys** - Jangan hapus data master kalau sudah ada transaksi

---

## 📞 **Kalau Masih Error**

### **Error: Table already exists**
```bash
# Drop all tables manually via phpMyAdmin
# Or:
mysql -u root -e "DROP DATABASE inventaris_toko; CREATE DATABASE inventaris_toko"
php spark migrate
```

### **Error: Authentication failed**
```bash
# Check my.ini sudah fix
# Restart Laragon
# Test: mysql -u root -e "SELECT 1"
```

### **Error: Migration file not found**
```bash
# Clear cache
php spark cache:clear
# Check file exists di app/Database/Migrations/
```

---

## ✅ **Status Checklist**

- [x] Migration files created (2 files, 24 tables)
- [x] Seeder file created (sample data)
- [x] SQL import file ready (`import_database.sql`)
- [x] `.env` updated (database name)
- [ ] **MySQL authentication fixed** ← YOU ARE HERE
- [ ] Database created
- [ ] Migrations run successfully
- [ ] Sample data seeded
- [ ] Login tested

---

## 🎉 **You're All Set!**

Setelah MySQL fixed, tinggal:
1. Restart Laragon
2. Run `php spark migrate`
3. Run `php spark db:seed InitialDataSeeder`
4. Test login!

Good luck fixing MySQL! 🚀
