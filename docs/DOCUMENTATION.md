# 📚 DOKUMENTASI INVENTARIS TOKO - FITUR & DATABASE

**Version**: 1.0  
**Last Updated**: 2026-01-27  
**Project**: Inventaris Toko - Mini ERP System

---

## 📋 TABLE OF CONTENTS

1. [Overview Sistem](#overview-sistem)
2. [Fitur yang Sudah Tersedia](#fitur-yang-sudah-tersedia)
3. [Database Schema](#database-schema)
4. [Plan yang Belum Dikerjakan](#plan-yang-belum-dikerjakan)
5. [API Endpoints](#api-endpoints)
6. [User Roles & Permissions](#user-roles--permissions)
7. [Technical Stack](#technical-stack)

---

## 🔍 OVERVIEW SISTEM

### Deskripsi Aplikasi
Inventaris Toko adalah aplikasi Mini ERP untuk toko distribusi yang membutuhkan manajemen stok multi-gudang, sistem kredit B2B dengan Kontra Bon, dan pelaporan keuangan yang komprehensif.

### Target Pengguna
- **Owner**: Pemilik toko dengan akses penuh
- **Admin**: Staff operasional yang mengelola transaksi harian
- **Gudang**: Staff gudang yang mengelola stok dan penerimaan barang
- **Sales**: Tim penjualan yang mengelola pelanggan dan penjualan

### Platform
- **Platform**: Web Application (Local LAN)
- **Tech Stack**: CodeIgniter 4 (PHP 8.1+), MySQL, Tailwind CSS
- **Deployment**: Local server (Laragon/XAMPP)

---

## ✅ FITUR YANG SUDAH TERSEDIA

### 🏢 1. MANAJEMEN DATA MASTER

#### 1.1 Products Management ✅
**Fitur:**
- CRUD Produk lengkap (Create, Read, Update, Delete)
- Support barcode/SKU unik
- Kategorisasi produk
- Harga beli (HPP) dan harga jual
- Alert stok minimum
- Multi-unit support (Dus, Pcs, dll)

**Akses:**
- Owner: Full access (bisa edit HPP & bypass validation)
- Admin: CRUD operations
- Gudang: View & update stok
- Sales: View & input penjualan

**Endpoints:**
- `GET /master/products` - List semua produk
- `POST /master/products` - Buat produk baru
- `PUT /master/products/{id}` - Update produk
- `DELETE /master/products/{id}` - Hapus produk

---

#### 1.2 Customers Management ✅
**Fitur:**
- CRUD Pelanggan lengkap
- Limit kredit per pelanggan
- Tracking saldo piutang (receivable balance)
- Informasi kontak lengkap
- Status aktif/non-aktif

**Validasi:**
- Cek limit kredit sebelum transaksi kredit
- Block transaksi jika melebihi limit
- Update otomatis saldo piutang

**Akses:**
- Owner: Full access
- Admin: Full access
- Sales: Create & edit pelanggan

---

#### 1.3 Suppliers Management ✅
**Fitur:**
- CRUD Supplier lengkap
- Tracking saldo utang (payable balance)
- Informasi kontak dan alamat
- Status aktif/non-aktif

**Validasi:**
- Tracking otomatis hutang dari purchase orders
- Payment processing untuk hutang supplier

**Akses:**
- Owner: Full access
- Admin: Full access

---

#### 1.4 Warehouses Management ✅
**Fitur:**
- CRUD Gudang lengkap
- Support multi-warehouse
- Code gudang unik
- Alamat dan status gudang

**Fitur Multi-Warehouse:**
- Tracking stok per gudang
- Transfer stok antar gudang
- Consolidated reporting

**Akses:**
- Owner: Full access
- Admin: View & select gudang
- Gudang: Full access

---

#### 1.5 Salespersons Management ✅
**Fitur:**
- CRUD Salesperson lengkap
- Komisi per penjualan
- Target tracking
- Informasi kontak

**Akses:**
- Owner: Full access
- Admin: View & assign

---

#### 1.6 Users Management ✅
**Fitur:**
- CRUD User lengkap
- Role-based access control
- Password hashing (bcrypt)
- Status aktif/non-aktif

**Roles:**
- OWNER: Akses penuh termasuk hidden sales
- ADMIN: Manajemen transaksi, tanpa net profit
- GUDANG: Operasional gudang
- SALES: Penjualan dan manajemen pelanggan

**Akses:**
- Owner: Full access (CRUD users)
- Admin: View only

---

### 💰 2. SISTEM TRANSAKSI

#### 2.1 Sales System ✅
**Fitur:**
- **Cash Sales**: Penjualan tunai dengan pembayaran langsung
- **Credit Sales**: Penjualan kredit dengan jatuhtempo
- **Multi-item support**: Berbagai produk dalam satu transaksi
- **Discount per item**: Diskon persentase per produk
- **Overall discount**: Diskon total transaksi
- **Stock validation**: Cek stok sebelum transaksi
- **Delivery note**: Cetak surat jalan

**Workflow Cash Sales:**
1. Pilih pelanggan (opsional)
2. Pilih warehouse stok
3. Input produk dan quantity
4. Sistem hitung total otomatis
5. Validasi stok
6. Proses pembayaran
7. Update stok otomatis
8. Cetak invoice/delivery note

**Workflow Credit Sales:**
1. Pilih pelanggan (wajib)
2. Pilih warehouse stok
3. Input produk dan quantity
4. Validasi limit kredit pelanggan
5. Sistem hitung total & jatuhtempo default (30 hari)
6. Update saldo piutang pelanggan
7. Update stok otomatis
8. Cetak invoice/delivery note

**Stock Management:**
- Auto-reserve stok saat transaksi
- Create stock mutation log
- Update product_stocks table
- Low stock alert

**Akses:**
- Owner: Full access + hidden sales
- Admin: Create sales
- Sales: Create sales
- Gudang: View & process

---

#### 2.2 Purchases Management ✅
**Fitur:**
- **Purchase Order (PO)**: Buat PO ke supplier
- **Goods Receipt**: Terima barang dari PO
- **Multiple items**: Berbagai produk dalam satu PO
- **Price negotiation**: Harga beli per item
- **Status tracking**: PENDING, RECEIVED
- **Stock update**: Auto update stok saat diterima

**Workflow:**
1. Pilih supplier
2. Pilih warehouse tujuan
3. Input produk dan quantity
4. Tentukan harga beli
5. Create PO
6. Receive barang saat datang
7. Update stok otomatis
8. Update saldo hutang supplier

**Features:**
- Track PO status
- Partial receiving support
- Supplier balance tracking
- Purchase history

**Akses:**
- Owner: Full access
- Admin: Full access
- Gudang: Receive & manage

---

#### 2.3 Returns Processing ✅
**Fitur:**
- **Sales Returns**: Retur penjualan dari pelanggan
- **Purchase Returns**: Retur pembelian ke supplier
- **Reason tracking**: Alasan retur
- **Condition status**: GOOD, DAMAGED, DEFECTIVE
- **Approval workflow**: Need approval before processing
- **Stock restoration**: Return stok ke gudang

**Sales Returns Workflow:**
1. Pilih penjualan yang diretur
2. Input produk dan quantity yang diretur
3. Tentukan kondisi barang
4. Masukkan alasan
3. Submit untuk approval (Owner/Admin)
4. Approval: Update stok otomatis
5. Adjust piutang pelanggan (jika perlu)

**Purchase Returns Workflow:**
1. Pilih PO yang diretur
2. Input produk dan quantity
3. Tentukan kondisi
4. Submit untuk approval
5. Approval: Update stok otomatis
6. Adjust hutang supplier

**Akses:**
- Owner: Full access + approval
- Admin: Create & approve
- Gudang: Process returns

---

### 💳 3. SISTEM KEUANGAN (FINANCE)

#### 3.1 Kontra Bon System ✅
**Fitur:**
- **Invoice Consolidation**: Gabungkan beberapa invoice unpaid jadi satu tagihan
- **B2B Billing**: Sistem billing khusus untuk B2B
- **Customer Selection**: Filter invoice berdasarkan pelanggan
- **Payment Tracking**: Track pembayaran partial & full
- **Status Management**: DRAFT, CONFIRMED, PARTIAL, PAID

**Workflow Kontra Bon:**
1. Pilih pelanggan yang ingin dikonsolidasi
2. View semua unpaid invoice pelanggan tersebut
3. Pilih invoice yang ingin digabungkan
4. Sistem hitung total otomatis
5. Create Kontra Bon
6. Link invoice ke Kontra Bon
7. Kirim ke pelanggan

**Payment Processing:**
1. Terima pembayaran dari pelanggan
2. Input jumlah pembayaran
3. Sistem alokasi ke invoice terkait
4. Update status Kontra Bon
5. Update saldo piutang pelanggan
6. Update status invoice terkait

**Akses:**
- Owner: Full access
- Admin: Create & process

---

#### 3.2 Payment Processing ✅
**Fitur:**
- **Receivable Payments**: Pembayaran piutang pelanggan
- **Payable Payments**: Pembayaran utang supplier
- **Multiple payment methods**: Cash, Transfer, Bank, dll
- **Payment allocation**: Alokasi pembayaran ke invoice/kontra bon
- **Balance updates**: Update otomatis saldo
- **Payment history**: Track semua pembayaran

**Receivables Workflow:**
1. Pilih pelanggan dengan piutang
2. View semua unpaid invoice/kontra bon
3. Pilih invoice yang ingin dibayar
4. Input jumlah pembayaran
5. Pilih metode pembayaran
6. Process payment
7. Update saldo piutang
8. Update status invoice

**Payables Workflow:**
1. Pilih supplier dengan hutang
2. View semua unpaid PO
3. Pilih PO yang ingin dibayar
4. Input jumlah pembayaran
5. Pilih metode pembayaran
6. Process payment
7. Update saldo hutang supplier
8. Update status PO

**Akses:**
- Owner: Full access
- Admin: Full access

---

### 📊 4. MANAJEMEN STOK (INVENTORY)

#### 4.1 Stock Tracking ✅
**Fitur:**
- **Real-time tracking**: Stok up-to-date setiap saat
- **Multi-warehouse**: Stok per gudang
- **Stock mutations**: Log semua pergerakan stok
- **Stock reservation**: Reserve stok saat transaksi
- **Low stock alerts**: Alert ketika stok di bawah minimum

**Stock Mutation Types:**
- **IN**: Stok masuk (PO, retur purchase, adjustment)
- **OUT**: Stok keluar (sales, retur sales, adjustment)
- **ADJUSTMENT**: Penyesuaian stok manual
- **TRANSFER**: Pemindahan stok antar gudang

**Stock Validation:**
- Cek stok sebelum transaksi
- Block transaksi jika stok tidak cukup
- Auto-update stok setelah transaksi
- Create mutation log

---

#### 4.2 Stock Adjustments ✅
**Fitur:**
- **Stock in adjustment**: Tambah stok manual
- **Stock out adjustment**: Kurangi stok manual
- **Reason tracking**: Alasan penyesuaian
- **Approval required**: Perlu approval Owner/Admin
- **Mutation logging**: Log semua penyesuaian

**Workflow:**
1. Pilih produk dan warehouse
2. Input quantity adjustment
3. Pilih type adjustment (IN/OUT)
4. Input alasan
5. Submit untuk approval
6. Approval: Update stok
7. Create mutation log

**Akses:**
- Owner: Full access
- Admin: Create & approve
- Gudang: Request adjustment

---

#### 4.3 Stock Transfers ✅
**Fitur:**
- **Inter-warehouse transfer**: Pindah stok antar gudang
- **Stock validation**: Cek stok source gudang
- **Dual logging**: Log di source & destination gudang
- **Transfer tracking**: Track semua transfer
- **Auto-update**: Update otomatis stok kedua gudang

**Workflow:**
1. Pilih produk yang ingin dipindah
2. Pilih source warehouse
3. Pilih destination warehouse
4. Input quantity
5. Validasi stok source warehouse
6. Process transfer
7. Update stok source (OUT mutation)
8. Update stok destination (IN mutation)
9. Create transfer logs

**Akses:**
- Owner: Full access
- Admin: Full access
- Gudang: Process transfers

---

#### 4.4 Barcode Scanning ✅
**Fitur:**
- **Barcode lookup**: Cari produk via barcode/SKU
- **Product details**: Tampilkan detail produk
- **Stock info**: Tampilkan stok saat ini
- **Mobile friendly**: Support untuk scanner mobile
- **Real-time**: Data up-to-date

**Workflow:**
1. Scan barcode produk
2. Sistem lookup produk
3. Tampilkan detail produk
4. Tampilkan stok per gudang
5. Input quantity (jika transaksi)

**Akses:**
- Semua user yang memiliki akses produk

---

### 📈 5. LAPORAN & ANALITIK (REPORTS)

#### 5.1 Stock Reports ✅
**Fitur:**
- **Stock Card**: Kartu stok dengan complete history
- **Stock Balance**: Saldo stok per produk dan gudang
- **Movement History**: History pergerakan stok
- **Low Stock Report**: Produk dengan stok rendah
- **Stock Summary**: Ringkasan stok semua produk
- **Warehouse Distribution**: Distribusi stok per gudang

**Reports Available:**
1. **Stock Card**: History lengkap per produk
   - Running balance
   - Filter by date range
   - Filter by warehouse
   - Mutation types breakdown

2. **Stock Balance**: Stok saat ini
   - Per produk
   - Per warehouse
   - Total stok
   - Low stock alerts

3. **Stock Movement**: Report pergerakan
   - By date range
   - By warehouse
   - By product
   - Mutation types

**Akses:**
- Owner: Full access
- Admin: View access
- Gudang: Full access

---

#### 5.2 Sales Reports ✅
**Fitur:**
- **Daily Sales**: Laporan penjualan harian
- **Sales by Customer**: Penjualan per pelanggan
- **Sales by Product**: Penjualan per produk
- **Sales by Salesperson**: Penjualan per sales
- **Sales History**: History penjualan
- **Payment Status**: Status pembayaran (paid/unpaid/partial)

**Reports Available:**
1. **Daily Sales Summary**: Ringkasan harian
   - Total sales
   - Cash sales
   - Credit sales
   - Number of transactions

2. **Sales by Product**: Produk terlaris
   - Top selling products
   - Sales quantity
   - Revenue per product

3. **Sales by Customer**: Pelanggan teratas
   - Total spent
   - Number of transactions
   - Average transaction value

4. **Sales History**: History lengkap
   - Filter by date range
   - Filter by payment type
   - Filter by customer
   - Filter by salesperson

**Akses:**
- Owner: Full access (termasuk hidden sales)
- Admin: View access (tanpa hidden sales & net profit)
- Sales: View access sendiri

---

#### 5.3 Financial Reports ✅
**Fitur:**
- **Profit & Loss**: Laporan laba rugi
- **Cash Flow**: Laporan arus kas
- **Daily Financials**: Laporan keuangan harian
- **Monthly Summary**: Ringkasan bulanan
- **Receivables Aging**: Aging piutang
- **Payables Aging**: Aging hutang

**Reports Available:**
1. **Profit & Loss**: Laba rugi
   - Revenue (penjualan)
   - COGS (harga pokok penjualan)
   - Gross profit
   - Expenses (operational)
   - Net profit
   - Filter by date range
   - **OWNER ONLY**: Termasuk hidden sales & net profit

2. **Cash Flow**: Arus kas
   - Cash inflows (penjualan cash, pembayaran piutang)
   - Cash outflows (pembelian, pembayaran hutang, retur)
   - Net cash flow
   - Filter by date range

3. **Daily Financials**: Keuangan harian
   - Sales summary
   - Purchases summary
   - Returns summary
   - Net cash flow
   - Transaction counts

4. **Monthly Summary**: Ringkasan bulanan
   - Monthly trends
   - Year-to-date comparison
   - Monthly breakdown
   - Charts & graphs

5. **Aging Reports**: Aging piutang & hutang
   - 0-30 days
   - 31-60 days
   - 61-90 days
   - >90 days
   - Per customer/supplier

**Akses:**
- Owner: Full access (all financial reports)
- Admin: Basic financials (tanpa net profit)

---

#### 5.4 Product Performance Reports ✅
**Fitur:**
- **Product Analytics**: Analisis performa produk
- **Top Products**: Produk terlaris
- **Slow Moving**: Produk lambat terjual
- **Margin Analysis**: Analisis margin keuntungan
- **Stock Turnover**: Rata-rata perputaran stok

**Reports Available:**
1. **Product Performance**: Performa produk
   - Total quantity sold
   - Total revenue
   - Average price
   - Number of sales
   - Filter by date range

2. **Top Products**: Produk terlaris
   - Top N products
   - Sort by quantity or revenue
   - Stock availability

3. **Slow Moving**: Produk lambat
   - Products with low sales
   - High stock, low turnover
   - Recommendation for action

**Akses:**
- Owner: Full access
- Admin: View access
- Gudang: View access

---

#### 5.5 Customer Analysis Reports ✅
**Fitur:**
- **Customer Analytics**: Analisis pelanggan
- **Customer Value**: Nilai pelanggan
- **Purchase Patterns**: Pola pembelian
- **Payment Behavior**: Behavior pembayaran
- **Credit Risk**: Risiko kredit

**Reports Available:**
1. **Customer Analysis**: Analisis pelanggan
   - Total spent
   - Transaction count
   - Average transaction value
   - First & last transaction
   - Payment timeliness

2. **Top Customers**: Pelanggan teratas
   - Top N customers by spending
   - Top N by transaction count
   - Customer retention

3. **Credit Analysis**: Analisis kredit
   - Credit utilization
   - Payment history
   - Late payments
   - Risk assessment

**Akses:**
- Owner: Full access
- Admin: View access
- Sales: View access sendiri

---

### 🔐 6. SISTEM SECURITY & ACCESS CONTROL

#### 6.1 Authentication ✅
**Fitur:**
- **Login/Logout**: Sistem autentikasi
- **Session management**: Manajemen session
- **Password hashing**: Bcrypt untuk keamanan
- **Remember me**: Optional remember login
- **Session timeout**: Auto logout setelah inactivity

**Security Features:**
- CSRF protection
- SQL injection prevention
- XSS protection
- Input validation
- Error handling

---

#### 6.2 Role-Based Access Control (RBAC) ✅
**Fitur:**
- **4 User Roles**: Owner, Admin, Gudang, Sales
- **Permission matrix**: Hak akses per role
- **Hidden transactions**: Transaksi hidden khusus Owner
- **Data filtering**: Filter data berdasarkan role

**Permission Matrix:**

| Feature | Owner | Admin | Gudang | Sales |
|---------|--------|--------|--------|-------|
| View all transactions (including hidden) | ✅ | ❌ | ❌ | ❌ |
| View financial reports (net profit) | ✅ | ❌ | ❌ | ❌ |
| Edit stock & prices (bypass validation) | ✅ | ❌ | ❌ | ❌ |
| Manage users | ✅ | ❌ | ❌ | ❌ |
| Manage master data | ✅ | ✅ | View | View |
| Input transactions | ✅ | ✅ | ❌ | ✅ |
| View stock | ✅ | ✅ | ✅ | ✅ |
| Process returns | ✅ | ✅ | ✅ | ❌ |
| Manage payments | ✅ | ✅ | ❌ | ❌ |
| Generate reports | ✅ | ✅ | Stock | Sales |
| Create hidden sales | ✅ | ❌ | ❌ | ❌ |
| Bypass credit limit | ✅ | ❌ | ❌ | ❌ |

---

### 🌐 7. API ENDPOINTS

#### 7.1 Authentication API ✅
```
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
GET  /api/auth/profile
PUT  /api/auth/profile
POST /api/auth/change-password
```

---

#### 7.2 Products API ✅
```
GET    /api/products
GET    /api/products/{id}
POST   /api/products
PUT    /api/products/{id}
DELETE /api/products/{id}
GET    /api/products/stock
GET    /api/products/{id}/stock
GET    /api/products/{id}/price-history
GET    /api/products/barcode
```

---

#### 7.3 Sales API ✅
```
GET    /api/sales
GET    /api/sales/{id}
POST   /api/sales
PUT    /api/sales/{id}
DELETE /api/sales/{id}
GET    /api/sales/stats
GET    /api/sales/receivables
GET    /api/sales/report
```

---

#### 7.4 Stock API ✅
```
GET    /api/stock
GET    /api/stock/summary
GET    /api/stock/card/{id}
POST   /api/stock/adjust
POST   /api/stock/transfer
POST   /api/stock/availability
GET    /api/stock/barcode
GET    /api/stock/stats
GET    /api/stock/report
```

---

#### 7.5 Customers API ✅
```
GET    /api/customers
GET    /api/customers/{id}
POST   /api/customers
PUT    /api/customers/{id}
DELETE /api/customers/{id}
GET    /api/customers/{id}/receivable
GET    /api/customers/credit-limit
```

---

#### 7.6 Suppliers API ✅
```
GET    /api/suppliers
GET    /api/suppliers/{id}
POST   /api/suppliers
PUT    /api/suppliers/{id}
DELETE /api/suppliers/{id}
```

---

#### 7.7 Warehouses API ✅
```
GET    /api/warehouses
GET    /api/warehouses/{id}
POST   /api/warehouses
PUT    /api/warehouses/{id}
DELETE /api/warehouses/{id}
```

---

#### 7.8 Purchase Orders API ✅
```
GET    /api/purchase-orders
GET    /api/purchase-orders/{id}
POST   /api/purchase-orders
PUT    /api/purchase-orders/{id}
DELETE /api/purchase-orders/{id}
POST   /api/purchase-orders/{id}/receive
```

---

#### 7.9 Sales Returns API ✅
```
GET    /api/sales-returns
GET    /api/sales-returns/{id}
POST   /api/sales-returns
PUT    /api/sales-returns/{id}
DELETE /api/sales-returns/{id}
POST   /api/sales-returns/{id}/approve
```

---

#### 7.10 Purchase Returns API ✅
```
GET    /api/purchase-returns
GET    /api/purchase-returns/{id}
POST   /api/purchase-returns
PUT    /api/purchase-returns/{id}
DELETE /api/purchase-returns/{id}
POST   /api/purchase-returns/{id}/approve
```

---

#### 7.11 Reports API ✅
```
GET /api/reports/profit-loss
GET /api/reports/cash-flow
GET /api/reports/monthly-summary
GET /api/reports/product-performance
GET /api/reports/customer-analysis
```

---

## 🗃️ DATABASE SCHEMA

### Overview
- **Total Tables**: 13 tabel
- **Engine**: InnoDB
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

---

### 1. USERS Table
**Purpose**: Manajemen user dan autentikasi

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `username` (VARCHAR(50), UNIQUE, NOT NULL)
- `password_hash` (VARCHAR(255), NOT NULL)
- `fullname` (VARCHAR(100), NOT NULL)
- `role` (ENUM: 'OWNER', 'ADMIN', 'GUDANG', 'SALES', NOT NULL)
- `is_active` (TINYINT(1), DEFAULT 1)
- `email` (VARCHAR(100))
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Foreign key: None (parent table)
- Used by: sales, stock_mutations

---

### 2. WAREHOUSES Table
**Purpose**: Manajemen lokasi gudang

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `code` (VARCHAR(10), UNIQUE, NOT NULL)
- `name` (VARCHAR(100), NOT NULL)
- `address` (TEXT)
- `is_active` (TINYINT(1), DEFAULT 1)

**Relationships:**
- Parent table for: product_stocks, sales, purchases, stock_mutations
- Referenced by: products_stocks (warehouse_id), sales (warehouse_id), etc.

---

### 3. CATEGORIES Table
**Purpose**: Kategorisasi produk

**Columns:**
- `id` (INT UNSIGNED, PK, AUTO_INCREMENT)
- `name` (VARCHAR(50), NOT NULL)

**Relationships:**
- Parent table for: products
- Referenced by: products (category_id)

---

### 4. PRODUCTS Table
**Purpose**: Master data produk

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `sku` (VARCHAR(50), UNIQUE, NOT NULL)
- `name` (VARCHAR(150), NOT NULL)
- `category_id` (INT UNSIGNED, FK)
- `unit` (VARCHAR(20), DEFAULT 'Pcs')
- `price_buy` (DECIMAL(15,2), DEFAULT 0) - HPP
- `price_sell` (DECIMAL(15,2), DEFAULT 0) - Harga jual
- `min_stock_alert` (INT, DEFAULT 10)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- FK: categories (category_id)
- Referenced by: product_stocks, sale_items, purchase_order_items, stock_mutations

---

### 5. PRODUCT_STOCKS Table
**Purpose**: Tracking stok per produk per gudang

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `product_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `warehouse_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `quantity` (INT, DEFAULT 0, NOT NULL)
- UNIQUE KEY: (product_id, warehouse_id)

**Relationships:**
- FK: products (product_id), warehouses (warehouse_id)
- Cascade delete: ON DELETE CASCADE

---

### 6. CUSTOMERS Table
**Purpose**: Master data pelanggan

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `code` (VARCHAR(20), UNIQUE, NOT NULL)
- `name` (VARCHAR(100), NOT NULL)
- `address` (TEXT)
- `phone` (VARCHAR(20))
- `email` (VARCHAR(100))
- `credit_limit` (DECIMAL(15,2), DEFAULT 0)
- `receivable_balance` (DECIMAL(15,2), DEFAULT 0)
- `is_active` (TINYINT(1), DEFAULT 1)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Referenced by: sales, kontra_bons, payments, sales_returns

---

### 7. SUPPLIERS Table
**Purpose**: Master data supplier

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `code` (VARCHAR(20), UNIQUE, NOT NULL)
- `name` (VARCHAR(100), NOT NULL)
- `address` (TEXT)
- `phone` (VARCHAR(20))
- `email` (VARCHAR(100))
- `payable_balance` (DECIMAL(15,2), DEFAULT 0)
- `is_active` (TINYINT(1), DEFAULT 1)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Referenced by: purchase_orders, payments, purchase_returns

---

### 8. SALESPERSONS Table
**Purpose**: Master data salesperson

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `code` (VARCHAR(20), UNIQUE, NOT NULL)
- `name` (VARCHAR(100), NOT NULL)
- `address` (TEXT)
- `phone` (VARCHAR(20))
- `email` (VARCHAR(100))
- `commission_rate` (DECIMAL(5,2), DEFAULT 0)
- `is_active` (TINYINT(1), DEFAULT 1)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- Referenced by: sales

---

### 9. KONTRA_BONS Table
**Purpose**: Konsolidasi invoice B2B

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `customer_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `number` (VARCHAR(50), UNIQUE, NOT NULL)
- `total_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `paid_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `status` (ENUM: 'DRAFT', 'CONFIRMED', 'PARTIAL', 'PAID', DEFAULT 'DRAFT')
- `notes` (TEXT)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- FK: customers (customer_id)
- Referenced by: sales (kontra_bon_id), payments

---

### 10. SALES Table
**Purpose**: Header transaksi penjualan

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `number` (VARCHAR(50), UNIQUE, NOT NULL)
- `customer_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `warehouse_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `salesperson_id` (BIGINT UNSIGNED, FK)
- `date` (DATE, NOT NULL)
- `total_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `discount_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `final_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `payment_type` (ENUM: 'CASH', 'CREDIT', DEFAULT 'CASH', NOT NULL)
- `payment_status` (ENUM: 'PAID', 'UNPAID', 'PARTIAL', DEFAULT 'PAID')
- `paid_amount` (DECIMAL(15,2), DEFAULT 0, NOT NULL)
- `is_hidden` (TINYINT(1), DEFAULT 0)
- `kontra_bon_id` (BIGINT UNSIGNED, FK)
- `notes` (TEXT)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- FK: customers, warehouses, salespersons, kontra_bons
- Referenced by: sale_items, payments, sales_returns

**Special Features:**
- `is_hidden`: Transaksi hidden hanya terlihat Owner
- Global scope filters hidden sales for non-Owner roles

---

### 11. SALE_ITEMS Table
**Purpose**: Detail line items penjualan

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `sale_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `product_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `quantity` (INT, NOT NULL)
- `unit_price` (DECIMAL(15,2), NOT NULL)
- `discount_percent` (DECIMAL(5,2), DEFAULT 0)
- `total_price` (DECIMAL(15,2), NOT NULL)

**Relationships:**
- FK: sales (sale_id), products (product_id)
- Cascade delete: ON DELETE CASCADE

---

### 12. STOCK_MUTATIONS Table
**Purpose**: Audit trail pergerakan stok

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `product_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `warehouse_id` (BIGINT UNSIGNED, FK, NOT NULL)
- `mutation_type` (ENUM: 'IN', 'OUT', 'ADJUSTMENT', 'TRANSFER', NOT NULL)
- `quantity` (INT, NOT NULL)
- `reference_type` (VARCHAR(50))
- `reference_id` (BIGINT UNSIGNED)
- `notes` (TEXT)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- FK: products (product_id), warehouses (warehouse_id)

**Mutation Types:**
- **IN**: Stok masuk (PO, retur purchase, adjustment)
- **OUT**: Stok keluar (sales, retur sales, adjustment)
- **ADJUSTMENT**: Penyesuaian manual
- **TRANSFER**: Pemindahan antar gudang

**Reference Types:**
- SALE, PURCHASE, SALES_RETURN, PURCHASE_RETURN, STOCK_ADJUSTMENT, STOCK_TRANSFER, etc.

---

### 13. PAYMENTS Table
**Purpose**: Log semua pembayaran

**Columns:**
- `id` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `payment_number` (VARCHAR(50), UNIQUE, NOT NULL)
- `payment_type` (ENUM: 'RECEIVABLE', 'PAYABLE', NOT NULL)
- `reference_type` (ENUM: 'SALE', 'KONTRA_BON', 'PURCHASE', 'RETURN_SALE', 'RETURN_PURCHASE', NOT NULL)
- `reference_id` (BIGINT UNSIGNED, NOT NULL)
- `customer_id` (BIGINT UNSIGNED, FK)
- `supplier_id` (BIGINT UNSIGNED, FK)
- `amount` (DECIMAL(15,2), NOT NULL)
- `payment_method` (VARCHAR(50))
- `payment_date` (DATE, NOT NULL)
- `notes` (TEXT)
- `created_at` (DATETIME, DEFAULT CURRENT_TIMESTAMP)

**Relationships:**
- FK: customers (customer_id), suppliers (supplier_id)
- Referenced by: None (logging only)

**Payment Types:**
- **RECEIVABLE**: Pembayaran dari pelanggan
- **PAYABLE**: Pembayaran ke supplier

---

### Database Relationships Summary

```
users
  ├── sales (salesperson_id)
  └── stock_mutations (created_by)

categories
  └── products

warehouses
  ├── product_stocks
  ├── sales
  ├── purchase_orders
  └── stock_mutations

products
  ├── product_stocks
  ├── sale_items
  ├── purchase_order_items
  ├── sales_return_items
  ├── purchase_return_items
  └── stock_mutations

customers
  ├── sales
  ├── kontra_bons
  ├── payments
  └── sales_returns

suppliers
  ├── purchase_orders
  ├── payments
  └── purchase_returns

salespersons
  └── sales

kontra_bons
  ├── sales
  └── payments

sales
  ├── sale_items
  ├── payments
  └── sales_returns

purchase_orders
  ├── purchase_order_items
  └── purchase_returns

stock_mutations
  └── None (audit trail only)

payments
  └── None (logging only)
```

---

## 🚧 PLAN YANG BELUM DIKERJAKAN

### 🔴 PRIORITAS TINGGI (High Priority)

#### 1. PDF Report Generation
**Status**: ⚠️ PARTIAL (backend logic ready, frontend pending)

**Plan:**
- Implementasi library PDF (dompdf, TCPDF, atau mpdf)
- Generate PDF untuk:
  - Invoice penjualan
  - Delivery notes
  - Purchase orders
  - Stock card
  - Financial reports
- Email PDF ke customer/supplier
- Download/print PDF dari browser

**Files yang perlu diupdate:**
- `app/Libraries/PdfGenerator.php` - Create library PDF
- `app/Controllers/Reports.php` - Add PDF generation methods
- Update views dengan tombol download PDF

**Estimasi waktu**: 3-5 hari

---

#### 2. Email Notifications System
**Status**: ❌ BELUM DIBUAT

**Plan:**
- Setup email configuration (SMTP)
- Implementasi notification system:
  - Email invoice ke customer
  - Email konfirmasi pembayaran
  - Email alert low stock
  - Email reminder jatuh tempo
  - Email PO ke supplier
- Email queue system untuk batch processing
- Template email profesional
- Email history logging

**Fitur yang perlu diimplement:**
- Email service class
- Email templates
- Notification triggers
- Email queue management
- Email history tracking

**Files yang perlu dibuat:**
- `app/Services/EmailService.php`
- `app/Config/Email.php` - Email configuration
- `app/Views/emails/` - Email templates
- `app/Models/NotificationModel.php` - Notification history

**Estimasi waktu**: 4-6 hari

---

#### 3. Mobile-Friendly UI Improvements
**Status**: ⚠️ PARTIAL (basic responsive, need improvement)

**Plan:**
- Implementasi mobile-first design
- Touch-friendly interfaces
- Swipe gestures untuk list
- Bottom navigation bar
- Optimized untuk tablet & phone
- Mobile-specific features:
  - Quick scan barcode dengan kamera
  - Quick actions
  - Mobile-specific dashboard

**Fitur yang perlu diimplement:**
- Mobile CSS framework (Tailwind mobile)
- Touch-optimized components
- Progressive Web App (PWA) support
- Mobile app icons
- Offline mode support

**Files yang perlu diupdate:**
- Update semua views untuk mobile
- `public/assets/css/mobile.css`
- `public/manifest.json` - PWA manifest

**Estimasi waktu**: 5-7 hari

---

### 🟡 PRIORITAS SEDANG (Medium Priority)

#### 4. Multi-Currency Support
**Status**: ❌ BELUM DIBUAT

**Plan:**
- Support multiple currencies
- Currency converter
- Currency rate management
- Multi-currency pricing
- Multi-currency reporting
- Default currency configuration

**Fitur yang perlu diimplement:**
- Currency management table
- Exchange rate API integration
- Currency converter service
- Update product prices untuk multiple currencies
- Multi-currency financial reports

**Files yang perlu dibuat:**
- `app/Models/CurrencyModel.php`
- `app/Services/CurrencyService.php`
- `app/Controllers/CurrencyController.php`
- Update database: add `currencies` table

**Estimasi waktu**: 4-5 hari

---

#### 5. Advanced Analytics Dashboard
**Status**: ⚠️ PARTIAL (basic reports, need advanced analytics)

**Plan:**
- Interactive charts & graphs (Chart.js, ApexCharts)
- Real-time data updates
- Key Performance Indicators (KPIs)
- Trend analysis
- Forecasting features
- Dashboard widgets:
  - Sales trend chart
  - Stock level indicators
  - Financial health metrics
  - Customer engagement metrics
  - Product performance gauge

**Fitur yang perlu diimplement:**
- Chart library integration
- Dashboard widget system
- Real-time data streaming (WebSocket)
- Analytics calculation engine
- Custom dashboard builder
- Export charts sebagai image/PDF

**Files yang perlu dibuat:**
- `app/Services/AnalyticsService.php`
- `app/Controllers/Dashboard/AdvancedDashboard.php`
- Update `app/Views/dashboard/` dengan charts
- `public/assets/js/charts.js`

**Estimasi waktu**: 6-8 hari

---

#### 6. Commission & Bonus System
**Status**: ⚠️ PARTIAL (basic commission tracking, need full system)

**Plan:**
- Komisi per salesperson
- Bonus structure configuration
- Komisi calculation engine
- Commission reports
- Payment tracking
- Sales targets & KPIs
- Performance bonuses

**Fitur yang perlu diimplement:**
- Commission rules configuration
- Automatic commission calculation
- Commission payment processing
- Commission history tracking
- Performance tracking per salesperson
- Commission reports

**Files yang perlu dibuat:**
- `app/Models/CommissionModel.php`
- `app/Services/CommissionService.php`
- `app/Controllers/CommissionController.php`
- Update `salespersons` table untuk commission data
- `app/Views/commission/` - Commission views

**Estimasi waktu**: 4-5 hari

---

### 🔵 PRIORITAS RENDAH (Low Priority)

#### 7. SMS Notifications
**Status**: ❌ BELUM DIBUAT

**Plan:**
- SMS gateway integration
- SMS notifications:
  - Payment confirmation
  - Due date reminder
  - Low stock alert
  - Delivery notification
- SMS template management
- SMS history logging
- Two-way SMS support

**Requirements:**
- SMS gateway API (e.g., Twilio, Nexmo, lokal gateway)
- SMS service class
- SMS templates
- SMS queue system

**Files yang perlu dibuat:**
- `app/Services/SmsService.php`
- `app/Config/Sms.php` - SMS configuration
- `app/Models/SmsLogModel.php`
- `app/Views/sms/` - SMS templates

**Estimasi waktu**: 3-4 hari

---

#### 8. Barcode Scanner Integration
**Status**: ⚠️ PARTIAL (basic barcode lookup, need full scanner integration)

**Plan:**
- Barcode scanner hardware integration
- USB/Bluetooth barcode scanner support
- Mobile camera barcode scanning
- Batch barcode scanning
- Barcode label printing
- Quick product lookup

**Fitur yang perlu diimplement:**
- Barcode scanner event handling
- Mobile camera integration (camera API)
- Barcode label generator
- Print integration
- Batch processing

**Files yang perlu dibuat:**
- `app/Services/BarcodeService.php`
- Update views untuk scanner integration
- Barcode label templates
- `public/assets/js/scanner.js`

**Estimasi waktu**: 3-4 hari

---

#### 9. Multi-Language Support (i18n)
**Status**: ❌ BELUM DIBUAT

**Plan:**
- Support multiple languages
- Language switcher
- Translation management
- Language-specific date/number formatting
- Dynamic content translation

**Languages yang perlu didukung:**
- Indonesian (default)
- English
- Optional: Javanese, Sundanese, dll.

**Fitur yang perlu diimplement:**
- Language files/translation system
- Language detection
- Translation management interface
- Auto-translate untuk static content

**Files yang perlu dibuat:**
- `app/Language/id/` - Indonesian translations
- `app/Language/en/` - English translations
- `app/Services/LanguageService.php`
- Update semua views untuk translation support

**Estimasi waktu**: 4-5 hari

---

#### 10. Advanced Search & Filtering
**Status**: ⚠️ PARTIAL (basic search, need advanced features)

**Plan:**
- Full-text search
- Advanced filtering
- Saved search queries
- Auto-complete/suggestions
- Search history
- Search analytics

**Fitur yang perlu diimplement:**
- Full-text search engine (Elasticsearch/Sphinx)
- Advanced filter builder
- Saved search templates
- Search suggestions
- Search result highlighting

**Files yang perlu dibuat:**
- `app/Services/SearchService.php`
- Update models untuk full-text search
- Update views untuk advanced search UI

**Estimasi waktu**: 3-4 hari

---

#### 11. Backup & Restore System
**Status**: ❌ BELUM DIBUAT

**Plan:**
- Automated database backup
- File backup
- Restore functionality
- Backup scheduling
- Backup history
- Incremental backup support
- Cloud backup integration (optional)

**Fitur yang perlu diimplement:**
- Backup scheduler (cron job)
- Backup compression
- Backup encryption
- Restore validation
- Backup notifications
- Backup reports

**Files yang perlu dibuat:**
- `app/Services/BackupService.php`
- `app/Controllers/BackupController.php`
- `app/Config/Backup.php` - Backup configuration
- `app/Commands/BackupCommand.php` - CLI backup command

**Estimasi waktu**: 3-4 hari

---

#### 12. Audit Trail Enhancement
**Status**: ⚠️ PARTIAL (basic mutations, need full audit trail)

**Plan:**
- Complete audit trail system
- Track semua user actions:
  - Login/logout
  - CRUD operations
  - Configuration changes
  - Data exports
  - Report generations
- Audit log viewer
- Audit log export
- Compliance reporting

**Fitur yang perlu diimplement:**
- Audit middleware
- Audit log service
- Audit log viewer
- Audit report generation
- Compliance dashboard

**Files yang perlu dibuat:**
- `app/Filters/AuditFilter.php`
- `app/Models/AuditLogModel.php`
- `app/Services/AuditService.php`
- `app/Controllers/AuditController.php`

**Estimasi waktu**: 4-5 hari

---

#### 13. Real-Time Notifications (WebSocket)
**Status**: ❌ BELUM DIBUAT

**Plan:**
- WebSocket server integration
- Real-time updates:
  - Stock changes
  - New orders
  - Payment confirmations
  - System alerts
- Push notifications (browser/mobile)
- Notification management
- Notification preferences

**Requirements:**
- WebSocket server (e.g., Ratchet, Pusher)
- Push notification service (FCM, APNs)
- Notification queue
- Real-time client-side handling

**Files yang perlu dibuat:**
- WebSocket server scripts
- `app/Services/NotificationService.php`
- `app/Models/NotificationModel.php`
- `public/assets/js/websocket-client.js`

**Estimasi waktu**: 5-7 hari

---

### 🟢 FUTURE ENHANCEMENTS (Nice to Have)

#### 14. Artificial Intelligence Features
**Plan:**
- Demand forecasting
- Price optimization
- Customer segmentation
- Churn prediction
- Inventory optimization
- Sales prediction

---

#### 15. Integration with External Systems
**Plan:**
- Accounting software integration (QuickBooks, Xero)
- Payment gateway integration (Midtrans, Stripe)
- E-commerce integration (Tokopedia, Shopee)
- CRM integration
- ERP integration

---

#### 16. Advanced Reporting
**Plan:**
- Custom report builder
- Scheduled reports
- Report subscription
- Report distribution
- Advanced visualizations
- Data export (Excel, CSV, PDF)

---

#### 17. System Performance Optimization
**Plan:**
- Database query optimization
- Caching implementation (Redis)
- CDN integration
- Load balancing
- Database sharding
- Data archiving

---

#### 18. Security Enhancements
**Plan:**
- Two-factor authentication (2FA)
- IP whitelisting
- Advanced password policies
- Session security enhancements
- API rate limiting
- DDoS protection

---

## 📊 SUMMARY IMPLEMENTASI

### ✅ Fitur yang Sudah Implementasi (100% Complete)

| Module | Status | Completeness |
|--------|--------|-------------|
| Master Data Management | ✅ Complete | 100% |
| Sales System | ✅ Complete | 100% |
| Purchases | ✅ Complete | 100% |
| Returns Processing | ✅ Complete | 100% |
| Kontra Bon | ✅ Complete | 100% |
| Payment Processing | ✅ Complete | 100% |
| Stock Management | ✅ Complete | 100% |
| Stock Adjustments | ✅ Complete | 100% |
| Stock Transfers | ✅ Complete | 100% |
| Barcode Lookup | ✅ Complete | 100% |
| Basic Reports | ✅ Complete | 100% |
| Financial Reports | ✅ Complete | 100% |
| Stock Reports | ✅ Complete | 100% |
| User Management | ✅ Complete | 100% |
| Role-Based Access | ✅ Complete | 100% |
| API Endpoints | ✅ Complete | 100% |

### 🚧 Fitur yang Belum Implementasi

| Priority | Feature | Estimated Time | Complexity |
|----------|---------|----------------|------------|
| 🔴 High | PDF Report Generation | 3-5 hari | Medium |
| 🔴 High | Email Notifications | 4-6 hari | High |
| 🔴 High | Mobile-Friendly UI | 5-7 hari | Medium |
| 🟡 Medium | Multi-Currency | 4-5 hari | Medium |
| 🟡 Medium | Advanced Analytics | 6-8 hari | High |
| 🟡 Medium | Commission System | 4-5 hari | Medium |
| 🔵 Low | SMS Notifications | 3-4 hari | Medium |
| 🔵 Low | Barcode Scanner | 3-4 hari | Medium |
| 🔵 Low | Multi-Language | 4-5 hari | Medium |
| 🔵 Low | Advanced Search | 3-4 hari | Medium |
| 🔵 Low | Backup & Restore | 3-4 hari | Low |
| 🔵 Low | Audit Trail Enhancement | 4-5 hari | Medium |
| 🔵 Low | Real-Time Notifications | 5-7 hari | High |

---

## 🎯 REKOMENDASI PRIORITAS PENGEMBANGAN

### Fase 1: Critical Business Enhancements (1-2 bulan)
1. ✅ Selesaikan PDF Report Generation
2. ✅ Implement Email Notifications
3. ✅ Improve Mobile UI/UX

### Fase 2: Analytics & Intelligence (2-3 bulan)
4. ✅ Advanced Analytics Dashboard
5. ✅ Commission & Bonus System
6. ✅ Multi-Currency Support

### Fase 3: User Experience Improvements (1-2 bulan)
7. ✅ Barcode Scanner Integration
8. ✅ Advanced Search
9. ✅ Real-Time Notifications

### Fase 4: System Robustness (1-2 bulan)
10. ✅ Backup & Restore
11. ✅ Audit Trail Enhancement
12. ✅ Multi-Language Support

### Fase 5: Future Enhancements (Ongoing)
13. AI Features
14. External Integrations
15. Performance Optimization
16. Security Enhancements

---

## 📝 NOTES

### Technical Debt
- Beberapa views masih menggunakan hard-coded strings (perlu translation support)
- Email service belum diimplementasi
- PDF generation library belum di-setup
- Caching layer belum diimplementasi

### Known Limitations
- Single currency only
- Single language (Indonesian)
- No real-time notifications
- Limited mobile optimization
- No automated backups

### Performance Considerations
- Database queries perlu optimasi untuk volume besar
- Reports perlu caching untuk improve performance
- Large datasets perlu pagination
- Consider indexing strategy untuk improvement

---

## 📞 SUPPORT & MAINTENANCE

### Untuk Developer
- Review code documentation in file headers
- Check CodeIgniter 4 documentation
- Refer to this documentation for feature overview
- Check IMPLEMENTATION_PLAN.md untuk technical details

### Untuk User
- Gunakan HELP di aplikasi untuk guidance
- Hubungi technical support untuk issues
- Check TESTING_GUIDE.md untuk testing procedures

---

## 📄 RELATED DOCUMENTATION

- `IMPLEMENTATION_PLAN.md` - Technical implementation plan
- `TESTING_GUIDE.md` - Testing procedures
- `DATABASE_SCHEMA.md` - Detailed database schema
- `API_DOCUMENTATION.md` - API endpoints (if exists)
- `USER_MANUAL.md` - User guide (if exists)

---

## 🎓 GLOSSARY

**ERP**: Enterprise Resource Planning - Sistem manajemen sumber daya perusahaan

**B2B**: Business to Business - Transaksi antar bisnis

**Kontra Bon**: Sistem konsolidasi invoice B2B untuk billing

**HPP**: Harga Pokok Penjualan - Cost of Goods Sold (COGS)

**Stok Mutations**: Catatan pergerakan stok (IN/OUT/ADJUSTMENT/TRANSFER)

**Aging**: Analisis keterlambatan pembayaran (0-30, 31-60, 61-90, >90 days)

**Receivables**: Piutang dari pelanggan

**Payables**: Hutang ke supplier

**Audit Trail**: Catatan log semua aktivitas di sistem

---

**END OF DOCUMENTATION**

*Last updated: 2026-01-27*  
*Version: 1.0*  
*Author: Development Team*