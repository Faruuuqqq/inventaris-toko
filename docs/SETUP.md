# 🔧 Panduan Setup - Inventaris Toko

Panduan lengkap untuk setup environment development aplikasi Inventaris Toko.

**Estimasi waktu**: 20-30 menit

---

## 📋 Daftar Isi

1. [Requirements (Syarat Sistem)](#requirements)
2. [Installation (Instalasi)](#installation)
3. [Database Setup (Setup Database)](#database-setup)
4. [Konfigurasi Aplikasi](#konfigurasi-aplikasi)
5. [Menjalankan Aplikasi](#menjalankan-aplikasi)
6. [Verifikasi Setup](#verifikasi-setup)
7. [Troubleshooting](#troubleshooting)

---

## Requirements

### Software yang Harus Diinstall

| Software | Versi Min | Keterangan |
|----------|-----------|-----------|
| **PHP** | 8.1+ | Runtime untuk CodeIgniter |
| **MySQL / MariaDB** | 5.7+ | Database server |
| **Composer** | 2.0+ | PHP package manager |
| **Git** | Latest | Version control |

### Tools Rekomendasi

| Tool | Kegunaan | Download |
|------|----------|----------|
| **Laragon** | Web Server Bundle (PHP + MySQL + Apache) | [laragon.org](https://laragon.org/) |
| **VS Code** | Code Editor | [code.visualstudio.com](https://code.visualstudio.com/) |
| **Postman** | API Testing | [postman.com](https://www.postman.com/) |
| **MySQL Workbench** | Database GUI | [mysql.com](https://www.mysql.com/products/workbench/) |

### Windows User?

**Kami rekomendasikan pakai Laragon:**
- Sudah include PHP, MySQL, Apache, Composer
- Lebih ringan dari XAMPP
- Lebih mudah di-setup
- Download: https://laragon.org/

---

## Installation

### Step 1: Clone Repository

```bash
# Clone repository
git clone https://github.com/your-org/inventaris-toko.git
cd inventaris-toko

# Atau jika sudah ada folder:
cd inventaris-toko
git pull origin main
```

### Step 2: Install Dependencies dengan Composer

```bash
composer install
```

Output yang diharapkan:
```
Installing dependencies from lock file
...
✓ Package installed successfully
```

Jika ada error, coba:
```bash
composer install --no-cache
composer update
```

### Step 3: Copy Environment File

Pada Laragon:
```bash
# Copy .env.example ke .env
copy .env.example .env

# Atau manual: Copy file .env.example dan rename menjadi .env
```

---

## Database Setup

### ⚠️ PENTING: Pilih Metode Setup

Ada 2 metode setup database. Pilih **salah satu**:

#### **Metode 1: Menggunakan Migrations (Recommended)** ✅

Ini adalah metode modern dan direkomendasikan. Migrations akan create table secara otomatis.

```bash
# 1. Buat database kosong di MySQL
mysql -u root -p
CREATE DATABASE inventaris_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. Jalankan migration untuk create tables
php spark migrate

# 3. (Optional) Seed sample data
php spark db:seed SampleDataSeeder
```

**Keuntungan:**
- ✅ Lebih rapi dan terstruktur
- ✅ Mudah di-version control
- ✅ Mudah rollback jika ada error
- ✅ Semua tim bisa punya database yang sama

**Output yang diharapkan:**
```
Running all new migrations...

✓ Migrated: 2024-02-01-000000-CreateMissingTables.php
✓ Migrated: [migration_name]
```

---

#### **Metode 2: Import SQL File (Manual)**

Jika Metode 1 tidak bekerja, gunakan SQL file langsung.

```bash
# 1. Buat database kosong
mysql -u root -p
CREATE DATABASE inventaris_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. Import database dari file SQL
mysql -u root -p inventaris_toko < plan/database.sql

# 3. Verifikasi
mysql -u root -p inventaris_toko
SHOW TABLES;
EXIT;
```

**File SQL location:**
- **Main schema**: `plan/database.sql` (181 lines)
- Berisi semua 13 tables untuk aplikasi

---

### Database Schema Overview

Aplikasi Inventaris Toko menggunakan **13+ tables utama**:

```
MASTER DATA (7 tables):
├── users              - User accounts & roles
├── categories         - Kategori produk
├── products           - Data produk (SKU, harga, dll)
├── customers          - Data pelanggan
├── suppliers          - Data supplier
├── warehouses         - Multi-lokasi gudang
└── salespersons       - Data salesman

INVENTORY (2 tables):
├── product_stocks     - Stok per gudang & produk (pivot table)
└── stock_mutations    - History pergerakan stok (Kartu Stok)

TRANSACTIONS (4 tables):
├── sales              - Master penjualan (Invoice)
├── sale_items         - Detail item per penjualan
├── kontra_bons        - Tukar faktur B2B (batch invoice)
└── payments           - History pembayaran (Terima/Bayar)

ADDITIONAL TABLES (Optional - Created by advanced migrations):
├── purchase_orders        - Data pembelian dari supplier
├── purchase_order_items   - Detail item per PO
├── sales_returns          - Retur penjualan
├── sales_return_details   - Detail item retur jual
├── purchase_returns       - Retur pembelian
├── purchase_return_details- Detail item retur beli
├── expenses               - Log pengeluaran/biaya
└── api_tokens             - API authentication tokens

Total: 13 core tables + optional advanced features tables
```

### Database Connection Check

Pastikan koneksi database sudah benar:

```bash
# Cek koneksi database
php spark db:show

# Output: Database name, hostname, etc
```

---

## Konfigurasi Aplikasi

### Step 1: Edit File `.env`

Buka file `.env` di root folder dan edit:

```ini
# ===== ENVIRONMENT =====
CI_ENVIRONMENT = development
app.timezone = Asia/Jakarta

# ===== BASE URL =====
# Sesuaikan dengan folder Anda
app.baseURL = 'http://localhost/inventaris-toko/public/'

# ===== DATABASE =====
database.default.hostname = localhost
database.default.database = inventaris_toko
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi

# ===== SESSION =====
# Untuk development bisa pakai database
session.driver = database
session.expiration = 7200  # 2 jam

# ===== LOGGING =====
log.threshold = 4  # Development: 4 (Alerts)
log.handlers = [CodeIgniter\Logs\Handlers\FileHandler]
```

### Step 2: Verifikasi Konfigurasi

Buat file test di `public/test-connection.php`:

```php
<?php
// Quick database test
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/Services.php';

try {
    $db = \Config\Database::connect();
    echo "✅ Database Connected!<br>";
    echo "Database: " . $db->database . "<br>";
    echo "Tables: ";
    $tables = $db->table('information_schema.tables')
        ->where('table_schema', $db->database)
        ->get()->getResultArray();
    echo count($tables) . " tables found<br>";
} catch (\Exception $e) {
    echo "❌ Connection Error: " . $e->getMessage();
}
?>
```

Akses: `http://localhost/inventaris-toko/public/test-connection.php`

---

## Menjalankan Aplikasi

### Opsi 1: Menggunakan PHP Built-in Server (Recommended untuk Development)

```bash
php spark serve
```

**Output:**
```
Starting CodeIgniter development server on http://localhost:8080
Press Control+C to stop the server
```

**Akses aplikasi:** `http://localhost:8080`

### Opsi 2: Menggunakan Laragon

1. Buka Laragon
2. Click "Menu" → "Auto Hosts" (untuk auto-DNS)
3. Akses: `http://inventaris-toko.local/public/`

### Opsi 3: Menggunakan Apache (XAMPP)

1. Pastikan Apache running
2. Akses: `http://localhost/inventaris-toko/public/`

---

## Login & Testing

### Default Credentials

Setelah setup, gunakan credentials ini untuk login:

| Role | Username | Password | Akses |
|------|----------|----------|--------|
| **Owner** | owner | password | Semua fitur |
| **Admin** | admin | password | Master Data + Transaksi |

### ⚠️ PENTING untuk Production

**JANGAN gunakan credentials default di production!**

Ganti password setelah login:
1. Login dengan credentials di atas
2. Dashboard → Click nama user (top-right)
3. Change password

---

## Verifikasi Setup

Pastikan semua berfungsi dengan baik:

### Checklist

- [ ] Aplikasi bisa diakses (login page muncul)
- [ ] Bisa login dengan credentials default
- [ ] Dashboard bisa dibuka (tidak ada error)
- [ ] Static assets load (CSS, JS, images terlihat)
- [ ] Database tables sudah ada (13 tables)

### Quick Test

```bash
# 1. Check PHP version
php -v
# Output: PHP 8.x.x ...

# 2. Check Composer
composer --version
# Output: Composer 2.x.x ...

# 3. Check database
php spark db:show
# Output: toko_distributor, localhost, etc

# 4. Check routes
php spark routes | head -20
# Output: Daftar routes

# 5. Seed sample data (optional)
php spark db:seed UserSeeder
```

### Test dengan Browser

Buka DevTools (F12) dan cek:

**Network Tab:**
- [ ] Semua requests status 200 atau 302 (tidak ada 404/500)
- [ ] CSS/JS files load dengan baik

**Console Tab:**
- [ ] Tidak ada JavaScript errors (merah)
- [ ] Warnings boleh (kuning)

**Application Tab:**
- [ ] Session cookie ada
- [ ] LocalStorage berfungsi

---

## Troubleshooting

### ❌ "Cannot GET /" atau 404 Error

**Penyebab:** URL salah atau route tidak ditemukan

**Solusi:**
```bash
# 1. Cek baseURL di .env
# Harus sesuai dengan folder struktur Anda
app.baseURL = 'http://localhost/inventaris-toko/public/'

# 2. Cek .htaccess di folder public/
# Pastikan file .htaccess ada dan readable

# 3. Restart server
# Stop Ctrl+C, jalankan: php spark serve

# 4. Clear browser cache (Ctrl+Shift+Delete)
```

### ❌ Database Connection Error

**Penyebab:** MySQL tidak running atau credentials salah

**Solusi:**
```bash
# 1. Cek MySQL status di Laragon (tombol Start MySQL)

# 2. Verifikasi credentials di .env:
database.default.hostname = localhost
database.default.username = root
database.default.password = [kosongkan jika tidak ada password]

# 3. Test koneksi manual
mysql -u root -p
SHOW DATABASES;
EXIT;

# 4. Jika masih error, cek error log:
tail writable/logs/log-*.log
```

### ❌ "Class Model not found" Error

**Penyebab:** Composer dependencies tidak lengkap

**Solusi:**
```bash
composer install
composer update
composer dump-autoload
```

### ❌ Blank Page atau White Screen

**Penyebab:** PHP error di-suppress

**Solusi:**
```bash
# 1. Edit .env, ubah environment ke development:
CI_ENVIRONMENT = development

# 2. Cek error log:
tail -f writable/logs/log-*.log

# 3. Restart server
```

### ❌ Session/Login Error

**Penyebab:** Folder `writable/` tidak writable

**Solusi:**
```bash
# 1. Buat folder jika belum ada
mkdir -p writable/uploads
mkdir -p writable/logs
mkdir -p writable/cache

# 2. Set permissions (Linux/Mac):
chmod -R 755 writable/

# 3. Restart server
```

### ❌ CSRF Token Mismatch

**Penyebab:** Session configuration issue

**Solusi:**
```bash
# 1. Clear browser cookies (F12 → Application → Cookies)
# 2. Restart server
# 3. Login ulang
```

---

## Next Steps

Setelah setup berhasil:

### 1. Baca Dokumentasi

- **`docs/ARCHITECTURE.md`** - Struktur project & code standards
- **`docs/API.md`** - API endpoints reference
- **`docs/MODAL_SYSTEM_GUIDE.md`** - Cara pakai modal system

### 2. Pahami Codebase

```
app/
├── Controllers/     - Business logic
│   ├── Master/      - CRUD master data
│   ├── Transactions/ - Sales, purchase, returns
│   └── Finance/     - Payments, reports
├── Models/          - Database queries
├── Views/           - HTML templates
└── Config/
    ├── Routes.php   - Semua routes
    └── Database.php - Database config
```

### 3. Test API

Import Postman collection ke Postman:
```
File → Import → docs/api/Inventaris_Toko_API.postman_collection.json
```

### 4. Mulai Develop

```bash
# 1. Create feature branch
git checkout -b feature/your-feature-name

# 2. Make changes & commit
git add .
git commit -m "feat: describe your changes"

# 3. Push & create PR
git push origin feature/your-feature-name
```

---

## Support & Resources

Jika stuck atau ada pertanyaan:

1. **Check Logs**: `writable/logs/log-*.log`
2. **Check Documentation**: `docs/` folder
3. **Online Resources**:
   - [CodeIgniter 4 Docs](https://codeigniter.com/user_guide/)
   - [PHP Docs](https://www.php.net/docs.php)
   - [Stack Overflow - CodeIgniter Tag](https://stackoverflow.com/questions/tagged/codeigniter4)

---

## Checklist Setup Selesai ✅

- ✅ PHP 8.1+ installed
- ✅ MySQL/MariaDB running
- ✅ Composer dependencies installed
- ✅ `.env` file configured
- ✅ Database created & migrations run
- ✅ Application accessible & can login
- ✅ DevTools shows no critical errors
- ✅ Sample data seeded (optional)

**Selamat! Setup selesai. Aplikasi siap digunakan!** 🎉
