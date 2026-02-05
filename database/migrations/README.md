# Database Migrations

CodeIgniter 4 database migrations untuk Inventaris Toko.

## 📋 File Naming Convention

Migrations mengikuti CodeIgniter best practice:
```
YYYYMMDDHHmmss_snake_case_description.php
```

**Format:** 
- `YYYYMMDD` - Tanggal (year-month-day)
- `HHmmss` - Waktu (hour-minute-second)
- `_snake_case_description` - Deskripsi action (lowercase, underscore-separated)

**Contoh:**
- `20260201100000_add_purchase_order_support.php` = 2026-02-01 10:00:00
- `20260202000000_add_transaction_returns_support.php` = 2026-02-02 00:00:00

## 📦 Migrations Overview

### 1. Add Purchase Order Support
**File:** `20260201100000_add_purchase_order_support.php`  
**Purpose:** Tambah fitur Purchase Order (PO)

**Apa yang dibuat:**
- Kolom baru di tabel `purchase_orders`: nomor_po, tanggal_po, estimasi_tanggal, dll
- Tabel baru: `purchase_order_items` (detail items per PO)
- Indexes untuk performance

**Gunakan untuk:**
- Sistem pembelian dari supplier
- Tracking PO hingga diterima

---

### 2. Add Transaction Returns Support
**File:** `20260202000000_add_transaction_returns_support.php`  
**Purpose:** Tambah fitur retur barang (Sales & Purchase Returns)

**Apa yang dibuat:**
- Tabel: `sales_returns` + `sales_return_details` (retur penjualan)
- Tabel: `purchase_returns` + `purchase_return_details` (retur pembelian)
- Status tracking: PENDING, APPROVED, PROCESSED, REJECTED

**Gunakan untuk:**
- Retur barang dari customer
- Retur barang ke supplier
- Tracking alasan retur dan approval flow

---

### 3. Add Financial Tracking & API Support
**File:** `20260202100000_add_financial_tracking_and_api_support.php`  
**Purpose:** Tambah financial tracking & API authentication

**Apa yang dibuat:**
- Tabel: `expenses` (tracking pengeluaran)
- Tabel: `api_tokens` (API authentication)
- Kolom: `debt_balance` di suppliers, `due_date` di customers
- Kolom: `invoice_number`, `due_date` di sales
- Kolom: `last_login` di users

**Gunakan untuk:**
- Financial reporting (pengeluaran, piutang, utang)
- API integration
- Audit trail (last login)

---

### 4. Add Inventory & Product Enhancements
**File:** `20260203000000_add_inventory_and_product_enhancements.php`  
**Purpose:** Enhance inventory tracking dan product management

**Apa yang dibuat:**
- Kolom: `type`, `reference_number`, `current_balance` di stock_mutations
- Kolom: `min_stock_alert` di product_stocks
- Kolom: `jenis`, `status` di warehouses (multi-warehouse types)
- Kolom: `status`, `harga_beli_terakhir`, `stok` di products

**Gunakan untuk:**
- Kartu stok (stock card) report
- Minimum stock alerts
- Multi-warehouse support (Baik/Rusak/Transit)
- Product status tracking

---

## 🚀 Cara Menggunakan

### Jalankan semua migrations
```bash
php spark migrate
```

### Cek status migrations
```bash
php spark migrate:status
```

Output:
```
| Version           | Filename                              | Batch | Migrated On        |
|-------------------|---------------------------------------|-------|------------------|
| 20260201100000    | add_purchase_order_support            | 1     | 2026-02-05 10:00:00 |
| 20260202000000    | add_transaction_returns_support       | 1     | 2026-02-05 10:00:00 |
| 20260202100000    | add_financial_tracking_and_api_support| 1     | 2026-02-05 10:00:00 |
| 20260203000000    | add_inventory_and_product_enhancements| 1     | 2026-02-05 10:00:00 |
```

### Rollback migrations (undo)
```bash
# Undo last batch
php spark migrate:rollback

# Undo specific batch
php spark migrate:rollback --batch=1
```

### Create fresh database (reset)
```bash
# Reset dan reseed database
php spark migrate:refresh --seed
```

### Lihat riwayat migrations
```bash
# Check migrations table
select * from migrations;
```

---

## 🔧 Membuat Migration Baru

Jika perlu menambah feature baru:

```bash
php spark make:migration NameOfMigration
```

**Contoh:**
```bash
php spark make:migration add_delivery_tracking

# Akan membuat file dengan format:
# database/migrations/YYYYMMDDHHMMSS_add_delivery_tracking.php
```

---

## 📝 Best Practices

1. **Naming Convention**
   - ✅ `20260201100000_add_purchase_order_support.php` - Clear, snake_case, purposeful
   - ❌ `20240201000000_CreateMissingTables.php` - Vague, mixed case

2. **One Responsibility Per File**
   - ✅ Satu migration = satu feature atau logical group
   - ❌ Mix berbagai features dalam satu file

3. **Idempotent Migrations**
   - ✅ Gunakan `IF NOT EXISTS` untuk CREATE TABLE
   - ❌ Assume database state selalu sama

4. **Reversible Migrations**
   - ✅ Implement method `down()` untuk rollback
   - ❌ Biarkan `down()` kosong

5. **Documentation**
   - ✅ Add docblock dengan purpose dan perubahan
   - ❌ No comments, unclear what migration does

---

## 🐛 Troubleshooting

### Error: Migration not found
```
Caused by CodeIgniter\Database\Exceptions\DatabaseException
Migration not found
```

**Solution:**
- Check file exists: `ls database/migrations/`
- Check class name matches filename (snake_case → CamelCase)
- Check `app/Config/Migrations.php` configuration

### Error: Table already exists
```
Error: Table 'purchase_orders' already exists
```

**Solution:**
- Use `IF NOT EXISTS` in migration:
  ```php
  $this->forge->createTable('table_name', true); // true = IF NOT EXISTS
  ```

---

## 📚 Referensi

- [CodeIgniter 4 Migrations](https://codeigniter.com/user_guide/dbutil/migration.html)
- [Database Forging](https://codeigniter.com/user_guide/database/forge.html)
