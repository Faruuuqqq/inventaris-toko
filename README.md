# 🏪 TokoManager POS - Inventory Management System

[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.4-orange.svg)](https://codeigniter.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3+-38B2AC.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**Status:** ✅ **PRODUCTION READY** | Last Updated: Feb 2026 | All 222 Routes Verified ✅

## 🎯 Tentang Aplikasi

TokoManager adalah sistem POS dan manajemen inventori yang komprehensif, dirancang khusus untuk toko distributor B2B dengan fitur:

### ✨ Fitur Utama
- 📊 **Advanced Analytics Dashboard** dengan Chart.js visualizations
- 📦 **Inventory Management** dengan monitoring stok real-time
- 💰 **Multi-warehouse Stock Management**
- 💳 **Credit Limit Tracking** untuk pelanggan
- 📈 **Sales Analytics** dengan trend analysis
- 🔔 **Real-time Notification System** dengan auto-refresh
- 📑 **Export Reports** - CSV export untuk semua laporan (Daily, P&L, Cash Flow, Monthly)
- 🔐 **Role-based Access Control** (Owner & Admin)
- 🎨 **Modern UI/UX** dengan Tailwind CSS
- 📱 **Responsive Design** (Mobile/Tablet/Desktop)
- 🏷️ **Hidden Transactions** - Owner bisa sembunyikan transaksi tertentu

## 🚀 Prasyarat Sistem

### Web Server
- **PHP** 8.1+ (8.2 recommended)
- **MySQL** 5.7+ atau MariaDB 10.2+
- **Composer** 2.0+
- **Apache/Nginx** web server
- **Laragon** atau **XAMPP** (recommended untuk Windows)

### Browser Support
- ✅ Chrome 120+ (Recommended)
- ✅ Firefox 120+
- ✅ Edge 120+
- ✅ Safari 17+ (Mac)

## 📋 Menu Navigasi

```
Dashboard                    ┌── Data Utama
                    │   ├── 📦 Produk      → Manajemen produk (SKU, harga, kategori)
                    │   ├── 👥 Customer    → Data pelanggan (limit kredit, piutang)
                    │   ├── 🚚 Supplier    → Data supplier (utang)
                    │   ├── 🏭 Warehouse   → Multi-lokasi gudang
                    │   └── 👨 Salesperson → Tim penjual
                    │
                    └── ⚙️ Users       → Manajemen user (Owner only)
                    │
                    └── 🚫 Settings     → Konfigurasi sistem
                    │
                    └── 🚪 Logout
                    │
                    └── Transaksi
                    │       ├── 💰 Penjualan Tunai
                    │       ├── 💳 Penjualan Kredit
                    │       ├── 📦 Pembelian
                    │       ├── 🔄 Retur Penjualan
                    │       ├── 🔄 Retur Pembelian
                    │       │       └── 📄 Surat Jalan
                    │       └── 📋 Kontra Bon
                    │       └── ⚙️ Pembayaran
                    │       │       ├── 💵 Pembayaran Piutang
                    │       │       └── 💸 Pembayaran Utang
                    │       └── 🏷 Informasi & Laporan
                    │           ├── 📊 Histori (Semua Transaksi)
                    │           ├── 💼 Saldo Piutang
                    │           ├── 💰 Saldo Stok
                    │           ├── 📈 Kartu Stok
                    │           └── 📊 Laporan Harian
                    │
                    └── 📊 Laporan Laba Rugi (Owner only)
```

## 🔐 Kredensial Login

| Role | Username | Email | Password | Akses |
|------|----------|--------|---------|--------|--------------|
| Owner | owner | owner@example.com | password123 | **SEMUA FITUR** + Hidden Transactions |
| Admin | admin | admin@example.com | password123 | Transaksi, Master Data, Settings |

---

## 🚀 Quick Start (Setup Cepat)

### Untuk Pengguna Baru - Baca Panduan Setup Lengkap:

👉 **[BACA `docs/SETUP.md` UNTUK PANDUAN LENGKAP](docs/SETUP.md)**

Panduan di atas mencakup:
- ✅ Prerequisites & installation
- ✅ Database setup (2 metode: Migrations atau SQL Import)
- ✅ Configuration (.env setup)
- ✅ Running the application
- ✅ Troubleshooting

### Quick Command (untuk yang sudah experienced):

```bash
# 1. Install dependencies
composer install

# 2. Setup .env
cp .env.example .env
# Edit .env dengan konfigurasi Anda

# 3. Setup database (pilih salah satu):
# Metode A: Migrations (recommended)
php spark migrate

# Metode B: SQL Import
mysql -u root -p toko_distributor < plan/database.sql

# 4. Jalankan aplikasi
php spark serve

# 5. Akses
# http://localhost:8080
```

---

## 🛠️ Development Commands

Like `npm run` in modern development workflows, use `composer run` for common tasks:

### Server & Development
```bash
composer run dev              # Start development server (localhost:8080)
```

### Testing
```bash
composer run test             # Run PHPUnit tests
composer run test:coverage    # Generate coverage report (build/logs/html)
```

### Notification System Testing
```bash
# Seed notifications for testing
php spark db:seed NotificationSeeder

# Check notification endpoints
curl -X GET http://localhost:8080/notifications/getUnreadCount \
     -H "X-Requested-With: XMLHttpRequest"
```

### CSV Export Testing
```bash
# Test CSV export for daily report
curl -X GET "http://localhost:8080/info/reports/daily?export=csv" \
     -H "X-Requested-With: XMLHttpRequest"

# Test CSV with date range
curl -X GET "http://localhost:8080/info/reports/daily?date=2026-02-15&export=csv" \
     -H "X-Requested-With: XMLHttpRequest"
```

### Database
```bash
composer run db:migrate       # Apply pending migrations
composer run db:refresh       # Rollback & re-run all migrations + seed
composer run db:seed          # Run database seeders
composer run fresh            # Full reset: db:refresh + cache:clear
```

### Seeding Test Data
```bash
# Seed core data for testing
php spark db:seed DatabaseSeeder

# Seed notifications specifically
php spark db:seed NotificationSeeder

# Seed all test data
php spark db:seed DatabaseSeeder && php spark db:seed NotificationSeeder
```

### Code Quality
```bash
composer run lint             # Auto-fix code formatting (PSR-12 standard)
composer run lint:check       # Check formatting without changes
composer run prepare          # Run lint + test before committing
```

### Utilities
```bash
composer run cache:clear      # Clear application cache
composer run route:list       # Display all routes
```

### Installation (First Time)
```bash
# 1. Install php-cs-fixer for linting
composer require --dev friendsofphp/php-cs-fixer:^3.59

# 2. Then use composer run commands
```

**Code Standards:** All commands enforce PSR-12 standard. See `.php-cs-fixer.dist.php` for configuration.

---

## 📚 Dokumentasi

Aplikasi memiliki dokumentasi lengkap di folder `docs/`:

### 🎯 Dokumentasi Utama (WAJIB BACA)

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-----------|
| **[SETUP.md](docs/SETUP.md)** | Panduan installation & konfigurasi lengkap | Developer baru |
| **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** | Struktur project, database schema, code standards | Backend developer |
| **[API.md](docs/API.md)** | Reference semua API endpoints & contoh | API consumer |

### 📖 Dokumentasi Tambahan

| Dokumen | Deskripsi |
|---------|-----------|
| **[TESTING_GUIDE.md](docs/TESTING_GUIDE.md)** | Panduan testing lengkap (manual & automated) |
| **[EXPORT_ENHANCEMENT_PLAN.md](docs/EXPORT_ENHANCEMENT_PLAN.md)** | Rencana pengembangan fitur export (3 fase) |
| **[MODAL_SYSTEM_GUIDE.md](docs/MODAL_SYSTEM_GUIDE.md)** | Panduan modal dialog system |
| **[SEEDING_GUIDE.md](docs/SEEDING_GUIDE.md)** | Panduan database seeding & sample data |
| **[Postman Collection](docs/api/Inventaris_Toko_API.postman_collection.json)** | Import ke Postman untuk test API |

---

## 🔧 Struktur Project & Folder

```
inventaris-toko/
├── 📄 README.md                  ← Dokumentasi utama (file ini)
├── 📄 AGENTS.md                  ← Development guidelines untuk AI agents
├── 📄 .env                       ← Konfigurasi environment
├── 📄 composer.json              ← PHP dependencies
├── 📄 phpunit.xml                ← Testing configuration
│
├── 📁 app/                       ← SOURCE CODE APLIKASI
│   ├── Config/                   ← Konfigurasi
│   │   ├── Routes.php            ← Semua routes (222 total)
│   │   └── Database.php
│   ├── Controllers/              ← Business logic (16 controllers)
│   │   ├── Master/               ← CRUD untuk master data
│   │   ├── Transactions/         ← Sales, purchase, returns
│   │   ├── Finance/              ← Payments & reports
│   │   ├── Info/                 ← Reporting & analytics
│   │   └── Api/                  ← API endpoints
│   ├── Models/                   ← Database models (15+ models)
│   ├── Views/                    ← HTML templates (104 views)
│   ├── Entities/                 ← Data classes
│   ├── Services/                 ← Business logic services
│   ├── Traits/                   ← Reusable code
│   └── Database/                 ← Migrations & seeds
│
├── 📁 public/                    ← Web root
│   ├── index.php                 ← Entry point
│   └── assets/
│       ├── css/                  ← Tailwind CSS
│       ├── js/                   ← JavaScript
│       └── images/               ← Images
│
├── 📁 tests/                     ← Unit & integration tests
├── 📁 docs/                      ← 📚 DOKUMENTASI LENGKAP
│   ├── SETUP.md                  ← **Setup & installation guide**
│   ├── ARCHITECTURE.md           ← **Tech stack & code standards**
│   ├── API.md                    ← **API endpoints reference**
│   ├── MODAL_SYSTEM_GUIDE.md     ← Modal dialog system
│   ├── SEEDING_GUIDE.md          ← Database seeding
│   └── api/                      ← API documentation
│       ├── Inventaris_Toko_API.postman_collection.json
│       └── API_ENDPOINT_LIST.md
│
├── 📁 database/                  ← Database files
│   ├── migrations/               ← Schema migrations
│   └── seeds/                    ← Data seeders
│
├── 📁 plan/                      ← Planning files
│   └── database.sql              ← Main database schema (181 lines)
│
├── 📁 vendor/                    ← Composer packages (git-ignored)
├── 📁 writable/                  ← Writable files (logs, cache)
└── 📁 builds/                    ← Build files
```

---

## 📚 Dokumentasi Lengkap

Semua dokumentasi telah diorganisir rapi di folder `docs/`:

### 🎯 Dokumentasi Utama (Baca Dulu)
- **`docs/FINAL_ENDPOINT_VERIFICATION_REPORT.md`** ⭐ - Report komprehensif semua endpoints (222 routes verified)
- **`docs/COMPREHENSIVE_API_DOCUMENTATION.md`** - Spesifikasi API lengkap dengan contoh request/response
- **`docs/ROUTES_VIEWS_COMPLETE_INTEGRATION_CHECK.md`** - Verifikasi 100% routes terintegrasi di views
- **`docs/PROJECT_COMPLETION_SUMMARY.md`** - Ringkasan proyek dan achievement

### 🔧 Panduan Pengembang
- **`docs/DEVELOPER_ONBOARDING_GUIDE.md`** - Setup development environment
- **`docs/AUTOMATED_TEST_SUITE_TEMPLATE.md`** - Template untuk automated testing

### 🧪 Testing & API
- **`docs/api/Inventaris_Toko_API.postman_collection.json`** - Postman collection (50+ endpoints)
- **`docs/phase-reports/`** - Detail laporan per fase development

### 📦 Archive
- **`docs/archive/`** - File-file dokumentasi lama dan summary

---

## 📊 Statistik Aplikasi

| Aspek | Detail |
|-------|--------|
| **Framework** | CodeIgniter 4.0+ |
| **Language** | PHP 8.1+ |
| **Database** | MySQL 5.7+ (Database: `toko_distributor`, 13 tables) |
| **Frontend** | Tailwind CSS 3.x + Alpine.js 3.x |
| **Routes** | 222 routes (semua verified ✅) |
| **Controllers** | 16 controllers |
| **Models** | 15+ models |
| **Views** | 104 views |
| **Tests** | PHPUnit 10.x |
| **Status** | ✅ Production Ready |

---

## 🔍 API Quick Reference

### Lihat Dokumentasi API Lengkap?

👉 **[Baca `docs/API.md` untuk referensi API lengkap](docs/API.md)**

### Master Data Endpoints

```
GET/POST   /master/products              → Produk
GET/POST   /master/customers             → Pelanggan
GET/POST   /master/suppliers             → Supplier
GET/POST   /master/warehouses            → Gudang
GET/POST   /master/salespersons          → Salesman
```

### Transaction Endpoints

```
GET/POST   /sales/cash                   → Penjualan Tunai
GET/POST   /sales/credit                 → Penjualan Kredit
GET/POST   /purchase                     → Pembelian
GET/POST   /payments/receivables         → Pembayaran Piutang
```

### Report Endpoints

```
GET        /info/saldo/stock-data        → Data Stok
GET        /info/reports/stock-card      → Kartu Stok
GET        /info/reports/daily           → Laporan Harian
```

### Testing dengan Postman

Import Postman collection:
```
docs/api/Inventaris_Toko_API.postman_collection.json
```

Lihat `docs/API.md` untuk dokumentasi lengkap semua endpoints!

---

## 📊 Export Features

### Current Export Capabilities (Built-in)

| Report Type | CSV Export | PDF Export |
|-------------|------------|------------|
| Daily Report | ✅ `?export=csv` | - |
| Profit & Loss | ✅ `?export=csv` | - |
| Cash Flow | ✅ `?export=csv` | - |
| Monthly Summary | ✅ `?export=csv` | - |
| Products | - | ✅ `?export=pdf` |
| Customers | - | ✅ `?export=pdf` |
| Suppliers | - | ✅ `?export=pdf` |

### Export Examples

```bash
# Daily Report CSV
curl -X GET "http://localhost:8080/info/reports/daily?export=csv"

# Daily Report with specific date
curl -X GET "http://localhost:8080/info/reports/daily?date=2026-02-15&export=csv"

# Owner: Include hidden transactions
curl -X GET "http://localhost:8080/info/reports/daily?include_hidden=1&export=csv"
```

### 🔮 Future Export Enhancements

Lihat rencana lengkap di: **[EXPORT_ENHANCEMENT_PLAN.md](docs/EXPORT_ENHANCEMENT_PLAN.md)**

**Planned Features:**
- 📗 **Excel Export** (XLSX format)
- 📋 **JSON Export** (API consumption)
- ⚙️ **Background Processing** (large datasets)
- 📬 **Scheduled Reports** (email delivery)
- 🔧 **Custom Report Builder** (drag & drop)
- ☁️ **Cloud Storage Integration**

---

## 🔧 Troubleshooting

### ❌ Halaman Kosong / 404 Error
**Solusi:**
1. Pastikan `app.baseURL` benar di `.env` (contoh: `http://localhost/inventaris-toko/public/`)
2. Enable `mod_rewrite` di Apache (cek `.htaccess`)
3. Restart Apache/Nginx service
4. Clear browser cache

### ❌ Database Error (Connection Refused)
**Solusi:**
1. Pastikan MySQL/MariaDB service running
2. Check credentials di `.env` (host, username, password)
3. Import database: `mysql -u root -p toko_distributor < plan/database.sql`
4. Verify database exists: `SHOW DATABASES;`

### ❌ Session/Login Error
**Solusi:**
1. Pastikan folder `writable/` ada dan permission 755+
2. Pastikan `session_save_path` di `Config/App.php` pointing ke writable folder
3. Clear browser cookies
4. Login ulang

### ❌ API Error (404 / Method Not Found)
**Solusi:**
1. Check route di `app/Config/Routes.php` (222 routes tersedia)
2. Verify HTTP method (GET, POST, PUT, DELETE)
3. Lihat dokumentasi: `docs/COMPREHENSIVE_API_DOCUMENTATION.md`
4. Test dengan Postman: import `docs/api/Inventaris_Toko_API.postman_collection.json`

### ❌ Missing Dependencies
**Solusi:**
```bash
# Update Composer dependencies
composer install
composer update
```

---

## 🎯 Support & Resources

Jika menghadapi masalah atau pertanyaan:

1. **CodeIgniter Docs**: https://codeigniter.com/user_guide/
2. **Stack Overflow**: https://stackoverflow.com/questions/tagged/codeigniter4
3. **GitHub Repository**: Dokumentasi kode dan issue tracking

---

**🚀 SELAMAT MENGGUNAKAN APLIKASI!**

Aplikasi sudah siap digunakan dengan fitur-fitur:
- 📦 Manajemen data master
- 🛒 Sistem transaksi lengkap
- 💰 Kontrol keuangan robust
- 📊 Pelaporan detail
- 🔐 Akses berbasis role
- 📊 Tracking audit lengkap

Mulai penggunaan sekarang untuk optimalisasi alur kerja! 🎉